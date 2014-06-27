<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 */

/**
 * checkout step for payment method
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 03.01.2011
 * @license see license file in modules root directory
 */
class SilvercartCheckoutFormStep4 extends CustomHtmlFormStep {

    /**
     * Don't cache this form.
     *
     * @var bool
     */
    protected $excludeFromCache = true;

    /**
     * The form field definitions.
     *
     * @var array
     */
    protected $formFields = array(
        'PaymentMethod' => array(
            'type'              => 'SilvercartCheckoutOptionsetField',
            'title'             => 'Bezahlart',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        )
    );
    
    /**
     * List of allowed payment methods
     *
     * @var ArrayList 
     */
    protected $allowedPaymentMethods = null;
    
    /**
     * List of active payment methods
     *
     * @var DataList 
     */
    protected $activePaymentMethods = null;
    
    /**
     * A list of registered nested forms to render into this checkout step
     *
     * @var ArrayList
     */
    protected $registeredNestedForms = array();
    
    /**
     * Determines whether to skip this step or not.
     *
     * @var bool
     */
    protected $skipPaymentStep = null;

    /**
     * constructor
     *
     * @param Controller $controller  the controller object
     * @param array      $params      additional parameters
     * @param array      $preferences array with preferences
     * @param bool       $barebone    is the form initialized completely?
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.06.2014
     */
    public function __construct($controller, $params = null, $preferences = null, $barebone = false) {
        parent::__construct($controller, $params, $preferences, $barebone);

        if (!$barebone) {
            /*
             * redirect a user if his cart is empty and no order exists
             */
            $checkoutData = $this->controller->getCombinedStepData();
            if (!Member::currentUser() ||
                (!Member::currentUser()->getCart()->isFilled() &&
                 !array_key_exists('orderId', $checkoutData))) {
                
                $frontPage = SilvercartPage_Controller::PageByIdentifierCode();
                $this->getController()->redirect($frontPage->RelativeLink());
            }
            
            if (Member::currentUserID() > 0) {
                foreach ($this->getAllowedPaymentMethods() as $paymentMethod) {
                    if ($paymentMethod->getNestedFormName()) {
                        $formName = $paymentMethod->getNestedFormName();
                    } else {
                        $formName = "SilvercartCheckoutFormStep4DefaultPayment";
                    }
                    $params = array(
                        'PaymentMethod'     => $paymentMethod->ID,
                    );
                    $preferences = array(
                        'submitButtonTitle' => sprintf(_t('SilvercartCheckoutFormStep4.CHOOSE_PAYMENT_METHOD', 'I want to pay with %s'), $paymentMethod->Name),
                        );
                    $registeredNestedForm = new $formName($this->controller, $params, $preferences, $barebone);
                    $this->registerCustomHtmlForm($formName . $paymentMethod->ID, $registeredNestedForm);
                    $this->addRegisteredNestedForm($registeredNestedForm);
                }
            }
        }
    }

    /**
     * Returns the Cache Key for the current step
     * 
     * @return string
     */
    public function getCacheKeyExtension() {
        return $this->Controller()->getCacheKey();
    }

    /**
     * Here we set some preferences.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.03.2013
     */
    public function preferences() {
        $paymentMethods = SilvercartPaymentMethod::get()->filter("isActive", 1);
        $stepIsVisible  = true;
        if ($paymentMethods->count() == 0) {
            $this->addMessage('Keine Zahlungsarten definiert!');
        } else {
            if ($this->SkipPaymentStep()) {
                $stepIsVisible = false;
            }
            $this->preferences['stepIsVisible']             = $stepIsVisible;
            $this->preferences['stepTitle']                 = _t('SilvercartCheckoutFormStep4.TITLE', 'Payment');
            $this->preferences['submitButtonTitle']         = _t('SilvercartCheckoutFormStep.FORWARD', 'Next');
            $this->preferences['fillInRequestValues']       = true;
            $this->preferences['loadShoppingcartModules']   = false;
            $this->preferences['createShoppingcartForms']   = false;
            $this->preferences['doJsValidationScrolling']   = false;
        }

        parent::preferences();
    }

