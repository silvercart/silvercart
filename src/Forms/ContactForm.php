<?php

namespace SilverCart\Forms;

use Heyday\SilverStripe\HoneyPot\HoneyPotField;
use SilverCart\Dev\Tools;
use SilverCart\Forms\CustomForm;
use SilverCart\Forms\FormFields\GoogleRecaptchaField;
use SilverCart\Forms\FormFields\TextareaField;
use SilverCart\Forms\FormFields\TextField;
use SilverCart\Model\ContactMessage;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Forms\FormFieldValue;
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
     * HoneyPotField
     * 
     * @var HoneyPotField|null
     */
    protected $honeyPotField = null;
    
    /**
     * Returns the required fields.
     * 
     * @return array
     */
    public function getRequiredFields() : array
    {
        $requiredFields = self::config()->get('requiredFields');
        if ($this->HasCustomFormFields()) {
            foreach ($this->ContactPage()->FormFields() as $dbFormField) {
                /* @var $dbFormField \SilverCart\Model\Forms\FormField */
                if ($dbFormField->IsRequired) {
                    $requiredFields[] = $dbFormField->Name;
                }
            }
        }
        if ($this->EnableHoneyPot()) {
            $honeyPotField = $this->getHoneyPotField();
            $requiredFields[$honeyPotField->Name] = [
                'isFilledIn' => false,
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
            $member = Customer::currentUser();
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
                    ],
                    $this->getCustomFormFields(),
                    $this->getSubjectFields(),
                    $this->getGoogleRecaptchaFields(),
                    $this->getHoneyPotFields()
            );
        });
        return parent::getCustomFields();
    }
    
    /**
     * Returns the fields for the subject.
     * 
     * @return array
     */
    protected function getCustomFormFields() : array
    {
        $fields = [];
        if ($this->HasCustomFormFields()) {
            foreach ($this->ContactPage()->FormFields() as $dbFormField) {
                /* @var $dbFormField \SilverCart\Model\Forms\FormField */
                $fields[] = $dbFormField->getFormField();
            }
        }
        return $fields;
    }
    
    /**
     * Returns the fields for the subject.
     * 
     * @return array
     */
    protected function getSubjectFields() : array
    {
        $fields   = [];
        $subjects = $this->ContactPage()->Subjects();
        if ($subjects->exists()) {
            $fields = [
                DropdownField::create('ContactMessageSubjectID', $this->ContactPage()->fieldLabel('Subject'), $subjects->map('ID', 'Subject')->toArray()),
            ];
        }
        return $fields;
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
     * Returns the HoneyPot related form fields.
     * 
     * @return array
     */
    protected function getHoneyPotFields() : array
    {
        $fields = [];
        if ($this->EnableHoneyPot()) {
            $fields[] = $this->getHoneyPotField();
        }
        return $fields;
    }
    
    /**
     * Returns the HoneyPot related form fields.
     * 
     * @return array
     */
    protected function getHoneyPotField() : ?HoneyPotField
    {
        if ($this->honeyPotField === null
         && $this->EnableHoneyPot()
        ) {
            $fieldName = 'Website';
            $index     = 1;
            while ($this->ContactPage()->FormFields()->filter('Name', $fieldName)->exists()) {
                $fieldName = "{$fieldName}-{$index}";
                $index++;
            }
            $this->honeyPotField = HoneyPotField::create($fieldName);
        }
        return $this->honeyPotField;
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
        if ($this->HasSubjects()
         && array_key_exists('ContactMessageSubjectID', $data)
        ) {
            $subject = $this->ContactPage()->Subjects()->byID((int) $data['ContactMessageSubjectID']);
            if ($subject instanceof ContactFormPage\Subject) {
                $data['SubjectText'] = $subject->Subject;
            }
        }
        $data['Message'] = str_replace('\r\n', "\n", $data['Message']);
        $contactMessage  = ContactMessage::create();
        $contactMessage->update($data);
        $contactMessage->write();
        if ($this->HasCustomFormFields()) {
            foreach ($this->ContactPage()->FormFields() as $dbFormField) {
                /* @var $dbFormField \SilverCart\Model\Forms\FormField */
                if (array_key_exists($dbFormField->Name, $data)) {
                    $value = FormFieldValue::create();
                    $value->FieldTitle  = $dbFormField->Title;
                    $value->FieldValue  = $data[$dbFormField->Name];
                    $value->FormFieldID = $dbFormField->ID;
                    $value->write();
                    $contactMessage->FormFieldValues()->add($value);
                }
            }
        }
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
     * @return ContactFormPage|null
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.04.2015
     */
    protected function ContactPage() : ?ContactFormPage
    {
        $contactPage = $this->getController()->data();
        if ($contactPage->IdentifierCode != Page::IDENTIFIER_CONTACT_FORM_PAGE) {
            $contactPage = Tools::PageByIdentifierCode(Page::IDENTIFIER_CONTACT_FORM_PAGE);
        }
        return $contactPage;
    }
    
    /**
     * Returns whether Google reCAPTCHA is enabled or not.
     * 
     * @return bool
     */
    public function EnableGoogleRecaptcha() : bool
    {
        return GoogleRecaptchaField::isEnabled();
    }
    
    /**
     * Returns whether HoneyPot is enabled.
     * 
     * @return bool
     */
    public function EnableHoneyPot() : bool
    {
        return class_exists(HoneyPotField::class);
    }
    
    /**
     * Returns whether this form has FormFields.
     * 
     * @return bool
     */
    public function HasCustomFormFields() : bool
    {
        return $this->ContactPage()->FormFields()->exists();
    }
    
    /**
     * Returns whether this form has subjects.
     * 
     * @return bool
     */
    public function HasSubjects() : bool
    {
        return $this->ContactPage()->Subjects()->exists();
    }
}