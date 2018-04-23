<?php

namespace SilverCart\Model;

use SilverCart\Dev\Tools;
use SilverCart\Model\ShopEmail;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Product\Product;
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
class EmailAddress extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = array(
        'Name'  => 'Varchar(100)',
        'Email' => 'Varchar(100)'
    );
    
    /**
     * n:m relations
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @var type array
     */
    private static $belongs_many_many = array(
        'ShopEmails' => ShopEmail::class,
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartEmailAddress';
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
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
     * @since 13.07.2012
     */
    public function plural_name() {
        return Tools::plural_name_for($this); 
    }
    
    /**
     * Summaryfields for display in tables.
     * 
     * @return array 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 30.6.2011
     */
    public function summaryFields() {
        $fields = parent::summaryFields();
        $fields['Name']  = Product::singleton()->fieldLabel('Title');
        $fields['Email'] = Address::singleton()->fieldLabel('Email');
        return $fields;
    }


    /**
     * Getter for the email address with the name prefixed
     * 
     * @return string
     */
    public function getEmailAddressWithName() {
        if (!empty($this->Name) && !empty($this->Email)) {
            return $this->Name . " <" . $this->Email . ">";
        } else {
            return false;
        }
    }
    
    /**
     * Getter for the email address
     * 
     * @return string
     */
    public function getEmailAddress() {
        if (!empty ($this->Email)) {
            return $this->Email;
        } else {
            return false;
        }
    }
}

