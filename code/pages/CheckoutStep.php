<?php

/**
 * Seite fuer den Checkoutprozess.
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 09.11.2010
 * @license none
 */
class CheckoutStep extends CustomHtmlFormStepPage {

    /**
     * Creates a default checkout page if non exists.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 28.01.2011
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        $records = DataObject::get_one($this->ClassName);
        if (!$records) {
            $page               = new $this->ClassName();
            $page->Title        = _t('Page.CHECKOUT');
            $page->URLSegment   = _t('CheckoutStep.URL_SEGMENT', 'checkout');
            $page->Status       = "Published";
            $page->ShowInMenus  = true;
            $page->ShowInSearch = true;
            $page->write();
            $page->publish("Stage", "Live");
        }
    }
}

/**
 * Seite fuer den Checkoutprozess.
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 09.11.2010
 * @license none
 */
class CheckoutStep_Controller extends CustomHtmlFormStepPage_Controller {

    /**
     * Legt Voreinstellungen fest.
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 17.11.2010
     */
    protected $preferences = array(
        'templateDir' => ''
    );
    /**
     * Enthaelt das Zahlungsmodul-Objekt.
     *
     * @var PaymentMethod
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    protected $paymentMethodObj = false;

    /**
     * Bindet Formulare ein und laedt CSS- und Javascriptdateien.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 09.11.2010
     */
    public function init() {
        $this->preferences['templateDir'] = PIXELTRICKS_CHECKOUT_BASE_PATH_REL . 'templates/Layout/';

        parent::init();
    }

    /**
     * Gibt zurueck, ob ein Fehler im Zahlungsmodul aufgetreten ist.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function getErrorOccured() {
        return $this->paymentMethodObj->getErrorOccured();
    }

    /**
     * Gibt die Fehlerliste als DataObjectSet zurueck.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function getErrorList() {
        return $this->paymentMethodObj->getErrorList();
    }

    /**
     * Loescht den Warenkorb.
     *
     * @param bool $includeShoppingCart set wether the shoppingcart should be
     *                                  deleted
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pxieltricks GmbH
     * @since 22.11.2010
     */
    public function deleteSessionData($includeShoppingCart = true) {
        parent::deleteSessionData();

        $member = Member::currentUser();

        if ($includeShoppingCart && $member) {
            if ($member->shoppingCartID != 0) {
                $shoppingCart = $member->ShoppingCart();
                $shoppingCart->delete();
            }
        }

        if (isset($_SESSION['paypal_module_payer_id'])) {
            unset($_SESSION['paypal_module_payer_id']);
        }
        if (isset($_SESSION['paypal_module_token'])) {
            unset($_SESSION['paypal_module_token']);
        }
    }

    /**
     * Liefert die Bearbeitungsgebuehren fuer die gewaehlte Zahlungsart
     * zurueck.
     *
     * @return float
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 25.11.2010
     */
    public function getHandlingCosts() {
        $checkoutData = $this->getCombinedStepData();
        $handlingCosts = 0;

        $paymentMethodObj = DataObject::get_by_id(
            'PaymentMethod',
            $checkoutData['PaymentMethod']
        );

        if ($paymentMethodObj) {
            $handlingCostsObj = $paymentMethodObj->getHandlingCost();
        } else {
            $handlingCostsObj = new Money;
            $handlingCostsObj->setAmount($handlingCosts);
        }

        return $handlingCostsObj;
    }

    /**
     * Returns the shipping method title.
     *
     * @return string
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 26.1.2011
     */
    public function CarrierAndShippingMethodTitle() {
        $checkoutData = $this->getCombinedStepData();
        $title = '';

        $selectedShippingMethod = DataObject::get_by_id(
            'ShippingMethod',
            $checkoutData['ShippingMethod']
        );

        if ($selectedShippingMethod) {
            $title = $selectedShippingMethod->carrier()->Title . "-" . $selectedShippingMethod->Title;
        }

        return $title;
    }

