<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Base
 */

/**
 * Theses are the shipping methods the shop offers
 *
 * @package Silvercart
 * @subpackage Base
 * @author Roland Lehmann <rlehmann@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright pixeltricks GmbH
 * @since 17.07.2013
 * @license see license file in modules root directory
 */
class SilvercartShippingMethod extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'isActive'                      => 'Boolean',
        'priority'                      => 'Int',
        'DoNotShowOnShippingFeesPage'   => 'Boolean',
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
        'SilvercartCarrier'   => 'SilvercartCarrier',
    );
    /**
     * Has-many relationship.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartOrders' => 'SilvercartOrder',
        'SilvercartShippingFees' => 'SilvercartShippingFee',
        'SilvercartShippingMethodLanguages' => 'SilvercartShippingMethodLanguage'
    );
    /**
     * Many-many relationships.
     *
     * @var array
     */
    public static $many_many = array(
        'SilvercartZones'           => 'SilvercartZone',
        'SilvercartCustomerGroups'  => 'Group',
    );
    /**
     * Belongs-many-many relationships.
     *
     * @var array
     */
    public static $belongs_many_many = array(
        'SilvercartPaymentMethods' => 'SilvercartPaymentMethod',
    );
    /**
     * Virtual database columns.
     *
     * @var array
     */
    public static $casting = array(
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
    );

    /**
     * Grant API access on this item.
     *
     * @var bool
     */
    public static $api_access = true;
    
    /**
     * Default sort field and direction
     *
     * @var string
     */
    public static $default_sort = "priority DESC";
    
    /**
     * Shipping address
     *
     * @var SilvercartAddress
     */
    protected $shippingAddress = null;
    
    /**
     * Shipping country
     *
     * @var SilvercartCountry
     */
    protected $shippingCountry = null;
    
    /**
     * Searchable fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012
     */
    public function searchableFields() {
        $searchableFields = array(
            'SilvercartShippingMethodLanguages.Title' => array(
                'title' => $this->fieldLabel('Title'),
                'filter' => 'PartialMatchFilter'
            ),
            'isActive' => array(
                'title' => $this->fieldLabel('isActive'),
                'filter' => 'ExactMatchFilter'
            ),
            'SilvercartCarrier.ID' => array(
                'title' => $this->fieldLabel('SilvercartCarrier'),
                'filter' => 'ExactMatchFilter'
            ),
            'SilvercartZones.ID' => array(
                'title' => $this->fieldLabel('SilvercartZones'),
                'filter'    => 'ExactMatchFilter'
            ),
            'SilvercartCustomerGroups.ID' => array(
                'title' => $this->fieldLabel('SilvercartCustomerGroups'),
                'filter'    => 'ExactMatchFilter'
            )
        );
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
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                array(
                        'Title'                             => _t('SilvercartProduct.COLUMN_TITLE'),
                        'Description'                       => _t('SilvercartShippingMethod.DESCRIPTION'),
                        'DescriptionForShippingFeesPage'    => _t('SilvercartShippingMethod.DescriptionForShippingFeesPage'),
                        'activatedStatus'                   => _t('SilvercartShopAdmin.PAYMENT_ISACTIVE'),
                        'priority'                          => _t('Silvercart.PRIORITY'),
                        'AttributedZones'                   => _t('SilvercartShippingMethod.FOR_ZONES', 'for zones'),
                        'isActive'                          => _t('SilvercartPage.ISACTIVE', 'active'),
                        'SilvercartCarrier'                 => _t('SilvercartCarrier.SINGULARNAME', 'carrier'),
                        'SilvercartShippingFees'            => _t('SilvercartShippingFee.PLURALNAME', 'shipping fees'),
                        'SilvercartZones'                   => _t('SilvercartZone.PLURALNAME', 'zones'),
                        'SilvercartCustomerGroups'          => _t('SilvercartCustomerGroup.PLURALNAME'),
                        'SilvercartShippingMethodLanguages' => _t('SilvercartConfig.TRANSLATION'),
                        'DoNotShowOnShippingFeesPage'       => _t('SilvercartShippingMethod.DoNotShowOnShippingFeesPage'),
                        'ExpectedDelivery'                  => _t('SilvercartShippingMethod.ExpectedDelivery'),
                        'DeliveryTime'                      => _t('SilvercartShippingMethod.DeliveryTime'),
                        'DeliveryTimeMin'                   => _t('SilvercartShippingMethod.DeliveryTimeMin'),
                        'DeliveryTimeMinDesc'               => _t('SilvercartShippingMethod.DeliveryTimeMinDesc'),
                        'DeliveryTimeMax'                   => _t('SilvercartShippingMethod.DeliveryTimeMax'),
                        'DeliveryTimeMaxDesc'               => _t('SilvercartShippingMethod.DeliveryTimeMaxDesc'),
                        'DeliveryTimeText'                  => _t('SilvercartShippingMethod.DeliveryTimeText'),
                        'DeliveryTimeTextDesc'              => _t('SilvercartShippingMethod.DeliveryTimeTextDesc'),
                    )
        );
    }
    
    /**
     * Sets the summary fields.
     *
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'SilvercartCarrier.Title'   => $this->fieldLabel('SilvercartCarrier'),
            'Title'                     => $this->fieldLabel('Title'),
            'activatedStatus'           => $this->fieldLabel('activatedStatus'),
            'AttributedZones'           => $this->fieldLabel('AttributedZones'),
            'AttributedCustomerGroups'  => $this->fieldLabel('SilvercartCustomerGroups'),
            'priority'                  => $this->fieldLabel('priority'),
        );
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
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
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
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
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
    public function excludeFromScaffolding() {
        $excludeFromScaffolding = array(
            'SilvercartCountries',
            'SilvercartPaymentMethods',
            'SilvercartOrders',
        );
        
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
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this, 'SilvercartCarrierID', false);
        
        $fields->dataFieldByName('DeliveryTimeMin')->setDescription($this->fieldLabel('DeliveryTimeMinDesc'));
        $fields->dataFieldByName('DeliveryTimeMax')->setDescription($this->fieldLabel('DeliveryTimeMaxDesc'));
        $fields->dataFieldByName('DeliveryTimeText')->setDescription($this->fieldLabel('DeliveryTimeTextDesc'));

        if ($this->isInDB()) {
            $feeTable           = $fields->dataFieldByName('SilvercartShippingFees');
            $feesTableConfig    = $feeTable->getConfig();
            $exportButton       = new GridFieldExportButton();
            $exportColumsArray  = array(
                            'ID',
                            'MaximumWeight',
                            'UnlimitedWeight',
                            'PriceAmount',
                            'PriceCurrency',
                            'SilvercartZoneID',
                            'SilvercartShippingMethodID',
                            'SilvercartTaxID',
                        );
            $exportButton->setExportColumns($exportColumsArray);
            $feesTableConfig->addComponent($exportButton);
            $feesTableConfig->removeComponentsByType('SilvercartGridFieldAddExistingAutocompleter');
            $feesTableConfig->removeComponentsByType('GridFieldDeleteAction');
            $feesTableConfig->addComponent(new GridFieldDeleteAction());
            
            if (class_exists('GridFieldSortableRows')) {
                $feesTableConfig->addComponent(new GridFieldSortableRows('priority'));
            }
        }
        
        return $fields;
    }
    
    /**
     * Returns whether to show this shipping method on shipping fees page.
     * 
     * @return bool
     */
    public function getShowOnShippingFeesPage() {
        return !$this->DoNotShowOnShippingFeesPage;
    }
    
    /**
     * determins the right shipping fee for a shipping method depending on the
     * cart's weight and the country of the customers shipping address
     * 
     * @param int $weight Weight in gramm to get fee for
     *
     * @return SilvercartShippingFee the most convenient shipping fee for this shipping method
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.04.2013
     */
    public function getShippingFee($weight = null) {
        $fee = false;

        if (is_null($weight)) {
            if (!Member::currentUser() ||
                !Member::currentUser()->SilvercartShoppingCart()) {
                return $fee;
            }
            $weight = Member::currentUser()->SilvercartShoppingCart()->getWeightTotal();
        }

        $shippingCountry = $this->getShippingCountry();
        if (is_null($shippingCountry)) {
            $shippingAddress = $this->getShippingAddress();
            if (is_null($shippingAddress)) {
                $shippingAddress = Controller::curr()->getShippingAddress();
                $this->setShippingAddress($shippingAddress);
                SilvercartTools::Log('getShippingFee', 'CAUTION: shipping address was not preset! Fallback to current controller ' . Controller::curr()->class, 'SilvercartShippingMethod');
            }
            if ($shippingAddress instanceof SilvercartAddress) {
                $shippingCountry = $shippingAddress->SilvercartCountry();
                $this->setShippingCountry($shippingCountry);
            }
        }
        
        if ($shippingCountry instanceof SilvercartCountry) {
            $zones = SilvercartZone::getZonesFor($shippingCountry->ID);
            
            if ($zones->exists()) {
                $zoneMap            = $zones->map('ID','ID');
                $zoneIDs            = $zoneMap->toArray();
                $zoneIDsAsString    = "'" . implode("','", $zoneIDs) . "'";
                $filter = array(
                    "SilvercartShippingMethodID" => $this->ID,
                );
                $fees = SilvercartShippingFee::get()
                                                ->filter($filter)
                                                ->where(
                                                        sprintf(
                                                                '("MaximumWeight" >= ' . $weight . ' OR "UnlimitedWeight" = 1) AND "SilvercartZoneID" IN (%s)',
                                                                $zoneIDsAsString
                                                        )
                                                )
                                                ->sort('PostPricing, PriceAmount');
                if ($fees->exists()) {
                    $fee = $fees->first();
                }
            }
        }
        return $fee;
    }
    
    /**
     * getter for the shipping methods title
     *
     * @return string the title in the corresponding front end language
     */
    public function getDescription() {
        return $this->getLanguageFieldValue('Description');
    }
    
    /**
     * getter for the shipping methods DescriptionForShippingFeesPage
     *
     * @return string the title in the corresponding front end language
     */
    public function getDescriptionForShippingFeesPage() {
        return $this->getLanguageFieldValue('DescriptionForShippingFeesPage');
    }
    
    /**
     * getter for the shipping methods title
     *
     * @return string the title in the corresponding front end language 
     */
    public function getTitle() {
        return $this->getLanguageFieldValue('Title');
    }

    /**
     * pseudo attribute which can be called with $this->TitleWithCarrierAndFee
     *
     * @return string carrier + title + fee
     */
    public function getTitleWithCarrierAndFee() {
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
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 11.10.2011
     */
    public function getTitleWithCarrier() {
        if ($this->SilvercartCarrier()) {
            return $this->SilvercartCarrier()->Title . " - " . $this->Title;
        }
        return false;
    }
    
    /**
     * Returns the delivery time as string.
     * 
     * @return string
     */
    public function getDeliveryTime() {
        $deliveryTime = self::get_delivery_time($this->getShippingFee());
        if (empty($deliveryTime)) {
            $deliveryTime = self::get_delivery_time($this);
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
    public static function get_delivery_time($context, $forceDisplayInDays = false) {
        $deliveryTime = '';
        if ($context != false) {
            if (!empty($context->DeliveryTimeText)) {
                $deliveryTime = $context->DeliveryTimeText;
            } elseif ($context->DeliveryTimeMin > 0) {
                if (self::isInCheckoutContextWithPrepayment() ||
                    $forceDisplayInDays) {
                    $deliveryTime  = $context->DeliveryTimeMin;
                    if ($context->DeliveryTimeMax > 0) {
                        $deliveryTime .= ' - ';
                        $deliveryTime .= $context->DeliveryTimeMax;
                    }
                    if ($deliveryTime == 1) {
                        $deliveryTime .= ' ' . _t('Silvercart.BusinessDay');
                    } else {
                        $deliveryTime .= ' ' . _t('Silvercart.BusinessDays');
                    }
                    $deliveryTime .= ' ' . _t('SilvercartShippingMethod.DeliveryTimePrepaymentHint');
                } else {
                    $deliveryTime  = SilvercartTools::getDateNice(date(_t('Silvercart.DATEFORMAT'), time() + (self::addSundaysToDeliveryTime($context->DeliveryTimeMin)*60*60*24)), true, true, true);
                    if ($context->DeliveryTimeMax > 0) {
                        $deliveryTime .= ' - ';
                        $deliveryTime .= SilvercartTools::getDateNice(date(_t('Silvercart.DATEFORMAT'), time() + (self::addSundaysToDeliveryTime($context->DeliveryTimeMax)*60*60*24)), true, true, true);
                    }
                }
            }
        }
        return $deliveryTime;
    }
    
    /**
     * Adds the sundays to the delivery time.
     * 
     * @param int $deliveryTime Delivery time in days
     * 
     * @return int
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2014
     */
    protected static function addSundaysToDeliveryTime($deliveryTime) {
        $currentWeekDay = date('N');
        $sundaysPlain   = floor(($deliveryTime + $currentWeekDay) / 7);
        $sundaysTotal   = floor(($deliveryTime + $currentWeekDay + $sundaysPlain) / 7);
        return $deliveryTime + $sundaysTotal;
    }
    
    /**
     * Returns whether the current application context is in checkout with 
     * prepayment as payment method.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2014
     */
    protected static function isInCheckoutContextWithPrepayment() {
        $isPrepayment = false;
        if (Controller::curr() instanceof SilvercartCheckoutStep_Controller) {
            $checkout = Controller::curr();
            /*@var $checkout SilvercartCheckoutStep_Controller */
            $checkoutStep = $checkout->getCurrentFormInstance();
            if ($checkoutStep instanceof SilvercartCheckoutFormStep5) {
                $paymentMethod = $checkoutStep->SilvercartShoppingCart()->getPaymentMethod();
                if ($paymentMethod instanceof SilvercartPaymentPrepayment &&
                    $paymentMethod->PaymentChannel == 'prepayment') {
                    $isPrepayment = true;
                }
            }
        }
        return $isPrepayment;
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
    public function AttributedCustomerGroups($dbField = "Title") {
        return SilvercartTools::AttributedDataObject($this->SilvercartCustomerGroups(), $dbField);
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
    public function AttributedZones($dbField = "Title") {
        return SilvercartTools::AttributedDataObject($this->SilvercartZones(), $dbField);
    }

    /**
     * Returns the attributed zones as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.01.2012
     */
    public function AttributedZoneIDs() {
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
    public function AttributedPaymentMethods($dbField = "Name") {
        return SilvercartTools::AttributedDataObject($this->SilvercartPaymentMethods(), $dbField);
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
     * Checks whether this shipping method has a fee with activated post pricing
     *
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2012
     */
    public function hasFeeWithPostPricing() {
        $hasFeeWithPostPricing = false;
        foreach ($this->SilvercartShippingFees() as $shippingFee) {
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
     * @param SilvercartCarrier $carrier         Carrier to get shipping methods for
     * @param SilvercartAddress $shippingAddress Address to get shipping methods for
     *
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.10.2011
     */
    public static function getAllowedShippingMethods($carrier = null, $shippingAddress = null) {
        $extendableShippingMethod       = singleton('SilvercartShippingMethod');
        $allowedShippingMethodsArray    = array();
        $shippingMethods                = self::getAllowedShippingMethodsBase($carrier);

        if ($shippingMethods) {
            foreach ($shippingMethods as $shippingMethod) {
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
        
        $allowedShippingMethods = new ArrayList($allowedShippingMethodsArray);
        
        $extendableShippingMethod->extend('updateAllowedShippingMethods', $allowedShippingMethods);
        
        return $allowedShippingMethods;
    }
    
    /**
     * Returns allowed shipping methods. Those are active
     * 
     * @param SilvercartCarrier $carrier Carrier to get shipping methods for
     *
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2012
     */
    public static function getAllowedShippingMethodsForOverview($carrier = null) {
        $shippingMethods = self::getAllowedShippingMethodsBase($carrier);
        return $shippingMethods;
    }
    
    /**
     * Returns allowed shipping methods. Those are active
     * 
     * @param SilvercartCarrier $carrier Carrier to get shipping methods for
     *
     * @return SS_List
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.02.2013
     */
    public static function getAllowedShippingMethodsBase($carrier = null) {
        $extendedFilter = "";
        if (!is_null($carrier)) {
            $extendedFilter = sprintf(
                    ' AND "SilvercartShippingMethod"."SilvercartCarrierID" = \'%s\'',
                    $carrier->ID
            );
        }
        
        $customerGroups = SilvercartCustomer::getCustomerGroups();
        if ($customerGroups &&
            $customerGroups instanceof SS_List &&
            $customerGroups->exists()) {
            $customerGroupIDs   = implode(',', $customerGroups->map('ID', 'ID')->toArray());
            $filter = sprintf(
                '"SilvercartShippingMethod"."isActive" = 1 AND ("SilvercartShippingMethod_SilvercartCustomerGroups"."GroupID" IN (%s) OR "SilvercartShippingMethod"."ID" NOT IN (%s))%s',
                $customerGroupIDs,
                'SELECT "SilvercartShippingMethod_SilvercartCustomerGroups"."SilvercartShippingMethodID" FROM "SilvercartShippingMethod_SilvercartCustomerGroups"',
                $extendedFilter
            );
            
            $joinTable      = 'SilvercartShippingMethod_SilvercartCustomerGroups';
            $joinOnClause   = '"SilvercartShippingMethod_SilvercartCustomerGroups"."SilvercartShippingMethodID" = "SilvercartShippingMethod"."ID"';
            
            $shippingMethods = SilvercartShippingMethod::get()
                    ->leftJoin($joinTable, $joinOnClause)
                    ->where($filter);
        } else {
            $filter = sprintf(
                '"SilvercartShippingMethod"."isActive" = 1 AND ("SilvercartShippingMethod"."ID" NOT IN (%s))%s',
                'SELECT "SilvercartShippingMethod_SilvercartCustomerGroups"."SilvercartShippingMethodID" FROM "SilvercartShippingMethod_SilvercartCustomerGroups"',
                $extendedFilter
            );
            
            $shippingMethods = SilvercartShippingMethod::get()
                    ->where($filter);
        }
        
        return $shippingMethods;
    }
    
    /**
     * Returns all allowed shipping fees in the given products, countries
     * and customer groups context.
     *
     * @param SilvercartProduct $product       Product to get fee for
     * @param SilvercartCountry $country       Country to get fee for
     * @param Group             $customerGroup Customer group to get fee for
     * 
     * @return ArrayList
     */
    public static function getAllowedShippingFeesFor(SilvercartProduct $product, SilvercartCountry $country, Group $customerGroup) {
        $extendableShippingMethod   = singleton('SilvercartShippingMethod');
        
        $filter = sprintf(
            '"SilvercartShippingMethod"."isActive" = 1 AND ("SilvercartShippingMethod_SilvercartCustomerGroups"."GroupID" IN (%s) OR "SilvercartShippingMethod"."ID" NOT IN (%s))',
            $customerGroup->ID,
            'SELECT "SilvercartShippingMethod_SilvercartCustomerGroups"."SilvercartShippingMethodID" FROM "SilvercartShippingMethod_SilvercartCustomerGroups"'
        );
            
        $joinTable      = 'SilvercartShippingMethod_SilvercartCustomerGroups';
        $joinOnClause   = '"SilvercartShippingMethod_SilvercartCustomerGroups"."SilvercartShippingMethodID" = "SilvercartShippingMethod"."ID"';

        $shippingMethods = SilvercartShippingMethod::get()
                ->leftJoin($joinTable, $joinOnClause)
                ->where($filter);
        
        $extendableShippingMethod->extend('updateAllowedShippingFeesFor', $shippingMethods, $product);
        
        $shippingFees = new ArrayList();
        
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
     * @param SilvercartProduct $product       Product to get fee for
     * @param SilvercartCountry $country       Country to get fee for
     * @param Group             $customerGroup Customer group to get fee for
     * 
     * @return SilvercartShippingFee
     */
    public static function getAllowedShippingFeeFor(SilvercartProduct $product, SilvercartCountry $country, Group $customerGroup) {
        $shippingFees = self::getAllowedShippingFeesFor($product, $country, $customerGroup);
        return $shippingFees->First();
    }

    /**
     * Filters the given shipping methods by default permission criteria
     * 
     * @param SilvercartShippingMethod $shippingMethods Shipping methods to filter
     *
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.04.2012
     */
    public static function filterShippingMethods($shippingMethods) {
        $allowedShippingMethods = new ArrayList();
        $customerGroups         = SilvercartCustomer::getCustomerGroups();
        foreach ($shippingMethods as $shippingMethod) {
            foreach ($customerGroups as $customerGroup) {
                if ($shippingMethod->SilvercartCustomerGroups()->find('ID', $customerGroup->ID) ||
                    $shippingMethod->SilvercartCustomerGroups()->count() == 0) {
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
     * @return SilvercartAddress 
     */
    public function getShippingAddress() {
        return $this->shippingAddress;
    }

    /**
     * Sets the shipping address
     *
     * @param SilvercartAddress $shippingAddress Shipping address
     * 
     * @return void
     */
    public function setShippingAddress($shippingAddress) {
        $this->shippingAddress = $shippingAddress;
    }
    
    /**
     * Returns the shipping country
     *
     * @return SilvercartCountry
     */
    public function getShippingCountry() {
        return $this->shippingCountry;
    }

    /**
     * Sets the shipping country
     *
     * @param SilvercartCountry $shippingCountry Shipping country
     * 
     * @return void
     */
    public function setShippingCountry($shippingCountry) {
        $this->shippingCountry = $shippingCountry;
    }

}
