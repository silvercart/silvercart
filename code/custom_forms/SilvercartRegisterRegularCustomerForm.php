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
 * @subpackage Forms
 */

/**
 * CustomHtmlForm for registration of a regular customer
 *
 * @package Silvercart
 * @subpackage Forms
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 21.10.2010
 */
class SilvercartRegisterRegularCustomerForm extends CustomHtmlForm {

    /**
     * define form fields
     *
     * @var array
     */
    protected $formFields = array(
        'Salutation' => array(
            'type' => 'DropdownField',
            'title' => 'Anrede',
            'value' => array(
                '' => 'Bitte wählen',
                'Frau' => 'Frau',
                'Herr' => 'Herr'
            ),
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'FirstName' => array(
            'type' => 'TextField',
            'title' => 'Vorname',
            'checkRequirements' => array(
                'isFilledIn' => true,
                'hasMinLength' => 3
            )
        ),
        'Surname' => array(
            'type' => 'TextField',
            'title' => 'Nachname',
            'checkRequirements' => array(
                'isFilledIn' => true,
                'hasMinLength' => 3
            )
        ),
        'Street' => array(
            'type' => 'TextField',
            'title' => 'Straße',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'StreetNumber' => array(
            'type' => 'TextField',
            'title' => 'Hausnummer',
            'maxLength' => 10,
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Postcode' => array(
            'type' => 'TextField',
            'title' => 'PLZ',
            'maxLength' => 10,
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'City' => array(
            'type' => 'TextField',
            'title' => 'Stadt',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Country' => array(
            'type' => 'DropdownField',
            'title' => 'Land',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Email' => array(
            'type' => 'TextField',
            'title' => 'Email Adresse',
            'checkRequirements' => array(
                'isEmailAddress' => true,
                'isFilledIn' => true,
                'callBack'      => 'doesEmailExistAlready'
            )
        ),
        'EmailCheck' => array(
            'type' => 'TextField',
            'title' => 'E-Mail-Adresse Gegenprüfung',
            'checkRequirements' => array(
               'mustEqual' => 'Email',
            )
        ),
        'PhoneAreaCode' => array(
            'type' => 'TextField',
            'title' => 'Telefon Vorwahl',
            'checkRequirements' => array(
                'isFilledIn' => true,
                'isNumbersOnly' => true
            )
        ),
        'Phone' => array(
            'type' => 'TextField',
            'title' => 'Telefon Nummer.',
            'checkRequirements' => array(
                'isFilledIn' => true,
                'isNumbersOnly' => true
            )
        ),
        'BirthdayDay' => array(
            'type' => 'DropdownField',
            'title' => 'Tag',
            'value' => array(),
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'BirthdayMonth' => array(
            'type' => 'DropdownField',
            'title' => 'Monat',
            'value' => array(),
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'BirthdayYear' => array(
            'type' => 'TextField',
            'title' => 'Jahr',
            'maxLength' => 4,
            'checkRequirements' => array(
                'isFilledIn' => true,
                'isNumbersOnly' => true,
                'hasLength' => 4
            )
        ),
        'Password' => array(
            'type' => 'PasswordField',
            'title' => 'Passwort',
            'checkRequirements' => array(
                'isFilledIn' => true,
                'hasMinLength' => 6,
                'mustNotEqual' => 'FirstName',
            )
        ),
        'PasswordCheck' => array(
            'type' => 'PasswordField',
            'title' => 'Passwort Gegenprüfung',
            'checkRequirements' => array(
                'mustEqual' => 'Password'
            )
        ),
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
        ),
        'backlink' => array(
            'type' => 'HiddenField',
            'value' => ''
        ),
        'backlinkText' => array(
            'type' => 'HiddenField',
            'value' => ''
        ),
        'optInTempText' => array(
            'type' => 'HiddenField',
            'value' => ''
        )
    );

    /**
     * preferences
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 26.1.2011
     */
    protected $preferences = array(
        'submitButtonTitle' => 'Abschicken',
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
        $this->preferences['submitButtonTitle'] = _t('SilvercartPage.SUBMIT', 'Send');
        parent::__construct($controller, $params, $preferences, $barebone);
    }

    /**
     * Set initial values in form fields
     *
     * @return void
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.10.2010
     */
    protected function fillInFieldValues() {
        $this->formFields['Salutation']['title'] = _t('SilvercartAddress.SALUTATION');
        $this->formFields['Salutation']['value'] = array('' => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE'), "Frau" => _t('SilvercartAddress.MISSIS'), "Herr" => _t('SilvercartAddress.MISTER'));
        $this->formFields['FirstName']['title'] = _t('SilvercartAddress.FIRSTNAME', 'firstname');
        $this->formFields['Surname']['title'] = _t('SilvercartAddress.SURNAME', 'surname');
        $this->formFields['Email']['title'] = _t('SilvercartAddress.EMAIL', 'email address');
        $this->formFields['EmailCheck']['title'] = _t('SilvercartAddress.EMAIL_CHECK', 'email address check');
        $this->formFields['Street']['title'] = _t('SilvercartAddress.STREET', 'street');
        $this->formFields['StreetNumber']['title'] = _t('SilvercartAddress.STREETNUMBER', 'streetnumber');
        $this->formFields['Postcode']['title'] = _t('SilvercartAddress.POSTCODE', 'postcode');
        $this->formFields['City']['title'] = _t('SilvercartAddress.CITY', 'city');
        $this->formFields['Phone']['title'] = _t('SilvercartAddress.PHONE', 'phone');
        $this->formFields['PhoneAreaCode']['title'] = _t('SilvercartAddress.PHONEAREACODE', 'phone area code');
        $this->formFields['Country']['title'] = _t('SilvercartCountry.SINGULARNAME');
        $this->formFields['BirthdayDay']['title'] = _t('SilvercartPage.DAY');
        $this->formFields['BirthdayMonth']['title'] = _t('SilvercartPage.MONTH');
        $this->formFields['BirthdayYear']['title'] = _t('SilvercartPage.YEAR');
        $this->formFields['Password']['title'] = _t('SilvercartPage.PASSWORD');
        $this->formFields['PasswordCheck']['title'] = _t('SilvercartPage.PASSWORD_CHECK');
        $this->formFields['HasAcceptedTermsAndConditions']['title'] = _t('SilvercartCheckoutFormStep.I_ACCEPT_TERMS');
        $this->formFields['HasAcceptedRevocationInstruction']['title'] = _t('SilvercartCheckoutFormStep.I_ACCEPT_REVOCATION');
        $this->formFields['SubscribedToNewsletter']['title'] = _t('SilvercartCheckoutFormStep.I_SUBSCRIBE_NEWSLETTER');
        $birthdayDays = array(
            '' => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE')
        );
        $birthdayMonths = array(
            '' => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE'),
            '1' => _t('SilvercartPage.JANUARY'),
            '2' => _t('SilvercartPage.FEBRUARY'),
            '3' => _t('SilvercartPage.MARCH'),
            '4' => _t('SilvercartPage.APRIL'),
            '5' => _t('SilvercartPage.MAY'),
            '6' => _t('SilvercartPage.JUNE'),
            '7' => _t('SilvercartPage.JULY'),
            '8' => _t('SilvercartPage.AUGUST'),
            '9' => _t('SilvercartPage.SEPTEMBER'),
            '10' => _t('SilvercartPage.OCTOBER'),
            '11' => _t('SilvercartPage.NOVEMBER'),
            '12' => _t('SilvercartPage.DECEMBER')
        );

        for ($idx = 1; $idx < 32; $idx++) {
            $birthdayDays[$idx] = $idx;
        }

        $this->formFields['BirthdayDay']['value'] = $birthdayDays;
        $this->formFields['BirthdayMonth']['value'] = $birthdayMonths;

        $this->formFields['Country']['value'] = DataObject::get('SilvercartCountry', "`SilvercartCountry`.`Active`=1")->toDropdownMap('Title', 'Title', _t('SilvercartCheckoutFormStep2.EMPTYSTRING_COUNTRY', '--country--'));

        if (isset($_GET['backlink'])) {
            $this->formFields['backlink']['value'] = Convert::raw2sql($_GET['backlink']);
        }
        if (isset($_GET['backlinkText'])) {
            $this->formFields['backlinkText']['value'] = Convert::raw2sql($_GET['backlinkText']);
        }
        if (isset($_GET['optInTempText'])) {
            $this->formFields['optInTempText']['value'] = Convert::raw2sql($_GET['optInTempText']);
        }
    }

    /**
     * Form callback: Does the entered Email already exist?
     *
     * @param string $value the email address to be checked
     *
     * @return array to be rendered in the template
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.10.2010
     */
    public function doesEmailExistAlready($value) {
        $emailExistsAlready = false;

        $results = DataObject::get_one(
            'Member',
            "Email = '" . $value . "'"
        );

        if ($results) {
            $emailExistsAlready = true;
        }

        return array(
            'success' => !$emailExistsAlready,
            'errorMessage' => _t('SilvercartPage.EMAIL_ALREADY_REGISTERED', 'This Email address is already registered')
        );
    }

    /**
     * No validation errors occured, so we register the customer and send
     * mails with further instructions for the double opt-in procedure.
     *
     * @param SS_HTTPRequest $data     SS session data
     * @param Form           $form     the form object
     * @param array          $formData CustomHTMLForms session data
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 21.10.2010
     * @return void
     */
    protected function submitSuccess($data, $form, $formData) {
        $anonymousCustomer = false;

        /*
         * Logout anonymous users and save their shoppingcart temporarily.
         */
        if (Member::currentUser()) {
            $anonymousCustomer = Member::currentUser();
            Member::currentUser()->logOut();
        }

        // Create Confirmation hash for opt-in confirmation mail
        $confirmationHash = md5(
            $formData['Email'] .
            $formData['FirstName'] .
            $formData['Surname'] .
            mktime() .
            rand()
        );

        // Aggregate Data and set defaults
        $formData['MemberID']           = Member::currentUserID();
        $formData['ConfirmationHash']   = $confirmationHash;
        $formData['Locale']             = 'de_DE';
        $formData['OptInStatus']        = 0;
        $formData['Birthday']           = $formData['BirthdayYear'] . '-' .
                                          $formData['BirthdayMonth'] . '-' .
                                          $formData['BirthdayDay'];

        // Create new regular customer and perform a log in
        $customer = new SilvercartRegularCustomer();

        // Pass shoppingcart to registered customer and delete the anonymous
        // customer.
        if ($anonymousCustomer) {
            $newShoppingCart = $anonymousCustomer->SilvercartShoppingCart()->duplicate(true);

            foreach ($anonymousCustomer->SilvercartShoppingCart()->SilvercartShoppingCartPositions() as $shoppingCartPosition) {
                $newShoppingCartPosition = $shoppingCartPosition->duplicate(false);
                $newShoppingCartPosition->SilvercartShoppingCartID = $newShoppingCart->ID;
                $newShoppingCartPosition->write();
            }

            $customer->SilvercartShoppingCartID = $newShoppingCart->ID;
            $anonymousCustomer->delete();
        }

        $customer->castedUpdate($formData);
        $customer->setField('ConfirmationBacklink',     $formData['backlink']);
        $customer->setField('ConfirmationBacklinkText', $formData['backlinkText']);
        $customer->setField('OptInTempText',            $formData['optInTempText']);
        $customer->write();
        $customer->logIn();
        $customer->changePassword($formData['Password']);

        // Add customer to intermediate group
        $customerGroup = DataObject::get_one(
            'Group',
            "`Code` = 'b2c-optin'"
        );
        if ($customerGroup) {
            $customer->Groups()->add($customerGroup);
        }

        // Create ShippingAddress for customer and populate it with registration data
        $shippingAddress = new SilvercartShippingAddress();
        $shippingAddress->castedUpdate($formData);

        $country = DataObject::get_one(
            'SilvercartCountry',
            sprintf(
                "`Title` = '%s'",
                $formData['Country']
            )
        );
        if ($country) {
            $shippingAddress->SilvercartCountryID = $country->ID;
        }
        $shippingAddress->write();

        // Create InvoiceAddress for customer and populate it with registration data
        $invoiceAddress = new SilvercartInvoiceAddress();
        $invoiceAddress->castedUpdate($formData);
        if ($country) {
            $invoiceAddress->SilvercartCountryID = $country->ID;
        }
        $invoiceAddress->write();

        //connect the ShippingAddress and the InvoiceAddress to the customer
        $customer->SilvercartShippingAddressID = $shippingAddress->ID;
        $customer->SilvercartInvoiceAddressID  = $invoiceAddress->ID;
        $customer->SilvercartAddress()->add($shippingAddress);
        $customer->SilvercartAddress()->add($invoiceAddress);
        $customer->write();

        $this->sendOptInMail($formData);

        // Remove from the anonymous newsletter recipients list
        SilvercartAnonymousNewsletterRecipient::removeByEmailAddress($customer->Email);

        // Redirect to welcome page
        Director::redirect($this->controller->PageByIdentifierCode('RegistrationWelcomePage')->Link());
    }

    /**
     * sendOptInMail
     *
     * @param array $formData Enthaelt die geparsten Formulardaten
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 25.10.2010
     */
    protected function sendOptInMail($formData) {
        SilvercartShopEmail::send(
            'RegistrationOptIn',
            $formData['Email'],
            array(
                'FirstName'         => $formData['FirstName'],
                'Surname'           => $formData['Surname'],
                'Email'             => $formData['Email'],
                'ConfirmationLink'  => SilvercartPage_Controller::PageByIdentifierCode("SilvercartRegisterConfirmationPage")->getAbsoluteLiveLink(false).'/?h='.urlencode($formData['ConfirmationHash'])
            )
        );
    }
}