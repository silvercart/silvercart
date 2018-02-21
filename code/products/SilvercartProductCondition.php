<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Products
 */

/**
 * Definition for the condition of a product.
 *
 * @package Silvercart
 * @subpackage Products
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 09.08.2011
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartProductCondition extends DataObject {

    /**
     * attributes
     *
     * @var array
     */
    private static $db = array(
        'Code'             => 'VarChar',
        'SeoMicrodataCode' => "Enum(',NewCondition,UsedCondition,DamagedCondition,RefurbishedCondition','')",
    );
    
    /**
     * n:m relations
     *
     * @var array
     */
    private static $has_many = array(
        'SilvercartProducts'                  => 'SilvercartProduct',
        'SilvercartProductConditionLanguages' => 'SilvercartProductConditionLanguage',
    );
    
    /**
     * cast attribute class types to other SS types
     *
     * @var array
     */
    private static $casting = array(
        'Title'         => 'VarChar(255)',
        'MicrodataCode' => 'Text',
    );
    
     /**
     * List of default microdata codes.
     *
     * @var array
     */
    private static $default_microdata_codes = array(
        'new'         => 'NewCondition',
        'used'        => 'UsedCondition',
        'damaged'     => 'DamagedCondition',
        'refurbished' => 'RefurbishedCondition',
    );

    /**
     * Returns the translated singular name of the object.
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this);
    }
    
    /**
     * retirieves title from related language class depending on the set locale
     *
     * @return string 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 12.01.2012
     */
    public function getTitle() {
        return $this->getLanguageFieldValue('Title');
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
            'SilvercartProducts'
        );
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }

    /**
     * define the CMS fields
     *
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this, 'Code', false);
        
        $enumValues = $this->dbObject('SeoMicrodataCode')->enumValues();
        $i18nSource = array();
        foreach ($enumValues as $value => $label) {
            if (empty($label)) {
                $i18nSource[$value] = '';
            } else {
                $i18nSource[$value] = $this->fieldLabel('SeoMicrodataCode' . $label);
            }
        }
        $fields->dataFieldByName('SeoMicrodataCode')->setSource($i18nSource);
        $fields->dataFieldByName('SeoMicrodataCode')->setDescription($this->fieldLabel('SeoMicrodataCodeDescription'));
        
        return $fields;
    }
    
    /**
     * Returns a string with HTML Code for a selector box that lets the user
     * choose a product condition.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.08.2011
     */
    public static function getDropdownFieldOptionSet() {
        $productConditionMap    = array();
        $productConditions      = SilvercartProductCondition::get();
        
        if ($productConditions->exists()) {
            $productConditionMap = $productConditions->map('ID', 'Title')->toArray();
        }
        
        return $productConditionMap;
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.08.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Title'                                 => _t('SilvercartProductCondition.TITLE'),
                'SilvercartProducts'                    => _t('SilvercartProduct.PLURALNAME'),
                'SilvercartProductConditionLanguages'   => _t('SilvercartProductConditionLanguage.PLURALNAME'),
                'DefaultDamaged'                        => _t('SilvercartProductCondition.DefaultDamaged', 'Damaged'),
                'DefaultNew'                            => _t('SilvercartProductCondition.DefaultNew', 'New'),
                'DefaultRefurbished'                    => _t('SilvercartProductCondition.DefaultRefurbished', 'Refurbished'),
                'DefaultUsed'                           => _t('SilvercartProductCondition.DefaultUsed', 'Used'),
                'SeoMicrodataCode'                      => _t('SilvercartProductCondition.SeoMicrodataCode', 'SEO microdata code'),
                'SeoMicrodataCodeDescription'           => _t('SilvercartProductCondition.SeoMicrodataCodeDescription', 'Set up one of these values to increase the SEO visibility.'),
                'SeoMicrodataCodeDamagedCondition'      => _t('SilvercartProductCondition.SeoMicrodataCodeDamaged', 'Damaged'),
                'SeoMicrodataCodeNewCondition'          => _t('SilvercartProductCondition.SeoMicrodataCodeNew', 'New'),
                'SeoMicrodataCodeRefurbishedCondition'  => _t('SilvercartProductCondition.SeoMicrodataCodeRefurbished', 'Refurbished'),
                'SeoMicrodataCodeUsedCondition'         => _t('SilvercartProductCondition.SeoMicrodataCodeUsed', 'Used'),
            )
        );
        
        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Defines the form fields for the search in ModelAdmin
     * 
     * @return array seach fields definition
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012
     */
    public function searchableFields() {
        $searchableFields = array(
            'SilvercartProductConditionLanguages.Title' => array(
                'title' => $this->fieldLabel('Title'),
                'filter' => 'PartialMatchFilter'
            ),
        );
        $this->extend('updateSearchableFields', $searchableFields);
        return $searchableFields;
    }
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.03.2011
     */
    public function summaryFields() {
        $summaryFields = array(
            'Title'             => $this->fieldLabel('Title'),
        );
        
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Default records for product conditions.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2018
     */
    public function requireDefaultRecords() {
        foreach (self::$default_microdata_codes as $code => $microdataCode) {
            $condition = SilvercartProductCondition::get()->filter('Code', $code)->first();
            if (is_null($condition) ||
                !$condition->exists()) {
                $condition = new SilvercartProductCondition();
                $condition->Code             = $code;
                $condition->SeoMicrodataCode = $microdataCode;
                $condition->Title            = $this->fieldLabel('Default' . ucfirst($code));
                $condition->write();
            }
        }
    }
    
    /**
     * Returns the title for SEO microdata
     *
     * @return string
     */
    public function getMicrodataCode() {
        $microDataCode = $this->SeoMicrodataCode;
        if (empty($microDataCode) &&
            array_key_exists($this->Title, self::$default_microdata_codes)) {
            $microDataCode = self::$default_microdata_codes[$this->Code];
        }
        if (!empty($microDataCode)) {
            $microDataCode = 'http://schema.org/' . $microDataCode;
        }
        return $microDataCode;
    }
}

/**
 * Translations for SilvercartProductCondition
 *
 * @package Silvercart
 * @subpackage Translation
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 12.01.2012
 * @license see license file in modules root directory
 */
class SilvercartProductConditionLanguage extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'Title' => 'VarChar(255)'
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartProductCondition' => 'SilvercartProductCondition'
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
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 12.01.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'Title' => $this->fieldLabel('Title')
        );


        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 12.01.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'Title' => _t('SilvercartProduct.COLUMN_TITLE')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
}