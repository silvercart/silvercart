<?php

namespace SilverCart\Model\Shipment;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\FormFields\FieldGroup;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Order\ShoppingCart;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\Tax;
use SilverCart\Model\Shipment\ShippingMethod;
use SilverCart\Model\Shipment\Zone;
use SilverCart\ORM\FieldType\DBMoney as SilverCartDBMoney;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBMoney;
use SilverStripe\ORM\Filters\LessThanFilter;
use SilverStripe\ORM\HasManyList;
use SilverStripe\ORM\Search\SearchContext;

/**
 * A carrier has many shipping fees.
 * They mainly depend on the freights weight.
 *
 * @package SilverCart
 * @subpackage Model_Shipment
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property float             $MaximumWeight               Maximum Weight
 * @property bool              $UnlimitedWeight             Fee has unlimited weight?
 * @property SilverCartDBMoney $Price                       Price
 * @property bool              $PostPricing                 Fee has post pricing?
 * @property bool              $freeOfShippingCostsDisabled Fee is never for free?
 * @property SilverCartDBMoney $freeOfShippingCostsFrom     Cart total price for free shipping costs.
 * @property int               $priority                    Priority
 * @property int               $DeliveryTimeMin             Minimum delivery time
 * @property int               $DeliveryTimeMax             Maximum delivery time
 * @property string            $DeliveryTimeText            Delivery time text
 * 
 * @property string $PriceFormatted                Price formatted
 * @property string $PriceFormattedPlain           Price formatted plain
 * @property string $AttributedShippingMethods     Attributed shipping methods
 * @property string $MaximumWeightLimitedOrNot     Maximum weight or unlimited text
 * @property string $PriceAmount                   Price amount
 * @property string $PriceCurrency                 Price currency
 * @property string $MaximumWeightNice             Maximum weight with unit
 * @property string $MaximumWeightUnitAbbreviation Maximum weight unit abbreviation
 * 
 * @method Zone           Zone()           Returns the related Zone.
 * @method ShippingMethod ShippingMethod() Returns the related ShippingMethod.
 * 
 * @method HasManyList Orders() Returns a list of related Orders.
 */
