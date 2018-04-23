<?php

namespace SilverCart\Model\Product;

use SilverCart\Dev\Tools;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\ProductConditionTranslation;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\Filters\PartialMatchFilter;

/**
 * Definition for the condition of a product.
 *
 * @package SilverCart
 * @subpackage Model_Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductCondition extends DataObject {

    /**
     * attributes
     *
     * @var array
     */
    private static $db = array(
        'Code'             => 'Varchar',
        'SeoMicrodataCode' => "Enum(',NewCondition,UsedCondition,DamagedCondition,RefurbishedCondition','')",
    );
    
    /**
     * n:m relations
     *
     * @var array
     */
    private static $has_many = array(
        'Products'                     => Product::class,
        'ProductConditionTranslations' => ProductConditionTranslation::class,
    );
    
    /**
     * cast attribute class types to other SS types
     *
     * @var array
     */
    private static $casting = array(
        'Title'         => 'Varchar(255)',
        'MicrodataCode' => 'Text',
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartProductCondition';
    
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
        return Tools::singular_name_for($this);
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
        return Tools::plural_name_for($this);
    }
    
    /**
     * retirieves title from related language class depending on the set locale
     *
     * @return string
     */
    public function getTitle() {
        return $this->getTranslationFieldValue('Title');
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
            'Products'
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
        $fields = DataObjectExtension::getCMSFields($this, 'Code', false);
        
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
        
        $field = new \SilverCart\Forms\FormFields\TextField('Test', 'Test', 'Test');
        $field->setTemplate('Test');
        $fields->addFieldToTab('Root.Main', $field);
        $field2 = new \SilverStripe\Forms\OptionsetField('Test2', 'Test2', ['1','2','3']);
        $field2->setTemplate('Test');
        $fields->addFieldToTab('Root.Main', $field2);
        
        return $fields;
    }
    
    /**
     * Returns a string with HTML Code for a selector box that lets the user
     * choose a product condition.
     *
     * @return string
     */
    public static function getDropdownFieldOptionSet() {
        $productConditionMap    = array();
        $productConditions      = ProductCondition::get();
        
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.02.2018
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Title'                                => _t(ProductCondition::class . '.TITLE', 'Condition'),
                'Products'                             => _t(Product::class . '.PLURALNAME', 'Products'),
                'ProductConditionTranslations'         => _t(ProductConditionTranslation::class . '.PLURALNAME', 'Translations'),
                'DefaultDamaged'                       => _t(ProductCondition::class . '.DefaultDamaged', 'Damaged'),
                'DefaultNew'                           => _t(ProductCondition::class . '.DefaultNew', 'New'),
                'DefaultRefurbished'                   => _t(ProductCondition::class . '.DefaultRefurbished', 'Refurbished'),
                'DefaultUsed'                          => _t(ProductCondition::class . '.DefaultUsed', 'Used'),
                'SeoMicrodataCode'                     => _t(ProductCondition::class . '.SeoMicrodataCode', 'SEO microdata code'),
                'SeoMicrodataCodeDescription'          => _t(ProductCondition::class . '.SeoMicrodataCodeDescription', 'Set up one of these values to increase the SEO visibility.'),
                'SeoMicrodataCodeDamagedCondition'     => _t(ProductCondition::class . '.SeoMicrodataCodeDamaged', 'Damaged'),
                'SeoMicrodataCodeNewCondition'         => _t(ProductCondition::class . '.SeoMicrodataCodeNew', 'New'),
                'SeoMicrodataCodeRefurbishedCondition' => _t(ProductCondition::class . '.SeoMicrodataCodeRefurbished', 'Refurbished'),
                'SeoMicrodataCodeUsedCondition'        => _t(ProductCondition::class . '.SeoMicrodataCodeUsed', 'Used'),
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
            'ProductConditionTranslations.Title' => array(
                'title' => $this->fieldLabel('Title'),
                'filter' => PartialMatchFilter::class,
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
            $condition = ProductCondition::get()->filter('Code', $code)->first();
            if (is_null($condition) ||
                !$condition->exists()) {
                $condition = new ProductCondition();
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