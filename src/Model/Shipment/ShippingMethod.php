<?php

namespace SilverCart\Model\Shipment;

use SilverCart\Admin\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverCart\Dev\DateTools;
use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Pages\CheckoutStepController;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Shipment\Carrier;
use SilverCart\Model\Shipment\ShippingFee;
use SilverCart\Model\Shipment\ShippingMethodTranslation;
use SilverCart\Model\Shipment\Zone;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\SS_List;
use SilverStripe\ORM\Filters\ExactMatchFilter;
use SilverStripe\ORM\Filters\PartialMatchFilter;
use SilverStripe\Security\Group;

/**
 * These are the shipping methods the shop offers.
 *
 * @package SilverCart
 * @subpackage Model_Shipment
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property bool   $isActive                    Is active?
 * @property bool   $isPickup                    Is pickup?
 * @property int    $priority                    Priority
 * @property bool   $DoNotShowOnShippingFeesPage Do not show this shipping method on  shipping fees page?
 * @property int    $DeliveryTimeMin             Delivery time min
 * @property int    $DeliveryTimeMax             Delivery time max
 * @property string $DeliveryTimeText            Delivery time text
 * 
 * @method Carrier Carrier() Returns the related Carrier.
 * @method Image   Logo()    Returns the related Logo.
 * 
 * @method \SilverStripe\ORM\HasManyList Orders()                     Returns the related Orders.
 * @method \SilverStripe\ORM\HasManyList ShippingFees()               Returns the related ShippingFees.
 * @method \SilverStripe\ORM\HasManyList ShippingMethodTranslations() Returns the related translations.
 * 
 * @method \SilverStripe\ORM\ManyManyList Zones()          Returns the related Zones.
 * @method \SilverStripe\ORM\ManyManyList CustomerGroups() Returns the related customer Groups.
 * @method \SilverStripe\ORM\ManyManyList PaymentMethods() Returns the related PaymentMethods.
 */
