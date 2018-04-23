<?php

namespace SilverCart\Model;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
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
 */
class ContactMessage extends DataObject {

    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = array(
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
    );
    
    /**
     * Has-one relationships.
     *
     * @var array
     */
    private static $has_one = array(
        'Country' => Country::class,
    );

    /**
     * Casting.
     *
     * @var array
     */
    private static $casting = array(
        'CreatedNice' => 'Varchar',
    );

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
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011
     */
    public function singular_name() {
        return Tools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011 
     */
    public function plural_name() {
        return Tools::plural_name_for($this);
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.04.2018
     */
    public function fieldLabels($includerelations = true) {
        $fields = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'CreatedNice'  => Tools::field_label('DATE'),
                'Salutation'   => Address::singleton()->fieldLabel('Salutation'),
                'FirstName'    => Member::singleton()->fieldLabel('FirstName'),
                'Surname'      => Member::singleton()->fieldLabel('Surname'),
                'Email'        => Member::singleton()->fieldLabel('Email'),
                'Street'       => Address::singleton()->fieldLabel('Street'),
                'StreetNumber' => Address::singleton()->fieldLabel('StreetNumber'),
                'Postcode'     => Address::singleton()->fieldLabel('Postcode'),
                'City'         => Address::singleton()->fieldLabel('City'),
                'Country'      => Country::singleton()->singular_name(),
                'Phone'        => Address::singleton()->fieldLabel('Phone'),
                'Message'      => _t(ContactMessage::class . '.MESSAGE', 'message')
            )
        );
        
        $this->extend('updateFieldLabels', $fields, $includerelations);
        
        return $fields;
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.04.2018
     */
    public function summaryFields() {
        $fields = array(
            'CreatedNice'   => $this->fieldLabel('CreatedNice'),
            'Salutation'    => $this->fieldLabel('Salutation'),
            'FirstName'     => $this->fieldLabel('FirstName'),
            'Surname'       => $this->fieldLabel('Surname'),
            'Email'         => $this->fieldLabel('Email'),
        );
        
        $this->extend('updateSummaryFields', $fields);
            
        return $fields;
    }

    /**
     * returns the orders creation date formated: dd.mm.yyyy hh:mm
     *
     * @return string
     */
    public function getCreatedNice() {
        return date('d.m.Y - H:i', strtotime($this->Created));
    }

    /**
     * Disable editing for all Member types.
     *
     * @param Member $member Member, defined for compatibility with parent
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function canEdit($member = null) {
        if ($member === null) {
            $member = Customer::currentUser();
        }
        if ($member && $member->inGroup('administrators')) {
            return true;
        }
        return false;
    }

    /**
     * Send the contact message via email.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.04.2018
     */
    public function send() {
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
            $fields
        );
        $this->extend('onAfterSend');
    }
    
    /**
     * returns field value for given fieldname with stripped slashes
     *
     * @param string $field fieldname
     * 
     * @return string 
     */
    public function getField($field) {
        $parentField = parent::getField($field);
        if (!is_null($parentField) &&
            $field != 'ClassName') {
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
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 10.02.2013
     */
    public function excludeFromScaffolding() {
        $excludeFromScaffolding = array(
            'Salutation'
        );

        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);

        return $excludeFromScaffolding;
    }
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     */
    public function getCMSFields() {
        $fields = DataObjectExtension::getCMSFields($this);
        $salutationDropdown = new DropdownField('Salutation', $this->fieldLabel('Salutation'), Tools::getSalutationMap());
        $fields->insertBefore($salutationDropdown, 'FirstName');
        return $fields;
    }
   
    
}
