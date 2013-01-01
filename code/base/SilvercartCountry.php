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
 * Abstract for a country
 *
 * @package Silvercart
 * @subpackage Base
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
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
        'freeOfShippingCostsFrom'   => 'Money',
        'IsPrioritive'              => 'Boolean(0)',
        'DisplayPosition'           => 'Int',
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.06.2012
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Title'                         => _t('SilvercartCountry.SINGULARNAME'),
                'ISO2'                          => _t('SilvercartCountry.ISO2', 'ISO Alpha2'),
                'ISO3'                          => _t('SilvercartCountry.ISO3', 'ISO Alpha3'),
                'ISON'                          => _t('SilvercartCountry.ISON', 'ISO numeric'),
                'FIPS'                          => _t('SilvercartCountry.FIPS', 'FIPS code'),
                'Continent'                     => _t('SilvercartCountry.CONTINENT', 'Continent'),
                'Currency'                      => _t('SilvercartCountry.CURRENCY', 'Currency'),
                'Active'                        => _t('SilvercartCountry.ACTIVE', 'Active'),
                'AttributedZones'               => _t('SilvercartCountry.ATTRIBUTED_ZONES', 'attributed zones'),
                'AttributedPaymentMethods'      => _t('SilvercartCountry.ATTRIBUTED_PAYMENTMETHOD', 'attributed payment method'),
                'ActivityText'                  => _t('SilvercartCountry.ACTIVE', 'Active'),
                'SilvercartCountryLanguages'    => _t('SilvercartCountryLanguage.PLURALNAME'),
                'SilvercartZones'               => _t('SilvercartZone.PLURALNAME'),
                'SilvercartPaymentMethods'      => _t('SilvercartPaymentMethod.PLURALNAME'),
                'SilvercartZones'               => _t('SilvercartZone.PLURALNAME'),
                'freeOfShippingCostsFrom'       => _t('SilvercartCountry.FREEOFSHIPPINGCOSTSFROM'),
                'IsPrioritive'                  => _t('SilvercartCountry.ISPRIORITIVE'),
                'IsPrioritiveShort'             => _t('SilvercartCountry.ISPRIORITIVE_SHORT'),
                'DisplayPosition'               => _t('SilvercartCountry.DISPLAYPOSITION'),
            )
        );
    }

    /**
     * Searchable fields of SIlvercartCountry.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.06.2012
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
            'SilvercartZones.ID' => array(
                'title'     => $this->fieldLabel('SilvercartZones'),
                'filter'    => 'PartialMatchFilter',
            ),
            'SilvercartPaymentMethods.ID' => array(
                'title'     => $this->fieldLabel('SilvercartPaymentMethods'),
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
                )
        );
        
        $this->extend('updateSummary', $summaryFields);
        return $summaryFields;
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields(
                array(
                    'fieldClasses' => array(
                        'freeOfShippingCostsFrom'   => 'SilvercartMoneyField',
                    ),
                )
        );
        $fields->removeByName('SilvercartPaymentMethods');
        $fields->removeByName('SilvercartZones');
        $fields->removeByName('Locale');//Field comes from Translatable

        $paymentMethodsTable = new ManyManyComplexTableField(
            $this,
            'SilvercartPaymentMethods',
            'SilvercartPaymentMethod',
            null,
            'getCmsFields_forPopup'
        );
        $paymentMethodsTable->setAddTitle(_t('SilvercartPaymentMethod.TITLE', 'payment method'));
        $tabParam = "Root." . _t('SilvercartPaymentMethod.TITLE');
        $fields->addFieldToTab($tabParam, $paymentMethodsTable);
        
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguageClassName());
        foreach ($languageFields as $languageField) {
            $fields->insertBefore($languageField, 'ISO2');
        }
        
        $displayPositionMap = array(
            '0' => _t('SilvercartOrderSearchForm.PLEASECHOOSE'),
        );
        for ($x = 1; $x <= self::getPrioritiveCountryCount(false); $x++) {
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
     * @return DataObjectSet
     */
    public static function get_active() {
        $activeCountries = DataObject::get('SilvercartCountry', '"Active" = 1');
        if (!$activeCountries) {
            $activeCountries = new ArrayList();
        }
        return $activeCountries;
    }
    
    /**
     * Returns a list of prioritive countries
     * 
     * @param bool $onlyActive Search only for active coutries?
     * 
     * @return DataObjectSet
     */
    public static function getPrioritiveCountries($onlyActive = true) {
        $key            = 0;
        $addToFilter    = '';
        if ($onlyActive) {
            $key            = 1;
            $addToFilter    = ' AND Active = 1';
        }
        if (!array_key_exists($key, self::$prioritiveCountries)) {
            $prioritiveCountries = DataObject::get(
                    'SilvercartCountry',
                    'IsPrioritive = 1' . $addToFilter,
                    'DisplayPosition ASC, SilvercartCountryLanguage.Title ASC'
            );
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
            if ($prioritiveCountries instanceof DataObjectSet) {
                $prioritiveCountryCount = $prioritiveCountries->Count() + 1;
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
     * @return DataObjectSet
     */
    public static function getNonPrioritiveCountries($onlyActive = true) {
        $key            = 0;
        $addToFilter    = '';
        if ($onlyActive) {
            $key            = 1;
            $addToFilter    = ' AND Active = 1';
        }
        if (!array_key_exists($key, self::$nonPrioritiveCountries)) {
            $nonPrioritiveCountries = DataObject::get(
                    'SilvercartCountry',
                    'IsPrioritive = 0' . $addToFilter,
                    'SilvercartCountryLanguage.Title ASC'
            );
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
            if ($nonPrioritiveCountries instanceof DataObjectSet) {
                $nonPrioritiveCountryCount = $nonPrioritiveCountries->Count() + 1;
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
                foreach ($prioritiveCountries->map() as $id => $title) {
                    $dropdownMap[$id] = $title;
                }
            }
            if (self::getNonPrioritiveCountryCount() > 0) {
                if ((is_null($emptyString) && count($dropdownMap) > 0) ||
                    (!is_null($emptyString) && count($dropdownMap) > 1)) {
                    $dropdownMap[' '] = '------------------------';
                }
                $nonPrioritiveCountries = self::getNonPrioritiveCountries($onlyActive);
                foreach ($nonPrioritiveCountries->map() as $id => $title) {
                    $dropdownMap[$id] = $title;
                }
            }
            self::$prioritiveDropdownMap[$key] = $dropdownMap;
        }
        return self::$prioritiveDropdownMap[$key];
    }
}
