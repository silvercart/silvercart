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
     */
    public static $db = array(
        'TaxIdNumber'       => 'VarChar(30)',
        'Company'           => 'VarChar(255)',
        'Salutation'        => 'Enum("Herr,Frau","Herr")',
        'FirstName'         => 'VarChar(50)',
        'Surname'           => 'VarChar(50)',
        'Addition'          => 'VarChar(255)',
        'PostNumber'        => 'VarChar(255)',
        'Packstation'       => 'VarChar(255)',
        'Street'            => 'VarChar(255)',
        'StreetNumber'      => 'VarChar(15)',
        'Postcode'          => 'VarChar',
        'City'              => 'VarChar(100)',
        'PhoneAreaCode'     => 'VarChar(10)',
        'Phone'             => 'VarChar(50)',
        'Fax'               => 'VarChar(50)',
        'IsPackstation'     => 'Boolean(0)',
    );
    
    /**
     * Has-one relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'Member'            => 'Member',
        'SilvercartCountry' => 'SilvercartCountry'
    );
    
    /**
     * Has-one relationships.
     *
     * @var array
     */
    public static $casting = array(
        'FullName'              => 'Text',
        'SalutationText'        => 'VarChar',
        'SilvercartCountryISO2' => 'Text',
        'SilvercartCountryISO3' => 'Text',
        'SilvercartCountryISON' => 'Text',
        'SilvercartCountryFIPS' => 'Text',
    );
    
    /**
     * Custom Add Export fields to export by XML
     *
     * @var array
     */
    public static $custom_add_export_fields = array(
        'SilvercartCountryISO2',
        'SilvercartCountryISO3',
        'SilvercartCountryISON',
        'SilvercartCountryFIPS',
    );
    
    /**
     * Property to indicate whether this is an anonymous address
     *
     * @var bool
     */
    protected $isAnonymous = false;
    
    /**
     * Property to indicate whether this is an anonymous shipping address
     *
     * @var bool
     */
    protected $isAnonymousShippingAddress = false;
    
    /**
     * Property to indicate whether this is an anonymous invoice address
     *
     * @var bool
     */
    protected $isAnonymousInvoiceAddress = false;
    
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
     * CMS fields for this object
     * 
     * @param array $params Scaffolding parameters
     * 
     * @return FieldList
     */
    public function getCMSFields($params = null) {
        $fields = SilvercartDataObject::getCMSFields($this);
        if ($fields->dataFieldByName('SilvercartCountryID')) {
            $countryDropdown = new DropdownField(
                    'SilvercartCountryID',
                    $this->fieldLabel('Country'),
                    SilvercartCountry::getPrioritiveDropdownMap());
            $fields->replaceField('SilvercartCountryID', $countryDropdown);
        }
        return $fields;
    }
    
    /**
     * Sets the summary fields.
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.06.2012
     */
    public function summaryFields() {
        $summaryFields = array(
                'Street'        => $this->fieldLabel('Street'),
                'StreetNumber'  => $this->fieldLabel('StreetNumber'),
                'Postcode'      => $this->fieldLabel('Postcode'),
                'City'          => $this->fieldLabel('City'),
                'SilvercartCountry.ISO2'    => $this->fieldLabel('SilvercartCountry'),
        );
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }

    /**
     * Sets the field labels.
     *
     * @param bool $includerelations set to true to include the DataObjects relations
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.06.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Street'                => _t('SilvercartAddress.STREET'),
                'StreetNumber'          => _t('SilvercartAddress.STREETNUMBER'),
                'Postcode'              => _t('SilvercartAddress.POSTCODE'),
                'City'                  => _t('SilvercartAddress.CITY'),
                'PhoneAreaCode'         => _t('SilvercartAddress.PHONEAREACODE'),
                'Phone'                 => _t('SilvercartAddress.PHONE'),
                'PhoneShort'            => _t('SilvercartAddress.PHONE_SHORT'),
                'SilvercartCountry'     => _t('SilvercartCountry.SINGULARNAME'),
                'Country'               => _t('SilvercartCountry.SINGULARNAME'),
                'Addition'              => _t('SilvercartAddress.ADDITION'),
                'PostNumber'            => _t('SilvercartAddress.POSTNUMBER'),
                'Packstation'           => _t('SilvercartAddress.PACKSTATION'),
                'PackstationPlain'      => _t('SilvercartAddress.PACKSTATION_PLAIN'),
                'Salutation'            => _t('SilvercartAddress.SALUTATION'),
                'FirstName'             => _t('SilvercartAddress.FIRSTNAME'),
                'Surname'               => _t('SilvercartAddress.SURNAME'),
                'TaxIdNumber'           => _t('SilvercartAddress.TAXIDNUMBER'),
                'Company'               => _t('SilvercartAddress.COMPANY'),
                'Name'                  => _t('SilvercartAddress.NAME'),
                'UsePackstation'        => _t('SilvercartAddress.USE_PACKSTATION'),
                'UseAbsoluteAddress'    => _t('SilvercartAddress.USE_ABSOLUTEADDRESS'),
                'IsPackstation'         => _t('SilvercartAddress.IS_PACKSTATION'),
                'AddressType'           => _t('SilvercartAddress.ADDRESSTYPE'),
                'Member'                => _t('SilvercartOrder.CUSTOMER'),
            )
        );
        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Some polishing on before write
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.10.2012
     */
    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        if ($this->IsPackstation) {
            $this->Street       = '';
            $this->StreetNumber = '';
        } else {
            $this->PostNumber   = '';
            $this->Packstation  = '';
        }
    }
    
    /**
     * Indicates wether this address is set as a standard address for shipping
     * or invoicing.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.06.2012
     */
    public function hasAddressData() {
        $hasAddressData = false;
        
        if ($this->ID > 0 ||
            $this->isAnonymous()) {
            $hasAddressData = true;
        }
        
        return $hasAddressData;
    }

    /**
     * Returns whether this is an anonymous address
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.06.2012
     */
    public function isAnonymous() {
        return $this->isAnonymous;
    }

    /**
     * Returns whether this is an anonymous shipping address
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.06.2012
     */
    public function isAnonymousShippingAddress() {
        return $this->isAnonymousShippingAddress;
    }

    /**
     * Returns whether this is an anonymous invoice address
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.06.2012
     */
    public function isAnonymousInvoiceAddress() {
        return $this->isAnonymousInvoiceAddress;
    }
    
    /**
     * Sets whether this is an anonymous address
     *
     * @param bool $isAnonymous Anonymous?
     *
     * @return void
     */
    public function setIsAnonymous($isAnonymous) {
        $this->isAnonymous = $isAnonymous;
    }
    
    /**
     * Sets whether this is an anonymous shipping address
     *
     * @param bool $isAnonymousShippingAddress Anonymous?
     *
     * @return void
     */
    public function setIsAnonymousShippingAddress($isAnonymousShippingAddress) {
        $this->isAnonymousShippingAddress = $isAnonymousShippingAddress;
        $this->setIsAnonymous($isAnonymousShippingAddress);
    }
    
    /**
     * Sets whether this is an anonymous invoice address
     *
     * @param bool $isAnonymousInvoiceAddress Anonymous?
     *
     * @return void
     */
    public function setIsAnonymousInvoiceAddress($isAnonymousInvoiceAddress) {
        $this->isAnonymousInvoiceAddress = $isAnonymousInvoiceAddress;
        $this->setIsAnonymous($isAnonymousInvoiceAddress);
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
            Member::currentUser()->SilvercartAddresses()->count() < 2) {

            $isLastAddress = true;
        }

        return $isLastAddress;
    }
    
    /**
     * Returns the full name (first name + sur name)
     * 
     * @return string
     */
    public function getFullName() {
        return $this->FirstName . ' ' . $this->Surname;
    }

    /**
     * Returns the localized salutation string.
     *
     * @return string
     */
    public function getSalutationText() {
        if ($this->Salutation == 'Herr') {
            $salutation = _t('SilvercartAddress.MISTER', 'Mister');
        } elseif ($this->Salutation == 'Frau') {
            $salutation = _t('SilvercartAddress.MISSES', 'Misses');
        } else {
            $salutation = _t('SilvercartAddress.' . strtoupper($this->Salutation), $this->Salutation);
        }
        return $salutation;
    }
    
    /**
     * Returns the ISO2 of the related country
     *
     * @return string
     */
    public function getSilvercartCountryISO2() {
        $silvercartCountryISO2 = '';
        if ($this->SilvercartCountryID > 0) {
            $silvercartCountryISO2 = $this->SilvercartCountry()->ISO2;
        }
        return $silvercartCountryISO2;
    }
    
    /**
     * Returns the ISO3 of the related country
     *
     * @return string
     */
    public function getSilvercartCountryISO3() {
        $silvercartCountryISO3 = '';
        if ($this->SilvercartCountryID > 0) {
            $silvercartCountryISO3 = $this->SilvercartCountry()->ISO3;
        }
        return $silvercartCountryISO3;
    }
    
    /**
     * Returns the ISON of the related country
     *
     * @return string
     */
    public function getSilvercartCountryISON() {
        $silvercartCountryISON = '';
        if ($this->SilvercartCountryID > 0) {
            $silvercartCountryISON = $this->SilvercartCountry()->ISON;
        }
        return $silvercartCountryISON;
    }
    
    /**
     * Returns the FIPS of the related country
     *
     * @return string
     */
    public function getSilvercartCountryFIPS() {
        $silvercartCountryFIPS = '';
        if ($this->SilvercartCountryID > 0) {
            $silvercartCountryFIPS = $this->SilvercartCountry()->FIPS;
        }
        return $silvercartCountryFIPS;
    }

    /**
     * Checks, whether this is an invoice address.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.06.2012
     */
    public function isInvoiceAddress() {
        $isInvoiceAddress = false;
        if ($this->ID == Member::currentUser()->SilvercartInvoiceAddressID ||
            $this->isAnonymousInvoiceAddress()) {
            $isInvoiceAddress = true;
        }
        return $isInvoiceAddress;
    }

    /**
     * Checks, whether this is an invoice address.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.06.2012
     */
    public function isShippingAddress() {
        $isShippingAddress = false;
        if ($this->ID == Member::currentUser()->SilvercartShippingAddressID ||
            $this->isAnonymousShippingAddress()) {
            $isShippingAddress = true;
        }
        return $isShippingAddress;
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
    
   
    /**
     * returns field value for given fieldname with stripped slashes
     *
     * @param string $field fieldname
     * 
     * @return string 
     */
    public function getField($field) {
        $parentField = parent::getField($field);
        if (!is_null($parentField)) {
            $parentField = stripcslashes($parentField);
        }
        return $parentField;
    }
}
