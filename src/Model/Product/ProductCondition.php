<?php

namespace SilverCart\Model\Product;

use SilverCart\Dev\Tools;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\ProductConditionTranslation;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\Filters\PartialMatchFilter;
use SilverStripe\ORM\HasManyList;

/**
 * Definition for the condition of a product.
 *
 * @package SilverCart
 * @subpackage Model_Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $Code             Code
 * @property string $SeoMicrodataCode SEO Microdata Code
 * 
 * @property string $Title Title (current locale context)
 * 
 * @method HasManyList Products()                     Returns a list of related Products.
 * @method HasManyList ProductConditionTranslations() Returns a list of translations for this ProductCondition.
 */
class ProductCondition extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * attributes
     *
     * @var array
     */
    private static $db = [
        'Code'             => 'Varchar',
        'SeoMicrodataCode' => "Enum(',NewCondition,UsedCondition,DamagedCondition,RefurbishedCondition','')",
    ];
    /**
     * n:m relations
     *
     * @var array
     */
    private static $has_many = [
        'Products'                     => Product::class,
        'ProductConditionTranslations' => ProductConditionTranslation::class,
    ];
    /**
     * cast attribute class types to other SS types
     *
     * @var array
     */
    private static $casting = [
        'Title'         => 'Varchar(255)',
        'MicrodataCode' => 'Text',
    ];
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
    private static $default_microdata_codes = [
        'new'         => 'NewCondition',
        'used'        => 'UsedCondition',
        'damaged'     => 'DamagedCondition',
        'refurbished' => 'RefurbishedCondition',
    ];
    
    /**
     * Returns the translated singular name.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this); 
    }
    
    /**
     * retirieves title from related language class depending on the set locale
     *
     * @return string
     */
    public function getTitle() : string
    {
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
    public function excludeFromScaffolding() : array
    {
        $excludeFromScaffolding = [
            'Products'
        ];
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }

    /**
     * define the CMS fields
     *
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $enumValues = $this->dbObject('SeoMicrodataCode')->enumValues();
            $i18nSource = [];
            foreach ($enumValues as $value => $label) {
                if (empty($label)) {
                    $i18nSource[$value] = '';
                } else {
                    $i18nSource[$value] = $this->fieldLabel('SeoMicrodataCode' . $label);
                }
            }
            $fields->dataFieldByName('SeoMicrodataCode')->setSource($i18nSource);
            $fields->dataFieldByName('SeoMicrodataCode')->setDescription($this->fieldLabel('SeoMicrodataCodeDescription'));
        });
        return DataObjectExtension::getCMSFields($this, 'Code', false);
    }
    
    /**
     * Returns a string with HTML Code for a selector box that lets the user
     * choose a product condition.
     *
     * @return string
     */
    public static function getDropdownFieldOptionSet() : string
    {
        $productConditionMap = [];
        $productConditions   = ProductCondition::get();
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
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
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
        ]);
    }
    
    /**
     * Defines the form fields for the search in ModelAdmin
     * 
     * @return array
     */
    public function searchableFields() : array
    {
        $searchableFields = [
            'ProductConditionTranslations.Title' => [
                'title' => $this->fieldLabel('Title'),
                'filter' => PartialMatchFilter::class,
            ],
        ];
        $this->extend('updateSearchableFields', $searchableFields);
        return $searchableFields;
    }
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     */
    public function summaryFields() : array
    {
        $summaryFields = [
            'Title' => $this->fieldLabel('Title'),
        ];
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Default records for product conditions.
     * 
     * @return void
     */
    public function requireDefaultRecords() : void
    {
        foreach (self::$default_microdata_codes as $code => $microdataCode) {
            $condition = ProductCondition::get()->filter('Code', $code)->first();
            if (is_null($condition)
             || !$condition->exists()
            ) {
                $condition = ProductCondition::create();
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
    public function getMicrodataCode() : string
    {
        $microDataCode = $this->SeoMicrodataCode;
        if (empty($microDataCode)
         && array_key_exists($this->Title, self::$default_microdata_codes)
        ) {
            $microDataCode = self::$default_microdata_codes[$this->Code];
        }
        if (!empty($microDataCode)) {
            $microDataCode =  "http://schema.org/{$microDataCode}";
        }
        return $microDataCode;
    }
}