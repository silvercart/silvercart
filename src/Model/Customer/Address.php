<?php

namespace SilverCart\Model\Customer;

use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Pages\CheckoutStepController;
use SilverCart\Model\Pages\Page;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBHTMLText;
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
 * 
 * @property string $TaxIdNumber   Tax Id Number
 * @property string $Company       Company
 * @property string $Salutation    Salutation
 * @property string $AcademicTitle Academic Title
 * @property string $FirstName     Firstname
 * @property string $Surname       Surname
 * @property string $Addition      Addition
 * @property string $PostNumber    PostNumber
 * @property string $Packstation   Packstation
 * @property string $Street        Street
 * @property string $StreetNumber  Street Number
 * @property string $Postcode      Postcode
 * @property string $City          City
 * @property string $Phone         Phone
 * @property string $Fax           Fax
 * @property bool   $IsPackstation Is Packstation?
 * @property int    $MemberID      Member ID
 * @property int    $CountryID     Country ID
 * 
 * @method Member  Member()  Returns the related Member.
 * @method Country Country() Returns the related Country.
 */
class Address extends DataObject implements PermissionProvider
{
    use \SilverCart\ORM\ExtensibleDataObject;
    const TYPE_INVOICE      = 'Invoice';
    const TYPE_SHIPPING     = 'Shipping';
    const PERMISSION_CREATE = 'SILVERCART_ADDRESS_CREATE';
    const PERMISSION_DELETE = 'SILVERCART_ADDRESS_DELETE';
    const PERMISSION_EDIT   = 'SILVERCART_ADDRESS_EDIT';
    const PERMISSION_VIEW   = 'SILVERCART_ADDRESS_VIEW';
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = [
        'TaxIdNumber'       => 'Varchar(30)',
        'Company'           => 'Varchar(255)',
        'Salutation'        => 'Enum(",Herr,Frau","")',
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
    ];
    /**
     * Has-one relationships.
     *
     * @var array
     */
    private static $has_one = [
        'Member'  => Member::class,
        'Country' => Country::class,
    ];
    /**
     * Has-one relationships.
     *
     * @var array
     */
    private static $casting = [
        'FullName'       => 'Text',
        'SalutationText' => 'Varchar',
        'Summary'        => 'Text',
        'CountryISO2'    => 'Text',
        'CountryISO3'    => 'Text',
        'CountryISON'    => 'Text',
        'CountryFIPS'    => 'Text',
    ];
    /**
     * Defaults for attributes.
     *
     * @var array
     */
    private static $defaults = [
        'IsPackstation' => '0',
    ];
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
    public static $custom_add_export_fields = [
        'CountryISO2',
        'CountryISO3',
        'CountryISON',
        'CountryFIPS',
    ];
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
     * @var bool|null
     */
    protected $isOrderShippingAddress = null;
    /**
     * Marks the address as invoice address in order context.
     *
     * @var bool|null
     */
    protected $isOrderInvoiceAddress = null;

    /**
     * Sets the customer readonly state for invoice addresses.
     * 
     * @param bool $invoice_address_is_readonly Set to true to make the invoice address readonly
     * 
     * @return void
     */
    public static function set_invoice_address_is_readonly(bool $invoice_address_is_readonly) : void
    {
        self::$invoice_address_is_readonly = $invoice_address_is_readonly;
    }
    
    /**
     * Returns the customer readonly state for invoice addresses. 
     * 
     * @return bool
     */
    public static function get_invoice_address_is_readonly() : bool
    {
        return self::$invoice_address_is_readonly;
    }
    
