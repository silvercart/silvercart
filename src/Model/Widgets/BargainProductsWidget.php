<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Model\ {
    Product\Product,
    Translation\TranslationTools,
    Widgets\BargainProductsWidgetTranslation,
    Widgets\Widget,
    Widgets\WidgetTools
};
use SilverCart\View\GroupView\GroupViewHandler;
use SilverStripe\ORM\ {
    ArrayList,
    FieldType\DBBoolean,
    FieldType\DBInt,
    FieldType\DBVarchar
};

/**
 * Provides the a view of the bargain products.
 * You can define the number of products to be shown.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.08.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class BargainProductsWidget extends Widget
{
    /**
     * DB attributes of this widget
     * 
     * @var array
     */
    private static $db = [
        'numberOfProductsToFetch' => DBInt::class,
        'fetchMethod'             => "Enum('random,sortOrderAsc,sortOrderDesc','random')",
        'GroupView'               => DBVarchar::class,
        'isContentView'           => DBBoolean::class,
        'UseAsSlider'             => DBBoolean::class,
    ];
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_many = [
        'BargainProductsWidgetTranslations' => BargainProductsWidgetTranslation::class
    ];
    /**
     * Set default values.
     * 
     * @var array
     */
    private static $defaults = [
        'numberOfProductsToFetch' => 5,
        'UseAsSlider'             => true
    ];
    /**
     * Casted Attributes.
     * 
     * @var array
     */
    private static $casting = [
        'FrontTitle'   => 'Text',
        'FrontContent' => 'Text',
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartBargainProductsWidget';
    /**
     * Plain product elements
     *
     * @var ArrayList 
     */
    protected $products= null;
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
     * Getter for the FrontContent depending on the set language
     *
     * @return string The HTML front content
     */
    public function getFrontContent()
    {
        return $this->getTranslationFieldValue('FrontContent');
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        
        $fetchMethods = [
            'random'        => $this->fieldLabel('fetchMethodRandom'),
            'sortOrderAsc'  => $this->fieldLabel('fetchMethodSortOrderAsc'),
            'sortOrderDesc' => $this->fieldLabel('fetchMethodSortOrderDesc'),
        ];
        $fetchMethodsField = $fields->dataFieldByName('fetchMethod');
        $fetchMethodsField->setSource($fetchMethods);
        $fields->replaceField('GroupView', GroupViewHandler::getGroupViewDropdownField('GroupView', $this->fieldLabel('GroupView')));
        
        return $fields;
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.08.2018
     */
    public function fieldLabels($includerelations = true)
    {
        return array_merge(
                parent::fieldLabels($includerelations),
                WidgetTools::fieldLabelsForProductSliderWidget($this),
                [
                    'ProductGroupPage'                  => _t(ProductGroupItemsWidget::class . '.STOREADMIN_FIELDLABEL', 'Please choose the product group to display:'),
                    'useSelectionMethod'                => _t(ProductGroupItemsWidget::class . '.USE_SELECTIONMETHOD', 'Selection method for products'),
                    'SelectionMethodProductGroup'       => _t(ProductGroupItemsWidget::class . '.SELECTIONMETHOD_PRODUCTGROUP', 'From product group'),
                    'SelectionMethodProducts'           => _t(ProductGroupItemsWidget::class . '.SELECTIONMETHOD_PRODUCTS', 'Choose manually'),
                    'ProductGroupTab'                   => _t(ProductGroupItemsWidget::class . '.CMS_PRODUCTGROUPTABNAME', 'Product group'),
                    'ProductsTab'                       => _t(ProductGroupItemsWidget::class . '.CMS_PRODUCTSTABNAME', 'Products'),
                    'BargainProductsWidgetTranslations' => _t(TranslationTools::class . '.TRANSLATIONS', 'Translations'),
                    'UseAsSlider'                       => _t(Widget::class . '.UseAsSlider', 'Use as a slider'),
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
     * @since 27.03.2012
     */
    public function Elements()
    {
        if (is_null($this->elements)) {
            if (!$this->numberOfProductsToFetch) {
                $defaults = $this->config()->get('defaults');
                $this->numberOfProductsToFetch = $defaults['numberOfProductsToFetch'];
            }
            $priceField = 'PriceGrossAmount';
            if (Config::Pricetype() === Config::PRICE_TYPE_NET) {
                $priceField = 'PriceNetAmount';
            }
            $productTable = Tools::get_table_name(Product::class);
            switch ($this->fetchMethod) {
                case 'sortOrderAsc':
                    $sort = '"' . $productTable . '"."MSRPriceAmount" - "' . $productTable . '"."PriceGrossAmount" ASC';
                    break;
                case 'sortOrderDesc':
                    $sort = '"' . $productTable . '"."MSRPriceAmount" - "' . $productTable . '"."PriceGrossAmount" DESC';
                    break;
                case 'random':
                default:
                    $sort = "RAND()";
            }
            $this->listFilters = [];
            if (count(self::$registeredFilterPlugins) > 0) {
                foreach (self::$registeredFilterPlugins as $registeredPlugin) {
                    $pluginFilters = $registeredPlugin->filter();

                    if (is_array($pluginFilters)) {
                        $this->listFilters = array_merge(
                            $this->listFilters,
                            $pluginFilters
                        );
                    }
                }
            }
            $filter = sprintf(
                            '"' . $productTable . '"."MSRPriceAmount" IS NOT NULL 
                            AND "' . $productTable . '"."MSRPriceAmount" > 0
                            AND "' . $productTable . '"."%s" < "' . $productTable . '"."MSRPriceAmount"',
                            $priceField
            );
            foreach ($this->listFilters as $listFilterIdentifier => $listFilter) {
                $filter .= ' ' . $listFilter;
            }
            $products = Product::getProducts(
                    $filter,
                    $sort,
                    null,
                    "0," . $this->numberOfProductsToFetch
            );
            $this->elements = $products;
        }
        return $this->elements;
    }
    
    /**
     * Returns the content for non slider widgets
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.05.2012
     */
    public function ElementsContent()
    {
        return $this->customise([
            'Elements' => $this->Elements(),
        ])->renderWith(WidgetTools::getGroupViewTemplateName($this));
    }
    
    /**
     * Returns the products to inject into a product group
     *
     * @return ArrayList
     */
    public function getProducts()
    {
        $this->products = ArrayList::create();
        if (!$this->UseAsSlider) {
            $this->products = $this->Elements();
        }
        return $this->products;
    }
    
    /**
     * Creates the cache key for this widget.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2012
     */
    public function WidgetCacheKey()
    {
        return WidgetTools::ProductWidgetCacheKey($this);
    }
}