<?php

/**
 * checkout step for order confirmation
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2011
 * @license BSD
 */
class CheckoutFormStep4 extends CustomHtmlForm {

    protected $formFields = array(
        'ChosenShippingMethod' => array(
            'type' => 'ReadonlyField',
            'title' => 'gewählte Versandart'
        ),
        'ChosenPaymentMethod' => array(
            'type' => 'ReadonlyField',
            'title' => 'gewählte Bezahlart'
        ),
        'Note' => array(
            'type' => 'TextareaField'
        ),

        /**
         * leagal fields
         */
        'HasAcceptedTermsAndConditions' => array(
            'type' => 'CheckboxField',
            'title' => 'Ich akzeptiere die allgemeinen Geschäftsbedingungen',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'HasAcceptedRevocationInstruction' => array(
            'type' => 'CheckboxField',
            'title' => 'Ich habe die Widerrufsbelehrung gelesen',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'SubscribedToNewsletter' => array(
            'type' => 'CheckboxField',
            'title' => 'Ich möchte den Newsletter abonnieren'
        )
    );

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
     * Setzt Initialwerte in Formularfeldern.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 09.11.2010
     */
    protected function fillInFieldValues() {
        $this->controller->fillFormFields(&$this->formFields);
        $stepData = $this->controller->getCombinedStepData();

        $chosenShippingMethod = DataObject::get_by_id('ShippingMethod', $stepData['ShippingMethod']);
        if ($chosenShippingMethod) {
            $this->formFields['ChosenShippingMethod']['value'] = $chosenShippingMethod->Title;
        }

        $chosenPaymentMethod = DataObject::get_by_id('PaymentMethod', $stepData['PaymentMethod']);
        if ($chosenPaymentMethod) {
            $this->formFields['ChosenPaymentMethod']['value'] = $chosenPaymentMethod->Name;
        }
    }

    /**
     * Liefert die Adressdaten als DataObject zurueck.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.01.2011
     */
    public function AddressData() {
        $checkoutData       = $this->controller->getCombinedStepData();
        $shippingAddress    = $this->controller->extractAddressDataFrom('Shipping', $checkoutData);
        $invoiceAddress     = $this->controller->extractAddressDataFrom('Invoice', $checkoutData);

        $shippingCountry = DataObject::get_by_id(
            'Country',
            $shippingAddress['CountryID']
        );

        if ($shippingCountry) {
            $shippingAddress['country'] = $shippingCountry;
        }

        $invoiceCountry = DataObject::get_by_id(
            'Country',
            $invoiceAddress['CountryID']
        );

        if ($invoiceCountry) {
            $invoiceAddress['country'] = $invoiceCountry;
        }

        $addressData  = new ArrayData(
            array(
                'shippingAddress'   => $shippingAddress,
                'invoiceAddress'    => $invoiceAddress
            )
        );
        return $addressData;
    }
}