    /**
     * Returns the handling costs for the chosen shipping method.
     *
     * @return Money
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 26.1.2011
     */
    public function HandlingCostShipment() {
        $checkoutData = $this->getCombinedStepData();
        $handlingCostShipment = 0;

        $selectedShippingMethod = DataObject::get_by_id(
                        'ShippingMethod',
                        $checkoutData['ShippingMethod']
        );

        if ($selectedShippingMethod) {
            $handlingCostShipmentObj = $selectedShippingMethod->getShippingFee()->Price;
        } else {
            $handlingCostShipmentObj = new Money();
            $handlingCostShipmentObj->setAmount($handlingCostShipment);
        }

        return $handlingCostShipmentObj;
    }

    /**
     * Returns the payment method title.
     *
     * @return string
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 26.1.2011
     */
    public function PaymentMethodTitle() {
        $checkoutData = $this->getCombinedStepData();
        $title = '';

        $paymentMethodObj = DataObject::get_by_id(
                        'PaymentMethod',
                        $checkoutData['PaymentMethod']
        );

        if ($paymentMethodObj) {
            $title = $paymentMethodObj->Name;
        }

        return $title;
    }

    /**
     * Returns the handling costs for the chosen payment method.
     *
     * @return Money
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 26.1.2011
     */
    public function HandlingCostPayment() {
        $checkoutData = $this->getCombinedStepData();
        $handlingCostPayment = 0;

        $paymentMethodObj = DataObject::get_by_id(
                        'PaymentMethod',
                        $checkoutData['PaymentMethod']
        );

        if ($paymentMethodObj) {
            $handlingCostPaymentObj = $paymentMethodObj->getHandlingCost();
        } else {
            $handlingCostPaymentObj = new Money();
            $handlingCostPaymentObj->setAmount(0);
        }

        return $handlingCostPaymentObj;
    }

    /**
     * Template metod; returns the price of the cart positions + shipping fee
     *
     * @return string a price amount
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 4.1.2011
     */
    public function getAmountGrossRaw() {
        $member = Member::currentUser();
        $stepData = $this->getCombinedStepData();
        $cart = $member->shoppingCart();
        $shippingMethod = DataObject::get_by_id('ShippingMethod', $stepData['ShippingMethod']);
        $amountTotal = 0;

        if ($cart && $shippingMethod) {
            $shippingFee = $shippingMethod->getShippingFee()->Price->getAmount();
            $priceSumCart = $cart->getPrice()->getAmount();

            if ($shippingFee && $priceSumCart) {
                $amountTotal = $shippingFee + $priceSumCart;
            }
        }

        $amountTotalObj = new Money;
        $amountTotalObj->setAmount($amountTotal);

        return $amountTotalObj;
    }

    /**
     * Fügt den Versandadressdaten ein Präfix hinzu.
     *
     * @param string $prefix Präfix
     * @param array  $data   Extrahiert die Versandadressdaten aus den Checkout-daten.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 05.01.2011
     */
    public function extractAddressDataFrom($prefix, $data) {
        $addressData = array();
        $shippingDataFields = array(
            $prefix.'_Salutation'       => 'Salutation',
            $prefix.'_FirstName'        => 'FirstName',
            $prefix.'_Surname'          => 'Surname',
            $prefix.'_Addition'         => 'Addition',
            $prefix.'_Street'           => 'Street',
            $prefix.'_StreetNumber'     => 'StreetNumber',
            $prefix.'_Postcode'         => 'Postcode',
            $prefix.'_City'             => 'City',
            $prefix.'_Phone'            => 'Phone',
            $prefix.'_PhoneAreaCode'    => 'PhoneAreaCode',
            $prefix.'_Country'          => 'CountryID'
        );

        if (is_array($data)) {
            foreach ($shippingDataFields as $shippingFieldName => $dataFieldName) {
                if (isset($data[$shippingFieldName])) {
                    $addressData[$dataFieldName] = $data[$shippingFieldName];
                }
            }
        }

        return $addressData;
    }

}
