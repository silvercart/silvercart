<?php

namespace SilverCart\Model;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Model\BlacklistEntry;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\ShopEmail;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;

/**
 * A contact message object. There's a storeadmin view for this object, too.
 *
 * @package SilverCart
 * @subpackage Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 10.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $Salutation     Salutation
 * @property string $FirstName      FirstName
 * @property string $Surname        Surname
 * @property string $Street         Street
 * @property string $StreetNumber   StreetNumber
 * @property string $Postcode       Postcode
 * @property string $City           City
 * @property string $Email          Email
 * @property string $Phone          Phone
 * @property string $Message        Message
 * @property bool   $IsSpam         IsSpam
 * @property string $CreatedNice    Created Nice
 * @property string $SalutationText Salutation Text
 * 
 * @method Country Country() Returns the related Country.
 * @method Member  Member()  Returns the related Member.
 */
class ContactMessage extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * Configuration parameter to determine whether to send an acknowledgement of
     * receipt to the customer or not.
     *
     * @var bool
     */
    private static $send_acknowledgement_of_receipt = true;
    /**
     * Set this to false to delete spam detected messages.
     *
     * @var bool
     */
    private static $store_spam_in_database = true;
    /**
     * Allowed number of repeating contact messages with the same content.
     * Set to 0 to deactivate.
     *
     * @var int
     */
    private static $mark_as_spam_after_repeating = 3;
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = [
        'Salutation'    => 'Varchar(16)',
        'FirstName'     => 'Varchar(255)',
        'Surname'       => 'Varchar(128)',
        'Street'        => 'Varchar(255)',
        'StreetNumber'  => 'Varchar(15)',
        'Postcode'      => 'Varchar',
        'City'          => 'Varchar(100)',
        'Email'         => 'Varchar(255)',
        'Phone'         => 'Varchar(255)',
        'Message'       => 'Text',
        'IsSpam'        => 'Boolean',
    ];
    /**
     * Has-one relationships.
     *
     * @var array
     */
    private static $has_one = [
        'Country' => Country::class,
        'Member'  => Member::class,
    ];
    /**
     * Casting.
     *
     * @var array
     */
    private static $casting = [
        'CreatedNice'    => 'Varchar',
        'SalutationText' => 'Varchar',
    ];
    /**
     * Default SQL sort statement.
     *
     * @var string
     */
    private static $default_sort = 'Created DESC';
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartContactMessage';
    
    /**
     * Returns the translated singular name.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name.
     * 
     * @return string 
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this);
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'Blacklist'    => _t(ContactMessage::class . '.Blacklist', 'Blacklist'),
            'CreatedNice'  => Tools::field_label('DATE'),
            'Salutation'   => Address::singleton()->fieldLabel('Salutation'),
            'FirstName'    => Member::singleton()->fieldLabel('FirstName'),
            'Surname'      => Member::singleton()->fieldLabel('Surname'),
            'Name'         => Address::singleton()->fieldLabel('Name'),
            'Email'        => Member::singleton()->fieldLabel('Email'),
            'Street'       => Address::singleton()->fieldLabel('Street'),
            'StreetNumber' => Address::singleton()->fieldLabel('StreetNumber'),
            'Postcode'     => Address::singleton()->fieldLabel('Postcode'),
            'City'         => Address::singleton()->fieldLabel('City'),
            'Country'      => Country::singleton()->singular_name(),
            'Phone'        => Address::singleton()->fieldLabel('Phone'),
            'Message'      => _t(ContactMessage::class . '.MESSAGE', 'message'),
            'Member'       => Member::singleton()->fieldLabel('Customer'),
        ]);
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     */
    public function summaryFields() : array
    {
        $fields = [
            'CreatedNice'   => $this->fieldLabel('CreatedNice'),
            'Salutation'    => $this->fieldLabel('Salutation'),
            'FirstName'     => $this->fieldLabel('FirstName'),
            'Surname'       => $this->fieldLabel('Surname'),
            'Email'         => $this->fieldLabel('Email'),
        ];
        $this->extend('updateSummaryFields', $fields);
        return $fields;
    }
    
    /**
     * Marks spam on before write if necessary.
     * 
     * @return void
     */
    protected function onBeforeWrite() : void
    {
        parent::onBeforeWrite();
        if (!$this->exists()) {
            $this->IsSpam = BlacklistEntry::isSpam($this->Message)
                    || ((int) self::config()->mark_as_spam_after_repeating > 0
                     && self::get()->filter('Message', $this->Message)->count() > self::config()->mark_as_spam_after_repeating);
        }
    }
    
    /**
     * Deletes spam on after write if necessary.
     * 
     * @return void
     */
    protected function onAfterWrite() : void
    {
        parent::onAfterWrite();
        if ($this->IsSpam
         && !self::config()->store_spam_in_database
        ) {
            $this->delete();
        }
    }

    /**
     * returns the orders creation date formated: dd.mm.yyyy hh:mm
     *
     * @return string
     */
    public function getCreatedNice() : string
    {
        return Tools::getDateWithTimeNice($this->Created);
    }

    /**
     * Returns the localized salutation string.
     *
     * @return string
     */
    public function getSalutationText() : string
    {
        return Tools::getSalutationText($this->Salutation);
    }

    /**
     * Disable editing for all Member types.
     *
     * @param Member $member Member, defined for compatibility with parent
     *
     * @return bool
     */
    public function canEdit($member = null) : bool
    {
        if ($member === null) {
            $member = Customer::currentUser();
        }
        if ($member
         && $member->isAdmin()
        ) {
            return true;
        }
        return false;
    }

    /**
     * Send the contact message via email.
     *
     * @return void
     */
    public function send() : void
    {
        if ($this->IsSpam) {
            // don not send if marked as spam.
            return;
        }
        $this->extend('onBeforeSend');
        $fields = ['ContactMessage' => $this];
        $db     = $this->config()->get('db');
        $hasOne = $this->config()->get('has_one');

        foreach (array_keys($db) as $fieldName) {
            $value = $this->{$fieldName};
            if ($fieldName == 'Message') {
                $value = str_replace('\r\n', '<br/>', nl2br($value));
            }
            $fields[$fieldName] = $value;
        }
        foreach (array_keys($hasOne) as $hasOneName) {
            $fields[$hasOneName] = $this->{$hasOneName}();
        }

        ShopEmail::send(
                'ContactMessage',
                Config::DefaultContactMessageRecipient(),
                $fields,
                [],
                Tools::default_locale()->getLocale()
        );
        if ($this->config()->send_acknowledgement_of_receipt) {
            ShopEmail::send(
                'ContactMessageAcknowledgement',
                $this->Email,
                $fields
            );
        }
        $this->extend('onAfterSend');
    }
    
    /**
     * returns field value for given fieldname with stripped slashes
     *
     * @param string $field fieldname
     * 
     * @return string|null
     */
    public function getField($field) : ?string
    {
        $parentField = parent::getField($field);
        if (!is_null($parentField)
         && $field != 'ClassName'
        ) {
            $parentField = stripcslashes($parentField);
        }
        return $parentField;
    }
    
    /**
     * Returns an array of field/relation names (db, has_one, has_many, 
     * many_many, belongs_many_many) to exclude from form scaffolding in
     * backend.
     * This is a performance friendly way to exclude fields.
     * 
     * @return array
     */
    public function excludeFromScaffolding() : array
    {
        $excludeFromScaffolding = [
            'Salutation'
        ];
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $salutationDropdown = DropdownField::create('Salutation', $this->fieldLabel('Salutation'), Tools::getSalutationMap());
            $fields->insertBefore($salutationDropdown, 'FirstName');
            BlacklistEntry::getBlackListCMSFields($fields);
        });
        return DataObjectExtension::getCMSFields($this);
    }
}