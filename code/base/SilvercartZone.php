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
 * abstract for a shipping zone; makes it easier to calculate shipping rates
 * Every carrier might have it´s own zones. That´s why zones:countries is n:m
 *
 * @package Silvercart
 * @subpackage Base
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 23.10.2010
 * @license see license file in modules root directory
 */
class SilvercartZone extends DataObject {
    
    /**
     * Has-many relationship.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartShippingFees'  => 'SilvercartShippingFee',
        'SilvercartZoneLanguages' => 'SilvercartZoneLanguage',
        'SilvercartHandlingCosts' => 'SilvercartHandlingCost',
    );
    /**
     * Many-many relationships.
     *
     * @var array
     */
    public static $many_many = array(
        'SilvercartCountries'       => 'SilvercartCountry',
        'SilvercartCarriers'        => 'SilvercartCarrier',
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
        'Title'                         => 'Text',
    );
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
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
                        'UseAllCountries'           => _t('SilvercartZone.USE_ALL_COUNTRIES'),
                        'SilvercartZoneLanguages'   => _t('SilvercartZoneLanguage.PLURALNAME'),
                        'SilvercartHandlingCosts'   => _t('SilvercartHandlingCost.PLURALNAME')
                )
        );
        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
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
        if ($this->isInDB()) {
            $useAllCountries = new CheckboxField('UseAllCountries', $this->fieldLabel('UseAllCountries'));
            $fields->addFieldToTab('Root.Main', $useAllCountries);
        }
        return $fields;
    }
    
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
     * retirieves title from related language class depending on the set locale
     *
     * @return string
     */
    public function getTitle() {
        return $this->getLanguageFieldValue('Title');
    }
    
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
            'SilvercartZoneLanguages.Title' => array(
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
     * Processing hook before writing the DataObject
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2012 
     */
    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        if (array_key_exists('UseAllCountries', $_POST)) {
            $countries = SilvercartCountry::get();
            foreach ($countries as $country) {
                $this->SilvercartCountries()->add($country);
            }
        }
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
     * Returns the attributed shipping methods as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function AttributedShippingMethods() {
        return SilvercartTools::AttributedDataObject($this->SilvercartShippingMethods());
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
        $silvercartCarriersAsArray     = $this->SilvercartCarriers()->map()->toArray();
        if (count($silvercartCarriersAsArray) > 0 && is_array($silvercartCarriersAsArray)) {
            $silvercartCarriersAsString = implode(',', $silvercartCarriersAsArray);
        }
        return $silvercartCarriersAsString;
    }
    
    /**
     * Returns all zones for the given country ID
     *
     * @param int $countryID ID of the country to get zones for
     * 
     * @return DataList
     */
    public static function getZonesFor($countryID) {
        return self::get()
            ->leftJoin(
                'SilvercartZone_SilvercartCountries',
                'SZSC.SilvercartZoneID = SilvercartZone.ID',
                'SZSC'
            )
            ->filter(
                array(
                    'SilvercartCountryID' => $countryID,
                )
            );
    }
    
    /**
     * Returns whether this zone is related to all active countries
     *
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.06.2012
     */
    public function hasAllCountries() {
        /* @var $countries ArrayList */
        $countries          = $this->SilvercartCountries();
        $availableCountries = SilvercartCountry::get()->filter("Active", 1);
        $hasAllCountries    = true;
        foreach ($availableCountries as $availableCountry) {
            if (!$countries->find('ID', $availableCountry->ID)) {
                $hasAllCountries = false;
                break;
            }
        }
        return $hasAllCountries;
    }

}

/**
 * Translations for a zone
 * 
 * @package Silvercart
 * @subpackage Translation
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 21.01.2012
 * @license see license file in modules root directory
 */
class SilvercartZoneLanguage extends DataObject {
   
    /**
     * DB attributes
     *
     * @var array
     */
    public static $db = array(
        'Title' => 'VarChar'
    );

    /**
     * has one relations
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartZone' => 'SilvercartZone'
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.01.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'SilvercartZone' => _t('SilvercartZone.PLURALNAME'),
                    'Title'          => _t('SilvercartProduct.COLUMN_TITLE')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
}