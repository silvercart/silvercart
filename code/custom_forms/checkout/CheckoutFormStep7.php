<?php

/**
 * CheckoutProcessOrder
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2011
 * @license BSD
 */
class CheckoutFormStep7 extends CustomHtmlForm {

    /**
     * Initialisierung
     *
     * @param Controller $controller  Das Controllerobjekt
     * @param array      $params      Zusaetzliche Parameter
     * @param array      $preferences Array mit Voreinstellungen
     * @param bool       $barebone    Gibt an, ob das Formular komplett initialisiert werden soll
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
            if (!$this->controller->isFilledCart()) {
                Director::redirect("/home/");
            }
        }
    }

    /**
     * Prozessormethode.
     *
     * @return empty
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

        if (isset($checkoutData['note'])) {
            $customerNote = $checkoutData['note'];
        } else {
            $customerNote = '';
        }

        $shippingData = $this->controller->extractAddressDataFrom('Shipping', $checkoutData);

        $invoiceData  = $this->controller->extractAddressDataFrom('Invoice', $checkoutData);

        $order = new Order();
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