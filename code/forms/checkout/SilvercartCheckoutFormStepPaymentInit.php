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
 * Initializing checkout step for a payment module.
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 03.01.2011
 * @license see license file in modules root directory
 */
class SilvercartCheckoutFormStepPaymentInit extends CustomHtmlFormStep {

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
    protected $paymentMethodObj = null;

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
     * @since 05.04.2018
     */
    public function __construct($controller, $params = null, $preferences = null, $barebone = false) {
        $this->paymentMethodObj = self::init_payment_method($controller);

        parent::__construct($controller, $params, $preferences, $barebone);

        if (!$barebone) {
            /*
             * redirect a user if his cart is empty and no order exists
             */
            $checkoutData = $this->controller->getCombinedStepData();
            if (!SilvercartCustomer::currentUser() ||
                (!SilvercartCustomer::currentUser()->getCart()->isFilled() &&
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 31.03.2011
     */
    public function preferences() {
        $this->preferences['stepIsVisible'] = false;

        parent::preferences();
    }

    /**
     * processor method
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.11.2010
     */
    public function process() {
        if ($this->paymentMethodObj) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Render the error template.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 04.04.2011
     */
    public function renderError() {
        $controller        = Controller::curr();
        $templateVariables = array(
            'errorObject' => $this->paymentMethodObj,
        );

        $controller->paymentMethodObj = $this->paymentMethodObj;
        $controller->initOutput       = $this->customise($templateVariables)->renderWith('SilvercartCheckoutFormStepPaymentError');
    }
    
    /**
     * Returns the PaymentMethod to use in template
     *
     * @return SilvercartPaymentMethod
     */
    public function getPaymentMethod() {
        return $this->paymentMethodObj;
    }
    
    /**
     * Initializes the payment method.
     * 
     * @param Controller $controller Controller
     * 
     * @return \SilvercartPaymentMethod
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2018
     */
    public static function init_payment_method($controller) {
        $paymentMethod = null;
        $member        = SilvercartCustomer::currentUser();
        $checkoutData  = $controller->getCombinedStepData();

        if ($member instanceof Member &&
            $member->exists()) {

            if (array_key_exists('PaymentMethod', $checkoutData)) {
                $paymentMethod = SilvercartPaymentMethod::get()->byID($checkoutData['PaymentMethod']);
                if ($paymentMethod instanceof SilvercartPaymentMethod &&
                    $paymentMethod->exists()) {
                    
                    $paymentMethod->setController($controller);

                    $paymentMethod->setCancelLink(Director::absoluteURL($controller->Link()) . 'GotoStep/2');
                    $paymentMethod->setReturnLink(Director::absoluteURL($controller->Link()));

                    $paymentMethod->setCustomerDetailsByCheckoutData($checkoutData);
                    $paymentMethod->setInvoiceAddressByCheckoutData($checkoutData);
                    $paymentMethod->setShippingAddressByCheckoutData($checkoutData);
                    $paymentMethod->setShoppingCart($member->getCart());
                }
            }
        }
        
        return $paymentMethod;
    }
    
}

