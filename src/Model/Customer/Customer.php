<?php

namespace SilverCart\Model\Customer;

use Moo\HasOneSelector\Form\Field as HasOneSelector;
use SilverCart\Dev\Tools;
use SilverCart\Admin\Model\Config;
use SilverCart\Model\ShopEmail;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\CustomerConfig;
use SilverCart\Model\DataValue;
use SilverCart\Model\Order\NumberRange;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Order\OrderStatus;
use SilverCart\Model\Order\ShoppingCart;
use SilverCart\Model\Order\ShoppingCartPosition;
use SilverCart\Model\Pages\CheckoutStepController;
use SilverCart\Model\Pages\CustomerDataPage;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Shipment\Zone;
use SilverStripe\Admin\SecurityAdmin;
use SilverStripe\CMS\Controllers\ModelAsController;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\Filters\ExactMatchFilter;
use SilverStripe\ORM\Filters\PartialMatchFilter;
use SilverStripe\ORM\Search\SearchContext;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Group;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\TemplateGlobalProvider;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Security\IdentityStore;
use SilverStripe\Security\LoginAttempt;
use SilverStripe\Security\Member_Validator;
use SilverStripe\Security\PermissionProvider;

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
 * 
 * @property Member $owner Owner
 */
class Customer extends DataExtension implements TemplateGlobalProvider, PermissionProvider
{
    const GROUP_CODE_ADMINISTRATORS = 'administrators';
    const GROUP_CODE_ANONYMOUS      = 'anonymous';
    const GROUP_CODE_B2B            = 'b2b';
    const GROUP_CODE_B2C            = 'b2c';
    const PERMISSION_CREATE         = 'SILVERCART_CUSTOMER_CREATE';
    const PERMISSION_EDIT           = 'SILVERCART_CUSTOMER_EDIT';
    const PERMISSION_DELETE         = 'SILVERCART_CUSTOMER_DELETE';
    const PERMISSION_VIEW           = 'SILVERCART_CUSTOMER_VIEW';
    const SESSION_KEY_SHIPPING_COUNTRY_ID = 'SilverCart.ShippingCountryID';
    
    /**
     * Returns the current shipping country
     *
     * @return Country|null
     */
    public static function currentShippingCountry() : ?Country
    {
        self::setCurrentShippingCountry();
        $shippingCountry = Country::get()->filter([
            'ID'     => (int) Tools::Session()->get(self::SESSION_KEY_SHIPPING_COUNTRY_ID),
            'Active' => true,
        ])->first();
        if ($shippingCountry === null) {
            $customer = Customer::currentUser();
            if ($customer) {
                $shippingCountry = $customer->ShippingAddress()->Country();
            }
            if ($shippingCountry === null
             || !$shippingCountry->exists()
            ) {
                $shippingCountry = Country::get()->filter([
                    'ISO2'   => substr(Tools::current_locale(), 3),
                    'Active' => 1,
                ])->first();
            }
            if (!($shippingCountry instanceof Country)
             || !$shippingCountry->exists()
            ) {
                $shippingCountry = SiteConfig::current_site_config()->getShopCountry();
            }
            if (!($shippingCountry instanceof Country)
             || !$shippingCountry->exists()
            ) {
                $shippingCountry = Country::get()->filter('Active', true)->first();
            }
            if ($shippingCountry instanceof Country) {
                Tools::Session()->set(self::SESSION_KEY_SHIPPING_COUNTRY_ID, $shippingCountry->ID);
                Tools::saveSession();
            }
        }
        return $shippingCountry;
    }
    
    /**
     * Sets the current shipping country context.
     * 
     * @param Country|null $country Country
     * 
     * @return void
     */
    public static function setCurrentShippingCountry(Country $country = null) : void
    {
        if (!($country instanceof Country)
         && Controller::has_curr()
        ) {
            $ctrl = Controller::curr();
            if ($ctrl->hasMethod('getShippingAddress')) {
                $address = $ctrl->getShippingAddress();
                if ($address instanceof Address
                 && $address->Country()->exists()
                ) {
                    Tools::Session()->set(self::SESSION_KEY_SHIPPING_COUNTRY_ID, $address->Country()->ID);
                    Tools::saveSession();
                    return;
                }
            }
        }
        if ($country instanceof Country) {
            Tools::Session()->set(self::SESSION_KEY_SHIPPING_COUNTRY_ID, $country->ID);
            Tools::saveSession();
        } elseif (array_key_exists('ShippingCountryID', $_POST)) {
            Tools::Session()->set(self::SESSION_KEY_SHIPPING_COUNTRY_ID, (int) $_POST['ShippingCountryID']);
            Tools::saveSession();
        } elseif (array_key_exists('scid', $_GET)) {
            Tools::Session()->set(self::SESSION_KEY_SHIPPING_COUNTRY_ID, (int) $_GET['scid']);
            Tools::saveSession();
        }
    }

    /**
     * Returns the registration opt-in confirmation base link.
     * 
     * @return string
     */
    public static function getRegistrationOptInConfirmationBaseLink() : string
    {
        if (empty(self::$registrationOptInConfirmationBaseLink)) {
            self::$registrationOptInConfirmationBaseLink = Tools::PageByIdentifierCode(Page::IDENTIFIER_REGISTRATION_PAGE)->Link('optin');
        }
        return self::$registrationOptInConfirmationBaseLink;
    }
    
