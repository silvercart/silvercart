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
 * abstract for a customers address
 * As a customer might want to get an order delivered to a third person, the address has a FirstName and Surname.
 *
 * @package Silvercart
 * @subpackage Customer
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 * @since 22.10.2010
 */
class SilvercartAddress extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = array(
        'TaxIdNumber'       => 'VarChar(30)',
        'Company'           => 'VarChar(255)',
        'Salutation'        => 'Enum("Herr,Frau","Herr")',
        'AcademicTitle'     => 'VarChar(50)',
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
    private static $has_one = array(
        'Member'            => 'Member',
        'SilvercartCountry' => 'SilvercartCountry'
    );
    
    /**
     * Has-one relationships.
     *
     * @var array
     */
    private static $casting = array(
        'FullName'              => 'Text',
        'SalutationText'        => 'VarChar',
        'Summary'               => 'Text',
        'SilvercartCountryISO2' => 'Text',
        'SilvercartCountryISO3' => 'Text',
        'SilvercartCountryISON' => 'Text',
        'SilvercartCountryFIPS' => 'Text',
    );
    
    /**
     * Defaults for attributes.
     *
     * @var array
     */
    private static $defaults = array(
        'IsPackstation' => '0',
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
     * Grant API access on this item.
     *
     * @var bool
     */
    private static $api_access = true;
    
    /**
     * Set this to true to make the invoice address readonly for customers.
     *
     * @var bool
     */
    private static $invoice_address_is_readonly = false;

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
     * Determines whether the current search context is restful.
     *
     * @var bool
     */
    protected $isRestfulContext = false;

    /**
     * Sets the customer readonly state for invoice addresses.
     * 
     * @param bool $invoice_address_is_readonly Set to true to make the invoice address readonly
     * 
     * @return void
     */
    public static function set_invoice_address_is_readonly($invoice_address_is_readonly) {
        self::$invoice_address_is_readonly = $invoice_address_is_readonly;
    }
    
    /**
     * Returns the customer readonly state for invoice addresses. 
     * 
     * @return bool
     */
    public static function get_invoice_address_is_readonly() {
        return self::$invoice_address_is_readonly;
    }
    
    /**
     * Returns the customer readonly state for invoice addresses. 
     * 
     * @return bool
     */
    public static function invoice_address_is_readonly() {
        return self::get_invoice_address_is_readonly();
    }
    
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
     * Set permissions.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.05.2013
     */
    public function providePermissions() {
        return array(
            'SILVERCART_ADDRESS_VIEW'   => _t('SilvercartAddress.SILVERCART_ADDRESS_VIEW'),
            'SILVERCART_ADDRESS_EDIT'   => _t('SilvercartAddress.SILVERCART_ADDRESS_EDIT'),
            'SILVERCART_ADDRESS_DELETE' => _t('SilvercartAddress.SILVERCART_ADDRESS_DELETE')
        );
    }

    /**
     * Indicates wether the current user can view this object.
     * 
     * @param Member $member Member to check permission for.
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.05.2013
     */
    public function canView($member = null) {
        $canView = false;
        if ((Member::currentUserID() == $this->MemberID &&
             !is_null($this->MemberID)) ||
            Permission::checkMember($member, 'SILVERCART_ADDRESS_VIEW')) {
            $canView = true;
        }
        return $canView;
    }

    /**
     * Indicates wether the current user can edit this object.
     * 
     * @param Member $member Member to check permission for.
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.11.2016
     */
    public function canEdit($member = null) {
        $canEdit = false;
        if ((Member::currentUserID() == $this->MemberID &&
             !is_null($this->MemberID)) &&
            !($this->isInvoiceAddress() &&
              self::invoice_address_is_readonly())) {
            $canEdit = true;
        }
        if (Permission::checkMember($member, 'SILVERCART_ADDRESS_EDIT')) {
            $canEdit = true;
        }
        return $canEdit;
    }

    /**
     * Indicates wether the current user can delete this object.
     * 
     * @param Member $member Member to check permission for.
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.11.2016
     */
    public function canDelete($member = null) {
        $canDelete = false;
        if ((Member::currentUserID() == $this->MemberID &&
             !is_null($this->MemberID)) &&
            !($this->isInvoiceAddress() &&
              self::invoice_address_is_readonly())) {
            $canDelete = true;
        }
        if (Permission::checkMember($member, 'SILVERCART_ADDRESS_DELETE')) {
            $canDelete = true;
        }
        return $canDelete;
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
        $fields->dataFieldByName('Salutation')->setSource(array(
            'Herr' => _t('SilvercartAddress.MISTER'),
            'Frau' => _t('SilvercartAddress.MISSES')
        ));
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
     * Searchable fields of this object.
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.05.2013
     */
    public function searchableFields() {
        $fields = array(
            'TaxIdNumber'       => array(
                'title'     => $this->fieldLabel('TaxIdNumber'),
                'filter'    => 'PartialMatchFilter'
            ),
            'Company'           => array(
                'title'     => $this->fieldLabel('Company'),
                'filter'    => 'PartialMatchFilter'
            ),
            'Salutation'        => array(
                'title'     => $this->fieldLabel('Salutation'),
                'filter'    => 'ExactMatchFilter'
            ),
            'AcademicTitle' => array(
                'title'     => $this->fieldLabel('AcademicTitle'),
                'filter'    => 'PartialMatchFilter'
            ),
            'FirstName'         => array(
                'title'     => $this->fieldLabel('FirstName'),
                'filter'    => 'PartialMatchFilter'
            ),
            'Surname'           => array(
                'title'     => $this->fieldLabel('Surname'),
                'filter'    => 'PartialMatchFilter'
            ),
            'Addition'          => array(
                'title'     => $this->fieldLabel('Addition'),
                'filter'    => 'PartialMatchFilter'
            ),
            'PostNumber'        => array(
                'title'     => $this->fieldLabel('PostNumber'),
                'filter'    => 'PartialMatchFilter'
            ),
            'Packstation'       => array(
                'title'     => $this->fieldLabel('Packstation'),
                'filter'    => 'PartialMatchFilter'
            ),
            'Street'            => array(
                'title'     => $this->fieldLabel('Street'),
                'filter'    => 'PartialMatchFilter'
            ),
            'StreetNumber'      => array(
                'title'     => $this->fieldLabel('StreetNumber'),
                'filter'    => 'PartialMatchFilter'
            ),
            'Postcode'          => array(
                'title'     => $this->fieldLabel('Postcode'),
                'filter'    => 'PartialMatchFilter'
            ),
            'City'              => array(
                'title'     => $this->fieldLabel('City'),
                'filter'    => 'PartialMatchFilter'
            ),
            'PhoneAreaCode'     => array(
                'title'     => $this->fieldLabel('PhoneAreaCode'),
                'filter'    => 'PartialMatchFilter'
            ),
            'Phone'             => array(
                'title'     => $this->fieldLabel('Phone'),
                'filter'    => 'PartialMatchFilter'
            ),
            'Fax'               => array(
                'title'     => $this->fieldLabel('Fax'),
                'filter'    => 'PartialMatchFilter'
            ),
            'IsPackstation'     => array(
                'title'     => $this->fieldLabel('IsPackstation'),
                'filter'    => 'ExactMatchFilter'
            ),
            'Member.ID'        => array(
                'title'     => $this->fieldLabel('Member'),
                'filter'    => 'ExactMatchFilter'
            ),
            'SilvercartCountry.ID'        => array(
                'title'     => $this->fieldLabel('SilvercartCountry'),
                'filter'    => 'ExactMatchFilter'
            ),
        );
        
        if ($this->isRestfulContext) {
            $fields = array_merge(
                    $fields,
                    array(
                        'LastEdited' => array(
                            'title'     => $this->fieldLabel('LastEdited'),
                            'filter'    => 'GreaterThanFilter'
                        ),
                        'ID'        => array(
                            'title'     => $this->fieldLabel('ID'),
                            'filter'    => 'ExactMatchFilter'
                        ),
                    )
            );
        }
        
        return $fields;
    }

    /**
     * Generates a SearchContext to be used for building and processing
     * a generic search form for properties on this object.
     *
     * @return SearchContext
     */
    public function getRestfulSearchContext() {
        $this->isRestfulContext = true;
        return $this->getDefaultSearchContext();
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
                'AcademicTitle'         => _t('SilvercartAddress.AcademicTitle'),
                'FirstName'             => _t('SilvercartAddress.FIRSTNAME'),
                'Surname'               => _t('SilvercartAddress.SURNAME'),
                'TaxIdNumber'           => _t('SilvercartAddress.TAXIDNUMBER'),
                'Company'               => _t('SilvercartAddress.COMPANY'),
                'IsBusinessAccount'     => _t('SilvercartAddress.ISBUSINESSACCOUNT'),
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
     * Returns the title to represent this address.
     * 
     * @return string
     */
    public function getTitle() {
        $title = $this->singular_name();
        $this->extend('updateTitle', $title);
        return $title;
    }
    
    /**
     * Returns the title to represent this address.
     * 
     * @return string
     */
    public function getSummary() {
        $summary = '';
        if (!empty($this->Company)) {
            $summary .= $this->Company . ', ';
        }
        $summary .= $this->SalutationText . ' ';
        if (!empty($this->AcademicTitle)) {
            $summary .= $this->AcademicTitle . ' ';
        }
        $summary .= $this->FullName . ', ';
        if (!empty($this->Addition)) {
            $summary .= $this->Addition . ', ';
        }
        if ($this->IsPackstation) {
            $summary .= $this->PostNumber . ' ';
            $summary .= $this->fieldLabel('Packstation') . ' ' . $this->Packstation . ', ';
        } else {
            $summary .= $this->Street . ' ' . $this->StreetNumber . ', ';
        }
        $summary .= $this->Postcode . ' ' . $this->City . ', ';
        $summary .= $this->Postcode . ' ' . $this->City . ', ';
        $this->extend('updateSummary', $summary);
        return $summary;
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.11.2014
     */
    public function isLastAddress() {
        $isLastAddress = false;

        if (SilvercartCustomer::currentUser() &&
            SilvercartCustomer::currentUser()->SilvercartAddresses()->count() < 2) {

            $isLastAddress = true;
        }

        return $isLastAddress;
    }
    
    /**
     * Checks whether the given address equals this address.
     * 
     * @param SilvercartAddress $address Address to check equality for.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.01.2014
     */
    public function isEqual(SilvercartAddress $address) {
        $isEqual = true;
        
        $propertiesToCheck = array(
            'Salutation',
            'AcademicTitle',
            'FirstName',
            'Surname',
            'Addition',
            'Street',
            'StreetNumber',
            'Postcode',
            'City',
            'Phone',
            'PhoneAreaCode',
            'Fax',
            'SilvercartCountryID',
            'TaxIdNumber',
            'Company',
            'PostNumber',
            'Packstation',
            'IsPackstation',
        );
        $this->extend('updateIsEqualPropertiesToCheck', $propertiesToCheck);
        
        foreach ($propertiesToCheck as $property) {
            if ($this->{$property} != $address->{$property}) {
                $isEqual = false;
                break;
            }
        }
        
        $this->extend('updateIsEqual', $address, $isEqual);
        
        return $isEqual;
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
        return SilvercartTools::getSalutationText($this->Salutation);
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
     * @since 15.11.2014
     */
    public function isInvoiceAddress() {
        $isInvoiceAddress = false;
        if ($this->ID == SilvercartCustomer::currentUser()->SilvercartInvoiceAddressID ||
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Ramon Kupper <rkupper@pixeltricks.de>
     * @since 23.02.2016
     */
    public function isShippingAddress() {
        $isShippingAddress = false;
        if ($this->ID == SilvercartCustomer::currentUser()->SilvercartShippingAddressID ||
            $this->isAnonymousShippingAddress()) {
            $isShippingAddress = true;
        } else if (Controller::curr() instanceof SilvercartCheckoutStep_Controller) {
            $checkoutData = Controller::curr()->getCombinedStepData();
            if (array_key_exists('ShippingAddress', $checkoutData) && 
                is_numeric($this->ID) &&
                $this->ID > 0 &&
                $this->ID === $checkoutData['ShippingAddress']) {
                $isShippingAddress = true; 
            }
        }
        return $isShippingAddress;
    }

    /**
     * Indicates if this is both an invoice and shipping address.
     *
     * @return bool
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Ramon Kupper <rkupper@pixeltricks.de>
     * @since 16.11.2013
     */
    public function isInvoiceAndShippingAddress() {
        $isInvoiceAndShippingAddress = false;
        
        if ($this->isInvoiceAddress() &&
            $this->isShippingAddress()) {

            $isInvoiceAndShippingAddress = true;
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
