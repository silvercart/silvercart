<?php
/**
 * CheckoutProcessOrder
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2011
 * @license none
 */
class SilvercartCheckoutFormStep7 extends CustomHtmlForm {

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
     * @copyright 2011 pixeltricks GmbH
     * @since 07.01.2011
     */
    public function __construct($controller, $params = null, $preferences = null, $barebone = false) {
        parent::__construct($controller, $params, $preferences, $barebone);

        if (!$barebone) {
            /*
             * redirect a user if his cart is empty
             */
            if (!Member::currentUser()->SilvercartShoppingCart()->isFilled()) {
                Director::redirect("/home/");
            }
        }
    }

    /**
     * processor method
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.11.2010
     */
    public function process() {
        $checkoutData = $this->controller->getCombinedStepData();

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

        $shippingData = $this->controller->extractAddressDataFrom('Shipping', $checkoutData);
        $invoiceData  = $this->controller->extractAddressDataFrom('Invoice', $checkoutData);

        $order = new SilvercartOrder();
        $order->setCustomerEmail($customerEmail);
        $order->setShippingMethod($checkoutData['ShippingMethod']);
        $order->setPaymentMethod($checkoutData['PaymentMethod']);
        $order->setNote($customerNote);
        $order->setWeight();
        $order->createFromShoppingCart();

        $order->createShippingAddress($shippingData);
        $order->createInvoiceAddress($invoiceData);

        // send order confirmation mail
        $order->sendConfirmationMail();

        $this->controller->setStepData(
            array(
                'orderId' => $order->ID
            )
        );
        $this->controller->addCompletedStep();
        $this->controller->NextStep();
    }

}