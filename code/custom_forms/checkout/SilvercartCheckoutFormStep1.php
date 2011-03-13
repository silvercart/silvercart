<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Forms Checkout
 */

/**
 * form step for customers shipping/billing address
 *
 * @package Silvercart
 * @subpackage Forms Checkout
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCheckoutFormStep1 extends CustomHtmlForm {

    protected $formFields = array(
        'InvoiceAddressAsShippingAddress' => array(
            'type'      => 'CheckboxField',
            'title'     => 'Rechnungsadresse als Versandadresse nutzen',
            'value'     => '0',
            'jsEvents'  => array(
              'setEventHandler' => array(
                'type'          => 'click',
                'callFunction'  => 'toggleShippingAddressSection'
              )
            )
        ),
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
     * preferences
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 26.1.2011
     */
    protected $preferences = array(
        'submitButtonTitle'     => 'weiter',
        'stepTitle'             => 'Adressen',
        'fillInRequestValues'   => true,
    );

    /**
     * init
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
        $this->preferences['submitButtonTitle'] = _t('SilvercartCheckoutFormStep.FORWARD', 'Next');
        $this->preferences['stepTitle'] = _t('SilvercartCheckoutFormStep1.TITLE', 'Addresses');
        parent::__construct($controller, $params, $preferences, $barebone);

        if (!$barebone) {
            /*
             * redirect a user if his cart is empty
             */
            if (!Member::currentUser()->SilvercartShoppingCart()->isFilled()) {
                $frontPage = SilvercartPage_Controller::PageByIdentifierCode();
                Director::redirect($frontPage->RelativeLink());
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
        $this->formFields['InvoiceAddressAsShippingAddress']['title'] = _t('SilvercartAddress.InvoiceAddressAsShippingAddress');

        $this->formFields['Invoice_Salutation']['title'] = _t('SilvercartAddress.SALUTATION', 'salutation');
        $this->formFields['Invoice_Salutation']['value'] = array('' => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE'), "Frau" => _t('SilvercartAddress.MISSIS', 'misses'), "Herr" => _t('SilvercartAddress.MISTER', 'mister'));
        $this->formFields['Invoice_FirstName']['title'] = _t('SilvercartAddress.FIRSTNAME', 'firstname');
        $this->formFields['Invoice_Surname']['title'] = _t('SilvercartAddress.SURNAME', 'surname');
        $this->formFields['Invoice_Addition']['title'] = _t('SilvercartAddress.ADDITION', 'addition');
        $this->formFields['Invoice_Street']['title'] = _t('SilvercartAddress.STREET', 'street');
        $this->formFields['Invoice_StreetNumber']['title'] = _t('SilvercartAddress.STREETNUMBER', 'streetnumber');
        $this->formFields['Invoice_Postcode']['title'] = _t('SilvercartAddress.POSTCODE', 'postcode');
        $this->formFields['Invoice_City']['title'] = _t('SilvercartAddress.CITY', 'city');
        $this->formFields['Invoice_Phone']['title'] = _t('SilvercartAddress.PHONE', 'phone');
        $this->formFields['Invoice_PhoneAreaCode']['title'] = _t('SilvercartAddress.PHONEAREACODE', 'phone area code');
        $this->formFields['Invoice_Country']['title'] = _t('SilvercartCountry.SINGULARNAME');

        $this->formFields['Shipping_Salutation']['title'] = _t('SilvercartAddress.SALUTATION');
        $this->formFields['Shipping_Salutation']['value'] = array('' => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE'), "Frau" => _t('SilvercartAddress.MISSIS'), "Herr" => _t('SilvercartAddress.MISTER'));
        $this->formFields['Shipping_FirstName']['title'] = _t('SilvercartAddress.FIRSTNAME');
        $this->formFields['Shipping_Surname']['title'] = _t('SilvercartAddress.SURNAME');
        $this->formFields['Shipping_Addition']['title'] = _t('SilvercartAddress.ADDITION');
        $this->formFields['Shipping_Street']['title'] = _t('SilvercartAddress.STREET');
        $this->formFields['Shipping_StreetNumber']['title'] = _t('SilvercartAddress.STREETNUMBER');
        $this->formFields['Shipping_Postcode']['title'] = _t('SilvercartAddress.POSTCODE');
        $this->formFields['Shipping_City']['title'] = _t('SilvercartAddress.CITY');
        $this->formFields['Shipping_Phone']['title'] = _t('SilvercartAddress.PHONE');
        $this->formFields['Shipping_PhoneAreaCode']['title'] = _t('SilvercartAddress.PHONEAREACODE');
        $this->formFields['Shipping_Country']['title'] = _t('SilvercartCountry.SINGULARNAME');

        $countries = DataObject::get('SilvercartCountry');
        if ($countries) {
            $this->formFields['Shipping_Country']['value'] = $countries->toDropDownMap('ID', 'Title', _t('SilvercartCheckoutFormStep1.EMPTYSTRING_COUNTRY', '--country--'));
            $this->formFields['Invoice_Country']['value'] = $countries->toDropDownMap('ID', 'Title', _t('SilvercartCheckoutFormStep1.EMPTYSTRING_COUNTRY', '--country--'));
        }
        $member = SilvercartCustomerRole::currentRegisteredCustomer(); //method located in decorator; can not be called via class Member
        if ($member) {
            $this->formFields['Email']['value'] = $member->Email;
            if ($member->SilvercartInvoiceAddress()) {
                $this->formFields['Invoice_FirstName']['value'] = $member->FirstName;
                $this->formFields['Invoice_Surname']['value'] = $member->Surname;
                $this->formFields['Invoice_Salutation']['selectedValue'] = $member->Salutation;
                $this->formFields['Invoice_Addition']['value'] = $member->SilvercartInvoiceAddress()->Addition;
                $this->formFields['Invoice_Street']['value'] = $member->SilvercartInvoiceAddress()->Street;
                $this->formFields['Invoice_StreetNumber']['value'] = $member->SilvercartInvoiceAddress()->StreetNumber;
                $this->formFields['Invoice_Postcode']['value'] = $member->SilvercartInvoiceAddress()->Postcode;
                $this->formFields['Invoice_City']['value'] = $member->SilvercartInvoiceAddress()->City;
                $this->formFields['Invoice_PhoneAreaCode']['value'] = $member->SilvercartInvoiceAddress()->PhoneAreaCode;
                $this->formFields['Invoice_Phone']['value'] = $member->SilvercartInvoiceAddress()->Phone;
                $this->formFields['Invoice_Country']['selectedValue'] = $member->SilvercartInvoiceAddress()->SilvercartCountry()->ID;
            }
            if ($member->SilvercartShippingAddress()) {
                $this->formFields['Shipping_FirstName']['value'] = $member->FirstName;
                $this->formFields['Shipping_Surname']['value'] = $member->Surname;
                $this->formFields['Shipping_Salutation']['selectedValue'] = $member->Salutation;
                $this->formFields['Shipping_Addition']['value'] = $member->SilvercartShippingAddress()->Addition;
                $this->formFields['Shipping_Street']['value'] = $member->SilvercartShippingAddress()->Street;
                $this->formFields['Shipping_StreetNumber']['value'] = $member->SilvercartShippingAddress()->StreetNumber;
                $this->formFields['Shipping_Postcode']['value'] = $member->SilvercartShippingAddress()->Postcode;
                $this->formFields['Shipping_City']['value'] = $member->SilvercartShippingAddress()->City;
                $this->formFields['Shipping_PhoneAreaCode']['value'] = $member->SilvercartShippingAddress()->PhoneAreaCode;
                $this->formFields['Shipping_Phone']['value'] = $member->SilvercartShippingAddress()->Phone;
                $this->formFields['Shipping_Country']['selectedValue'] = $member->SilvercartShippingAddress()->SilvercartCountry()->ID;
            }
        }
        $this->controller->fillFormFields($this->formFields);

        if ($this->formFields['InvoiceAddressAsShippingAddress']['value'] == '1') {
            $this->controller->addJavascriptOnloadSnippet('
                $(\'#ShippingAddressFields\').css(\'display\', \'none\');
            ');
        }
    }

    /**
     * We intercept the submit handler since we have to alter some field
     * checks depending on the status of the field "InvoiceAddressAsShippingAddress".
     *
     * @param SS_HTTPRequest $data submit data
     * @param Form           $form form object
     *
     * @return ViewableData
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pxieltricks GmbH
     * @since 13.03.2011
     */
    public function submit($data, $form) {

        // Disable the check instructions if the shipping address shall be
        // the same as the invoice address.
        if ($data['InvoiceAddressAsShippingAddress'] == '1'){
            $this->deactivateValidationFor('Shipping_Salutation');
            $this->deactivateValidationFor('Shipping_FirstName');
            $this->deactivateValidationFor('Shipping_Surname');
            $this->deactivateValidationFor('Shipping_Addition');
            $this->deactivateValidationFor('Shipping_Street');
            $this->deactivateValidationFor('Shipping_StreetNumber');
            $this->deactivateValidationFor('Shipping_Postcode');
            $this->deactivateValidationFor('Shipping_City');
            $this->deactivateValidationFor('Shipping_PhoneAreaCode');
            $this->deactivateValidationFor('Shipping_Phone');
            $this->deactivateValidationFor('Shipping_Country');
        }

        parent::submit($data, $form);
    }

    /**
     * executed if there are no valdation errors on submit
     * Form data is saved in session
     *
     * @param SS_HTTPRequest $data     contains the frameworks form data
     * @param Form           $form     not used
     * @param array          $formData contains the modules form data
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 09.11.2010
     */
    public function submitSuccess($data, $form, $formData) {

        // Set invoice address as shipping address if desired
        if ($data['InvoiceAddressAsShippingAddress'] == '1'){
            $formData['Shipping_Salutation']       = $formData['Invoice_Salutation'];
            $formData['Shipping_FirstName']        = $formData['Invoice_FirstName'];
            $formData['Shipping_Surname']          = $formData['Invoice_Surname'];
            $formData['Shipping_Addition']         = $formData['Invoice_Addition'];
            $formData['Shipping_Street']           = $formData['Invoice_Street'];
            $formData['Shipping_StreetNumber']     = $formData['Invoice_StreetNumber'];
            $formData['Shipping_Postcode']         = $formData['Invoice_Postcode'];
            $formData['Shipping_City']             = $formData['Invoice_City'];
            $formData['Shipping_PhoneAreaCode']    = $formData['Invoice_PhoneAreaCode'];
            $formData['Shipping_Phone']            = $formData['Invoice_Phone'];
            $formData['Shipping_Country']          = $formData['Invoice_Country'];
        }

        $this->controller->setStepData($formData);
        $this->controller->addCompletedStep();
        $this->controller->NextStep();
    }
}

