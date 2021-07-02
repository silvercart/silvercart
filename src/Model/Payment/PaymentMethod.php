<?php

namespace SilverCart\Model\Payment;

use Locale;
use ReflectionClass;
use SilverCart\Admin\Dev\Install\RequireDefaultRecords;
use SilverCart\Admin\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverCart\Admin\Forms\ImageUploadField;
use SilverCart\Admin\Model\Config;
use SilverCart\Extensions\Assets\ImageExtension;
use SilverCart\Dev\Tools;
use SilverCart\Forms\Checkout\CheckoutChoosePaymentMethodForm;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Order\OrderStatus;
use SilverCart\Model\Order\ShoppingCart;
use SilverCart\Model\Pages\CheckoutStepController;
use SilverCart\Model\Payment\HandlingCost;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Payment\PaymentMethodTranslation;
use SilverCart\Model\Product\Image;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\Tax;
use SilverCart\Model\Shipment\Zone;
use SilverCart\Model\Shipment\ShippingMethod;
use SilverCart\ORM\DataObjectExtension;
use SilverCart\Model\Translation\TranslationTools;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\Image as SilverStripeImage;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\RequestHandler;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBMoney;
use SilverStripe\ORM\Filters\ExactMatchFilter;
use SilverStripe\ORM\Filters\GreaterThanFilter;
use SilverStripe\ORM\Filters\LessThanFilter;
use SilverStripe\Security\Group;
use SilverStripe\Security\Member;

/**
 * Base class for payment.
 * Every payment module must extend this class.
 *
 * @package SilverCart
 * @subpackage Model\Payment
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 07.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property bool   $isActive                            Is active
 * @property float  $minAmountForActivation              Min amount for activation
 * @property float  $maxAmountForActivation              Max amount for activation
 * @property string $mode                                Mode
 * @property bool   $showPaymentLogos                    Show payment logos
 * @property int    $orderRestrictionMinQuantity         Max amount for activation
 * @property bool   $enableActivationByOrderRestrictions Enable activation by order restrictions
 * @property bool   $ShowFormFieldsOnPaymentSelection    Show form fields on payment selection
 * @property string $sumModificationImpact               Sum modification impact
 * @property string $sumModificationImpactType           Sum modification impact type
 * @property float  $sumModificationValue                Sum modification value
 * @property string $sumModificationValueType            Sum modification value type
 * @property string $sumModificationLabel                Sum modification label
 * @property string $sumModificationProductNumber        Sum modification product number
 * @property bool   $useSumModification                  Use sum modification
 * 
 * @method PaymentStatus PaymentStatus() Returns the related PaymentStatus.
 * @method Zone          Zone()          Returns the related Zone.
 * 
 * @method \SilverStripe\ORM\HasManyList HandlingCosts() Returns the related HandlingCosts.
 * @method \SilverStripe\ORM\HasManyList Orders()        Returns the related Orders.
 * @method \SilverStripe\ORM\HasManyList PaymentLogos()  Returns the related PaymentLogos.
 * 
 * @method \SilverStripe\ORM\ManyManyList ShippingMethods()        Returns the related ShippingMethods.
 * @method \SilverStripe\ORM\ManyManyList ShowOnlyForGroups()      Returns the related Groups to include to this payment method.
 * @method \SilverStripe\ORM\ManyManyList ShowNotForGroups()       Returns the related Groups to exclude from this payment method.
 * @method \SilverStripe\ORM\ManyManyList ShowOnlyForUsers()       Returns the related Members to include to this payment method.
 * @method \SilverStripe\ORM\ManyManyList ShowNotForUsers()        Returns the related Members to exclude from this payment method.
 * @method \SilverStripe\ORM\ManyManyList OrderRestrictionStatus() Returns the related OrderStatus list to restrict this payment method to.
 * @method \SilverStripe\ORM\ManyManyList Countries()              Returns the related Countries to restrict this payment method to.
 * 
 */