    /**
     * Sets the registration opt-in confirmation base link.
     * 
     * @param string $link Base link to set
     * 
     * @return void
     */
    public static function setRegistrationOptInConfirmationBaseLink(string $link = null) : void
    {
        self::$registrationOptInConfirmationBaseLink = $link;
    }
    
    /**
     * Returns whether to hide prices for the current customer context.
     * 
     * @return bool
     */
    public static function hidePrices() : bool
    {
        $hide = self::getCustomerGroups()->filter('HidePrices', true)->exists();
        Member::singleton()->extend('updateHidePrices', $hide);
        return $hide;
    }
    
    /**
     * Returns an optional information HTML content to show when hiding prices.
     * 
     * @return DBHTMLText
     */
    public static function hidePricesInfo() : DBHTMLText
    {
        $info = DBHTMLText::create();
        if (self::hidePrices()) {
            $groups = self::getCustomerGroups()->filter('HidePrices', true);
            $html   = '';
            foreach ($groups as $group) {
                /* @var $group Group */
                if ((string) $group->HidePricesInfo === '') {
                    continue;
                }
                $html .= $group->HidePricesInfo;
            }
            $info->setValue($html);
        }
        return $info;
    }

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
     * @var string[]
     */
    protected $groupCacheKey = [];
    /**
     * Determines whether the customer has to pay taxes or not
     *
     * @var bool[]
     */
    protected $doesNotHaveToPayTaxes = [];
    /**
     * Skip change password info message?
     * 
     * @var bool
     */
    protected bool $skipChangePasswordInfo = false;
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
        'RegistrationOptInConfirmationHash' => 'Varchar(128)',
        'RegistrationOptInConfirmed'        => 'Boolean(0)',
        'Birthday'                          => 'Date',
        'CustomerNumber'                    => 'Varchar(128)',
        'MarkForDeletion'                   => 'Boolean(0)',
        'MarkForDeletionDate'               => 'Date',
        'MarkForDeletionReason'             => 'Text',
        'MarkForDeletionReasonID'           => 'Int',
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
        'PaymentMethods'       => PaymentMethod::class . '.ShowOnlyForUsers',
        'HiddenPaymentMethods' => PaymentMethod::class . '.ShowNotForUsers',
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
     * Default sort.
     *
     * @var string
     */
    private static $default_sort = "CustomerNumber DESC";
    /**
     * Code of default B2C customer group
     *
     * @var string
     */
    private static $default_customer_group_code = self::GROUP_CODE_B2C;
    /**
     * Code of default B2B customer group
     *
     * @var string
     */
    private static $default_customer_group_code_b2b = self::GROUP_CODE_B2B;
    /**
     * List of codes of valid customer group.
     *
     * @var array
     */
    private static $valid_customer_group_codes = [
        self::GROUP_CODE_B2C,
        self::GROUP_CODE_B2B,
        self::GROUP_CODE_ADMINISTRATORS,
    ];
    /**
     * Holds the current shopping carts for every requested Member.
     *
     * @var array
     */
    private static $shoppingCartList = [];
    /**
     * Registration opt-in confirmation base link.
     *
     * @var string|null
     */
    protected static $registrationOptInConfirmationBaseLink = null;
    /**
     * Stores the called status seperated by customer ID.
     *
     * @var bool[]
     */
    protected $getCMSFieldsIsCalled = [];

    // ------------------------------------------------------------------------
    // Extension methods
    // ------------------------------------------------------------------------

    /**
     * Set permissions.
     *
     * @return array
     */
    public function providePermissions() : array
    {
        $customer = Member::singleton();
        $permissions = [
            self::PERMISSION_VIEW   => [
                'name'     => $customer->fieldLabel(self::PERMISSION_VIEW),
                'help'     => $customer->fieldLabel(self::PERMISSION_VIEW . '_HELP'),
                'category' => $customer->i18n_singular_name(),
                'sort'     => 10,
            ],
            self::PERMISSION_CREATE   => [
                'name'     => $customer->fieldLabel(self::PERMISSION_CREATE),
                'help'     => $customer->fieldLabel(self::PERMISSION_CREATE . '_HELP'),
                'category' => $customer->i18n_singular_name(),
                'sort'     => 20,
            ],
            self::PERMISSION_EDIT   => [
                'name'     => $customer->fieldLabel(self::PERMISSION_EDIT),
                'help'     => $customer->fieldLabel(self::PERMISSION_EDIT . '_HELP'),
                'category' => $customer->i18n_singular_name(),
                'sort'     => 30,
            ],
            self::PERMISSION_DELETE => [
                'name'     => $customer->fieldLabel(self::PERMISSION_DELETE),
                'help'     => $customer->fieldLabel(self::PERMISSION_DELETE . '_HELP'),
                'category' => $customer->i18n_singular_name(),
                'sort'     => 40,
            ],
        ];
        return $permissions;
    }

    /**
     * Indicates wether the current user can view this object.
     * 
     * @param Member $member declated to be compatible with parent
     *
     * @return bool|null
     */
    public function canView($member = null) : ?bool
    {
        return Permission::checkMember($member, self::PERMISSION_VIEW) ? true : null;
    }
    
    /**
     * Order should not be created via backend
     * 
     * @param Member $member Member to check permission for
     *
     * @return false
     */
    public function canCreate($member = null) : ?bool
    {
        return Permission::checkMember($member, self::PERMISSION_CREATE) ? true : null;
    }

