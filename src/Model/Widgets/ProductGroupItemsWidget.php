<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Admin\Model\Config;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Widgets\ProductSliderWidget;
use SilverCart\Model\Widgets\Widget;
use SilverCart\Model\Widgets\WidgetTools;
use SilverCart\Model\Widgets\ProductGroupItemsWidgetTranslation;

/**
 * Provides a view of items of a definable productgroup.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductGroupItemsWidget extends Widget {
    
    use ProductSliderWidget;
    
    /**
     * Attributes.
     * 
     * @var array
     */
    private static $db = array(
        'numberOfProductsToShow'  => 'Int',
        'numberOfProductsToFetch' => 'Int',
        'fetchMethod'             => "Enum('random,sortOrderAsc','random')",
        'GroupView'               => 'Varchar(255)',
        'isContentView'           => 'Boolean',
        'Autoplay'                => 'Boolean(1)',
        'buildArrows'             => 'Boolean(1)',
        'buildNavigation'         => 'Boolean(1)',
        'buildStartStop'          => 'Boolean(1)',
        'slideDelay'              => 'Int',
        'stopAtEnd'               => 'Boolean(0)',
        'transitionEffect'        => "Enum('fade,horizontalSlide,verticalSlide','fade')",
        'useSlider'               => "Boolean(0)",
        'useRoundabout'           => "Boolean(0)",
        'ProductGroupPageID'      => 'Int',
        'useSelectionMethod'      => "Enum('productGroup,products','productGroup')"
    );

    /**
     * Has_many relationships.
     *
     * @var array
     */
    private static $many_many = array(
        'Products' => Product::class,
    );
    /**
     * Has_many relationships.
     *
     * @var array
     */
    private static $many_many_extraFields = [
        'Products' => [
            'Sort' => 'Int',
        ],
    ];
    
    /**
     * field casting
     *
     * @var array
     */
    private static $casting = array(
        'FrontTitle'   => 'Varchar(255)',
        'FrontContent' => 'Text',
    );
    
    /**
     * Set default values.
     * 
     * @var array
     */
    private static $defaults = array(
        'numberOfProductsToShow'  => 5,
        'numberOfProductsToFetch' => 5,
        'slideDelay'              => 5000
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_many = array(
        'ProductGroupItemsWidgetTranslations' => ProductGroupItemsWidgetTranslation::class,
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartProductGroupItemsWidget';
    
    /**
     * Getter for the front title depending on the set language
     *
     * @return string
     */
    public function getFrontTitle() {
        return $this->getTranslationFieldValue('FrontTitle');
    }
    
    /**
     * Getter for the FrontContent depending on the set language
     *
     * @return string
     */
    public function getFrontContent() {
        return $this->getTranslationFieldValue('FrontContent');
    }
    
    /**
     * Returns an array of field/relation names (db, has_one, has_many, 
     * many_many, belongs_many_many) to exclude from form scaffolding in
     * backend.
     * This is a performance friendly way to exclude fields.
     * Excludes all fields that are added in a ToggleCompositeField later.
     * 
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 21.02.2013
     */
    public function excludeFromScaffolding() {
        $parentExcludes = parent::excludeFromScaffolding();
        
        $excludeFromScaffolding = array_merge(
                $parentExcludes,
                array(
                    'Autoplay',
                    'buildArrows',
                    'buildNavigation',
                    'buildStartStop',
                    'slideDelay',
                    'stopAtEnd',
                    'transitionEffect',
                    'useSlider',
                    'useRoundabout',
                    'GroupView',
                    'ProductGroupPageID',
                )
        );
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     */
    public function getCMSFields() {
        $fetchMethods = array(
            'random'       => $this->fieldLabel('fetchMethodRandom'),
            'sortOrderAsc' => $this->fieldLabel('fetchMethodSortOrderAsc')
        );
        $fields = WidgetTools::getCMSFieldsForProductSliderWidget($this, $fetchMethods);
        
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
     * @since 03.03.2014
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                WidgetTools::fieldLabelsForProductSliderWidget($this),
                array(
                    'ProductGroupPage'                    => _t(ProductGroupItemsWidget::class . '.STOREADMIN_FIELDLABEL', 'Please choose the product group to display:'),
                    'ProductGroupPageDescription'         => _t(ProductGroupItemsWidget::class . '.ProductGroupPageDescription', 'Only needed if "Selection method for products" is set to "From product group".'),
                    'useSelectionMethod'                  => _t(ProductGroupItemsWidget::class . '.USE_SELECTIONMETHOD', 'Selection method for products'),
                    'SelectionMethodProductGroup'         => _t(ProductGroupItemsWidget::class . '.SELECTIONMETHOD_PRODUCTGROUP', 'From product group'),
                    'SelectionMethodProducts'             => _t(ProductGroupItemsWidget::class . '.SELECTIONMETHOD_PRODUCTS', 'Choose manually'),
                    'ProductGroupTab'                     => _t(ProductGroupItemsWidget::class . '.CMS_PRODUCTGROUPTABNAME', 'Product group'),
                    'ProductsTab'                         => _t(ProductGroupItemsWidget::class . '.CMS_PRODUCTSTABNAME', 'Products'),
                    'ProductGroupItemsWidgetTranslations' => _t(Config::class . '.TRANSLATIONS', 'Translations'),
                    'SelectProductDescription'            => _t(ProductGroupItemsWidget::class . '.SELECT_PRODUCT_DESCRIPTION', 'Select products by product number seperated by semicolon'),
                    'Products'                            => _t(Product::class . '.PLURALNAME', 'Products')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
}