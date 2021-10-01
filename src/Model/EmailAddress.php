<?php

namespace SilverCart\Model;

use SilverCart\Dev\Tools;
use SilverCart\Model\ShopEmail;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Pages\ContactFormPage\Subject as ContactFormSubject;
use SilverCart\Model\Product\Product;
use SilverStripe\Control\Email\Email;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\ORM\DataObject;

/**
 * Abstract for an email address; For a more comfortable view a name can be added.
 *
 * @package SilverCart
 * @subpackage Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 10.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class EmailAddress extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartEmailAddress';
    /**
     * Attributes.
     *
     * @var string
     */
    private static $db = [
        'Name'  => 'Varchar(100)',
        'Email' => 'Varchar(100)',
    ];
    /**
     * n:m relations
     * 
     * @var string
     */
    private static $belongs_many_many = [
        'ShopEmails'          => ShopEmail::class,
        'ContactFormSubjects' => ContactFormSubject::class,
    ];
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this); 
    }
    
    /**
     * Returns the field labels.
     * 
     * @param bool $includerelations Include relations?
     * 
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, []);
    }
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $shopEmailsField = $fields->dataFieldByName('ShopEmails');
            if ($shopEmailsField instanceof GridField) {
                $shopEmailsField->getConfig()->removeComponentsByType(GridFieldAddNewButton::class);
            }
            $subjectsField = $fields->dataFieldByName('ContactFormSubjects');
            if ($subjectsField instanceof GridField) {
                $subjectsField->getConfig()->removeComponentsByType(GridFieldAddNewButton::class);
            }
        });
        return parent::getCMSFields();
    }
    
    /**
     * Summaryfields for display in tables.
     * 
     * @return array
     */
    public function summaryFields() : array
    {
        $fields = parent::summaryFields();
        $fields['Name']  = Product::singleton()->fieldLabel('Title');
        $fields['Email'] = Address::singleton()->fieldLabel('Email');
        return $fields;
    }
    
    /**
     * Returns the mail to parameter to use with @see Email::create().
     * 
     * @return string|array
     */
    public function getMailTo()
    {
        $to = null;
        if (Email::is_valid_address($this->Email)) {
            if (!empty($this->Name)) {
                $to = [$this->Email => $this->Name];
            } else {
                $to = $this->Email;
            }
        }
        return $to;
    }

    /**
     * Getter for the email address with the name prefixed
     * 
     * @return string
     */
    public function getEmailAddressWithName() : string
    {
        if (!empty($this->Name)
         && !empty($this->Email)
        ) {
            return "{$this->Name} <{$this->Email}>";
        }
        return $this->getEmailAddress();
    }
    
    /**
     * Getter for the email address
     * 
     * @return string
     */
    public function getEmailAddress() : string
    {
        return (string) $this->Email;
    }
    
    /**
     * Getter for the title
     * 
     * @return string
     */
    public function getTitle() : string
    {
        return $this->getEmailAddressWithName();
    }
}