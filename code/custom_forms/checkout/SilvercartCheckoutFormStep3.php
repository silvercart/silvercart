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
 * checkout step for shipping method
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 03.01.2011
 * @license see license file in modules root directory
 */
class SilvercartCheckoutFormStep3 extends CustomHtmlFormStep {

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
        'ShippingMethod' => array(
            'type'              => 'SilvercartShippingOptionsetField',
            'title'             => 'Versandart',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        )
    );

    /**
     * Set of shipping methods
     *
     * @var DataList
     */
    protected $shippingMethods = null;
    
    /**
     * Determines whether to skip this step or not.
     *
     * @var bool
     */
    protected $skipShippingStep = null;

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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
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
        }
    }

    /**
     * Here we set some preferences.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.02.2013
     */
    public function preferences() {
        $stepIsVisible = true;
        if ($this->SkipShippingStep()) {
            $stepIsVisible = false;
        }
        $this->preferences['stepIsVisible']             = $stepIsVisible;
        $this->preferences['stepTitle']                 = _t('SilvercartCheckoutFormStep3.TITLE', 'Shipment');
        $this->preferences['submitButtonTitle']         = _t('SilvercartCheckoutFormStep.FORWARD', 'Next');
        $this->preferences['fillInRequestValues']       = true;
        $this->preferences['loadShoppingcartModules']   = false;
        $this->preferences['createShoppingcartForms']   = false;
        $this->preferences['doJsValidationScrolling']   = false;

        parent::preferences();
    }

    /**
     * Set initial form values
     *
     * @return void
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.02.2013
     */
    protected function fillInFieldValues() {
        $this->controller->fillFormFields($this->formFields);

        $shippingAddress= $this->getShippingAddress();

        if ($shippingAddress) {
            $title = sprintf(
                _t('SilvercartShippingMethod.CHOOSE_SHIPPING_METHOD'),
                $this->getShippingAddress()->SilvercartCountry()->Title
            );
        } else {
            $title = _t('SilvercartShippingMethod.CHOOSE_SHIPPING_METHOD');
        }

        $this->formFields['ShippingMethod']['title'] = $title;
        
        $shippingMethods = $this->getShippingMethods();
        if ($shippingMethods->count() > 0) {
            $map = $shippingMethods->map('ID', 'TitleWithCarrierAndFee');
            if ($map instanceof SS_Map) {
                $map = $map->toArray();
            }
            $this->formFields['ShippingMethod']['value'] = $map;
        }
        
        $stepData = $this->controller->getCombinedStepData();

        if (isset($stepData['ShippingMethod'])) {
            $this->formFields['ShippingMethod']['selectedValue'] = $stepData['ShippingMethod'];
        } else {
            if (isset($shippingMethods) &&
                $shippingMethods &&
                $shippingMethods->exists()) {
                $this->formFields['ShippingMethod']['selectedValue'] = $shippingMethods->First()->ID;
            }
        }
    }
    
    /**
     * If there is only one shipping method, set the shipping method and skip 
     * this step
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.09.2012
     */
    public function process() {
        $shippingMethods = $this->getShippingMethods();
        if ($this->SkipShippingStep()) {
            // there is only one shipping method, set it and skip this step
            $formData = array(
                'ShippingMethod' => $shippingMethods->First()->ID,
            );
            $this->controller->setStepData($formData);
            $this->controller->addCompletedStep();
            $this->controller->NextStep();
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
     * @since 4.1.2011
     */
    public function submitSuccess($data, $form, $formData) {
        if ($this->controller->stepDataChanged($formData, 'ShippingMethod')) {
            $this->controller->removeCompletedStep($this->controller->getCurrentStep() + 1,true);
        }
        $this->controller->setStepData($formData);
        $this->controller->addCompletedStep();
        $this->controller->NextStep();
    }
    
    /**
     * Returns the checkouts current shipping address
     * 
     * @return SilvercartAddress
     */
    public function getShippingAddress() {
        return $this->Controller()->getShippingAddress();
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
     * Returns the shipping methods.
     * 
     * @return DataList
     */
    public function getShippingMethods() {
        if (is_null($this->shippingMethods)) {
            $shippingMethods = SilvercartShippingMethod::getAllowedShippingMethods(null, $this->getShippingAddress());
            if (!($shippingMethods instanceof DataList) ||
                $shippingMethods->Count() == 0) {
                $shippingMethods = SilvercartShippingMethod::get();
                if (!($shippingMethods instanceof DataList)) {
                    $shippingMethods = new DataList();
                }
            }
            $this->shippingMethods = $shippingMethods;
        }
        return $this->shippingMethods;
    }

    /**
     * Returns whether to skip this step or not.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.03.2013
     */
    public function SkipShippingStep() {
        if (is_null($this->skipShippingStep)) {
            if (SilvercartConfig::SkipShippingStepIfUnique() &&
                $this->getShippingMethods()->Count() == 1) {
                $this->skipShippingStep = true;
            } else {
                $this->skipShippingStep = false;
            }
        }
        return $this->skipShippingStep;
    }
    
}

