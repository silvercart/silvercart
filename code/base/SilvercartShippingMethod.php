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
 * Theses are the shipping methods the shop offers
 *
 * @package Silvercart
 * @subpackage Base
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 20.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartShippingMethod extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'isActive' => 'Boolean'
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
        'AttributedCountries'       => 'Varchar(255)',
        'activatedStatus'           => 'Varchar(255)',
        'AttributedCustomerGroups'  => 'Text',
        'AttributedZones'           => 'Text',
        'AttributedZoneIDs'         => 'Text',
        'Title'                     => 'Text',
        'Description'               => 'Text',
    );
    
    /**
     * Default sort field and direction
     *
     * @var string
     */
    public static $default_sort = "`SilvercartCarrierID`";
    
    /**
     * Shipping address
     *
     * @var SilvercartAddress
     */
    protected $shippingAddress = null;
    
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                array(
                        'Title'                             => _t('SilvercartProduct.COLUMN_TITLE'),
                        'Description'                       => _t('SilvercartShippingMethod.DESCRIPTION'),
                        'activatedStatus'                   => _t('SilvercartShopAdmin.PAYMENT_ISACTIVE'),
                        'AttributedZones'                   => _t('SilvercartShippingMethod.FOR_ZONES', 'for zones'),
                        'isActive'                          => _t('SilvercartPage.ISACTIVE', 'active'),
                        'SilvercartCarrier'                 => _t('SilvercartCarrier.SINGULARNAME', 'carrier'),
                        'SilvercartShippingFees'            => _t('SilvercartShippingFee.PLURALNAME', 'shipping fees'),
                        'SilvercartZones'                   => _t('SilvercartZone.PLURALNAME', 'zones'),
                        'SilvercartCustomerGroups'          => _t('Group.PLURALNAME'),
                        'SilvercartShippingMethodLanguages' => _t('SilvercartConfig.TRANSLATION'),
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
     * @since 5.7.2011
     */
    public function singular_name() {
        if (_t('SilvercartShippingMethod.SINGULARNAME')) {
            return _t('SilvercartShippingMethod.SINGULARNAME');
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
        if (_t('SilvercartShippingMethod.PLURALNAME')) {
            return _t('SilvercartShippingMethod.PLURALNAME');
        } else {
            return parent::plural_name();
        }   
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldSet the fields for the backend
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 28.10.10
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        //multilingual fields, in fact just the title
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguage());
        foreach ($languageFields as $languageField) {
            $fields->insertBefore($languageField, 'isActive');
        }
        $fields->removeByName('SilvercartCountries');
        $fields->removeByName('SilvercartPaymentMethods');
        $fields->removeByName('SilvercartOrders');
        $fields->removeByName('SilvercartZones');

        if ($this->ID > 0) {
            $feeTable = $fields->dataFieldByName('SilvercartShippingFees');
            if ($feeTable) {
                $permissions = array_merge(
                        $feeTable->getPermissions(),
                        array(
                            'export',
                        )
                );
                $feeTable->setPermissions($permissions);
                $feeTable->setFieldListCsv(
                        array(
                            'ID'                            => 'ID',
                            'MaximumWeight'                 => 'MaximumWeight',
                            'UnlimitedWeight'               => 'UnlimitedWeight',
                            'PriceAmount'                   => 'PriceAmount',
                            'PriceCurrency'                 => 'PriceCurrency',
                            'SilvercartZoneID'              => 'SilvercartZoneID',
                            'SilvercartShippingMethodID'    => 'SilvercartShippingMethodID',
                            'SilvercartTaxID'               => 'SilvercartTaxID',
                        )
                );
            }
        
            $zonesTable = new ManyManyComplexTableField(
                $this,
                'SilvercartZones',
                'SilvercartZone',
                null,
                'getCMSFields_forPopup'
            );
            $fields->findOrMakeTab('Root.SilvercartZones', $this->fieldLabel('SilvercartZones'));
            $fields->addFieldToTab('Root.SilvercartZones', $zonesTable);
        
            $groupsTable = new ManyManyComplexTableField(
                $this,
                'SilvercartCustomerGroups',
                'Group',
                null,
                'getCMSFields_forPopup'
            );
            $fields->findOrMakeTab('Root.SilvercartCustomerGroups', $this->fieldLabel('SilvercartCustomerGroups'));
            $fields->addFieldToTab('Root.SilvercartCustomerGroups', $groupsTable);
        }

        return $fields;
    }
    
    /**
     * determins the right shipping fee for a shipping method depending on the
     * cart's weight and the country of the customers shipping address
     *
     * @return SilvercartShippingFee the most convenient shipping fee for this shipping method
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2012
     */
    public function getShippingFee() {
        $fee             = false;

        if (!Member::currentUser() ||
            !Member::currentUser()->SilvercartShoppingCart()) {

            return $fee;
        }

        $cartWeightTotal = Member::currentUser()->SilvercartShoppingCart()->getWeightTotal();
        $shippingAddress = $this->getShippingAddress();
        if (is_null($shippingAddress)) {
            $shippingAddress = Controller::curr()->getShippingAddress();
            $this->setShippingAddress($shippingAddress);
            SilvercartTools::Log('getShippingFee', 'CAUTION: shipping address was not preset! Fallback to current controller ' . Controller::curr()->class, 'SilvercartShippingMethod');
        }
        if ($shippingAddress) {
            $zones = SilvercartZone::getZonesFor($shippingAddress->SilvercartCountryID);
            if ($zones) {
                $zoneMap            = $zones->map();
                $zoneIDs            = array_flip($zoneMap);
                $zoneIDsAsString    = "'" . implode("','", $zoneIDs) . "'";
                $fees               = DataObject::get(
                    'SilvercartShippingFee',
                    sprintf(
                        "`SilvercartShippingMethodID` = '%s' AND (`MaximumWeight` >= %d OR `UnlimitedWeight` = 1) AND `SilvercartZoneID` IN (%s)",
                        $this->ID,
                        $cartWeightTotal,
                        $zoneIDsAsString
                    ),
                    'PriceAmount'
                );

                if ($fees) {
                    $fee = $fees->First();
                }
            }
        }
        
        return $fee;
    }
    
    /**
     * getter for the shipping methods title
     *
     * @return string the title in the corresponding front end language 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012
     */
    public function getDescription() {
        $description = '';
        if ($this->getLanguage()) {
            $description = $this->getLanguage()->Description;
        }
        return $description;
    }
    
    /**
     * getter for the shipping methods title
     *
     * @return string the title in the corresponding front end language 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 11.01.2012
     */
    public function getTitle() {
        $title = '';
        if ($this->getLanguage()) {
            $title = $this->getLanguage()->Title;
        }
        return $title;
    }

    /**
     * pseudo attribute which can be called with $this->TitleWithCarrierAndFee
     *
     * @return string carrier + title + fee
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 15.11.2010
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
     * @copyright 2012 pixeltricks GmbH
     * @since 17.01.2012
     */
    public function AttributedZoneIDs() {
        return $this->AttributedZones('ID');
    }

    /**
     * Returns the attributed payment methods as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function AttributedPaymentMethods() {
        return SilvercartTools::AttributedDataObject($this->SilvercartPaymentMethods());
    }

    /**
     * Returns the activation status as HTML-Checkbox-Tag.
     *
     * @return CheckboxField
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function activatedStatus() {
        $checkboxField = new CheckboxField('isActivated' . $this->ID, 'isActived', $this->isActive);

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
     * @param SilvercartCarrier $carrier Carrier to get shipping methods for
     *
     * @return DataObjectSet
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.07.2011
     */
    public static function getAllowedShippingMethods($carrier = null) {
        $extendableShippingMethod   = singleton('SilvercartShippingMethod');
        $allowedShippingMethods     = array();
        $shippingMethods            = self::getAllowedShippingMethodsBase($carrier);

        if ($shippingMethods) {
            foreach ($shippingMethods as $shippingMethod) {                
                // If there is no shipping fee defined for this shipping
                // method we don't want to show it.
                if ($shippingMethod->getShippingFee() !== false) {
                    $allowedShippingMethods[] = $shippingMethod;
                }
            }
        }
        
        $allowedShippingMethods = new DataObjectSet($allowedShippingMethods);
        
        $extendableShippingMethod->extend('updateAllowedShippingMethods', $allowedShippingMethods);
        
        return $allowedShippingMethods;
    }
    
    /**
     * Returns allowed shipping methods. Those are active
     * 
     * @param SilvercartCarrier $carrier Carrier to get shipping methods for
     *
     * @return DataObjectSet
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
     * @return DataObjectSet
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2012
     */
    public static function getAllowedShippingMethodsBase($carrier = null) {
        $extendedFilter = "";
        if (!is_null($carrier)) {
            $extendedFilter = sprintf(
                    " AND `SilvercartShippingMethod`.`SilvercartCarrierID` = '%s'",
                    $carrier->ID
            );
        }
        
        $customerGroups = SilvercartCustomer::getCustomerGroups();
        if ($customerGroups) {
            $customerGroupIDs   = implode(',', $customerGroups->map('ID', 'ID'));
            $filter = sprintf(
                "`SilvercartShippingMethod`.`isActive` = 1 AND (`SilvercartShippingMethod_SilvercartCustomerGroups`.`GroupID` IN (%s) OR `SilvercartShippingMethod`.`ID` NOT IN (%s))%s",
                $customerGroupIDs,
                "SELECT `SilvercartShippingMethod_SilvercartCustomerGroups`.`SilvercartShippingMethodID` FROM `SilvercartShippingMethod_SilvercartCustomerGroups`",
                $extendedFilter
            );
            $join   = "LEFT JOIN `SilvercartShippingMethod_SilvercartCustomerGroups` ON (`SilvercartShippingMethod_SilvercartCustomerGroups`.`SilvercartShippingMethodID` = `SilvercartShippingMethod`.`ID`)";
        } else {
            $filter = sprintf(
                "`SilvercartShippingMethod`.`isActive` = 1 AND (`SilvercartShippingMethod`.`ID` NOT IN (%s))%s",
                "SELECT `SilvercartShippingMethod_SilvercartCustomerGroups`.`SilvercartShippingMethodID` FROM `SilvercartShippingMethod_SilvercartCustomerGroups`",
                $extendedFilter
            );
            $join   = "";
        }
        
        $shippingMethods        = DataObject::get(
                'SilvercartShippingMethod',
                $filter,
                "",
                $join
        );
        return $shippingMethods;
    }
    
    /**
     * Filters the given shipping methods by default permission criteria
     * 
     * @param SilvercartShippingMethod $shippingMethods Shipping methods to filter
     *
     * @return DataObjectSet
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.04.2012
     */
    public static function filterShippingMethods($shippingMethods) {
        $allowedShippingMethods = new DataObjectSet();
        $customerGroups         = SilvercartCustomer::getCustomerGroups();
        foreach ($shippingMethods as $shippingMethod) {
            foreach ($customerGroups as $customerGroup) {
                if ($shippingMethod->SilvercartCustomerGroups()->find('ID', $customerGroup->ID)) {
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

}

/**
 * Collection controller for shipping methods
 * 
 * @package Silvercart
 * @subpackage Base
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 17.01.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartShippingMethod_CollectionController extends ModelAdmin_CollectionController {
    
    
    /**
     * Extends the CSV field list of the results table
     *
     * @param array $searchCriteria passed through from ResultsForm 
     * 
     * @return TableListField 
     */
    public function getResultsTable($searchCriteria) {
        $tf = parent::getResultsTable($searchCriteria);
        $tf->setFieldListCsv(
                array(
                    'ID'                    => 'ID',
                    'Title'                 => 'Title',
                    'isActive'              => 'isActive',
                    'SilvercartCarrierID'   => 'SilvercartCarrierID',
                    'AttributedZoneIDs'     => 'AttributedZoneIDs',
                )
        );
        return $tf;
    }
    
    /**
     * Generate a CSV import form for a single {@link DataObject} subclass.
     *
     * @return Form
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.09.2011
     */
    public function ImportForm() {
        $form = parent::ImportForm();
        if ($form instanceof Form) {
            $optionsetField = new OptionsetField(
                    'ObjectClass',
                    _t('SilvercartShippingMethod.CHOOSE_DATAOBJECT_TO_IMPORT', 'Choose DataObject to import'),
                    array(
                        'SilvercartShippingMethod'  => _t('SilvercartShippingMethod.PLURALNAME'),
                        'SilvercartShippingFee'     => _t('SilvercartShippingFee.PLURALNAME'),
                    ),
                    ''
            );
            $form->Fields()->push($optionsetField);
        }
        return $form;
    }
    
}
