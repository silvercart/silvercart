<?php

namespace SilverCart\Model\Product;

use SilverCart\Dev\Tools;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Translation\TranslationExtension;
use SilverCart\ORM\DataObjectCacheExtension;
use SilverCart\ORM\DataObjectExtension;
use SilverCart\ORM\ExtensibleDataObject;
use SilverCart\VersionedDataObject\Extensions\Model\VersionedDataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;
use SilverStripe\VersionedAdmin\Forms\HistoryViewerField;
use SilverStripe\View\Requirements;
use function _t;

/**
 * Translations for a product.
 *
 * @package SilverCart
 * @subpackage Model_Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $Title            Title
 * @property string $ShortDescription Short Description
 * @property string $LongDescription  Long Description
 * @property string $CartDescription  Cart Description
 * @property string $MetaDescription  Meta Description
 * @property string $MetaTitle        Meta Title
 * @property string $OrderEmailText   Order Email Text
 * 
 * @method Product Product() Returns the related Product.
 * 
 * @mixin DataObjectCacheExtension
 * @mixin DataObjectExtension
 * @mixin TranslationExtension
 * @mixin Versioned
 */
class ProductTranslation extends DataObject
{
    use ExtensibleDataObject;
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = [
        'Title'            => 'Varchar(255)',
        'ShortDescription' => 'Text',
        'LongDescription'  => 'HTMLText',
        'CartDescription'  => 'HTMLText',
        'MetaDescription'  => 'Varchar(255)',
        'MetaTitle'        => 'Varchar(64)', //search engines use only 64 chars
        'OrderEmailText'   => 'HTMLText',
    ];
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_one = [
        'Product' => Product::class,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartProductTranslation';
    /**
     * Extensions
     * 
     * @var string[]
     */
    private static $extensions = [
        TranslationExtension::class,
        DataObjectCacheExtension::class,
        //Versioned::class . ".versioned",
        //VersionedDataObject::class,
    ];
    /**
     * Grant API access on this item.
     *
     * @var bool|array
     */
    private static bool|array $api_access = true;
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this); 
    }
    
    /**
     * Returns an array of field/relation names (db, has_one, has_many, 
     * many_many, belongs_many_many) to exclude from form scaffolding in
     * backend.
     * This is a performance friendly way to exclude fields.
     * 
     * @return array
     */
    public function excludeFromScaffolding() : array
    {
        $excludeFromScaffolding = [
            'Product'
        ];
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     */
    public function getCMSFields() : FieldList
    {
        return DataObjectExtension::getCMSFields($this);
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
        $this->beforeUpdateFieldLabels(function(&$labels) {
            $labels = array_merge(
                    $labels,
                    [
                        'Title'            => _t(Product::class . '.COLUMN_TITLE', 'Title'),
                        'ShortDescription' => _t(Product::class . '.SHORTDESCRIPTION', 'Listdescription'),
                        'LongDescription'  => _t(Product::class . '.DESCRIPTION', 'Description'),
                        'CartDescription'  => _t(Product::class . '.CartDescription', 'Cart description'),
                        'MetaDescription'  => _t(Product::class . '.METADESCRIPTION', 'Meta description for search engines'),
                        'MetaTitle'        => _t(Product::class . '.METATITLE', 'Meta title for search engines'),
                        'Locale'           => _t(Product::class . '.LOCALE', 'Locale'),
                        'OrderEmailText'   => _t(Product::class . '.OrderEmailText', 'Order Confirmation Email Information'),
                    ]
            );
        });
        return parent::fieldLabels($includerelations);
    }
    
    /**
     * columns for table overview
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
     * Sets the cache relevant fields.
     * 
     * @return array
     */
    public function getCacheRelevantFields() : array
    {
        $cacheRelevantFields = [
            'Title',
            'ShortDescription',
            'LongDescription',
            'MetaDescription',
            'MetaTitle',
        ];
        $this->extend('updateCacheRelevantFields', $cacheRelevantFields);
        return $cacheRelevantFields;
    }
}