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
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2013
     */
    public function process() {
        $checkoutData = $this->controller->getCombinedStepData();
        $member = Member::currentUser();
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
            $this->controller->addCompletedStep();
            $this->controller->NextStep(false);
        }


        return false;
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
}
