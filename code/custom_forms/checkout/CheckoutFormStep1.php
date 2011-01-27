<?php

/**
 * form step for customers shipping/billing address
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2011
 * @license BSD
 */
class CheckoutFormStep1 extends CustomHtmlForm {

    protected $formFields = array(
        /**
         * fields for billing address
         */
        'Invoice_Salutation' => array(
            'type' => 'DropdownField',
            'title' => 'Anrede',
            'value' => array('' => 'Bitte wählen', 'Frau' => 'Frau', 'Herr' => 'Herr'),
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Invoice_FirstName' => array(
            'type' => 'TextField',
            'title' => 'Vorname',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Invoice_Surname' => array(
            'type' => 'TextField',
            'title' => 'Nachname',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Invoice_Addition' => array(
            'type' => 'TextField',
            'title' => 'Adresszusatz'
        ),
        'Invoice_Street' => array(
            'type' => 'TextField',
            'title' => 'Straße',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Invoice_StreetNumber' => array(
            'type' => 'TextField',
            'title' => 'Hausnummer',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Invoice_Postcode' => array(
            'type' => 'TextField',
            'title' => 'PLZ',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Invoice_City' => array(
            'type' => 'TextField',
            'title' => 'Ort',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Invoice_Phone' => array(
            'type' => 'TextField',
            'title' => 'Telefon'
        ),
        'Invoice_PhoneAreaCode' => array(
            'type' => 'TextField',
            'title' => 'Vorwahl'
        ),
        'Invoice_Country' => array(
            'type' => 'DropdownField',
            'title' => 'Land',
            'value' => array(),
            'checkRequirements' => array(
                'isFilledIn' => true,
            )
        ),

        /**
         * Fields for shipping address
         */

        'Shipping_Salutation' => array(
            'type' => 'DropdownField',
            'title' => 'Anrede',
            'value' => array('' => 'Bitte wählen', 'Frau' => 'Frau', 'Herr' => 'Herr'),
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Shipping_FirstName' => array(
            'type' => 'TextField',
            'title' => 'Vorname',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Shipping_Surname' => array(
            'type' => 'TextField',
            'title' => 'Nachname',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Shipping_Addition' => array(
            'type' => 'TextField',
            'title' => 'Adresszusatz'
        ),
        'Shipping_Street' => array(
            'type' => 'TextField',
            'title' => 'Straße',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Shipping_StreetNumber' => array(
            'type' => 'TextField',
            'title' => 'Hausnummer',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Shipping_Postcode' => array(
            'type' => 'TextField',
            'title' => 'PLZ',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Shipping_City' => array(
            'type' => 'TextField',
            'title' => 'Ort',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Shipping_Phone' => array(
            'type' => 'TextField',
            'title' => 'Telefon'
        ),
        'Shipping_PhoneAreaCode' => array(
            'type' => 'TextField',
            'title' => 'Vorwahl'
        ),
        'Shipping_Country' => array(
            'type' => 'DropdownField',
            'title' => 'Land',
            'value' => array(),
            'checkRequirements' => array(
                'isFilledIn' => true,
            )
        ),
        'Email' => array(
            'type' => 'TextField',
            'title' => 'Email',
            'checkRequirements' => array(
                'isEmailAddress' => true,
                'isFilledIn' => true
            )
        )
    );

    /**
     * Voreinstellungen
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 26.1.2011
     */
    protected $preferences = array(
        'submitButtonTitle'         => 'weiter',
        'stepTitle' => 'Adressen'
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
     * Set initial form values
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 3.1.2011
     * @return void
     */
    protected function fillInFieldValues() {
        $countries = DataObject::get('Country');
        if ($countries) {
            $this->formFields['Shipping_Country']['value'] = $countries->toDropDownMap('ID', 'Title', '--Land--');
            $this->formFields['Invoice_Country']['value'] = $countries->toDropDownMap('ID', 'Title', '--Land--');
        }
        $member = CustomerRole::currentRegisteredCustomer(); //method located in decorator; can not be called via class Member
        if ($member) {
            $this->formFields['Email']['value'] = $member->Email;
            if ($member->invoiceAddress()) {
                $this->formFields['Invoice_FirstName']['value'] = $member->FirstName;
                $this->formFields['Invoice_Surname']['value'] = $member->Surname;
                $this->formFields['Invoice_Salutation']['selectedValue'] = $member->Salutation;
                $this->formFields['Invoice_Addition']['value'] = $member->invoiceAddress()->Addition;
                $this->formFields['Invoice_Street']['value'] = $member->invoiceAddress()->Street;
                $this->formFields['Invoice_StreetNumber']['value'] = $member->invoiceAddress()->StreetNumber;
                $this->formFields['Invoice_Postcode']['value'] = $member->invoiceAddress()->Postcode;
                $this->formFields['Invoice_City']['value'] = $member->invoiceAddress()->City;
                $this->formFields['Invoice_PhoneAreaCode']['value'] = $member->invoiceAddress()->PhoneAreaCode;
                $this->formFields['Invoice_Phone']['value'] = $member->invoiceAddress()->Phone;
                $this->formFields['Invoice_Country']['selectedValue'] = $member->invoiceAddress()->country()->ID;
            }
            if ($member->shippingAddress()) {
                $this->formFields['Shipping_FirstName']['value'] = $member->FirstName;
                $this->formFields['Shipping_Surname']['value'] = $member->Surname;
                $this->formFields['Shipping_Salutation']['selectedValue'] = $member->Salutation;
                $this->formFields['Shipping_Addition']['value'] = $member->shippingAddress()->Addition;
                $this->formFields['Shipping_Street']['value'] = $member->shippingAddress()->Street;
                $this->formFields['Shipping_StreetNumber']['value'] = $member->shippingAddress()->StreetNumber;
                $this->formFields['Shipping_Postcode']['value'] = $member->shippingAddress()->Postcode;
                $this->formFields['Shipping_City']['value'] = $member->shippingAddress()->City;
                $this->formFields['Shipping_PhoneAreaCode']['value'] = $member->shippingAddress()->PhoneAreaCode;
                $this->formFields['Shipping_Phone']['value'] = $member->shippingAddress()->Phone;
                $this->formFields['Shipping_Country']['selectedValue'] = $member->shippingAddress()->country()->ID;
            }
        }
    }

    /**
     * Wird ausgefuehrt, wenn nach dem Senden des Formulars keine Validierungs-
     * fehler aufgetreten sind.
     * Speichert die gesendeten Formulardaten in der Session zum spaeteren
     * Abruf.
     *
     * @param SS_HTTPRequest $data     Enthaelt die gesendeten "rohen" Formulardaten
     * @param Form           $form     wird nicht verwendet
     * @param array          $formData Enthaelt die geparsten Formulardaten
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 09.11.2010
     */
    public function submitSuccess($data, $form, $formData) {
        $this->controller->setStepData($formData);
        $this->controller->addCompletedStep();
        $this->controller->NextStep();
    }
}