class ShippingMethod extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * Configuration property to determine whether to include Saturdays into the
     * amount of days for the delivery time by default.
     *
     * @var bool
     */
    private static $include_saturdays_in_delivery_time = true;
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = [
        'isActive'                      => 'Boolean',
        'isPickup'                      => 'Boolean(0)',
        'priority'                      => 'Int',
        'DoNotShowOnShippingFeesPage'   => 'Boolean',
        'DeliveryTimeMin'               => 'Int',
        'DeliveryTimeMax'               => 'Int',
        'DeliveryTimeText'              => 'Varchar(256)',
    ];
    /**
     * Has-one relationships.
     *
     * @var array
     */
    private static $has_one = [
        'Carrier' => Carrier::class,
        'Logo'    => Image::class,
    ];
    /**
     * Has-many relationship.
     *
     * @var array
     */
    private static $has_many = [
        'Orders'                     => Order::class,
        'ShippingFees'               => ShippingFee::class,
        'ShippingMethodTranslations' => ShippingMethodTranslation::class,
    ];
    /**
     * Many-many relationships.
     *
     * @var array
     */
    private static $many_many = [
        'Zones'           => Zone::class,
        'CustomerGroups'  => Group::class,
    ];
    /**
     * Belongs-many-many relationships.
     *
     * @var array
     */
    private static $belongs_many_many = [
        'PaymentMethods' => PaymentMethod::class,
    ];
    /**
     * Virtual database columns.
     *
     * @var array
     */
    private static $casting = [
        'AttributedCountries'               => 'Varchar(255)',
        'activatedStatus'                   => 'Varchar(255)',
        'AttributedCustomerGroups'          => 'Text',
        'AttributedZones'                   => 'Text',
        'AttributedZoneIDs'                 => 'Text',
        'Title'                             => 'Text',
        'Description'                       => 'Text',
        'DescriptionForShippingFeesPage'    => 'Text',
        'ShowOnShippingFeesPage'            => 'Boolean',
        'DeliveryTime'                      => 'Text',
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartShippingMethod';
    /**
     * Grant API access on this item.
     *
     * @var bool
     */
    private static $api_access = true;
    /**
     * Default sort field and direction
     *
     * @var string
     */
    private static $default_sort = "priority ASC";
    /**
     * Shipping address
     *
     * @var Address
     */
    protected $shippingAddress = null;
    /**
     * Shipping country
     *
     * @var Country
     */
    protected $shippingCountry = null;
    /**
     * Shipping fees by weight
     *
     * @var array
     */
    protected $shippingFees = [];
    
    /**
     * Searchable fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012
     */
    public function searchableFields()
    {
        $searchableFields = [
            'ShippingMethodTranslations.Title' => [
                'title'  => $this->fieldLabel('Title'),
                'filter' => PartialMatchFilter::class,
            ],
            'isActive' => [
                'title'  => $this->fieldLabel('isActive'),
                'filter' => ExactMatchFilter::class,
            ],
            'Carrier.ID' => [
                'title'  => $this->fieldLabel('Carrier'),
                'filter' => ExactMatchFilter::class,
            ],
            'Zones.ID' => [
                'title'  => $this->fieldLabel('Zones'),
                'filter' => ExactMatchFilter::class,
            ],
            'CustomerGroups.ID' => [
                'title'  => $this->fieldLabel('CustomerGroups'),
                'filter' => ExactMatchFilter::class,
            ],
        ];
        $this->extend('updateSearchableFields', $searchableFields);
        return $searchableFields;
    }
    
    /**
     * Sets the field labels.
     *
     * @param bool $includerelations set to true to include the DataObjects relations
     * 
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2013
     */
    public function fieldLabels($includerelations = true)
    {
        return $this->defaultFieldLabels($includerelations, [
            'Title'                          => Product::singleton()->fieldLabel('Title'),
            'Description'                    => _t(ShippingMethod::class . '.DESCRIPTION', 'Description'),
            'DescriptionForShippingFeesPage' => _t(ShippingMethod::class . '.DescriptionForShippingFeesPage', 'Description for Shipping Fees Page (will be used instead of "Description")'),
            'activatedStatus'                => PaymentMethod::singleton()->fieldLabel('isActive'),
            'priority'                       => Tools::field_label('Priority'),
            'AttributedZones'                => _t(ShippingMethod::class . '.FOR_ZONES', 'for zones'),
            'ForZones'                       => _t(ShippingMethod::class . '.FOR_ZONES', 'for zones'),
            'isActive'                       => PaymentMethod::singleton()->fieldLabel('isActive'),
            'isPickup'                       => _t(ShippingMethod::class . '.isPickup', 'Is pickup (no active shipping, customer needs to pickup himself)'),
            'Carrier'                        => Carrier::singleton()->singular_name(),
            'ShippingFees'                   => ShippingFee::singleton()->plural_name(),
            'Zones'                          => Zone::singleton()->plural_name(),
            'CustomerGroups'                 => Group::singleton()->plural_name(),
            'ShippingMethodTranslations'     => ShippingMethodTranslation::singleton()->plural_name(),
            'DoNotShowOnShippingFeesPage'    => _t(ShippingMethod::class . '.DoNotShowOnShippingFeesPage', 'Do not show on Shipping Fees Page'),
            'ExpectedDelivery'               => _t(ShippingMethod::class . '.ExpectedDelivery', 'Expected Delivery'),
            'ReadyForPickup'                 => _t(ShippingMethod::class . '.ReadyForPickup', 'Ready for pickup'),
            'DeliveryTime'                   => _t(ShippingMethod::class . '.DeliveryTime', 'Delivery time'),
            'DeliveryTimeMin'                => _t(ShippingMethod::class . '.DeliveryTimeMin', 'Minimum delivery time'),
            'DeliveryTimeMinDesc'            => _t(ShippingMethod::class . '.DeliveryTimeMinDesc', 'Minimum delivery time in business days'),
            'DeliveryTimeMax'                => _t(ShippingMethod::class . '.DeliveryTimeMax', 'Maximum delivery time'),
            'DeliveryTimeMaxDesc'            => _t(ShippingMethod::class . '.DeliveryTimeMaxDesc', 'Maximum delivery time in business days'),
            'DeliveryTimeText'               => _t(ShippingMethod::class . '.DeliveryTimeText', 'Own text for delivery time'),
            'DeliveryTimeTextDesc'           => _t(ShippingMethod::class . '.DeliveryTimeTextDesc', 'Will be used instead of "Minimum delivery time" and "Maximum delivery time".'),
            'DateFormat'                     => _t(Tools::class . '.DATEFORMAT', 'm/d/Y'),
            'BusinessDay'                    => _t(ShippingMethod::class . '.BusinessDay', 'Business day'),
            'BusinessDays'                   => _t(ShippingMethod::class . '.BusinessDays', 'Business days'),
            'DeliveryTimePrepaymentHint'     => _t(ShippingMethod::class . '.DeliveryTimePrepaymentHint', 'when cashed'),
            'DeliveryTimePrepaymentHintReleaseDate' => _t(ShippingMethod::class . '.DeliveryTimePrepaymentHintReleaseDate', 'when cashed in time'),
            'ChooseShippingMethod'           => _t(ShippingMethod::class . '.CHOOSE_SHIPPING_METHOD', 'Please choose a shipping method'),
            'ExpectedDeliveryTime'           => _t(ShippingMethod::class . '.ExpectedDeliveryTime', 'Expected delivery time'),
            'SameDay'                        => _t(ShippingMethod::class . '.SameDay', 'Same day'),
            'SuppliedCountries'              => _t(ShippingMethod::class . '.SuppliedCountries', 'Supplied countries'),
            'Price'                          => Product::singleton()->fieldLabel('Price'),
            'Logo'                           => Page::singleton()->fieldLabel('Logo'),
        ]);
    }
    
    /**
     * Sets the summary fields.
     *
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012
     */
    public function summaryFields()
    {
        $summaryFields = [
            'Carrier.Title'             => $this->fieldLabel('Carrier'),
            'Title'                     => $this->fieldLabel('Title'),
            'activatedStatus'           => $this->fieldLabel('activatedStatus'),
            'AttributedZones'           => $this->fieldLabel('AttributedZones'),
            'AttributedCustomerGroups'  => $this->fieldLabel('CustomerGroups'),
            'priority'                  => $this->fieldLabel('priority'),
        ];
        $this->extend("updateSummaryFields", $summaryFields);
        return $summaryFields;
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
    public function singular_name()
    {
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
    public function plural_name()
    {
        return Tools::plural_name_for($this); 
    }

    /**
     * Returns an array of field/relation names (db, has_one, has_many, 
     * many_many, belongs_many_many) to exclude from form scaffolding in
     * backend.
     * This is a performance friendly way to exclude fields.
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function excludeFromScaffolding()
    {
        $excludeFromScaffolding = [
            'Countries',
            'PaymentMethods',
            'Orders',
        ];
        
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        
        return $excludeFromScaffolding;
    }

        /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.06.2014
     */
    public function getCMSFields()
    {
        $fields = DataObjectExtension::getCMSFields($this, 'CarrierID', false);
        
        $fields->dataFieldByName('DeliveryTimeMin')->setDescription($this->fieldLabel('DeliveryTimeMinDesc'));
        $fields->dataFieldByName('DeliveryTimeMax')->setDescription($this->fieldLabel('DeliveryTimeMaxDesc'));
        $fields->dataFieldByName('DeliveryTimeText')->setDescription($this->fieldLabel('DeliveryTimeTextDesc'));

        if ($this->isInDB()) {
            $feeTable           = $fields->dataFieldByName('ShippingFees');
            $feesTableConfig    = $feeTable->getConfig();
            $exportButton       = new GridFieldExportButton();
            $exportColumsArray  = [
                'ID',
                'MaximumWeight',
                'UnlimitedWeight',
                'PriceAmount',
                'PriceCurrency',
                'ZoneID',
                'ShippingMethodID',
                'TaxID',
            ];
            $exportButton->setExportColumns($exportColumsArray);
            $feesTableConfig->addComponent($exportButton);
            $feesTableConfig->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
            $feesTableConfig->removeComponentsByType(GridFieldDeleteAction::class);
            $feesTableConfig->addComponent(new GridFieldDeleteAction());
            
            if (class_exists('\Symbiote\GridFieldExtensions\GridFieldOrderableRows')) {
                $feesTableConfig->addComponent(new \Symbiote\GridFieldExtensions\GridFieldOrderableRows('priority'));
            } elseif (class_exists('\UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows')) {
                $feesTableConfig->addComponent(new \UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows('priority'));
            }
        }
        
        return $fields;
    }
    
    /**
     * Returns whether to show this shipping method on shipping fees page.
     * 
     * @return bool
     */
    public function getShowOnShippingFeesPage()
    {
        return !$this->DoNotShowOnShippingFeesPage;
    }
    
    /**
     * Sets the shipping fee by ID and weight.
     * 
     * @param int   $shippingFeeID Shipping fee ID
     * @param float $weight        Weight
     * 
     * @return $this
     */
    public function setShippingFeeByID($shippingFeeID, $weight = null)
    {
        $shippingFee = ShippingFee::get()->byID($shippingFeeID);
        $this->setShippingFee($shippingFee, $weight);
        return $this;
    }
    
    /**
     * Sets the shipping fee by weight.
     * 
     * @param ShippingFee $shippingFee Shipping fee
     * @param float       $weight      Weight
     * 
     * @return $this
     */
    public function setShippingFee($shippingFee, $weight = null)
    {
        $this->shippingFees[(string) $weight] = $shippingFee;
        return $this;
    }
    
    /**
     * Returns the shipping fee for the given weight.
     * 
     * @param int $weight Weight in gramm to get fee for
     *
     * @return ShippingFee
     */
    public function getShippingFee($weight = null)
    {
        if (!array_key_exists((string) $weight, $this->shippingFees)) {
            $this->shippingFees[(string) $weight] = $this->detectShippingFee($weight);
        }
        return $this->shippingFees[(string) $weight];
    }
    
    /**
     * determins the right shipping fee for a shipping method depending on the
     * cart's weight and the country of the customers shipping address
     * 
     * @param int $weight Weight in gramm to get fee for
     *
     * @return ShippingFee
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.04.2018
     */
    public function detectShippingFee($weight = null)
    {
        $fee = false;
        $this->extend('onBeforeDetectShippingFee', $fee, $weight);
        if ($fee !== false) {
            return $fee;
        }
        if (is_null($weight)) {
            if (!Customer::currentUser()
             || !Customer::currentUser()->getCart()
            ) {
                return $fee;
            }
            $weight = Customer::currentUser()->getCart()->getWeightTotal();
        }

        $shippingCountry = $this->getShippingCountry();
        if (is_null($shippingCountry)) {
            $shippingAddress = $this->getShippingAddress();
            if (is_null($shippingAddress)
             && method_exists(Controller::curr(), 'getShippingAddress')
            ) {
                $shippingAddress = Controller::curr()->getShippingAddress();
                $this->setShippingAddress($shippingAddress);
            }
            if ($shippingAddress instanceof Address) {
                $shippingCountry = $shippingAddress->Country();
                $this->setShippingCountry($shippingCountry);
            }
        }
        
        if ($shippingCountry instanceof Country) {
            $zones = Zone::getZonesFor($shippingCountry->ID);
            
            if ($zones->exists()) {
                $zoneMap            = $zones->map('ID','ID');
                $zoneIDs            = $zoneMap->toArray();
                $zoneIDsAsString    = "'" . implode("','", $zoneIDs) . "'";
                $filter = [
                    "ShippingMethodID" => $this->ID,
                ];
                $fees = ShippingFee::get()
                        ->filter($filter)
                        ->where(
                                sprintf(
                                        '("MaximumWeight" >= ' . $weight . ' OR "UnlimitedWeight" = 1) AND "ZoneID" IN (%s)',
                                        $zoneIDsAsString
                                )
                        )
                        ->sort('PostPricing, PriceAmount');
                if ($fees->exists()) {
                    $fee = $fees->first();
                }
            }
        }
        $this->extend('onAfterDetectShippingFee', $fee);
        return $fee;
    }
    
    /**
     * getter for the shipping methods title
     *
     * @return string the title in the corresponding front end language
     */
    public function getDescription()
    {
        return $this->getTranslationFieldValue('Description');
    }
    
    /**
     * getter for the shipping methods DescriptionForShippingFeesPage
     *
     * @return string the title in the corresponding front end language
     */
    public function getDescriptionForShippingFeesPage()
    {
        return $this->getTranslationFieldValue('DescriptionForShippingFeesPage');
    }
    
    /**
     * getter for the shipping methods title
     *
     * @return string the title in the corresponding front end language 
     */
    public function getTitle()
    {
        return $this->getTranslationFieldValue('Title');
    }

    /**
     * pseudo attribute which can be called with $this->TitleWithCarrierAndFee
     *
     * @return string carrier + title + fee
     */
    public function getTitleWithCarrierAndFee()
    {
        if ($this->getShippingFee()) {
            return $this->getShippingFee()->getFeeWithCarrierAndShippingMethod();
        } else {
            return false;
        }
    }
    
    /**
     * pseudo attribute
     *
     * @return false|string
     */
    public function getTitleWithCarrier()
    {
        if ($this->Carrier()) {
            return $this->Carrier()->Title . " - " . $this->Title;
        }
        return false;
    }
    
    /**
     * Returns the delivery time as string.
     * 
     * @param bool   $forceDisplayInDays Force displaying the delivery time in days
     * @param double $weight             Weight of the stuff to ship
     * 
     * @return string
     */
    public function getDeliveryTime($forceDisplayInDays = false, $weight = null)
    {
        $deliveryTime = self::get_delivery_time_for($this->getShippingFee($weight), $forceDisplayInDays);
        if (empty($deliveryTime)) {
            $deliveryTime = self::get_delivery_time_for($this, $forceDisplayInDays);
        }
        return $deliveryTime;
    }
    
    /**
     * Returns the delivery time min as a date string.
     * 
     * @param double $weight        Weight of the stuff to ship
     * @param bool   $fullMonthName Set to true to show the full month name
     * @param bool   $forceYear     Set to true to force showing the year
     * @param bool   $withWeekDay   Set to true to show the name of the week day
     * 
     * @return string
     */
    public function getDeliveryTimeMinDate($weight = null, $fullMonthName = true, $forceYear = false, $withWeekDay = true)
    {
        $deliveryTimeMinDate = null;
        $deliveryTimeMin     = (int) $this->getShippingFee($weight)->DeliveryTimeMin;
        $deliveryTimeMax     = (int) $this->getShippingFee($weight)->DeliveryTimeMax;
        if ($deliveryTimeMin === 0
         && $deliveryTimeMax === 0) {
            $deliveryTimeMin = (int) $this->DeliveryTimeMin;
            $deliveryTimeMax = (int) $this->DeliveryTimeMax;
        }
        if ($deliveryTimeMax > 0) {
            $deliveryTimeMinDate = Tools::getDateNice(date(static::singleton()->fieldLabel('DateFormat'), time() + (DateTools::addOffDaysToBusinessDays($deliveryTimeMin, $this->config()->include_saturdays_in_delivery_time)*60*60*24)), $fullMonthName, $forceYear, $withWeekDay);
        }
        return $deliveryTimeMinDate;
    }
    
    /**
     * Returns the delivery time max as a date string.
     * 
     * @param double $weight        Weight of the stuff to ship
     * @param bool   $fullMonthName Set to true to show the full month name
     * @param bool   $forceYear     Set to true to force showing the year
     * @param bool   $withWeekDay   Set to true to show the name of the week day
     * 
     * @return string
     */
    public function getDeliveryTimeMaxDate($weight = null, $fullMonthName = true, $forceYear = false, $withWeekDay = true)
    {
        $deliveryTimeMaxDate = null;
        $deliveryTimeMin     = (int) $this->getShippingFee($weight)->DeliveryTimeMin;
        $deliveryTimeMax     = (int) $this->getShippingFee($weight)->DeliveryTimeMax;
        if ($deliveryTimeMax === 0) {
            $deliveryTimeMin = (int) $this->DeliveryTimeMin;
            $deliveryTimeMax = (int) $this->DeliveryTimeMax;
        }
        if ($deliveryTimeMax === 0) {
            $deliveryTimeMax = $deliveryTimeMin;
        }
        if ($deliveryTimeMax > 0) {
            $deliveryTimeMaxDate = Tools::getDateNice(date(static::singleton()->fieldLabel('DateFormat'), time() + (DateTools::addOffDaysToBusinessDays($deliveryTimeMax, $this->config()->include_saturdays_in_delivery_time)*60*60*24)), $fullMonthName, $forceYear, $withWeekDay);
        }
        return $deliveryTimeMaxDate;
    }
    
    /**
     * Returns the delivery time as string.
     * 
     * @param int    $minDays            Delivery time minimum days
     * @param int    $maxDays            Delivery time maximum days
     * @param string $text               Delivery time text
     * @param bool   $forceDisplayInDays Force displaying the delivery time in days
     * 
     * @return string
     */
    public static function get_delivery_time($minDays, $maxDays, $text = '', $forceDisplayInDays = false)
    {
        // override $forceDisplayInDays if set via config
        if (true === self::config()->get('always_force_display_in_days')) {
            $forceDisplayInDays = true;
        }

        $deliveryTime = '';
        if (!empty($text)) {
            $deliveryTime = $text;
        } elseif ($minDays > 0
               || $maxDays > 0
        ) {
            if ((self::isInCheckoutContextWithPrepayment()
              && !(self::isInCheckoutContextWithReleaseDate()
               && $minDays > 3))
             || $forceDisplayInDays
            ) {
                $deliveryTime = $minDays;
                if ($minDays == 0) {
                    $deliveryTime = static::singleton()->fieldLabel('SameDay');
                }
                if ($maxDays > $minDays) {
                    $deliveryTime .= ' - ';
                    $deliveryTime .= $maxDays;
                }
                if ($deliveryTime === '1'
                 || $maxDays === '1'
                ) {
                    $deliveryTime .= ' ' . static::singleton()->fieldLabel('BusinessDay');
                } else {
                    $deliveryTime .= ' ' . static::singleton()->fieldLabel('BusinessDays');
                }
                $deliveryTime .= ' ' . static::singleton()->fieldLabel('DeliveryTimePrepaymentHint');
            } else {
                $deliveryTime  = Tools::getDateNice(date(static::singleton()->fieldLabel('DateFormat'), time() + (DateTools::addOffDaysToBusinessDays($minDays, self::config()->include_saturdays_in_delivery_time)*60*60*24)), true, true, true);
                if ($maxDays > 0
                 && $maxDays > $minDays
                ) {
                    $deliveryTime .= ' - ';
                    $deliveryTime .= Tools::getDateNice(date(static::singleton()->fieldLabel('DateFormat'), time() + (DateTools::addOffDaysToBusinessDays($maxDays, self::config()->include_saturdays_in_delivery_time)*60*60*24)), true, true, true);
                }
                if (self::isInCheckoutContextWithPrepayment()) {
                    $deliveryTime .= ' ' . static::singleton()->fieldLabel('DeliveryTimePrepaymentHintReleaseDate');
                }
            }
        }
        return $deliveryTime;
    }
    
    /**
     * Returns the delivery time as string.
     * 
     * @param DataObject $context            Context object to get delivery time for
     * @param bool       $forceDisplayInDays Force displaying the delivery time in days
     * 
     * @return string
     */
    public static function get_delivery_time_for($context, $forceDisplayInDays = false)
    {
        if (is_null($context)
            || $context == false
        ) {
            return false;
        }
        return self::get_delivery_time($context->DeliveryTimeMin, $context->DeliveryTimeMax, $context->DeliveryTimeText, $forceDisplayInDays);
    }
    
    /**
     * Returns whether the current application context is in checkout with 
     * prepayment as payment method.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2014
     */
    protected static function isInCheckoutContextWithPrepayment()
    {
        $isPrepayment = false;
        if (Controller::curr() instanceof CheckoutStepController) {
            $paymentMethod = Customer::currentUser()->getCart()->getPaymentMethod();
            if (class_exists('SilverCart\\Prepayment\\Model\\Prepayment')
                && $paymentMethod instanceof \SilverCart\Prepayment\Model\Prepayment
                && $paymentMethod->PaymentChannel == 'prepayment'
            ) {
                $isPrepayment = true;
            }
        }
        return $isPrepayment;
    }
    
    /**
     * Returns whether the current application context is in checkout while 
     * having a product with a release date in shopping cart.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.10.2018
     */
    protected static function isInCheckoutContextWithReleaseDate()
    {
        $hasReleaseDateInCheckout = false;
        if (Controller::curr() instanceof CheckoutStepController) {
            $hasReleaseDateInCheckout = Customer::currentUser()->getCart()->HasProductWithReleaseDates();
        }
        return $hasReleaseDateInCheckout;
    }

    /**
     * Returns the attributed customer groups as string (limited to 150 chars).
     * 
     * @param string $dbField Db field to use to display
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2012
     */
    public function AttributedCustomerGroups($dbField = "Title")
    {
        return Tools::AttributedDataObject($this->CustomerGroups(), $dbField);
    }

    /**
     * Returns the attributed zones as string (limited to 150 chars).
     * 
     * @param string $dbField Db field to use to display
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2012
     */
    public function AttributedZones($dbField = "Title")
    {
        return Tools::AttributedDataObject($this->Zones(), $dbField);
    }

    /**
     * Returns the attributed zones as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.01.2012
     */
    public function AttributedZoneIDs()
    {
        return $this->AttributedZones('ID');
    }

    /**
     * Returns the attributed payment methods as string (limited to 150 chars).
     * 
     * @param string $dbField Db field to use to display
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.02.2013
     */
    public function AttributedPaymentMethods($dbField = "Name")
    {
        return Tools::AttributedDataObject($this->PaymentMethods(), $dbField);
    }

    /**
     * Returns the activation status as HTML-Checkbox-Tag.
     *
     * @return CheckboxField
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.06.2012
     */
    public function activatedStatus()
    {
        $checkboxField = CheckboxField::create('isActivated' . $this->ID, 'isActived', $this->isActive);
        $checkboxField->setReadonly(true);
        $checkboxField->setDisabled(true);
        return $checkboxField;
    }

    /**
     * Checks whether this shipping method has a fee with activated post pricing
     *
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2012
     */
    public function hasFeeWithPostPricing()
    {
        $hasFeeWithPostPricing = false;
        foreach ($this->ShippingFees() as $shippingFee) {
            if ($shippingFee->PostPricing) {
                $hasFeeWithPostPricing = true;
                break;
            }
        }
        return $hasFeeWithPostPricing;
    }
    
    /**
     * Returns allowed shipping methods. Those are active
     * 
     * @param Carrier $carrier         Carrier to get shipping methods for
     * @param Address $shippingAddress Address to get shipping methods for
     *
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.09.2018
     */
    public static function getAllowedShippingMethods($carrier = null, $shippingAddress = null)
    {
        $extendableShippingMethod       = ShippingMethod::singleton();
        $allowedShippingMethodsArray    = [];
        $shippingMethods                = self::getAllowedShippingMethodsBase($carrier);

        if ($shippingMethods instanceof SS_List
         && $shippingMethods->exists()
        ) {
            foreach ($shippingMethods as $shippingMethod) {
                /* @var $shippingMethod ShippingMethod */
                if (!is_null($shippingAddress)) {
                    $shippingMethod->setShippingAddress($shippingAddress);
                }
                // If there is no shipping fee defined for this shipping
                // method we don't want to show it.
                if ($shippingMethod->getShippingFee() !== false) {
                    $allowedShippingMethodsArray[] = $shippingMethod;
                }
            }
        }
        
        $allowedShippingMethods = ArrayList::create($allowedShippingMethodsArray);
        
        $extendableShippingMethod->extend('updateAllowedShippingMethods', $allowedShippingMethods);
        
        return $allowedShippingMethods;
    }
    
    /**
     * Returns allowed shipping methods. Those are active
     * 
     * @param Carrier $carrier Carrier to get shipping methods for
     *
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2012
     */
    public static function getAllowedShippingMethodsForOverview($carrier = null)
    {
        $shippingMethods = self::getAllowedShippingMethodsBase($carrier);
        return $shippingMethods;
    }
    
    /**
     * Returns allowed shipping methods. Those are active
     * 
     * @param Carrier $carrier Carrier to get shipping methods for
     *
     * @return DataList
     */
    public static function getAllowedShippingMethodsBase(Carrier $carrier = null) : DataList
    {
        $extendedFilter = "";
        $shippingTable  = ShippingMethod::config()->table_name;
        if ($carrier instanceof Carrier
         && $carrier->exists()
        ) {
            $extendedFilter = " AND {$shippingTable}.CarrierID = {$carrier->ID}";
        }
        $customerGroups = Customer::getCustomerGroups();
        if ($customerGroups instanceof SS_List
         && $customerGroups->exists()
        ) {
            $customerGroupIDs = implode(',', $customerGroups->map('ID', 'ID')->toArray());
            $filter           = "({$shippingTable}_CustomerGroups.GroupID IN ({$customerGroupIDs}) OR {$shippingTable}_CustomerGroups.SilvercartShippingMethodID IS NULL){$extendedFilter}";
            $joinTable        = "{$shippingTable}_CustomerGroups";
            $joinOnClause     = "{$joinTable}.{$shippingTable}ID = {$shippingTable}.ID";
            $shippingMethods  = ShippingMethod::get()
                    ->filter('isActive', true)
                    ->leftJoin($joinTable, $joinOnClause)
                    ->where($filter);
        } else {
            $filter          = "({$shippingTable}_CustomerGroups.SilvercartShippingMethodID IS NULL){$extendedFilter}";
            $joinTable       = "{$shippingTable}_CustomerGroups";
            $joinOnClause    = "{$joinTable}.{$shippingTable}ID = {$shippingTable}.ID";
            $shippingMethods = ShippingMethod::get()
                    ->filter('isActive', true)
                    ->leftJoin($joinTable, $joinOnClause)
                    ->where($filter);
        }
        return $shippingMethods;
    }
    
    /**
     * Returns all allowed shipping fees in the given products, countries
     * and customer groups context.
     *
     * @param Product $product       Product to get fee for
     * @param Country $country       Country to get fee for
     * @param Group   $customerGroup Customer group to get fee for
     * @param bool    $excludePickup Set to true to exclude pickups
     * 
     * @return ArrayList
     */
    public static function getAllowedShippingFeesFor(Product $product, Country $country, Group $customerGroup, $excludePickup = false)
    {
        $extendableShippingMethod   = ShippingMethod::singleton();
        
        $shippingTable  = Tools::get_table_name(ShippingMethod::class);
        $addToFilter = '';
        if ($excludePickup) {
            $addToFilter = ' AND ' . $shippingTable . '.isPickup = 0';
        }
        
        $filter = sprintf(
            '"' . $shippingTable . '"."isActive" = 1 AND ("' . $shippingTable . '_CustomerGroups"."GroupID" IN (%s) OR "' . $shippingTable . '"."ID" NOT IN (%s))%s',
            $customerGroup->ID,
            'SELECT "' . $shippingTable . '_CustomerGroups"."' . $shippingTable . 'ID" FROM "' . $shippingTable . '_CustomerGroups"',
            $addToFilter
        );
         
        $joinTable      =  $shippingTable . '_CustomerGroups';
        $joinOnClause   = '"' . $shippingTable . '_CustomerGroups"."' . $shippingTable . 'ID" = "' . $shippingTable . '"."ID"';

        $shippingMethods = ShippingMethod::get()
                ->leftJoin($joinTable, $joinOnClause)
                ->where($filter);
        
        $extendableShippingMethod->extend('updateAllowedShippingFeesFor', $shippingMethods, $product);
        
        $shippingFees = ArrayList::create();
        
        if ($shippingMethods->exists()) {
            foreach ($shippingMethods as $shippingMethod) {
                $shippingMethod->setShippingCountry($country);
                $shippingFee = $shippingMethod->getShippingFee($product->Weight);
                if ($shippingFee) {
                    $shippingFees->push($shippingFee);
                }
            }
        }
        
        return $shippingFees;
    }
    
    /**
     * Returns the first allowed shipping fee in the given products, countries
     * and customer groups context.
     *
     * @param Product $product       Product to get fee for
     * @param Country $country       Country to get fee for
     * @param Group   $customerGroup Customer group to get fee for
     * @param bool    $excludePickup Set to true to exclude pickups
     * 
     * @return ShippingFee
     */
    public static function getAllowedShippingFeeFor(Product $product, Country $country, Group $customerGroup, $excludePickup = false)
    {
        $shippingFees = self::getAllowedShippingFeesFor($product, $country, $customerGroup, $excludePickup);
        return $shippingFees->First();
    }

    /**
     * Filters the given shipping methods by default permission criteria
     * 
     * @param ShippingMethod $shippingMethods Shipping methods to filter
     *
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.04.2012
     */
    public static function filterShippingMethods($shippingMethods)
    {
        $allowedShippingMethods = new ArrayList();
        $customerGroups         = Customer::getCustomerGroups();
        foreach ($shippingMethods as $shippingMethod) {
            foreach ($customerGroups as $customerGroup) {
                if ($shippingMethod->CustomerGroups()->find('ID', $customerGroup->ID)
                    || $shippingMethod->CustomerGroups()->count() == 0
                ) {
                    $allowedShippingMethods->push($shippingMethod);
                    break;
                }
            }
        }
        return $allowedShippingMethods;
    }
    
    /**
     * Returns the shipping address
     *
     * @return Address 
     */
    public function getShippingAddress()
    {
        if ($this->shippingAddress === null) {
            $ctrl = Controller::curr();
            if ($ctrl->hasMethod('getShippingAddress')) {
                $this->setShippingAddress($ctrl->getShippingAddress());
                if ($this->shippingCountry === null
                 && $this->shippingAddress instanceof Address
                ) {
                    $this->setShippingCountry($this->shippingAddress->Country());
                }
            }
        }
        return $this->shippingAddress;
    }

    /**
     * Sets the shipping address
     *
     * @param Address $shippingAddress Shipping address
     * 
     * @return void
     */
    public function setShippingAddress($shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
    }
    
    /**
     * Returns the shipping country
     *
     * @return Country
     */
    public function getShippingCountry()
    {
        return $this->shippingCountry;
    }

    /**
     * Sets the shipping country
     *
     * @param Country $shippingCountry Shipping country
     * 
     * @return void
     */
    public function setShippingCountry($shippingCountry)
    {
        $this->shippingCountry = $shippingCountry;
    }

}