class PaymentMethod extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    /**
     * Defines the attributes of the class
     *
     * @var array
     */
    private static $db = [
        'isActive'                              => 'Boolean',
        'minAmountForActivation'                => 'Float',
        'maxAmountForActivation'                => 'Float',
        'mode'                                  => "Enum('Live,Dev','Dev')",
        'showPaymentLogos'                      => 'Boolean',
        'orderRestrictionMinQuantity'           => 'Int',
        'enableActivationByOrderRestrictions'   => 'Boolean',
        'ShowFormFieldsOnPaymentSelection'      => 'Boolean',
        'sumModificationImpact'                 => "Enum('productValue,totalValue','productValue')",
        'sumModificationImpactType'             => "Enum('charge,discount','charge')",
        'sumModificationValue'                  => 'Float',
        'sumModificationValueType'              => "Enum('absolute,percent','absolute')",
        'sumModificationLabel'                  => 'Varchar(255)',
        'sumModificationProductNumber'          => 'Varchar(255)',
        'useSumModification'                    => 'Boolean(0)'
    ];
    /**
     * Defines 1:1 relations
     *
     * @var array
     */
    private static $has_one = [
        'PaymentStatus' => PaymentStatus::class,
        'Zone'          => Zone::class,
    ];
    /**
     * Defines 1:n relations
     *
     * @var array
     */
    private static $has_many = [
        'HandlingCosts' => HandlingCost::class,
        'Orders'        => Order::class,
        'PaymentLogos'  => Image::class,
    ];
    /**
     * Defines n:m relations
     *
     * @var array
     */
    private static $many_many = [
        'ShippingMethods'        => ShippingMethod::class,
        'ShowOnlyForGroups'      => Group::class,
        'ShowNotForGroups'       => Group::class,
        'ShowOnlyForUsers'       => Member::class,
        'ShowNotForUsers'        => Member::class,
        'OrderRestrictionStatus' => OrderStatus::class,
    ];
    /**
     * Defines m:n relations
     *
     * @var array
     */
    private static $belongs_many_many = [
        'Countries' => Country::class,
    ];
    /**
     * Virtual database columns.
     *
     * @var array
     */
    private static $casting = [
        'AttributedCountries'       => 'Varchar(255)',
        'AttributedZones'           => 'Varchar(255)',
        'activatedStatus'           => 'Varchar(255)',
        'Name'                      => 'Varchar(150)',
        'paymentDescription'        => 'Text',
        'LongPaymentDescription'    => 'Text',
    ];
    /**
     * Default values for new PaymentMethods
     *
     * @var array
     */
    private static $defaults = [
        'showPaymentLogos'                 => true,
        'ShowFormFieldsOnPaymentSelection' => false,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartPaymentMethod';
    /**
     * Grant API access on this item.
     *
     * @var bool
     */
    private static $api_access = true;
    /**
     * The link to direct after cancelling by user or session expiry.
     *
     * @var string
     */
    protected $cancelLink = '';
    /**
     * The link to redirect back into shop after payment.
     *
     * @var string
     */
    protected $returnLink = '';
    /**
     * The link to notify shop after payment (push).
     *
     * @var string
     */
    protected $notificationLink = '';
    /**
     * Indicates whether an error occured or not.
     *
     * @var bool
     */
    protected $errorOccured;
    /**
     * A list of errors.
     *
     * @var array
     */
    protected $errorList = [];
    /**
     * A list of possible payment channels.
     *
     * @var array
     */
    private static $possible_payment_channels = [];
    /**
     * Contains the module name for display in the admin backend
     *
     * @var string
     */
    protected $moduleName = '';
    /**
     * Contains a referer to the order object
     *
     * @var Controller
     */
    protected $controller;
    /**
     * Details of customer
     *
     * @var Member
     */
    protected $customerDetails = null;
    /**
     * Invoice address
     *
     * @var Address
     */
    protected $invoiceAddress = null;
    /**
     * Shipping address
     *
     * @var Address
     */
    protected $shippingAddress = null;
    /**
     * Shopping cart
     *
     * @var ShoppingCart
     */
    protected $shoppingCart = null;
    /**
     * Order
     *
     * @var Order
     */
    protected $order = null;
    /**
     * ID of the check out form to render additional form fields
     *
     * @var string
     */
    protected $formID = '';
    /**
     * Path to the uploads folder
     *
     * @var string
     */
    protected $uploadsFolder = '';
    /**
     * Marker to check whether the CMS fields are called or not
     *
     * @var bool 
     */
    protected $getCMSFieldsIsCalled = false;
    
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
     * getter for the multilingual attribute name
     *
     * @return string 
     */
    public function getName() : string
    {
        $name = '';     
        if ($this->isExtendingPaymentMethod()
         && $this->hasMethod('getTranslationFieldValue')
        ) {
            $name = $this->getTranslationFieldValue('Name');
        }
        return (string) $name;
    }
    
    /**
     * getter for the multilingual attribute paymentDescription
     *
     * @return string 
     */
    public function getpaymentDescription() : string
    {
        $paymentDescription = '';
        if ($this->isExtendingPaymentMethod()
         && $this->hasMethod('getTranslationFieldValue')
        ) {
            $paymentDescription = $this->getTranslationFieldValue('paymentDescription');
        }
        return (string) $paymentDescription;
    }
    
    /**
     * getter for the multilingual attribute LongPaymentDescription
     *
     * @return string 
     */
    public function getLongPaymentDescription() : string
    {
        $LongPaymentDescription = '';
        if ($this->isExtendingPaymentMethod()
         && $this->hasMethod('getTranslationFieldValue')
        ) {
            $LongPaymentDescription = $this->getTranslationFieldValue('LongPaymentDescription');
        }
        return (string) $LongPaymentDescription;
    }
    
    // ------------------------------------------------------------------------
    // Methods
    // ------------------------------------------------------------------------
    
    /**
     * Searchable fields
     *
     * @return array
     */
    public function searchableFields() : array
    {
        $searchableFields = [
            'isActive' => [
                'title'  => $this->fieldLabel('isActive'),
                'filter' => ExactMatchFilter::class,
            ],
            'minAmountForActivation' => [
                'title'  => $this->fieldLabel('MinAmountForActivation'),
                'filter' => GreaterThanFilter::class,
            ],
            'maxAmountForActivation' => [
                'title'  => $this->fieldLabel('MaxAmountForActivation'),
                'filter' => LessThanFilter::class,
            ],
            'Countries.ID' => [
                'title'  => $this->fieldLabel('AttributedCountries'),
                'filter' => ExactMatchFilter::class
            ],
        ];
        $this->extend('updateSearchableFields', $searchableFields);
        return $searchableFields;
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        $this->beforeUpdateFieldLabels(function (&$labels) {
            $labels = array_merge(
                    $labels,
                    Tools::field_labels_for(self::class),
                    [
                        'Title'                             => Product::singleton()->fieldLabel('Title'),
                        'Name'                              => _t(PaymentMethod::class . '.NAME', 'Name'),
                        'isActive'                          => _t(PaymentMethod::class . '.PAYMENT_ISACTIVE', 'Activated'),
                        'activatedStatus'                   => _t(PaymentMethod::class . '.PAYMENT_ISACTIVE', 'Activated'),
                        'AttributedZones'                   => Country::singleton()->fieldLabel('AttributedZones'),
                        'AttributedCountries'               => _t(PaymentMethod::class . '.ATTRIBUTED_COUNTRIES', 'Attributed countries'),
                        'minAmountForActivation'            => _t(PaymentMethod::class . '.FROM_PURCHASE_VALUE', 'from purchase value'),
                        'maxAmountForActivation'            => _t(PaymentMethod::class . '.TILL_PURCHASE_VALUE', 'till purchase value'),
                        'ShowFormFieldsOnPaymentSelection'  => _t(PaymentMethod::class . '.SHOW_FORM_FIELDS_ON_PAYMENT_SELECTION', 'Show form fields on payment selection'),
                        'PaymentMethodTranslations'         => PaymentMethodTranslation::singleton()->plural_name(),
                        'ShippingMethods'                   => ShippingMethod::singleton()->plural_name(),
                        'Countries'                         => Country::singleton()->plural_name(),
                        'PaymentDescription'                => _t(PaymentMethod::class . '.PAYMENT_DESCRIPTION', 'Description'),
                        'LongPaymentDescription'            => _t(PaymentMethod::class . '.LONG_PAYMENT_DESCRIPTION', 'Description to display on payment method page'),
                        'HandlingCosts'                     => HandlingCost::singleton()->plural_name(),
                        'PaymentLogos'                      => _t(PaymentMethod::class . '.PAYMENT_LOGOS', 'Logos'),
                        'OrderStatus'                       => OrderStatus::singleton()->plural_name(),
                        'ShowOnlyForGroups'                 => _t(PaymentMethod::class . '.SHOW_ONLY_FOR_GROUPS_LABEL', 'Deactivate for the following groups'),
                        'ShowNotForGroups'                  => _t(PaymentMethod::class . '.SHOW_NOT_FOR_GROUPS_LABEL', 'Activate for the following groups'),
                        'ShowNotForUsers'                   => _t(PaymentMethod::class . '.SHOW_NOT_FOR_USERS_LABEL', 'Activate for the following users'),
                        'ShowOnlyForUsers'                  => _t(PaymentMethod::class . '.SHOW_ONLY_FOR_USERS_LABEL', 'Deactivate for the following users'),
                        'AddPaymentLogos'                   => _t(PaymentMethod::class . '.AddPaymentLogos', 'Add Logo'),
                        'modeLive'                          => _t(PaymentMethod::class . '.PAYMENT_MODE_LIVE', 'Live'),
                        'modeDev'                           => _t(PaymentMethod::class . '.PAYMENT_MODE_DEV', 'Dev'),
                        'SumModifiers'                      => _t(PaymentMethod::class . '.PAYMENT_SUMMODIFIERS', 'Charges/Discounts'),
                        'sumModificationImpact'             => _t(PaymentMethod::class . '.PAYMENT_SUMMODIFICATIONIMPACT', 'Discount'),
                        'sumModificationImpactType'         => _t(PaymentMethod::class . '.PAYMENT_SUMMODIFICATIONIMPACTTYPE', 'Type'),
                        'sumModificationValue'              => _t(PaymentMethod::class . '.PAYMENT_SUMMODIFICATIONVALUE', 'Value'),
                        'sumModificationValueType'          => _t(PaymentMethod::class . '.PAYMENT_SUMMODIFICATIONIMPACTVALUETYPE', 'The value is'),
                        'sumModificationLabel'              => _t(PaymentMethod::class . '.PAYMENT_SUMMODIFICATIONLABELFIELD', 'Label for shopping cart/order'),
                        'sumModificationProductNumber'      => _t(PaymentMethod::class . '.PAYMENT_SUMMODIFICATIONPRODUCTNUMBERFIELD', 'Product number to use for XML order export'),
                        'useSumModification'                => _t(PaymentMethod::class . '.PAYMENT_USE_SUMMODIFICATION', 'Activate'),
                        'MaxAmountForActivation'            => _t(PaymentMethod::class . '.PAYMENT_MAXAMOUNTFORACTIVATION', 'Maximum amount'),
                        'MinAmountForActivation'            => _t(PaymentMethod::class . '.PAYMENT_MINAMOUNTFORACTIVATION', 'Minimum amount'),
                        'Translations'                      => _t(Config::class . '.TRANSLATIONS', 'Translations'),
                        'ModifyProductValue'                => _t(PaymentMethod::class . '.PAYMENT_MODIFY_PRODUCTVALUE', 'Product value'),
                        'ModifyTotalValue'                  => _t(PaymentMethod::class . '.PAYMENT_MODIFY_TOTALVALUE', 'Total value'),
                        'ModifyCharge'                      => _t(PaymentMethod::class . '.PAYMENT_MODIFY_TYPE_CHARGE', 'Charge'),
                        'ModifyDiscount'                    => _t(PaymentMethod::class . '.PAYMENT_MODIFY_TYPE_DISCOUNT', 'Discount'),
                        'ModifyAbsolute'                    => _t(PaymentMethod::class . '.PAYMENT_IMPACT_TYPE_ABSOLUTE', 'Absolute'),
                        'ModifyPercent'                     => _t(PaymentMethod::class . '.PAYMENT_IMPACT_TYPE_PERCENT', 'In percent'),
                        'ActivationByOrderRestrictions'     => _t(PaymentMethod::class . '.ENABLE_RESTRICTION_BY_ORDER_LABEL', 'Use the following rule'),
                        'AccessManagementBasic'             => _t(PaymentMethod::class . '.ACCESS_MANAGEMENT_BASIC_LABEL', 'General'),
                        'AccessManagementGroup'             => _t(PaymentMethod::class . '.ACCESS_MANAGEMENT_GROUP_LABEL', 'By group(s)'),
                        'AccessManagementUser'              => _t(PaymentMethod::class . '.ACCESS_MANAGEMENT_USER_LABEL', 'By user(s)'),
                        'AccessSettings'                    => _t(PaymentMethod::class . '.ACCESS_SETTINGS', 'Access management'),
                        'BasicSettings'                     => _t(PaymentMethod::class . '.BASIC_SETTINGS', 'Basic settings'),
                        'HandlingCosts'                     => _t(PaymentMethod::class . '.HANDLINGCOSTS_SETTINGS', 'Handling costs'),
                        'Mode'                              => _t(PaymentMethod::class . '.MODE', 'Mode'),
                        'RestrictionByOrderQuantityLabel'   => _t(PaymentMethod::class . '.RESTRICT_BY_ORDER_QUANTITY', 'The customer must have completed the following number of orders'),
                        'RestrictionByOrderStatusLabel'     => _t(PaymentMethod::class . '.RESTRICT_BY_ORDER_STATUS', 'whose order status is marked in the following list'),
                        'RestrictionLabel'                  => _t(PaymentMethod::class . '.RESTRICTION_LABEL', 'Activate only, when the following criteria are met'),
                        'ShippingMethodsDesc'               => _t(PaymentMethod::class . '.SHIPPINGMETHOD_DESC', 'Bind the payment method to the following shipping methods:'),
                        'ShowPaymentLogos'                  => _t(PaymentMethod::class . '.SHOW_PAYMENT_LOGOS', 'Show logos in frontend'),
                    ]
            );
        });
        return parent::fieldLabels($includerelations);
    }

    /**
     * i18n for summary fields
     *
     * @return array
     */
    public function summaryFields() : array
    {
        return [
            'Name'                   => $this->fieldLabel('Name'),
            'activatedStatus'        => $this->fieldLabel('activatedStatus'),
            'AttributedCountries'    => $this->fieldLabel('AttributedCountries'),
            'minAmountForActivation' => $this->fieldLabel('MinAmountForActivation'),
            'maxAmountForActivation' => $this->fieldLabel('MaxAmountForActivation'),
        ];
    }
    
    /**
     * Returns the payment module name.
     * 
     * @return string
     */
    public function getModuleName() : string
    {
        return (string) $this->moduleName;
    }

    /**
     * Returns the title of the payment method
     *
     * @return string
     */
    public function getTitle() : string
    {
        return (string) $this->Name;
    }

    /**
     * Returns the path to the payment methods logo
     *
     * @return string
     */
    public function getLogo()
    {
        
    }

    /**
     * Returns the link for cancel action or end of session
     *
     * @return string
     */
    public function getCancelLink()
    {
        return $this->cancelLink;
    }

    /**
     * Returns the link to get back in the shop
     *
     * @return string
     */
    public function getReturnLink()
    {
        return $this->returnLink;
    }

    /**
     * Returns the link to notify the shop (push)
     *
     * @return string
     */
    public function getNotificationLink()
    {
        if (empty($this->notificationLink)) {
            $this->setNotificationLink(Director::absoluteUrl(Tools::PageByIdentifierCode('SilvercartPaymentNotification')->Link("process/{$this->moduleName}/{$this->ID}")));
        }
        return $this->notificationLink;
    }

    /**
     * Returns handling costs for this payment method
     *
     * @return Money a money object
     */
    public function getHandlingCost()
    {
        $controller         = Controller::curr();
        $member             = Customer::currentRegisteredCustomer();
        $handlingCostToUse  = false;

        if (method_exists($controller, 'getAddress')) {
            // 1) Use shipping address from checkout
            $shippingAddress = $controller->getAddress('ShippingAddress');
        } elseif ($member
               && $member->ShippingAddressID > 0
        ) {
            // 2) Use customer's default shipping address
            $shippingAddress = $member->ShippingAddress();
        } else {
            // 3) Generate shipping address with shop's default country
            $shippingAddress = Address::create();
            $shippingAddress->Country = Country::get()->filter('ISO2', strtoupper(Locale::getRegion(i18n::get_locale())))->first();
        }

        if (!$shippingAddress) {
            return false;
        }

        $zonesDefined = false;

        foreach ($this->HandlingCosts() as $handlingCost) {
            $zone = $handlingCost->Zone();

            if ($zone->Countries()->find('ISO3', $shippingAddress->Country()->ISO3)) {
                $handlingCostToUse = $handlingCost;
                $zonesDefined = true;
                break;
            }
        }

        // Fallback if HandlingCosts are available but no zone is defined
        if (!$zonesDefined) {
            if ($this->HandlingCosts()->Count() > 0) {
                $handlingCostToUse = $this->HandlingCosts()->First();
            } else {
                $tax                              = Tax::get()->filter('isDefault', 1)->first();
                $handlingCostToUse                = HandlingCost::create();
                $handlingCostToUse->PaymentMethod = $this;
                $handlingCostToUse->Tax           = $tax;
                $handlingCostToUse->TaxID         = $tax->ID;
                $handlingCostToUse->amount        = DBMoney::create();
                $handlingCostToUse->amount->setAmount(0);
                $handlingCostToUse->amount->setCurrency(Config::DefaultCurrency());
            }
        }

        return $handlingCostToUse;
    }
    
    /**
     * Returns the charges and discounts for the product values for this 
     * payment method.
     * 
     * @param ShoppingCart $shoppingCart The shopping cart object
     * @param string       $priceType    'gross' or 'net'
     *
     * @return DBMoney
     */
    public function getChargesAndDiscountsForProducts(ShoppingCart $shoppingCart, string $priceType = null) : DBMoney
    {
        $handlingCosts = DBMoney::create();
        $handlingCosts->setAmount(0);
        $handlingCosts->setCurrency(Config::DefaultCurrency());
        if ($priceType === null) {
            $priceType = Config::PriceType();
        }
        if ($this->useSumModification
         && $this->sumModificationImpact === 'productValue'
        ) {
            $excludedPositions  = [];
            $shoppingCartAmount = $shoppingCart->getAmountTotalWithoutFees([], false, true);
            switch ($this->sumModificationValueType) {
                case 'percent':
                    $modificationValue = $shoppingCartAmount->getAmount() / 100 * $this->sumModificationValue;
                    $index = 1;
                    foreach ($shoppingCart->ShoppingCartPositions() as $position) {
                        if ($position->ProductID > 0
                         && $position->Product() instanceof Product
                         && $position->Product()->ExcludeFromPaymentDiscounts
                        ) {
                            $modificationValue -= $position->getPrice()->getAmount() / 100 * $this->sumModificationValue;
                            $excludedPositions[] = $index;
                        }
                        $index++;
                    }
                    $this->sumModificationLabel .= ' (' . _t(PaymentMethod::class . '.ChargeOrDiscountForAmount',
                            'on {price}',
                            [
                                'price' => $shoppingCartAmount->Nice()
                            ]
                    ) . ')';
                    break;
                case 'absolute':
                default:
                    $modificationValue = $this->sumModificationValue;
            }
            if (count($excludedPositions) > 0) {
                if (count($excludedPositions) == 1) {
                    $this->sumModificationLabel .= ' (' . _t(PaymentMethod::class . '.ExcludedPosition',
                            'position {position} is excluded',
                            [
                                'position' => implode(', ', $excludedPositions)
                            ]
                    ) . ')';
                } else {
                    $this->sumModificationLabel .= ' (' . _t(PaymentMethod::class . '.ExcludedPositions',
                            'the positions {positions} are excluded',
                            [
                                'positions' => implode(', ', $excludedPositions)
                            ]
                    ) . ')';
                }
            }
            if ($this->sumModificationImpactType == 'charge') {
                $handlingCostAmount = $modificationValue;
            } else {
                $handlingCostAmount = "-{$modificationValue}";
            }
            if (Config::PriceType() === Config::PRICE_TYPE_GROSS) {
                $shoppingCartTotal = $shoppingCart->getAmountTotalGrossWithoutFees([], false, true);
            } else {
                $shoppingCartTotal = $shoppingCart->getAmountTotalNetWithoutFees([], false, true);
            }
            if ($handlingCostAmount < 0
             && $shoppingCartTotal->getAmount() < ($handlingCostAmount * -1)
            ) {
                if ($shoppingCartTotal->getAmount == 0.0) {
                    $handlingCostAmount = 0.0;
                } else {
                    $handlingCostAmount = ($shoppingCartTotal->getAmount() * -1);
                }
            }
            if (Config::PriceType() === Config::PRICE_TYPE_NET) {
                $taxRate = $shoppingCart->getMostValuableTaxRate();
                if ($taxRate instanceof Tax
                 && $taxRate->exists()
                ) {
                    $handlingCostAmount = round($handlingCostAmount / (100 + $taxRate->Rate) * 100, 4);
                }
            }
            $handlingCosts->setAmount(round($handlingCostAmount, 2));
        }
        
        $this->extend('updateChargesAndDiscountsForProducts', $handlingCosts);
        return $handlingCosts;
    }
    
    /**
     * Returns the charges and discounts for the shopping cart total for
     * this payment method.
     * 
     * @param ShoppingCart $shoppingCart The shopping cart object
     * @param string       $priceType    'gross' or 'net'
     *
     * @return mixed boolean|DataObject
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2013
     */
    public function getChargesAndDiscountsForTotal(ShoppingCart $shoppingCart, $priceType = false)
    {
        $handlingCosts = DBMoney::create();
        $handlingCosts->setAmount(0);
        $handlingCosts->setCurrency(Config::DefaultCurrency());

        if ($priceType === false) {
            $priceType = Config::PriceType();
        }

        if ($this->useSumModification
         && $this->sumModificationImpact == 'totalValue'
        ) {
            $excludedPositions = [];
            
            switch ($this->sumModificationValueType) {
                case 'percent':
                    $amount            = $shoppingCart->getAmountTotal([], false, true);
                    $modificationValue = $amount->getAmount() / 100 * $this->sumModificationValue;
                    $index = 1;
                    foreach ($shoppingCart->ShoppingCartPositions() as $position) {
                        if ($position->ProductID > 0
                         && $position->Product() instanceof Product
                         && $position->Product()->ExcludeFromPaymentDiscounts
                        ) {
                            $modificationValue -= $position->getPrice()->getAmount() / 100 * $this->sumModificationValue;
                            $excludedPositions[] = $index;
                        }
                        $index++;
                    }
                    break;
                case 'absolute':
                default:
                    $modificationValue = $this->sumModificationValue;
            }
            
            if (count($excludedPositions) > 0) {
                if (count($excludedPositions) == 1) {
                    $this->sumModificationLabel .= ' (' . _t(PaymentMethod::class . '.ExcludedPosition',
                            'position {position} is excluded',
                            [
                                'position' => implode(', ', $excludedPositions)
                            ]
                    ) . ')';
                } else {
                    $this->sumModificationLabel .= ' (' . _t(PaymentMethod::class . '.ExcludedPositions',
                            'the positions {positions} are excluded',
                            [
                                'positions' => implode(', ', $excludedPositions)
                            ]
                    ) . ')';
                }
            }
            
            if ($this->sumModificationImpactType == 'charge') {
                $handlingCostAmount = $modificationValue;
            } else {
                $handlingCostAmount = "-{$modificationValue}";
            }

            if (Config::PriceType() === Config::PRICE_TYPE_GROSS) {
                $shoppingCartTotal = $shoppingCart->getAmountTotal([], false, true);
            } else {
                $shoppingCartTotal  = $shoppingCart->getAmountTotalNetWithoutVat([], false, true);
                $taxRate            = $shoppingCart->getMostValuableTaxRate();
                if ($taxRate instanceof Tax) {
                    $handlingCostAmount = round($handlingCostAmount / (100 + $taxRate->Rate) * 100, 4);
                }
            }

            if ($handlingCostAmount < 0
             && $shoppingCartTotal->getAmount() < ($handlingCostAmount * -1)
            ) {
                $handlingCostAmount = ($shoppingCartTotal->getAmount() * -1);
            }
            $handlingCosts->setAmount($handlingCostAmount);
        }
        
        $this->extend('updateChargesAndDiscountsForTotal', $handlingCosts);
        if ($handlingCosts->getAmount() == 0) {
            $handlingCosts = false;
        }
        return $handlingCosts;
    }

    /**
     * Retunrns a path to a picture with additional information for this payment method
     *
     * @return int
     */
    public function getDescriptionImage()
    {
        
    }

    /**
     * Returns if an error has occured
     *
     * @return bool
     */
    public function getErrorOccured()
    {
        return count($this->getErrorList()) > 0;
    }

    /**
     * Returns a ArrayList with errors
     *
     * @return ArrayList
     */
    public function getErrorList()
    {
        $errorList = [];
        $errorIdx  = 0;

        foreach ($this->errorList as $error) {
            $errorList['error' . $errorIdx] = [
                'error' => $error
            ];
            $errorIdx++;
        }

        return ArrayList::create($errorList);
    }
    
    /**
     * Returns active payment methods.
     * 
     * @return DataList
     */
    public static function getActivePaymentMethods()
    {
        return PaymentMethod::get()->filter('isActive', 1);
    }
    
    /**
     * Returns allowed payment methods.
     * 
     * @param Country      $shippingCountry                  The Country to check the payment methods for.
     * @param ShoppingCart $shoppingCart                     The shopping cart object
     * @param Boolean      $forceAnonymousCustomerIfNotExist When true, an anonymous customer will be created when no customer exists
     * 
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.11.2014
     */
    public static function getAllowedPaymentMethodsFor($shippingCountry, $shoppingCart, $forceAnonymousCustomerIfNotExist = false)
    {
        if (!$shippingCountry) {
            return ArrayList::create();
        }
        $paymentMethod          = self::singleton();
        $allowedPaymentMethods  = [];
        $paymentMethods         = $shippingCountry->PaymentMethods()->filter('isActive', true);
        $member                 = Customer::currentUser();
        $paymentMethod->extend('onBeforeGetAllowedPaymentMethodsFor', $allowedPaymentMethods, $paymentMethods, $shippingCountry, $shoppingCart, $forceAnonymousCustomerIfNotExist);
        if (!$member
         && $forceAnonymousCustomerIfNotExist
        ) {
            $member         = Member::create();
            $anonymousGroup = Group::get()->filter('Code', Customer::GROUP_CODE_ANONYMOUS)->first();
            $memberGroups   = ArrayList::create();
            $memberGroups->push($anonymousGroup);
        } else {
            $memberGroups = $member->Groups();
        }
        $shippingMethodID = null;
        if (Controller::curr() instanceof CheckoutStepController) {
            $checkoutData = Controller::curr()->getCheckout()->getData();
            if (array_key_exists('ShippingMethod', $checkoutData)) {
                $shippingMethodID   = $checkoutData['ShippingMethod'];
            }
        }
        if ($paymentMethods->exists()) {
            foreach ($paymentMethods as $paymentMethod) {
                $overwriteIsAllowed  = false;
                $paymentMethod->extend('overwriteIsAllowedPaymentMethod', $overwriteIsAllowed, $allowedPaymentMethods, $paymentMethod, $shippingCountry, $shoppingCart, $member);
                if ($overwriteIsAllowed) {
                    continue;
                }
                $assumePaymentMethod = true;
                $containedInGroup    = false;
                $containedInUsers    = false;
                $doAccessChecks      = true;
                // ------------------------------------------------------------
                // Basic checks
                // ------------------------------------------------------------
                if ($paymentMethod->enableActivationByOrderRestrictions) {
                    $assumePaymentMethod = $paymentMethod->isActivationByOrderRestrictionsPossible($member);
                    $doAccessChecks      = false;
                }
                $checkAmount = $shoppingCart->getAmountTotalWithoutFees()->getAmount();
                if (!$paymentMethod->isAvailableForAmount($checkAmount)) {
                    $assumePaymentMethod = false;
                    $doAccessChecks      = false;
                }
                // ------------------------------------------------------------
                // Shipping method check
                // ------------------------------------------------------------
                if (!is_null($shippingMethodID)
                 && $paymentMethod->ShippingMethods()->exists()
                 && !$paymentMethod->ShippingMethods()->find('ID', $shippingMethodID)
                ) {
                    $assumePaymentMethod = false;
                    $doAccessChecks      = false;
                }
                // ------------------------------------------------------------
                // Access checks
                // ------------------------------------------------------------
                if ($doAccessChecks) {
                    // Check if access for groups or is set positively
                    if ($paymentMethod->ShowOnlyForGroups()->exists()) {
                        foreach ($paymentMethod->ShowOnlyForGroups() as $paymentGroup) {
                            if ($memberGroups->find('ID', $paymentGroup->ID)) {
                                $containedInGroup = true;
                                break;
                            }
                        }
                        if ($containedInGroup) {
                            $assumePaymentMethod = true;
                        } else {
                            $assumePaymentMethod = false;
                        }
                    }
                    // Check if access for users or is set positively
                    if ($paymentMethod->ShowOnlyForUsers()->exists()) {
                        if ($paymentMethod->ShowOnlyForUsers()->find('ID', $member->ID)) {
                            $containedInUsers = true;
                        }
                        if ($containedInUsers) {
                            $assumePaymentMethod = true;
                        } else {
                            if (!$containedInGroup) {
                                $assumePaymentMethod = false;
                            }
                        }
                    }
                    // Check if access for groups is set negatively
                    if ($paymentMethod->ShowNotForGroups()->exists()) {
                        foreach ($paymentMethod->ShowNotForGroups() as $paymentGroup) {
                            if ($memberGroups->find('ID', $paymentGroup->ID)) {
                                if (!$containedInUsers) {
                                    $assumePaymentMethod = false;
                                }
                            }
                        }
                    }
                    // Check if access for users is set negatively
                    if ($paymentMethod->ShowNotForUsers()->exists()) {
                        if ($paymentMethod->ShowNotForUsers()->find('ID', $member->ID)) {
                            if (!$containedInUsers) {
                                $assumePaymentMethod = false;
                            }
                        }
                    }
                }
                if ($assumePaymentMethod) {
                    $allowedPaymentMethods[] = $paymentMethod;
                }
            }
        }
        $paymentMethod->extend('onAfterGetAllowedPaymentMethodsFor', $allowedPaymentMethods, $paymentMethods, $shippingCountry, $shoppingCart, $forceAnonymousCustomerIfNotExist);
        $allowedPaymentMethodList = ArrayList::create($allowedPaymentMethods);
        return $allowedPaymentMethodList;
    }
    
    /**
     * Checks if the given member has completed enough orders with a
     * specified status.
     * 
     * @param Member $member The member object whose orders are checked
     * 
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.07.2011
     */
    protected function isActivationByOrderRestrictionsPossible(Member $member)
    {
        $isActivationByOrderRestrictionsPossible = false;
        $nrOfValidOrders                         = 0;
        
        if (!$member) {
           return $isActivationByOrderRestrictionsPossible;
        }
        
        if ($member->Orders()) {
            foreach ($member->Orders() as $order) {
                if ($this->OrderRestrictionStatus()->find('ID', $order->OrderStatus()->ID)) {
                    $nrOfValidOrders++;
                }
                if ($nrOfValidOrders >= $this->orderRestrictionMinQuantity) {
                    break;
                }
            }
            
            if ($nrOfValidOrders >= $this->orderRestrictionMinQuantity) {
                $isActivationByOrderRestrictionsPossible = true;
            }
        }
        
        return $isActivationByOrderRestrictionsPossible;
    }

    /**
     * Returns allowed shipping methods. Those are
     * 
     * - shipping methods which are related directly to the payment method
     * - shipping methods which are NOT related to any payment method
     *
     * @return ArrayList
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sascha Koehler <skoehler@pixeltricks.de>
     * @since 11.05.2011
     */
    public function getAllowedShippingMethods()
    {
        $allowedShippingMethods = [];
        $shippingMethods        = ShippingMethod::get()->filter(["isActive" => 1]);

        if ($shippingMethods->exists()) {
            foreach ($shippingMethods as $shippingMethod) {

                // Find shippping methods that are directly related to
                // payment methods....
                if ($shippingMethod->PaymentMethods()->exists()) {
                    
                    // ... and exclude them, if the current payment method is
                    // not related.
                    if (!$shippingMethod->PaymentMethods()->find('ID', $this->ID)) {
                        continue;
                    }
                }
                
                // If there is no shipping fee defined for this shipping
                // method we don't want to show it.
                if ($shippingMethod->getShippingFee() !== false) {
                    $allowedShippingMethods[] = $shippingMethod;
                }
            }
        }
        
        $allowedShippingMethodList = ArrayList::create($allowedShippingMethods);
        
        return $allowedShippingMethodList;
    }

    /**
     * Returns weather this payment method is available for a zone specified by id or not
     *
     * @param int $zoneId Zone id to be checked
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.11.2010
     */
    public function isAvailableForZone($zoneId)
    {
        
    }

    /**
     * Is this payment method allowed for a shipping method?
     *
     * @param int $shippingMethodId Die ID id of shipping method to be checked
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.11.2010
     */
    public function isAvailableForShippingMethod($shippingMethodId)
    {
        
    }

    /**
     * Is this payment method allowed for a total amount?
     *
     * @param int $amount Amount to be checked
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.11.2010
     */
    public function isAvailableForAmount($amount)
    {
        $isAvailable = false;
        $amount      = (float) $amount;
        $minAmount   = (float) $this->minAmountForActivation;
        $maxAmount   = (float) $this->maxAmountForActivation;

        if ($minAmount != 0.0
         && $maxAmount != 0.0
        ) {
            if ($amount >= $minAmount
             && $amount <= $maxAmount
            ) {
                $isAvailable = true;
            }
        } elseif ($minAmount != 0.0) {
            if ($amount >= $minAmount) {
                $isAvailable = true;
            }
        } elseif ($maxAmount != 0.0) {
            if ($amount <= $maxAmount) {
                $isAvailable = true;
            }
        } else {
            $isAvailable = true;
        }

        return $isAvailable;
    }

    /**
     * writes a payment method to the db in case none does exist yet
     *
     * @return void
     */
    public function requireDefaultRecords() : void
    {
        $this->createUploadFolder();
        // not a base class
        if ($this->moduleName !== '') {
            if ($this->hasMultiplePaymentChannels()) {
                $paymentModule = static::create();
                foreach ($paymentModule->getPossiblePaymentChannels() as $channel => $name) {
                    if (!static::get()->filter('PaymentChannel', $channel)->exists()) {
                        $paymentMethod = static::create();
                        $paymentMethod->isActive       = 0;
                        $paymentMethod->PaymentChannel = $channel;
                        $paymentMethod->write();
                        $this->addTranslationsTo($paymentMethod, $name);
                    }
                }
            } elseif (!static::get()->exists()) {
                // entry does not exist yet
                //prepayment's default record gets activated if test data is enabled
                if ($this->moduleName == "Prepayment"
                 && RequireDefaultRecords::isEnabledTestData()
                ) {
                    $this->isActive = 1;
                    //As we do not know if the country is instanciated yet we do write this relation in the country class too.
                    $germany = Country::get()->filter('ISO2', 'DE')->first();
                    if ($germany instanceof Country) {
                        $this->Countries()->add($germany);
                    }
                } else {
                    $this->isActive = 0;
                }
                $this->Name  = _t(static::class . '.NAME',  $this->moduleName);
                $this->Title = _t(static::class . '.TITLE', $this->moduleName);
                $this->write();
                $this->addTranslationsTo($this, $this->moduleName);
            }
        }
        parent::requireDefaultRecords();
    }
    
    /**
     * Adds the default translations to the given $paymentMethod.
     * 
     * @param PaymentMethod $paymentMethod Payment method
     * @param string        $moduleName    Module name
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.08.2019
     */
    protected function addTranslationsTo(PaymentMethod $paymentMethod, string $moduleName)
    {
        $locales = ['de_DE', 'en_US', 'en_GB'];
        if (!in_array(Tools::current_locale(), $locales)) {
            $locales[] = Tools::current_locale();
        }
        $translationClassName = static::class . 'Translation';
        foreach ($locales as $locale) {
            $reflection    = new ReflectionClass(static::class);
            $relationField = "{$reflection->getShortName()}ID";
            $table         = PaymentMethodTranslation::config()->table_name;
            $filter        = "{$table}.Locale = '{$locale}' AND {$relationField} = '{$paymentMethod->ID}'";
            $translation   = DataObject::get($translationClassName)->where($filter)->first();
            if (!($translation instanceof DataObject)) {
                $translation = Injector::inst()->create($translationClassName);
                $translation->Locale = $locale;
            }
            $translation->Name             = $moduleName;
            $translation->{$relationField} = $paymentMethod->ID;
            $translation->write();
        }
    }
    
    /**
     * find out if we are dealing with an extended class or with PaymentMethod.
     * This is needed for the multilingual feature
     *
     * @return boolean 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 29.01.2012
     */
    public function isExtendingPaymentMethod()
    {
        $result = false;
        if ($this->ClassName) {
            if (in_array(PaymentMethod::class, class_parents($this->ClassName))) {
                $result = true;
            }
        }
        return $result;
    }
    
    /**
     * exclude the following fields
     *
     * @return array field names or relation names as numeric array 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 10.02.2013
     */
    public function excludeFromScaffolding()
    {
        $excludeFields = [
            'Countries',
            'Orders'
        ];
        $this->extend('updateExcludeFromScaffolding', $excludeFields);
        return $excludeFields;
    }
    
    /**
     * On before write. Checks for payment method detail request data when creating new payment 
     * methods.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.02.2017
     */
    public function onBeforeWrite()
    {
        if (!$this->exists()
         && array_key_exists('PaymentMethod', $_POST)
         && Customer::currentUser()->isAdmin()
        ) {
            $paymentMethod  = $_POST['PaymentMethod'];
            $paymentChannel = '';
            if (strpos($paymentMethod, '--') !== false) {
                list(
                    $paymentMethod,
                    $paymentChannel
                ) = explode('--', $paymentMethod);
            }
            unset($_POST['PaymentMethod']);
            if ($_POST['ClassName'] === $paymentMethod
             && $_POST['PaymentChannel'] === $paymentChannel
            ) {
                $this->Name = $this->getPaymentChannelName($paymentChannel);
            }
        }
        parent::onBeforeWrite();
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     */
    public function getCMSFields() : FieldList
    {
        $this->getCMSFieldsIsCalled = true;
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            if (!$this->exists()) {
                $paymentMethods = [];
                $subClasses = ClassInfo::subclassesFor(get_class($this));
                foreach ($subClasses as $subClass) {
                    if ($subClass == PaymentMethod::class) {
                        continue;
                    }
                    $subObject = singleton($subClass);
                    $paymentMethods[$subClass] = [
                        'channels' => $subObject->getPossiblePaymentChannels(),
                        'name'     => _t("{$subClass}.NAME",  $subObject->moduleName),
                        'title'    => _t("{$subClass}.TITLE", $subObject->moduleName),
                    ];
                }
                $paymentMethodsSource = [];
                foreach ($paymentMethods as $paymentMethodName => $paymentMethodData) {
                    if (count($paymentMethodData['channels']) > 0) {
                        foreach ($paymentMethodData['channels'] as $channelName => $channelTitle) {
                            $paymentMethodsSource["{$paymentMethodName}--{$channelName}"] = "{$channelTitle} ({$paymentMethodData['name']})";
                        }
                    } else {
                        $paymentMethodsSource[$paymentMethodName] = $paymentMethodData['title'];
                    }
                }
                asort($paymentMethodsSource);
                $firstClassName      = array_key_first($paymentMethodsSource);
                $firstPaymentChannel = '';
                if (strpos($firstClassName, '--') !== false) {
                    list($firstClassName, $firstPaymentChannel) = explode('--', $firstClassName);
                }
                $paymentMethodsSource = array_merge([
                    '' => Tools::field_label('PleaseChoose'),
                ], $paymentMethodsSource);
                $fields->addFieldToTab('Root.Main', DropdownField::create('PaymentMethod', $this->singular_name(), $paymentMethodsSource));
                $fields->addFieldToTab('Root.Main', HiddenField::create('ClassName', '', $firstClassName));
                $fields->addFieldToTab('Root.Main', HiddenField::create('PaymentChannel', '', $firstPaymentChannel));
                foreach (self::$db as $dbFieldName => $dbFieldType) {
                    $fields->removeByName($dbFieldName);
                }
                foreach (self::$has_one as $has_oneFieldName => $has_oneFieldType) {
                    $fields->removeByName("{$has_oneFieldName}ID");
                }
            }
        });
        return DataObjectExtension::getCMSFields($this);
    }
    
    /**
     * GUI for additional charges / discounts
     * 
     * @param FieldList $fields Fields to modify
     * 
     * @return void
     */
    public function getFieldsForChargesAndDiscounts($fields)
    {
        $this->getCMSFieldsIsCalled = true;
        $impactFieldValues = [
            'productValue'  => $this->fieldLabel('ModifyProductValue'),
            'totalValue'    => $this->fieldLabel('ModifyTotalValue'),
        ];
        $impactTypeValues = [
            'charge'    => $this->fieldLabel('ModifyCharge'),
            'discount'  => $this->fieldLabel('ModifyDiscount'),
        ];
        $impactValueTypeValues = [
            'absolute'  => $this->fieldLabel('ModifyAbsolute'),
            'percent'   => $this->fieldLabel('ModifyPercent'),
        ];
        
        $sumModifiersDataToggle = ToggleCompositeField::create(
                'SumModifiers',
                $this->fieldLabel('SumModifiers'),
                [
                        CheckboxField::create( 'useSumModification',        $this->fieldLabel('useSumModification')),
                        OptionsetField::create('sumModificationImpact',     $this->fieldLabel('sumModificationImpact'),     $impactFieldValues),
                        OptionsetField::create('sumModificationImpactType', $this->fieldLabel('sumModificationImpactType'), $impactTypeValues),
                        TextField::create(     'sumModificationValue',      $this->fieldLabel('sumModificationValue')),
                        OptionsetField::create('sumModificationValueType',  $this->fieldLabel('sumModificationValueType'),  $impactValueTypeValues),
                        TextField::create(     'sumModificationLabel',      $this->fieldLabel('sumModificationLabel')),
                        TextField::create('sumModificationProductNumber',   $this->fieldLabel('sumModificationProductNumber')),
                ]
        )->setHeadingLevel(4)->setStartClosed(true);
        
        $fields->addFieldToTab('Root.Basic', $sumModifiersDataToggle);
    }

    /**
     * Returns modified CMS fields for the payment modules
     *
     * @return FieldList
     */
    public function getCMSFieldsForModules()
    {
        $this->getCMSFieldsIsCalled = true;
        $tabset = TabSet::create('Root');
        $fields = FieldList::create($tabset);
        
        // --------------------------------------------------------------------
        // Common GUI elements for all payment methods
        // --------------------------------------------------------------------
        $tabBasic = Tab::create('Basic', $this->fieldLabel('BasicSettings'));
        $translationsTab = Tab::create('Translations');
        $translationsTab->setTitle($this->fieldLabel('Translations'));
        $tabset->push($tabBasic);
        $tabset->push($translationsTab);
        $tabBasicFieldList = FieldList::create();
        $tabBasic->setChildren($tabBasicFieldList);
        //multilingual fields
        $tabBasicFieldList->push(CheckboxField::create('isActive', $this->fieldLabel('isActive')));
        if ($this->hasField('PaymentChannel')) {
            $tabBasicFieldList->push(ReadonlyField::create('DisplayPaymentChannel', $this->fieldLabel('PaymentChannel'), $this->getPaymentChannelName($this->PaymentChannel)));
        }
        $tabBasicFieldList->push(DropdownField::create('mode', $this->fieldLabel('Mode'),
                    [
                        'Live' => $this->fieldLabel('modeLive'),
                        'Dev'  => $this->fieldLabel('modeDev'),
                    ],
                    $this->mode
                ));
        if ($this->isExtendingPaymentMethod()) {
           $languageFields = TranslationTools::prepare_cms_fields($this->getTranslationClassName());
            foreach ($languageFields as $languageField) {
                $tabBasicFieldList->push($languageField);
            } 
        }
        $tabBasicFieldList->push(TextField::create('minAmountForActivation', $this->fieldLabel('MinAmountForActivation')));
        $tabBasicFieldList->push(TextField::create('maxAmountForActivation', $this->fieldLabel('MaxAmountForActivation')));
        $tabBasicFieldList->push(DropdownField::create(
                    'PaymentStatusID',
                    $this->fieldLabel('PaymentStatus'),
                    PaymentStatus::get()->map('ID', 'Title')->toArray()
                )
                ->setDescription($this->fieldLabel('PaymentStatusDesc'))
                ->setEmptyString(Tools::field_label('PleaseChoose'))
        );
        
        // --------------------------------------------------------------------
        // Handling cost table
        // --------------------------------------------------------------------
        $tabHandlingCosts= Tab::create('HandlingCosts', $this->fieldLabel('HandlingCosts'));
        $tabset->push($tabHandlingCosts);
        
        $handlingCostField = GridField::create(
                'HandlingCosts',
                $this->fieldLabel('HandlingCosts'),
                $this->HandlingCosts(),
                GridFieldConfig_RelationEditor::create(50)
        );
        $tabHandlingCosts->setChildren(
            FieldList::create(
                $handlingCostField
            )
        );
        

        // --------------------------------------------------------------------
        // GUI for management of logo images
        // --------------------------------------------------------------------
        $tabLogos = Tab::create('Logos', $this->fieldLabel('PaymentLogos'));
        $tabset->push($tabLogos);
        
        $paymentLogosTable = GridField::create(
                'PaymentLogos',
                $this->fieldLabel('PaymentLogos'),
                $this->PaymentLogos(),
                GridFieldConfig_RelationEditor::create()
        );
        
        $paymentLogosTable->getConfig()->removeComponentsByType('GridFieldAddNewButton');
        $paymentLogosTable->getConfig()->removeComponentsByType('GridFieldAddExistingAutocompleter');
        $paymentLogosTable->getConfig()->addComponent(new GridFieldDeleteAction());
        
        $paymentLogosUploadField = ImageUploadField::create('UploadPaymentLogos', $this->fieldLabel('AddPaymentLogos'));
        $paymentLogosUploadField->setFolderName('assets/payment-images');
        
        $tabLogos->setChildren(
            FieldList::create(
                CheckboxField::create('showPaymentLogos', $this->fieldLabel('ShowPaymentLogos')),
                $paymentLogosUploadField,
                $paymentLogosTable
            )
        );
        
        // --------------------------------------------------------------------
        // GUI for access management
        // --------------------------------------------------------------------
        $tabAccessManagement = Tab::create('AccessManagement', $this->fieldLabel('AccessSettings'));
        $tabset->push($tabAccessManagement);
        
        $tabsetAccessManagement = TabSet::create('AccessManagementSection');
        $tabAccessManagement->push($tabsetAccessManagement);
        
        $tabAccessManagementBasic = Tab::create('AccessManagementBasic', $this->fieldLabel('AccessManagementBasic'));
        $tabAccessManagementGroup = Tab::create('AccessManagementGroup', $this->fieldLabel('AccessManagementGroup'));
        $tabAccessManagementUser  = Tab::create('AccessManagementUser',  $this->fieldLabel('AccessManagementUser'));
        $tabsetAccessManagement->push($tabAccessManagementBasic);
        $tabsetAccessManagement->push($tabAccessManagementGroup);
        $tabsetAccessManagement->push($tabAccessManagementUser);
        
        $showOnlyForGroupsTable = GridField::create(
                'ShowOnlyForGroups',
                $this->fieldLabel('ShowOnlyForGroups'),
                $this->ShowOnlyForGroups(),
                GridFieldConfig_RelationEditor::create()
        );
        $showNotForGroupsTable = GridField::create(
                'ShowNotForGroups',
                $this->fieldLabel('ShowNotForGroups'),
                $this->ShowNotForGroups(),
                GridFieldConfig_RelationEditor::create()
        );
        $showOnlyForUsersTable = GridField::create(
                'ShowOnlyForUsers',
                $this->fieldLabel('ShowOnlyForUsers'),
                $this->ShowOnlyForUsers(),
                GridFieldConfig_RelationEditor::create()
        );
        $showNotForUsersTable = GridField::create(
                'ShowNotForUsers',
                $this->fieldLabel('ShowNotForUsers'),
                $this->ShowNotForUsers(),
                GridFieldConfig_RelationEditor::create()
        );
        
        $restrictionByOrderQuantityField = TextField::create('orderRestrictionMinQuantity', '');
        
        $restrictionByOrderStatusField = GridField::create(
                'OrderRestrictionStatus',
                $this->fieldLabel('OrderStatus'),
                $this->OrderRestrictionStatus(),
                GridFieldConfig_RelationEditor::create()
        );
        
        // Access management basic --------------------------------------------
        $tabAccessManagementBasic->push(
            HeaderField::create('RestrictionLabel', $this->fieldLabel('RestrictionLabel') . ':', 2)
        );
        $tabAccessManagementBasic->push(LiteralField::create('separatorForActivationByOrderRestrictions', '<hr />'));
        $tabAccessManagementBasic->push(
            CheckboxField::create(
                'enableActivationByOrderRestrictions',
                $this->fieldLabel('ActivationByOrderRestrictions')
            )
        );
        $tabAccessManagementBasic->push(
            LiteralField::create('RestrictionByOrderQuantityLabel', '<p>' . $this->fieldLabel('RestrictionByOrderQuantityLabel') . ':</p>')
        );
        $tabAccessManagementBasic->push($restrictionByOrderQuantityField);
        $tabAccessManagementBasic->push(
            LiteralField::create('RestrictionByOrderStatusLabel', '<p>' . $this->fieldLabel('RestrictionByOrderStatusLabel') . ':</p>')
        );
        $tabAccessManagementBasic->push(
            $restrictionByOrderStatusField
        );
        
        // Access management for groups ---------------------------------------
        $tabAccessManagementGroup->push(
            HeaderField::create('ShowOnlyForGroupsLabel', $this->fieldLabel('ShowOnlyForGroups') . ':', 2)
        );
        $tabAccessManagementGroup->push($showOnlyForGroupsTable);
        $tabAccessManagementGroup->push(
            HeaderField::create('ShowNotForGroupsLabel', $this->fieldLabel('ShowNotForGroups') . ':', 2)
        );
        $tabAccessManagementGroup->push($showNotForGroupsTable);
        
        // Access management for users ----------------------------------------
        $tabAccessManagementUser->push(
            HeaderField::create('ShowOnlyForUsersLabel', $this->fieldLabel('ShowOnlyForUsers') . ':', 2)
        );
        $tabAccessManagementUser->push($showOnlyForUsersTable);
        $tabAccessManagementUser->push(
            HeaderField::create('ShowNotForUsersLabel', $this->fieldLabel('ShowNotForUsers') . ':', 2)
        );
        $tabAccessManagementUser->push($showNotForUsersTable);
        
        // --------------------------------------------------------------------
        // Countries
        // --------------------------------------------------------------------
        $countriesTab = Tab::create('Countries', $this->fieldLabel('Countries'));
        $tabset->push($countriesTab);
        $countriesTable = GridField::create(
                'Countries',
                $this->fieldLabel('Countries'),
                $this->Countries(),
                GridFieldConfig_RelationEditor::create()
        );
        $countriesTab->push($countriesTable);
        
        // --------------------------------------------------------------------
        // shipping methods
        // --------------------------------------------------------------------
        $shippingMethodsTab     = Tab::create('ShippingMethods', $this->fieldLabel('ShippingMethods'));
        $shippingMethodsDesc    = HeaderField::create('ShippingMethodsDesc', $this->fieldLabel('ShippingMethodsDesc'));
        
        $shippingMethodsTable = GridField::create(
                'ShippingMethods',
                $this->fieldLabel('ShippingMethods'),
                $this->ShippingMethods(),
                GridFieldConfig_RelationEditor::create(50)
        );
        $tabset->push($shippingMethodsTab);
        $shippingMethodsTab->push($shippingMethodsDesc);
        $shippingMethodsTab->push($shippingMethodsTable);
        
        $this->getFieldsForChargesAndDiscounts($fields);
        
        $this->extend('updateCMSFields', $fields);
        
        return $fields;
    }

    /**
     * set the link to be visited on a cancel action
     *
     * @param string $link the url
     *
     * @return void
     */
    public function setCancelLink($link)
    {
        $this->cancelLink = $link;
        $this->extend('updateCancelLink', $this->cancelLink);
    }

    /**
     * sets the link to return to the shop
     *
     * @param string $link the url
     *
     * @return void
     */
    public function setReturnLink($link)
    {
        $this->returnLink = $link;
        $this->extend('updateReturnLink', $this->returnLink);
    }

    /**
     * sets the link to notify the shop (push)
     *
     * @param string $link the url
     *
     * @return void
     */
    public function setNotificationLink($link)
    {
        $this->notificationLink = $link;
        $this->extend('updateNotificationLinkLink', $this->notificationLink);
    }
    
    /**
     * Returns the sumModificationValue.
     * 
     * @return float
     */
    public function getsumModificationValue()
    {
        $sumModificationValue = $this->getField('sumModificationValue');
        if (!$this->getCMSFieldsIsCalled) {
            $this->extend('updateSumModificationValue', $sumModificationValue);
        }
        return $sumModificationValue;
    }

    /**
     * set the controller
     *
     * @param Controller $controller the controller action
     *
     * @return void
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }
    
    /**
     * Returns the controller.
     * 
     * @return Controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Returns the attributed countries as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function AttributedCountries()
    {
        return Tools::AttributedDataObject($this->Countries());
    }

    /**
     * Returns the attributed zones as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     * @deprecated
     */
    public function AttributedZones()
    {
        return Tools::AttributedDataObject($this->Zone());
    }

    /**
     * Returns the activation status as HTML-Checkbox-Tag.
     *
     * @return CheckboxField
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function activatedStatus()
    {
        return CheckboxField::create('isActivated' . $this->ID, 'isActived', $this->isActive)
                ->setReadonly(true)
                ->setDisabled(true);
    }

    /**
     * writes a log entry
     *
     * @param string $context the context for the log entry
     * @param string $text    the text for the log entry
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function Log($context, $text)
    {
        Config::Log($context, $text, empty($this->moduleName) ? 'payment' : 'payment-' . $this->moduleName);
    }

    /**
     * registers an error
     *
     * @param string $errorText text for the error message
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.11.2010
     */
    public function addError($errorText)
    {
        array_push($this->errorList, $errorText);
    }

    /**
     * Creates payment status DB objects from the given list.
     *
     * @param array $statusList The payment status list as associative array
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function createRequiredPaymentStatus($statusList)
    {
        foreach ($statusList as $code => $title) {
            $table = PaymentStatus::config()->get('table_name');
            if (!PaymentStatus::get()->filter('Code', $code)->sort('"' . $table . '"."ID"')->first()) {
                $status = PaymentStatus::create();
                $status->Title = $title;
                $status->Code = $code;
                $status->write();
            }
        }
    }

    /**
     * Creates the upload folder for payment images if it doesn't exist.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.06.2014
     */
    public function createUploadFolder()
    {
        $uploadsFolder = Folder::get()->filter('Name', 'payment-images')->first();
        if (!($uploadsFolder instanceof Folder)) {
            $uploadsFolder = Folder::create();
            $uploadsFolder->Name = 'payment-images';
            $uploadsFolder->Title = 'payment-images';
            $uploadsFolder->ParentID = 0;//$assetsFolder->ID;
            $uploadsFolder->write();
        }
        $this->uploadsFolder = $uploadsFolder;
    }

    /**
     * Creates the upload folder for payment images if it doesn't exist.
     *
     * @param array  $paymentLogos      The payment logos as associative array:
     *                                  ['LogoName' => 'PATH_TO_FILE', ....]
     * @param string $paymentModuleName The name of the payment module
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function createLogoImageObjects($paymentLogos, $paymentModuleName)
    {
        $this->createUploadFolder();
        $paymentModule = PaymentMethod::get()->filter('ClassName', $paymentModuleName)->sort('ID', 'ASC')->first();
        if ($paymentModule instanceof PaymentMethod) {
            if (count($this->getPossiblePaymentChannels()) > 0) {
                foreach ($paymentLogos as $paymentChannel => $logos) {
                    $paymentChannel = DataObject::get($paymentModuleName)->filter('PaymentChannel', $paymentChannel)->first();
                    if ($paymentChannel) {
                        $this->addPaymentLogos($paymentChannel, $logos);
                    }
                }
            } else {
                $this->addPaymentLogos($paymentModule, $paymentLogos);
            }
        }
    }
    
    /**
     * Adds the given payment logos for the given payment module.
     * 
     * @param PaymentMethod $paymentModule Payment module
     * @param array         $paymentLogos  Payment logos
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    protected function addPaymentLogos($paymentModule, $paymentLogos)
    {
        if (!$paymentModule->PaymentLogos()->exists()) {
            foreach ($paymentLogos as $title => $logo) {
                $image = SilverStripeImage::get()->filter('Name', basename($logo))->first();

                if ((!($image instanceof SilverStripeImage)
                  || !$image->exists())
                 && file_exists($logo)
                ) {
                    $uploadsPath = ASSETS_PATH . DIRECTORY_SEPARATOR . $this->uploadsFolder->Filename;
                    ImageExtension::create_from_path($logo, $uploadsPath);
                }

                if ($image instanceof SilverStripeImage
                 && $image->exists()
                ) {
                    $paymentLogo          = Image::create();
                    $paymentLogo->Title   = $title;
                    $paymentLogo->ImageID = $image->ID;
                    $paymentLogo->write();
                    $paymentModule->PaymentLogos()->add($paymentLogo);
                }
            }
        }
    }

    /**
     * Sets the customers details
     *
     * @param Member $customerDetails Details of customer
     *
     * @return void
     */
    public function setCustomerDetails(Member $customerDetails)
    {
        $this->customerDetails = $customerDetails;
    }

    /**
     * Sets the invoice address
     *
     * @param Address $invoiceAddress Invoice address
     *
     * @return void
     */
    public function setInvoiceAddress(Address $invoiceAddress)
    {
        $this->invoiceAddress = $invoiceAddress;
    }

    /**
     * Sets the shipping address
     *
     * @param Address $shippingAddress Shipping address
     *
     * @return void
     */
    public function setShippingAddress(Address $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
    }

    /**
     * Sets the shopping cart
     *
     * @param ShoppingCart $shoppingCart Shopping cart
     *
     * @return void
     */
    public function setShoppingCart(ShoppingCart $shoppingCart)
    {
        $this->shoppingCart = $shoppingCart;
    }

    /**
     * Sets the order object
     *
     * @param Order $order The order object
     *
     * @return void
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Returns the customers details
     *
     * @return Member
     */
    public function getCustomerDetails()
    {
        return $this->customerDetails;
    }

    /**
     * Returns the invoice address
     *
     * @return Address
     */
    public function getInvoiceAddress()
    {
        return $this->invoiceAddress;
    }

    /**
     * Returns the shipping address
     *
     * @return Address
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * Returns the shopping cart
     *
     * @return ShoppingCart
     */
    public function getShoppingCart()
    {
        return $this->shoppingCart;
    }

    /**
     * Returns the order
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Sets the customers details by checkout data
     *
     * @param array $checkoutData Checkout data
     *
     * @return void
     */
    public function setCustomerDetailsByCheckoutData($checkoutData)
    {
        $addressData     = $checkoutData['InvoiceAddress'];
        $customerDetails = Member::create();
        $customerDetails->Email      = isset($checkoutData['Email']) ? $checkoutData['Email'] : '';
        $customerDetails->Salutation = isset($addressData['Salutation']) ? $addressData['Salutation'] : '';
        $customerDetails->FirstName  = isset($addressData['FirstName']) ? $addressData['FirstName'] : '';
        $customerDetails->Surname    = isset($addressData['Surname']) ? $addressData['Surname'] : '';
        $this->setCustomerDetails($customerDetails);
    }

    /**
     * Sets the customers details by checkout data
     *
     * @param array $checkoutData Checkout data
     *
     * @return void
     */
    public function setInvoiceAddressByCheckoutData($checkoutData)
    {
        $address = $this->getAddressByCheckoutData($checkoutData);
        $this->setInvoiceAddress($address);
    }

    /**
     * Sets the customers details by checkout data
     *
     * @param array $checkoutData Checkout data
     *
     * @return void
     */
    public function setShippingAddressByCheckoutData($checkoutData)
    {
        $address = $this->getAddressByCheckoutData($checkoutData, 'ShippingAddress');
        $this->setShippingAddress($address);
    }
    
    /**
     * Creates an address using the given checkout data and prefix.
     * 
     * @param array  $checkoutData Checkout data
     * @param string $type         Address type
     * 
     * @return Address
     */
    public function getAddressByCheckoutData($checkoutData, $type = 'InvoiceAddress')
    {
        $db      = Address::config()->get('db');
        $has_one = Address::config()->get('has_one');
        
        $addressData = $checkoutData[$type];
        $address     = Address::create();
        foreach (array_keys($db) as $fieldname) {
            if (array_key_exists($fieldname, $addressData)) {
                $address->{$fieldname} = $addressData[$fieldname];
            }
        }
        foreach (array_keys($has_one) as $relationname) {
            $fieldname         = $relationname . 'ID';
            $plainFieldname    = str_replace('Silvercart', '', $fieldname);
            $plainRelationname = str_replace('Silvercart', '', $relationname);
            if (array_key_exists($relationname, $addressData)) {
                $address->{$fieldname} = $addressData[$relationname];
            } elseif (array_key_exists($fieldname, $addressData)) {
                $address->{$fieldname} = $addressData[$fieldname];
            } elseif (array_key_exists($plainRelationname, $addressData)) {
                $address->{$fieldname} = $addressData[$plainRelationname];
            } elseif (array_key_exists($plainFieldname, $addressData)) {
                $address->{$fieldname} = $addressData[$plainFieldname];
            }
            if (!is_null($address->{$fieldname})) {
                $address->{$plainFieldname} = $address->{$fieldname};
            }
        }
        
        if (is_null($address->IsPackstation)
         || $type == 'InvoiceAddress'
        ) {
            $address->IsPackstation = false;
        }
        $address->Country = Country::get()->byID($address->CountryID);
        
        return $address;
    }

    /**
     * Returns all possible payment channels of the current payment module.
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.08.2019
     */
    public function getPossiblePaymentChannels() : array
    {
        $possiblePaymentChannels = [];
        if (!$this->hasMultiplePaymentChannels()) {
            return [];
        }
        foreach ($this->config()->possible_payment_channels as $key => $value) {
            $possiblePaymentChannels[$key] = _t(static::class . '.PAYMENT_CHANNEL_' . strtoupper($key), $value);
        }
        return $possiblePaymentChannels;
    }
    
    /**
     * Returns whether this payment method has more than one channel.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.08.2019
     */
    public function hasMultiplePaymentChannels() : bool
    {
        return count($this->config()->possible_payment_channels) > 0;
    }

    /**
     * Returns the i18n title for a payment channel.
     *
     * @param string $paymentChannel The payment channel
     *
     * @return string
     */
    public function getPaymentChannelName(string $paymentChannel = null) : string
    {
        return _t($this->ClassName . '.PAYMENT_CHANNEL_' . strtoupper($paymentChannel), empty($paymentChannel) ? 'none' : $paymentChannel);
    }
    
    /**
     * Returns an optional payment specific form name to insert into checkout step 3.
     *
     * @return bool
     */
    public function getNestedFormName() : bool
    {
        return false;
    }
    
    /**
     * Returns the context/nested CheckoutChoosePaymentMethodForm.
     * 
     * @param RequestHandler $controller Controller
     * 
     * @return Form
     */
    public function CheckoutChoosePaymentMethodForm(RequestHandler $controller = null) : Form
    {
        if (is_null($controller)) {
            $controller = Controller::curr();
        }
        $member   = Customer::currentUser();
        $checkout = $controller->getCheckout();
        /* @var $checkout \SilverCart\Checkout\Checkout */
        $checkoutData = $checkout->getData();
        $this->setController($controller);
        $this->setCancelLink(Director::absoluteURL($controller->Link('step/3')));
        $this->setReturnLink(Director::absoluteURL($controller->Link()));
        $this->setCustomerDetailsByCheckoutData($checkoutData);
        $this->setInvoiceAddressByCheckoutData($checkoutData);
        $this->setShippingAddressByCheckoutData($checkoutData);
        if (array_key_exists('ShippingMethod', $checkoutData)) {
            $member->getCart()->setShippingMethodID((int) $checkoutData['ShippingMethod']);
        }
        $this->setShoppingCart($member->getCart());
        $formName = $this->getNestedFormName();
        if (!class_exists($formName)) {
            $formName = CheckoutChoosePaymentMethodForm::class;
        }
        $form = new $formName($this, $controller);
        return $form;
    }

    /***********************************************************************************************
     ***********************************************************************************************
     **                                                                                           ** 
     ** Internal Payment processing section.                                                      ** 
     **                                                                                           ** 
     **     - doPocessBeforeOrder                                                                 ** 
     **     - doProcessAfterOrder                                                                 ** 
     **     - doProcessBeforePaymentProvider                                                      ** 
     **     - doProcessAfterPaymentProvider                                                       ** 
     **     - doProcessNotification                                                               ** 
     **                                                                                           ** 
     ***********************************************************************************************
     **********************************************************************************************/
    
    /**
     * Is called right before redirecting to the external payment provider (e.g. like redirecting to
     * paypal to do the payment).
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function doProcessBeforePaymentProvider(array $checkoutData)
    {
        if ($this->canProcessBeforePaymentProvider($checkoutData)) {
            $this->processBeforePaymentProvider($checkoutData);
        }
    }
    
    /**
     * Is called right after returning to the checkout after being redirected to the external 
     * payment provider (e.g. like doing the payment at PayPal and then redirecting to the shop).
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function doProcessAfterPaymentProvider(array $checkoutData)
    {
        if ($this->canProcessAfterPaymentProvider($checkoutData)) {
            $this->processAfterPaymentProvider($checkoutData);
        }
    }
    
    /**
     * Is called by default checkout right before placing an order.
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function doProcessBeforeOrder(array $checkoutData)
    {
        if ($this->canProcessBeforeOrder($checkoutData)) {
            $this->processBeforeOrder($checkoutData);
        }
    }
    
    /**
     * Is called by default checkout right after placing an order.
     * 
     * @param \SilverCart\Model\Order\Order $order        Order
     * @param array                         $checkoutData Checkout data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function doProcessAfterOrder(Order $order, array $checkoutData)
    {
        if ($this->canProcessAfterOrder($order, $checkoutData)) {
            $this->processAfterOrder($order, $checkoutData);
        }
    }
    
    /**
     * Is called when a payment provider sends a background notification to the shop.
     * 
     * @param HTTPRequest $request Request data
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function doProcessNotification(HTTPRequest $request)
    {
        return $this->processNotification($request);
    }

    /***********************************************************************************************
     ***********************************************************************************************
     **                                                                                           ** 
     ** Payment processing section. SilverCart checkout will call these methods:                  ** 
     **                                                                                           ** 
     **     - canProcessBeforePaymentProvider                                                     ** 
     **     - canProcessAfterPaymentProvider                                                      ** 
     **     - canProcessBeforeOrder                                                               ** 
     **     - canProcessAfterOrder                                                                ** 
     **     - canPlaceOrder                                                                       ** 
     **     - processBeforePaymentProvider                                                        ** 
     **     - processAfterPaymentProvider                                                         ** 
     **     - processBeforeOrder                                                                  ** 
     **     - processAfterOrder                                                                   ** 
     **     - processNotification                                                                 ** 
     **     - processConfirmationText                                                             ** 
     **     - resetProgress                                                                       ** 
     **                                                                                           ** 
     ***********************************************************************************************
     **********************************************************************************************/
    
    /**
     * Returns whether the checkout is ready to call self::processBeforePaymentProvider().
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function canProcessBeforePaymentProvider(array $checkoutData) : bool
    {
        return false;
    }
    
    /**
     * Returns whether the checkout is ready to call self::processAfterPaymentProvider().
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function canProcessAfterPaymentProvider(array $checkoutData) : bool
    {
        return false;
    }
    
    /**
     * Returns whether the checkout is ready to call self::processBeforeOrder().
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function canProcessBeforeOrder(array $checkoutData) : bool
    {
        return false;
    }
    
    /**
     * Returns whether the checkout is ready to call self::processAfterOrder().
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function canProcessAfterOrder(Order $order, array $checkoutData) : bool
    {
        return $this->canPlaceOrder($checkoutData) && $order instanceof Order && $order->exists();
    }
    
    /**
     * Is called by default checkout right before placing an order.
     * If this returns false, the order won't be placed and the checkout won't be finalized.
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function canPlaceOrder(array $checkoutData) : bool
    {
        return false;
    }
    
    /**
     * Is called by default checkout right before placing an order.
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.04.2018
     */
    protected function processBeforeOrder(array $checkoutData) : void
    {
        
    }
    
    /**
     * Is called by default checkout right after placing an order.
     * 
     * @param \SilverCart\Model\Order\Order $order        Order
     * @param array                         $checkoutData Checkout data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.04.2018
     */
    protected function processAfterOrder(Order $order, array $checkoutData) : void
    {
        
    }
    
    /**
     * Is called right before redirecting to the external payment provider (e.g. like redirecting to
     * paypal to do the payment).
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.04.2018
     */
    protected function processBeforePaymentProvider(array $checkoutData) : void
    {
        
    }
    
    /**
     * Is called right after returning to the checkout after being redirected to the external 
     * payment provider (e.g. like doing the payment at PayPal and then redirecting to the shop).
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.04.2018
     */
    protected function processAfterPaymentProvider(array $checkoutData) : void
    {
        
    }
    
    /**
     * Is called when a payment provider sends a background notification to the shop.
     * 
     * @param HTTPRequest $request Request data
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    protected function processNotification(HTTPRequest $request)
    {
        
    }
    
    /**
     * Is called before rendering the order confirmation page right after the order placement is 
     * finalized.
     * Expects an optional string to display additional information to the customer (e.g. showing
     * the shop owners bank account data if the customer chose prepayment).
     * 
     * @param \SilverCart\Model\Order\Order $order        Order
     * @param array                         $checkoutData Checkout data
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.04.2018
     */
    public function processConfirmationText(Order $order, array $checkoutData) : string
    {
        return '';
    }
    
    /**
     * Resets the payment progress (usually hold in session).
     * To be implemented for each complex payment method.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.10.2018
     */
    public function resetProgress() : void
    {
        
    }
}