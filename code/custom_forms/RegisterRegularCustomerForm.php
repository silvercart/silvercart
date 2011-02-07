<?php

/**
 * CustomHtmlForm for registration of a regular customer
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license BSD
 * @since 21.10.2010
 */
class RegisterRegularCustomerForm extends CustomHtmlForm {

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
                'mustNotEqual' => 'FirstName'
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
        )
    );

    /**
     * Set initial values in form fields
     *
     * @return void
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.10.2010
     */
    protected function fillInFieldValues() {
        $this->formFields['Salutation']['title'] = _t('Address.SALUTATION');
        $this->formFields['Salutation']['value'] = array('' => _t('EditAddressForm.EMPTYSTRING_PLEASECHOOSE'), "Frau" => _t('Address.MISSIS'), "Herr" => _t('Address.MISTER'));
        $this->formFields['BirthdayDay']['title'] = _t('Page.DAY');
        $this->formFields['BirthdayMonth']['title'] = _t('Page.MONTH');
        $this->formFields['BirthdayYear']['title'] = _t('Page.YEAR');
        $this->formFields['Password']['title'] = _t('Page.PASSWORD');
        $this->formFields['PasswordCheck']['title'] = _t('Page.PASSWORD_CHECK');
        $this->formFields['HasAcceptedTermsAndConditions']['title'] = _t('CheckoutFormStep.I_ACCEPT_TERMS');
        $this->formFields['HasAcceptedRevocationInstruction']['title'] = _t('CheckoutFormStep.I_ACCEPT_REVOCATION');
        $this->formFields['SubscribedToNewsletter']['title'] = _t('CheckoutFormStep.I_SUBSCRIBE_NEWSLETTER');
        $birthdayDays = array(
            '' => _t('EditAddressForm.EMPTYSTRING_PLEASECHOOSE')
        );
        $birthdayMonths = array(
            '' => _t('EditAddressForm.EMPTYSTRING_PLEASECHOOSE'),
            '1' => _t('Page.JANUARY'),
            '2' => _t('Page.FEBRUARY'),
            '3' => _t('Page.MARCH'),
            '4' => _t('Page.APRIL'),
            '5' => _t('Page.MAY'),
            '6' => _t('Page.JUNE'),
            '7' => _t('Page.JULY'),
            '8' => _t('Page.AUGUST'),
            '9' => _t('Page.SEPTEMBER'),
            '10' => _t('Page.OCTOBER'),
            '11' => _t('Page.NOVEMBER'),
            '12' => _t('Page.DECEMBER')
        );

        for ($idx = 1; $idx < 32; $idx++) {
            $birthdayDays[$idx] = $idx;
        }

        $this->formFields['BirthdayDay']['value'] = $birthdayDays;
        $this->formFields['BirthdayMonth']['value'] = $birthdayMonths;

        $this->formFields['Country']['value'] = DataObject::get('Country')->toDropdownMap('Title', 'Title', '-bitte wählen-');
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
            'errorMessage' => _t('Page.EMAIL_ALREADY_REGISTERED', 'This Email address is already registered')
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
        /*
         * Logout any user before registration, which should not happen, because the form is not shown if logged in
         * This is just double precaution
         */
        if (Member::currentUser()) {
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
        $formData['ownerID']            = Member::currentUserID();
        $formData['ConfirmationHash']   = $confirmationHash;
        $formData['Locale']             = 'de_DE';
        $formData['OptInStatus']        = 0;
        $formData['Birthday']           = $formData['BirthdayYear'] . '-' .
                                          $formData['BirthdayMonth'] . '-' .
                                          $formData['BirthdayDay'];

        // Create new regular customer and perform a log in
        $customer = new RegularCustomer();
        $customer->castedUpdate($formData);
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
        $shippingAddress = new ShippingAddress();
        $shippingAddress->castedUpdate($formData);

        $country = DataObject::get_one(
            'Country',
            sprintf(
                "`Title` = '%s'",
                $formData['Country']
            )
        );
        if ($country) {
            $shippingAddress->countryID = $country->ID;
        }
        $shippingAddress->write();

        // Create InvoiceAddress for customer and populate it with registration data
        $invoiceAddress = new InvoiceAddress();
        $invoiceAddress->castedUpdate($formData);
        if ($country) {
            $invoiceAddress->countryID = $country->ID;
        }
        $invoiceAddress->write();

        //connect the ShippingAddress and the InvoiceAddress to the customer
        $customer->shippingAddressID = $shippingAddress->ID;
        $customer->invoiceAddressID  = $invoiceAddress->ID;
        $customer->write();

        $this->sendOptInMail($formData);

        // Redirect to welcome page
        $param = _t('RegistrationPage.URL_SEGMENT')."/"._t('Page.WELCOME_PAGE_URL_SEGMENT');
        Director::redirect($param);
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
        ShopEmail::send(
            'RegistrationOptIn',
            $formData['Email'],
            array(
                'FirstName'         => $formData['FirstName'],
                'Surname'           => $formData['Surname'],
                'Email'             => $formData['Email'],
                'ConfirmationLink'  => Director::absoluteURL(sprintf("/%s/%s", _t('RegistrationPage.URL_SEGMENT'), _t('RegisterConfirmationPage.URL_SEGMENT'))).'/?h='.urlencode($formData['ConfirmationHash'])
            )
        );
    }
}