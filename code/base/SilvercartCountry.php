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
 * Abstract for a country
 *
 * @package Silvercart
 * @subpackage Base
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 * @since 20.10.2010
 */
class SilvercartCountry extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'ISO2'                      => 'VarChar',
        'ISO3'                      => 'VarChar',
        'ISON'                      => 'Int',
        'FIPS'                      => 'VarChar',
        'Continent'                 => 'VarChar',
        'Currency'                  => 'VarChar',
        'Active'                    => 'Boolean',
        'freeOfShippingCostsFrom'   => 'SilvercartMoney',
        'IsPrioritive'              => 'Boolean(0)',
        'DisplayPosition'           => 'Int',
        'IsNonTaxable'              => 'Boolean(0)',
    );
    /**
     * Default values
     *
     * @var array
     */
    public static $defaults = array(
        'Active' => false,
    );
    /**
     * Has-many relationship.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartCountryLanguages' => 'SilvercartCountryLanguage'
    );
    /**
     * Many-many relationships.
     *
     * @var array
     */
    public static $many_many = array(
        'SilvercartPaymentMethods' => 'SilvercartPaymentMethod'
    );
    /**
     * Belongs-many-many relationships.
     *
     * @var array
     */
    public static $belongs_many_many = array(
        'SilvercartZones' => 'SilvercartZone'
    );
    /**
     * Virtual database columns.
     *
     * @var array
     */
    public static $casting = array(
        'AttributedZones'           => 'Varchar(255)',
        'AttributedPaymentMethods'  => 'Varchar(255)',
        'ActivityText'              => 'VarChar',
        'Title'                     => 'Text',
        'IsPrioritiveText'          => 'VarChar',
    );
    
     /**
     * Default sort order and direction
     *
     * @var string
     */
    public static $default_sort = "SilvercartCountry.Active DESC, SilvercartCountry.IsPrioritive DESC, SilvercartCountryLanguage.Title ASC";
    
    /**
     * list of prioritive countries
     *
     * @var array 
     */
    protected static $prioritiveCountries = array();
    
    /**
     * count of prioritive countries
     *
     * @var array
     */
    protected static $prioritiveCountryCount = array();
    
    /**
     * list of non prioritive countries
     *
     * @var array 
     */
    protected static $nonPrioritiveCountries = array();
    
    /**
     * count of non prioritive countries
     *
     * @var array
     */
    protected static $nonPrioritiveCountryCount = array();
    
    /**
     * dropdown map sorted by prioritive countries
     *
     * @var array
     */
    protected static $prioritiveDropdownMap = array();
    
    /**
     * A DataList of all active countries or en empty ArrayList
     * 
     * @var SS_List
     */
    protected static $activeCountries = null;

        /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.06.2012
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
     * @since 18.06.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this);
    }

    /**
     * i18n for field labels
     *
     * @param bool $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.07.2013
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Title'                             => _t('SilvercartCountry.SINGULARNAME'),
                'ISO2'                              => _t('SilvercartCountry.ISO2', 'ISO Alpha2'),
                'ISO3'                              => _t('SilvercartCountry.ISO3', 'ISO Alpha3'),
                'ISON'                              => _t('SilvercartCountry.ISON', 'ISO numeric'),
                'FIPS'                              => _t('SilvercartCountry.FIPS', 'FIPS code'),
                'Continent'                         => _t('SilvercartCountry.CONTINENT', 'Continent'),
                'Currency'                          => _t('SilvercartCountry.CURRENCY', 'Currency'),
                'Active'                            => _t('SilvercartCountry.ACTIVE', 'Active'),
                'AttributedZones'                   => _t('SilvercartCountry.ATTRIBUTED_ZONES', 'attributed zones'),
                'AttributedPaymentMethods'          => _t('SilvercartCountry.ATTRIBUTED_PAYMENTMETHOD', 'attributed payment method'),
                'ActivityText'                      => _t('SilvercartCountry.ACTIVE', 'Active'),
                'SilvercartCountryLanguages'        => _t('SilvercartCountryLanguage.PLURALNAME'),
                'SilvercartZones'                   => _t('SilvercartZone.PLURALNAME'),
                'SilvercartPaymentMethods'          => _t('SilvercartPaymentMethod.PLURALNAME'),
                'SilvercartZones'                   => _t('SilvercartZone.PLURALNAME'),
                'freeOfShippingCostsFrom'           => _t('SilvercartCountry.FREEOFSHIPPINGCOSTSFROM'),
                'IsPrioritive'                      => _t('SilvercartCountry.ISPRIORITIVE'),
                'IsPrioritiveShort'                 => _t('SilvercartCountry.ISPRIORITIVE_SHORT'),
                'DisplayPosition'                   => _t('SilvercartCountry.DISPLAYPOSITION'),
                'IsNonTaxable'                  => _t('SilvercartCountry.IsNonTaxable'),
                'SilvercartCountryLanguages.Title'  => _t('SilvercartCountry.TITLE'),
                'SilvercartCountryLanguage.Title'  => _t('SilvercartCountry.TITLE'),
            )
        );
    }

    /**
     * Searchable fields of SIlvercartCountry.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.04.2014
     */
    public function  searchableFields() {
        return array(
            'SilvercartCountryLanguages.Title' => array(
                'title'     => $this->singular_name(),
                'filter'    => 'PartialMatchFilter',
            ),
            'ISO2' => array(
                'title'     => $this->fieldLabel('ISO2'),
                'filter'    => 'PartialMatchFilter',
            ),
            'ISO3' => array(
                'title'     => $this->fieldLabel('ISO3'),
                'filter'    => 'PartialMatchFilter',
            ),
            'ISON' => array(
                'title'     => $this->fieldLabel('ISON'),
                'filter'    => 'PartialMatchFilter',
            ),
            'FIPS' => array(
                'title'     => $this->fieldLabel('FIPS'),
                'filter'    => 'PartialMatchFilter',
            ),
            'Continent' => array(
                'title'     => $this->fieldLabel('Continent'),
                'filter'    => 'PartialMatchFilter',
            ),
            'Currency' => array(
                'title'     => $this->fieldLabel('Currency'),
                'filter'    => 'PartialMatchFilter',
            ),
            'SilvercartPaymentMethods.ID' => array(
                'title'     => $this->fieldLabel('SilvercartPaymentMethods'),
                'filter'    => 'PartialMatchFilter',
            ),
            'SilvercartZones.ID' => array(
                'title'     => $this->fieldLabel('SilvercartZones'),
                'filter'    => 'PartialMatchFilter',
            ),
            'Active' => array(
                'title'     => $this->fieldLabel('Active'),
                'filter'    => 'ExactMatchFilter',
            ),
            'IsPrioritive' => array(
                'title'     => $this->fieldLabel('IsPrioritiveShort'),
                'filter'    => 'ExactMatchFilter',
            ),
            'IsNonTaxable' => array(
                'title'     => $this->fieldLabel('IsNonTaxable'),
                'filter'    => 'ExactMatchFilter',
            ),
        );
    }
    
    /**
     * Returns freeOfShippingCostsFrom in a nice format
     *
     * @return string
     */
    public function getFreeOfShippingCostsFromNice() {
        return $this->freeOfShippingCostsFrom->Nice();
    }

        /**
     * Summary fields
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012 
     */
    public function summaryFields() {
        $summaryFields = array_merge(
                parent::summaryFields(),
                array(
                    'Title'                             => $this->fieldLabel('Title'),
                    'ISO2'                              => $this->fieldLabel('ISO2'),
                    'ISO3'                              => $this->fieldLabel('ISO3'),
                    'Continent'                         => $this->fieldLabel('Continent'),
                    'Currency'                          => $this->fieldLabel('Currency'),
                    'AttributedZones'                   => $this->fieldLabel('AttributedZones'),
                    'AttributedPaymentMethods'          => $this->fieldLabel('AttributedPaymentMethods'),
                    'ActivityText'                      => $this->fieldLabel('ActivityText'),
                    'getFreeOfShippingCostsFromNice'    => $this->fieldLabel('freeOfShippingCostsFrom'),
                    'IsPrioritiveText'                  => $this->fieldLabel('IsPrioritiveShort'),
                    'IsNonTaxable'                      => $this->fieldLabel('IsNonTaxable'),
                )
        );
        
        $this->extend('updateSummary', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Returns an array of field/relation names (db, has_one, has_many, 
     * many_many, belongs_many_many) to exclude from form scaffolding in
     * backend.
     * This is a performance friendly way to exclude fields.
     * 
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 10.02.2013
     */
    public function excludeFromScaffolding() {
        $excludeFromScaffolding = array(
            'SilvercartZones',
            'Locale'
        );
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this);
        
        $paymentMethodsTable = $fields->dataFieldByName('SilvercartPaymentMethods');
        if ($paymentMethodsTable instanceof FormField) {
            $paymentMethodsTable->setConfig(SilvercartGridFieldConfig_RelationEditor::create());
        }
        
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguageClassName());
        foreach ($languageFields as $languageField) {
            $fields->insertBefore($languageField, 'ISO2');
        }
        
        $displayPositionMap = array(
            '0' => _t('SilvercartOrderSearchForm.PLEASECHOOSE'),
        );
        for ($x = 1; $x <= self::getPrioritiveCountryCount(false) + 1; $x++) {
            $displayPositionMap[$x] = $x;
        }
        $displayPositionField = new DropdownField('DisplayPosition', $this->fieldLabel('DisplayPosition'), $displayPositionMap);
        $fields->insertAfter($displayPositionField, 'IsPrioritive');

        return $fields;
    }
    
    /**
     * Hook before writing th object
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.12.2012
     */
    public function onBeforeWrite() {
        parent::onBeforeWrite();
        if (!$this->IsPrioritive) {
            $this->DisplayPosition = 0;
        } elseif ($this->DisplayPosition == 0) {
            $this->DisplayPosition = 1000;
        }
    }

    /**
     * Returns the text label for a countries activity.
     *
     * @return string
     */
    public function getActivityText() {
        if ($this->Active) {
            return _t('Silvercart.YES');
        }
        return _t('Silvercart.NO');
    }

    /**
     * Returns the text label for a countries priority.
     *
     * @return string
     */
    public function getIsPrioritiveText() {
        if ($this->IsPrioritive) {
            return _t('Silvercart.YES');
        }
        return _t('Silvercart.NO');
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
        return SilvercartTools::AttributedDataObject($this->SilvercartZones());
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
     * Returns the title
     *
     * @return string $title i18n title
     */
    public function getTitle() {
        return $this->getLanguageFieldValue('Title');
    }
    
    /**
     * Returns all active countries
     * 
     * @return SS_List
     */
    public static function get_active() {
        if (!self::$activeCountries) {
            $activeCountries = SilvercartCountry::get()->filter("Active", 1);
            if (!$activeCountries->exists()) {
                $activeCountries = new ArrayList();
            }
            self::$activeCountries = $activeCountries;
        }
        return self::$activeCountries;
    }
    
    /**
     * Returns a list of prioritive countries
     * 
     * @param bool $onlyActive Search only for active coutries?
     * 
     * @return SS_List
     */
    public static function getPrioritiveCountries($onlyActive = true) {
        $key            = 0;
        $addToFilter    = array();
        if ($onlyActive) {
            $key            = 1;
            $addToFilter    = array("Active" => 1);
        }
        if (!array_key_exists($key, self::$prioritiveCountries)) {
            $filter = array_merge(array("IsPrioritive" => 1), $addToFilter);
            $prioritiveCountries = SilvercartCountry::get()
                                    ->filter($filter)
                                    ->sort(array("DisplayPosition" => "ASC", "Title" => "ASC"));
            self::$prioritiveCountries[$key] = $prioritiveCountries;
        }
        return self::$prioritiveCountries[$key];
    }
    
    /**
     * Returns the count of prioritive countries
     * 
     * @param bool $onlyActive Search only for active coutries?
     * 
     * @return int
     */
    public static function getPrioritiveCountryCount($onlyActive = true) {
        $key = 0;
        if ($onlyActive) {
            $key = 1;
        }
        if (!array_key_exists($key, self::$prioritiveCountryCount)) {
            $prioritiveCountryCount = 0;
            $prioritiveCountries    = self::getPrioritiveCountries($onlyActive);
            if ($prioritiveCountries instanceof SS_List) {
                $prioritiveCountryCount = (int) $prioritiveCountries->count();
            }
            self::$prioritiveCountryCount[$key] = $prioritiveCountryCount;
        }
        return self::$prioritiveCountryCount[$key];
    }
    
    /**
     * Returns a list of non prioritive countries
     * 
     * @param bool $onlyActive Search only for active coutries?
     * 
     * @return ArrayList
     */
    public static function getNonPrioritiveCountries($onlyActive = true) {
        $key            = 0;
        $addToFilter    = array();
        if ($onlyActive) {
            $key            = 1;
            $addToFilter    = array("Active" => 1);
        }
        if (!array_key_exists($key, self::$nonPrioritiveCountries)) {
            $filter = array_merge(array("IsPrioritive" => 0), $addToFilter);
            $nonPrioritiveCountries = SilvercartCountry::get()
                                        ->filter($filter)
                                        ->sort(array("Title" => "ASC"));
            self::$nonPrioritiveCountries[$key] = $nonPrioritiveCountries;
        }
        return self::$nonPrioritiveCountries[$key];
    }
    
    /**
     * Returns the count of non prioritive countries
     * 
     * @param bool $onlyActive Search only for active coutries?
     * 
     * @return int
     */
    public static function getNonPrioritiveCountryCount($onlyActive = true) {
        $key = 0;
        if ($onlyActive) {
            $key = 1;
        }
        if (!array_key_exists($key, self::$nonPrioritiveCountryCount)) {
            $nonPrioritiveCountryCount  = 0;
            $nonPrioritiveCountries     = self::getNonPrioritiveCountries($onlyActive);
            if ($nonPrioritiveCountries instanceof SS_List) {
                $nonPrioritiveCountryCount = (int) $nonPrioritiveCountries->count();
            }
            self::$nonPrioritiveCountryCount[$key] = $nonPrioritiveCountryCount;
        }
        return self::$nonPrioritiveCountryCount[$key];
    }
    
    /**
     * Returns a dropdown map sorted by prioritive countries
     * 
     * @param bool   $onlyActive  Search only for active coutries?
     * @param string $emptyString String to show for empty value
     * 
     * @return array
     */
    public static function getPrioritiveDropdownMap($onlyActive = true, $emptyString = null) {
        $key = 0;
        if ($onlyActive) {
            $key = 1;
        }
        if (!is_null($emptyString)) {
            $key .= md5($emptyString);
        }
        if (!array_key_exists($key, self::$prioritiveDropdownMap)) {
            $dropdownMap = array();
            if (!is_null($emptyString)) {
                $dropdownMap[''] = $emptyString;
            }
            if (self::getPrioritiveCountryCount() > 0) {
                $prioritiveCountries    = self::getPrioritiveCountries($onlyActive);
                foreach ($prioritiveCountries->map()->toArray() as $id => $title) {
                    $dropdownMap[$id] = $title;
                }
            }
            if (self::getNonPrioritiveCountryCount() > 0) {
                if ((is_null($emptyString) && count($dropdownMap) > 0) ||
                    (!is_null($emptyString) && count($dropdownMap) > 1)) {
                    $dropdownMap[' '] = '------------------------';
                }
                $nonPrioritiveCountries = self::getNonPrioritiveCountries($onlyActive);
                foreach ($nonPrioritiveCountries->map()->toArray() as $id => $title) {
                    $dropdownMap[$id] = $title;
                }
            }
            if (empty($dropdownMap) &&
                SilvercartTools::isBackendEnvironment()) {
                $allCountries = SilvercartCountry::get();
                $dropdownMap = $allCountries->map()->toArray();
            }
            self::$prioritiveDropdownMap[$key] = $dropdownMap;
        }
        return self::$prioritiveDropdownMap[$key];
    }
}

/**
 * Translations for SilvercartCountry
 *
 * @package Silvercart
 * @subpackage Translation
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 26.04.2012
 * @license see license file in modules root directory
 */
class SilvercartCountryLanguage extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'Title'     => 'VarChar(255)',
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartCountry' => 'SilvercartCountry'
    );
    
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
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),             array(
                    'Title'             => _t('SilvercartProduct.COLUMN_TITLE'),
                    'SilvercartCountry' => _t('SilvercartCountry.SINGULARNAME')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
}
