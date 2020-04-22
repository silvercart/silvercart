<?php

namespace SilverCart\Model\Widgets;

use DateTime;
use SilverCart\Model\ {
    Product\Product,
    Translation\TranslationTools,
    Widgets\Widget,
    Widgets\WidgetTools
};
use SilverStripe\Forms\CompositeField;
use SilverStripe\ORM\ {
    ArrayList,
    FieldType\DBBoolean,
    FieldType\DBInt
};

/**
 * Provides the a view of the new products.
 * 
 * You can define the number of products to be shown.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.08.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class NewProductsWidget extends Widget
{
    /**
     * Indicates the number of products that shall be shown with this widget.
     * 
     * @var int
     */
    private static $db = [
        'numberOfProductsToShow'   => DBInt::class,
        'UseAsSlider'              => DBBoolean::class,
        'ShowProductsFromQuantity' => DBInt::class,
        'ShowProductsFromUnit'     => 'Enum("day,month,year","month")',
    ];
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_many = [
        'NewProductsWidgetTranslations' => NewProductsWidgetTranslation::class
    ];
    /**
     * Casted Attributes.
     * 
     * @var array
     */
    private static $casting = [
        'FrontTitle' => 'Text',
    ];
    /**
     * Set default values.
     * 
     * @var array
     */
    private static $defaults = [
        'numberOfProductsToShow'   => 8,
        'UseAsSlider'              => true,
        'ShowProductsFromQuantity' => 2,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartNewProductsWidget';
    
    /**
     * Getter for the front title depending on the set language
     *
     * @return string
     */
    public function getFrontTitle()
    {
        return $this->getTranslationFieldValue('FrontTitle');
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.08.2018
     */
    public function fieldLabels($includerelations = true)
    {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                [
                    'numberOfProductsToShow'        => _t(self::class . '.numberOfProductsToShow', 'Number of products to show'),
                    'UseAsSlider'                   => _t(Widget::class . '.UseAsSlider', 'Use as a slider'),
                    'ShowProductsFrom'              => _t(self::class . '.ShowProductsFrom', 'Show products added within the last'),
                    'ShowProductsFromQuantity'      => _t(self::class . '.ShowProductsFromQuantity', 'Number of time units to show products in this widget'),
                    'ShowProductsFromUnit'          => _t(self::class . '.ShowProductsFromUnit', 'Time unit to show products in this widget'),
                    'NewProductsWidgetTranslations' => _t(TranslationTools::class . '.TRANSLATIONS', 'Translations'),
                ]
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        
        $unitSrc = [
            'day'   => Product::singleton()->fieldLabel('Days'),
            'month' => Product::singleton()->fieldLabel('Months'),
            'year'  => Product::singleton()->fieldLabel('Years'),
        ];
        $fields->dataFieldByName('ShowProductsFromUnit')->setSource($unitSrc);
        
        $showProductsFromField = CompositeField::create([
            $fields->dataFieldByName('ShowProductsFromQuantity'),
            $fields->dataFieldByName('ShowProductsFromUnit'),
        ])
                ->setTitle($this->fieldLabel('ShowProductsFrom'))
                ->setTemplate(CompositeField::class . '_noholder');
        $fields->removeByName('ShowProductsFromQuantity');
        $fields->removeByName('ShowProductsFromUnit');
        $fields->addFieldToTab('Root.Main', $showProductsFromField);
        
        return $fields;
    }
    
    /**
     * Returns a number of topseller products.
     * 
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.08.2018
     */
    public function Elements()
    {
        if (empty($this->ShowProductsFromQuantity)
         || empty($this->ShowProductsFromUnit)
        ) {
            return ArrayList::create();
        }
        if (!$this->numberOfProductsToShow) {
            $defaults = $this->config()->get('defaults');
            $this->numberOfProductsToShow = $defaults['numberOfProductsToShow'];
        }
        
        $timeDifference = '-' . $this->ShowProductsFromQuantity . ' ' . $this->ShowProductsFromUnit;
        $date           = new DateTime(date('Y-m-d H:i:s'));
        $date->format('Y/m/d');
        $date->modify($timeDifference);
        $modifiedDate   = $date->getTimestamp();
        $minCreated     = date('Y-m-d H:i:s', $modifiedDate);
        $products       = Product::get()
                ->where('"' . Product::config()->get('table_name') . '"."Created" > \'' . $minCreated . '\'')
                ->where('"' . Product::config()->get('table_name') . '"."ProductGroupID" > 0')
                ->limit($this->numberOfProductsToShow)
                ->sort('"' . Product::config()->get('table_name') . '"."Created"', 'DESC');
        
        return $products;
    }
    
    /**
     * Creates the cache key for this widget.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.08.2018
     */
    public function WidgetCacheKey()
    {
        $key = WidgetTools::ProductWidgetCacheKey($this);
        return $key;
    }
}