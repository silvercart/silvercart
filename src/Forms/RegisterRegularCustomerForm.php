<?php

namespace SilverCart\Forms;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\CustomForm;
use SilverCart\Forms\FormFields\TextField;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Newsletter\AnonymousNewsletterRecipient;
use SilverCart\Model\Newsletter\Newsletter;
use SilverCart\Model\Pages\CheckoutStep;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Pages\RegistrationPage;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\PasswordField;
use SilverStripe\Security\Member;
use SilverStripe\Security\MemberAuthenticator\MemberAuthenticator;
use SilverStripe\Security\Security;

/**
 * Form for registration of a regular customer.
 *
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class RegisterRegularCustomerForm extends CustomForm
{
    use HoneyPotable;
    /**
     * List of required fields.
     *
     * @var array
     */
    private static $requiredFields = [
        'Salutation',
        'FirstName' => [
            'isFilledIn'   => true,
            'hasMinLength' => 3,
        ],
        'Surname' => [
            'isFilledIn'   => true,
            'hasMinLength' => 3,
        ],
        'Street',
        'StreetNumber',
        'Postcode',
        'City',
        'Country',
        'Email' => [
            'isEmailAddress' => true,
            'isFilledIn'     => true,
            'doesEmailExist' => false,
        ],
        'EmailCheck' => [
            'isFilledIn' => true,
            'mustEqual'  => 'Email',
        ],
        'Password' => [
            'isValidPassword' => true,
        ],
        'PasswordCheck' => [
            'mustEqual' => 'Password',
        ],
    ];
    /**
     * Optional backlink to overwrite the default redirection after a successful submission.
     *
     * @var string
     */
    protected $backLink = '';
    /**
     * Holds the registered Member.
     *
     * @var Member
     */
    protected $customer = '';

    /**
     * Returns the required fields.
     * 
     * @return array
     */
    public function getRequiredFields()
    {
        if ($this->demandBirthdayDate()) {
            $requiredFields = self::config()->get('requiredFields');
            $requiredFields += [
                'BirthdayDay' => [
                    'isFilledIn' => true,
                ],
                'BirthdayMonth',
                'BirthdayYear' => [
                    'isFilledIn'    => true,
                    'isNumbersOnly' => true,
                    'hasLength'     => 4,
                ],
            ];
            if ($this->UseMinimumAgeToOrder()) {
                $requiredFields['BirthdayDay']['hasMinAge'] = Config::MinimumAgeToOrder();
            }
            self::config()->set('requiredFields', $requiredFields);
        }
        if ($this->EnableBusinessCustomers()) {
            $requiredFields = self::config()->get('requiredFields');
            $requiredFields += [
                'TaxIdNumber' => [
                    'isFilledInDependentOn' => [
                        'field'     => 'IsBusinessAccount',
                        'hasValue'  => '1'
                    ],
                ],
                'Company' => [
                    'isFilledInDependentOn' => [
                        'field'     => 'IsBusinessAccount',
                        'hasValue'  => '1'
                    ],
                ],
            ];
            self::config()->set('requiredFields', $requiredFields);
        }
        if ($this->EnableHoneyPot()) {
            $honeyPotField = $this->getHoneyPotField();
            $requiredFields[$honeyPotField->Name] = [
                'isFilledIn' => false,
            ];
        }
        return parent::getRequiredFields();
    }

    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields()
    {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $passwordField      = PasswordField::create('Password', Page::singleton()->fieldLabel('Password'));
            $passwordCheckField = PasswordField::create('PasswordCheck', Page::singleton()->fieldLabel('PasswordCheck'));
            $passwordPattern    = CustomRequiredFields::config()->password_pattern;
            $passwordMinlength  = CustomRequiredFields::config()->password_minlength;
            if (!empty($passwordPattern)) {
                $passwordField->setAttribute('pattern', $passwordPattern);
                $passwordCheckField->setAttribute('pattern', $passwordPattern);
            }
            if (!empty($passwordMinlength)) {
                $passwordField->setAttribute('minlength', $passwordMinlength);
                $passwordCheckField->setAttribute('minlength', $passwordMinlength);
                $passwordField->setDescription(_t(self::class . '.PasswordHint', 'Create a password for your login. Your password needs at least {minlength} characters and contain at least 1 capital letter, 1 small letter and 1 number.', [
                    'minlength' => $passwordMinlength,
                ]));
            }
            $address = Address::singleton();
            $fields  = array_merge(
                    $fields,
                    $this->getBirthdayFields(),
                    $this->getBusinessFields(),
                    $this->getHoneyPotFields(),
                    [
                        DropdownField::create('Salutation',    $address->fieldLabel('Salutation'),    Tools::getSalutationMap()),
                        TextField::create(    'AcademicTitle', $address->fieldLabel('AcademicTitle'), '', $address->config()->max_length_academic_title),
                        TextField::create(    'FirstName',     $address->fieldLabel('FirstName'),     '', $address->config()->max_length_first_name),
                        TextField::create(    'Surname',       $address->fieldLabel('Surname'),       '', $address->config()->max_length_surname),
                        TextField::create(    'Addition',      $address->fieldLabel('Addition'),      '', $address->config()->max_length_addition),
                        TextField::create(    'Street',        $address->fieldLabel('Street'),        '', $address->config()->max_length_street),
                        TextField::create(    'StreetNumber',  $address->fieldLabel('StreetNumber'),  '', $address->config()->max_length_street_number),
                        TextField::create(    'Postcode',      $address->fieldLabel('Postcode'),      '', $address->config()->max_length_postcode),
                        TextField::create(    'City',          $address->fieldLabel('City'),          '', $address->config()->max_length_city),
                        DropdownField::create('Country',       $address->fieldLabel('Country'),       Country::getPrioritiveDropdownMap(true, Tools::field_label('PleaseChoose'))),
                        TextField::create(    'Phone',         $address->fieldLabel('Phone')),
                        TextField::create(    'Fax',           $address->fieldLabel('Fax')),
                        EmailField::create(   'Email',         $address->fieldLabel('Email')),
                        EmailField::create(   'EmailCheck',    $address->fieldLabel('EmailCheck')),
                        $passwordField,
                        $passwordCheckField,
                        $newsletterField = CheckboxField::create('SubscribedToNewsletter', CheckoutStep::singleton()->fieldLabel('SubscribeNewsletter')),
                        HiddenField::create('backlink', 'backlink', $this->getBackLink()),
                    ]
            );
            $newsletterField->setDescription(Newsletter::singleton()->fieldLabel('OptInNotFinished'));
        });
        return parent::getCustomFields();
    }
    
    /**
     * Returns the birthday fields if enabled.
     * 
     * @return array
     */
    protected function getBirthdayFields()
    {
        $birthdayFields = [];
        if ($this->demandBirthdayDate()) {
            $birthdayDays = [
                '' => Tools::field_label('PleaseChoose')
            ];
            for ($idx = 1; $idx < 32; $idx++) {
                $birthdayDays[$idx] = $idx;
            }

            $birthdayFields = [
                DropdownField::create('BirthdayDay', Page::singleton()->fieldLabel('Day'), $birthdayDays),
                DropdownField::create('BirthdayMonth', Page::singleton()->fieldLabel('Month'), Tools::getMonthMap()),
                TextField::create('BirthdayYear', Page::singleton()->fieldLabel('Year'), '', 4),
            ];
        }
        return $birthdayFields;
    }
    
    /**
     * Returns the business fields if enabled.
     * 
     * @return array
     */
    protected function getBusinessFields()
    {
        $businessFields = [];
        if ($this->EnableBusinessCustomers()) {
            $address        = Address::singleton();
            $businessFields = [
                CheckboxField::create('IsBusinessAccount', $address->fieldLabel('IsBusinessAccount')),
                TextField::create(    'TaxIdNumber',       $address->fieldLabel('TaxIdNumber'),       '', $address->config()->max_length_tax_id_number)->setAttribute('pattern', '[A-Z|0-9]{7,12}'),
                TextField::create(    'Company',           $address->fieldLabel('Company'),           '', $address->config()->max_length_company),
            ];
        }
        return $businessFields;
    }
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomActions()
    {
        $this->beforeUpdateCustomActions(function (array &$actions) {
            $actions += [
                FormAction::create('submit', Page::singleton()->fieldLabel('Submit'))
                    ->setUseButtonTag(true)->addExtraClass('btn-primary')
            ];
        });
        return parent::getCustomActions();
    }
    
    /**
     * Submits the form.
     * 
     * @param array      $data Submitted data
     * @param CustomForm $form Form
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.10.2018
     */
    public function doSubmit($data, CustomForm $form)
    {
        // Aggregate Data and set defaults
        $currentUserID = 0;
        $currentUser   = Security::getCurrentUser();
        if ($currentUser instanceof Member
         && $currentUser->exists()
        ) {
            $currentUserID = $currentUser->ID;
        }
        $data['MemberID'] = $currentUserID;
        $data['Locale']   = Tools::current_locale();
        if ($this->demandBirthdayDate()
         && !empty($data['BirthdayDay'])
         && !empty($data['BirthdayMonth'])
         && !empty($data['BirthdayYear'])
        ) {
            $data['Birthday'] = $data['BirthdayYear'] . '-' . $data['BirthdayMonth'] . '-' . $data['BirthdayDay'];
        }

        // Create new regular customer and perform a log in
        $customer = $this->handleAnonymousCustomer()->castedUpdate($data);
        $customer->write();
        $customer->changePassword($data['Password']);
        $this->setCustomer($customer);
        Member::password_validator()->checkHistoricalPasswords(0);

        $customerGroup = $this->getTargetCustomerGroup($data);
        if ($customerGroup) {
            $customer->Groups()->add($customerGroup);
        }

        // Create ShippingAddress for customer and populate it with registration data
        $address = Address::create()->castedUpdate($data);
        $country = Country::get()->byID((int) $data['Country']);
        if ($country) {
            $address->CountryID = $country->ID;
        }
        $address->write();
        $this->extend('updateRegisteredAddress', $address, $data, $form);

        //connect the ShippingAddress and the InvoiceAddress to the customer
        $customer->Addresses()->add($address);
        $customer->ShippingAddressID = $address->ID;
        $customer->InvoiceAddressID  = $address->ID;
        $customer->write();
        $this->handleNewsletterRecipient($customer);
        $this->handleOptIn($customer);
        $this->extend('updateRegisteredCustomer', $customer, $data, $form);

        $redirectTo = $this->getController()->Link('welcome');
        if (array_key_exists('redirect', $data)
         && !empty($data['redirect'])
        ) {
            $redirectTo = $data['redirect'];
        } elseif (array_key_exists('backlink', $data)
               && !empty($data['backlink'])
        ) {
            $redirectTo = $data['backlink'];
        }
        $authenticator = new MemberAuthenticator();
        $authenticator->getLoginHandler($redirectTo)->performLogin($customer, ['Remember' => false], $this->getRequest());
        $this->getController()->redirect($redirectTo);
    }
    
    /**
     * Handles the opt in part.
     * 
     * @param Member $customer Customer
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2019
     */
    public function handleOptIn(Member $customer) : void
    {
        $customer->sendRegistrationOptInEmail();
    }
    
    /**
     * Handles the anonymous customer object if exists.
     * Anonymous customer will be transformed into a newly registered customer.
     * 
     * @return Member
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2018
     */
    public function handleAnonymousCustomer()
    {
        $customer = Customer::currentUser();
        if ($customer instanceof Member
         && $customer->exists()
        ) {
            $customer->Groups()->removeAll();
        } else {
            $customer = Member::create();
        }
        return $customer;
    }
    
    /**
     * Handles the newsletter recipient data.
     * 
     * @param Member $customer New customer
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.11.2017
     */
    public function handleNewsletterRecipient(Member $customer)
    {
        // Remove from the anonymous newsletter recipients list
        if (AnonymousNewsletterRecipient::doesExist($customer->Email)) {
            $recipient = AnonymousNewsletterRecipient::getByEmailAddress($customer->Email);
            if ($recipient->NewsletterOptInStatus) {
                $customer->NewsletterOptInStatus      = 1;
                $customer->NewsletterConfirmationHash = $recipient->NewsletterOptInConfirmationHash;
                $customer->write();
            }
            AnonymousNewsletterRecipient::removeByEmailAddress($customer->Email);
        }
        
        if ( $customer->SubscribedToNewsletter
         && !$customer->NewsletterOptInStatus
        ) {
            Newsletter::subscribeRegisteredCustomer($customer);
        }
    }
    
    /**
     * Indicates wether the registration fields specific to business customers
     * should be shown.
     *
     * @return boolean
     */
    public function EnableBusinessCustomers()
    {
        return Config::enableBusinessCustomers();
    }
    
    /**
     * Indicates wether the birthday date has to be entered.
     *
     * @return boolean
     */
    public function demandBirthdayDate()
    {
        return Config::demandBirthdayDateOnRegistration();
    }
    
    /**
     * Returns whether there is a minimum age to order.
     *
     * @return boolean
     */
    public function UseMinimumAgeToOrder()
    {
        return Config::UseMinimumAgeToOrder();
    }
    
    /**
     * Returns the minimum age to order.
     *
     * @return boolean
     */
    public function MinimumAgeToOrder()
    {
        return Config::MinimumAgeToOrder();
    }
    
    /**
     * Returns the target customer group.
     * 
     * @param array $data Submitted form data.
     * 
     * @return Group
     */
    public function getTargetCustomerGroup($data)
    {
        if (array_key_exists('IsBusinessAccount', $data)
         && $data['IsBusinessAccount'] == '1'
        ) {
            $customerGroup = Customer::default_customer_group_b2b();
        } else {
            $customerGroup = Customer::default_customer_group();
        }
        return $customerGroup;
    }
    
    /**
     * Returns the backlink.
     * 
     * @return string
     */
    public function getBackLink() : string
    {
        return $this->backLink;
    }
    
    /**
     * Returns the customer or NULL.
     * 
     * @return Member|null
     */
    public function getCustomer() : ?Member
    {
        return $this->customer;
    }

    /**
     * Sets the backlink.
     * 
     * @param string $backLink Backlink
     * 
     * @return void
     */
    public function setBackLink($backLink) : RegisterRegularCustomerForm
    {
        $this->backLink = $backLink;
        return $this;
    }
    
    /**
     * Sets the customer.
     * 
     * @param Member $customer Customer
     * 
     * @return \SilverCart\Forms\RegisterRegularCustomerForm
     */
    public function setCustomer(Member $customer) : RegisterRegularCustomerForm
    {
        $this->customer = $customer;
        return $this;
    }
    
    /**
     * Returns whether the customer is in checkout process while going through the
     * registration process.
     * 
     * @return bool
     */
    public function getIsInCheckout() : bool
    {
        return (bool) RegistrationPage::getIsInCheckout();
    }
    
    /**
     * Sets whether the customer is in checkout process while going through the
     * registration process.
     * 
     * @param bool $is Customer is in checkout?
     * 
     * @return RegisterRegularCustomerForm
     */
    public function setIsInCheckout(bool $is) : RegisterRegularCustomerForm
    {
        RegistrationPage::setIsInCheckout($is);
        return $this;
    }
}