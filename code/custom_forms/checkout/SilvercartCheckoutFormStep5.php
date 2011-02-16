<?php

/**
 * CheckoutProcessPaymentBeforeOrder
 *
 * Ruft die Methode "processPaymentBeforeOrder" im gewaehlten Zahlungsmodul
 * auf.
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2011
 * @license BSD
 */
class SilvercartCheckoutFormStep5 extends CustomHtmlForm {

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
            if (!$this->controller->isFilledCart()) {
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
        $member = Member::currentUser();
        $checkoutData = $this->controller->getCombinedStepData();

        if (!$this->paymentMethodObj) {
            $this->paymentMethodObj = DataObject::get_by_id(
                'SilvercartPaymentMethod',
                $checkoutData['PaymentMethod']
            );
        }

        if ($this->paymentMethodObj) {
            $this->paymentMethodObj->setController($this->controller);
            $orderAmount = $member->SilvercartShoppingCart()->getAmountTotal();
            $taxes       = $member->SilvercartShoppingCart()->getTaxRatesWithoutFees();
            $taxRates    = array();
            $this->paymentMethodObj->setData('order', 'taxes', array());

            foreach ($taxes as $tax) {
                $taxRates[] = array(
                    'rate'   => $tax->Rate,
                    'amount' => $tax->Amount->getAmount()
                );
            }

            $this->paymentMethodObj->setData(
                'order',
                'taxes',
                $taxRates
            );

            $this->paymentMethodObj->setCancelLink(Director::absoluteURL($this->controller->Link()) . 'Cancel');
            $this->paymentMethodObj->setReturnLink(Director::absoluteURL($this->controller->Link()));
            $this->paymentMethodObj->setData('order', 'amount_gross', $orderAmount->getAmount());
            $this->paymentMethodObj->setData('customer', array('details', 'FirstName'), $member->FirstName);
            $this->paymentMethodObj->setData('customer', array('details', 'SurName'), $member->Surname);
            $this->paymentMethodObj->setData('customer', array('deliveryAddress', 'FirstName'), $member->FirstName);
            $this->paymentMethodObj->setData('customer', array('deliveryAddress', 'SurName'), $member->Surname);
            $this->paymentMethodObj->setData('customer', array('shippingAddress', 'FirstName'), $member->FirstName);
            $this->paymentMethodObj->setData('customer', array('shippingAddress', 'SurName'), $member->Surname);

            $this->paymentMethodObj->processPaymentBeforeOrder();
        } else {
            Director::redirect($this->controller->Link() . '/Cancel');
        }
    }

}

