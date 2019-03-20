<?php

namespace SilverCart\Model\Customer;

use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Pages\CheckoutStepController;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\Filters\ExactMatchFilter;
use SilverStripe\ORM\Filters\GreaterThanFilter;
use SilverStripe\ORM\Filters\PartialMatchFilter;
use SilverStripe\ORM\Search\SearchContext;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;

/**
 * abstract for a customers address.
 * As a customer might want to get an order delivered to a third person, the address has a FirstName and Surname.
 *
 * @package SilverCart
 * @subpackage Model_Customer
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Address extends DataObject implements PermissionProvider {
    
    const TYPE_INVOICE  = 'Invoice';
    const TYPE_SHIPPING = 'Shipping';
    
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = array(
        'TaxIdNumber'       => 'Varchar(30)',
        'Company'           => 'Varchar(255)',
        'Salutation'        => 'Enum("Herr,Frau","Herr")',
        'AcademicTitle'     => 'Varchar(50)',
        'FirstName'         => 'Varchar(50)',
        'Surname'           => 'Varchar(50)',
        'Addition'          => 'Varchar(255)',
        'PostNumber'        => 'Varchar(255)',
        'Packstation'       => 'Varchar(255)',
        'Street'            => 'Varchar(255)',
        'StreetNumber'      => 'Varchar(15)',
        'Postcode'          => 'Varchar',
        'City'              => 'Varchar(100)',
        'Phone'             => 'Varchar(50)',
        'Fax'               => 'Varchar(50)',
        'IsPackstation'     => 'Boolean(0)',
    );
    
    /**
     * Has-one relationships.
     *
     * @var array
     */
    private static $has_one = array(
        'Member'  => Member::class,
        'Country' => Country::class,
    );
    
    /**
     * Has-one relationships.
     *
     * @var array
     */
    private static $casting = array(
        'FullName'       => 'Text',
        'SalutationText' => 'Varchar',
        'Summary'        => 'Text',
        'CountryISO2'    => 'Text',
        'CountryISO3'    => 'Text',
        'CountryISON'    => 'Text',
        'CountryFIPS'    => 'Text',
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
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartAddress';
    
    /**
     * Custom Add Export fields to export by XML
     *
     * @var array
     */
    public static $custom_add_export_fields = array(
        'CountryISO2',
        'CountryISO3',
        'CountryISON',
        'CountryFIPS',
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
     * Marks the address as shipping address in checkout context.
     *
     * @var bool
     */
    protected $isCheckoutShippingAddress = false;
    
    /**
     * Marks the address as invoice address in checkout context.
     *
     * @var bool
     */
    protected $isCheckoutInvoiceAddress = false;

    /**
     * Marks the address as shipping address in order context.
     *
     * @var bool
     */
    protected $isOrderShippingAddress = null;
    
    /**
     * Marks the address as invoice address in order context.
     *
     * @var bool
     */
    protected $isOrderInvoiceAddress = null;

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
     * Extracts the street name and number out of a street with number string.
     * Examples:
     *   -----------------------------------------------------
     *  | JTL Input       | SC Street Name | SC Street Number |
     *   -----------------------------------------------------
     *  | Teststreet 51   | Teststreet     | 51               |
     *  | Teststreet51    | Teststreet     | 51               |
     *  | Test Street51   | Teststreet     | 51               |
     *  | Test Street 51  | Teststreet     | 51               |
     *  | Teststreet 51a  | Teststreet     | 51a              |
     *  | Teststreet51a   | Teststreet     | 51a              |
     *  | Test Street51a  | Teststreet     | 51a              |
     *  | Test Street 51a | Teststreet     | 51a              |
     *   -----------------------------------------------------
     * 
     * @param string $streetNameWithNumber Street name with number string
     * 
     * @return string[]
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.06.2018
     */
    public static function extract_street_name_and_number($streetNameWithNumber) {
        $streetParts  = [];
        $streetName   = $streetNameWithNumber;
        $streetNumber = '';
        if (preg_match('/([^\d]+)\s?(.+)/i', $streetName, $streetParts)) {
            $streetName   = $streetParts[1];
            $streetNumber = $streetParts[2];
        }
        return [
            $streetName,
            $streetNumber,
        ];
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
     * Set permissions.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.05.2013
     */
    public function providePermissions() {
        return array(
            'SILVERCART_ADDRESS_VIEW'   => $this->fieldLabel('SILVERCART_ADDRESS_VIEW'),
            'SILVERCART_ADDRESS_EDIT'   => $this->fieldLabel('SILVERCART_ADDRESS_EDIT'),
            'SILVERCART_ADDRESS_DELETE' => $this->fieldLabel('SILVERCART_ADDRESS_DELETE')
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
        if (is_null($member)) {
            $member = Security::getCurrentUser();
        }
        if (($member instanceof Member &&
             $member->ID == $this->MemberID &&
             !is_null($this->MemberID)) ||
            Permission::checkMember($member, 'SILVERCART_ADDRESS_VIEW')) {
            $canView = true;
        }
		$results = $this->extend('canView', $member);
		if ($results && is_array($results)) {
            if(!min($results)) {
                $canView = false;
            }
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
        if (is_null($member)) {
            $member = Security::getCurrentUser();
        }
        if (($member instanceof Member &&
             $member->ID == $this->MemberID &&
             !is_null($this->MemberID)) &&
            !($this->isInvoiceAddress() &&
              self::invoice_address_is_readonly())) {
            $canEdit = true;
        }
        if (Permission::checkMember($member, 'SILVERCART_ADDRESS_EDIT')) {
            $canEdit = true;
        }
		$results = $this->extend('canEdit', $member);
		if ($results && is_array($results)) {
            if(!min($results)) {
                $canEdit = false;
            }
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
        if (is_null($member)) {
            $member = Security::getCurrentUser();
        }
        if (($member instanceof Member &&
             $member->ID == $this->MemberID &&
             !is_null($this->MemberID)) &&
            !($this->isInvoiceAddress() &&
              self::invoice_address_is_readonly())) {
            $canDelete = true;
        }
        if (Permission::checkMember($member, 'SILVERCART_ADDRESS_DELETE')) {
            $canDelete = true;
        }
		$results = $this->extend('canDelete', $member);
		if ($results && is_array($results)) {
            if(!min($results)) {
                $canDelete = false;
            }
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
        $fields = DataObjectExtension::getCMSFields($this);
        if ($fields->dataFieldByName('CountryID')) {
            $countryDropdown = new DropdownField(
                    'CountryID',
                    $this->fieldLabel('Country'),
                    Country::getPrioritiveDropdownMap());
            $fields->replaceField('CountryID', $countryDropdown);
        }
        $fields->dataFieldByName('Salutation')->setSource(Tools::getSalutationMap());
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
                'Street'       => $this->fieldLabel('Street'),
                'StreetNumber' => $this->fieldLabel('StreetNumber'),
                'Postcode'     => $this->fieldLabel('Postcode'),
                'City'         => $this->fieldLabel('City'),
                'Country.ISO2' => $this->fieldLabel('Country'),
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
            'TaxIdNumber' => array(
                'title'  => $this->fieldLabel('TaxIdNumber'),
                'filter' => PartialMatchFilter::class,
            ),
            'Company' => array(
                'title'  => $this->fieldLabel('Company'),
                'filter' => PartialMatchFilter::class,
            ),
            'Salutation' => array(
                'title'  => $this->fieldLabel('Salutation'),
                'filter' => ExactMatchFilter::class,
            ),
            'AcademicTitle' => array(
                'title'  => $this->fieldLabel('AcademicTitle'),
                'filter' => PartialMatchFilter::class,
            ),
            'FirstName' => array(
                'title'  => $this->fieldLabel('FirstName'),
                'filter' => PartialMatchFilter::class,
            ),
            'Surname' => array(
                'title'  => $this->fieldLabel('Surname'),
                'filter' => PartialMatchFilter::class,
            ),
            'Addition' => array(
                'title'  => $this->fieldLabel('Addition'),
                'filter' => PartialMatchFilter::class,
            ),
            'PostNumber' => array(
                'title'  => $this->fieldLabel('PostNumber'),
                'filter' => PartialMatchFilter::class,
            ),
            'Packstation' => array(
                'title'  => $this->fieldLabel('Packstation'),
                'filter' => PartialMatchFilter::class,
            ),
            'Street'            => array(
                'title'  => $this->fieldLabel('Street'),
                'filter' => PartialMatchFilter::class,
            ),
            'StreetNumber' => array(
                'title'  => $this->fieldLabel('StreetNumber'),
                'filter' => PartialMatchFilter::class,
            ),
            'Postcode' => array(
                'title'  => $this->fieldLabel('Postcode'),
                'filter' => PartialMatchFilter::class,
            ),
            'City' => array(
                'title'  => $this->fieldLabel('City'),
                'filter' => PartialMatchFilter::class,
            ),
            'Phone' => array(
                'title'  => $this->fieldLabel('Phone'),
                'filter' => PartialMatchFilter::class,
            ),
            'Fax' => array(
                'title'  => $this->fieldLabel('Fax'),
                'filter' => PartialMatchFilter::class,
            ),
            'IsPackstation' => array(
                'title'  => $this->fieldLabel('IsPackstation'),
                'filter' => ExactMatchFilter::class,
            ),
            'Member.ID' => array(
                'title'  => $this->fieldLabel('Member'),
                'filter' => ExactMatchFilter::class,
            ),
            'Country.ID' => array(
                'title'  => $this->fieldLabel('Country'),
                'filter' => ExactMatchFilter::class,
            ),
        );
        
        if ($this->isRestfulContext) {
            $fields = array_merge(
                    $fields,
                    array(
                        'LastEdited' => array(
                            'title'  => $this->fieldLabel('LastEdited'),
                            'filter' => GreaterThanFilter::class,
                        ),
                        'ID'        => array(
                            'title'  => $this->fieldLabel('ID'),
                            'filter' => ExactMatchFilter::class,
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
                'Street'             => _t(Address::class . '.STREET', 'Street'),
                'StreetNumber'       => _t(Address::class . '.STREETNUMBER', 'Streetnumber'),
                'Postcode'           => _t(Address::class . '.POSTCODE', 'Postcode'),
                'City'               => _t(Address::class . '.CITY', 'City'),
                'Phone'              => _t(Address::class . '.PHONE', 'Phone'),
                'PhoneShort'         => _t(Address::class . '.PHONE_SHORT', 'Phone'),
                'Fax'                => _t(Address::class . '.FAX', 'Fax'),
                'Country'            => Country::singleton()->singular_name(),
                'Addition'           => _t(Address::class . '.ADDITION', 'Addition'),
                'PostNumber'         => _t(Address::class . '.POSTNUMBER', 'Your PostNumber'),
                'PostNumberPlain'    => _t(Address::class . '.POSTNUMBER_PLAIN', 'PostNumber'),
                'Packstation'        => _t(Address::class . '.PACKSTATION', 'Packstation (e.g. "Packstation 105")'),
                'PackstationPlain'   => _t(Address::class . '.PACKSTATION_PLAIN', 'Packstation'),
                'Salutation'         => _t(Address::class . '.SALUTATION', 'Salutation'),
                'AcademicTitle'      => _t(Address::class . '.AcademicTitle', 'Academic title'),
                'FirstName'          => _t(Address::class . '.FIRSTNAME', 'Firstname'),
                'Surname'            => _t(Address::class . '.SURNAME', 'Surname'),
                'TaxIdNumber'        => _t(Address::class . '.TAXIDNUMBER', 'Tax ID number'),
                'Company'            => _t(Address::class . '.COMPANY', 'Company'),
                'IsBusinessAccount'  => _t(Address::class . '.ISBUSINESSACCOUNT', 'Is business address'),
                'Name'               => _t(Address::class . '.NAME', 'Name'),
                'UsePackstation'     => _t(Address::class . '.USE_PACKSTATION', 'This is a PACKSTATION address'),
                'UseAbsoluteAddress' => _t(Address::class . '.USE_ABSOLUTEADDRESS', 'This is an absolute address'),
                'IsPackstation'      => _t(Address::class . '.IS_PACKSTATION', 'Address is PACKSTATION'),
                'AddressType'        => _t(Address::class . '.ADDRESSTYPE', 'Type of address'),
                'Member'             => _t(Order::class .  '.CUSTOMER', 'Customer'),
                'PackstationLabel'   => _t(Address::class . '.PACKSTATION_LABEL', 'PACKSTATION'),
                'NoAddressAvailable' => _t(Address::class . '.NO_ADDRESS_AVAILABLE', 'No address available'),
                'Email'              => _t(Address::class . '.EMAIL', 'Email address'),
                'EmailCheck'         => _t(Address::class . '.EMAIL_CHECK', 'Email address check'),
                'EditAddress'        => _t(Address::class . '.EDITADDRESS', 'Edit address'),
                'Mister'             => _t(Address::class . '.MISTER', 'Mister'),
                'Misses'             => _t(Address::class . '.MISSES', 'Misses'),
                'InvoiceAddress'     => _t(Address::class . '.InvoiceAddress', 'Invoice address'),
                'InvoiceAddresses'   => _t(Address::class . '.InvoiceAddresses', 'Invoice addresses'),
                'ShippingAddress'    => _t(Address::class . '.ShippingAddress', 'Shipping address'),
                'ShippingAddresses'  => _t(Address::class . '.ShippingAddresses', 'Shipping addresses'),
                'InvoiceAddressAsShippingAddress' => _t(Address::class . '.InvoiceAddressAsShippingAddress', 'Use invoice address as shipping address'),
                'SILVERCART_ADDRESS_VIEW'   => _t(Address::class . '.SILVERCART_ADDRESS_VIEW', 'View address'),
                'SILVERCART_ADDRESS_EDIT'   => _t(Address::class . '.SILVERCART_ADDRESS_EDIT', 'Edit address'),
                'SILVERCART_ADDRESS_DELETE' => _t(Address::class . '.SILVERCART_ADDRESS_DELETE', 'Delete address'),
            )
        );
        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Updates phone numbers if necessary.
     * Since the PhoneAreaCode property was removed, it has to be concatinated 
     * to the Phone property if not done yet. The PhoneAreaCode database column
     * will be delted.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2018
     */
    public function requireDefaultRecords() {
        
        $databaseConfig = DB::getConfig();
        $databaseName   = $databaseConfig['database'];
        $tableName      = self::config()->get('table_name');
        $columnName     = 'PhoneAreaCode';
        $query          = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '{$databaseName}' AND TABLE_NAME = '{$tableName}' AND COLUMN_NAME = '{$columnName}'";
        $result         = DB::query($query);

        if ($result->numRecords() > 0) {
            $updateQuery = "UPDATE {$tableName} SET Phone = CONCAT({$columnName}, CONCAT(' ', Phone)), {$columnName} = NULL WHERE {$columnName} IS NOT NULL";
            $alterQuery  = "ALTER TABLE {$tableName} DROP COLUMN {$columnName}";
            DB::query($updateQuery);
            DB::query($alterQuery);
        }
        
        parent::requireDefaultRecords();
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

        if (Customer::currentUser() &&
            Customer::currentUser()->Addresses()->count() < 2) {

            $isLastAddress = true;
        }

        return $isLastAddress;
    }
    
    /**
     * Checks whether the given address equals this address.
     * 
     * @param Address $address Address to check equality for.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.01.2014
     */
    public function isEqual(Address $address) {
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
            'Fax',
            'CountryID',
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
        return Tools::getSalutationText($this->Salutation);
    }
    
    /**
     * Returns the ISO2 of the related country
     *
     * @return string
     */
    public function getCountryISO2() {
        $countryISO2 = '';
        if ($this->CountryID > 0) {
            $countryISO2 = $this->Country()->ISO2;
        }
        return $countryISO2;
    }
    
    /**
     * Returns the ISO3 of the related country
     *
     * @return string
     */
    public function getCountryISO3() {
        $countryISO3 = '';
        if ($this->CountryID > 0) {
            $countryISO3 = $this->Country()->ISO3;
        }
        return $countryISO3;
    }
    
    /**
     * Returns the ISON of the related country
     *
     * @return string
     */
    public function getCountryISON() {
        $countryISON = '';
        if ($this->CountryID > 0) {
            $countryISON = $this->Country()->ISON;
        }
        return $countryISON;
    }
    
    /**
     * Returns the FIPS of the related country
     *
     * @return string
     */
    public function getCountryFIPS() {
        $countryFIPS = '';
        if ($this->CountryID > 0) {
            $countryFIPS = $this->Country()->FIPS;
        }
        return $countryFIPS;
    }

    /**
     * Checks, whether this is an invoice address.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.12.2017
     */
    public function isInvoiceAddress() {
        $isOrderInvoiceAddress = $this->getIsOrderInvoiceAddress();
        if (!is_null($isOrderInvoiceAddress)) {
            return $isOrderInvoiceAddress;
        }
        $isInvoiceAddress = false;
        $currentCustomer  = Customer::currentUser();
        if (($currentCustomer instanceof Member &&
             $this->ID == $currentCustomer->InvoiceAddressID) ||
            $this->ID == $this->Member()->InvoiceAddressID ||
            $this->isAnonymousInvoiceAddress()) {
            $isInvoiceAddress = true;
        }
        if (!$isInvoiceAddress) {
            $isInvoiceAddress = $this->getIsCheckoutInvoiceAddress();
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
     * @since 15.12.2017
     */
    public function isShippingAddress() {
        $isOrderShippingAddress = $this->getIsOrderShippingAddress();
        if (!is_null($isOrderShippingAddress)) {
            return $isOrderShippingAddress;
        }
        $isShippingAddress = false;
        $currentCustomer   = Customer::currentUser();
        if (($currentCustomer instanceof Member &&
             $this->ID == $currentCustomer->ShippingAddressID) ||
            $this->ID == $this->Member()->ShippingAddressID ||
            $this->isAnonymousShippingAddress()) {
            $isShippingAddress = true;
        } else if (Controller::curr() instanceof CheckoutStepController) {
            $checkoutData = Controller::curr()->getCheckout()->getData();
            if (array_key_exists('ShippingAddress', $checkoutData) && 
                is_numeric($this->ID) &&
                $this->ID > 0 &&
                $this->ID === $checkoutData['ShippingAddress']) {
                $isShippingAddress = true; 
            }
        }
        if (!$isShippingAddress) {
            $isShippingAddress = $this->getIsCheckoutShippingAddress();
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
     * Returns if this address is the current shipping address in checkout context.
     * 
     * @return bool
     */
    public function getIsCheckoutShippingAddress() {
        return $this->isCheckoutShippingAddress;
    }
   
    /**
     * Returns if this address is the current invoice address in checkout context.
     * 
     * @return bool
     */
    public function getIsCheckoutInvoiceAddress() {
        return $this->isCheckoutInvoiceAddress;
    }
    
    /**
     * Returns if this address is the current shipping address in order context.
     * 
     * @return bool
     */
    public function getIsOrderShippingAddress() {
        return $this->isOrderShippingAddress;
    }
   
    /**
     * Returns if this address is the current invoice address in order context.
     * 
     * @return bool
     */
    public function getIsOrderInvoiceAddress() {
        return $this->isOrderInvoiceAddress;
    }

    /**
     * Sets if this address is the current shipping address in checkout context.
     * 
     * @param bool $isCheckoutShippingAddress Is shipping address?
     * 
     * @return void
     */
    public function setIsCheckoutShippingAddress($isCheckoutShippingAddress) {
        $this->isCheckoutShippingAddress = $isCheckoutShippingAddress;
    }

    /**
     * Sets if this address is the current invoice address in checkout context.
     * 
     * @param bool $isCheckoutInvoiceAddress Is invoice address?
     * 
     * @return void
     */
    public function setIsCheckoutInvoiceAddress($isCheckoutInvoiceAddress) {
        $this->isCheckoutInvoiceAddress = $isCheckoutInvoiceAddress;
    }

    /**
     * Sets if this address is the current shipping address in order context.
     * 
     * @param bool $isOrderShippingAddress Is shipping address?
     * 
     * @return void
     */
    public function setIsOrderShippingAddress($isOrderShippingAddress) {
        $this->isOrderShippingAddress = $isOrderShippingAddress;
    }

    /**
     * Sets if this address is the current invoice address in order context.
     * 
     * @param bool $isOrderInvoiceAddress Is invoice address?
     * 
     * @return void
     */
    public function setIsOrderInvoiceAddress($isOrderInvoiceAddress) {
        $this->isOrderInvoiceAddress = $isOrderInvoiceAddress;
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
     * Renders the address with the default template.
     * 
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function render($headLine = null)
    {
        return $this->customise(['HeadLine' => $headLine])->renderWith(Address::class);
    }
    
    /**
     * Returns the delete link.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.03.2019
     */
    public function DeleteLink() : string
    {
        return Tools::PageByIdentifierCode('SilvercartAddressHolder')->Link("deleteAddress/{$this->ID}");
    }
    
    /**
     * Returns the edit link.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.03.2019
     */
    public function EditLink() : string
    {
        return Tools::PageByIdentifierCode('SilvercartAddressHolder')->Link("edit/{$this->ID}");
    }
    
    /**
     * Returns the link to set this address as default invoice address.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.03.2019
     */
    public function SetAsInvoiceAddressLink() : string
    {
        return Tools::PageByIdentifierCode('SilvercartAddressHolder')->Link("setInvoiceAddress/{$this->ID}");
    }
    
    /**
     * Returns the link to set this address as default shipping address.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.03.2019
     */
    public function SetAsShippingAddressLink() : string
    {
        return Tools::PageByIdentifierCode('SilvercartAddressHolder')->Link("setShippingAddress/{$this->ID}");
    }
}
