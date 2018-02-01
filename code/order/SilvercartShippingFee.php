<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Order
 */

/**
 * A carrier has many shipping fees.
 * They mainly depend on the freights weight.
 *
 * @package Silvercart
 * @subpackage Order
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 06.11.2010
 * @license see license file in modules root directory
 */
class SilvercartShippingFee extends DataObject {

    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'MaximumWeight'                 => 'Float',
        'UnlimitedWeight'               => 'Boolean',
        'Price'                         => 'SilvercartMoney',
        'PostPricing'                   => 'Boolean',
        'freeOfShippingCostsDisabled'   => 'Boolean',
        'freeOfShippingCostsFrom'       => 'SilvercartMoney',
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
    public static $has_one = array(
        'SilvercartZone'              => 'SilvercartZone',
        'SilvercartShippingMethod'    => 'SilvercartShippingMethod',
        'SilvercartTax'               => 'SilvercartTax',
    );

    /**
     * Has-many Relationship.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartOrders' => 'SilvercartOrder'
    );

    /**
     * Virtual database fields.
     *
     * @var array
     */
    public static $casting = array(
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
    public static $default_sort = "priority DESC";
    
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
        return SilvercartTools::singular_name_for($this);
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
        return SilvercartTools::plural_name_for($this);
    }

    /**
     * i18n for summary fields
     *
     * @return array
     * 
     * @author Seabstian Diel <sdiel@pixeltricks.de>
     * @since 20.02.2013
     */
    public function summaryFields() {
        $summaryFields = array(
            'SilvercartZone.Title'      => $this->fieldLabel('SilvercartZone'),
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
     * @author Seabstian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'MaximumWeight'                 => _t('SilvercartShippingFee.MAXIMUM_WEIGHT'),
                    'UnlimitedWeight'               => _t('SilvercartShippingFee.UNLIMITED_WEIGHT_LABEL'),
                    'Price'                         => _t('SilvercartShippingFee.COSTS'),
                    'SilvercartZone'                => _t('SilvercartShippingMethod.FOR_ZONES'),
                    'SilvercartZone.Title'          => _t('SilvercartShippingMethod.FOR_ZONES'),
                    'AttributedShippingMethods'     => _t('SilvercartShippingFee.ATTRIBUTED_SHIPPINGMETHOD'),
                    'SilvercartTax'                 => _t('SilvercartTax.SINGULARNAME', 'tax'),
                    'PostPricing'                   => _t('SilvercartShippingFee.POST_PRICING'),
                    'SilvercartOrders'              => _t('SilvercartOrder.PLURALNAME'),
                    'PostPricingInfo'               => _t('SilvercartShippingFee.POST_PRICING_INFO'),
                    'EmptyString'                   => _t('SilvercartShippingFee.EMPTYSTRING_CHOOSEZONE'),
                    'freeOfShippingCostsDisabled'   => _t('SilvercartShippingFee.FREEOFSHIPPINGCOSTSDISABLED'),
                    'freeOfShippingCostsFrom'       => _t('SilvercartShippingFee.FREEOFSHIPPINGCOSTSFROM'),
                    'SilvercartShippingMethod'      => _t('SilvercartShippingMethod.SINGULARNAME'),
                    'priority'                      => _t('Silvercart.PRIORITY'),
                    'DeliveryTime'                  => _t('SilvercartShippingMethod.DeliveryTime'),
                    'DeliveryTimeMin'               => _t('SilvercartShippingMethod.DeliveryTimeMin'),
                    'DeliveryTimeMinDesc'           => _t('SilvercartShippingMethod.DeliveryTimeMinDesc'),
                    'DeliveryTimeMax'               => _t('SilvercartShippingMethod.DeliveryTimeMax'),
                    'DeliveryTimeMaxDesc'           => _t('SilvercartShippingMethod.DeliveryTimeMaxDesc'),
                    'DeliveryTimeText'              => _t('SilvercartShippingMethod.DeliveryTimeText'),
                    'DeliveryTimeTextDesc'          => _t('SilvercartShippingMethod.DeliveryTimeTextDesc'),
                    'DeliveryTimeHint'              => _t('SilvercartShippingFee.DeliveryTimeHint'),
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
            'MaximumWeight'             => new LessThanFilter('MaximumWeight')
        );
        return new SearchContext(
            $this->class,
            $fields,
            $filters
        );
    }

