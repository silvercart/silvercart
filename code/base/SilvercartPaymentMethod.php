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
 * @subpackage Base
 */

/**
 * base class for payment
 *
 * Every payment module must extend this class
 *
 * @package Silvercart
 * @subpackage Base
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 07.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartPaymentMethod extends DataObject {
    
    /**
     * Defines the attributes of the class
     *
     * @var array
     */
    public static $db = array(
        'isActive'                              => 'Boolean',
        'minAmountForActivation'                => 'Float',
        'maxAmountForActivation'                => 'Float',
        'mode'                                  => "Enum('Live,Dev','Dev')",
        'orderStatus'                           => 'Varchar(50)',
        'showPaymentLogos'                      => 'Boolean',
        'orderRestrictionMinQuantity'           => 'Int',
        'enableActivationByOrderRestrictions'   => 'Boolean',
        'ShowFormFieldsOnPaymentSelection'      => 'Boolean',
        'sumModificationImpact'                 => "enum('productValue,totalValue','productValue')",
        'sumModificationImpactType'             => "enum('charge,discount','charge')",
        'sumModificationValue'                  => 'Float',
        'sumModificationValueType'              => "enum('absolute,percent','absolute')",
        'sumModificationLabel'                  => 'VarChar(255)',
        'useSumModification'                    => 'Boolean(0)'
    );
    /**
     * Defines 1:1 relations
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartZone'            => 'SilvercartZone'
    );
    /**
     * Defines 1:n relations
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartHandlingCosts'   => 'SilvercartHandlingCost',
        'SilvercartOrders'          => 'SilvercartOrder',
        'PaymentLogos'              => 'SilvercartImage'
    );
    /**
     * Defines n:m relations
     *
     * @var array
     */
    public static $many_many = array(
        'SilvercartShippingMethods' => 'SilvercartShippingMethod',
        'ShowOnlyForGroups'         => 'Group',
        'ShowNotForGroups'          => 'Group',
        'ShowOnlyForUsers'          => 'Member',
        'ShowNotForUsers'           => 'Member',
        'OrderRestrictionStatus'    => 'SilvercartOrderStatus'
    );
    /**
     * Defines m:n relations
     *
     * @var array
     */
    public static $belongs_many_many = array(
        'SilvercartCountries' => 'SilvercartCountry'
    );
    /**
     * Virtual database columns.
     *
     * @var array
     */
    public static $casting = array(
        'AttributedCountries'       => 'Varchar(255)',
        'AttributedZones'           => 'Varchar(255)',
        'activatedStatus'           => 'Varchar(255)',
        'Name'                      => 'Varchar(150)',
        'paymentDescription'        => 'Text',
        'LongPaymentDescription'    => 'Text',
    );
    /**
     * Default values for new PaymentMethods
     *
     * @var array
     */
    public static $defaults = array(
        'showPaymentLogos'                 => true,
        'ShowFormFieldsOnPaymentSelection' => false,
    );
    
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
    protected $errorList = array();
    /**
     * Indicates whether a payment module has multiple payment channels or not.
     *
     * @var bool
     */
    public static $has_multiple_payment_channels = false;
    /**
     * A list of possible payment channels.
     *
     * @var array
     */
    public static $possible_payment_channels = array();
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
     * @var SilvercartAddress
     */
    protected $invoiceAddress = null;
    /**
     * Shipping address
     *
     * @var SilvercartAddress
     */
    protected $shippingAddress = null;
    /**
     * Shopping cart
     *
     * @var SilvercartShoppingCart
     */
    protected $shoppingCart = null;
    /**
     * Order
     *
     * @var SilvercartOrder
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
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.05.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.05.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this);
    }
    
    /**
     * getter for the multilingual attribute name
     *
     * @return string 
     */
    public function getName() {
        $name = '';     
        if ($this->isExtendingSilvercartPaymentMethod() && $this->hasMethod('getLanguageFieldValue')) {
            $name = $this->getLanguageFieldValue('Name');
        }
        return $name;
    }
    
    /**
     * getter for the multilingual attribute paymentDescription
     *
     * @return string 
     */
    public function getpaymentDescription() {
        $paymentDescription = '';
        if ($this->isExtendingSilvercartPaymentMethod() && $this->hasMethod('getLanguageFieldValue')) {
            $paymentDescription = $this->getLanguageFieldValue('paymentDescription');
        }
        return $paymentDescription;
    }
    
    /**
     * getter for the multilingual attribute LongPaymentDescription
     *
     * @return string 
     */
    public function getLongPaymentDescription() {
        $LongPaymentDescription = '';
        if ($this->isExtendingSilvercartPaymentMethod() && $this->hasMethod('getLanguageFieldValue')) {
            $LongPaymentDescription = $this->getLanguageFieldValue('LongPaymentDescription');
        }
        return $LongPaymentDescription;
    }
    
    // ------------------------------------------------------------------------
    // Methods
    // ------------------------------------------------------------------------
    
    /**
     * Searchable fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011
     */
    public function searchableFields() {
        $searchableFields = array(
            'Name' => array(
                'title'  => _t('SilvercartProduct.COLUMN_TITLE'),
                'filter' => 'PartialMatchFilter'
            ),
            'isActive' => array(
                'title'  => _t("SilvercartShopAdmin.PAYMENT_ISACTIVE"),
                'filter' => 'ExactMatchFilter'
            ),
            'minAmountForActivation' => array(
                'title'  => _t('SilvercartShopAdmin.PAYMENT_MINAMOUNTFORACTIVATION'),
                'filter' => 'GreaterThanFilter'
            ),
            'maxAmountForActivation' => array(
                'title'  => _t('SilvercartShopAdmin.PAYMENT_MAXAMOUNTFORACTIVATION'),
                'filter' => 'LessThanFilter'
            ),
            'SilvercartZone.ID' => array(
                'title'  => _t("SilvercartCountry.ATTRIBUTED_ZONES"),
                'filter' => 'ExactMatchFilter'
            ),
            'SilvercartCountries.ID' => array(
                'title'  => _t("SilvercartPaymentMethod.ATTRIBUTED_COUNTRIES"),
                'filter' => 'ExactMatchFilter'
            )
        );
        $this->extend('updateSearchableFields', $searchableFields);
        return $searchableFields;
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.04.2011
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'Name'                              => _t('SilvercartPaymentMethod.NAME'),
                    'activatedStatus'                   => _t('SilvercartShopAdmin.PAYMENT_ISACTIVE'),
                    'AttributedZones'                   => _t('SilvercartCountry.ATTRIBUTED_ZONES'),
                    'AttributedCountries'               => _t('SilvercartPaymentMethod.ATTRIBUTED_COUNTRIES'),
                    'minAmountForActivation'            => _t('SilvercartPaymentMethod.FROM_PURCHASE_VALUE', 'from purchase value'),
                    'maxAmountForActivation'            => _t('SilvercartPaymentMethod.TILL_PURCHASE_VALUE', 'till purchase value'),
                    'ShowFormFieldsOnPaymentSelection'  => _t('SilvercartPaymentMethod.SHOW_FORM_FIELDS_ON_PAYMENT_SELECTION'),
                    'SilvercartPaymentMethodLanguages'  => _t('SilvercartPaymentMethodLanguage.PLURALNAME'),
                    'SilvercartShippingMethods'         => _t('SilvercartShippingMethod.PLURALNAME'),
                    'SilvercartCountries'               => _t('SilvercartCountry.PLURALNAME'),
                    'LongPaymentDescription'            => _t('SilvercartPaymentMethod.LONG_PAYMENT_DESCRIPTION'),
                    'SilvercartHandlingCosts'           => _t('SilvercartHandlingCost.PLURALNAME'),
                    'PaymentLogos'                      => _t('SilvercartPaymentMethod.PAYMENT_LOGOS'),
                    'SilvercartOrderStatus'             => _t('SilvercartOrderStatus.PLURALNAME'),
                    'ShowOnlyForGroups'                 => _t('SilvercartPaymentMethod.SHOW_ONLY_FOR_GROUPS_LABEL'),
                    'ShowNotForGroups'                  => _t('SilvercartPaymentMethod.SHOW_NOT_FOR_GROUPS_LABEL'),
                    'ShowNotForUsers'                   => _t('SilvercartPaymentMethod.SHOW_NOT_FOR_USERS_LABEL'),
                    'ShowOnlyForUsers'                  => _t('SilvercartPaymentMethod.SHOW_ONLY_FOR_USERS_LABEL'),
                    'AddPaymentLogos'                   => _t('SilvercartPaymentMethod.AddPaymentLogos'),
                    
                )
        );
    }

    /**
     * i18n for summary fields
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.02.2011
     * @copyright 2010 pixeltricks GmbH
     * @return array
     */
    public function summaryFields() {
        return array(
            'Name'                   => _t('SilvercartPaymentMethod.NAME'),
            'activatedStatus'        => _t('SilvercartShopAdmin.PAYMENT_ISACTIVE'),
            'AttributedZones'        => _t('SilvercartCountry.ATTRIBUTED_ZONES'),
            'AttributedCountries'    => _t('SilvercartPaymentMethod.ATTRIBUTED_COUNTRIES'),
            'minAmountForActivation' => _t('SilvercartPaymentMethod.FROM_PURCHASE_VALUE'),
            'maxAmountForActivation' => _t('SilvercartPaymentMethod.TILL_PURCHASE_VALUE'),
        );
    }

    /**
     * Returns the title of the payment method
     *
     * @return string
     */
    public function getTitle() {
        return $this->Name;
    }

    /**
     * Returns the status that for orders created with this payment method
     *
     * @return string orderstatus code
     */
    public function getDefaultOrderStatus() {
        return $this->orderStatus;
    }

    /**
     * Returns the path to the payment methods logo
     *
     * @return string
     */
    public function getLogo() {
        
    }

    /**
     * Returns the link for cancel action or end of session
     *
     * @return string
     */
    public function getCancelLink() {
        return $this->cancelLink;
    }

    /**
     * Returns the link to get back in the shop
     *
     * @return string
     */
    public function getReturnLink() {
        return $this->returnLink;
    }

    /**
     * Returns handling costs for this payment method
     *
     * @return Money a money object
     */
    public function getHandlingCost() {
        $controller         = Controller::curr();
        $member             = SilvercartCustomer::currentRegisteredCustomer();
        $handlingCostToUse  = false;

        if (method_exists($controller, 'getAddress')) {
            // 1) Use shipping address from checkout
            $shippingAddress   = $controller->getAddress('Shipping');
        } else {
            if ($member &&
                $member->ShippingAddressID > 0) {

                // 2) Use customer's default shipping address
                $shippingAddress = $member->ShippingAddress();
            } else {
                // 3) Generate shipping address with shop's default country
                $currentShopLocale = i18n::get_lang_from_locale(i18n::get_locale());
                $shippingAddress = new SilvercartAddress();
                $shippingAddress->SilvercartCountry = DataObject::get_one(
                    'SilvercartCountry',
                    sprintf(
                        "
                        ISO2 = '%s'
                        ",
                        strtoupper($currentShopLocale)
                    )
                );
            }
        }

        if (!$shippingAddress) {
            return false;
        }

        $zonesDefined = false;

        foreach ($this->SilvercartHandlingCosts() as $handlingCost) {
            $zone = $handlingCost->SilvercartZone();

            if ($zone->SilvercartCountries()->find('ISO3', $shippingAddress->SilvercartCountry()->ISO3)) {
                $handlingCostToUse = $handlingCost;
                $zonesDefined = true;
                break;
            }
        }

        // Fallback if SilvercartHandlingCosts are available but no zone is defined
        if (!$zonesDefined) {
            if ($this->SilvercartHandlingCosts()->Count() > 0) {
                $handlingCostToUse = $this->SilvercartHandlingCosts()->First();
            } else {
                $silvercartTax                              = DataObject::get_one('SilvercartTax', 'isDefault = 1');
                $handlingCostToUse                          = new SilvercartHandlingCost();
                $handlingCostToUse->SilvercartPaymentMethod = $this;
                $handlingCostToUse->SilvercartTax           = $silvercartTax;
                $handlingCostToUse->SilvercartTaxID         = $silvercartTax->ID;
                $handlingCostToUse->amount                  = new Money();
                $handlingCostToUse->amount->setAmount(0);
                $handlingCostToUse->amount->setCurrency(SilvercartConfig::DefaultCurrency());
            }
        }

        return $handlingCostToUse;
    }
    
    /**
     * Returns the charges and discounts for the product values for this 
     * payment method.
     * 
     * @param SilvercartShoppingCart $silvercartShoppingCart The shopping cart object
     * @param string                 $priceType              'gross' or 'net'
     *
     * @return mixed boolean|DataObject
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 14.12.2011
     */
    public function getChargesAndDiscountsForProducts(SilvercartShoppingCart $silvercartShoppingCart, $priceType = false) {
        $handlingCosts = new Money;
        $handlingCosts->setAmount(0);
        $handlingCosts->setCurrency(SilvercartConfig::DefaultCurrency());

        if ($priceType === false) {
            $priceType = SilvercartConfig::PriceType();
        }

        if ($this->useSumModification &&
            $this->sumModificationImpact == 'productValue') {
            
            switch ($this->sumModificationValueType) {
                case 'percent':
                    if (SilvercartConfig::PriceType() == 'gross') {
                        $amount = $silvercartShoppingCart->getAmountTotalGrossWithoutFees(array(), false, true);
                    } else {
                        $amount = $silvercartShoppingCart->getAmountTotalNetWithoutFees(array(), false, true);
                    }
                    $modificationValue = $amount->getAmount() / 100 * $this->sumModificationValue;
                    break;
                case 'absolute':
                default:
                    $modificationValue = $this->sumModificationValue;
            }
            
            if ($this->sumModificationImpactType == 'charge') {
                $handlingCostAmount = $modificationValue;
            } else {
                $handlingCostAmount = "-".$modificationValue;
            }

            if (SilvercartConfig::PriceType() == 'gross') {
                $shoppingCartTotal = $silvercartShoppingCart->getAmountTotalGrossWithoutFees(array(), false, true);
            } else {
                $shoppingCartTotal = $silvercartShoppingCart->getAmountTotalNetWithoutFees(array(), false, true);
            }

            if ($handlingCostAmount < 0 &&
                $shoppingCartTotal->getAmount() < ($handlingCostAmount * -1)) {

                if ($shoppingCartTotal->getAmount == 0.0) {
                    $handlingCostAmount = 0.0;
                } else {
                    $handlingCostAmount = ($shoppingCartTotal->getAmount() * -1);
                }
            }

            if (SilvercartConfig::PriceType() == 'net') {
                $taxRate = $silvercartShoppingCart->getMostValuableTaxRate($silvercartShoppingCart->getTaxRatesWithoutFeesAndCharges('SilvercartVoucher'));

                if ($taxRate) {
                    $handlingCostAmount = round($handlingCostAmount / (100 + $taxRate->Rate) * 100, 4);
                }
            }

            $handlingCosts->setAmount($handlingCostAmount);

            return $handlingCosts;
        }
        
        return false;
    }
    
    /**
     * Returns the charges and discounts for the shopping cart total for
     * this payment method.
     * 
     * @param SilvercartShoppingCart $silvercartShoppingCart The shopping cart object
     * @param string                 $priceType              'gross' or 'net'
     *
     * @return mixed boolean|DataObject
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.09.2012
     */
    public function getChargesAndDiscountsForTotal(SilvercartShoppingCart $silvercartShoppingCart, $priceType = false) {
        $handlingCosts = new Money;
        $handlingCosts->setAmount(0);
        $handlingCosts->setCurrency(SilvercartConfig::DefaultCurrency());

        if ($priceType === false) {
            $priceType = SilvercartConfig::PriceType();
        }

        if ($this->useSumModification &&
            $this->sumModificationImpact == 'totalValue') {
            
            switch ($this->sumModificationValueType) {
                case 'percent':
                    $amount            = $silvercartShoppingCart->getAmountTotal(array(), false, true);
                    $modificationValue = $amount->getAmount() / 100 * $this->sumModificationValue;
                    break;
                case 'absolute':
                default:
                    $modificationValue = $this->sumModificationValue;
            }
            
            if ($this->sumModificationImpactType == 'charge') {
                $handlingCostAmount = $modificationValue;
            } else {
                $handlingCostAmount = "-".$modificationValue;
            }

            if (SilvercartConfig::PriceType() == 'gross') {
                $shoppingCartTotal = $silvercartShoppingCart->getAmountTotal(array(), false, true);
            } else {
                $shoppingCartTotal  = $silvercartShoppingCart->getAmountTotalNetWithoutVat(array(), false, true);
                $taxRate            = $silvercartShoppingCart->getMostValuableTaxRate($silvercartShoppingCart->getTaxRatesWithoutFeesAndCharges('SilvercartVoucher'));
                $handlingCostAmount = round($handlingCostAmount / (100 + $taxRate->Rate) * 100, 4);
            }

            if ($handlingCostAmount < 0 &&
                $shoppingCartTotal->getAmount() < ($handlingCostAmount * -1)) {

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
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function getDescriptionImage() {
        
    }

    /**
     * Returns if an error has occured
     *
     * @return bool
     */
    public function getErrorOccured() {
        if (count($this->getErrorList()) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Returns a ArrayList with errors
     *
     * @return ArrayList
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function getErrorList() {
        $errorList = array();
        $errorIdx = 0;

        foreach ($this->errorList as $error) {
            $errorList['error' . $errorIdx] = array(
                'error' => $error
            );
            $errorIdx++;
        }

        return new ArrayList($errorList);
    }
    
    /**
     * Returns allowed shipping methods.
     * 
     * @param string                 $shippingCountry                  The SilvercartCountry to check the payment methods for.
     * @param SilvercartShoppingCart $shoppingCart                     The shopping cart object
     * @param Boolean                $forceAnonymousCustomerIfNotExist When true, an anonymous customer will be created when no customer exists
     * 
     * @return ArrayList
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.05.2012
     */
    public static function getAllowedPaymentMethodsFor($shippingCountry, $shoppingCart, $forceAnonymousCustomerIfNotExist = false) {
        $allowedPaymentMethods  = array();
        
        if (!$shippingCountry) {
            return $allowedPaymentMethods;
        }
        
        $paymentMethods = $shippingCountry->SilvercartPaymentMethods('isActive = 1');
        $member         = Member::currentUser();
        if (!$member &&
            $forceAnonymousCustomerIfNotExist) {
            $member         = new Member();
            $anonymousGroup = DataObject::get_one('Group', "\"Code\" = 'anonymous'");
            $memberGroups   = new ArrayList();
            $memberGroups->push($anonymousGroup);
        } else {
            $memberGroups = $member->Groups();
        }
        
        $shippingMethodID = null;
        if (Controller::curr() instanceof SilvercartCheckoutStep_Controller) {
            $checkoutData       = Controller::curr()->getCombinedStepData();
            if (array_key_exists('ShippingMethod', $checkoutData)) {
                $shippingMethodID   = $checkoutData['ShippingMethod'];
            }
        }
        
        if ($paymentMethods) {
            foreach ($paymentMethods as $paymentMethod) {
                $assumePaymentMethod    = true;
                $containedInGroup       = false;
                $containedInUsers       = false;
                $doAccessChecks         = true;
        
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
                if (!is_null($shippingMethodID) &&
                    $paymentMethod->SilvercartShippingMethods()->exists() &&
                    !$paymentMethod->SilvercartShippingMethods()->find('ID', $shippingMethodID)) {
                    $assumePaymentMethod    = false;
                    $doAccessChecks         = false;
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
        
        $allowedPaymentMethods = new ArrayList($allowedPaymentMethods);
        
        return $allowedPaymentMethods;
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
    protected function isActivationByOrderRestrictionsPossible(Member $member) {
        $isActivationByOrderRestrictionsPossible = false;
        $nrOfValidOrders                         = 0;
        
        if (!$member) {
           return $isActivationByOrderRestrictionsPossible;
        }
        
        if ($member->SilvercartOrder()) {
            foreach ($member->SilvercartOrder() as $orderObj) {
                if ($this->OrderRestrictionStatus()->find('ID', $orderObj->SilvercartOrderStatus()->ID)) {
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
     * @copyright 2011 pixeltricks GmbH
     * @since 11.05.2011
     */
    public function getAllowedShippingMethods() {
        $allowedShippingMethods = array();
        $shippingMethods        = SilvercartShippingMethod::get()->filter(array("isActive" => 1));

        if ($shippingMethods->exists()) {
            foreach ($shippingMethods as $shippingMethod) {

                // Find shippping methods that are directly related to
                // payment methods....
                if ($shippingMethod->SilvercartPaymentMethods()->exists()) {
                    
                    // ... and exclude them, if the current payment method is
                    // not related.
                    if (!$shippingMethod->SilvercartPaymentMethods()->find('ID', $this->ID)) {
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
        
        $allowedShippingMethods = new ArrayList($allowedShippingMethods);
        
        return $allowedShippingMethods;
    }

    /**
     * Returns weather this payment method is available for a zone specified by id or not
     *
     * @param int $zoneId Zone id to be checked
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function isAvailableForZone($zoneId) {
        
    }

    /**
     * Is this payment method allowed for a shipping method?
     *
     * @param int $shippingMethodId Die ID id of shipping method to be checked
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function isAvailableForShippingMethod($shippingMethodId) {
        
    }

    /**
     * Is this payment method allowed for a total amount?
     *
     * @param int $amount Amount to be checked
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function isAvailableForAmount($amount) {
        $isAvailable    = false;
        $amount         = (float) $amount;
        $minAmount      = (float) $this->minAmountForActivation;
        $maxAmount      = (float) $this->maxAmountForActivation;

        if (($minAmount != 0.0 &&
             $maxAmount != 0.0)) {
            
            if ($amount >= $minAmount &&
                $amount <= $maxAmount) {

                $isAvailable = true;
            }
        } else if ($minAmount != 0.0) {
            if ($amount >= $minAmount) {
                $isAvailable = true;
            }
        } else if ($maxAmount != 0.0) {
            if ($amount <= $maxAmount) {
                $isAvailable = true;
            }
        } else {
            $isAvailable = true;
        }

        return $isAvailable;
    }

    /**
     * Hook: processed before order creation
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function processPaymentBeforeOrder() {
        $this->controller->addCompletedStep();
        $this->controller->NextStep();
    }

    /**
     * Hook: processed after order creation
     *
     * @param Order $orderObj created order object
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function processPaymentAfterOrder($orderObj) {
        $this->controller->addCompletedStep();
        $this->controller->NextStep();
    }

    /**
     * Hook: called after jumpback from payment provider
     * processed before order creation
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 19.11.2010
     */
    public function processReturnJumpFromPaymentProvider() {
        $this->controller->addCompletedStep();
        $this->controller->NextStep();
    }

    /**
     * possibility to return a text at the end of the order process
     * processed after order creation
     *
     * @param Order $orderObj the order object
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 06.01.2011
     */
    public function processPaymentConfirmationText($orderObj) {
        // Override if necessary
    }

    /**
     * writes a payment method to the db in case none does exist yet
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2013
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        $this->createUploadFolder();

        // not a base class
        if ($this->moduleName !== '') {

            $className = $this->ClassName;
            /**
             * original expression
             * $has_multiple_payment_channels = $className::$has_multiple_payment_channels;
             * was replaced with eval call to provide compatibility to PHP 5.2
             */
            $has_multiple_payment_channels = eval('return ' . $className . '::$has_multiple_payment_channels;');

            if ($has_multiple_payment_channels) {
                $paymentModule = new $className();
                foreach ($paymentModule->getPossiblePaymentChannels() as $channel => $name) {
                    if (!DataObject::get($className)->filter('PaymentChannel', $channel)->exists()) {
                        $paymentMethod = new $className();
                        $paymentMethod->isActive       = 0;
                        $paymentMethod->PaymentChannel = $channel;
                        $paymentMethod->write();
                        $languages = array('de_DE', 'en_US', 'en_GB');

                        if (!in_array(Translatable::get_current_locale(), $languages)) {
                            $languages[]    = Translatable::get_current_locale();
                        }
                        $languageClassName = $this->ClassName . 'Language';
                        foreach ($languages as $language) {
                            $relationField = $this->ClassName . 'ID';
                            $filter = sprintf("\"Locale\" = '%s' AND \"%s\" = '%s'", $language, $relationField, $paymentMethod->ID);
                            $langObj = DataObject::get_one($languageClassName, $filter);
                            if (!$langObj) {
                                $langObj = new $languageClassName();
                                $langObj->Locale = $language;
                            }
                            $langObj->Name = $name;
                            $langObj->{$relationField} = $paymentMethod->ID;
                            $langObj->write();
                        }
                    }
                }
            } elseif (!DataObject::get_one($className)) {
                // entry does not exist yet
                //prepayment's default record gets activated if test data is enabled
                if ($this->moduleName == "Prepayment" && SilvercartRequireDefaultRecords::isEnabledTestData()) {
                    $this->isActive = 1;
                    //As we do not know if the country is instanciated yet we do write this relation in the country class too.
                    $germany = DataObject::get_one('SilvercartCountry', "\"ISO2\" = 'DE'");
                    if ($germany) {
                        $this->SilvercartCountries()->add($germany);
                    }
                } else {
                    $this->isActive = 0;
                }
                $this->Name     = _t($className . '.NAME',  $this->moduleName);
                $this->Title    = _t($className . '.TITLE', $this->moduleName);
                $this->write();
                $languages = array('de_DE', 'en_US', 'en_GB');
                foreach ($languages as $language) {
                   $filter = sprintf(
                       "\"Locale\" = '%s' AND \"%sID\" = '%s'",
                       $language,
                       $this->class,
                       $this->ID
                   );
                   $langObjClassName   = $this->class.'Language';
                   $langObjClassNameId = $langObjClassName.'ID';
                   $langObj = DataObject::get_one($langObjClassName, $filter);
                   if (!$langObj) {
                       $langObj = new $langObjClassName();
                       $langObj->Locale = $language;
                   }
                   $langObj->Name = $this->moduleName;
                   $langObj->setField($langObjClassNameId, $this->ID);
                   $langObj->write();
                }
            }
        }
    }
    
    /**
     * find out if we are dealing with an extended class or with SilvercartPaymentMethod.
     * This is needed for the multilingual feature
     *
     * @return boolean 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 29.01.2012
     */
    public function isExtendingSilvercartPaymentMethod() {
        $result = false;
        if ($this->ClassName) {
            if (in_array('SilvercartPaymentMethod', class_parents($this->ClassName))) {
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
    public function excludeFromScaffolding() {
        $excludeFields = array(
            'SilvercartCountries',
            'SilvercartOrders'
        );
        $this->extend('updateExcludeFromScaffolding', $excludeFields);
        return $excludeFields;
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.10.10
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this);
        return $fields;
    }

    /**
     * Returns modified CMS fields for the payment modules
     *
     * @return FieldList
     */
    public function getCMSFieldsForModules() {
        $tabset = new TabSet('Root');
        
        // --------------------------------------------------------------------
        // Common GUI elements for all payment methods
        // --------------------------------------------------------------------
        $tabBasic = new Tab('Basic', _t('SilvercartPaymentMethod.BASIC_SETTINGS', 'basic settings'));
        $translationsTab = new Tab('Translations');
        $translationsTab->setTitle(_t('SilvercartConfig.TRANSLATIONS'));
        $tabset->push($tabBasic);
        $tabset->push($translationsTab);
        $tabBasicFieldSet = new FieldList();
        $tabBasic->setChildren($tabBasicFieldSet);
        //multilingual fields
        $tabBasicFieldSet->push(new CheckboxField('isActive', _t('SilvercartShopAdmin.PAYMENT_ISACTIVE', 'activated')));
        if ($this->isExtendingSilvercartPaymentMethod()) {
           $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguageClassName());
            foreach ($languageFields as $languageField) {
                $tabBasicFieldSet->push($languageField);
            } 
        }
        $tabBasicFieldSet->push(new DropdownField(
                    'mode',
                    _t('SilvercartPaymentMethod.MODE', 'mode', null, 'Modus'
                    ),
                    array(
                        'Live' => _t('SilvercartShopAdmin.PAYMENT_MODE_LIVE'),
                        'Dev' => _t('SilvercartShopAdmin.PAYMENT_MODE_DEV')
                    ),
                    $this->mode
                ));
        $tabBasicFieldSet->push(new TextField('minAmountForActivation', _t('SilvercartShopAdmin.PAYMENT_MINAMOUNTFORACTIVATION')));
        $tabBasicFieldSet->push(new TextField('maxAmountForActivation', _t('SilvercartShopAdmin.PAYMENT_MAXAMOUNTFORACTIVATION')));
        $tabBasicFieldSet->push(new DropdownField(
                    'orderStatus',
                    _t('SilvercartPaymentMethod.STANDARD_ORDER_STATUS', 'standard order status for this payment method'),
                    SilvercartOrderStatus::getStatusList()->map('Code', 'Title')->toArray(),
                    null,
                    null,
                    _t("SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE")
                ));
        
        // --------------------------------------------------------------------
        // Handling cost table
        // --------------------------------------------------------------------
        $tabHandlingCosts= new Tab('HandlingCosts', _t('SilvercartPaymentMethod.HANDLINGCOSTS_SETTINGS'));
        $tabset->push($tabHandlingCosts);
        $handlingCostField = new DropdownField('SilvercartHandlingCost', $this->fieldLabel('SilvercartHandlingCosts'));
        $tabHandlingCosts->setChildren(
            new FieldList(
                $handlingCostField
            )
        );

        // --------------------------------------------------------------------
        // GUI for management of logo images
        // --------------------------------------------------------------------
        $tabLogos = new Tab('Logos', _t('SilvercartPaymentMethod.PAYMENT_LOGOS', 'Payment Logos'));
        $tabset->push($tabLogos);
        
        $paymentLogosTable = new GridField(
                'PaymentLogos',
                $this->fieldLabel('PaymentLogos'),
                $this->PaymentLogos(),
                SilvercartGridFieldConfig_RelationEditor::create()
        );
        
        $paymentLogosTable->getConfig()->removeComponentsByType('GridFieldAddNewButton');
        $paymentLogosTable->getConfig()->removeComponentsByType('GridFieldAddExistingAutocompleter');
        $paymentLogosTable->getConfig()->addComponent(new GridFieldDeleteAction());
        
        $paymentLogosUploadField = new SilvercartImageUploadField('UploadPaymentLogos', $this->fieldLabel('AddPaymentLogos'));
        $paymentLogosUploadField->setFolderName('Uploads/payment-images');
        
        $tabLogos->setChildren(
            new FieldList(
                new CheckboxField('showPaymentLogos', _t('SilvercartShopAdmin.SHOW_PAYMENT_LOGOS')),
                $paymentLogosUploadField,
                $paymentLogosTable
            )
        );
        
        // --------------------------------------------------------------------
        // GUI for access management
        // --------------------------------------------------------------------
        $tabAccessManagement = new Tab('AccessManagement', _t('SilvercartPaymentMethod.ACCESS_SETTINGS', 'Access management'));
        $tabset->push($tabAccessManagement);
        
        $tabsetAccessManagement = new TabSet('AccessManagementSection');
        $tabAccessManagement->push($tabsetAccessManagement);
        
        $tabAccessManagementBasic = new Tab('AccessManagementBasic', _t('SilvercartPaymentMethod.ACCESS_MANAGEMENT_BASIC_LABEL', 'General'));
        $tabAccessManagementGroup = new Tab('AccessManagementGroup', _t('SilvercartPaymentMethod.ACCESS_MANAGEMENT_GROUP_LABEL', 'By group(s)'));
        $tabAccessManagementUser  = new Tab('AccessManagementUser',  _t('SilvercartPaymentMethod.ACCESS_MANAGEMENT_USER_LABEL', 'By user(s)'));
        $tabsetAccessManagement->push($tabAccessManagementBasic);
        $tabsetAccessManagement->push($tabAccessManagementGroup);
        $tabsetAccessManagement->push($tabAccessManagementUser);
        
        $showOnlyForGroupsTable = new GridField(
                'ShowOnlyForGroups',
                $this->fieldLabel('ShowOnlyForGroups'),
                $this->ShowOnlyForGroups(),
                SilvercartGridFieldConfig_RelationEditor::create()
        );
        $showNotForGroupsTable = new GridField(
                'ShowNotForGroups',
                $this->fieldLabel('ShowNotForGroups'),
                $this->ShowNotForGroups(),
                SilvercartGridFieldConfig_RelationEditor::create()
        );
        $showOnlyForUsersTable = new GridField(
                'ShowOnlyForUsers',
                $this->fieldLabel('ShowOnlyForUsers'),
                $this->ShowOnlyForUsers(),
                SilvercartGridFieldConfig_RelationEditor::create()
        );
        $showNotForUsersTable = new GridField(
                'ShowNotForUsers',
                $this->fieldLabel('ShowNotForUsers'),
                $this->ShowNotForUsers(),
                SilvercartGridFieldConfig_RelationEditor::create()
        );
        
        $restrictionByOrderQuantityField = new TextField('orderRestrictionMinQuantity', '');
        
        $restrictionByOrderStatusField = new GridField(
                'OrderRestrictionStatus',
                $this->fieldLabel('SilvercartOrderStatus'),
                $this->OrderRestrictionStatus(),
                SilvercartGridFieldConfig_RelationEditor::create()
        );
        
        // Access management basic --------------------------------------------
        $tabAccessManagementBasic->push(
            new HeaderField('RestrictionLabel', _t('SilvercartPaymentMethod.RESTRICTION_LABEL').':', 2)
        );
        $tabAccessManagementBasic->push(new LiteralField('separatorForActivationByOrderRestrictions', '<hr />'));
        $tabAccessManagementBasic->push(
            new CheckboxField(
                'enableActivationByOrderRestrictions',
                _t('SilvercartPaymentMethod.ENABLE_RESTRICTION_BY_ORDER_LABEL')
            )
        );
        $tabAccessManagementBasic->push(
            new LiteralField('RestrictionByOrderQuantityLabel', '<p>'._t('SilvercartPaymentMethod.RESTRICT_BY_ORDER_QUANTITY').':</p>')
        );
        $tabAccessManagementBasic->push($restrictionByOrderQuantityField);
        $tabAccessManagementBasic->push(
            new LiteralField('RestrictionByOrderStatusLabel', '<p>'._t('SilvercartPaymentMethod.RESTRICT_BY_ORDER_STATUS').':</p>')
        );
        $tabAccessManagementBasic->push(
            $restrictionByOrderStatusField
        );
        
        // Access management for groups ---------------------------------------
        $tabAccessManagementGroup->push(
            new HeaderField('ShowOnlyForGroupsLabel', _t('SilvercartPaymentMethod.SHOW_ONLY_FOR_GROUPS_LABEL').':', 2)
        );
        $tabAccessManagementGroup->push($showOnlyForGroupsTable);
        $tabAccessManagementGroup->push(
            new HeaderField('ShowNotForGroupsLabel', _t('SilvercartPaymentMethod.SHOW_NOT_FOR_GROUPS_LABEL').':', 2)
        );
        $tabAccessManagementGroup->push($showNotForGroupsTable);
        
        // Access management for users ----------------------------------------
        $tabAccessManagementUser->push(
            new HeaderField('ShowOnlyForUsersLabel', _t('SilvercartPaymentMethod.SHOW_ONLY_FOR_USERS_LABEL').':', 2)
        );
        $tabAccessManagementUser->push($showOnlyForUsersTable);
        $tabAccessManagementUser->push(
            new HeaderField('ShowNotForUsersLabel', _t('SilvercartPaymentMethod.SHOW_NOT_FOR_USERS_LABEL').':', 2)
        );
        $tabAccessManagementUser->push($showNotForUsersTable);
        
        // --------------------------------------------------------------------
        // GUI for additional charges / discounts
        // --------------------------------------------------------------------
        $tabSumModifiers = new Tab('SumModifiers', _t('SilvercartPaymentMethod.PAYMENT_SUMMODIFIERS'));
        $tabset->push($tabSumModifiers);
        
        $useSumModificationField = new CheckboxField('useSumModification', _t('SilvercartPaymentMethod.PAYMENT_USE_SUMMODIFICATION'));
        
        $impactFieldValues = array(
            'productValue'  => _t('SilvercartPaymentMethod.PAYMENT_MODIFY_PRODUCTVALUE'),
            'totalValue'    => _t('SilvercartPaymentMethod.PAYMENT_MODIFY_TOTALVALUE')
        );
        $impactField = new OptionsetField('sumModificationImpact', _t('SilvercartPaymentMethod.PAYMENT_SUMMODIFICATIONIMPACT').':', $impactFieldValues);
        
        $impactTypeValues = array(
            'charge'    => _t('SilvercartPaymentMethod.PAYMENT_MODIFY_TYPE_CHARGE'),
            'discount'  => _t('SilvercartPaymentMethod.PAYMENT_MODIFY_TYPE_DISCOUNT')
        );
        $impactTypeField = new OptionsetField('sumModificationImpactType', _t('SilvercartPaymentMethod.PAYMENT_SUMMODIFICATIONIMPACTTYPE').':', $impactTypeValues);

        $impactValueField = new TextField('sumModificationValue', _t('SilvercartPaymentMethod.PAYMENT_SUMMODIFICATIONVALUE').':');

        $impactValueTypeValues = array(
            'absolute'  => _t('SilvercartPaymentMethod.PAYMENT_IMPACT_TYPE_ABSOLUTE'),
            'percent'   => _t('SilvercartPaymentMethod.PAYMENT_IMPACT_TYPE_PERCENT')
        );
        $impactValueTypeField = new OptionsetField('sumModificationValueType', _t('SilvercartPaymentMethod.PAYMENT_SUMMODIFICATIONIMPACTVALUETYPE').':', $impactValueTypeValues);
        
        $impactLabelField = new TextField('sumModificationLabel', _t('SilvercartPaymentMethod.PAYMENT_SUMMODIFICATIONLABELFIELD'));
        
        $tabSumModifiers->push($useSumModificationField);
        $tabSumModifiers->push($impactField);
        $tabSumModifiers->push($impactTypeField);
        $tabSumModifiers->push($impactValueField);
        $tabSumModifiers->push($impactValueTypeField);
        $tabSumModifiers->push($impactLabelField);
        
        // --------------------------------------------------------------------
        // Countries
        // --------------------------------------------------------------------
        $countriesTab = new Tab('SilvercartCountries', $this->fieldLabel('SilvercartCountries'));
        $tabset->push($countriesTab);
        $countriesTable = new GridField(
                'SilvercartCountries',
                $this->fieldLabel('SilvercartCountries'),
                $this->SilvercartCountries(),
                SilvercartGridFieldConfig_RelationEditor::create()
                );
        $countriesTab->push($countriesTable);
        
        // --------------------------------------------------------------------
        // shipping methods
        // --------------------------------------------------------------------
        $shippingMethodsTab     = new Tab('SilvercartShippingMethods', $this->fieldLabel('SilvercartShippingMethods'));
        $shippingMethodsDesc    = new HeaderField('SilvercartShippingMethodsDesc', _t('SilvercartPaymentMethod.SHIPPINGMETHOD_DESC'));
        
        $shippingMethodsTable = new GridField(
                'SilvercartShippingMethods',
                _t('SilvercartPaymentMethod.SHIPPINGMETHOD', 'shipping method'),
                $this->SilvercartShippingMethods(),
                SilvercartGridFieldConfig_RelationEditor::create(50)
        );
        $tabset->push($shippingMethodsTab);
        $shippingMethodsTab->push($shippingMethodsDesc);
        $shippingMethodsTab->push($shippingMethodsTable);
        return new FieldList($tabset);
    }

    /**
     * Returns the original CMSFields.
     *
     * @return FieldList
     * @deprecated since version 2.0 we are using SilvercartDataObject::getCMSFields()
     */
    public function getCMSFieldsOriginal() {
        return parent::getCMSFields();
    }

    /**
     * set the link to be visited on a cancel action
     *
     * @param string $link the url
     *
     * @return void
     */
    public function setCancelLink($link) {
        $this->cancelLink = $link;
    }

    /**
     * sets the link to return to the shop
     *
     * @param string $link the url
     *
     * @return void
     */
    public function setReturnLink($link) {
        $this->returnLink = $link;
    }

    /**
     * set the controller
     *
     * @param Controller $controller the controller action
     *
     * @return void
     */
    public function setController($controller) {
        $this->controller = $controller;
    }

    /**
     * Returns the attributed countries as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function AttributedCountries() {
        return SilvercartTools::AttributedDataObject($this->SilvercartCountries());
    }

    /**
     * Returns the attributed zones as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function AttributedZones() {
        return SilvercartTools::AttributedDataObject($this->SilvercartZone());
    }

    /**
     * Returns the activation status as HTML-Checkbox-Tag.
     *
     * @return CheckboxField
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.06.2012
     */
    public function activatedStatus() {
        $checkboxField = new CheckboxField('isActivated' . $this->ID, 'isActived', $this->isActive);
        $checkboxField->setReadonly(true);
        $checkboxField->setDisabled(true);
        return $checkboxField;
    }

    /**
     * writes a log entry
     *
     * @param string $context the context for the log entry
     * @param string $text    the text for the log entry
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 17.11.2010
     */
    public function Log($context, $text) {
        SilvercartConfig::Log($context, $text, $this->ClassName);
    }

    /**
     * registers an error
     *
     * @param string $errorText text for the error message
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function addError($errorText) {
        array_push($this->errorList, $errorText);
    }

    /**
     * Creates order status DB objects from the given list.
     *
     * @param array $orderStatusList The order status list as associative array
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function createRequiredOrderStatus($orderStatusList) {
        foreach ($orderStatusList as $code => $title) {
            if (!DataObject::get_one('SilvercartOrderStatus', sprintf("\"Code\"='%s'", $code), true, "SilvercartOrderStatus.ID")) {
                $silvercartOrderStatus = new SilvercartOrderStatus();
                $silvercartOrderStatus->Title = $title;
                $silvercartOrderStatus->Code = $code;
                $silvercartOrderStatus->write();
            }
        }
    }

    /**
     * Creates the upload folder for payment images if it doesn't exist.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function createUploadFolder() {
        $uploadsFolder = DataObject::get_one('Folder', "\"Name\"='Uploads'");

        if (!$uploadsFolder) {
            $uploadsFolder = new Folder();
            $uploadsFolder->Name = 'Uploads';
            $uploadsFolder->Title = 'Uploads';
            $uploadsFolder->Filename = 'assets/Uploads/';
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function createLogoImageObjects($paymentLogos, $paymentModuleName) {
        //make sure that the folder "Uploads" exists
        Folder::find_or_make('Uploads');
        $paymentModule = SilvercartPaymentMethod::get()->filter(array("ClassName" => $paymentModuleName))->sort(array("ID" => "ASC"))->first();
        if ($paymentModule) {
            if (count($this->getPossiblePaymentChannels()) > 0) {
                // Multiple payment channels
                foreach ($paymentLogos as $paymentChannel => $logos) {
                    $paymentChannelMethod = DataObject::get_one($paymentModuleName, sprintf("\"PaymentChannel\"='%s'", $paymentChannel), true, $paymentModuleName.".ID");
                    if ($paymentChannelMethod) {
                        if (!$paymentChannelMethod->PaymentLogos()->exists()) {
                            foreach ($logos as $title => $logo) {
                                $paymentLogo = new SilvercartImage();
                                $paymentLogo->Title = $title;
                                $storedLogo = DataObject::get_one('Image', sprintf("\"Name\"='%s'", basename($logo)));
                                if ($storedLogo) {
                                    $paymentLogo->ImageID = $storedLogo->ID;
                                } else {
                                    file_put_contents(Director::baseFolder() . '/' . $this->uploadsFolder->Filename . basename($logo), file_get_contents(Director::baseFolder() . $logo));
                                    $image = new Image();
                                    $image->setFilename($this->uploadsFolder->Filename . basename($logo));
                                    $image->setName(basename($logo));
                                    $image->Title = basename($logo, '.png');
                                    $image->ParentID = $this->uploadsFolder->ID;
                                    $image->write();
                                    $paymentLogo->ImageID = $image->ID;
                                }
                                $paymentLogo->write();
                                $paymentChannelMethod->PaymentLogos()->add($paymentLogo);
                            }
                        }
                    }
                }
            } else {
                // Single payment channels
                foreach ($paymentLogos as $title => $logo) {
                    if (!$paymentModule->PaymentLogos()->exists()) {

                        $paymentLogo = new SilvercartImage();
                        $paymentLogo->Title = $title;
                        $storedLogo = DataObject::get_one('Image', sprintf("\"Name\"='%s'", basename($logo)));

                        if ($storedLogo) {
                            $paymentLogo->ImageID = $storedLogo->ID;
                        } else {
                            file_put_contents(Director::baseFolder() . '/' . $this->uploadsFolder->Filename . basename($logo), file_get_contents(Director::baseFolder() . $logo));
                            $image = new Image();
                            $image->setFilename($this->uploadsFolder->Filename . basename($logo));
                            $image->setName(basename($logo));
                            $image->Title = basename($logo, '.png');
                            $image->ParentID = $this->uploadsFolder->ID;
                            $image->write();
                            $paymentLogo->ImageID = $image->ID;
                        }
                        $paymentLogo->write();
                        $paymentModule->PaymentLogos()->add($paymentLogo);
                    }
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
    public function setCustomerDetails(Member $customerDetails) {
        $this->customerDetails = $customerDetails;
    }

    /**
     * Sets the invoice address
     *
     * @param SilvercartAddress $invoiceAddress Invoice address
     *
     * @return void
     */
    public function setInvoiceAddress(SilvercartAddress $invoiceAddress) {
        $this->invoiceAddress = $invoiceAddress;
    }

    /**
     * Sets the shipping address
     *
     * @param SilvercartAddress $shippingAddress Shipping address
     *
     * @return void
     */
    public function setShippingAddress(SilvercartAddress $shippingAddress) {
        $this->shippingAddress = $shippingAddress;
    }

    /**
     * Sets the shopping cart
     *
     * @param SilvercartShoppingCart $shoppingCart Shopping cart
     *
     * @return void
     */
    public function setShoppingCart(SilvercartShoppingCart $shoppingCart) {
        $this->shoppingCart = $shoppingCart;
    }

    /**
     * Sets the order object
     *
     * @param SilvercartOrder $order The order object
     *
     * @return void
     */
    public function setOrder(SilvercartOrder $order) {
        $this->order = $order;
    }

    /**
     * Returns the customers details
     *
     * @return Member
     */
    public function getCustomerDetails() {
        return $this->customerDetails;
    }

    /**
     * Returns the invoice address
     *
     * @return SilvercartAddress
     */
    public function getInvoiceAddress() {
        return $this->invoiceAddress;
    }

    /**
     * Returns the shipping address
     *
     * @return SilvercartAddress
     */
    public function getShippingAddress() {
        return $this->shippingAddress;
    }

    /**
     * Returns the shopping cart
     *
     * @return SilvercartShoppingCart
     */
    public function getShoppingCart() {
        return $this->shoppingCart;
    }

    /**
     * Returns the step configuration.
     *
     * Should return an array with the following structure:
     * array(
     *     '{insert module-filesystem-name}/templates/checkout/' => array(
     *         'prefix' => 'SilvercartPayment{insert module-class-name}CheckoutFormStep'
     *     )
     * )
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.04.2011
     */
    public function getStepConfiguration() {
        $directory = 'silvercart_payment_' . strtolower($this->moduleName) . '/templates/checkout/';
        $className = $this->ClassName;
        /**
         * original expression
         * $has_multiple_payment_channels = $className::$has_multiple_payment_channels;
         * was replaced with eval call to provide compatibility to PHP 5.2
         */
        $has_multiple_payment_channels = eval('return ' . $className . '::$has_multiple_payment_channels;');
        if ($has_multiple_payment_channels
            && !empty($this->PaymentChannel)
            && is_string($this->PaymentChannel)) {
            
            $directory .= $this->PaymentChannel . '/';
            $stepModule = $this->moduleName . ucfirst($this->PaymentChannel);
        } else {
            $stepModule = $this->moduleName;
        }
        if ($this->ShowFormFieldsOnPaymentSelection) {
            $stepModule .= 'Preceded';
        }
        $prefix = 'SilvercartPayment' . $stepModule . 'CheckoutFormStep';
        return array(
            $directory => array(
                'prefix' => $prefix,
            ),
        );
    }

    /**
     * Returns the order
     *
     * @return SilvercartOrder
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * Sets the customers details by checkout data
     *
     * @param array $checkoutData Checkout data
     *
     * @return void
     */
    public function setCustomerDetailsByCheckoutData($checkoutData) {
        $customerDetails = new Member();
        $customerDetails->Email      = isset($checkoutData['Email']) ? $checkoutData['Email'] : '';
        $customerDetails->Salutation = isset($checkoutData['Invoice_Salutation']) ? $checkoutData['Invoice_Salutation'] : '';
        $customerDetails->FirstName  = isset($checkoutData['Invoice_FirstName']) ? $checkoutData['Invoice_FirstName'] : '';
        $customerDetails->Surname    = isset($checkoutData['Invoice_Surname']) ? $checkoutData['Invoice_Surname'] : '';
        $this->setCustomerDetails($customerDetails);
    }

    /**
     * Sets the customers details by checkout data
     *
     * @param array $checkoutData Checkout data
     *
     * @return void
     */
    public function setInvoiceAddressByCheckoutData($checkoutData) {
        $invoiceAddress = new SilvercartAddress();
        $invoiceAddress->Salutation             = isset($checkoutData['Invoice_Salutation'])    ? $checkoutData['Invoice_Salutation'] : '';
        $invoiceAddress->FirstName              = isset($checkoutData['Invoice_FirstName'])     ? $checkoutData['Invoice_FirstName'] : '';
        $invoiceAddress->Surname                = isset($checkoutData['Invoice_Surname'])       ? $checkoutData['Invoice_Surname'] : '';
        $invoiceAddress->Street                 = isset($checkoutData['Invoice_Street'])        ? $checkoutData['Invoice_Street'] : '';
        $invoiceAddress->StreetNumber           = isset($checkoutData['Invoice_StreetNumber'])  ? $checkoutData['Invoice_StreetNumber'] : '';
        $invoiceAddress->Postcode               = isset($checkoutData['Invoice_Postcode'])      ? $checkoutData['Invoice_Postcode'] : '';
        $invoiceAddress->City                   = isset($checkoutData['Invoice_City'])          ? $checkoutData['Invoice_City'] : '';
        $invoiceAddress->CountryID              = isset($checkoutData['Invoice_Country'])       ? $checkoutData['Invoice_Country'] : '';
        $invoiceAddress->SilvercartCountryID    = isset($checkoutData['Invoice_Country'])       ? $checkoutData['Invoice_Country'] : '';
        $invoiceAddress->PhoneAreaCode          = isset($checkoutData['Invoice_PhoneAreaCode']) ? $checkoutData['Invoice_PhoneAreaCode'] : '';
        $invoiceAddress->Phone                  = isset($checkoutData['Invoice_Phone'])         ? $checkoutData['Invoice_Phone'] : '';
        // Insert SilvercartCountry object
        $invoiceAddress->Country                = DataObject::get_by_id('SilvercartCountry', $invoiceAddress->CountryID);

        $this->setInvoiceAddress($invoiceAddress);
    }

    /**
     * Sets the customers details by checkout data
     *
     * @param array $checkoutData Checkout data
     *
     * @return void
     */
    public function setShippingAddressByCheckoutData($checkoutData) {
        $shippingAddress = new SilvercartAddress();
        $shippingAddress->Salutation            = isset($checkoutData['Shipping_Salutation'])       ? $checkoutData['Shipping_Salutation'] : '';
        $shippingAddress->FirstName             = isset($checkoutData['Shipping_FirstName'])        ? $checkoutData['Shipping_FirstName'] : '';
        $shippingAddress->Surname               = isset($checkoutData['Shipping_Surname'])          ? $checkoutData['Shipping_Surname'] : '';
        $shippingAddress->Street                = isset($checkoutData['Shipping_Street'])           ? $checkoutData['Shipping_Street'] : '';
        $shippingAddress->StreetNumber          = isset($checkoutData['Shipping_StreetNumber'])     ? $checkoutData['Shipping_StreetNumber'] : '';
        $shippingAddress->Postcode              = isset($checkoutData['Shipping_Postcode'])         ? $checkoutData['Shipping_Postcode'] : '';
        $shippingAddress->City                  = isset($checkoutData['Shipping_City'])             ? $checkoutData['Shipping_City'] : '';
        $shippingAddress->CountryID             = isset($checkoutData['Shipping_Country'])          ? $checkoutData['Shipping_Country'] : '';
        $shippingAddress->SilvercartCountryID   = isset($checkoutData['Shipping_Country'])          ? $checkoutData['Shipping_Country'] : '';
        $shippingAddress->PhoneAreaCode         = isset($checkoutData['Shipping_PhoneAreaCode'])    ? $checkoutData['Shipping_PhoneAreaCode'] : '';
        $shippingAddress->Phone                 = isset($checkoutData['Shipping_Phone'])            ? $checkoutData['Shipping_Phone'] : '';

        if (array_key_exists('Shipping_IsPackstation', $checkoutData)) {
            $shippingAddress->IsPackstation = $checkoutData['Shipping_IsPackstation'];
        } else {
            $shippingAddress->IsPackstation = false;
        }
        if (array_key_exists('Shipping_PostNumber', $checkoutData)) {
            $shippingAddress->PostNumber = $checkoutData['Shipping_PostNumber'];
        }
        if (array_key_exists('Shipping_Packstation', $checkoutData)) {
            $shippingAddress->Packstation = $checkoutData['Shipping_Packstation'];
        }

        // Insert SilvercartCountry object
        $shippingAddress->Country = DataObject::get_by_id('SilvercartCountry', $shippingAddress->CountryID);

        $this->setShippingAddress($shippingAddress);
    }

    /**
     * Returns all possible payment channels of the current payment module.
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 31.03.2011
     */
    public function getPossiblePaymentChannels() {
        $possiblePaymentChannels = array();
        $className = $this->ClassName;
        /**
         * original expression
         * $has_multiple_payment_channels = $className::$has_multiple_payment_channels;
         * was replaced with eval call to provide compatibility to PHP 5.2
         */
        $has_multiple_payment_channels = eval('return ' . $className . '::$has_multiple_payment_channels;');
        $possible_payment_channels = eval('return ' . $className . '::$possible_payment_channels;');
        if ($has_multiple_payment_channels == false
            || count($possible_payment_channels) == 0) {
            return array();
        }
        foreach ($possible_payment_channels as $key => $value) {
            $possiblePaymentChannels[$key] = _t($this->ClassName . '.PAYMENT_CHANNEL_' . strtoupper($key), $value);
        }
        return $possiblePaymentChannels;
    }

    /**
     * Returns the i18n title for a payment channel.
     *
     * @param string $paymentChannel The payment channel
     *
     * @return string
     */
    public function getPaymentChannelName($paymentChannel) {
        return _t($this->ClassName . '.PAYMENT_CHANNEL_' . strtoupper($paymentChannel));
    }
    
    /**
     * Returns an optional payment specific form name to insert into checkout step 3.
     *
     * @return string|boolean
     */
    public function getNestedFormName() {
        return false;
    }

    /**
     * Returns the URL for payment notifications.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function getNotificationUrl() {
        $notifyUrl = Director::absoluteUrl(
            SilvercartTools::PageByIdentifierCode('SilvercartPaymentNotification')->Link().'process/'.$this->moduleName
        );

        return $notifyUrl;
    }
}
