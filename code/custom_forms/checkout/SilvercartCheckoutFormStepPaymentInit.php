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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.01.2011
     */
    public function __construct($controller, $params = null, $preferences = null, $barebone = false) {
        $member       = Member::currentUser();
        $checkoutData = $controller->getCombinedStepData();

        if (!$this->payment &&
             $member) {

            if (array_key_exists('PaymentMethod', $checkoutData)) {
                $this->paymentMethodObj = DataObject::get_by_id(
                    'SilvercartPaymentMethod',
                    $checkoutData['PaymentMethod']
                );
                if ($this->paymentMethodObj) {
                    $this->paymentMethodObj->setController($controller);

                    $this->paymentMethodObj->setCancelLink(Director::absoluteURL($controller->Link()) . 'GotoStep/2');
                    $this->paymentMethodObj->setReturnLink(Director::absoluteURL($controller->Link()));

                    $this->paymentMethodObj->setCustomerDetailsByCheckoutData($checkoutData);
                    $this->paymentMethodObj->setInvoiceAddressByCheckoutData($checkoutData);
                    $this->paymentMethodObj->setShippingAddressByCheckoutData($checkoutData);
                    $this->paymentMethodObj->setShoppingCart($member->SilvercartShoppingCart());
                }
            }
        }

        parent::__construct($controller, $params, $preferences, $barebone);

        if (!$barebone) {
            /*
             * redirect a user if his cart is empty and no order exists
             */
            $checkoutData = $this->controller->getCombinedStepData();
            if (!Member::currentUser() ||
                (!Member::currentUser()->SilvercartShoppingCart()->isFilled() &&
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
}

