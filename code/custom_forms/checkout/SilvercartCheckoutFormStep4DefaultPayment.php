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
 * Default nested form for PaymentMethods
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 11.07.2011
 * @license see license file in modules root directory
 */
class SilvercartCheckoutFormStep4DefaultPayment extends CustomHtmlFormStep {

    /**
     * Don't cache this form.
     *
     * @var bool
     */
    protected $excludeFromCache = true;

    /**
     * The payment method chosen in checkout
     *
     * @var SilvercartPaymentMethod
     */
    protected $paymentMethod = null;
    
    /**
     * constructor. Set alternative form action here.
     *
     * @param Controller $controller  the controller object
     * @param array      $params      additional parameters
     * @param array      $preferences array with preferences
     * @param bool       $barebone    is the form initialized completely?
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function __construct($controller, $params = null, $preferences = null, $barebone = false) {
        $member = SilvercartCustomer::currentUser();
        $checkoutData = $controller->getCombinedStepData();
        if ($member &&
            is_array($params) &&
            array_key_exists('PaymentMethod', $params)) {
            $paymentMethod = DataObject::get_by_id('SilvercartPaymentMethod', $params['PaymentMethod']);
            if ($paymentMethod) {
                $paymentMethod->setController($controller);
                
                $paymentMethod->setCancelLink(Director::absoluteURL($controller->Link()) . 'GotoStep/2');
                $paymentMethod->setReturnLink(Director::absoluteURL($controller->Link()));

                $paymentMethod->setCustomerDetailsByCheckoutData($checkoutData);
                $paymentMethod->setInvoiceAddressByCheckoutData($checkoutData);
                $paymentMethod->setShippingAddressByCheckoutData($checkoutData);
                if (array_key_exists('ShippingMethod', $checkoutData)) {
                    $member->getCart()->setShippingMethodID($checkoutData['ShippingMethod']);
                }
                $paymentMethod->setShoppingCart($member->getCart());
                $this->setPaymentMethod($paymentMethod);
            }
        }
        parent::__construct($controller, $params, $preferences, $barebone);
    }

    /**
     * Here we set some preferences.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.07.2014
     */
    public function preferences() {
        $this->preferences['fillInRequestValues']       = true;
        $this->preferences['doJsValidationScrolling']   = false;
        $this->preferences['submitButtonUseButtonTag']  = true;
        $this->preferences['submitButtonExtraClasses']  = array(
            'silvercart-button',
            'btn',
            'btn-large',
            'btn-success',
            'pull-right',
        );

        parent::preferences();
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
     * Returns the related payment method
     *
     * @return SilvercartPaymentMethod 
     */
    public function getPaymentMethod() {
        return $this->paymentMethod;
    }

    /**
     * Sets the related payment method
     *
     * @param SilvercartPaymentMethod $paymentMethod Related payment method
     * 
     * @return void
     */
    public function setPaymentMethod(SilvercartPaymentMethod $paymentMethod) {
        $this->paymentMethod = $paymentMethod;
    }
    
}