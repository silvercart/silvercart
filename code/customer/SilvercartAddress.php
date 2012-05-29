<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
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
 * abstract for a customers address
 * As a customer might want to get an order delivered to a third person, the address has a FirstName and Surname.
 *
 * @package Silvercart
 * @subpackage Customer
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 22.10.2010
 */
class SilvercartAddress extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.12.2011
     */
    public static $db = array(
        'TaxIdNumber'       => 'VarChar(30)',
        'Company'           => 'VarChar(255)',
        'Salutation'        => 'Enum("Herr,Frau","Herr")',
        'FirstName'         => 'VarChar(50)',
        'Surname'           => 'VarChar(50)',
        'Addition'          => 'VarChar(255)',
        'Street'            => 'VarChar(255)',
        'StreetNumber'      => 'VarChar(15)',
        'Postcode'          => 'VarChar',
        'City'              => 'VarChar(100)',
        'PhoneAreaCode'     => 'VarChar(10)',
        'Phone'             => 'VarChar(50)',
        'Fax'               => 'VarChar(50)'
    );
    
    /**
     * Has-one relationships.
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.12.2011
     */
    public static $has_one = array(
        'Member'            => 'Member',
        'SilvercartCountry' => 'SilvercartCountry'
    );
    
    /**
     * Has-one relationships.
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.12.2011
     */
    public static $casting = array(
        'SalutationText' => 'VarChar',
    );
    
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
        if (_t('SilvercartAddress.SINGULARNAME')) {
            return _t('SilvercartAddress.SINGULARNAME');
        } else {
            return parent::singular_name();
        } 
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
        if (_t('SilvercartAddress.PLURALNAME')) {
            return _t('SilvercartAddress.PLURALNAME');
        } else {
            return parent::plural_name();
        }   
    }
    
    /**
     * Sets the summary fields.
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function summaryFields() {
        return array_merge(
            parent::summaryFields(),
            array(
                'Street'    => $this->fieldLabel('Street'),
                'City'      => $this->fieldLabel('City'),
            )
        );
    }

    /**
     * Sets the field labels.
     *
     * @param bool $includerelations set to true to include the DataObjects relations
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2012
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Street'            => _t('SilvercartAddress.STREET'),
                'StreetNumber'      => _t('SilvercartAddress.STREETNUMBER'),
                'Postcode'          => _t('SilvercartAddress.POSTCODE'),
                'City'              => _t('SilvercartAddress.CITY'),
                'PhoneAreaCode'     => _t('SilvercartAddress.PHONEAREACODE'),
                'Phone'             => _t('SilvercartAddress.PHONE'),
                'PhoneShort'        => _t('SilvercartAddress.PHONE_SHORT'),
                'SilvercartCountry' => _t('SilvercartCountry.SINGULARNAME'),
                'Addition'          => _t('SilvercartAddress.ADDITION'),
                'FirstName'         => _t('SilvercartAddress.FIRSTNAME'),
                'Surname'           => _t('SilvercartAddress.SURNAME'),
                'TaxIdNumber'       => _t('SilvercartAddress.TAXIDNUMBER'),
                'Company'           => _t('SilvercartAddress.COMPANY'),
            )
        );
    }
    
    /**
     * Return field labels as DataObject.
     *
     * @return DataObject
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.05.2012
     */
    public function fieldLabel() {
        return new DataObject($this->fieldLabels());
    }
    
    /**
     * Indicates wether this address is set as a standard address for shipping
     * or invoicing.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.10.2011
     */
    public function hasAddressData() {
        $hasAddressData = false;
        
        if ($this->ID > 0) {
            $hasAddressData = true;
        }
        
        return $hasAddressData;
    }
    
    /**
     * Indicates wether this address is the address of a company. The fields
     * "Company" and "TaxIdNumber" must be filled in to conform that.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.12.2011
     */
    public function isCompanyAddress() {
        $isCompanyAddress = false;
        
        if (!empty($this->TaxIdNumber) &&
            !empty($this->Company)) {
            
            $isCompanyAddress = true;
        }
        
        return $isCompanyAddress;
    }

    /**
     * Indicates wether this is the last address of the customer.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.03.2012
     */
    public function isLastAddress() {
        $isLastAddress = false;

        if (Member::currentUser() &&
            Member::currentUser()->SilvercartAddresses()->Count() < 2) {

            $isLastAddress = true;
        }

        return $isLastAddress;
    }

    /**
     * Returns the localized salutation string.
     *
     * @return string
     */
    public function getSalutationText() {
        if ($this->Salutation == 'Herr') {
            $salutation = _t('SilvercartAddress.MISTER', 'Mister');
        } else {
            $salutation = _t('SilvercartAddress.MISSES', 'Misses');
        }
        return $salutation;
    }

    /**
     * Checks, whether this is an invoice address.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2011
     */
    public function isInvoiceAddress() {
        return $this->ID == Member::currentUser()->SilvercartInvoiceAddressID;
    }

    /**
     * Checks, whether this is an invoice address.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2011
     */
    public function isShippingAddress() {
        return $this->ID == Member::currentUser()->SilvercartShippingAddressID;
    }

    /**
     * Indicates if this is both an invoice and shipping address.
     *
     * @return bool
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.11.2011
     */
    public function isInvoiceAndShippingAddress() {
        $isInvoiceAndShippingAddress = false;
        
        if ($this->ID > 0) {
            if (Member::currentUser()->SilvercartInvoiceAddressID == $this->ID &&
                Member::currentUser()->SilvercartShippingAddressID == $this->ID) {

                $isInvoiceAndShippingAddress = true;
            }
        }
        
        return $isInvoiceAndShippingAddress;
    }
}
