<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Widgets
 */

/**
 * Provides the a view of the bargain products.
 * 
 * You can define the number of products to be shown.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.03.2012
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartBargainProductsWidget extends SilvercartWidget implements SilvercartProductSliderWidget {
    
    /**
     * DB attributes of this widget
     * 
     * @var array
     */
    public static $db = array(
        'numberOfProductsToShow'        => 'Int',
        'numberOfProductsToFetch'       => 'Int',
        'fetchMethod'                   => "Enum('random,sortOrderAsc,sortOrderDesc','random')",
        'GroupView'                     => 'VarChar(255)',
        'isContentView'                 => 'Boolean',
        'Autoplay'                      => 'Boolean(1)',
        'autoPlayDelayed'               => 'Boolean(1)',
        'autoPlayLocked'                => 'Boolean(0)',
        'buildArrows'                   => 'Boolean(1)',
        'buildNavigation'               => 'Boolean(1)',
        'buildStartStop'                => 'Boolean(1)',
        'slideDelay'                    => 'Int',
        'stopAtEnd'                     => 'Boolean(0)',
        'transitionEffect'              => "Enum('fade,horizontalSlide,verticalSlide','fade')",
        'useSlider'                     => "Boolean(0)",
        'useRoundabout'                 => "Boolean(0)",
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartBargainProductsWidgetLanguages' => 'SilvercartBargainProductsWidgetLanguage'
    );
    
    /**
     * Set default values.
     * 
     * @var array
     */
    public static $defaults = array(
        'numberOfProductsToShow'    => 5,
        'numberOfProductsToFetch'   => 5,
        'slideDelay'                => 5000
    );
    
    /**
     * Casted Attributes.
     * 
     * @var array
     */
    public static $casting = array(
        'FrontTitle'                    => 'Text',
        'FrontContent'                  => 'Text',
    );
    
    /**
     * Getter for the front title depending on the set language
     *
     * @return string  
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012
     */
    public function getFrontTitle() {
        return $this->getLanguageFieldValue('FrontTitle');
    }
    
    /**
     * Getter for the FrontContent depending on the set language
     *
     * @return string The HTML front content 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012
     */
    public function getFrontContent() {
        return $this->getLanguageFieldValue('FrontContent');
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.03.2012
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this, 'ExtraCssClasses', false);
        
        $fetchMethods               = array(
                'random'        => $this->fieldLabel('fetchMethodRandom'),
                'sortOrderAsc'  => $this->fieldLabel('fetchMethodSortOrderAsc'),
                'sortOrderDesc' => $this->fieldLabel('fetchMethodSortOrderDesc'),
        );
        $fetchMethodsField = $fields->dataFieldByName('fetchMethod');
        $fetchMethodsField->setSource($fetchMethods);
        $fields->replaceField('GroupView', SilvercartGroupViewHandler::getGroupViewDropdownField('GroupView', $this->fieldLabel('GroupView')));
        
        // Temporary disabled slider functions.
        //SilvercartWidgetTools::getCMSFieldsSliderTabForProductSliderWidget($this, $fields);
        $fields->removeByName('numberOfProductsToShow');
        
        return $fields;
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
                    'autoPlayDelayed',
                    'autoPlayLocked',
                    'buildArrows',
                    'buildNavigation',
                    'buildStartStop',
                    'slideDelay',
                    'stopAtEnd',
                    'transitionEffect',
                    'useSlider',
                    'useRoundabout'
                )
        );
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }
    
    
    
    /**
     * Returns the slider tab input fields for this widget.
     * 
     * @param TabSet &$rootTabSet The root tab set
     * 
     * @return FieldList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.03.2012
     */
    public function getCMSFieldsSliderTab(&$rootTabSet) {
        SilvercartWidgetTools::getCMSFieldsSliderTabForProductSliderWidget($this, $rootTabSet);
    }
    
    /**
     * Returns the slider tab input fields for this widget.
     * 
     * @param TabSet &$rootTabSet The root tab set
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.03.2012
     */
    public function getCMSFieldsRoundaboutTab(&$rootTabSet) {
        SilvercartWidgetTools::getCMSFieldsRoundaboutTabForProductSliderWidget($this, $rootTabSet);
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.03.2012
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                SilvercartWidgetTools::fieldLabelsForProductSliderWidget($this),
                array(
                    'SilvercartProductGroupPage'                => _t('SilvercartProductGroupItemsWidget.STOREADMIN_FIELDLABEL'),
                    'useSelectionMethod'                        => _t('SilvercartProductGroupItemsWidget.USE_SELECTIONMETHOD'),
                    'SelectionMethodProductGroup'               => _t('SilvercartProductGroupItemsWidget.SELECTIONMETHOD_PRODUCTGROUP'),
                    'SelectionMethodProducts'                   => _t('SilvercartProductGroupItemsWidget.SELECTIONMETHOD_PRODUCTS'),
                    'ProductGroupTab'                           => _t('SilvercartProductGroupItemsWidget.CMS_PRODUCTGROUPTABNAME'),
                    'ProductsTab'                               => _t('SilvercartProductGroupItemsWidget.CMS_PRODUCTSTABNAME'),
                    'SilvercartBargainProductsWidgetLanguages'  => _t('Silvercart.TRANSLATIONS'),
                )
        );
    }
    
    /**
     * We set checkbox field values here to false if they are not in the post
     * data array.
     *
     * @param array $data The post data array
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.03.2012
     */
    public function populateFromPostData($data) {
        SilvercartWidgetTools::populateFromPostDataForProductSliderWidget($this, $data);
        parent::populateFromPostData($data);
    }
    
    /**
     * Sets numberOfProductsToFetch to numberOfProductsToShow if it's set to 0.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.03.2016
     */
    public function onBeforeWrite() {
        parent::onBeforeWrite();
        if ($this->numberOfProductsToShow == 0) {
            $this->numberOfProductsToShow = $this->numberOfProductsToFetch;
        }
    }
}