    /**
     * Customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function getCMSFields() {
        $this->getCMSFieldsIsCalled = true;
        $fields = parent::getCMSFields();
        
        $postPricingField = $fields->dataFieldByName('PostPricing');
        $postPricingField->setTitle($postPricingField->Title() . ' (' . $this->fieldLabel('PostPricingInfo') . ')');
        
        SilvercartTax::presetDropdownWithDefault($fields->dataFieldByName('SilvercartTaxID'), $this);
        
        $fieldGroup = new SilvercartFieldGroup('ShippingFeeGroup', '', $fields);
        $fieldGroup->push(          $fields->dataFieldByName('MaximumWeight'));
        $fieldGroup->pushAndBreak(  $fields->dataFieldByName('UnlimitedWeight'));
        $fieldGroup->push(          $fields->dataFieldByName('Price'));
        $fieldGroup->pushAndBreak(  $fields->dataFieldByName('SilvercartTaxID'));
        $fieldGroup->pushAndBreak(  $postPricingField);
        // only the carriers zones must be selectable
        $leftJoinTable  = 'SilvercartZone_SilvercartCarriers';
        $leftJoinOn     = '"SilvercartZone"."ID" = "SilvercartZone_SilvercartCarriers"."SilvercartZoneID"';
        $where          = sprintf(
                            "\"SilvercartZone_SilvercartCarriers\".\"SilvercartCarrierID\" = %s",
                            $this->SilvercartShippingMethod()->SilvercartCarrier()->ID
        );
        $zones          = SilvercartZone::get()
                            ->leftJoin($leftJoinTable, $leftJoinOn)
                            ->where($where);
        if ($zones->exists()) {
            $zonesMap   = $zones->map('ID', 'Title');
            $zonesField = new DropdownField(
                    'SilvercartZoneID',
                    _t('SilvercartShippingFee.ZONE_WITH_DESCRIPTION', 'zone (only carrier\'s zones available)'),
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
        if ($this->SilvercartShippingMethod()->exists()) {
            $parentDeliveryTime = '<br/>(' . SilvercartShippingMethod::get_delivery_time_for($this->SilvercartShippingMethod(), true) . ')';
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
            if ($product instanceof SilvercartProduct) {
                $amount = $product->getPrice()->getAmount();
            }
        }
        
        if ($this->SilvercartZone()->SilvercartCountries()->Count() == 1) {
            $country = $this->SilvercartZone()->SilvercartCountries()->First();
        }
        
        $priceFormatted = '';
        if ($this->PostPricing) {
            $priceFormatted = '---';
        } else {
            $priceObj = new Money();
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
            $priceObj = new Money();
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
        return SilvercartTools::AttributedDataObject($this->SilvercartShippingMethod());
    }

    /**
     * Returns the tax rate for this fee.
     * 
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2013
     */
    public function getTaxRate() {
        $taxRate = SilvercartShoppingCart::get_most_valuable_tax_rate();
        if ($taxRate === false) {
            $taxRate = $this->SilvercartTax()->getTaxRate();
        }
        return $taxRate;
    }

