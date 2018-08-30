<?php

namespace SilverCart\Model\Customer;

use SilverCart\Dev\Tools;
use SilverCart\Admin\Model\Config;
use SilverCart\Model\ShopEmail;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\CustomerConfig;
use SilverCart\Model\Order\NumberRange;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Order\ShoppingCart;
use SilverCart\Model\Pages\CheckoutStepController;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Shipment\Zone;
use SilverStripe\Admin\SecurityAdmin;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\Filters\ExactMatchFilter;
use SilverStripe\ORM\Filters\PartialMatchFilter;
use SilverStripe\ORM\Search\SearchContext;
use SilverStripe\Security\Group;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use SilverStripe\View\TemplateGlobalProvider;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Security\IdentityStore;

/**
 * Contains additional datafields for SilverCart customers and corresponding
 * methods.
 *
 * @package SilverCart
 * @subpackage Model_Customer
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Customer extends DataExtension implements TemplateGlobalProvider {
    
    /**
     * Comma separated string of related group names
     *
     * @var string[]
     */
    protected $groupNames = [];
    
    /**
     * List of related group IDs
     *
     * @var array[]
     */
    protected $groupIDs = [];
    
    /**
     * Group ID string to use as cache key part
     *
     * @var string
     */
    protected $groupCacheKey = [];
    
    /**
     * Determines whether the customer has to pay taxes or not
     *
     * @var bool[]
     */
    protected $doesNotHaveToPayTaxes = [];
    
    /**
     * DB attributes
     *
     * @return array
     */
    private static $db = [
        'Salutation'                        => "Enum(',Herr,Frau', '')",
        'NewsletterOptInStatus'             => 'Boolean(0)',
        'NewsletterConfirmationHash'        => 'Varchar(50)',
        'SubscribedToNewsletter'            => 'Boolean(0)',
        'HasAcceptedTermsAndConditions'     => 'Boolean(0)',
        'HasAcceptedRevocationInstruction'  => 'Boolean(0)',
        'Birthday'                          => 'Date',
        'CustomerNumber'                    => 'Varchar(128)',
    ];
    
    /**
     * has one attributes
     *
     * @var array
     */
    private static $has_one = [
        'ShoppingCart'    => ShoppingCart::class,
        'InvoiceAddress'  => Address::class,
        'ShippingAddress' => Address::class,
        'CustomerConfig'  => CustomerConfig::class,
    ];
    
    /**
     * has many attributes
     *
     * @var array
     */
    private static $has_many = [
        'Addresses' => Address::class,
        'Orders'    => Order::class,
    ];
    
    /**
     * belongs many many attributes
     *
     * @var array
     */
    private static $belongs_many_many = [
        'PaymentMethods' => PaymentMethod::class,
    ];
    
    /**
     * api access
     *
     * @var array
     */
    private static $api_access = [
        'view' => [
            'Email'
        ],
    ];
    
    /**
     * casted attributes
     *
     * @var array
     */
    private static $casting = [
        'GroupNames' => 'Text',
    ];

    /**
     * Code of default B2C customer group
     *
     * @var string
     */
    public static $default_customer_group_code = 'b2c';

    /**
     * Code of default B2B customer group
     *
     * @var string
     */
    public static $default_customer_group_code_b2b = 'b2b';

    /**
     * List of codes of valid customer group.
     *
     * @var array
     */
    public static $valid_customer_group_codes = [
        'b2c',
        'b2b',
        'administrators',
    ];
    
    /**
     * Holds the current shopping carts for every requested Member.
     *
     * @var array
     */
    private static $shoppingCartList = [];

    // ------------------------------------------------------------------------
    // Extension methods
    // ------------------------------------------------------------------------
    
    /**
     * manipulate the cms fields of the decorated class
     *
     * @param FieldList $fields the field set of cms fields
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.01.2014
     */
    public function updateCMSFields(FieldList $fields) {
        $fields->insertBefore($fields->dataFieldByName('Salutation'), 'FirstName');
        $fields->dataFieldByName('Salutation')->setSource(Tools::getSalutationMap());
        
        $fields->removeByName('NewsletterOptInStatus');
        $fields->removeByName('NewsletterConfirmationHash');
        $fields->removeByName('ShoppingCartID');
        $fields->removeByName('InvoiceAddressID');
        $fields->removeByName('ShippingAddressID');
        $fields->removeByName('CustomerConfigID');
        
        if ($this->owner->exists()) {
            //make addresses deletable in the grid field
            $addressesGrid = $fields->dataFieldByName('Addresses');
            $addressesConfig = $addressesGrid->getConfig();
            $addressesConfig->removeComponentsByType(GridFieldDeleteAction::class);
            $addressesConfig->addComponent(new GridFieldDeleteAction());
        
            $addresses = $this->owner->Addresses()->map('ID', 'Summary')->toArray();

            $invoiceAddressField  = new DropdownField('InvoiceAddressID',  $this->owner->fieldLabel('InvoiceAddress'),  $addresses);
            $shippingAddressField = new DropdownField('ShippingAddressID', $this->owner->fieldLabel('ShippingAddress'), $addresses);
            $fields->insertBefore($invoiceAddressField,  'Locale');
            $fields->insertBefore($shippingAddressField, 'Locale');
        }
    }
    
    /**
     * Updates the CMS fields to use BEFORE the scaffolding is called.
     *
     * @param array &$restrictFields Restrict fields 
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.06.2012
     */
    public function updateRestrictCMSFields(&$restrictFields) {
        $dbFields = (array) $this->owner->config()->get('db');
        foreach ($dbFields as $fieldName => $fieldType) {
            $restrictFields[] = $fieldName;
        }
    }

    /**
     * manipulate the field labels of the decorated class
     *
     * @param array &$labels The labels of cms fields
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.03.2012
     */
    public function updateFieldLabels(&$labels) {
        $labels = array_merge(
                $labels,
                [
                    'Salutation'                        => _t(Customer::class . '.SALUTATION', 'salutation'),
                    'SubscribedToNewsletter'            => _t(Customer::class . '.SUBSCRIBEDTONEWSLETTER', 'subscribed to newsletter'),
                    'HasAcceptedTermsAndConditions'     => _t(Customer::class . '.HASACCEPTEDTERMSANDCONDITIONS', 'has accepted terms and conditions'),
                    'HasAcceptedRevocationInstruction'  => _t(Customer::class . '.HASACCEPTEDREVOCATIONINSTRUCTION', 'has accepted revocation instruction'),
                    'Birthday'                          => _t(Customer::class . '.BIRTHDAY', 'birthday'),
                    'ClassName'                         => _t(Customer::class . '.TYPE', 'type'),
                    'CustomerNumber'                    => _t(Customer::class . '.CUSTOMERNUMBER', 'Customernumber'),
                    'CustomerNumberShort'               => _t(Customer::class . '.CUSTOMERNUMBER_SHORT', 'Customer-No.'),
                    'EmailAddress'                      => Page::singleton()->fieldLabel('EmailAddress'),
                    'FullName'                          => _t(Customer::class . '.FULL_NAME', 'Full name'),
                    'ShoppingCart'                      => ShoppingCart::singleton()->singular_name(),
                    'InvoiceAddress'                    => Address::singleton()->fieldLabel('InvoiceAddress'),
                    'ShippingAddress'                   => Address::singleton()->fieldLabel('ShippingAddress'),
                    'Addresses'                         => Address::singleton()->plural_name(),
                    'Orders'                            => Order::singleton()->plural_name(),
                    'PaymentMethods'                    => PaymentMethod::singleton()->plural_name(),
                    'GroupNames'                        => _t(Customer::class . '.TYPE', 'type'),
                    'AddressCountry'                    => Country::singleton()->singular_name(),
                    'IsBusinessAccount'                 => _t(Customer::class . '.ISBUSINESSACCOUNT', 'Is business account'),
                    
                    'BasicData'                         => _t(Customer::class . '.BASIC_DATA', 'Basics'),
                    'AddressData'                       => _t(Customer::class . '.ADDRESS_DATA', 'Basic address data'),
                    'InvoiceData'                       => _t(Customer::class . '.INVOICE_DATA', 'Invoice address data'),
                    'ShippingData'                      => _t(Customer::class . '.SHIPPING_DATA', 'Shipping address data'),
                ]
        );
    }
    
    /**
     * Defines additional searchable fields.
     *
     * @param array &$fields The searchable fields from the decorated object
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.06.2014
     */
    public function updateSearchableFields(&$fields) {
        $address = new Address();
        
        $addressesCountryFilter = [
            'Addresses.CountryID' => [
                'title'     => $address->fieldLabel('Country'),
                'filter'    => ExactMatchFilter::class,
                'field'     => new DropdownField('Addresses.CountryID', $address->fieldLabel('Country'), Country::getPrioritiveDropdownMap(false, '')),
            ],
        ];
        
        $fields = array_merge(
                $fields,
                [
                    'CustomerNumber' => [
                        'title'     => $this->owner->fieldLabel('CustomerNumber'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'FirstName' => [
                        'title'     => $this->owner->fieldLabel('FirstName'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'Groups.ID' => [
                        'title'     => $this->owner->fieldLabel('GroupNames'),
                        'filter'    => ExactMatchFilter::class,
                    ],
                    'SubscribedToNewsletter' => [
                        'title'     => $this->owner->fieldLabel('SubscribedToNewsletter'),
                        'filter'    => ExactMatchFilter::class,
                    ],
                    'Addresses.FirstName' => [
                        'title'     => $address->fieldLabel('FirstName'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'Addresses.Surname' => [
                        'title'     => $address->fieldLabel('Surname'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'Addresses.Street' => [
                        'title'     => $address->fieldLabel('Street'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'Addresses.StreetNumber' => [
                        'title'     => $address->fieldLabel('StreetNumber'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'Addresses.Postcode' => [
                        'title'     => $address->fieldLabel('Postcode'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'Addresses.City' => [
                        'title'     => $address->fieldLabel('City'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                ],
                $addressesCountryFilter,
                [
                    'InvoiceAddress.FirstName' => [
                        'title'     => $address->fieldLabel('FirstName'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'InvoiceAddress.Surname' => [
                        'title'     => $address->fieldLabel('Surname'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'InvoiceAddress.Street' => [
                        'title'     => $address->fieldLabel('Street'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'InvoiceAddress.StreetNumber' => [
                        'title'     => $address->fieldLabel('StreetNumber'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'InvoiceAddress.Postcode' => [
                        'title'     => $address->fieldLabel('Postcode'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'InvoiceAddress.City' => [
                        'title'     => $address->fieldLabel('City'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'InvoiceAddress.Country.ID' => [
                        'title'     => $address->fieldLabel('Country'),
                        'filter'    => ExactMatchFilter::class,
                    ],
                    
                    'ShippingAddress.FirstName' => [
                        'title'     => $address->fieldLabel('FirstName'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'ShippingAddress.Surname' => [
                        'title'     => $address->fieldLabel('Surname'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'ShippingAddress.Street' => [
                        'title'     => $address->fieldLabel('Street'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'ShippingAddress.StreetNumber' => [
                        'title'     => $address->fieldLabel('StreetNumber'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'ShippingAddress.Postcode' => [
                        'title'     => $address->fieldLabel('Postcode'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'ShippingAddress.City' => [
                        'title'     => $address->fieldLabel('City'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'ShippingAddress.Country.ID' => [
                        'title'     => $address->fieldLabel('Country'),
                        'filter'    => ExactMatchFilter::class,
                    ],
                ]
        );
    }
    
    /**
     * overwrite the summary fields
     *
     * @param array &$fields db fields to summarize the DataObject
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.04.2011
     */
    public function updateSummaryFields(&$fields) {
        if (get_class(Controller::curr()) != SecurityAdmin::class) {
            $fields = [
                'CustomerNumber'            => $this->owner->fieldLabel('CustomerNumber'),
                'Email'                     => $this->owner->fieldLabel('Email'),
                'ShippingAddressSummary'    => $this->owner->fieldLabel('ShippingAddress'),
                'InvoiceAddressSummary'     => $this->owner->fieldLabel('InvoiceAddress'),
                'GroupNames'                => $this->owner->fieldLabel('GroupNames'),
            ];
            $this->owner->extend('overwriteSummaryFields', $fields);
        } else {
            $fields = array_merge(
                    [
                        'CustomerNumber'            => $this->owner->fieldLabel('CustomerNumber'),
                        'GroupNames'                => $this->owner->fieldLabel('GroupNames'),
                    ],
                    $fields
            );
        }
    }

    /**
     * return the orders shipping address as complete string.
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getShippingAddressSummary() {
        $shippingAddressSummary = '';
        $shippingAddressSummary .= $this->owner->ShippingAddress()->FirstName . ' ' . $this->owner->ShippingAddress()->Surname . "<br/>" . PHP_EOL;
        $shippingAddressSummary .= $this->owner->ShippingAddress()->Street . ' ' . $this->owner->ShippingAddress()->StreetNumber . "<br/>" . PHP_EOL;
        $shippingAddressSummary .= $this->owner->ShippingAddress()->Addition == '' ? '' : $this->owner->ShippingAddress()->Addition . "<br/>" . PHP_EOL;
        $shippingAddressSummary .= strtoupper($this->owner->ShippingAddress()->Country()->ISO2) . '-' . $this->owner->ShippingAddress()->Postcode . ' ' . $this->owner->ShippingAddress()->City . "<br/>" . PHP_EOL;
        return Tools::string2html($shippingAddressSummary);
    }

    /**
     * return the orders invoice address as complete string.
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getInvoiceAddressSummary() {
        $invoiceAddressSummary = '';
        $invoiceAddressSummary .= $this->owner->InvoiceAddress()->FirstName . ' ' . $this->owner->InvoiceAddress()->Surname . "<br/>" . PHP_EOL;
        $invoiceAddressSummary .= $this->owner->InvoiceAddress()->Street . ' ' . $this->owner->InvoiceAddress()->StreetNumber . "<br/>" . PHP_EOL;
        $invoiceAddressSummary .= $this->owner->InvoiceAddress()->Addition == '' ? '' : $this->owner->InvoiceAddress()->Addition . "<br/>" . PHP_EOL;
        $invoiceAddressSummary .= strtoupper($this->owner->InvoiceAddress()->Country()->ISO2) . '-' . $this->owner->InvoiceAddress()->Postcode . ' ' . $this->owner->InvoiceAddress()->City . "<br/>" . PHP_EOL;
        return Tools::string2html($invoiceAddressSummary);
    }
    
    // ------------------------------------------------------------------------
    // Casting methods
    // ------------------------------------------------------------------------

    /**
     * Returns the related groups as comma separated list.
     *
     * @return string
     */
    public function getGroupNames() {
        if (!array_key_exists($this->owner->ID, $this->groupNames)) {
            $groupNamesMap      = $this->owner->Groups()->map()->toArray();
            $groupNamesAsString = implode(', ', $groupNamesMap);
            $this->groupNames[$this->owner->ID] = $groupNamesAsString;
        }
        return $this->groupNames[$this->owner->ID];
    }
    
    /**
     * Returns the related groups as comma separated list.
     *
     * @return string
     */
    public function getGroupIDs() {
        if (!array_key_exists($this->owner->ID, $this->groupIDs)) {
            $this->groupIDs[$this->owner->ID] = $this->owner->Groups()->map('ID','ID')->toArray();
        }
        return $this->groupIDs[$this->owner->ID];
    }
    
    /**
     * Returns the related groups as a cache key string.
     *
     * @return string
     */
    public function getGroupCacheKey() {
        if (!array_key_exists($this->owner->ID, $this->groupCacheKey)) {
            $groupCodes = $this->owner->Groups()->sort('Code')->map('ID','Code')->toArray();
            foreach ($groupCodes as $groupID => $groupCode) {
                if ($groupCode == 'administrators') {
                    unset($groupCodes[$groupID]);
                } elseif ($groupCode == 'anonymous') {
                    if (!in_array(self::default_customer_group_code(), $groupCodes)) {
                        $groupCodes[$groupID] = self::default_customer_group_code();
                    } else {
                        unset($groupCodes[$groupID]);
                    }
                }
            }
            $this->groupCacheKey[$this->owner->ID] = implode('_', $groupCodes);
        }
        if (Controller::curr()->getRequest()->getVar('stage') == 'Stage' &&
            Controller::curr()->canViewStage('Stage', $this->owner)) {
            $this->groupCacheKey[$this->owner->ID] .= '_' . uniqid('StageRandomCacheKey');
        }
        return $this->groupCacheKey[$this->owner->ID];
    }
    
    /**
     * Returns the group cache key for the current session Member context.
     * 
     * @return string
     */
    public static function get_group_cache_key() {
        $cacheKey = self::default_customer_group_code();
        $member   = self::currentUser();
        if ($member instanceof Member) {
            $cacheKey = $member->getGroupCacheKey();
        }
        return $cacheKey;
    }

    /**
     * Returns the localized salutation string.
     *
     * @return string
     */
    public function getSalutationText() {
        return Tools::getSalutationText($this->owner->Salutation);
    }

    /**
     * Returns the localized salutation string.
     *
     * @return string
     */
    public function getAnonymousName() {
        $anonymousName = $this->owner->FirstName;
        if (!empty($this->owner->Surname)) {
            $anonymousName .= ' ' . ucfirst(substr(trim($this->owner->Surname), 0, 1)) . '.';
        }
        return $anonymousName;
    }
    
    // ------------------------------------------------------------------------
    // Regular methods
    // ------------------------------------------------------------------------
    
    /**
     * Returns whether the current customer is a registered one.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.04.2015
     */
    public function isRegisteredCustomer() {
        $isRegisteredCustomer = false;
        if ($this->owner->Groups()->find('Code', self::default_customer_group_code()) ||
            $this->owner->Groups()->find('Code', self::default_customer_group_code_b2b()) ||
            $this->owner->Groups()->find('Code', 'administrators')) {

            $isRegisteredCustomer = true;
        }
        return $isRegisteredCustomer;
    }
    
    /**
     * Returns whether the current customer is a anonymous one.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function isAnonymousCustomer() {
        $isAnonymousCustomer = false;
        if ($this->owner->Groups()->find('Code', 'anonymous')) {
            $isAnonymousCustomer = true;
        }
        return $isAnonymousCustomer;
    }
    
    /**
     * Returns whether the current customer is in the given zone.
     * 
     * @var \SilverCart\Model\Shipment\Zone $zone Zone
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.04.2018
     */
    public function isInZone($zone) {
        $isInZone = true;
        if ($zone instanceof Zone &&
            $zone->exists()) {
            $isInZone = false;
            
            $shippingAddress = $this->owner->ShippingAddress();
            $shippingCountry = $shippingAddress->Country();
            if ($shippingCountry->exists()) {
                $matchingZones = Zone::getZonesFor($shippingCountry->ID);
                if ($matchingZones->exists()) {
                    $foundZone = $matchingZones->byID($zone->ID);
                    if ($foundZone instanceof Zone &&
                        $foundZone->exists()) {
                        $isInZone = true;
                    }
                }
            }
        }
        return $isInZone;
    }

    /**
     * Creates an anonymous customer if there's no currentMember object.
     *
     * @return Member
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.11.2014
     */
    public static function createAnonymousCustomer() {
        $member = self::currentUser();
        
        if (!$member) {
            $member = new Member();
            $member->write();
            
            // Add customer to intermediate group
            $customerGroup = Group::get()->filter('Code', 'anonymous')->first();
            
            if ($customerGroup) {
                $member->Groups()->add($customerGroup);
            }
            
            $member->logIn(true);
            /** @var IdentityStore $identityStore */
            $identityStore = Injector::inst()->get(IdentityStore::class);
            $identityStore->logIn($member, false, Controller::curr()->getRequest());
        }
        
        return $member;
    }
    
    /**
     * Returns the Member object if the current Member is an anonymous
     * customer.
     * If the user is not logged in or the Member is not anonymous boolean
     * false will be returned.
     *
     * @return mixed Member|boolean false
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.11.2014
     */
    public static function currentAnonymousCustomer() {
        $member = self::currentUser();
        
        if ($member instanceof Member &&
            $member->exists() &&
            $member->isAnonymousCustomer()) {
            
            return $member;
        }
        
        return false;
    }
    
    /**
     * Returns the default customer group code.
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2014
     */
    public static function default_customer_group_code() {
        return self::$default_customer_group_code;
    }
    
    /**
     * Returns the default customer group code B2B.
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2014
     */
    public static function default_customer_group_code_b2b() {
        return self::$default_customer_group_code_b2b;
    }
    
    /**
     * Returns the default B2C group.
     * 
     * @return Group
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2014
     */
    public static function default_customer_group() {
        return Group::get()->filter('Code', self::default_customer_group_code())->first();
    }
    
    /**
     * Returns the default B2B group.
     * 
     * @return Group
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2014
     */
    public static function default_customer_group_b2b() {
        return Group::get()->filter('Code', self::default_customer_group_code_b2b())->first();
    }
    
    /**
     * Returns whether this customer is a B2B customer.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.04.2015
     */
    public function isB2BCustomer() {
        $isB2BCustomer = false;
        if ($this->owner->Groups()->find('Code', self::default_customer_group_code_b2b())) {
            $isB2BCustomer = true;
        }
        return $isB2BCustomer;
    }

        /**
     * Returns whether this is a valid customer.
     * 
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2014
     */
    public function isValidCustomer() {
        $isValidCustomer = false;
        $member          = $this->owner;
        
        if ($member->Groups()->exists()) {
            $map = $member->Groups()->map('ID', 'Code')->toArray();
            foreach ($map as $groupCode) {
                if (in_array($groupCode, self::$valid_customer_group_codes)) {
                    $isValidCustomer = true;
                    break;
                }
            }
        }
        return $isValidCustomer;
    }

    /**
     * Function similar to Customer::currentUser(); Determins if we deal with a
     * registered customer who has opted in. Returns the member object or
     * false.
     *
     * @return mixed Member|boolean(false)
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public static function currentRegisteredCustomer() {
        $member             = self::currentUser();
        $registeredCustomer = false;
        
        if ($member instanceof Member &&
            $member->isValidCustomer()) {
            $registeredCustomer = $member;
        }
        
        return $registeredCustomer;
    }

    /**
     * Returns the current user.
     * 
     * @return Member
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.04.2018
     */
    public static function currentUser() {
        return Security::getCurrentUser();
    }
    
    /**
     * Returns a customers purchased products
     * 
     * @return ArrayList
     */
    public function getPurchasedProducts() {
        $orders             = $this->owner->Orders();
        $purchasedProducts  = new ArrayList();
        
        foreach ($orders as $order) {
            $positions = $order->OrderPositions();
            foreach ($positions as $position) {
                if (!$purchasedProducts->find('ID', $position->ProductID)) {
                    $purchasedProducts->push($position->Product());
                }
            }
        }
        
        return $purchasedProducts;
    }

    /**
     * Returns whether the given product is already purchased by customer or not
     * 
     * @param Product $product Product to check
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.08.2012
     */
    public function isPurchasedProduct(Product $product) {
        $isPurchasedProduct = false;
        $purchasedProducts  = $this->getPurchasedProducts();
        if ($purchasedProducts->find('ID', $product->ID)) {
            $isPurchasedProduct = true;
        }
        return $isPurchasedProduct;
    }

    /**
     * Get the customers shopping cart or create one if it doesn't exist yet.
     * 
     * @return ShoppingCart
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 01.12.2014
     */
    public function getCart() {
        $id = $this->owner->ID;

        if (!array_key_exists($id, self::$shoppingCartList)) {
            if ($this->owner->ShoppingCartID == 0 ||
                !ShoppingCart::get()->byID($this->owner->ShoppingCartID)) {
                $cart = new ShoppingCart();
                $cart->write();
                $this->owner->ShoppingCartID = $cart->ID;
                $this->owner->write();
            }

            self::$shoppingCartList[$id] = $this->owner->ShoppingCart();
        }
        return self::$shoppingCartList[$id];
    }
    
    /**
     * Returns all customer groups of the current customer as a DataList.
     * If Customer::currentUser() does not exist, the group for anonymous customers
     * will be returned. If no group for anonymous customers exists, null will 
     * be returned.
     * 
     * @return DataList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public static function getCustomerGroups() {
        $customer = Customer::currentUser();
        if ($customer) {
            $customerGroups = $customer->Groups();
        } else {
            $customerGroups = Group::get()->filter("Code", "anonymous");
        }
        return $customerGroups;
    }
    
    /**
     * Defines which attributes of an object can be accessed via api
     * 
     * @return SearchContext
     */
    public static function getRestfulSearchContext() {
        $fields  = FieldList::create([TextField::create('Email')]);
        $filters = ['Email' => ExactMatchFilter::create('Email')];
        return SearchContext::create('Member', $fields, $filters);
    }
    
    /**
     * Returns the translated salutation.
     *
     * @return string
     */
    public function getTranslatedSalutation() {
        $salutation = '';
        
        switch ($this->owner->Salutation) {
            case 'Frau':
                $salutation = Address::singleton()->fieldLabel('Misses');
                break;
            case 'Herr':
                $salutation = Address::singleton()->fieldLabel('Mister');
                break;
        }
            
        return $salutation;
    }
    
    /**
     * Get the customer's configuration object or create one if it doesn't
     * exist yet.
     *
     * @return CustomerConfig
     */
    public function getCustomerConfig() {
        if (!$this->owner->CustomerConfigID ||
            !CustomerConfig::get()->byID($this->owner->CustomerConfigID)) {
            
            $customerConfig                   = new CustomerConfig();
            $customerConfig->MemberID         = $this->owner->ID;
            $customerConfig->productsPerPage  = Config::getProductsPerPageDefault();
            $customerConfig->write();
            
            $this->owner->CustomerConfigID = $customerConfig->ID;
            $this->owner->write();
        }
        
        return $this->owner->CustomerConfig();
    }
    
    /**
     * Indicates wether the customer has finished the newsletter opt-in or not.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.10.2011
     */
    public function hasFinishedNewsletterOptIn() {
        $hasFinishedNewsletterOptIn = false;
        
        if ($this->owner->NewsletterOptInStatus) {
            $hasFinishedNewsletterOptIn = true;
        }
        
        return $hasFinishedNewsletterOptIn;
    }
    
    /**
     * Indicates wether the customer has defined only one address to be both
     * invoice and shipping address.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.10.2011
     */
    public function hasOnlyOneStandardAddress() {
        $hasOnlyOneStandardAddress = false;
        
        if ($this->owner->InvoiceAddressID == $this->owner->ShippingAddressID &&
            $this->owner->InvoiceAddressID > 0) {
            $hasOnlyOneStandardAddress = true;
        }
        
        return $hasOnlyOneStandardAddress;
    }
    
    /**
     * used to determine weather something should be shown on a template or not
     * 
     * @param bool $ignoreTaxExemption Determines whether to ignore tax exemption or not.
     *
     * @return bool
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.07.2013
     */
    public function showPricesGross($ignoreTaxExemption = false) {
        $pricetype = Config::Pricetype();
        
        if (!$ignoreTaxExemption &&
            $this->doesNotHaveToPayTaxes()) {
            $pricetype = 'net';
        }
        
        if ($pricetype == "gross") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns whether the customer has to pay tax or not.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.12.2013
     */
    public function doesNotHaveToPayTaxes() {
        if (!array_key_exists($this->owner->ID, $this->doesNotHaveToPayTaxes)) {
            $doesNotHaveToPayTaxes = null;
            if (Controller::curr() instanceof CheckoutStepController) {
                $checkoutData = Controller::curr()->getCheckout()->getData();
                if (array_key_exists('Shipping_Country', $checkoutData)) {
                    $country = Country::get()->byID($checkoutData['Shipping_Country']);
                    if ($country instanceof Country) {
                        $doesNotHaveToPayTaxes = (boolean) $country->IsNonTaxable;
                    }
                }
            }
            if (is_null($doesNotHaveToPayTaxes) && 
                $this->owner->ShippingAddress() instanceof Address &&
                $this->owner->ShippingAddress()->Country()->IsNonTaxable) {
                $doesNotHaveToPayTaxes = true;
            } elseif (is_null($doesNotHaveToPayTaxes)) {
                $doesNotHaveToPayTaxes = false;
            }
            $this->doesNotHaveToPayTaxes[$this->owner->ID] = $doesNotHaveToPayTaxes;
        }
        return $this->doesNotHaveToPayTaxes[$this->owner->ID];
    }

    /**
     * Returns the members price type
     *
     * @return string 
     */
    public function getPriceType() {
        $priceType = Config::DefaultPriceType();
        foreach ($this->owner->Groups() as $group) {
            if (!empty($group->Pricetype) &&
                $group->Pricetype != '---') {
                $priceType = $group->Pricetype;
                break;
            }
        }
        return $priceType;
    }
    
    // ------------------------------------------------------------------------
    // Hooks
    // ------------------------------------------------------------------------

    /**
     * If the user is not anonymous a customer number is attributed to the
     * Member if none is yet given.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.10.2011
     */
    public function onBeforeWrite() {
        parent::onBeforeWrite();
        
        if (!self::currentAnonymousCustomer()) {
            if (empty($this->owner->CustomerNumber)) {
                $this->owner->CustomerNumber = NumberRange::useReservedNumberByIdentifier('CustomerNumber');
            }
        }
    }
    
    /**
     * Attributes a shopping cart to the Member if none is attributed yet.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.10.2011
     */
    public function onAfterWrite() {
        parent::onAfterWrite();

        if ($this->owner->ShoppingCartID === null) {
            $cart = new ShoppingCart();
            $cart->write();
            $this->owner->ShoppingCartID = $cart->ID;
            $this->owner->write();
        }
        
        // check whether to add a member to an administrative group
        if (Customer::currentUser() &&
            Customer::currentUser()->inGroup('administrators') &&
            array_key_exists('Groups', $_POST)) {
            $groups = explode(',', $_POST['Groups']);
            if (count($groups) > 0) {
                foreach ($groups as $group) {
                    if (!($this->owner->Groups()->byID($group) instanceof Group) ||
                        !$this->owner->Groups()->byID($group)->exists()) {
                        $groupToAdd = Group::get()->byID($group);
                        if ($groupToAdd->exists()) {
                            $groupToAdd->Members()->add($this->owner);
                        }
                    }
                }
                if ($this->owner->Groups()->count() > count($groups)) {
                    foreach ($this->owner->Groups() as $group) {
                        if (!in_array($group->ID, $groups)) {
                            $group->Members()->remove($this->owner);
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Delete the attributed shopping cart if existant.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.10.2011
     */
    public function onAfterDelete() {
        parent::onAfterDelete();
        
        if ($this->owner->ShoppingCartID !== null) {
            $cart = ShoppingCart::get()->byID($this->owner->ShoppingCartID);
            if ($cart) {
                $cart->delete();
            }
        }
    }

    /**
     * Returns true if this user is an administrator.
     * Administrators have access to everything.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.02.2013
     */
    public function isAdmin() {
        return Permission::check('ADMIN', 'any', $this->owner);
    }

    /**
     * Returns true if the current user is an administrator.
     * Administrators have access to everything.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    public static function is_admin() {
        return Permission::check('ADMIN', 'any', self::currentUser());
    }
    
    /**
     * Returns the globals to use in template.
     * Overwrites the default globals for Member.
     * 
     * @return array
     */
    public static function get_template_global_variables() {
        return [
            'CurrentMember'   => 'currentUser',
            'CurrentCustomer' => 'currentUser',
            'currentCustomer' => 'currentUser',
            'currentUser'     => 'currentUser',
        ];
    }
    
    /**
     * Sends an email to the customer, containing a link to change the password.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.01.2017
     */
    public function sendChangePasswordEmail() {
        /* @var $member Member */
        $member                         = $this->owner;
        $variables                      = $member->toMap();
        $token                          = $member->generateAutologinTokenAndStoreHash();
        $variables['PasswordResetLink'] = Director::absoluteURL(Security::getPasswordResetLink($member, $token));
        
        $memberDbFields = (array)$this->owner->config()->get('db');
        foreach ($memberDbFields as $dbFieldName => $dbFieldType) {
            if (!array_key_exists($dbFieldName, $variables)) {
                $variables[$dbFieldName] = $member->{$dbFieldName};
            }
        }
        $variables['SalutationText'] = Tools::getSalutationText($variables['Salutation']);
        $variables['InvoiceAddress'] = $this->owner->InvoiceAddress();

        $this->requireDefaultChangePasswordEmail();
        ShopEmail::send(
                'ChangePassword',
                $member->Email,
                $variables
        );
    }
    
    /**
     * Creates the change password email in backend.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.01.2017
     */
    public function requireDefaultChangePasswordEmail() {
        $shopEmailChangePasswordEmail = ShopEmail::get()->filter('TemplateName', 'ChangePasswordEmail')->first();
        if (!$shopEmailChangePasswordEmail) {
            $shopEmailChangePasswordEmail = new ShopEmail();
            $shopEmailChangePasswordEmail->TemplateName = 'ChangePasswordEmail';
            $shopEmailChangePasswordEmail->Subject      = _t(ShopEmail::class . '.CHANGE_PASSWORD_SUBJECT', 'Change your password');
            $shopEmailChangePasswordEmail->write();
        }
    }
}