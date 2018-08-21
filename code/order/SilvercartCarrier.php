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
 * abstract for a shipping carrier
 *
 * @package Silvercart
 * @subpackage Order
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 06.11.2010
 * @license see license file in modules root directory
 */
class SilvercartCarrier extends DataObject {

    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'priority' => 'Int'
    );
    
    /**
     * Has-one relations.
     *
     * @var array
     */
    private static $has_one = array(
        'Logo' => 'Image',
    );

    /**
     * Has-many relationship.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartShippingMethods'   => 'SilvercartShippingMethod',
        'SilvercartCarrierLanguages'  => 'SilvercartCarrierLanguage',
    );

    /**
     * Many to many relations
     * 
     * @var array
     */
    public static $belongs_many_many = array(
        'SilvercartZones'   => 'SilvercartZone',
    );

    /**
     * Virtual database fields.
     *
     * @var array
     */
    public static $casting = array(
        'AttributedZones'           => 'Varchar(255)',
        'AttributedShippingMethods' => 'Varchar(255)',
        'Title'                     => 'VarChar(25)',
        'FullTitle'                 => 'VarChar(60)',
    );

    /**
     * Default sort field and direction
     *
     * @var string
     */
    public static $default_sort = "priority DESC";
    
    /**
     * retirieves title from related language class depending on the set locale
     *
     * @return string 
     */
    public function getTitle() {
        return $this->getLanguageFieldValue('Title');
    }
    
    /**
     * retirieves title from related language class depending on the set locale
     *
     * @return string 
     */
    public function getFullTitle() {
        return $this->getLanguageFieldValue('FullTitle');
    }
    
    /**
     * Defines the form fields for the search in ModelAdmin
     * 
     * @return array seach fields definition
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2012
     */
    public function searchableFields() {
        $searchableFields = array(
            'SilvercartCarrierLanguages.Title' => array(
                'title' => $this->fieldLabel('Title'),
                'filter' => 'PartialMatchFilter'
            ),
            'SilvercartZones.ID' => array(
                'title' => $this->fieldLabel('SilvercartZones'),
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
     * CMS fields
     *
     * @param array $params Params to use
     *
     * @return FieldList
     */
    public function getCMSFields($params = null) {
        $fields = SilvercartDataObject::getCMSFields($this);
        return $fields;
    }

    /**
     * Returns the objects field labels
     * 
     * @param bool $includerelations configuration setting
     * 
     * @return array the translated field labels 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'AttributedZones'            => _t('SilvercartCountry.ATTRIBUTED_ZONES'),
                    'AttributedShippingMethods'  => _t('SilvercartCarrier.ATTRIBUTED_SHIPPINGMETHODS'),
                    'SilvercartShippingMethods'  => _t('SilvercartShippingMethod.PLURALNAME', 'zones'),
                    'SilvercartZones'            => _t('SilvercartZone.PLURALNAME', 'zones'),
                    'SilvercartCarrierLanguages' => _t('SilvercartConfig.TRANSLATIONS'),
                    'Title'                      => _t('SilvercartPage.TITLE'),
                    'priority'                   => _t('Silvercart.PRIORITY'),
                )
        );
    }
    
    /**
     * Sets the summary fields.
     *
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.02.2013
     */
    public function summaryFields() {
        $summaryFields = array(
            'Title'                     => $this->fieldLabel('Title'),
            'AttributedZones'           => $this->fieldLabel('AttributedZones'),
            'AttributedShippingMethods' => $this->fieldLabel('AttributedShippingMethods'),
            'priority'                  => $this->fieldLabel('priority'),
        );
        
        $this->extend('updateSummaryFields', $summaryFields);
        
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
     * Returns all allowed shipping methods for the this carrier
     *
     * @return SilvercartShippingMethod 
     */
    public function getAllowedSilvercartShippingMethods() {
        return SilvercartShippingMethod::getAllowedShippingMethodsForOverview($this);
    }
    
}

/**
 * Translations for SilvercartCarrier
 *
 * @package Silvercart
 * @subpackage Translation
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 23.01.2012
 * @license see license file in modules root directory
 */
class SilvercartCarrierLanguage extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'Title'     => 'VarChar(25)',
        'FullTitle' => 'VarChar(60)'
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartCarrier' => 'SilvercartCarrier'
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
     * @since 22.01.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),             array(
                    'Title'             => _t('SilvercartProduct.COLUMN_TITLE'),
                    'FullTitle'         => _t('SilvercartCarrier.FULL_NAME'),
                    'SilvercartCarrier' => _t('SilvercartCarrier.SINGULARNAME')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
}
