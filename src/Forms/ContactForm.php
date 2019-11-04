<?php

namespace SilverCart\Forms;

use SilverCart\Dev\Tools;
use SilverCart\Forms\CustomForm;
use SilverCart\Forms\FormFields\GoogleRecaptchaField;
use SilverCart\Forms\FormFields\TextareaField;
use SilverCart\Forms\FormFields\TextField;
use SilverCart\Model\ContactMessage;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Pages\ContactFormPage;
use SilverCart\Model\Pages\Page;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Security\Member;

/** 
 * a contact form of the CustomHTMLForms modul.
 *
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ContactForm extends CustomForm
{
    /**
     * Spam check parameter for equal firstname and surname.
     * Contact messages with an equal firstname and surname will be ignored.
     *
     * @var bool
     */
    private static $spam_check_firstname_surname_enabled = true;
    /**
     * Custom extra CSS classes.
     *
     * @var array
     */
    protected $customExtraClasses = [
        'form-horizontal',
        'grouped',
    ];
    /**
     * Don't enable Security token for this type of form because we'll run
     * into caching problems when using it.
     * 
     * @var boolean
     */
    protected $securityTokenEnabled = false;
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
        'Email',
        'Message' => [
            'isFilledIn'   => true,
            'hasMinLength' => 3,
        ],
    ];
    
    /**
     * Returns the required fields.
     * 
     * @return array
     */
    public function getRequiredFields() : array
    {
        $requiredFields = self::config()->get('requiredFields');
        if ($this->EnableStreet()
         && $this->StreetIsRequired()
        ) {
            $requiredFields += [
                'Street',
                'StreetNumber',
            ];
        }
        if ($this->EnableCity()
         && $this->CityIsRequired()
        ) {
            $requiredFields += [
                'Postcode',
                'City',
            ];
        }
        if ($this->EnableCountry()
         && $this->CountryIsRequired()
        ) {
            $requiredFields += [
                'CountryID',
            ];
        }
        if ($this->EnablePhoneNumber()
         && $this->PhoneNumberIsRequired()
        ) {
            $requiredFields += [
                'Phone',
            ];
        }
        self::config()->set('requiredFields', $requiredFields);
        return parent::getRequiredFields();
    }

    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields() : array
    {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $address = Address::singleton();
            $member  = Customer::currentUser();
            if (!($member instanceof Member)) {
                $member = Member::singleton();
            }
            $fields = array_merge(
                    $fields,
                    [
                        DropdownField::create('Salutation', $member->fieldLabel('Salutation'), Tools::getSalutationMap(), $member->Salutation),
                        TextField::create('FirstName', $member->fieldLabel('FirstName'), $member->FirstName),
                        TextField::create('Surname', $member->fieldLabel('Surname'), $member->Surname),
                        EmailField::create('Email', $member->fieldLabel('EmailAddress'), $member->Email),
                        TextareaField::create('Message', Page::singleton()->fieldLabel('Message')),
                        TextField::create('Street', $address->fieldLabel('Street'), $address->Street),
                        TextField::create('StreetNumber', $address->fieldLabel('StreetNumber'), $address->StreetNumber),
                        TextField::create('Postcode', $address->fieldLabel('Postcode'), $address->Postcode),
                        TextField::create('City', $address->fieldLabel('City'), $address->City),
                    ],
                    $this->getStreetFields(),
                    $this->getCityFields(),
                    $this->getCountryFields(),
                    $this->getPhoneFields(),
                    $this->getGoogleRecaptchaFields()
            );
        });
        return parent::getCustomFields();
    }
    
    /**
     * Returns the fields for the city.
     * 
     * @return array
     */
    protected function getCityFields() : array
    {
        $cityFields = [];
        if ($this->EnableCity()) {
            $address = Address::singleton();
            $cityFields = [
                TextField::create('Postcode', $address->fieldLabel('Postcode')),
                TextField::create('City', $address->fieldLabel('City')),
            ];
        }
        return $cityFields;
    }
    
    /**
     * Returns the fields for the country.
     * 
     * @return array
     */
    protected function getCountryFields() : array
    {
        $countryFields = [];
        if ($this->EnableCity()) {
            $address = Address::singleton();
            $countryFields = [
                DropdownField::create('CountryID', $address->fieldLabel('Country'), Country::getPrioritiveDropdownMap(true, Tools::field_label('PleaseChoose'))),
            ];
        }
        return $countryFields;
    }
    
    /**
     * Returns the fields for the phone number.
     * 
     * @return array
     */
    protected function getPhoneFields() : array
    {
        $phoneFields = [];
        if ($this->EnableCity()) {
            $address = Address::singleton();
            $phoneFields = [
                TextField::create('Phone', $address->fieldLabel('Phone')),
            ];
        }
        return $phoneFields;
    }
    
    /**
     * Returns the fields for the street.
     * 
     * @return array
     */
    protected function getStreetFields() : array
    {
        $streetFields = [];
        if ($this->EnableStreet()) {
            $address = Address::singleton();
            $streetFields = [
                TextField::create('Street', $address->fieldLabel('Street') . ' / ' . $address->fieldLabel('StreetNumber')),
                TextField::create('StreetNumber', $address->fieldLabel('StreetNumber')),
            ];
        }
        return $streetFields;
    }
    
    /**
     * Returns the Google reCAPTCHA related form fields.
     * 
     * @return array
     */
    protected function getGoogleRecaptchaFields() : array
    {
        $fields = [];
        if ($this->EnableGoogleRecaptcha()) {
            $fields[] = GoogleRecaptchaField::create('GoogleRecaptcha', $this->fieldLabel('GoogleRecaptcha'));
        }
        return $fields;
    }
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomActions() : array
    {
        $this->beforeUpdateCustomActions(function (array &$actions) {
            $actions += [
                FormAction::create('submit', Page::singleton()->fieldLabel('SubmitMessage'))
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
     * @since 13.11.2017
     */
    public function doSubmit($data, CustomForm $form) : void
    {
        if (self::$spam_check_firstname_surname_enabled) {
            $firstName = trim($data['FirstName']);
            $surname   = trim($data['Surname']);
            if ($firstName == $surname) {
                // Very high spam risk. Do not accept and do not notify with message.
                $this->getController()->redirect($this->getController()->Link('thanks'));
                return;
            }
        }
        if ($this->EnableGoogleRecaptcha()) {
            $verified = GoogleRecaptchaField::verifyRequest();
            if (!$verified) {
                $this->setErrorMessage(_t(GoogleRecaptchaField::class . '.Verify', 'Please verify that you are not a robot.'));
                $this->setSessionData($this->getData());
                return;
            }
        }
        $customer = Customer::currentRegisteredCustomer();
        if ($customer instanceof Member
         && $customer->exists()
        ) {
            $data['MemberID'] = $customer->ID;
        }
        $data['Message'] = str_replace('\r\n', "\n", $data['Message']);
        $contactMessage  = ContactMessage::create();
        $contactMessage->update($data);
        $contactMessage->write();
        $contactMessage->send();
        // redirect a user to the page type for the response or to the root
        $this->getController()->redirect($this->getController()->Link('thanks'));
    }
    
    /**
     * Enables the spam check parameter for equal firstname and surname.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.11.2015
     */
    public static function enable_spam_check_firstname_surname() : void
    {
        self::$spam_check_firstname_surname_enabled = true;
    }
    
    /**
     * Disables the spam check parameter for equal firstname and surname.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.11.2015
     */
    public static function disable_spam_check_firstname_surname() : void
    {
        self::$spam_check_firstname_surname_enabled = false;
    }
    
    /**
     * Returns the contact form page.
     * 
     * @return ContactFormPage
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.04.2015
     */
    protected function ContactPage() : ContactFormPage
    {
        $contactPage = $this->getController()->data();
        if ($contactPage->IdentifierCode != Page::IDENTIFIER_CONTACT_FORM_PAGE) {
            $contactPage = Tools::PageByIdentifierCode(Page::IDENTIFIER_CONTACT_FORM_PAGE);
        }
        return $contactPage;
    }
    
    /**
     * Returns whether to enable the Street field.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.08.2014
     */
    public function EnableStreet() : bool
    {
        return (bool) $this->ContactPage()->EnableStreet;
    }

    /**
     * Returns whether to set the Street field as a required one.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.08.2014
     */
    public function StreetIsRequired() : bool
    {
        return (bool) $this->ContactPage()->StreetIsRequired;
    }
    
    /**
     * Returns whether to enable the City field.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.08.2014
     */
    public function EnableCity() : bool
    {
        return (bool) $this->ContactPage()->EnableCity;
    }

    /**
     * Returns whether to set the City field as a required one.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.08.2014
     */
    public function CityIsRequired() : bool
    {
        return (bool) $this->ContactPage()->CityIsRequired;
    }
    
    /**
     * Returns whether to enable the Country field.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.08.2014
     */
    public function EnableCountry() : bool
    {
        return (bool) $this->ContactPage()->EnableCountry;
    }

    /**
     * Returns whether to set the Country field as a required one.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.08.2014
     */
    public function CountryIsRequired() : bool
    {
        return (bool) $this->ContactPage()->CountryIsRequired;
    }
    
    /**
     * Returns whether to enable the phone number field.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.06.2014
     */
    public function EnablePhoneNumber() : bool
    {
        return (bool) $this->ContactPage()->EnablePhoneNumber;
    }

    /**
     * Returns whether to set the phone number field as a required one.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.06.2014
     */
    public function PhoneNumberIsRequired() : bool
    {
        return (bool) $this->ContactPage()->PhoneNumberIsRequired;
    }
    
    /**
     * Returns whether Google reCAPTCHA is enabled or not.
     * 
     * @return bool
     */
    public function EnableGoogleRecaptcha() : bool
    {
        return !empty(GoogleRecaptchaField::config()->recaptcha_secret)
            && !empty(GoogleRecaptchaField::config()->recaptcha_site_key);
    }
}