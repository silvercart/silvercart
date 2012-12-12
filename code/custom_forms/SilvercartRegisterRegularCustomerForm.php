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
        'IsBusinessAccount' => array(
            'type'      => 'CheckboxField',
            'title'     => 'Is business account'
        ),
        'TaxIdNumber' => array(
            'type'      => 'TextField',
            'title'     => 'Tax ID Number',
            'maxLength' => 30,
            'checkRequirements' => array(
                'isFilledInDependantOn' => array(
                    'field'     => 'IsBusinessAccount',
                    'hasValue'  => '1'
                )
            )
        ),
        'Company' => array(
            'type'      => 'TextField',
            'title'     => 'Company',
            'maxLength' => 50,
            'checkRequirements' => array(
                'isFilledInDependantOn' => array(
                    'field'     => 'IsBusinessAccount',
                    'hasValue'  => '1'
                )
            )
        ),
        'Salutation' => array(
            'type'  => 'DropdownField',
            'title' => 'Anrede',
            'value' => array(
                ''      => 'Bitte wählen',
                'Frau'  => 'Frau',
                'Herr'  => 'Herr'
            ),
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'FirstName' => array(
            'type'              => 'TextField',
            'title'             => 'Vorname',
            'checkRequirements' => array(
                'isFilledIn'    => true,
                'hasMinLength'  => 3
            )
        ),
        'Surname' => array(
            'type'              => 'TextField',
            'title'             => 'Nachname',
            'checkRequirements' => array(
                'isFilledIn'    => true,
                'hasMinLength'  => 3
            )
        ),
        'Street' => array(
            'type'              => 'TextField',
            'title'             => 'Straße',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'StreetNumber' => array(
            'type'              => 'TextField',
            'title'             => 'Hausnummer',
            'maxLength'         => 10,
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Postcode' => array(
            'type'              => 'TextField',
            'title'             => 'PLZ',
            'maxLength'         => 10,
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'City' => array(
            'type'              => 'TextField',
            'title'             => 'Stadt',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Country' => array(
            'type'              => 'DropdownField',
            'title'             => 'Land',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Email' => array(
            'type'              => 'TextField',
            'title'             => 'Email Adresse',
            'checkRequirements' => array(
                'isEmailAddress'    => true,
                'isFilledIn'        => true,
                'callBack'          => 'doesEmailExistAlready'
            )
        ),
        'EmailCheck' => array(
            'type'              => 'TextField',
            'title'             => 'E-Mail-Adresse Gegenprüfung',
            'checkRequirements' => array(
               'isFilledIn' => true,
               'mustEqual'  => 'Email',
            )
        ),
        'PhoneAreaCode' => array(
            'type'              => 'TextField',
            'title'             => 'Telefon Vorwahl',
            'checkRequirements' => array(
                'isFilledIn'    => true,
                'isNumbersOnly' => true
            )
        ),
        'Phone' => array(
            'type'              => 'TextField',
            'title'             => 'Telefon Nummer.',
            'checkRequirements' => array(
                'isFilledIn'    => true,
                'isNumbersOnly' => true
            )
        ),
        'Fax' => array(
            'type'  => 'TextField',
            'title' => 'Fax'
        ),
        'BirthdayDay' => array(
            'type'              => 'DropdownField',
            'title'             => 'Tag',
            'value'             => array(),
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'BirthdayMonth' => array(
            'type'              => 'DropdownField',
            'title'             => 'Monat',
            'value'             => array(),
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'BirthdayYear' => array(
            'type'              => 'TextField',
            'title'             => 'Jahr',
            'maxLength'         => 4,
            'checkRequirements' => array(
                'isFilledIn'    => true,
                'isNumbersOnly' => true,
                'hasLength'     => 4
            )
        ),
        'Password' => array(
            'type'              => 'PasswordField',
            'title'             => 'Passwort',
            'checkRequirements' => array(
                'isFilledIn'    => true,
                'hasMinLength'  => 6,
                'mustNotEqual'  => 'Email',
            )
        ),
        'PasswordCheck' => array(
            'type'              => 'PasswordField',
            'title'             => 'Passwort Gegenprüfung',
            'checkRequirements' => array(
                'isFilledIn'    => true,
                'mustEqual'     => 'Password'
            )
        ),
        'SubscribedToNewsletter' => array(
            'type'  => 'CheckboxField',
            'title' => 'Ich möchte den Newsletter abonnieren'
        ),
        'backlink' => array(
            'type'  => 'HiddenField',
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
        'submitButtonTitle'  => 'Abschicken',
        'markRequiredFields' => true
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
        parent::fillInFieldValues();
        $this->formFields['IsBusinessAccount']['title']         = _t('SilvercartCustomer.ISBUSINESSACCOUNT');
        $this->formFields['TaxIdNumber']['title']               = _t('SilvercartAddress.TAXIDNUMBER');
        $this->formFields['Company']['title']                   = _t('SilvercartAddress.COMPANY');
        $this->formFields['Salutation']['title']                = _t('SilvercartAddress.SALUTATION');
        $this->formFields['Salutation']['value']                = array('' => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE'), "Frau" => _t('SilvercartAddress.MISSES'), "Herr" => _t('SilvercartAddress.MISTER'));
        $this->formFields['FirstName']['title']                 = _t('SilvercartAddress.FIRSTNAME', 'firstname');
        $this->formFields['Surname']['title']                   = _t('SilvercartAddress.SURNAME', 'surname');
        $this->formFields['Email']['title']                     = _t('SilvercartAddress.EMAIL', 'email address');
        $this->formFields['EmailCheck']['title']                = _t('SilvercartAddress.EMAIL_CHECK', 'email address check');
        $this->formFields['Street']['title']                    = _t('SilvercartAddress.STREET', 'street');
        $this->formFields['StreetNumber']['title']              = _t('SilvercartAddress.STREETNUMBER', 'streetnumber');
        $this->formFields['Postcode']['title']                  = _t('SilvercartAddress.POSTCODE', 'postcode');
        $this->formFields['City']['title']                      = _t('SilvercartAddress.CITY', 'city');
        $this->formFields['Phone']['title']                     = _t('SilvercartAddress.PHONE', 'phone');
        $this->formFields['PhoneAreaCode']['title']             = _t('SilvercartAddress.PHONEAREACODE', 'phone area code');
        $this->formFields['Fax']['title']                       = _t('SilvercartAddress.FAX');
        $this->formFields['Country']['title']                   = _t('SilvercartCountry.SINGULARNAME');
        $this->formFields['BirthdayDay']['title']               = _t('SilvercartPage.DAY');
        $this->formFields['BirthdayMonth']['title']             = _t('SilvercartPage.MONTH');
        $this->formFields['BirthdayYear']['title']              = _t('SilvercartPage.YEAR');
        $this->formFields['Password']['title']                  = _t('SilvercartPage.PASSWORD');
        $this->formFields['PasswordCheck']['title']             = _t('SilvercartPage.PASSWORD_CHECK');
        $this->formFields['SubscribedToNewsletter']['title']    = _t('SilvercartCheckoutFormStep.I_SUBSCRIBE_NEWSLETTER');
        $birthdayDays = array(
            '' => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE')
        );
        $birthdayMonths = array(
            ''  => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE'),
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

        $this->formFields['Country']['value'] = SilvercartCountry::getPrioritiveDropdownMap(true, _t('SilvercartCheckoutFormStep2.EMPTYSTRING_COUNTRY'));

        if (isset($_GET['backlink'])) {
            $this->formFields['backlink']['value'] = Convert::raw2sql($_GET['backlink']);
        }
        
        if (!$this->demandBirthdayDate()) {
            unset($this->formFields['BirthdayDay']);
            unset($this->formFields['BirthdayMonth']);
            unset($this->formFields['BirthdayYear']);
        }
        
        if (!$this->EnableBusinessCustomers()) {
            unset($this->formFields['IsBusinessAccount']);
            unset($this->formFields['TaxIdNumber']);
            unset($this->formFields['Company']);
        }
    }

    /**
     * Form callback: Does the entered Email already exist?
     *
     * @param string $value the email address to be checked
     *
     * @return array to be rendered in the template
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Patrick Schneider <pschneider@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.11.2012
     */
    public function doesEmailExistAlready($value) {
        return SilvercartFormValidation::doesEmailExistAlready($value);
    }
    
    /**
     * Indicates wether the registration fields specific to business customers
     * should be shown.
     *
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.12.2011
     */
    public function EnableBusinessCustomers() {
        return SilvercartConfig::enableBusinessCustomers();
    }
    
    /**
     * Indicates wether the birthday date has to be entered.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.10.2011
     */
    public function demandBirthdayDate() {
        return SilvercartConfig::demandBirthdayDateOnRegistration();
    }

    /**
     * No validation errors occured, so we register the customer and send
     * mails with further instructions for the double opt-in procedure.
     *
     * @param SS_HTTPRequest $data     SS session data
     * @param Form           $form     the form object
     * @param array          $formData CustomHTMLForms session data
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.12.2012
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

        // Aggregate Data and set defaults
        $formData['MemberID']           = Member::currentUserID();
        $formData['Locale']             = Translatable::get_current_locale();
        if ($this->demandBirthdayDate()) {
            $formData['Birthday']           = $formData['BirthdayYear'] . '-' .
                                              $formData['BirthdayMonth'] . '-' .
                                              $formData['BirthdayDay'];
        }

        // Create new regular customer and perform a log in
        $customer = new Member();

        // Pass shoppingcart to registered customer and delete the anonymous
        // customer.
        if ($anonymousCustomer) {
            $newShoppingCart = $anonymousCustomer->SilvercartShoppingCart()->duplicate(true);

            foreach ($anonymousCustomer->SilvercartShoppingCart()->SilvercartShoppingCartPositions() as $shoppingCartPosition) {
                $newShoppingCartPosition = $shoppingCartPosition->duplicate(false);
                $newShoppingCartPosition->SilvercartShoppingCartID = $newShoppingCart->ID;
                $newShoppingCartPosition->write();

                $shoppingCartPosition->transferToNewPosition($newShoppingCartPosition);
            }

            $customer->SilvercartShoppingCartID = $newShoppingCart->ID;
            $anonymousCustomer->delete();
        }

        $customer->castedUpdate($formData);
        $customer->write();
        $customer->logIn();
        $customer->changePassword($formData['Password']);

        // Add customer to intermediate group
        
        if (array_key_exists('IsBusinessAccount', $formData) &&
            $formData['IsBusinessAccount'] == '1') {
            
            $customerGroup = DataObject::get_one(
                'Group',
                "`Code` = 'b2b'"
            );
        } else {
            $customerGroup = DataObject::get_one(
                'Group',
                "`Code` = 'b2c'"
            );
        }
        if ($customerGroup) {
            $customer->Groups()->add($customerGroup);
        }

        // Create ShippingAddress for customer and populate it with registration data
        $shippingAddress = new SilvercartShippingAddress();
        $shippingAddress->castedUpdate($formData);

        $country = DataObject::get_by_id(
            'SilvercartCountry',
            (int) $formData['Country']
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
        $customer->SilvercartAddresses()->add($shippingAddress);
        $customer->SilvercartAddresses()->add($invoiceAddress);
        $customer->write();

        // Remove from the anonymous newsletter recipients list
        if (SilvercartAnonymousNewsletterRecipient::doesExist($customer->Email)) {
            $recipient = SilvercartAnonymousNewsletterRecipient::getByEmailAddress($customer->Email);
            
            if ($recipient->NewsletterOptInStatus) {
                $customer->NewsletterOptInStatus      = 1;
                $customer->NewsletterConfirmationHash = $recipient->NewsletterOptInConfirmationHash;
                $customer->write();
            }
            SilvercartAnonymousNewsletterRecipient::removeByEmailAddress($customer->Email);
        }
        
        if ( $customer->SubscribedToNewsletter &&
            !$customer->NewsletterOptInStatus) {
            
            SilvercartNewsletter::subscribeRegisteredCustomer($customer);
        }

        // Redirect to welcome page
        if (array_key_exists('backlink', $formData) &&
            !empty($formData['backlink'])) {
            
             Director::redirect($formData['backlink']);
        } else {
            Director::redirect($this->controller->PageByIdentifierCode('SilvercartRegisterConfirmationPage')->Link());
        }
    }
}