    /**
     * Set initial form values
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.11.2010
     */
    protected function fillInFieldValues() {
        $allowedPaymentMethods = $this->getAllowedPaymentMethods();

        $this->controller->fillFormFields($this->formFields);
        $this->formFields['PaymentMethod']['title'] = _t('SilvercartCheckoutFormStep4.FIELDLABEL');

        $stepData = $this->controller->getCombinedStepData();

        if ($allowedPaymentMethods) {
            $this->formFields['PaymentMethod']['value'] = $allowedPaymentMethods->map('ID', 'Name');
        }

        if (isset($stepData['PaymentMethod'])) {
            $this->formFields['PaymentMethod']['selectedValue'] = $stepData['PaymentMethod'];
        } else {
            if ($allowedPaymentMethods &&
                $allowedPaymentMethods->exists()) {
                $this->formFields['PaymentMethod']['selectedValue'] = $allowedPaymentMethods->First()->ID;
            }
        }
    }

    /**
     * processor method
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2013
     */
    public function process() {
        $allowedPaymentMethods = $this->getAllowedPaymentMethods();
        if ($this->SkipPaymentStep()) {
            // there is only one payment method, set it and skip this step
            $paymentMethod     = $allowedPaymentMethods->first();
            $stepConfiguration = $paymentMethod->getStepConfiguration();
            $formData          = array(
                'PaymentMethod' => $allowedPaymentMethods->first()->ID,
            );
            $this->controller->setStepData($formData);
            $this->controller->resetStepMapping();
            $this->controller->registerStepDirectory($stepConfiguration);
            $this->controller->generateStepMapping();
            $this->controller->addCompletedStep();
            $this->controller->NextStep();
        } else {
            foreach ($this->getRegisteredNestedForms() as $registeredNestedForm) {
                if (method_exists($registeredNestedForm, 'process')) {
                    if ($registeredNestedForm->process()) {
                        $this->controller->resetStepMapping();
                        $this->controller->registerStepDirectory(
                            $registeredNestedForm->getPaymentMethod()->getStepConfiguration()
                        );
                        $this->controller->generateStepMapping();
                    }
                }
            }
        }
    }

    /**
     * executed if there are no valdation errors on submit
     * Form data is saved in session
     *
     * @param SS_HTTPRequest $data     contains the frameworks form data
     * @param Form           $form     not used
     * @param array          $formData contains the modules form data
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.11.2010
     */
    public function submitSuccess($data, $form, $formData) {
        $this->controller->setStepData($formData);

        $stepData = $this->controller->getCombinedStepData();

        if ($stepData &&
            isset($stepData['PaymentMethod'])) {
            
            $paymentMethod = DataObject::get_by_id('SilvercartPaymentMethod', $stepData['PaymentMethod']);
        }

        if ($paymentMethod) {
            $this->controller->resetStepMapping();

            $this->controller->registerStepDirectory(
                $paymentMethod->getStepConfiguration()
            );

            $this->controller->generateStepMapping();
            $this->controller->addCompletedStep();
            $this->controller->NextStep();
        } else {
            $this->getController()->redirect($this->getController()->Link());
        }
    }
    
    /**
     * Returns the allowed payment methods.
     *
     * @return ArrayList|Boolean
     */
    public function getAllowedPaymentMethods() {
        if (is_null($this->allowedPaymentMethods)) {
            $allowedPaymentMethods  = new ArrayList();
            $stepData               = $this->controller->getCombinedStepData();

            if (Member::currentUser() instanceof Member&&
                is_array($stepData) &&
                array_key_exists('Shipping_Country', $stepData) &&
                $stepData['Shipping_Country'] != "") {
                
                $shippingCountry = DataObject::get_by_id('SilvercartCountry', $stepData['Shipping_Country']);
                if ($shippingCountry instanceof SilvercartCountry) {
                    $allowedPaymentMethods  = SilvercartPaymentMethod::getAllowedPaymentMethodsFor($shippingCountry, Member::currentUser()->getCart());
                    if (!($allowedPaymentMethods instanceof ArrayList)) {
                        $allowedPaymentMethods = new ArrayList();
                    }
                }
            }
            $this->setAllowedPaymentMethods($allowedPaymentMethods);
        }
        return $this->allowedPaymentMethods;
    }

