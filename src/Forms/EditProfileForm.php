<?php

namespace SilverCart\Forms;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\CustomForm;
use SilverCart\Forms\FormFields\TextField;
use SilverCart\Forms\RegisterRegularCustomerForm;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Newsletter\Newsletter;
use SilverCart\Model\Pages\CheckoutStep;
use SilverCart\Model\Pages\Page;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\PasswordField;
use SilverStripe\Security\Member;

/** 
 * A form to manipulate a customers profile.
 *
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class EditProfileForm extends CustomForm {
    
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
     * List of required fields.
     *
     * @var array
     */
    private static $requiredFields = [
        'Email' => [
            'isEmailAddress' => true,
            'isFilledIn'     => true,
            'doesEmailExist' => false,
        ],
        'Salutation',
        'FirstName' => [
            'isFilledIn'   => true,
            'hasMinLength' => 3,
        ],
        'Surname' => [
            'isFilledIn'   => true,
            'hasMinLength' => 3,
        ],
        'Password' => [
            'isValidPassword' => true,
        ],
        'PasswordCheck' => [
            'mustEqual' => 'Password',
        ],
    ];
    
    /**
     * Returns the required fields.
     * 
     * @return array
     */
    public function getRequiredFields() {
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
        return parent::getRequiredFields();
    }

    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields() {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $member  = Customer::currentUser();
            $page    = Page::singleton();
            
            $birthdayDaySource   = ['' => Tools::field_label('PleaseChoose'), '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20', '21' => '21', '22' => 22, '23' => 23, '24' => '24', '25' => '25', '26' => '26', '27' => '27', '28' => '28', '29' => '29', '30' => '30', '31' => '31'];
            
            $salutationValue             = '';
            $firstNameValue              = '';
            $surnameValue                = '';
            $subscribedToNewsletterValue = '';
            $birthdayDayValue            = '';
            $birthdayMonthValue          = '';
            $birthdayYearValue           = '';
            $emailValue                  = '';

            if ($member instanceof Member
             && $member->exists()
            ) {
                $salutationValue             = $member->Salutation;
                $firstNameValue              = stripcslashes($member->FirstName);
                $surnameValue                = stripcslashes($member->Surname);
                $subscribedToNewsletterValue = $member->SubscribedToNewsletter;

                if ($member->Birthday) {
                    $birthdayDayValue   = date('j', strtotime($member->Birthday));
                    $birthdayMonthValue = date('m', strtotime($member->Birthday));
                    $birthdayYearValue  = date('Y', strtotime($member->Birthday));
                }
                if ($member->Email) {
                    $emailValue = stripcslashes($member->Email);
                }
            } elseif (!($member instanceof Member)) {
                $member = Member::singleton();
            }
            
            $passwordField      = PasswordField::create('Password', $page->fieldLabel('Password'));
            $passwordCheckField = PasswordField::create('PasswordCheck', $page->fieldLabel('PasswordCheck'));
            $passwordPattern    = CustomRequiredFields::config()->password_pattern;
            $passwordMinlength  = CustomRequiredFields::config()->password_minlength;
            if (!empty($passwordPattern)) {
                $passwordField->setAttribute('pattern', $passwordPattern);
                $passwordCheckField->setAttribute('pattern', $passwordPattern);
            }
            if (!empty($passwordMinlength)) {
                $passwordField->setAttribute('minlength', $passwordMinlength);
                $passwordCheckField->setAttribute('minlength', $passwordMinlength);
                $passwordField->setDescription(_t(RegisterRegularCustomerForm::class . '.PasswordHint', 'Create a password for your login. Your password needs at least {minlength} characters and contain at least 1 capital letter, 1 small letter and 1 number.', [
                    'minlength' => $passwordMinlength,
                ]));
            }
            $fields += [
                DropdownField::create('Salutation', $member->fieldLabel('Salutation'), Tools::getSalutationMap(), $salutationValue),
                TextField::create('FirstName', $member->fieldLabel('FirstName'), $firstNameValue),
                TextField::create('Surname', $member->fieldLabel('Surname'), $surnameValue),
                EmailField::create('Email', $member->fieldLabel('EmailAddress'), $emailValue),
                $passwordField,
                $passwordCheckField,
                $newsletterField = CheckboxField::create('SubscribedToNewsletter', CheckoutStep::singleton()->fieldLabel('SubscribeNewsletter'), $subscribedToNewsletterValue),
            ];
            if (!$member->SubscribedToNewsletter
             || !$member->NewsletterOptInStatus
            ) {
                $newsletterField->setDescription(Newsletter::singleton()->fieldLabel('OptInNotFinished'));
            }
            if ($this->demandBirthdayDate()) {
                $fields[] = DropdownField::create('BirthdayDay', $page->fieldLabel('Day'), $birthdayDaySource, $birthdayDayValue);
                $fields[] = DropdownField::create('BirthdayMonth', $page->fieldLabel('Month'), Tools::getMonthMap(), $birthdayMonthValue);
                $fields[] = TextField::create('BirthdayYear', $page->fieldLabel('Year'), $birthdayYearValue);
            }
        });
        return parent::getCustomFields();
    }
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomActions() {
        $this->beforeUpdateCustomActions(function (array &$actions) {
            $actions += [
                FormAction::create('submit', Page::singleton()->fieldLabel('Save'))
                    ->setUseButtonTag(true)->addExtraClass('btn-primary')
            ];
        });
        return parent::getCustomActions();
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
        return Config::demandBirthdayDateOnRegistration();
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
        return Config::UseMinimumAgeToOrder();
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
        return Config::MinimumAgeToOrder();
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
     * @since 10.11.2017
     */
    public function doSubmit($data, CustomForm $form) {
        $member = Customer::currentUser();

        unset($data['PasswordCheck']);
        if (empty($data['Password'])) {
            unset($data['Password']);
        }

        if ($this->demandBirthdayDate()) {
            if (!empty($data['BirthdayDay']) &&
                !empty($data['BirthdayMonth']) &&
                !empty($data['BirthdayYear'])) {
                $data['Birthday'] = $data['BirthdayYear'] . '-' .
                    $data['BirthdayMonth'] . '-' .
                    $data['BirthdayDay'];
            }
        }
        
        if (!array_key_exists('SubscribedToNewsletter', $data)) {
            $data['SubscribedToNewsletter'] = false;
        }

        $member->castedUpdate($data);
        
        if (!$member->SubscribedToNewsletter) {
            $member->NewsletterOptInStatus      = false;
            $member->NewsletterConfirmationHash = '';
        }
        
        $member->write();
        
        if ( $member->SubscribedToNewsletter &&
            !$member->NewsletterOptInStatus) {
            
            $confirmationHash = Newsletter::createConfirmationHash(
                $member->Salutation,
                $member->FirstName,
                $member->Surname,
                $member->Email
            );
            $member->setField('NewsletterConfirmationHash', $confirmationHash);
            $member->write();
            
            Newsletter::sendOptInEmailTo(
                $member->Salutation,
                $member->FirstName,
                $member->Surname,
                $member->Email,
                $confirmationHash,
                $member->Locale
            );
        }
        
        $this->setDefaultSuccessMessage();

        $this->getController()->redirect($this->getController()->Link());
    }
    
}