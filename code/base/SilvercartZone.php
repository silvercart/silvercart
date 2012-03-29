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
 * abstract for a shipping zone; makes it easier to calculate shipping rates
 * Every carrier might have it´s own zones. That´s why zones:countries is n:m
 *
 * @package Silvercart
 * @subpackage Base
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 23.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartZone extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'Title' => 'VarChar'
    );
    /**
     * Has-many relationship.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartShippingFees' => 'SilvercartShippingFee'
    );
    /**
     * Many-many relationships.
     *
     * @var array
     */
    public static $many_many = array(
        'SilvercartCountries'   => 'SilvercartCountry',
        'SilvercartCarriers'    => 'SilvercartCarrier',
    );
    /**
     * Belongs-many-many relationships.
     *
     * @var array
     */
    public static $belongs_many_many = array(
        'SilvercartShippingMethods' => 'SilvercartShippingMethod'
    );
    /**
     * Virtual database columns.
     *
     * @var array
     */
    public static $casting = array(
        'AttributedCountries'           => 'Varchar(255)',
        'AttributedShippingMethods'     => 'Varchar(255)',
        'SilvercartCarriersAsString'    => 'Text',
    );
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 5.7.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                        'Title'                     => _t('SilvercartPage.TITLE', 'title'),
                        'SilvercartCarriers'        => _t('SilvercartCarrier.PLURALNAME'),
                        'AttributedCountries'       => _t('SilvercartZone.ATTRIBUTED_COUNTRIES', 'attributed countries'),
                        'AttributedShippingMethods' => _t('SilvercartZone.ATTRIBUTED_SHIPPINGMETHODS', 'attributed shipping methods'),
                        'SilvercartShippingFees'    => _t('SilvercartShippingFee.PLURALNAME'),
                        'SilvercartShippingMethods' => _t('SilvercartShippingMethod.PLURALNAME'),
                        'SilvercartCountries'       => _t('SilvercartCountry.PLURALNAME'),
                    )
                );
        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldSet the fields for the backend
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2011
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $countriesTable = new ManyManyComplexTableField(
                        $this,
                        'SilvercartCountries',
                        'SilvercartCountry',
                        null,
                        'getCMSFields_forPopup',
                        null,
                        'Title'
        );
        $countriesTable->pageSize = 50;
        $fields->addFieldToTab('Root.SilvercartCountries', $countriesTable);
        
        $carriersTable = new ManyManyComplexTableField(
                        $this,
                        'SilvercartCarriers',
                        'SilvercartCarrier',
                        null,
                        'getCMSFields_forPopup',
                        null,
                        'Title'
        );
        $fields->addFieldToTab('Root.SilvercartCarriers', $carriersTable);
        
        $shippingTable = new SilvercartManyManyComplexTableField(
                        $this,
                        'SilvercartShippingMethods',
                        'SilvercartShippingMethod',
                        null,
                        'getCMSFields_forPopup',
                        null,
                        'Title'
        );
        $fields->addFieldToTab('Root.SilvercartShippingMethods', $shippingTable);
        
        return $fields;
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
        if (_t('SilvercartZone.SINGULARNAME')) {
            return _t('SilvercartZone.SINGULARNAME');
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
        if (_t('SilvercartZone.PLURALNAME')) {
            return _t('SilvercartZone.PLURALNAME');
        } else {
            return parent::plural_name();
        }   
    }
    
    /**
     * Searchable fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 5.7.2011
     */
    public function searchableFields() {
        $searchableFields = array(
            'Title' => array(
                'title' => $this->fieldLabel('Title'),
                'filter' => 'PartialMatchFilter'
            ),
            'SilvercartCarriers.ID' => array(
                'title' => $this->fieldLabel('SilvercartCarriers'),
                'filter' => 'ExactMatchFilter'
            ),
            'SilvercartCountries.ID' => array(
                'title' => $this->fieldLabel('SilvercartCountries'),
                'filter' => 'ExactMatchFilter'
            ),
            'SilvercartShippingMethods.ID' => array(
                'title' => $this->fieldLabel('SilvercartShippingMethods'),
                'filter' => 'ExactMatchFilter'
            )
        );
        $this->extend('updateSearchableFields', $searchableFields);
        return $searchableFields;
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2011
     */
    public function summaryFields() {
        $summaryFields = array(
            'Title'                         => $this->fieldLabel('Title'),
            'SilvercartCarriersAsString'    => $this->fieldLabel('SilvercartCarriers'),
            'AttributedCountries'           => $this->fieldLabel('AttributedCountries'),
            'AttributedShippingMethods'     => $this->fieldLabel('AttributedShippingMethods'),
        );
        
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }

    /**
     * Returns the attributed countries as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function AttributedCountries() {
        $attributedCountriesStr = '';
        $attributedCountries = array();
        $maxLength = 150;

        foreach ($this->SilvercartCountries() as $silvercartCountry) {
            $attributedCountries[] = $silvercartCountry->Title;
        }

        if (!empty($attributedCountries)) {
            $attributedCountriesStr = implode(', ', $attributedCountries);

            if (strlen($attributedCountriesStr) > $maxLength) {
                $attributedCountriesStr = substr($attributedCountriesStr, 0, $maxLength) . '...';
            }
        }

        return $attributedCountriesStr;
    }

    /**
     * Returns the attributed shipping methods as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function AttributedShippingMethods() {
        $attributedShippingMethodsStr = '';
        $attributedShippingMethods = array();
        $maxLength = 150;

        foreach ($this->SilvercartShippingMethods() as $silvercartShippingMethod) {
            $attributedShippingMethods[] = $silvercartShippingMethod->Title;
        }

        if (!empty($attributedShippingMethods)) {
            $attributedShippingMethodsStr = implode(', ', $attributedShippingMethods);

            if (strlen($attributedShippingMethodsStr) > $maxLength) {
                $attributedShippingMethodsStr = substr($attributedShippingMethodsStr, 0, $maxLength) . '...';
            }
        }

        return $attributedShippingMethodsStr;
    }
    
    /**
     * Returns the carriers as a comma separated string
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2012 
     */
    public function getSilvercartCarriersAsString() {
        $silvercartCarriersAsString    = '---';
        $silvercartCarriersAsArray     = $this->SilvercartCarriers()->map();
        if (count($silvercartCarriersAsArray) > 0) {
            $silvercartCarriersAsString = implode(',', $silvercartCarriersAsArray);
        }
        return $silvercartCarriersAsString;
    }
    
    /**
     * Returns all zones for the given country ID
     *
     * @param int $countryID ID of the country to get zones for
     * 
     * @return ComponentSet
     */
    public static function getZonesFor($countryID) {
        return DataObject::get(
                'SilvercartZone',
                sprintf(
                        "`SilvercartZone_SilvercartCountries`.`SilvercartCountryID` = '%s'",
                        $countryID
                ),
                '',
                "LEFT JOIN `SilvercartZone_SilvercartCountries` ON (`SilvercartZone_SilvercartCountries`.`SilvercartZoneID` = `SilvercartZone`.`ID`)"
        );
    }

}