    /**
     * Returns the customer readonly state for invoice addresses. 
     * 
     * @return bool
     */
    public static function invoice_address_is_readonly() : bool
    {
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
    public static function extract_street_name_and_number(string $streetNameWithNumber) : array
    {
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
     * Set permissions.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.05.2013
     */
    public function providePermissions() : array
    {
        $permissions = [
            self::PERMISSION_VIEW   => [
                'name'     => $this->fieldLabel(self::PERMISSION_VIEW),
                'help'     => $this->fieldLabel(self::PERMISSION_VIEW . '_HELP'),
                'category' => $this->i18n_singular_name(),
                'sort'     => 10,
            ],
            self::PERMISSION_EDIT   => [
                'name'     => $this->fieldLabel(self::PERMISSION_EDIT),
                'help'     => $this->fieldLabel(self::PERMISSION_EDIT . '_HELP'),
                'category' => $this->i18n_singular_name(),
                'sort'     => 20,
            ],
            self::PERMISSION_CREATE => [
                'name'     => $this->fieldLabel(self::PERMISSION_CREATE),
                'help'     => $this->fieldLabel(self::PERMISSION_CREATE . '_HELP'),
                'category' => $this->i18n_singular_name(),
                'sort'     => 30,
            ],
            self::PERMISSION_DELETE => [
                'name'     => $this->fieldLabel(self::PERMISSION_DELETE),
                'help'     => $this->fieldLabel(self::PERMISSION_DELETE . '_HELP'),
                'category' => $this->i18n_singular_name(),
                'sort'     => 40,
            ],
        ];
        $this->extend('updateProvidePermissions', $permissions);
        return $permissions;
    }

    /**
     * Indicates wether the current user can view this object.
     * 
     * @param Member $member Member to check permission for.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.05.2013
     */
    public function canView($member = null) : bool
    {
        $canView = false;
        if (is_null($member)) {
            $member = Security::getCurrentUser();
        }
        if (($member instanceof Member
          && $member->ID == $this->MemberID
          && !is_null($this->MemberID))
         || Permission::checkMember($member, self::PERMISSION_VIEW)
        ) {
            $canView = true;
        }
        $results = $this->extend('canView', $member);
        if ($results
         && is_array($results)
        ) {
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
    public function canEdit($member = null) : bool
    {
        $canEdit = false;
        if (is_null($member)) {
            $member = Security::getCurrentUser();
        }
        if (($member instanceof Member
          && $member->ID == $this->MemberID
          && !is_null($this->MemberID))
         && !($this->isInvoiceAddress()
           && self::invoice_address_is_readonly())
        ) {
            $canEdit = true;
        }
        if (Permission::checkMember($member, self::PERMISSION_EDIT)) {
            $canEdit = true;
        }
        $results = $this->extend('canEdit', $member);
        if ($results
         && is_array($results)
        ) {
            if(!min($results)) {
                $canEdit = false;
            }
        }
        return $canEdit;
    }

    /**
     * Indicates wether the current user can create this object.
     * 
     * @param Member $member  Member to check permission for.
     * @param array  $context Context
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2019
     */
    public function canCreate($member = null, $context = []) : bool
    {
        $can = false;
        if (is_null($member)) {
            $member = Security::getCurrentUser();
        }
        if (Permission::checkMember($member, self::PERMISSION_CREATE)) {
            $can = true;
        }
        $results = $this->extend('canCreate', $member);
        if ($results
         && is_array($results)
        ) {
            if(!min($results)) {
                $can = false;
            }
        }
        return $can;
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
    public function canDelete($member = null) : bool
    {
        $canDelete = false;
        if (is_null($member)) {
            $member = Security::getCurrentUser();
        }
        if (($member instanceof Member
          && $member->ID == $this->MemberID
          && !is_null($this->MemberID))
         && !($this->isInvoiceAddress()
           && self::invoice_address_is_readonly())
        ) {
            $canDelete = true;
        }
        if (Permission::checkMember($member, self::PERMISSION_DELETE)) {
            $canDelete = true;
        }
        $results = $this->extend('canDelete', $member);
        if ($results
         && is_array($results)
        ) {
            if(!min($results)) {
                $canDelete = false;
            }
        }
        return $canDelete;
    }

    /**
     * Indicates wether the current user can set this address as default shipping
     * address.
     * 
     * @param Member $member Member to check permission for.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.05.2019
     */
    public function canSetAsDefaultInvoiceAddress($member = null) : bool
    {
        if (is_null($member)) {
            $member = $this->Member();
        }
        $can = false;
        if ($member->InvoiceAddressID !== $this->ID
         || !$member->InvoiceAddress()->exists()
        ) {
            $can = true;
        }
        $results = $this->extend('canSetAsDefaultInvoiceAddress', $member);
        if ($results
         && is_array($results)
         && !min($results)
        ) {
            $can = false;
        }
        return $can;
    }

    /**
     * Indicates wether the current user can set this address as default shipping
     * address.
     * 
     * @param Member $member Member to check permission for.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.05.2019
     */
    public function canSetAsDefaultShippingAddress($member = null) : bool
    {
        if (is_null($member)) {
            $member = $this->Member();
        }
        $can = false;
        if ($member->ShippingAddressID !== $this->ID) {
            $can = true;
        }
        $results = $this->extend('canSetAsDefaultShippingAddress', $member);
        if ($results
         && is_array($results)
         && !min($results)
        ) {
            $can = false;
        }
        return $can;
    }
    
    /**
     * CMS fields for this object
     * 
     * @param array $params Scaffolding parameters
     * 
     * @return FieldList
     */
    public function getCMSFields($params = null) : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            if ($fields->dataFieldByName('CountryID')) {
                $countryDropdown = DropdownField::create(
                        'CountryID',
                        $this->fieldLabel('Country'),
                        Country::getPrioritiveDropdownMap());
                $fields->replaceField('CountryID', $countryDropdown);
            }
            $fields->dataFieldByName('Salutation')->setSource(Tools::getSalutationMap());
            if ($this->exists()) {
                $created = $this->owner->dbObject('Created');
                /* @var $created \SilverStripe\ORM\FieldType\DBDatetime */
                $createdNice = "{$created->Date()}, {$created->Time()}";
                $fields->insertBefore(\SilverStripe\Forms\ReadonlyField::create('CreatedNice', Tools::field_label('DATE'), $createdNice), 'TaxIdNumber');
            }
        });
        return DataObjectExtension::getCMSFields($this);
    }
    
    /**
     * Sets the summary fields.
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.06.2012
     */
    public function summaryFields() : array
    {
        $summaryFields = [
                'Street'       => $this->fieldLabel('Street'),
                'StreetNumber' => $this->fieldLabel('StreetNumber'),
                'Postcode'     => $this->fieldLabel('Postcode'),
                'City'         => $this->fieldLabel('City'),
                'Country.ISO2' => $this->fieldLabel('Country'),
        ];
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
    public function searchableFields() : array
    {
        $fields = [
            'TaxIdNumber' => [
                'title'  => $this->fieldLabel('TaxIdNumber'),
                'filter' => PartialMatchFilter::class,
            ],
            'Company' => [
                'title'  => $this->fieldLabel('Company'),
                'filter' => PartialMatchFilter::class,
            ],
            'Salutation' => [
                'title'  => $this->fieldLabel('Salutation'),
                'filter' => ExactMatchFilter::class,
            ],
            'AcademicTitle' => [
                'title'  => $this->fieldLabel('AcademicTitle'),
                'filter' => PartialMatchFilter::class,
            ],
            'FirstName' => [
                'title'  => $this->fieldLabel('FirstName'),
                'filter' => PartialMatchFilter::class,
            ],
            'Surname' => [
                'title'  => $this->fieldLabel('Surname'),
                'filter' => PartialMatchFilter::class,
            ],
            'Addition' => [
                'title'  => $this->fieldLabel('Addition'),
                'filter' => PartialMatchFilter::class,
            ],
            'PostNumber' => [
                'title'  => $this->fieldLabel('PostNumber'),
                'filter' => PartialMatchFilter::class,
            ],
            'Packstation' => [
                'title'  => $this->fieldLabel('Packstation'),
                'filter' => PartialMatchFilter::class,
            ],
            'Street' => [
                'title'  => $this->fieldLabel('Street'),
                'filter' => PartialMatchFilter::class,
            ],
            'StreetNumber' => [
                'title'  => $this->fieldLabel('StreetNumber'),
                'filter' => PartialMatchFilter::class,
            ],
            'Postcode' => [
                'title'  => $this->fieldLabel('Postcode'),
                'filter' => PartialMatchFilter::class,
            ],
            'City' => [
                'title'  => $this->fieldLabel('City'),
                'filter' => PartialMatchFilter::class,
            ],
            'Phone' => [
                'title'  => $this->fieldLabel('Phone'),
                'filter' => PartialMatchFilter::class,
            ],
            'Fax' => [
                'title'  => $this->fieldLabel('Fax'),
                'filter' => PartialMatchFilter::class,
            ],
            'IsPackstation' => [
                'title'  => $this->fieldLabel('IsPackstation'),
                'filter' => ExactMatchFilter::class,
            ],
            'Member.ID' => [
                'title'  => $this->fieldLabel('Member'),
                'filter' => ExactMatchFilter::class,
            ],
            'Country.ID' => [
                'title'  => $this->fieldLabel('Country'),
                'filter' => ExactMatchFilter::class,
            ],
        ];
        if ($this->isRestfulContext) {
            $fields = array_merge(
                    $fields,
                    [
                        'LastEdited' => [
                            'title'  => $this->fieldLabel('LastEdited'),
                            'filter' => GreaterThanFilter::class,
                        ],
                        'ID'        => [
                            'title'  => $this->fieldLabel('ID'),
                            'filter' => ExactMatchFilter::class,
                        ],
                    ]
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
    public function getRestfulSearchContext() : SearchContext
    {
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
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
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
            'NoAddressData'      => _t(Address::class . '.NoAddressData', 'No address data available.'),
            'InvoiceAddressAsShippingAddress' => _t(Address::class . '.InvoiceAddressAsShippingAddress', 'Use invoice address as shipping address'),
            self::PERMISSION_VIEW             => _t(Address::class . '.' . self::PERMISSION_VIEW, 'View address'),
            self::PERMISSION_EDIT             => _t(Address::class . '.' . self::PERMISSION_EDIT, 'Edit address'),
            self::PERMISSION_CREATE           => _t(Address::class . '.' . self::PERMISSION_CREATE, 'Create address'),
            self::PERMISSION_DELETE           => _t(Address::class . '.' . self::PERMISSION_DELETE, 'Delete address'),
            self::PERMISSION_VIEW . '_HELP'   => _t(Address::class . '.' . self::PERMISSION_VIEW . '_HELP', 'Allows a user to view any addresses (not only owned ones!). Own addresses can be viewed without this permission.'),
            self::PERMISSION_EDIT . '_HELP'   => _t(Address::class . '.' . self::PERMISSION_EDIT . '_HELP', 'Allows a user to edit any addresses (not only owned ones!). Own addresses can be edited without this permission if it is granted by the general shop configuration (default).'),
            self::PERMISSION_CREATE . '_HELP' => _t(Address::class . '.' . self::PERMISSION_CREATE . '_HELP', 'Allows a customer or user to create addresses.'),
            self::PERMISSION_DELETE . '_HELP' => _t(Address::class . '.' . self::PERMISSION_DELETE . '_HELP', 'Allows a user to delete any addresses (not only owned ones!). Own addresses can be deleted without this permission if it is granted by the general shop configuration (default).'),
        ]);
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
    public function requireDefaultRecords() : void
    {
        $databaseConfig = DB::getConfig();
        $databaseName   = $databaseConfig['database'];
        $tableName      = $this->config()->table_name;
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
    protected function onBeforeWrite() : void
    {
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
    public function getTitle() : string
    {
        $title = $this->singular_name();
        $this->extend('updateTitle', $title);
        return $title;
    }
    
    /**
     * Returns the title to represent this address.
     * 
     * @return string
     */
    public function getSummary() : string
    {
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
        $summary .= $this->Postcode . ' ' . $this->City;
        $this->extend('updateSummary', $summary);
        return $summary;
    }
    
    /**
     * Indicates wether this address is set as a standard address for shipping
     * or invoicing.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.06.2012
     */
    public function hasAddressData() : bool
    {
        $hasAddressData = false;
        if ($this->ID > 0
         || $this->isAnonymous()
        ) {
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
    public function isAnonymous() : bool
    {
        return (bool) $this->isAnonymous;
    }

    /**
     * Returns whether this is an anonymous shipping address
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.06.2012
     */
    public function isAnonymousShippingAddress() : bool
    {
        return (bool) $this->isAnonymousShippingAddress;
    }

    /**
     * Returns whether this is an anonymous invoice address
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.06.2012
     */
    public function isAnonymousInvoiceAddress() : bool
    {
        return (bool) $this->isAnonymousInvoiceAddress;
    }
    
    /**
     * Sets whether this is an anonymous address
     *
     * @param bool $isAnonymous Anonymous?
     *
     * @return $this
     */
    public function setIsAnonymous(bool $isAnonymous) : Address
    {
        $this->isAnonymous = $isAnonymous;
        return $this;
    }
    
    /**
     * Sets whether this is an anonymous shipping address
     *
     * @param bool $isAnonymousShippingAddress Anonymous?
     *
     * @return $this
     */
    public function setIsAnonymousShippingAddress(bool $isAnonymousShippingAddress) : Address
    {
        $this->isAnonymousShippingAddress = $isAnonymousShippingAddress;
        $this->setIsAnonymous($isAnonymousShippingAddress);
        return $this;
    }
    
    /**
     * Sets whether this is an anonymous invoice address
     *
     * @param bool $isAnonymousInvoiceAddress Anonymous?
     *
     * @return $this
     */
    public function setIsAnonymousInvoiceAddress(bool $isAnonymousInvoiceAddress) : Address
    {
        $this->isAnonymousInvoiceAddress = $isAnonymousInvoiceAddress;
        $this->setIsAnonymous($isAnonymousInvoiceAddress);
        return $this;
    }

    /**
     * Indicates wether this address is the address of a company. The fields
     * "Company" and "TaxIdNumber" must be filled in to conform that.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.12.2011
     */
    public function isCompanyAddress() : bool
    {
        $isCompanyAddress = false;
        if (!empty($this->TaxIdNumber)
         && !empty($this->Company)
        ) {
            $isCompanyAddress = true;
        }
        return $isCompanyAddress;
    }

    /**
     * Indicates wether this is the last address of the customer.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.11.2014
     */
    public function isLastAddress() : bool
    {
        $isLastAddress = false;
        if (Customer::currentUser()
         && Customer::currentUser()->Addresses()->count() < 2
        ) {
            $isLastAddress = true;
        }
        return $isLastAddress;
    }
    
    /**
     * Checks whether the given address equals this address.
     * 
     * @param Address $address Address to check equality for.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.01.2014
     */
    public function isEqual(Address $address) : bool
    {
        $isEqual           = true;
        $propertiesToCheck = [
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
        ];
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
    public function getFullName() : string
    {
        return "{$this->FirstName} {$this->Surname}";
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
     * Returns the ISO2 of the related country
     *
     * @return string
     */
    public function getCountryISO2() : string
    {
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
    public function getCountryISO3() : string
    {
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
    public function getCountryISON() : string
    {
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
    public function getCountryFIPS() : string
    {
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
    public function isInvoiceAddress() : bool
    {
        $isOrderInvoiceAddress = $this->getIsOrderInvoiceAddress();
        if (!is_null($isOrderInvoiceAddress)) {
            return $isOrderInvoiceAddress;
        }
        $isInvoiceAddress = false;
        $currentCustomer  = Customer::currentUser();
        if (($currentCustomer instanceof Member
          && $this->ID == $currentCustomer->InvoiceAddressID)
         || ($this->exists()
          && $this->ID == $this->Member()->InvoiceAddressID)
         || $this->isAnonymousInvoiceAddress()
        ) {
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
    public function isShippingAddress() : bool
    {
        $isOrderShippingAddress = $this->getIsOrderShippingAddress();
        if (!is_null($isOrderShippingAddress)) {
            return $isOrderShippingAddress;
        }
        $isShippingAddress = false;
        $currentCustomer   = Customer::currentUser();
        if (($currentCustomer instanceof Member
          && $this->ID == $currentCustomer->ShippingAddressID)
         || ($this->exists()
          && $this->ID == $this->Member()->ShippingAddressID)
         || $this->isAnonymousShippingAddress()
        ) {
            $isShippingAddress = true;
        } elseif (Controller::curr() instanceof CheckoutStepController) {
            $checkoutData = Controller::curr()->getCheckout()->getData();
            if (array_key_exists('ShippingAddress', $checkoutData)
             && is_numeric($this->ID)
             && $this->ID > 0
             && $this->ID === $checkoutData['ShippingAddress']
            ) {
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
     */
    public function isInvoiceAndShippingAddress() : bool
    {
        return $this->isInvoiceAddress() && $this->isShippingAddress();
    }
    
    /**
     * Returns if this address is the current shipping address in checkout context.
     * 
     * @return bool
     */
    public function getIsCheckoutShippingAddress() : bool
    {
        return $this->isCheckoutShippingAddress;
    }
   
    /**
     * Returns if this address is the current invoice address in checkout context.
     * 
     * @return bool
     */
    public function getIsCheckoutInvoiceAddress() : bool
    {
        return $this->isCheckoutInvoiceAddress;
    }
    
    /**
     * Returns if this address is the current shipping address in order context.
     * 
     * @return bool|null
     */
    public function getIsOrderShippingAddress() : ?bool
    {
        return $this->isOrderShippingAddress;
    }
   
    /**
     * Returns if this address is the current invoice address in order context.
     * 
     * @return bool|null
     */
    public function getIsOrderInvoiceAddress() : ?bool
    {
        return $this->isOrderInvoiceAddress;
    }

    /**
     * Sets if this address is the current shipping address in checkout context.
     * 
     * @param bool $isCheckoutShippingAddress Is shipping address?
     * 
     * @return $this
     */
    public function setIsCheckoutShippingAddress(bool $isCheckoutShippingAddress) : Address
    {
        $this->isCheckoutShippingAddress = $isCheckoutShippingAddress;
        return $this;
    }

    /**
     * Sets if this address is the current invoice address in checkout context.
     * 
     * @param bool $isCheckoutInvoiceAddress Is invoice address?
     * 
     * @return $this
     */
    public function setIsCheckoutInvoiceAddress(bool $isCheckoutInvoiceAddress) : Address
    {
        $this->isCheckoutInvoiceAddress = $isCheckoutInvoiceAddress;
        return $this;
    }

    /**
     * Sets if this address is the current shipping address in order context.
     * 
     * @param bool $isOrderShippingAddress Is shipping address?
     * 
     * @return $this
     */
    public function setIsOrderShippingAddress(bool $isOrderShippingAddress) : Address
    {
        $this->isOrderShippingAddress = $isOrderShippingAddress;
        return $this;
    }

    /**
     * Sets if this address is the current invoice address in order context.
     * 
     * @param bool $isOrderInvoiceAddress Is invoice address?
     * 
     * @return $this
     */
    public function setIsOrderInvoiceAddress(bool $isOrderInvoiceAddress) : Address
    {
        $this->isOrderInvoiceAddress = $isOrderInvoiceAddress;
        return $this;
    }
    
    /**
     * returns field value for given fieldname with stripped slashes
     *
     * @param string $field fieldname
     * 
     * @return string 
     */
    public function getField($field)
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
     * Executes an extension hook to add some HTML content after rendering the 
     * default address content.
     * 
     * @param string $separator Separator string to use between the content parts
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.07.2019
     */
    public function AfterAddressContent(string $separator = '<br/>') : DBHTMLText
    {
        $contentParts = $this->extend('updateAfterAddressContent');
        $html         = implode($separator, $contentParts);
        if (!empty($html)) {
            $html .= $separator;
        }
        return DBHTMLText::create()->setValue($html);
    }
    
    /**
     * Executes an extension hook to add some HTML content before rendering the 
     * default address content.
     * 
     * @param string $separator Separator string to use between the content parts
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.07.2019
     */
    public function BeforeAddressContent(string $separator = '<br/>') : DBHTMLText
    {
        $contentParts = $this->extend('updateBeforeAddressContent');
        $html         = implode($separator, $contentParts);
        if (!empty($html)) {
            $html .= $separator;
        }
        return DBHTMLText::create()->setValue($html);
    }
    
    /**
     * Executes an extension hook to add some HTML content before rendering the 
     * country data.
     * 
     * @param string $separator Separator string to use between the content parts
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.05.2020
     */
    public function BeforeCountryContent(string $separator = '<br/>') : DBHTMLText
    {
        $contentParts = $this->extend('updateBeforeCountryContent');
        $html         = implode($separator, $contentParts);
        if (!empty($html)) {
            $html .= $separator;
        }
        return DBHTMLText::create()->setValue($html);
    }
    
    /**
     * Renders the address with the default template or the template with the given
     * $templateAddition.
     * 
     * @param string $templateAddition Template addition
     * @param string $headline         Headline
     * @param string $cssClasses       CSS classes to add
     * 
     * @return DBHTMLText
     */
    public function forTemplate(string $templateAddition = null, string $headline = null, string $cssClasses = null) : DBHTMLText
    {
        $template = Address::class;
        if ($templateAddition !== null) {
            $template = "{$template}_{$templateAddition}";
        }
        return $this->renderWith($template, [
            'HeadLine'   => $headline,
            'CSSClasses' => $cssClasses,
        ]);
    }
    
    /**
     * Renders the address with the default template.
     * 
     * @param string $headline   Headline
     * @param string $cssClasses CSS classes to add
     * 
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function render(string $headline = null, string $cssClasses = null) : DBHTMLText
    {
        return $this->forTemplate(null, $headline, $cssClasses);
    }

    /**
     * Returns the rendered address to use as plain text.
     * 
     * @param string $headLine Headline
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.07.2019
     */
    public function renderPlainText(string $headLine = null) : string
    {
        $plainText = '';
        if ($this->exists()) {
            if (!is_null($headLine)) {
                $plainText .= $headLine . PHP_EOL;
            }
            if (!empty($this->TaxIdNumber)) {
                $plainText .= "{$this->fieldLabel('TaxIdNumber')}: {$this->TaxIdNumber}" . PHP_EOL;
            }
            if (!empty($this->Company)) {
                $plainText .= $this->Company . PHP_EOL;
            }
            $plainText .= "{$this->Salutation} ";
            if (!empty($this->AcademicTitle)) {
                $plainText .= "{$this->AcademicTitle} ";
            }
            $plainText .= "{$this->FullName}" . PHP_EOL;
            if (!empty($this->Addition)) {
                $plainText .= $this->Addition . PHP_EOL;
            }
            if ($this->IsPackstation) {
                $plainText .= "{$this->fieldLabel('PostNumber')}: {$this->PostNumber}" . PHP_EOL;
                $plainText .= "{$this->fieldLabel('PackstationPlain')}: {$this->Packstation}" . PHP_EOL;
            } else {
                $plainText .= "{$this->Street} {$this->StreetNumber}" . PHP_EOL;
            }
            $plainText .= "{$this->Country()->ISO2}-{$this->Postcode} {$this->City}" . PHP_EOL;
            if (!empty($this->Phone)) {
                $plainText .= "{$this->fieldLabel('PhoneShort')}: {$this->Phone}" . PHP_EOL;
            }
            if (!empty($this->Fax)) {
                $plainText .= "{$this->fieldLabel('Fax')}: {$this->Fax}" . PHP_EOL;
            }
        }
        return trim($plainText);
    }
    
    /**
     * Returns the line count of the plain text version.
     * 
     * @param string $headLine Headline
     * 
     * @return int
     */
    public function PlainTextLineCount(string $headLine = null) : int
    {
        return count(explode(PHP_EOL, $this->renderPlainText($headLine)));
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
        return Tools::PageByIdentifierCode(Page::IDENTIFIER_ADDRESS_HOLDER)->Link("deleteAddress/{$this->ID}");
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
        return Tools::PageByIdentifierCode(Page::IDENTIFIER_ADDRESS_HOLDER)->Link("edit/{$this->ID}");
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
        return Tools::PageByIdentifierCode(Page::IDENTIFIER_ADDRESS_HOLDER)->Link("setInvoiceAddress/{$this->ID}");
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
        return Tools::PageByIdentifierCode(Page::IDENTIFIER_ADDRESS_HOLDER)->Link("setShippingAddress/{$this->ID}");
    }

    /**
     * Returns the rendered address to use in emails.
     * 
     * @return DBHTMLText|null
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.07.2019
     */
    public function forEmail() : ?DBHTMLText
    {
        return $this->renderWith('SilverCart/Email/Includes/AddressData');
    }

    /**
     * Returns the rendered address to use in shop owner emails.
     * 
     * @return DBHTMLText|null
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.07.2019
     */
    public function forShopOwnerEmail() : ?DBHTMLText
    {
        return $this->renderWith('SilverCart/Email/Includes/AddressData_ShopOwner');
    }
}