    /**
     * Sets the allowed payment methods.
     *
     * @param SS_List|Boolean $allowedPaymentMethods Allowed payment method
     * 
     * @return void
     */
    public function setAllowedPaymentMethods(SS_List $allowedPaymentMethods) {
        $this->allowedPaymentMethods = $allowedPaymentMethods;
    }
    
    /**
     * Returns all registered nested forms as a ArrayList
     *
     * @return ArrayList
     */
    public function getRegisteredNestedForms() {
        return $this->registeredNestedForms;
    }

    /**
     * Sets all registered nested forms as a ArrayList
     *
     * @param ArrayList $registeredNestedForms Registered nested forms
     * 
     * @return void
     */
    public function setRegisteredNestedForms(ArrayList $registeredNestedForms) {
        $this->registeredNestedForms = $registeredNestedForms;
    }
    
    /**
     * Adds a registered nested form
     *
     * @param CustomHtmlForm $registeredNestedForm Registered newsted form
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.07.2011
     */
    public function addRegisteredNestedForm(CustomHtmlForm $registeredNestedForm) {
        if (empty($this->registeredNestedForms)) {
            $this->registeredNestedForms = new ArrayList();
        }
        $this->registeredNestedForms->push($registeredNestedForm);
    }
    
    /**
     * Returns the active payment methods.
     *
     * @return DataList|ArrayList
     */
    public function getActivePaymentMethods() {
        if (is_null($this->activePaymentMethods)) {
            $activePaymentMethods  = SilvercartPaymentMethod::getActivePaymentMethods();
            if (!($activePaymentMethods instanceof DataList)) {
                $activePaymentMethods = new ArrayList();
            }
            $this->setActivePaymentMethods($activePaymentMethods);
        }
        return $this->activePaymentMethods;
    }

    /**
     * Sets the active payment methods.
     *
     * @param DataList $activePaymentMethods Active payment method
     * 
     * @return void
     */
    public function setActivePaymentMethods($activePaymentMethods) {
        $this->activePaymentMethods = $activePaymentMethods;
    }

    /**
     * Returns whether to skip this step or not.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.03.2013
     */
    public function SkipPaymentStep() {
        if (is_null($this->skipPaymentStep)) {
            if (SilvercartConfig::SkipPaymentStepIfUnique() &&
                $this->getAllowedPaymentMethods()->Count() == 1) {
                if (($this->getRegisteredNestedForms() instanceof DataList ||
                     $this->getRegisteredNestedForms() instanceof ArrayList) &&
                    $this->getRegisteredNestedForms()->Count() >= 1 &&
                    $this->getRegisteredNestedForms()->First() instanceof SilvercartCheckoutFormStep4DefaultPayment) {
                    $this->skipPaymentStep = false;
                } else {
                    $this->skipPaymentStep = true;
                }
            } elseif (SilvercartConfig::SkipPaymentStepIfUnique() &&
                $this->getActivePaymentMethods()->Count() == 1) {
                if (($this->getRegisteredNestedForms() instanceof DataList ||
                     $this->getRegisteredNestedForms() instanceof ArrayList) &&
                    $this->getRegisteredNestedForms()->Count() >= 1 &&
                    $this->getRegisteredNestedForms()->First() instanceof SilvercartCheckoutFormStep4DefaultPayment) {
                    $this->skipPaymentStep = false;
                } else {
                    $this->skipPaymentStep = true;
                }
            } else {
                $this->skipPaymentStep = false;
            }
        }
        return $this->skipPaymentStep;
    }

}

