<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
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
 * @copyright Pixeltricks GmbH
 * @since 06.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartShippingFee extends DataObject {

    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'MaximumWeight'     => 'Int',   //gramms
        'UnlimitedWeight'   => 'Boolean',
        'Price'             => 'Money',
        'PostPricing'       => 'Boolean',
    );

    /**
     * Has-one relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartZone'              => 'SilvercartZone',
        'SilvercartShippingMethod'    => 'SilvercartShippingMethod',
        'SilvercartTax'               => 'SilvercartTax'
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
        'PriceFormatted'                => 'Varchar(20)',
        'AttributedShippingMethods'     => 'Varchar(255)',
        'MaximumWeightLimitedOrNot'     => 'Varchar(255)',
        'PriceAmount'                   => 'Varchar(255)',
        'PriceCurrency'                 => 'Varchar(255)',
    );
    
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011
     */
    public function singular_name() {
        if (_t('SilvercartShippingFee.SINGULARNAME')) {
            return _t('SilvercartShippingFee.SINGULARNAME');
        } else {
            return parent::singular_name();
        } 
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011 
     */
    public function plural_name() {
        if (_t('SilvercartShippingFee.PLURALNAME')) {
            return _t('SilvercartShippingFee.PLURALNAME');
        } else {
            return parent::plural_name();
        }   
    }

    /**
     * i18n for summary fields
     *
     * @return array
     * 
     * @author Seabstian Diel <sdiel@pixeltricks.de>
     * @since 28.04.2011
     */
    public function summaryFields() {
        return array_merge(
                parent::summaryFields(),
                array(
                    'SilvercartZone.Title'      => $this->fieldLabel('SilvercartZone'),
                    'AttributedShippingMethods' => $this->fieldLabel('AttributedShippingMethods'),
                    'MaximumWeightLimitedOrNot' => $this->fieldLabel('MaximumWeight'),
                    'PriceFormatted'            => $this->fieldLabel('Price'),
                )
        );
    }

    /**
     * i18n for field labels
     *
     * @param bool $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * 
     * @author Seabstian Diel <sdiel@pixeltricks.de>
     * @since 28.04.2011
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'MaximumWeight'             => _t('SilvercartShippingFee.MAXIMUM_WEIGHT'),
                    'UnlimitedWeight'           => _t('SilvercartShippingFee.UNLIMITED_WEIGHT_LABEL'),
                    'Price'                     => _t('SilvercartShippingFee.COSTS'),
                    'SilvercartZone'            => _t('SilvercartShippingMethod.FOR_ZONES'),
                    'SilvercartZone.Title'      => _t('SilvercartShippingMethod.FOR_ZONES'),
                    'AttributedShippingMethods' => _t('SilvercartShippingFee.ATTRIBUTED_SHIPPINGMETHOD'),
                    'SilvercartTax'             => _t('SilvercartTax.SINGULARNAME', 'tax'),
                    'PostPricing'               => _t('SilvercartShippingFee.POST_PRICING'),
                    'SilvercartOrders'          => _t('SilvercartOrder.PLURALNAME'),
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
            $maximumWeightLimitedOrNot = _t('SilvercartShippingFee.UNLIMITED_WEIGHT');
        }
        return $maximumWeightLimitedOrNot;
    }

    
    /**
     * Set a custom search context for fields like "greater than", "less than",
     * etc.
     *
     * @return SearchContext
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
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
     * @return FieldSet the fields for the backend
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2012
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        // only the carriers zones must be selectable
        $zones  = DataObject::get(
                'SilvercartZone',
                sprintf(
                        "`SilvercartZone_SilvercartCarriers`.`SilvercartCarrierID` = %s",
                        $this->SilvercartShippingMethod()->SilvercartCarrier()->ID
                ),
                "",
                "LEFT JOIN `SilvercartZone_SilvercartCarriers` ON (`SilvercartZone`.`ID` = `SilvercartZone_SilvercartCarriers`.`SilvercartZoneID`)"
        );
        if ($zones) {
            $fields->addFieldToTab(
                "Root.Main",
                new DropdownField(
                    'SilvercartZoneID',
                    _t('SilvercartShippingFee.ZONE_WITH_DESCRIPTION', 'zone (only carrier\'s zones available)'),
                    $zones->toDropDownMap('ID', 'Title', _t('SilvercartShippingFee.EMPTYSTRING_CHOOSEZONE', '--choose zone--'))
                )
            );
        }
        
        $postPricingField = $fields->dataFieldByName('PostPricing');
        $postPricingField->setTitle($postPricingField->Title() . ' (' . _t('SilvercartShippingFee.POST_PRICING_INFO') . ')');

        return $fields;
    }

    /**
     * Returns the Price formatted by locale.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2012
     */
    public function PriceFormatted() {
        $priceFormatted = '';
        if ($this->PostPricing) {
            $priceFormatted = '---';
        } else {
            $priceObj = new Money();
            $priceObj->setAmount($this->getPriceAmount());
            $priceObj->setCurrency($this->getPriceCurrency());

            $priceFormatted = $priceObj->Nice();
        }
        return $priceFormatted;
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.01.2012
     */
    public function getTaxRate() {
        if (Member::currentUser() &&
            Member::currentUser()->SilvercartShoppingCartID > 0) {

            $silvercartShoppingCart = Member::currentUser()->SilvercartShoppingCart();

            $taxRate = $silvercartShoppingCart->getMostValuableTaxRate(
                $silvercartShoppingCart->getTaxRatesWithoutFeesAndCharges('SilvercartVoucher')
            );

            if ($taxRate) {
                $taxRate = $taxRate->Rate;
            }
        } else {
            $taxRate = $this->SilvercartTax()->getTaxRate();
        }

        return $taxRate;
    }

    /**
     * returns the tax amount included in $this
     *
     * @return float
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.02.2011
     */
    public function getTaxAmount() {
        $taxRate   = $this->getTaxRate();
        $taxAmount = $this->Price->getAmount() - ($this->Price->getAmount() / (100 + $taxRate) * 100);

        if (Member::currentUser() &&
            Member::currentUser()->SilvercartShoppingCartID > 0) {

            $silvercartShoppingCart  = Member::currentUser()->SilvercartShoppingCart();
            $freeOfShippingCostsFrom = SilvercartConfig::FreeOfShippingCostsFrom();

            if (SilvercartConfig::PriceType() == 'gross') {
                $shoppingCartValue = $silvercartShoppingCart->getTaxableAmountGrossWithoutFees();
            } else {
                $shoppingCartValue = $silvercartShoppingCart->getTaxableAmountNetWithoutFees();
            }

            if (SilvercartConfig::UseFreeOfShippingCostsFrom()) {
                $freeOfShippingCostsFromAmount = (float) $freeOfShippingCostsFrom->getAmount();
                if (!is_null($freeOfShippingCostsFromAmount) &&
                    $freeOfShippingCostsFromAmount <= $shoppingCartValue->getAmount()) {
                    $taxAmount = 0.0;
                }
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
     * Returns the prices amount
     *
     * @return float
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.03.2012
     */
    public function getPriceAmount() {
        $price = (float) $this->Price->getAmount();

        if (SilvercartConfig::PriceType() == 'net') {
            $price = $price - $this->getTaxAmount();
        }

        if (Member::currentUser() &&
            Member::currentUser()->SilvercartShoppingCartID > 0) {

            $silvercartShoppingCart     = Member::currentUser()->SilvercartShoppingCart();
            $useFreeOfShippingCostsFrom = SilvercartConfig::UseFreeOfShippingCostsFrom();
            $freeOfShippingCostsFrom    = SilvercartConfig::FreeOfShippingCostsFrom();

            if (SilvercartConfig::PriceType() == 'gross') {
                $shoppingCartValue = $silvercartShoppingCart->getTaxableAmountGrossWithoutFees();
            } else {
                $shoppingCartValue = $silvercartShoppingCart->getTaxableAmountNetWithoutFees();
            }

            if ($useFreeOfShippingCostsFrom &&
                !is_null($freeOfShippingCostsFrom->getAmount()) &&
                $freeOfShippingCostsFrom->getAmount() > 0 &&
                (float) $freeOfShippingCostsFrom->getAmount() <= $shoppingCartValue->getAmount()) {

                $price = 0.0;
            }
        }

        $price = round($price, 2);

        return $price;
    }
    
    /**
     * Returns the prices currency
     *
     * @return string
     */
    public function getPriceCurrency() {
        return $this->Price->getCurrency();
    }
}