class ShippingFee extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = [
        'MaximumWeight'                 => 'Float',
        'UnlimitedWeight'               => 'Boolean',
        'Price'                         => SilverCartDBMoney::class,
        'PostPricing'                   => 'Boolean',
        'freeOfShippingCostsDisabled'   => 'Boolean',
        'freeOfShippingCostsFrom'       => SilverCartDBMoney::class,
        'priority'                      => 'Int',
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
        'Zone'           => Zone::class,
        'ShippingMethod' => ShippingMethod::class,
        'Tax'            => Tax::class,
    ];
    /**
     * Has-many Relationship.
     *
     * @var array
     */
    private static $has_many = [
        'Orders' => Order::class,
    ];
    /**
     * Virtual database fields.
     *
     * @var array
     */
    private static $casting = [
        'PriceFormatted'                  => 'Varchar(20)',
        'PriceFormattedPlain'             => 'Varchar(20)',
        'AttributedShippingMethods'       => 'Varchar(255)',
        'MaximumWeightLimitedOrNot'       => 'Varchar(255)',
        'PriceAmount'                     => 'Varchar(255)',
        'PriceCurrency'                   => 'Varchar(255)',
        'MaximumWeightNice'               => 'Varchar(255)',
        'MaximumWeightUnitAbbreviation'   => 'Varchar(2)',
        'FeeWithCarrierAndShippingMethod' => 'Text',
    ];
    /**
     * Default sort field and direction
     *
     * @var string
     */
    private static $default_sort = "priority DESC";
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartShippingFee';
    /**
     * Marker to check whether the CMS fields are called or not
     *
     * @var bool 
     */
    protected $getCMSFieldsIsCalled = false;
    /**
     * Cached Tax object. The related tax object will be stored in
     * this property after its first call.
     *
     * @var Tax
     */
    protected $cachedTax = null;
    
    /**
     * Returns the translated singular name of the object.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this);
    }

    /**
     * i18n for summary fields
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.02.2013
     */
    public function summaryFields() : array
    {
        $summaryFields = [
            'Zone.Title'                => $this->fieldLabel('Zone'),
            'AttributedShippingMethods' => $this->fieldLabel('AttributedShippingMethods'),
            'MaximumWeightLimitedOrNot' => $this->fieldLabel('MaximumWeight'),
            'PriceFormattedPlain'       => $this->fieldLabel('Price'),
            'priority'                  => $this->fieldLabel('priority'),
        ];
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }

    /**
     * i18n for field labels
     *
     * @param bool $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'UnlimitedWeight'               => _t(ShippingFee::class . '.UNLIMITED_WEIGHT_LABEL', 'Unlimited Maximum Weight'),
            'Unlimited'                     => _t(ShippingFee::class . '.UNLIMITED_WEIGHT', 'unlimited'),
            'Price'                         => _t(ShippingFee::class . '.COSTS', 'Costs'),
            'Zone'                          => ShippingMethod::singleton()->fieldLabel('ForZones'),
            'Zone.Title'                    => ShippingMethod::singleton()->fieldLabel('ForZones'),
            'ZoneWithDescription'           => _t(ShippingFee::class . '.ZONE_WITH_DESCRIPTION', 'zone (only carrier\'s zones available)'),
            'AttributedShippingMethods'     => _t(ShippingFee::class . '.ATTRIBUTED_SHIPPINGMETHOD', 'Attributed shipping method'),
            'Tax'                           => Tax::singleton()->singular_name(),
            'PostPricing'                   => _t(ShippingFee::class . '.POST_PRICING', 'Pricing after order'),
            'PostPricingInfo'               => _t(ShippingFee::class . '.POST_PRICING_INFO', 'Manual calculation of shipping fees after order.'),
            'Orders'                        => Order::singleton()->plural_name(),
            'EmptyString'                   => _t(ShippingFee::class . '.EMPTYSTRING_CHOOSEZONE', '--choose zone--'),
            'freeOfShippingCostsDisabled'   => _t(ShippingFee::class . '.FREEOFSHIPPINGCOSTSDISABLED', 'Disable free shipping for this fee'),
            'freeOfShippingCostsFrom'       => _t(ShippingFee::class . '.FREEOFSHIPPINGCOSTSFROM', 'Free of shipping costs from (overwrites country specific and global configuration)'),
            'FreeFrom'                      => _t(ShippingFee::class . '.FreeFrom', 'free from'),
            'ShippingMethod'                => ShippingMethod::singleton()->singular_name(),
            'priority'                      => Tools::field_label('Priority'),
            'DeliveryTime'                  => ShippingMethod::singleton()->fieldLabel('DeliveryTime'),
            'DeliveryTimeMin'               => ShippingMethod::singleton()->fieldLabel('DeliveryTimeMin'),
            'DeliveryTimeMinDesc'           => ShippingMethod::singleton()->fieldLabel('DeliveryTimeMinDesc'),
            'DeliveryTimeMax'               => ShippingMethod::singleton()->fieldLabel('DeliveryTimeMax'),
            'DeliveryTimeMaxDesc'           => ShippingMethod::singleton()->fieldLabel('DeliveryTimeMaxDesc'),
            'DeliveryTimeText'              => ShippingMethod::singleton()->fieldLabel('DeliveryTimeText'),
            'DeliveryTimeTextDesc'          => ShippingMethod::singleton()->fieldLabel('DeliveryTimeTextDesc'),
            'DeliveryTimeHint'              => _t(ShippingFee::class . '.DeliveryTimeHint', 'Optional delivery time. Overwrites the shipping methods values.'),
        ]);
    }

        /**
     * Returns the maximum weight or a hint, that this fee is unlimited.
     *
     * @return string
     */
    public function getMaximumWeightLimitedOrNot() : string
    {
        $maximumWeightLimitedOrNot = (string) $this->MaximumWeight;
        if ($this->UnlimitedWeight) {
            $maximumWeightLimitedOrNot = $this->fieldLabel('UnlimitedWeight');
        }
        return $maximumWeightLimitedOrNot;
    }

    
    /**
     * Set a custom search context for fields like "greater than", "less than",
     * etc.
     *
     * @return SearchContext
     */
    public function getDefaultSearchContext() : SearchContext
    {
        $fields  = $this->scaffoldSearchFields();
        $filters = [
            'MaximumWeight' => LessThanFilter::create('MaximumWeight')
        ];
        return SearchContext::create(
            get_class($this),
            $fields,
            $filters
        );
    }

    /**
     * Customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->getCMSFieldsIsCalled = true;
        $this->beforeUpdateCMSFields(function($fields) {
            $postPricingField = $fields->dataFieldByName('PostPricing');
            $postPricingField->setTitle($postPricingField->Title() . ' (' . $this->fieldLabel('PostPricingInfo') . ')');
            $maximumWeightField = $fields->dataFieldByName('MaximumWeight');
            $maximumWeightField->setRightTitle($this->fieldLabel('MaximumWeightDesc'));

            Tax::presetDropdownWithDefault($fields->dataFieldByName('TaxID'), $this);

            $fieldGroup = FieldGroup::create('ShippingFeeGroup', '', $fields);
            $fieldGroup->push(          $fields->dataFieldByName('Price'));
            $fieldGroup->pushAndBreak(  $fields->dataFieldByName('TaxID'));
            $fieldGroup->pushAndBreak(  $postPricingField);
            // only the carriers zones must be selectable
            $zoneTable      = Tools::get_table_name(Zone::class);
            $carrierTable   = Tools::get_table_name(Carrier::class);
            $leftJoinTable  = $zoneTable . '_Carriers';
            $leftJoinOn     = '"' . $zoneTable . '"."ID" = "' . $zoneTable . '_Carriers"."' . $zoneTable . 'ID"';
            $where          = sprintf(
                                '"' . $zoneTable . '_Carriers"."' . $carrierTable . 'ID" = %s',
                                $this->ShippingMethod()->Carrier()->ID
            );
            $zones          = Zone::get()
                                ->leftJoin($leftJoinTable, $leftJoinOn)
                                ->where($where);
            if ($zones->exists()) {
                $zonesMap   = $zones->map('ID', 'Title');
                $zonesField = DropdownField::create(
                        'ZoneID',
                        $this->fieldLabel('ZoneWithDescription'),
                        $zonesMap->toArray()
                );
                $fieldGroup->push($zonesField);
            }
            $fieldGroup->breakAndPush(  $fields->dataFieldByName('freeOfShippingCostsDisabled'));
            $fieldGroup->breakAndPush(  $fields->dataFieldByName('freeOfShippingCostsFrom'));

            $fields->dataFieldByName('DeliveryTimeMin')->setRightTitle($this->fieldLabel('DeliveryTimeMinDesc'));
            $fields->dataFieldByName('DeliveryTimeMax')->setRightTitle($this->fieldLabel('DeliveryTimeMaxDesc'));
            $fields->dataFieldByName('DeliveryTimeText')->setRightTitle($this->fieldLabel('DeliveryTimeTextDesc'));

            $parentDeliveryTime = '';
            if ($this->ShippingMethod()->exists()) {
                $parentDeliveryTime = ShippingMethod::get_delivery_time_for($this->ShippingMethod(), true);
                if (!empty($parentDeliveryTime)) {
                    $parentDeliveryTime = "<br/>({$parentDeliveryTime})";
                }
            }

            $fieldGroup->pushAndBreak(  LiteralField::create('DeliveryTimeHint', '<strong>' . $this->fieldLabel('DeliveryTimeHint') . $parentDeliveryTime . '</strong>'));
            $fieldGroup->push(          $fields->dataFieldByName('DeliveryTimeMin'));
            $fieldGroup->pushAndBreak(  $fields->dataFieldByName('DeliveryTimeMax'));
            $fieldGroup->pushAndBreak(  $fields->dataFieldByName('DeliveryTimeText'));

            $fields->removeByName('priority');
            $fields->insertAfter('ShippingMethodID', $maximumWeightField);
            $fields->insertAfter('ShippingMethodID', $fields->dataFieldByName('UnlimitedWeight'));
            $fields->addFieldToTab('Root.Main', $fieldGroup);
        });
        return parent::getCMSFields();
    }

    /**
     * Returns the Price for the current detail product formatted by locale.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.07.2013
     */
    public function PriceFormattedForDetailViewProduct() : string
    {
        $country = null;
        $amount  = null;
        if (Controller::curr()->hasMethod('getDetailViewProduct')) {
            $product = Controller::curr()->getDetailViewProduct();
            if ($product instanceof Product) {
                $amount = $product->getPrice()->getAmount();
            }
        }
        if ($this->Zone()->Countries()->count() === 1) {
            $country = $this->Zone()->Countries()->first();
        }
        $priceFormatted = '';
        if ($this->PostPricing) {
            $priceFormatted = '---';
        } else {
            $priceObj = DBMoney::create();
            $priceObj->setAmount($this->getPriceAmount(false, $amount, $country));
            $priceObj->setCurrency($this->getPriceCurrency());
            $priceFormatted = $priceObj->Nice();
        }
        return $priceFormatted;
    }

    /**
     * Returns the Price formatted by locale.
     * 
     * @param bool $plain Set to true to load the price amount without any manipulation
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2012
     */
    public function PriceFormatted(bool $plain = false) : string
    {
        $priceFormatted = '';
        if ($this->PostPricing) {
            $priceFormatted = '---';
        } else {
            $priceObj = DBMoney::create();
            $priceObj->setAmount($this->getPriceAmount($plain));
            $priceObj->setCurrency($this->getPriceCurrency());
            $priceFormatted = $priceObj->Nice();
        }
        return $priceFormatted;
    }

    /**
     * Returns the Price formatted by locale.
     *
     * @return string
     */
    public function getPriceFormatted() : string
    {
        return $this->PriceFormatted(false);
    }

    /**
     * Returns the Price formatted by locale.
     *
     * @return string
     */
    public function getPriceFormattedPlain() : string
    {
        return $this->PriceFormatted(true);
    }

    /**
     * Returns the attributed shipping methods as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function AttributedShippingMethods() : string
    {
        return Tools::AttributedDataObject($this->ShippingMethod());
    }

    /**
     * Returns the related Tax object.
     * Provides an extension hook to update the tax object by decorator.
     * 
     * @return Tax
     */
    public function Tax() : Tax
    {
        if (is_null($this->cachedTax)) {
            $this->cachedTax = $this->getComponent('Tax');
            if (!$this->getCMSFieldsIsCalled) {
                $this->extend('updateTax', $this->cachedTax);
            }
        }
        return $this->cachedTax;
    }

    /**
     * Returns the tax rate for this fee.
     * 
     * @return int
     */
    public function getTaxRate()
    {
        $taxRate = ShoppingCart::get_most_valuable_tax_rate();
        if ($taxRate === false) {
            $taxRate = $this->Tax()->getTaxRate();
        }
        return $taxRate;
    }

    /**
     * returns the tax amount included in $this
     * 
     * @param float $price Price to get tax amount for (if empty $this->getPriceAmount() is used)
     *
     * @return float
     */
    public function getTaxAmount($price = null) : float
    {
        if (is_null($price)) {
            $price = $this->Price->getAmount();
        }
        $taxRate   = $this->getTaxRate();
        $taxAmount = $price - ($price / (100 + $taxRate) * 100);

        if (Customer::currentUser()
         && Customer::currentUser()->ShoppingCartID > 0
        ) {
            $silvercartShoppingCart = Customer::currentUser()->getCart();
            $shoppingCartValue      = $silvercartShoppingCart->getTaxableAmountWithoutFees();
            $amountToGetFeeFor      = $shoppingCartValue->getAmount();
            $countryToGetFeeFor     = $this->ShippingMethod()->getShippingCountry();
            if ($this->ShippingIsFree($amountToGetFeeFor, $countryToGetFeeFor)) {
                $taxAmount = 0.0;
            }
        }
        return $taxAmount;
    }
    
    /**
     * helper method for displaying a fee in a dropdown menu
     *
     * @return false|string [carrier] - [shipping method] (+[fee amount][currency])
     */
    public function getFeeWithCarrierAndShippingMethod()
    {
        if ($this->ShippingMethod()) {
            $carrier = "";
            if ($this->ShippingMethod()->Carrier()) {
                $carrier = $this->ShippingMethod()->Carrier()->Title;
            }
            $shippingMethod            = $this->ShippingMethod()->Title;
            $shippingFeeAmountAsString = $this->PriceFormatted();
            if ($this->PostPricing) {
                $shippingFeeAmountAsString = $this->fieldLabel('PostPricing');
            } else {
                $shippingFeeAmountAsString = "+ {$this->PriceFormatted()}";
            }
            return "{$carrier} - {$shippingMethod} ({$shippingFeeAmountAsString})";
        }
        return false;
    }
    
    /**
     * Returns the price for this shipping fee.
     * 
     * @return DBMoney
     */
    public function getCalculatedPrice() : DBMoney
    {
        $priceObj = DBMoney::create();
        $priceObj->setAmount($this->getPriceAmount());
        $priceObj->setCurrency($this->getPriceCurrency());
        return $priceObj;
    }
    
    /**
     * Returns the price.
     * 
     * @return DBMoney
     */
    public function getPrice() : DBMoney
    {
        $price = $this->getField('Price');
        if (!$this->getCMSFieldsIsCalled) {
            $this->extend('updatePrice', $price);
        }
        return $price;
    }

    /**
     * Returns the prices amount
     * 
     * @param bool    $plain              Set to true to load the price amount without any manipulation
     * @param float   $amountToGetFeeFor  Amount to get fee for
     * @param Country $countryToGetFeeFor Amount to get fee for
     *
     * @return float
     */
    public function getPriceAmount(bool $plain = false, float $amountToGetFeeFor = null, Country $countryToGetFeeFor = null) : float
    {
        $price = (float) $this->Price->getAmount();
        if (!$plain) {
            if (Config::PriceType() === Config::PRICE_TYPE_NET) {
                $price = $price - $this->getTaxAmount($price);
            }
            if (Customer::currentUser()
             && Customer::currentUser()->ShoppingCartID > 0
            ) {
                $silvercartShoppingCart = Customer::currentUser()->getCart();
                $shoppingCartValue      = $silvercartShoppingCart->getTaxableAmountWithoutFees();
                if (is_null($amountToGetFeeFor)) {
                    $amountToGetFeeFor  = $shoppingCartValue->getAmount();
                }
                if (is_null($countryToGetFeeFor)) {
                    $countryToGetFeeFor = $this->ShippingMethod()->getShippingCountry();
                }
                if ($this->ShippingIsFree($amountToGetFeeFor, $countryToGetFeeFor)) {
                    $price = 0.0;
                }
            }
            $price = round($price, 2);
            $this->extend('updatePriceAmount', $price);
        } elseif (!is_null($amountToGetFeeFor)
               && !is_null($countryToGetFeeFor)
               && $this->ShippingIsFree($amountToGetFeeFor, $countryToGetFeeFor)
        ) {
            $price = 0.0;
        }
        return $price;
    }
    
    /**
     * Returns whether to use free shipping costs or not
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function UseFreeOfShippingCostsFrom() : bool
    {
        $useFreeOfShippingCostsFrom = false;
        if (!$this->freeOfShippingCostsDisabled
         && Config::UseFreeOfShippingCostsFrom()
        ) {
            $useFreeOfShippingCostsFrom = true;
        }
        return $useFreeOfShippingCostsFrom;
    }
    
    /**
     * Returns needed value for free shipping
     * 
     * @param Country $country Country to get free of shipping costs from value
     * 
     * @return DBMoney
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.09.2018
     */
    public function FreeOfShippingCostsFrom(Country $country = null) : DBMoney
    {
        if (is_null($country)) {
            $country = $this->Zone()->Countries()->first();
        }
        $freeOfShippingCostsFrom = DBMoney::create();
        if ($this->UseFreeOfShippingCostsFrom()) {
            if ($this->freeOfShippingCostsFrom->getAmount() > 0) {
                $freeOfShippingCostsFrom = $this->freeOfShippingCostsFrom;
            } else {
                $freeOfShippingCostsFrom = Config::FreeOfShippingCostsFrom($country);
            }
        }
        return $freeOfShippingCostsFrom;
    }
    
    /**
     * Returns whether shipping is free for the given amount and country
     * 
     * @param float   $amount  Amount to get free of shipping info
     * @param Country $country Country to get free of shipping info
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function ShippingIsFree(float $amount, Country $country = null) : bool
    {
        $shippingIsFree = false;
        if ($this->UseFreeOfShippingCostsFrom()
         && $this->FreeOfShippingCostsFrom($country)->getAmount() > 0
         && (float) $this->FreeOfShippingCostsFrom($country)->getAmount() <= $amount
        ) {
            $shippingIsFree = true;
        }
        return $shippingIsFree;
    }
    
    /**
     * Returns the prices currency
     *
     * @return string
     */
    public function getPriceCurrency() : string
    {
        return (string) $this->Price->getCurrency();
    }
    
    /**
     * Returns the maximum weight with unit abreviation in context of
     * Config::DisplayWeightsInKilogram().
     * 
     * @return string
     */
    public function getMaximumWeightNice() : string
    {
        $maximumWeight = $this->MaximumWeight;
        if (Config::getConfig()->WeightUnit === Config::WEIGHT_UNIT_GRAM) {
            if (Config::DisplayWeightsInKilogram()
             && $maximumWeight >= 100
            ) {
                $maximumWeightInKilo = number_format($maximumWeight / 1000, 2, ',', '.');
                $maximumWeightNice   = "{$maximumWeightInKilo} " . Config::WEIGHT_UNIT_KILOGRAM;
            } else {
                $maximumWeightNice = "{$maximumWeight} " . Config::WEIGHT_UNIT_GRAM;
            }
        } else {
            $maximumWeightNice = "{$maximumWeight} " . Config::getConfig()->WeightUnit;
        }
        return $maximumWeightNice;
    }
    
    /**
     * Returns the maximum weights unit abreviation in context of
     * Config::DisplayWeightsInKilogram().
     * Typo alias for $this->getMaximumWeightUnitAbbreviation().
     * 
     * @return string
     */
    public function getMaximumWeightUnitAbreviation() : string
    {
        return $this->getMaximumWeightUnitAbbreviation();
    }
    
    /**
     * Returns the maximum weights unit abreviation in context of
     * Config::DisplayWeightsInKilogram().
     * 
     * @return string
     */
    public function getMaximumWeightUnitAbbreviation() : string
    {
        $maximumWeightUnitAbbreviation = Config::getConfig()->WeightUnit;
        if ($maximumWeightUnitAbbreviation === Config::WEIGHT_UNIT_GRAM
         && Config::DisplayWeightsInKilogram()
        ) {
            $maximumWeightUnitAbbreviation = Config::WEIGHT_UNIT_KILOGRAM;
        }
        return $maximumWeightUnitAbbreviation;
    }
    
    /**
     * Returns the delivery time as string.
     * 
     * @param bool $forceDisplayInDays Force displaying the delivery time in days
     * 
     * @return string
     */
    public function getDeliveryTime($forceDisplayInDays = false) : string
    {
        return ShippingMethod::get_delivery_time_for($this, $forceDisplayInDays);
    }
    
    /**
     * Title to show in CMS.
     * 
     * @return string
     */
    public function getTitle() : string
    {
        $weight = $this->fieldLabel('UnlimitedWeight');
        $zone   = "";
        $free   = "";
        if (!$this->UnlimitedWeight) {
            $weight = $this->MaximumWeightNice;
        }
        if ($this->Zone()->exists()) {
            $zone = " | {$this->Zone()->Title}";
        }
        if ($this->freeOfShippingCostsFrom->getAmount() > 0) {
            $free = " | {$this->fieldLabel('FreeFrom')} {$this->freeOfShippingCostsFrom->Nice()}";
        }
        $title = "#{$this->ID}: {$this->PriceFormatted()} | {$weight}{$zone}{$free}";
        return $title;
    }
}