/**
 * Provides the a view of the bargain products.
 * 
 * You can define the number of products to be shown.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.03.2012
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartBargainProductsWidget_Controller extends SilvercartWidget_Controller implements SilvercartProductSliderWidget_Controller {

    /**
     * Product elements
     *
     * @var ArrayList 
     */
    protected $elements = null;

    /**
     * Plain product elements
     *
     * @var ArrayList 
     */
    protected $products= null;
    
    /**
     * Returns the elements
     *
     * @return ArrayList
     */
    public function getElements() {
        return $this->elements;
    }

    /**
     * Sets the elements
     *
     * @param ArrayList $elements Elements to set
     * 
     * @return void
     */
    public function setElements(ArrayList $elements) {
        $this->elements = $elements;
    }
    
    /**
     * Register forms for the contained products.
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.07.2011
     */
    public function init() {
        SilvercartWidgetTools::initProductSliderWidget($this);
    }
    
    /**
     * Insert the javascript necessary for the anything slider.
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 11.12.2011
     */
    public function initAnythingSlider() {
        SilvercartWidgetTools::initAnythingSliderForProductSliderWidget($this);
    }
    
    /**
     * Insert the javascript necessary for the roundabout slider.
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 11.12.2011
     */
    public function initRoundabout() {
        SilvercartWidgetTools::initRoundaboutForProductSliderWidget($this);
    }
    
    /**
     * Returns a number of bargain products.
     * 
     * @return SS_List
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.03.2012
     */
    public function Elements() {
        if (is_null($this->elements)) {
            if (!$this->numberOfProductsToFetch) {
                $this->numberOfProductsToFetch = SilvercartBargainProductsWidget::$defaults['numberOfProductsToFetch'];
            }

            if (SilvercartConfig::Pricetype() == 'net') {
                $priceField = 'PriceNetAmount';
            } else {
                $priceField = 'PriceGrossAmount';
            }

            switch ($this->fetchMethod) {
                case 'sortOrderAsc':
                    $sort = "\"SilvercartProduct\".\"MSRPriceAmount\" - \"SilvercartProduct\".\"PriceGrossAmount\" ASC";
                    break;
                case 'sortOrderDesc':
                    $sort = "\"SilvercartProduct\".\"MSRPriceAmount\" - \"SilvercartProduct\".\"PriceGrossAmount\" DESC";
                    break;
                case 'random':
                default:
                    $sort = "RAND()";
            }
            $this->listFilters = array();

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
                            "\"SilvercartProduct\".\"MSRPriceAmount\" IS NOT NULL 
                            AND \"SilvercartProduct\".\"MSRPriceAmount\" > 0
                            AND \"SilvercartProduct\".\"%s\" < \"SilvercartProduct\".\"MSRPriceAmount\"",
                            $priceField
            );

            foreach ($this->listFilters as $listFilterIdentifier => $listFilter) {
                $filter .= ' ' . $listFilter;
            }

            $products = SilvercartProduct::getProducts(
                    $filter,
                    $sort,
                    null,
                    "0," . $this->numberOfProductsToFetch
            );
            
            $this->elements = $products;

            foreach ($this->elements as $element) {
                $element->addCartFormIdentifier = $this->ID.'_'.$element->ID;
            }
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
    public function ElementsContent() {
        return $this->customise(array(
            'Elements' => $this->Elements(),
        ))->renderWith(SilvercartWidgetTools::getGroupViewTemplateName($this));
    }
    
    /**
     * Returns a number of products from the chosen productgroup.
     * 
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.03.2012
     */
    public function ProductPages() {
        if (is_null($this->elements)) {
            $this->Elements();
            $pages          = array();
            $pageProducts   = array();
            $pageNr         = 0;
            $PageProductIdx = 1;
            $isFirst        = true;
            if ($this->elements) {
                foreach ($this->elements as $product) {
                    $product->addCartFormIdentifier = $this->ID.'_'.$product->ID;
                    $pageProducts[] = $product;
                    $PageProductIdx++;

                    if ($pageNr > 0) {
                        $isFirst = false;
                    }
                    if ($PageProductIdx > $this->numberOfProductsToShow) {
                        $pages['Page'.$pageNr] = array(
                            'Elements'  => new ArrayList($pageProducts),
                            'IsFirst'   => $isFirst
                        );
                        $PageProductIdx = 1;
                        $pageProducts   = array();
                        $pageNr++;
                    }
                }
            }

            if (!array_key_exists('Page'.$pageNr, $pages) &&
                !empty($pageProducts)) {
                if ($pageNr > 0) {
                    $isFirst = false;
                }
                $pages['Page'.$pageNr] = array(
                    'Elements'  => new ArrayList($pageProducts),
                    'IsFirst'   => $isFirst,
                );
            }
            $this->elements = new ArrayList($pages);
        } else {
            foreach ($this->elements as $page) {
                $page->Content = Controller::curr()->customise($page)->renderWith(SilvercartWidgetTools::getGroupViewTemplateName($this));
            }
        }
        return $this->elements;
    }
    
    /**
     * Returns the products to inject into a product group
     *
     * @return ArrayList
     */
    public function getProducts() {
        $this->products = new ArrayList();
        if (!$this->useSlider &&
            !$this->useRoundabout) {
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
    public function WidgetCacheKey() {
        $key = SilvercartWidgetTools::ProductWidgetCacheKey($this);
        return $key;
    }
    
    /**
     * Returns the number of products to show
     * 
     * @return int
     */
    public function numberOfProductsToShowForGroupView() {
        switch ($this->GroupView) {
            case 'tile':
                $divisor = 2;
                break;
            case 'threetile':
                $divisor = 3;
                break;
            case 'fourtile':
                $divisor = 4;
                break;
            default:
                $divisor = 1;
                break;
        }
        return ceil($this->numberOfProductsToShow / $divisor);
    }
}
