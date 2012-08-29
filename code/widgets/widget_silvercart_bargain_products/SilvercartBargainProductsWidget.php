<?php
/**
 * Copyright 2012 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
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
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
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
        $fields = SilvercartWidgetTools::getCMSFieldsForProductSliderWidget($this);
        
        $translationTab             = new Tab('Translations',   $this->fieldLabel('TranslationsTab'));
        $translationsTableField     = new ComplexTableField($this, 'SilvercartBargainProductsWidgetLanguages', 'SilvercartBargainProductsWidgetLanguage');
        
        $fields->addFieldToTab('Root', $translationTab);
        $translationTab->push($translationsTableField);
        
        return $fields;
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
     * Returns the title of this widget.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.03.2012
     */
    public function Title() {
        return _t($this->ClassName() . '.TITLE');
    }
    
    /**
     * Returns the title of this widget for display in the WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.03.2012
     */
    public function CMSTitle() {
        return _t($this->ClassName() . '.CMSTITLE');
    }
    
    /**
     * Returns the description of what this template does for display in the
     * WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.03.2012
     */
    public function Description() {
        return _t($this->ClassName() . '.DESCRIPTION');
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
                    'TranslationsTab'                           => _t('SilvercartConfig.TRANSLATIONS'),
                    'SilvercartBargainProductsWidgetLanguages'  => _t('SilvercartBargainProductsWidgetLanguage.PLURALNAME'),
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
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
 */
class SilvercartBargainProductsWidget_Controller extends SilvercartWidget_Controller implements SilvercartProductSliderWidget_Controller {

    /**
     * Product elements
     *
     * @var DataObjectSet 
     */
    protected $elements = null;

    /**
     * Plain product elements
     *
     * @var DataObjectSet 
     */
    protected $products= null;
    
    /**
     * Returns the elements
     *
     * @return DataObjectSet
     */
    public function getElements() {
        return $this->elements;
    }

    /**
     * Sets the elements
     *
     * @param DataObjectSet $elements Elements to set
     * 
     * @return void
     */
    public function setElements(DataObjectSet $elements) {
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
     * @return DataObjectSet
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
                    $sort = "`SilvercartProduct`.`MSRPriceAmount` - `SilvercartProduct`.`PriceGrossAmount` ASC";
                    break;
                case 'sortOrderDesc':
                    $sort = "`SilvercartProduct`.`MSRPriceAmount` - `SilvercartProduct`.`PriceGrossAmount` DESC";
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
                            "`SilvercartProduct`.`MSRPriceAmount` IS NOT NULL 
                            AND `SilvercartProduct`.`MSRPriceAmount` > 0
                            AND `SilvercartProduct`.`%s` < `SilvercartProduct`.`MSRPriceAmount`",
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
     * @return DataObjectSet
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
                    $pageProducts[] = $product;
                    $PageProductIdx++;

                    if ($pageNr > 0) {
                        $isFirst = false;
                    }
                    if ($PageProductIdx > $this->numberOfProductsToShow) {
                        $pages['Page'.$pageNr] = array(
                            'Elements'  => new DataObjectSet($pageProducts),
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
                    'Elements'  => new DataObjectSet($pageProducts),
                    'IsFirst'   => $isFirst,
                );
            }
            $this->elements = new DataObjectSet($pages);
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
     * @return DataObjectSet
     */
    public function getProducts() {
        $this->products = new DataObjectSet();
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
}