    /**
     * returns the tax amount included in $this
     * 
     * @param float $price Price to get tax amount for (if empty $this->getPriceAmount() is used)
     *
     * @return float
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.06.2017
     */
    public function getTaxAmount($price = null) {
        if (is_null($price)) {
            $price = $this->Price->getAmount();
        }
        $taxRate   = $this->getTaxRate();
        $taxAmount = $price - ($price / (100 + $taxRate) * 100);

        if (SilvercartCustomer::currentUser() &&
            SilvercartCustomer::currentUser()->SilvercartShoppingCartID > 0) {

            $silvercartShoppingCart = SilvercartCustomer::currentUser()->getCart();
            $shoppingCartValue      = $silvercartShoppingCart->getTaxableAmountWithoutFees();
            $amountToGetFeeFor      = $shoppingCartValue->getAmount();
            $countryToGetFeeFor     = $this->SilvercartShippingMethod()->getShippingCountry();
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
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2012
     */
    public function getFeeWithCarrierAndShippingMethod() {
        if ($this->SilvercartShippingMethod()) {
            $carrier = "";
            if ($this->SilvercartShippingMethod()->SilvercartCarrier()) {
                $carrier = $this->SilvercartShippingMethod()->SilvercartCarrier()->Title;
            }
            $shippingMethod            = $this->SilvercartShippingMethod()->Title;
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
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.03.2012
     */
    public function getCalculatedPrice() {
        $priceObj = new Money();
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
     * @param bool              $plain              Set to true to load the price amount without any manipulation
     * @param float             $amountToGetFeeFor  Amount to get fee for
     * @param SilvercartCountry $countryToGetFeeFor Amount to get fee for
     *
     * @return float
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.11.2014
     */
    public function getPriceAmount($plain = false, $amountToGetFeeFor = null, $countryToGetFeeFor = null) {
        $price = (float) $this->Price->getAmount();

        if (!$plain) {
            if (SilvercartConfig::PriceType() == 'net') {
                $price = $price - $this->getTaxAmount($price);
            }

            if (SilvercartCustomer::currentUser() &&
                SilvercartCustomer::currentUser()->SilvercartShoppingCartID > 0) {
                $silvercartShoppingCart = SilvercartCustomer::currentUser()->getCart();
                $shoppingCartValue      = $silvercartShoppingCart->getTaxableAmountWithoutFees();
                if (is_null($amountToGetFeeFor)) {
                    $amountToGetFeeFor  = $shoppingCartValue->getAmount();
                }
                if (is_null($countryToGetFeeFor)) {
                    $countryToGetFeeFor = $this->SilvercartShippingMethod()->getShippingCountry();
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
            SilvercartConfig::UseFreeOfShippingCostsFrom()) {
            $useFreeOfShippingCostsFrom = true;
        }
        return $useFreeOfShippingCostsFrom;
    }
    
    /**
     * Returns needed value for free shipping
     * 
     * @param SilvercartCountry $country Country to get free of shipping costs from value
     * 
     * @return Money
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function FreeOfShippingCostsFrom($country) {
        $freeOfShippingCostsFrom = new Money();
        if ($this->UseFreeOfShippingCostsFrom()) {
            if ($this->freeOfShippingCostsFrom->getAmount() > 0) {
                $freeOfShippingCostsFrom = $this->freeOfShippingCostsFrom;
            } else {
                $freeOfShippingCostsFrom = SilvercartConfig::FreeOfShippingCostsFrom($country);
            }
        }
        return $freeOfShippingCostsFrom;
    }
    
    /**
     * Returns whether shipping is free for the given amount and country
     * 
     * @param float             $amount  Amount to get free of shipping info
     * @param SilvercartCountry $country Country to get free of shipping info
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
     * SilvercartConfig::DisplayWeightsInKilogram().
     * 
     * @return string
     */
    public function getMaximumWeightNice() {
        $maximumWeightInGram = $this->MaximumWeight;
        if (SilvercartConfig::DisplayWeightsInKilogram()) {
            $maximumWeightNice = number_format($maximumWeightInGram / 1000, 2, ',', '.');
        } else {
            $maximumWeightNice = $maximumWeightInGram;
        }
        $maximumWeightNice .= ' ' . $this->MaximumWeightUnitAbreviation;
        return $maximumWeightNice;
    }
    
    /**
     * Returns the maximum weights unit abreviation in context of
     * SilvercartConfig::DisplayWeightsInKilogram().
     * 
     * @return string
     */
    public function getMaximumWeightUnitAbreviation() {
        $maximumWeightUnitAbreviation = 'g';
        if (SilvercartConfig::DisplayWeightsInKilogram()) {
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
        return SilvercartShippingMethod::get_delivery_time_for($this, $forceDisplayInDays);
    }
}

