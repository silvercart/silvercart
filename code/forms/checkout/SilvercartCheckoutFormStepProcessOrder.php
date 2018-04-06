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
 * CheckoutProcessOrder
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 03.01.2011
 * @license see license file in modules root directory
 */
class SilvercartCheckoutFormStepProcessOrder extends CustomHtmlFormStep {

    /**
     * Don't cache this form.
     *
     * @var bool
     */
    protected $excludeFromCache = true;

    /**
     * Set this option to false in a payment module to prevent sending the order
     * confimation before finishing payment module dependent order manupulations
     *
     * @var bool
     */
    protected $sendConfirmationMail = true;
    
    /**
     * Order.
     *
     * @var SilvercartOrder
     */
    protected $order = null;
    
    /**
     * Payment method chosen for checkout.
     *
     * @var SilvercartPaymentMethod
     */
    protected $paymentMethod = null;

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
     * @since 15.11.2014
     */
    public function __construct($controller, $params = null, $preferences = null, $barebone = false) {
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
    public function  preferences() {
        $this->preferences['stepIsVisible']             = false;
        $this->preferences['createShoppingcartForms']   = false;

        parent::preferences();
    }

    /**
     * processor method
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.04.2018
     */
    public function process() {
        $result = false;
        $checkoutData = $this->controller->getCombinedStepData();
        $member = SilvercartCustomer::currentUser();
        if ($member instanceof Member) {
            // Vorbereitung der Parameter zur Erzeugung der Bestellung
            if (isset($checkoutData['Email'])) {
                $customerEmail = $checkoutData['Email'];
            } else {
                $customerEmail = '';
            }

            if (isset($checkoutData['Note'])) {
                $customerNote = $checkoutData['Note'];
            } else {
                $customerNote = '';
            }

            $anonymousCustomer = SilvercartCustomer::currentAnonymousCustomer();
            if ($anonymousCustomer) {
                // add a customer number to anonymous customer when ordering
                $anonymousCustomer->CustomerNumber = SilvercartNumberRange::useReservedNumberByIdentifier('CustomerNumber');
                $anonymousCustomer->write();
            }

            $shippingData = SilvercartTools::extractAddressDataFrom('Shipping', $checkoutData);
            $invoiceData  = SilvercartTools::extractAddressDataFrom('Invoice', $checkoutData);

            $order = $this->createOrder($customerEmail, $checkoutData, $customerNote);
            $this->extend('onAfterCreateOrder', $order);

            $order->createShippingAddress($shippingData);
            $order->createInvoiceAddress($invoiceData);

            $order->convertShoppingCartPositionsToOrderPositions();

            // send order confirmation mail
            if ($this->sendConfirmationMail) {
                $order->sendConfirmationMail();
            }

            $this->controller->setStepData(
                array(
                    'orderId' => $order->ID
                )
            );
            $this->setOrder($order);
            $this->controller->addCompletedStep();
            $this->controller->NextStep(false);
            $result = true;
        }

        return $result;
    }

    /**
     * Creates a SilvercartOrder object from the given parameters.
     *
     * @param string $customerEmail The customers email address
     * @param array  $checkoutData  The checkout data
     * @param string $customerNote  The optional note from the customer
     *
     * @return SilvercartOrder
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function createOrder($customerEmail, $checkoutData, $customerNote) {
        $order = new SilvercartOrder();
        $order->setCustomerEmail($customerEmail);
        $order->setShippingMethod($checkoutData['ShippingMethod']);
        $order->setPaymentMethod($checkoutData['PaymentMethod']);
        $order->setNote($customerNote);
        $order->setWeight();
        $order->setHasAcceptedTermsAndConditions($checkoutData['HasAcceptedTermsAndConditions']);
        $order->setHasAcceptedRevocationInstruction($checkoutData['HasAcceptedRevocationInstruction']);
        $order->createFromShoppingCart();

        return $order;
    }
    
    /**
     * Sets the order.
     * 
     * @param SilvercartOrder $order Order
     * 
     * @return void
     */
    public function setOrder(SilvercartOrder $order) {
        $this->order = $order;
    }
    
    /**
     * Returns the order.
     * 
     * @return SilvercartOrder
     */
    public function getOrder() {
        return $this->order;
    }
    
    /**
     * Returns the payment method chosen for checkout.
     * 
     * @return SilvercartPaymentMethod
     */
    public function getPaymentMethod() {
        if (is_null($this->paymentMethod)) {
            $this->paymentMethod = SilvercartCheckoutFormStepPaymentInit::init_payment_method($this->getController());
        }
        return $this->paymentMethod;
    }
}
