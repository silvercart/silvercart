<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 * @since 21.10.2010
 */
class SilvercartRegisterRegularCustomerForm extends CustomHtmlForm {

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
     * @since 07.01.2011
     */
    public function __construct($controller, $params = null, $preferences = null, $barebone = false) {
        $this->preferences['submitButtonTitle'] = _t('SilvercartPage.SUBMIT', 'Send');
        parent::__construct($controller, $params, $preferences, $barebone);
    }
    
    /**
     * Returns the form fields.
     * 
     * @param bool $withUpdate Call extensions to update fields?
     * 
     * @return array
     */
    public function getFormFields($withUpdate = true) {
        if (!array_key_exists('Salutation', $this->formFields)) {
            
            $birthdayFields = array();
            $businessFields = array();
            
            if ($this->demandBirthdayDate()) {
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
                
                $birthdayFields = array(
                    'BirthdayDay' => array(
                        'type'              => 'DropdownField',
                        'title'             => _t('SilvercartPage.DAY'),
                        'value'             => $birthdayDays,
                        'checkRequirements' => array(
                            'isFilledIn' => true
                        )
                    ),
                    'BirthdayMonth' => array(
                        'type'              => 'DropdownField',
                        'title'             => _t('SilvercartPage.MONTH'),
                        'value'             => $birthdayMonths,
                        'checkRequirements' => array(
                            'isFilledIn' => true
                        )
                    ),
                    'BirthdayYear' => array(
                        'type'              => 'TextField',
                        'title'             => _t('SilvercartPage.YEAR'),
                        'maxLength'         => 4,
                        'checkRequirements' => array(
                            'isFilledIn'    => true,
                            'isNumbersOnly' => true,
                            'hasLength'     => 4
                        )
                    ),
                );
            }

            if ($this->EnableBusinessCustomers()) {
                $businessFields = array(
                    'IsBusinessAccount' => array(
                        'type'      => 'CheckboxField',
                        'title'     => _t('SilvercartCustomer.ISBUSINESSACCOUNT')
                    ),
                    'TaxIdNumber' => array(
                        'type'      => 'TextField',
                        'title'     => _t('SilvercartAddress.TAXIDNUMBER'),
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
                        'title'     => _t('SilvercartAddress.COMPANY'),
                        'maxLength' => 50,
                        'checkRequirements' => array(
                            'isFilledInDependantOn' => array(
                                'field'     => 'IsBusinessAccount',
                                'hasValue'  => '1'
                            )
                        )
                    ),
                );
            }

            $backlink = '';
            if (isset($_GET['backlink'])) {
                $backlink = Convert::raw2sql($_GET['backlink']);
            }
            
            $formFields = array(
                'Salutation' => array(
                    'type'  => 'DropdownField',
                    'title' => _t('SilvercartAddress.SALUTATION'),
                    'value' => array(
                        ''     => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE'),
                        'Frau' => _t('SilvercartAddress.MISSES'),
                        'Herr' => _t('SilvercartAddress.MISTER')
                    ),
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'AcademicTitle' => array(
                    'type'              => 'TextField',
                    'title'             => _t('SilvercartAddress.AcademicTitle', 'Academic title'),
                ),
                'FirstName' => array(
                    'type'              => 'TextField',
                    'title'             => _t('SilvercartAddress.FIRSTNAME', 'firstname'),
                    'checkRequirements' => array(
                        'isFilledIn'    => true,
                        'hasMinLength'  => 3
                    )
                ),
                'Surname' => array(
                    'type'              => 'TextField',
                    'title'             => _t('SilvercartAddress.SURNAME', 'surname'),
                    'checkRequirements' => array(
                        'isFilledIn'    => true,
                        'hasMinLength'  => 3
                    )
                ),
                'Addition' => array(
                    'type'      => 'TextField',
                    'title'     => _t('SilvercartAddress.ADDITION', 'Addition'),
                ),
                'Street' => array(
                    'type'              => 'TextField',
                    'title'             => _t('SilvercartAddress.STREET', 'street'),
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'StreetNumber' => array(
                    'type'              => 'TextField',
                    'title'             => _t('SilvercartAddress.STREETNUMBER', 'streetnumber'),
                    'maxLength'         => 10,
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'Postcode' => array(
                    'type'              => 'TextField',
                    'title'             => _t('SilvercartAddress.POSTCODE', 'postcode'),
                    'maxLength'         => 10,
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'City' => array(
                    'type'              => 'TextField',
                    'title'             => _t('SilvercartAddress.CITY', 'city'),
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'Country' => array(
                    'type'              => 'DropdownField',
                    'title'             => _t('SilvercartCountry.SINGULARNAME'),
                    'value'             => SilvercartCountry::getPrioritiveDropdownMap(true, _t('SilvercartCheckoutFormStep2.EMPTYSTRING_COUNTRY')),
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'Email' => array(
                    'type'              => 'TextField',
                    'title'             => _t('SilvercartAddress.EMAIL', 'email address'),
                    'checkRequirements' => array(
                        'isEmailAddress'    => true,
                        'isFilledIn'        => true,
                        'callBack'          => 'doesEmailExistAlready'
                    )
                ),
                'EmailCheck' => array(
                    'type'              => 'TextField',
                    'title'             => _t('SilvercartAddress.EMAIL_CHECK', 'email address check'),
                    'checkRequirements' => array(
                       'isFilledIn' => true,
                       'mustEqual'  => 'Email',
                    )
                ),
                'PhoneAreaCode' => array(
                    'type'              => 'TextField',
                    'title'             => _t('SilvercartAddress.PHONEAREACODE', 'phone area code'),
                    'checkRequirements' => array(
                        'isFilledIn'    => true,
                        'isNumbersOnly' => true
                    )
                ),
                'Phone' => array(
                    'type'              => 'TextField',
                    'title'             => _t('SilvercartAddress.PHONE', 'phone'),
                    'checkRequirements' => array(
                        'isFilledIn'    => true,
                        'isNumbersOnly' => true
                    )
                ),
                'Fax' => array(
                    'type'  => 'TextField',
                    'title' => _t('SilvercartAddress.FAX')
                ),
                'Password' => array(
                    'type'              => 'PasswordField',
                    'title'             => _t('SilvercartPage.PASSWORD'),
                    'checkRequirements' => array(
                        'isFilledIn'    => true,
                        'hasMinLength'  => 6,
                        'mustNotEqual'  => 'Email',
                    )
                ),
                'PasswordCheck' => array(
                    'type'              => 'PasswordField',
                    'title'             => _t('SilvercartPage.PASSWORD_CHECK'),
                    'checkRequirements' => array(
                        'isFilledIn'    => true,
                        'mustEqual'     => 'Password'
                    )
                ),
                'SubscribedToNewsletter' => array(
                    'type'  => 'CheckboxField',
                    'title' => _t('SilvercartCheckoutFormStep.I_SUBSCRIBE_NEWSLETTER')
                ),
                'backlink' => array(
                    'type'  => 'HiddenField',
                    'value' => $backlink
                )
            );
            
            $this->formFields = array_merge(
                    $this->formFields,
                    $formFields,
                    $businessFields,
                    $birthdayFields
            );
        }
        return parent::getFormFields($withUpdate);
    }

    /**
     * Set initial values in form fields
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.04.2014
     */
    protected function fillInFieldValues() {
        parent::fillInFieldValues();
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
     * Returns whether there is a minimum age to order.
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.01.2014
     */
    public function UseMinimumAgeToOrder() {
        return SilvercartConfig::UseMinimumAgeToOrder();
    }
    
    /**
     * Returns the minimum age to order.
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.01.2014
     */
    public function MinimumAgeToOrder() {
        return SilvercartConfig::MinimumAgeToOrder();
    }

    /**
     * No validation errors occured, so we register the customer and send
     * mails with further instructions for the double opt-in procedure.
     *
     * @param SS_HTTPRequest $data       SS session data
     * @param Form           $form       the form object
     * @param array          $formData   CustomHTMLForms session data
     * @param bool           $doRedirect Set to true to redirect after submitSuccess
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.01.2015
     */
    protected function submitSuccess($data, $form, $formData, $doRedirect = true) {
        $anonymousCustomer = false;

        /*
         * Logout anonymous users and save their shoppingcart temporarily.
         */
        if (SilvercartCustomer::currentUser()) {
            $anonymousCustomer = SilvercartCustomer::currentUser();
            SilvercartCustomer::currentUser()->logOut();
        }

        // Aggregate Data and set defaults
        $formData['MemberID']           = Member::currentUserID();
        $formData['Locale']             = Translatable::get_current_locale();
        if ($this->demandBirthdayDate()) {
            $formData['Birthday']           = $formData['BirthdayYear'] . '-' .
                                              $formData['BirthdayMonth'] . '-' .
                                              $formData['BirthdayDay'];
            if ($this->UseMinimumAgeToOrder()) {
                if (!SilvercartConfig::CheckMinimumAgeToOrder($formData['Birthday'])) {
                    $this->errorMessages['BirthdayDay'] = array(
                        'message'     => SilvercartConfig::MinimumAgeToOrderError(),
                        'fieldname'   => _t('SilvercartPage.BIRTHDAY'),
                        'BirthdayDay' => array(
                            'message' => SilvercartConfig::MinimumAgeToOrderError(),
                        )
                    );
                    $this->errorMessages['BirthdayMonth'] = array(
                        'message'       => SilvercartConfig::MinimumAgeToOrderError(),
                        'fieldname'     => _t('SilvercartPage.BIRTHDAY'),
                        'BirthdayMonth' => array(
                            'message' => SilvercartConfig::MinimumAgeToOrderError(),
                        )
                    );
                    $this->errorMessages['BirthdayYear'] = array(
                        'message'      => SilvercartConfig::MinimumAgeToOrderError(),
                        'fieldname'    => _t('SilvercartPage.BIRTHDAY'),
                        'BirthdayYear' => array(
                            'message' => SilvercartConfig::MinimumAgeToOrderError(),
                        )
                    );
                    $this->setSubmitSuccess(false);
                    return $this->submitFailure($data, $form);
                }
            }
        }

        // Create new regular customer and perform a log in
        $customer = new Member();

        // Pass shoppingcart to registered customer and delete the anonymous
        // customer.
        if ($anonymousCustomer) {
            $newShoppingCart = $anonymousCustomer->getCart()->duplicate(true);

            foreach ($anonymousCustomer->getCart()->SilvercartShoppingCartPositions() as $shoppingCartPosition) {
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

        $customerGroup = $this->getTargetCustomerGroup($formData);
        if ($customerGroup) {
            $customer->Groups()->add($customerGroup);
        }

        // Create ShippingAddress for customer and populate it with registration data
        $address = new SilvercartAddress();
        $address->castedUpdate($formData);

        $country = DataObject::get_by_id(
            'SilvercartCountry',
            (int) $formData['Country']
        );
        if ($country) {
            $address->SilvercartCountryID = $country->ID;
        }
        $address->write();
        $this->extend('updateRegisteredAddress', $address, $data, $form, $formData);

        //connect the ShippingAddress and the InvoiceAddress to the customer
        $customer->SilvercartShippingAddressID = $address->ID;
        $customer->SilvercartInvoiceAddressID  = $address->ID;
        $customer->SilvercartAddresses()->add($address);
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
        $this->extend('updateRegisteredCustomer', $customer, $data, $form, $formData);

        if ($doRedirect) {
            // Redirect to welcome page
            if (array_key_exists('backlink', $formData) &&
                !empty($formData['backlink'])) {

                 $this->controller->redirect($formData['backlink']);
            } else {
                $this->controller->redirect($this->controller->PageByIdentifierCode('SilvercartRegisterConfirmationPage')->Link());
            }
        }
    }
    
    /**
     * Returns the target customer group.
     * 
     * @param array $formData Submitted form data.
     * 
     * @return Group
     */
    public function getTargetCustomerGroup($formData) {
        if (array_key_exists('IsBusinessAccount', $formData) &&
            $formData['IsBusinessAccount'] == '1') {
            $customerGroup = SilvercartCustomer::default_customer_group_b2b();
        } else {
            $customerGroup = SilvercartCustomer::default_customer_group();
        }
        return $customerGroup;
    }
}
