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
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBMoney;
use SilverStripe\ORM\Filters\LessThanFilter;
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
 */
class ShippingFee extends DataObject {

    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = array(
        'MaximumWeight'                 => 'Float',
        'UnlimitedWeight'               => 'Boolean',
        'Price'                         => \SilverCart\ORM\FieldType\DBMoney::class,
        'PostPricing'                   => 'Boolean',
        'freeOfShippingCostsDisabled'   => 'Boolean',
        'freeOfShippingCostsFrom'       => \SilverCart\ORM\FieldType\DBMoney::class,
        'priority'                      => 'Int',
        'DeliveryTimeMin'               => 'Int',
        'DeliveryTimeMax'               => 'Int',
        'DeliveryTimeText'              => 'Varchar(256)',
    );

    /**
     * Has-one relationships.
     *
     * @var array
     */
    private static $has_one = array(
        'Zone'           => Zone::class,
        'ShippingMethod' => ShippingMethod::class,
        'Tax'            => Tax::class,
    );

    /**
     * Has-many Relationship.
     *
     * @var array
     */
    private static $has_many = array(
        'Orders' => Order::class,
    );

    /**
     * Virtual database fields.
     *
     * @var array
     */
    private static $casting = array(
        'PriceFormatted'                    => 'Varchar(20)',
        'PriceFormattedPlain'               => 'Varchar(20)',
        'AttributedShippingMethods'         => 'Varchar(255)',
        'MaximumWeightLimitedOrNot'         => 'Varchar(255)',
        'PriceAmount'                       => 'Varchar(255)',
        'PriceCurrency'                     => 'Varchar(255)',
        'MaximumWeightNice'                 => 'Varchar(255)',
        'getMaximumWeightUnitAbreviation'   => 'Varchar(2)',
    );

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
     * Returns the translated singular name of the object.
     * 
     * @return string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.06.2012
     */
    public function singular_name() {
        return Tools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object.
     * 
     * @return string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.06.2012
     */
    public function plural_name() {
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
    public function summaryFields() {
        $summaryFields = array(
            'Zone.Title'                => $this->fieldLabel('Zone'),
            'AttributedShippingMethods' => $this->fieldLabel('AttributedShippingMethods'),
            'MaximumWeightLimitedOrNot' => $this->fieldLabel('MaximumWeight'),
            'PriceFormattedPlain'       => $this->fieldLabel('Price'),
            'priority'                  => $this->fieldLabel('priority'),
        );
        
        $this->extend('updateSummaryFields', $summaryFields);
        
        return $summaryFields;
    }

    /**
     * i18n for field labels
     *
     * @param bool $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'MaximumWeight'                 => _t(ShippingFee::class . '.MAXIMUM_WEIGHT', 'Maximum weight (g)'),
                    'UnlimitedWeight'               => _t(ShippingFee::class . '.UNLIMITED_WEIGHT_LABEL', 'Unlimited Maximum Weight'),
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
                )
        );
    }

        /**
     * Returns the maximum weight or a hint, that this fee is unlimited.
     *
     * @return string
     */
    public function getMaximumWeightLimitedOrNot() {
        $maximumWeightLimitedOrNot = $this->MaximumWeight;
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
    public function getDefaultSearchContext() {
        $fields     = $this->scaffoldSearchFields();
        $filters    = array(
            'MaximumWeight' => new LessThanFilter('MaximumWeight')
        );
        return new SearchContext(
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
    public function getCMSFields() {
        $this->getCMSFieldsIsCalled = true;
        $fields = parent::getCMSFields();
        
        $postPricingField = $fields->dataFieldByName('PostPricing');
        $postPricingField->setTitle($postPricingField->Title() . ' (' . $this->fieldLabel('PostPricingInfo') . ')');
        
        Tax::presetDropdownWithDefault($fields->dataFieldByName('TaxID'), $this);
        
        $fieldGroup = new FieldGroup('ShippingFeeGroup', '', $fields);
        $fieldGroup->push(          $fields->dataFieldByName('MaximumWeight'));
        $fieldGroup->pushAndBreak(  $fields->dataFieldByName('UnlimitedWeight'));
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
            $zonesField = new DropdownField(
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
                $parentDeliveryTime = '<br/>(' . $parentDeliveryTime . ')';
            }
        }
        
        $fieldGroup->pushAndBreak(  new LiteralField('DeliveryTimeHint', '<strong>' . $this->fieldLabel('DeliveryTimeHint') . $parentDeliveryTime . '</strong>'));
        $fieldGroup->push(          $fields->dataFieldByName('DeliveryTimeMin'));
        $fieldGroup->pushAndBreak(  $fields->dataFieldByName('DeliveryTimeMax'));
        $fieldGroup->pushAndBreak(  $fields->dataFieldByName('DeliveryTimeText'));
        
        $fields->addFieldToTab('Root.Main', $fieldGroup);

        return $fields;
    }

    /**
     * Returns the Price for the current detail product formatted by locale.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.07.2013
     */
    public function PriceFormattedForDetailViewProduct() {
        $country = null;
        $amount = null;
        if (Controller::curr()->hasMethod('getDetailViewProduct')) {
            $product = Controller::curr()->getDetailViewProduct();
            if ($product instanceof Product) {
                $amount = $product->getPrice()->getAmount();
            }
        }
        
        if ($this->Zone()->Countries()->count() == 1) {
            $country = $this->Zone()->Countries()->first();
        }
        
        $priceFormatted = '';
        if ($this->PostPricing) {
            $priceFormatted = '---';
        } else {
            $priceObj = new DBMoney();
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
    public function PriceFormatted($plain = false) {
        $priceFormatted = '';
        if ($this->PostPricing) {
            $priceFormatted = '---';
        } else {
            $priceObj = new DBMoney();
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
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.07.2012
     */
    public function getPriceFormattedPlain() {
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
    public function AttributedShippingMethods() {
        return Tools::AttributedDataObject($this->ShippingMethod());
    }

    /**
     * Returns the tax rate for this fee.
     * 
     * @return int
     */
    public function getTaxRate() {
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
    public function getTaxAmount($price = null) {
        if (is_null($price)) {
            $price = $this->Price->getAmount();
        }
        $taxRate   = $this->getTaxRate();
        $taxAmount = $price - ($price / (100 + $taxRate) * 100);

        if (Customer::currentUser() &&
            Customer::currentUser()->ShoppingCartID > 0) {

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
    public function getFeeWithCarrierAndShippingMethod() {
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
                $shippingFeeAmountAsString = '+ ' . $this->PriceFormatted();
            }
            return $carrier . ' - ' . $shippingMethod . ' (' . $shippingFeeAmountAsString . ')';
        }
        return false;
    }
    
    /**
     * Returns the price for this shipping fee.
     * 
     * @return DBMoney
     */
    public function getCalculatedPrice() {
        $priceObj = new DBMoney();
        $priceObj->setAmount($this->getPriceAmount());
        $priceObj->setCurrency($this->getPriceCurrency());

        return $priceObj;
    }
    
    /**
     * Returns the price.
     * 
     * @return Money
     */
    public function getPrice() {
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
    public function getPriceAmount($plain = false, $amountToGetFeeFor = null, $countryToGetFeeFor = null) {
        $price = (float) $this->Price->getAmount();

        if (!$plain) {
            if (Config::PriceType() == 'net') {
                $price = $price - $this->getTaxAmount($price);
            }

            if (Customer::currentUser() &&
                Customer::currentUser()->ShoppingCartID > 0) {
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
        } elseif (!is_null($amountToGetFeeFor) &&
                  !is_null($countryToGetFeeFor) &&
                  $this->ShippingIsFree($amountToGetFeeFor, $countryToGetFeeFor)) {
            $price = 0.0;
        }

        return $price;
    }
    
    /**
     * Returns whether to use free shipping costs or not
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function UseFreeOfShippingCostsFrom() {
        $useFreeOfShippingCostsFrom = false;
        if (!$this->freeOfShippingCostsDisabled &&
            Config::UseFreeOfShippingCostsFrom()) {
            $useFreeOfShippingCostsFrom = true;
        }
        return $useFreeOfShippingCostsFrom;
    }
    
    /**
     * Returns needed value for free shipping
     * 
     * @param Country $country Country to get free of shipping costs from value
     * 
     * @return Money
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function FreeOfShippingCostsFrom($country) {
        $freeOfShippingCostsFrom = new DBMoney();
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
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function ShippingIsFree($amount, $country) {
        $shippingIsFree = false;
        if ($this->UseFreeOfShippingCostsFrom() &&
            $this->FreeOfShippingCostsFrom($country)->getAmount() > 0 &&
            (float) $this->FreeOfShippingCostsFrom($country)->getAmount() <= $amount) {
            $shippingIsFree = true;
        }
        return $shippingIsFree;
    }
    
    /**
     * Returns the prices currency
     *
     * @return string
     */
    public function getPriceCurrency() {
        return $this->Price->getCurrency();
    }
    
    /**
     * Returns the maximum weight with unit abreviation in context of
     * Config::DisplayWeightsInKilogram().
     * 
     * @return string
     */
    public function getMaximumWeightNice() {
        $maximumWeightInGram = $this->MaximumWeight;
        if (Config::DisplayWeightsInKilogram()) {
            $maximumWeightNice = number_format($maximumWeightInGram / 1000, 2, ',', '.');
        } else {
            $maximumWeightNice = $maximumWeightInGram;
        }
        $maximumWeightNice .= ' ' . $this->MaximumWeightUnitAbreviation;
        return $maximumWeightNice;
    }
    
    /**
     * Returns the maximum weights unit abreviation in context of
     * Config::DisplayWeightsInKilogram().
     * 
     * @return string
     */
    public function getMaximumWeightUnitAbreviation() {
        $maximumWeightUnitAbreviation = 'g';
        if (Config::DisplayWeightsInKilogram()) {
            $maximumWeightUnitAbreviation = 'kg';
        }
        return $maximumWeightUnitAbreviation;
    }
    
    /**
     * Returns the delivery time as string.
     * 
     * @param bool $forceDisplayInDays Force displaying the delivery time in days
     * 
     * @return string
     */
    public function getDeliveryTime($forceDisplayInDays = false) {
        return ShippingMethod::get_delivery_time_for($this, $forceDisplayInDays);
    }
}

