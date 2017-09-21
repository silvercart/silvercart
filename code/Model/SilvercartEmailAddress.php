<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Customer
 */

/**
 * Abstract for an email address; For a more comfortable view a name can be added.
 *
 * @package Silvercart
 * @subpackage Customer
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 30.06.2011
 * @license see license file in modules root directory
 */
class SilvercartEmailAddress extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'Name'  => 'VarChar(100)',
        'Email'      => 'VarChar(100)'
    );
    
    /**
     * n:m relations
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @var type array
     */
    public static $belongs_many_many = array(
        'SilvercartShopEmails' => 'SilvercartShopEmail'
    );
    
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
        return SilvercartTools::singular_name_for($this);
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
        return SilvercartTools::plural_name_for($this); 
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
        $fields['Name'] = _t("SilvercartProduct.COLUMN_TITLE");
        $fields['Email'] = _t("SilvercartAddress.EMAIL");
        return $fields;
    }


    /**
     * Getter for the email address with the name prefixed
     * 
     * @return string|false The email address in angle brackets with the name prefixed 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 30.6.2011
     */
    public function getEmailAddressWithName() {
        $emailAddress = "";
        if (!empty($this->Name) && !empty($this->Email)) {
            $emailAddress = $this->Name . " <" . $this->Email . ">";
            return $emailAddress;
        } else {
            return false;
        }
    }
    
    /**
     * Getter for the email address
     * 
     * @return string|false
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 30.6.2011
     */
    public function getEmailAddress() {
        if (!empty ($this->Email)) {
            $email = $this->Email;
            return $email;
        } else {
            return false;
        }
    }
}

