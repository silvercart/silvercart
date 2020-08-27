<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\ {
    Pages\ProductGroupHolder,
    Pages\ProductGroupPage,
    Translation\TranslationTools,
    Widgets\ProductGroupWidgetTranslation,
    Widgets\Widget,
    Widgets\WidgetTools
};
use SilverStripe\Control\Director;
use SilverStripe\Forms\TreeMultiselectField;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\ORM\ {
    ArrayList,
    FieldType\DBBoolean,
    FieldType\DBText
};

/**
 * Widget to show a set of product groups.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 24.08.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductGroupWidget extends Widget
{
    /**
     * DB attributes of this widget
     * 
     * @var array
     */
    private static $db = [
        'ProductGroupIDs' => DBText::class,
        'UseAsSlider'     => DBBoolean::class,
    ];
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_many = [
        'ProductGroupWidgetTranslations' => ProductGroupWidgetTranslation::class,
    ];
    /**
     * Set default values.
     * 
     * @var array
     */
    private static $defaults = [
        'UseAsSlider' => true
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
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartProductGroupWidget';
    /**
     * Product elements
     *
     * @var ArrayList 
     */
    protected $elements = null;
    
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
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        
        $fields->removeByName('ProductGroupIDs');
        
        $holder = ProductGroupHolder::get()->first();
        
        $productGroupField = TreeMultiselectField::create('ProductGroupIDField', $this->fieldLabel('ProductGroups'), SiteTree::class)
                ->setValue($this->ProductGroupIDs)
                ->setTreeBaseID($holder->ID);
        $fields->addFieldToTab('Root.Main', $productGroupField);
        
        return $fields;
    }
    
    /**
     * Returns the ProductGroupIDs property.
     * Alias to use with TreeMultiselectField.
     * 
     * @return string
     */
    public function getProductGroupIDField()
    {
        return empty($this->ProductGroupIDs) ? ArrayList::create() : $this->ProductGroupIDs;
    }
    
    /**
     * Returns the ProductGroupIDs property.
     * Alias to use with TreeMultiselectField.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.08.2018
     */
    public function ProductGroupIDField()
    {
        return $this->getProductGroupIDField();
    }
    
    /**
     * Will be called by TreeMultiselectField after a successful post request.
     * 
     * @param array $items Chosen items
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.08.2018
     */
    public function onChangeProductGroupIDField($items)
    {
        $this->ProductGroupIDs = implode(',', $items);
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.08.2018
     */
    public function fieldLabels($includerelations = true)
    {
        return array_merge(
                parent::fieldLabels($includerelations),
                WidgetTools::fieldLabelsForProductSliderWidget($this),
                [
                    'ProductGroups'                  => ProductGroupPage::singleton()->plural_name(),
                    'ProductGroupWidgetTranslations' => _t(TranslationTools::class . '.TRANSLATIONS', 'Translations'),
                    'UseAsSlider'                    => _t(Widget::class . '.UseAsSlider', 'Use as a slider'),
                ]
        );
    }
    
    /**
     * Returns the elements
     *
     * @return ArrayList
     */
    public function getElements()
    {
        if (is_null($this->elements)) {
            $this->Elements();
        }
        return $this->elements;
    }

    /**
     * Sets the elements
     *
     * @param ArrayList $elements Elements to set
     * 
     * @return void
     */
    public function setElements(ArrayList $elements)
    {
        $this->elements = $elements;
    }
    
    /**
     * Returns a number of bargain products.
     * 
     * @return SS_List
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.08.2018
     */
    public function Elements()
    {
        if (is_null($this->elements)) {
            $this->elements = $this->ProductGroups();
        }
        return $this->elements;
    }
    
    /**
     * Returns the related product groups.
     * 
     * @return \SilverStripe\ORM\SS_List
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.08.2018
     */
    public function ProductGroups()
    {
        $productGroups   = ArrayList::create();
        $productGroupIDs = explode(',', $this->ProductGroupIDs);
        if (count($productGroupIDs) > 0) {
            $productGroups = ProductGroupPage::get()->filter('ID', $productGroupIDs);
        }
        return $productGroups;
    }
    
    /**
     * Creates the cache key for this widget.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.08.2018
     */
    public function WidgetCacheKey()
    {
        $cacheKey = i18n::get_locale() . '_' . implode('_', $this->ProductGroups()->map('ID', 'ID')->toArray());
        if (Director::isDev()) {
            $cacheKey .= '_' . uniqid();
        }
        return $cacheKey;
    }
}