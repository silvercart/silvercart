<?php
/**
 * Copyright 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
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
 * @copyright 2011 pixeltricks GmbH
 * @since 30.06.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartEmailAddress extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 30.6.2011
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
     * @copyright 2010 pixeltricks GmbH
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
     * @copyright 2010 pixeltricks GmbH
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