    /**
     * Indicates wether the current user can edit this object.
     * 
     * @param Member $member declated to be compatible with parent
     *
     * @return bool
     */
    public function canEdit($member = null) : ?bool
    {
        return Permission::checkMember($member, self::PERMISSION_EDIT) ? true : null;
    }

    /**
     * Indicates wether the current user can delete this object.
     * 
     * @param Member $member declated to be compatible with parent
     *
     * @return bool
     */
    public function canDelete($member = null) : ?bool
    {
        return Permission::checkMember($member, self::PERMISSION_DELETE) ? true : null;
    }

    /**
     * Indicates wether this user can be deleted automatically.
     *
     * @return bool
     */
    public function canBeDeletedAutomatically() : bool
    {
        $can     = $this->owner->MarkForDeletion
                && strtotime("{$this->owner->MarkForDeletionDate} 00:00:00") < strtotime(date('Y-m-d 00:00:00'))
                && !$this->hasOpenOrders();
        $results = $this->owner->extend('updateCanBeDeletedAutomatically');
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
    public function updateCMSFields(FieldList $fields) : void
    {
        $this->getCMSFieldsIsCalled[$this->owner->ID] = true;
        $fields->insertBefore($fields->dataFieldByName('Salutation'), 'FirstName');
        $fields->dataFieldByName('Salutation')->setSource(Tools::getSalutationMap());
        
        $fields->removeByName('NewsletterOptInStatus');
        $fields->removeByName('NewsletterConfirmationHash');
        $fields->removeByName('RegistrationOptInConfirmationHash');
        $fields->removeByName('ShoppingCartID');
        $fields->removeByName('InvoiceAddressID');
        $fields->removeByName('ShippingAddressID');
        $fields->removeByName('CustomerConfigID');
        $fields->removeByName('MarkForDeletionReasonID');
        
        if ($this->owner->exists()) {
            //make addresses deletable in the grid field
            $addressesGrid = $fields->dataFieldByName('Addresses');
            $addressesConfig = $addressesGrid->getConfig();
            $addressesConfig->removeComponentsByType(GridFieldDeleteAction::class);
            $addressesConfig->addComponent(new GridFieldDeleteAction());
        
            $addresses = $this->owner->Addresses()->map('ID', 'Summary')->toArray();

            $invoiceAddressField  = DropdownField::create('InvoiceAddressID',  $this->owner->fieldLabel('InvoiceAddress'),  $addresses);
            $shippingAddressField = DropdownField::create('ShippingAddressID', $this->owner->fieldLabel('ShippingAddress'), $addresses);
            $fields->insertBefore($invoiceAddressField,  'Locale');
            $fields->insertBefore($shippingAddressField, 'Locale');
            $created = $this->owner->dbObject('Created');
            /* @var $created \SilverStripe\ORM\FieldType\DBDatetime */
            $createdNice = "{$created->Date()}, {$created->Time()}";
            $fields->insertBefore(ReadonlyField::create('CreatedNice', Tools::field_label('DATE'), $createdNice), 'CustomerNumber');
            if (class_exists(HasOneSelector::class)) {
                $cartField = HasOneSelector::create('ShoppingCart', $this->owner->fieldLabel('ShoppingCartID'), $this->owner, ShoppingCart::class)->setLeftTitle($this->owner->fieldLabel('ShoppingCart'));
                $cartField->removeAddable();
                $cartField->removeLinkable();
                $cartField->getConfig()->removeComponentsByType(GridFieldDeleteAction::class);
                $fields->insertBefore($cartField, 'Locale');
            }
            if (!$this->owner->PaymentMethods()->exists()) {
                $fields->removeByName('PaymentMethods');
            }
            if (!$this->owner->HiddenPaymentMethods()->exists()) {
                $fields->removeByName('HiddenPaymentMethods');
            }
            if ($this->getLoginAttempts()->exists()) {
                $attemptField = GridField::create('LoginAttempts', LoginAttempt::singleton()->i18n_plural_name(), $this->getLoginAttempts(), GridFieldConfig_RecordEditor::create());
                $attemptField->getConfig()->removeComponentsByType(GridFieldAddNewButton::class);
                $attemptField->getConfig()->removeComponentsByType(GridFieldEditButton::class);
                $attemptField->getConfig()->removeComponentsByType(GridFieldFilterHeader::class);
                $fields->findOrMakeTab('Root.LoginAttempts', LoginAttempt::singleton()->i18n_plural_name());
                $fields->addFieldToTab('Root.LoginAttempts', $attemptField);
            }
        }
    }

    /**
     * Returns the CMS validator.
     * 
     * @return Member_Validator
     */
    public function getCMSValidator() : Member_Validator
    {
        $validator = Member_Validator::create();
        $validator->setForMember($this->owner);
        $this->owner->extend('updateValidator', $validator);
        return $validator;
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
                    'AllAddresses'                      => _t(Customer::class . '.AllAddresses', 'All addresses'),
                    'MarkForDeletion'                   => _t(Customer::class . '.MarkForDeletion', 'Mark for deletion'),
                    'MarkForDeletionReason'             => _t(Customer::class . '.MarkForDeletionReason', 'Mark for deletion reason'),
                    'Customer'                          => _t(Customer::class . '.Customer', 'Customer'),
                    'Salutation'                        => _t(Customer::class . '.SALUTATION', 'salutation'),
                    'SubscribedToNewsletter'            => _t(Customer::class . '.SUBSCRIBEDTONEWSLETTER', 'subscribed to newsletter'),
                    'HasAcceptedTermsAndConditions'     => _t(Customer::class . '.HASACCEPTEDTERMSANDCONDITIONS', 'has accepted terms and conditions'),
                    'HasAcceptedRevocationInstruction'  => _t(Customer::class . '.HASACCEPTEDREVOCATIONINSTRUCTION', 'has accepted revocation instruction'),
                    'RegistrationOptInConfirmationHash' => _t(Customer::class . '.RegistrationOptInConfirmationHash', 'Registration Opt-In Confirmation Hash'),
                    'RegistrationOptInConfirmed'        => _t(Customer::class . '.RegistrationOptInConfirmed', 'Registration Opt-In Confirmed'),
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
                    'HiddenPaymentMethods'              => _t(Customer::class . '.HiddenPaymentMethods', 'Hidden Payment Methods'),
                    'GroupNames'                        => _t(Customer::class . '.TYPE', 'type'),
                    'AddressCountry'                    => Country::singleton()->singular_name(),
                    'IsBusinessAccount'                 => _t(Customer::class . '.ISBUSINESSACCOUNT', 'Is business account'),
                    'AnonymousCustomer'                 => _t(self::class . '.ANONYMOUSCUSTOMER', 'Anonymous Customer'),
                    
                    'BasicData'                         => _t(Customer::class . '.BASIC_DATA', 'Basics'),
                    'AddressData'                       => _t(Customer::class . '.ADDRESS_DATA', 'Basic address data'),
                    'InvoiceData'                       => _t(Customer::class . '.INVOICE_DATA', 'Invoice address data'),
                    'ShippingData'                      => _t(Customer::class . '.SHIPPING_DATA', 'Shipping address data'),
                    self::PERMISSION_CREATE             => _t(Customer::class . '.' . self::PERMISSION_CREATE, 'Create customer'),
                    self::PERMISSION_CREATE . '_HELP'   => _t(Customer::class . '.' . self::PERMISSION_CREATE . '_HELP', 'Allows an user to create new customers.'),
                    self::PERMISSION_VIEW               => _t(Customer::class . '.' . self::PERMISSION_VIEW, 'View customer'),
                    self::PERMISSION_VIEW . '_HELP'     => _t(Customer::class . '.' . self::PERMISSION_VIEW . '_HELP', 'Allows an user to view any customer (not only the owned one!). The own customer can be viewed without this permission.'),
                    self::PERMISSION_EDIT               => _t(Customer::class . '.' . self::PERMISSION_EDIT, 'Edit customer'),
                    self::PERMISSION_EDIT . '_HELP'     => _t(Customer::class . '.' . self::PERMISSION_EDIT . '_HELP', 'Allows an user to edit any customer (not only the owned one!).'),
                    self::PERMISSION_DELETE             => _t(Customer::class . '.' . self::PERMISSION_DELETE, 'Delete customer'),
                    self::PERMISSION_DELETE . '_HELP'   => _t(Customer::class . '.' . self::PERMISSION_DELETE . '_HELP', 'Allows an user to delete any customer (not only the owned one!).'),
                ]
        );
    }
    
    /**
     * Defines additional searchable fields.
     *
     * @param array &$fields The searchable fields from the decorated object
     * 
     * @return void
     */
    public function updateSearchableFields(array &$fields) : void
    {
        $address = Address::singleton();
        $fields  = array_merge(
                [
                    'CustomerNumber' => [
                        'title'     => $this->owner->fieldLabel('CustomerNumber'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'ID' => [
                        'title'     => $this->owner->fieldLabel('ID'),
                        'filter'    => PartialMatchFilter::class,
                    ],
                ],
                $fields,
                [
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
                    'Addresses.Company' => [
                        'title'     => "{$this->owner->fieldLabel('AllAddresses')}: {$address->fieldLabel('Company')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'Addresses.FirstName' => [
                        'title'     => "{$this->owner->fieldLabel('AllAddresses')}: {$address->fieldLabel('FirstName')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'Addresses.Surname' => [
                        'title'     => "{$this->owner->fieldLabel('AllAddresses')}: {$address->fieldLabel('Surname')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'Addresses.Street' => [
                        'title'     => "{$this->owner->fieldLabel('AllAddresses')}: {$address->fieldLabel('Street')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'Addresses.StreetNumber' => [
                        'title'     => "{$this->owner->fieldLabel('AllAddresses')}: {$address->fieldLabel('StreetNumber')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'Addresses.Postcode' => [
                        'title'     => "{$this->owner->fieldLabel('AllAddresses')}: {$address->fieldLabel('Postcode')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'Addresses.City' => [
                        'title'     => "{$this->owner->fieldLabel('AllAddresses')}: {$address->fieldLabel('City')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'Addresses.CountryID' => [
                        'title'     => "{$this->owner->fieldLabel('AllAddresses')}: {$address->fieldLabel('Country')}",
                        'filter'    => ExactMatchFilter::class,
                        'field'     => DropdownField::create('Addresses.CountryID', $address->fieldLabel('Country'), Country::getPrioritiveDropdownMap(false, '')),
                    ],
                ],
                [
                    'InvoiceAddress.Company' => [
                        'title'     => "{$this->owner->fieldLabel('InvoiceAddress')}: {$address->fieldLabel('Company')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'InvoiceAddress.FirstName' => [
                        'title'     => "{$this->owner->fieldLabel('InvoiceAddress')}: {$address->fieldLabel('FirstName')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'InvoiceAddress.Surname' => [
                        'title'     => "{$this->owner->fieldLabel('InvoiceAddress')}: {$address->fieldLabel('Surname')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'InvoiceAddress.Street' => [
                        'title'     => "{$this->owner->fieldLabel('InvoiceAddress')}: {$address->fieldLabel('Street')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'InvoiceAddress.StreetNumber' => [
                        'title'     => "{$this->owner->fieldLabel('InvoiceAddress')}: {$address->fieldLabel('StreetNumber')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'InvoiceAddress.Postcode' => [
                        'title'     => "{$this->owner->fieldLabel('InvoiceAddress')}: {$address->fieldLabel('Postcode')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'InvoiceAddress.City' => [
                        'title'     => "{$this->owner->fieldLabel('InvoiceAddress')}: {$address->fieldLabel('City')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'InvoiceAddress.CountryID' => [
                        'title'     => "{$this->owner->fieldLabel('InvoiceAddress')}: {$address->fieldLabel('Country')}",
                        'filter'    => ExactMatchFilter::class,
                        'field'     => DropdownField::create('InvoiceAddress.CountryID', $address->fieldLabel('Country'), Country::getPrioritiveDropdownMap(false, '')),
                    ],
                    'ShippingAddress.Company' => [
                        'title'     => "{$this->owner->fieldLabel('ShippingAddress')}: {$address->fieldLabel('Company')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'ShippingAddress.FirstName' => [
                        'title'     => "{$this->owner->fieldLabel('ShippingAddress')}: {$address->fieldLabel('FirstName')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'ShippingAddress.Surname' => [
                        'title'     => "{$this->owner->fieldLabel('ShippingAddress')}: {$address->fieldLabel('Surname')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'ShippingAddress.Street' => [
                        'title'     => "{$this->owner->fieldLabel('ShippingAddress')}: {$address->fieldLabel('Street')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'ShippingAddress.StreetNumber' => [
                        'title'     => "{$this->owner->fieldLabel('ShippingAddress')}: {$address->fieldLabel('StreetNumber')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'ShippingAddress.Postcode' => [
                        'title'     => "{$this->owner->fieldLabel('ShippingAddress')}: {$address->fieldLabel('Postcode')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'ShippingAddress.City' => [
                        'title'     => "{$this->owner->fieldLabel('ShippingAddress')}: {$address->fieldLabel('City')}",
                        'filter'    => PartialMatchFilter::class,
                    ],
                    'ShippingAddress.CountryID' => [
                        'title'     => "{$this->owner->fieldLabel('ShippingAddress')}: {$address->fieldLabel('Country')}",
                        'filter'    => ExactMatchFilter::class,
                        'field'     => DropdownField::create('ShippingAddress.CountryID', $address->fieldLabel('Country'), Country::getPrioritiveDropdownMap(false, '')),
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
     * Returns whether the method self::getCMSFields() is called for the current 
     * customer context.
     * 
     * @param int $customerID Customer ID
     * 
     * @return bool
     */
    public function getCMSFieldsIsCalled(int $customerID) : bool
    {
        return array_key_exists($customerID, $this->getCMSFieldsIsCalled)
            && $this->getCMSFieldsIsCalled[$customerID];
    }
    
    /**
     * Returns the login attempt information.
     * 
     * @return DBHTMLText
     */
    public function getLoginAttemptInformation() : DBHTMLText
    {
        $info = DBHTMLText::create()->setProcessShortcodes(true);
        $page = CustomerDataPage::get()->first();
        if ($page instanceof CustomerDataPage) {
            $info->setValue($page->LoginAttemptContent);
        }
        return $info;
    }
    
    /**
     * Returns the Member's LoginAttempts.
     * 
     * @return DataList
     */
    public function getLoginAttempts() : DataList
    {
        return LoginAttempt::get()
                ->filterAny([
                    'EmailHashed' => sha1($this->owner->Email),
                    'MemberID'    => $this->owner->ID,
                ])
                ->sort('Created', 'DESC');
    }

    /**
     * Returns the customer number.
     * 
     * @return string
     */
    public function getCustomerNumber() : string
    {
        $customerNumber = $this->owner->getField('CustomerNumber');
        $getCMSFieldsIsCalled = $this->getCMSFieldsIsCalled($this->owner->ID);
        $this->owner->extend('updateCustomerNumber', $customerNumber, $getCMSFieldsIsCalled);
        return (string) $customerNumber;
    }

    /**
     * return the orders shipping address as complete string.
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getShippingAddressSummary() {
        return $this->owner->ShippingAddress()->SummaryHTML;
    }

    /**
     * return the orders invoice address as complete string.
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getInvoiceAddressSummary() : DBHTMLText
    {
        return $this->owner->InvoiceAddress()->SummaryHTML;
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
                if ($groupCode == self::GROUP_CODE_ADMINISTRATORS) {
                    unset($groupCodes[$groupID]);
                } elseif ($groupCode == self::GROUP_CODE_ANONYMOUS) {
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
    
    /**
     * Returns the customer's full name with salutation.
     * 
     * @return string
     */
    public function getNameWithSalutation() : string
    {
        $name = '---';
        if ($this->owner->exists()) {
            $name = "{$this->owner->SalutationText} {$this->owner->FirstName} {$this->owner->Surname}";
        }
        $this->owner->extend('updateNameWithSalutation', $name);
        return $name;
    }
    
    /**
     * Returns the customer's full name with salutation.
     * 
     * @return string
     */
    public function getSummaryTitle() : string
    {
        $title = $this->owner->fieldLabel('AnonymousCustomer');
        if ($this->owner->exists()) {
            $title = "{$this->owner->NameWithSalutation} [{$this->owner->CustomerNumber}]";
        }
        $this->owner->extend('updateSummaryTitle', $title);
        return $title;
    }
    
    // ------------------------------------------------------------------------
    // Regular methods
    // ------------------------------------------------------------------------
    
    /**
     * Returns whether the current customer is a registered one.
     * Alias for @see $this->isValidCustomer()
     * 
     * @return bool
     */
    public function isRegisteredCustomer() : bool
    {
        return $this->isValidCustomer();
    }
    
    /**
     * Returns whether the current customer is a anonymous one.
     * 
     * @return bool
     */
    public function isAnonymousCustomer() : bool
    {
        $isAnonymousCustomer = false;
        if ($this->owner->Groups()->find('Code', self::GROUP_CODE_ANONYMOUS)) {
            $isAnonymousCustomer = true;
        }
        return $isAnonymousCustomer;
    }
    
    /**
     * Returns whether the current customer is in the given zone.
     * 
     * @var \SilverCart\Model\Shipment\Zone $zone Zone
     * 
     * @return bool
     */
    public function isInZone(Zone $zone) : bool
    {
        $isInZone = true;
        if ($zone instanceof Zone
         && $zone->exists()
        ) {
            $isInZone        = false;
            $shippingAddress = $this->owner->ShippingAddress();
            $shippingCountry = $shippingAddress->Country();
            if ($shippingCountry->exists()) {
                $matchingZones = Zone::getZonesFor($shippingCountry->ID);
                if ($matchingZones->exists()) {
                    $foundZone = $matchingZones->byID($zone->ID);
                    if ($foundZone instanceof Zone
                     && $foundZone->exists()
                    ) {
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
     */
    public static function createAnonymousCustomer() : Member
    {
        $member = self::currentUser();
        if (!$member) {
            $member = Member::create();
            $member->URLSegment = uniqid('anonymous-');
            $member->write();
            // Add customer to intermediate group
            $customerGroup = Group::get()->filter('Code', self::GROUP_CODE_ANONYMOUS)->first();        
            if ($customerGroup) {
                $member->Groups()->add($customerGroup);
            }
            Security::setCurrentUser($member);
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
     * @return Member|bool
     */
    public static function currentAnonymousCustomer()
    {
        $member = self::currentUser();
        if ($member instanceof Member
         && $member->exists()
         && $member->isAnonymousCustomer()
        ) {
            return $member;
        }
        return false;
    }
    
    /**
     * Returns the default customer group code.
     * 
     * @return string
     */
    public static function default_customer_group_code() : string
    {
        return (string) Member::config()->default_customer_group_code;
    }
    
    /**
     * Returns the default customer group code B2B.
     * 
     * @return string
     */
    public static function default_customer_group_code_b2b() : string
    {
        return (string) Member::config()->default_customer_group_code_b2b;
    }
    
    /**
     * Returns the default B2C group.
     * 
     * @return Group|null
     */
    public static function default_customer_group() : ?Group
    {
        return Group::get()->filter('Code', self::default_customer_group_code())->first();
    }
    
    /**
     * Returns the default B2B group.
     * 
     * @return Group|null
     */
    public static function default_customer_group_b2b() : ?Group
    {
        return Group::get()->filter('Code', self::default_customer_group_code_b2b())->first();
    }
    
    /**
     * Returns whether this customer is a B2B customer.
     * 
     * @return bool
     */
    public function isB2BCustomer() : bool
    {
        $isB2BCustomer = false;
        if ($this->owner->Groups()->find('Code', self::default_customer_group_code_b2b())) {
            $isB2BCustomer = true;
        }
        return $isB2BCustomer;
    }

        /**
     * Returns whether this is a valid customer.
     * 
     * @return bool
     */
    public function isValidCustomer() : bool
    {
        return $this->owner->Groups()->filterAny('Code', (array) Member::config()->valid_customer_group_codes)->exists();
    }

    /**
     * Function similar to Customer::currentUser(); Determins if we deal with a
     * registered customer who has opted in. Returns the member object or
     * false.
     *
     * @return Member|null
     */
    public static function currentRegisteredCustomer() : ?Member
    {
        $member             = self::currentUser();
        $registeredCustomer = null;
        if ($member instanceof Member
         && $member->isValidCustomer()
        ) {
            $registeredCustomer = $member;
        }
        return $registeredCustomer;
    }

    /**
     * Returns the current user.
     * 
     * @return Member|null
     */
    public static function currentUser() : ?Member
    {
        return Security::getCurrentUser();
    }

    /**
     * Returns all customer groups.
     * 
     * @return DataList
     */
    public static function CustomerGroups() : DataList
    {
        return Group::get();
    }

    /**
     * Returns the currently logged in user's ID.
     * 
     * @return int
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.01.2019
     */
    public static function currentUserID() : int
    {
        $memberID = 0;
        $member   = self::currentUser();
        if ($member instanceof Member) {
            $memberID = $member->ID;
        }
        return $memberID;
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
     * Returns this customer's total purchased quantity of the given $product.
     * 
     * @return float
     */
    public function getPurchasedProductQuantity(Product $product) : float
    {
        $orders   = $this->owner->Orders();
        $quantity = 0;
        foreach ($orders as $order) {
            $positions = $order->OrderPositions();
            foreach ($positions as $position) {
                if ($position->Product()->ID === $product->ID) {
                    $quantity += $position->Quantity;
                }
            }
        }
        return $quantity;
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
     */
    public function getCart() : ShoppingCart
    {
        $id = $this->owner->ID;
        if (!array_key_exists($id, self::$shoppingCartList)) {
            $cart = ShoppingCart::getCart();
            if (is_null($cart)
            || (int) $cart->Member()->ID !== (int) $this->owner->ID
            ) {
                $cart = $this->owner->ShoppingCart();
                if (!$cart->exists()) {
                    $cart = ShoppingCart::create();
                    $cart->write();
                    $this->owner->ShoppingCartID = $cart->ID;
                    $this->owner->write();
                }
            }
            self::$shoppingCartList[$id] = $cart;
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
            $customerGroups = Group::get()->filter("Code", self::GROUP_CODE_ANONYMOUS);
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
     * @return bool
     */
    public function hasFinishedNewsletterOptIn() : bool
    {
        $has = (bool) $this->owner->NewsletterOptInStatus;
        $this->owner->extend('updateHasFinishedNewsletterOptIn', $has);
        return $has;
    }
    
    /**
     * Indicates wether the customer has defined only one address to be both
     * invoice and shipping address.
     *
     * @return bool
     */
    public function hasOnlyOneStandardAddress() : bool
    {
        $has = $this->owner->InvoiceAddressID === $this->owner->ShippingAddressID
            && $this->owner->InvoiceAddressID > 0;
        $this->owner->extend('updateHasOnlyOneStandardAddress', $has);
        return $has;
    }
    
    /**
     * Returns whether this customer has open orders.
     * An open order is not sent to SAP yet or has the order status NEW.
     * 
     * @return bool
     */
    public function hasOpenOrders() : bool
    {
        $has = $this->owner->Orders()->filter([
            'OrderStatus.Code' => OrderStatus::STATUS_CODE_NEW,
        ])->exists();
        $this->owner->extend('updateHasOpenOrders', $has);
        return $has;
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
    public function showPricesGross(bool $ignoreTaxExemption = false) : bool
    {
        $pricetype = Config::Pricetype();
        if (!$ignoreTaxExemption
         && $this->doesNotHaveToPayTaxes()
        ) {
            $pricetype = Config::PRICE_TYPE_NET;
        }
        return $pricetype === Config::PRICE_TYPE_GROSS;
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.07.2019
     */
    public function onBeforeWrite() {
        parent::onBeforeWrite();
        if (!self::currentAnonymousCustomer()
         && empty($this->owner->CustomerNumber)
        ) {
            $this->owner->CustomerNumber = NumberRange::useReservedNumberByIdentifier('CustomerNumber');
        }
        if ($this->owner->exists()
         && empty($this->owner->RegistrationOptInConfirmationHash)
        ) {
            $this->owner->RegistrationOptInConfirmationHash = $this->createOptInConfirmationHash();
        }
    }
    
    /**
     * Attributes a shopping cart to the Member if none is attributed yet.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.09.2018
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if ($this->owner->ShoppingCartID === null) {
            $cart = ShoppingCart::create();
            $cart->write();
            $this->owner->ShoppingCartID = $cart->ID;
            $this->owner->write();
        }
        
        // check whether to add a member to an administrative group
        if (Customer::currentUser()
         && Customer::currentUser()->inGroup(self::GROUP_CODE_ADMINISTRATORS)
         && array_key_exists('Groups', $_POST)
        ) {
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
     * Adds a message to inform the customer about the successfull password change
     * while being locked out due to security settings.
     * 
     * @param string           $password Password
     * @param ValidationResult $result   Result
     * 
     * @return void
     */
    public function onAfterChangePassword(string $password, ValidationResult $result) : void
    {
        if (!$result->isValid()
         || $this->skipChangePasswordInfo
        ) {
            $this->skipChangePasswordInfo = false;
            return;
        }
        $ctrl    = ModelAsController::controller_for(Page::singleton());
        $message = _t(Member::class . '.MessageChangePasswordSuccess', 'Congratulations! Your password was changed successfully.');
        $result->addMessage($message);
        $ctrl->setSuccessMessage($message);
        if ($this->owner->isLockedOut()) {
            $message = _t(
                    Member::class . '.MessageChangePasswordLockedOut',
                    'Please be aware that your account still has been temporarily disabled because of too many failed attempts at logging in. Please try again in {count} minutes.',
                    null,
                    ['count' => Member::config()->lock_out_delay_mins]
            );
            $result->addMessage($message);
            $ctrl->setInfoMessage($message);
        }
    }

    /**
     * Returns the minutes left for the LockedOutUntil property.
     * 
     * @return int
     */
    public function LockedOutUntilMinutes() : int
    {
        /** @var DBDatetime $lockedOutUntilObj */
        $lockedOutUntilObj = $this->owner->dbObject('LockedOutUntil');
        $now               = DBDatetime::now()->getTimestamp();
        $time              = $lockedOutUntilObj->getTimestamp();
        $ago               = abs($time - $now);
        return round($ago / 60);
    }

    /**
     * Sets the @see $this->skipChangePasswordInfo to true.
     * 
     * @return Member
     */
    public function skipChangePasswordInfo() : Member
    {
        $this->skipChangePasswordInfo = true;
        return $this->owner;
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
            'currentShippingCountry' => 'currentShippingCountry',
            'CurrentShippingCountry' => 'currentShippingCountry',
            'CustomerGroups'  => 'CustomerGroups',
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
        $variables['Member']            = $member;
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
                $variables,
                [],
                $member->Locale
        );
    }
    
    /**
     * Sends the deletion confirmation/notification to the customer and shop owner.
     * 
     * @return void
     */
    public function sendDeletionConfirmation() : void
    {
        $member    = $this->owner;
        $variables = ['Member' => $member];
        ShopEmail::send(
                'CustomerDeletionConfirmation',
                $member->Email,
                $variables,
                [],
                $member->Locale
        );
        ShopEmail::send(
                'CustomerDeletionNotification',
                Config::DefaultMailRegistrationRecipient(),
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
    
    /**
     * Creates a registration opt-in confirmation hash.
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2019
     */
    public function createOptInConfirmationHash() : string
    {
        $time     = time();
        $rand     = rand();
        $hashBase = "{$time}-{$rand}-{$this->owner->ID}";
        $hash     = md5($hashBase) . sha1($hashBase) . uniqid();
        return $hash;
    }
    
    /**
     * Returns the registration opt-in confirmation link.
     * 
     * @return string
     */
    public function getRegistrationOptInConfirmationLink() : string
    {
        return Director::absoluteURL(self::getRegistrationOptInConfirmationBaseLink()) . DIRECTORY_SEPARATOR . urlencode($this->owner->RegistrationOptInConfirmationHash);
    }
    
    /**
     * Sends the registration opt in email.
     * 
     * @return void
     */
    public function sendRegistrationOptInEmail() : void
    {
        if (!$this->owner->RegistrationOptInConfirmed
         && !empty($this->owner->Email)
        ) {
            if (empty($this->owner->RegistrationOptInConfirmationHash)) {
                $this->owner->RegistrationOptInConfirmationHash = $this->createOptInConfirmationHash();
                $this->owner->write();
            }
            ShopEmail::send('RegistrationOptIn', $this->owner->Email, ['Customer' => $this->owner], [], $this->owner->Locale);
        }
    }
    
    /**
     * Confirms the registration opt-in by the given $hash.
     * 
     * @param string $hash Hash to confirm
     * 
     * @return bool
     */
    public function confirmRegistrationOptIn(string $hash) : bool
    {
        $confirmed = false;
        if ($this->owner->RegistrationOptInConfirmed) {
            $confirmed = true;
        } elseif ($hash === $this->owner->RegistrationOptInConfirmationHash) {
            $confirmed = true;
            $this->owner->RegistrationOptInConfirmed = true;
            $this->owner->write();
            $customFields          = ['Customer' => $this->owner];
            $confirmationRecipient = $this->owner->Email;
            $confirmationLocale    = $this->owner->Locale;
            $notificationRecipient = Config::DefaultMailRegistrationRecipient();
            $notificationLocale    = Tools::default_locale()->getLocale();
            $this->owner->extend('onBeforeSendRegistrationOptInConfirmation', $confirmationRecipient, $customFields, $confirmationLocale);
            ShopEmail::send('RegistrationOptInConfirmation', $confirmationRecipient, $customFields, [], $confirmationLocale);
            $this->owner->extend('onBeforeSendRegistrationOptInNotification', $notificationRecipient, $customFields, $notificationLocale);
            ShopEmail::send('RegistrationOptInNotification', $notificationRecipient, $customFields, [], $notificationLocale);
        }
        return $confirmed;
    }
    
    /**
     * Moves the shopping cart of $this->owner to $customer.
     * 
     * @param Member $customer Target customer
     * 
     * @return void
     */
    public function moveShoppingCartTo(Member $customer) : void
    {
        $ownerCart      = $this->owner->getCart();
        $ownerPositions = $ownerCart->ShoppingCartPositions();
        $customerCart   = $customer->getCart();
        /** @var ShoppingCart $customerCart */
        if ($ownerPositions->exists()) {
            //delete registered customers cart positions
            $customerPositions = $customerCart->ShoppingCartPositions();
            if ($customerPositions->exists()) {
                foreach ($customerPositions as $customerPosition) {
                    /** @var ShoppingCartPosition $customerPosition */
                    $customerPosition->delete();
                }
            }
            //add anonymous positions to the registered user
            foreach ($ownerPositions as $ownerPosition) {
                /** @var ShoppingCartPosition $ownerPosition */
                $customerPositions->add($ownerPosition);
            }
        }
        if ($ownerCart->DataValues()->exists()) {
            foreach ($ownerCart->DataValues() as $dataValue) {
                /** @var DataValue $dataValue */
                $dataValue->ShoppingCartID = $customerCart->ID;
                $dataValue->write();
            }
        }
        $this->owner->extend('updateMoveShoppingCartTo', $customer);
    }
}