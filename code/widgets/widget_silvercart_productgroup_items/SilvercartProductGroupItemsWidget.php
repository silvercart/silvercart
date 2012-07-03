<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * Provides a view of items of a definable productgroup.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 26.05.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartProductGroupItemsWidget extends SilvercartWidget implements SilvercartProductSliderWidget {
    
    /**
     * Attributes.
     * 
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
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
        'SilvercartProductGroupPageID'  => 'Int',
        'useSelectionMethod'            => "Enum('productGroup,products','productGroup')"
    );

    /**
     * Has_many relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.02.2012
     */
    public static $many_many = array(
        'SilvercartProducts' => 'SilvercartProduct'
    );
    
    public static $casting = array(
        'FrontTitle'                    => 'VarChar(255)',
        'FrontContent'                  => 'HTMLText',
    );
    
    /**
     * Set default values.
     * 
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public static $defaults = array(
        'numberOfProductsToShow'    => 5,
        'numberOfProductsToFetch'   => 5,
        'slideDelay'                => 5000
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 27.01.2012
     */
    public static $has_many = array(
        'SilvercartProductGroupItemsWidgetLanguages' => 'SilvercartProductGroupItemsWidgetLanguage'
    );
    
    /**
     * Getter for the front title depending on the set language
     *
     * @return string  
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 27.01.2012
     */
    public function getFrontTitle() {
        $frontTitle = '';
        if ($this->getLanguage()) {
            $frontTitle = $this->getLanguage()->FrontTitle;
        }
        return $frontTitle;
    }
    
    /**
     * Getter for the FrontContent depending on the set language
     *
     * @return string The HTML front content 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 27.01.2012
     */
    public function getFrontContent() {
        $frontContent = '';
        if ($this->getLanguage()) {
            $frontContent = $this->getLanguage()->FrontContent;
        }
        return $frontContent;
    }
    
    /**
     * HtmlEditorFields need an own save method
     *
     * @param string $value content
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 27.01.2012
     */
    public function saveFrontContent($value) {
        $langObj = $this->getLanguage();
        $langObj->FrontContent = $value;
        $langObj->write();
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function getCMSFields() {
        $fields = SilvercartWidgetTools::getCMSFieldsForProductSliderWidget($this);
        
        $selectionMethods           = array(
                'productGroup'  => $this->fieldLabel('SelectionMethodProductGroup'),
                'products'      => $this->fieldLabel('SelectionMethodProducts'),
        );
        $productGroupField          = new SilvercartGroupedDropdownField(
            'SilvercartProductGroupPageID',
            $this->fieldLabel('SilvercartProductGroupPage'),
            SilvercartProductGroupHolder_Controller::getRecursiveProductGroupsForGroupedDropdownAsArray(null, true),
            $this->SilvercartProductGroupPageID
        );
        $productTableField          = new ManyManyComplexTableField($this, 'SilvercartProducts', 'SilvercartProduct');
        $selectionMethod            = new OptionsetField('useSelectionMethod',  $this->fieldLabel('useSelectionMethod'), $selectionMethods);
        $translationsTableField     = new ComplexTableField($this, 'SilvercartProductGroupItemsWidgetLanguages', 'SilvercartProductGroupItemsWidgetLanguage');
        
        $productGroupTab            = new Tab('productgroup',   $this->fieldLabel('ProductGroupTab'));
        $productsTab                = new Tab('products',       $this->fieldLabel('ProductsTab'));
        $translationTab             = new Tab('Translations',   $this->fieldLabel('TranslationsTab'));
        
        $fields->addFieldToTab('Root', $translationTab);
        $fields->addFieldToTab('Root.Basic.DisplaySet', $productGroupTab);
        $fields->addFieldToTab('Root.Basic.DisplaySet', $productsTab);
        $fields->addFieldToTab('Root.Basic.DisplaySet.Display', $selectionMethod);

        $productGroupTab->push($productGroupField);
        $productGroupTab->push($fields->dataFieldByName('fetchMethod'));
        $productGroupTab->push($fields->dataFieldByName('numberOfProductsToShow'));
        $productGroupTab->push($fields->dataFieldByName('numberOfProductsToFetch'));

        $productsTab->push($productTableField);
        
        $translationTab->push($translationsTableField);
        
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguage(true));
        foreach ($languageFields as $languageField) {
            $fields->addFieldToTab('Root.Basic.DisplaySet.Display', $languageField);
        }
        
        return $fields;
    }
    
    /**
     * Returns the slider tab input fields for this widget.
     * 
     * @param TabSet &$rootTabSet The root tab set
     * 
     * @return FieldSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public function getCMSFieldsSliderTab(&$rootTabSet) {
        SilvercartWidgetTools::getCMSFieldsSliderTabForProductSliderWidget($this, $rootTabSet);
    }
    
    /**
     * Returns the slider tab input fields for this widget.
     * 
     * @param TabSet &$rootTabSet The root tab set
     * 
     * @return FieldSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public function getCMSFieldsRoundaboutTab(&$rootTabSet) {
        SilvercartWidgetTools::getCMSFieldsRoundaboutTabForProductSliderWidget($this, $rootTabSet);
    }
    
    /**
     * Returns the title of this widget.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Title() {
        return _t('SilvercartProductGroupItemsWidget.TITLE');
    }
    
    /**
     * Returns the title of this widget for display in the WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function CMSTitle() {
        return _t('SilvercartProductGroupItemsWidget.CMSTITLE');
    }
    
    /**
     * Returns the description of what this template does for display in the
     * WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Description() {
        return _t('SilvercartProductGroupItemsWidget.DESCRIPTION');
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                SilvercartWidgetTools::fieldLabelsForProductSliderWidget($this),
                array(
                    'SilvercartProductGroupPage'    => _t('SilvercartProductGroupItemsWidget.STOREADMIN_FIELDLABEL'),
                    'useSelectionMethod'            => _t('SilvercartProductGroupItemsWidget.USE_SELECTIONMETHOD'),
                    'SelectionMethodProductGroup'   => _t('SilvercartProductGroupItemsWidget.SELECTIONMETHOD_PRODUCTGROUP'),
                    'SelectionMethodProducts'       => _t('SilvercartProductGroupItemsWidget.SELECTIONMETHOD_PRODUCTS'),
                    'ProductGroupTab'               => _t('SilvercartProductGroupItemsWidget.CMS_PRODUCTGROUPTABNAME'),
                    'ProductsTab'                   => _t('SilvercartProductGroupItemsWidget.CMS_PRODUCTSTABNAME'),
                    'TranslationsTab'               => _t('SilvercartConfig.TRANSLATIONS'),
                    'SilvercartProductGroupItemsWidgetLanguages' => _t('SilvercartProductGroupItemsWidgetLanguage.PLURALNAME'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * We set checkbox field values here to false if they are not in the post
     * data array.
     *
     * @param array $data The post data array
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public function populateFromPostData($data) {
        SilvercartWidgetTools::populateFromPostDataForProductSliderWidget($this, $data);
        parent::populateFromPostData($data);
    }
}

/**
 * Provides a view of items of a definable productgroup.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 26.05.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartProductGroupItemsWidget_Controller extends SilvercartWidget_Controller implements SilvercartProductSliderWidget_Controller {

    /**
     * Product elements
     *
     * @var DataObjectSet 
     */
    protected $elements = null;
    
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public function init() {
        SilvercartWidgetTools::initProductSliderWidget($this);
    }
    
    /**
     * Insert the javascript necessary for the anything slider.
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public function initAnythingSlider() {
        SilvercartWidgetTools::initAnythingSliderForProductSliderWidget($this);
    }
    
    /**
     * Insert the javascript necessary for the roundabout slider.
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public function initRoundabout() {
        SilvercartWidgetTools::initRoundaboutForProductSliderWidget($this);
    }
    
    /**
     * Returns a number of products from the chosen productgroup.
     * 
     * @return DataObjectSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function ProductPages() {
        if ($this->elements !== null) {
            foreach ($this->elements as $page) {
                $page->Content = Controller::curr()->customise($page)->renderWith(SilvercartWidgetTools::getGroupViewTemplateName($this));
            }
            return $this->elements;
        }
        switch ($this->useSelectionMethod) {
            case 'products':
                $this->elements = $this->getElementsByProducts();
                break;
            case 'productGroup':
            default:
                $this->elements = $this->getElementsByProductGroup();
                break;
        }

        if (!$this->SilvercartProductGroupPageID) {
            return false;
        }
        
        if (!$this->numberOfProductsToFetch) {
            $this->numberOfProductsToFetch = SilvercartProductGroupItemsWidget::$defaults['numberOfProductsToFetch'];
        }
        if (!$this->numberOfProductsToShow) {
            $this->numberOfProductsToShow = SilvercartProductGroupItemsWidget::$defaults['numberOfProductsToShow'];
        }

        if ($this->numberOfProductsToFetch < $this->numberOfProductsToShow) {
            $this->numberOfProductsToFetch = $this->numberOfProductsToShow;
        }

        $productgroupPage = DataObject::get_by_id(
            'SilvercartProductGroupPage',
            $this->SilvercartProductGroupPageID
        );

        if (!$productgroupPage) {
            return false;
        }
        $productgroupPageSiteTree = ModelAsController::controller_for($productgroupPage);
        
        switch ($this->fetchMethod) {
            case 'sortOrderAsc':
                $sort = "CASE WHEN SPGMSO.SortOrder THEN CONCAT(SPGMSO.SortOrder, SilvercartProduct.SortOrder) ELSE SilvercartProduct.SortOrder END ASC";
                break;
            case 'sortOrderDesc':
                $sort = "CASE WHEN SPGMSO.SortOrder THEN CONCAT(SPGMSO.SortOrder, SilvercartProduct.SortOrder) ELSE SilvercartProduct.SortOrder END DESC";
                break;
            case 'random':
            default:
                $sort = "RAND()";
        } 
        $products = $productgroupPageSiteTree->getProducts($this->numberOfProductsToFetch, $sort);

        $pages          = array();
        $pageProducts   = array();
        $pageNr         = 0;
        $PageProductIdx = 1;
        $isFirst        = true;

        if ($products) {
            foreach ($products as $product) {
                $pageProducts[] = $product;
                $PageProductIdx++;

                if ($pageNr > 0) {
                    $isFirst = false;
                }
                if ($PageProductIdx > $this->numberOfProductsToShow) {
                    $pages['Page'.$pageNr] = array(
                        'Elements' => new DataObjectSet($pageProducts),
                        'IsFirst'    => $isFirst
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
                'Elements' => new DataObjectSet($pageProducts),
                'IsFirst'  => $isFirst
            );
        }

        $this->elements = new DataObjectSet($pages);
        
        return $this->elements;
    }

    /**
     * Returns the elements for the static slider view.
     * 
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.02.2012
     */
    public function Elements() {
        if ($this->elements != null) {
            return $this->elements;
        }

        switch ($this->useSelectionMethod) {
            case 'products':
                $this->elements = $this->getElementsByProducts();
                break;
            case 'productGroup':
            default:
                $this->elements = $this->getElementsByProductGroup();
                break;
        }

        return $this->elements;
    }
    
    /**
     * Creates the cache key for this widget.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>, Sascha Koehler <skoehler@pixeltricks.de>
     * @since 02.07.2012
     */
    public function WidgetCacheKey() {
        $key = SilvercartWidgetTools::ProductWidgetCacheKey($this);
        return $key;
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
     * Returns the manually chosen products.
     * 
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.02.2012
     */
    public function getElementsByProducts() {
        $products = $this->SilvercartProducts();

        return $products;
    }
    
    /**
     * Returns a number of products from the chosen productgroup.
     * 
     * @return DataObjectSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function getElementsByProductGroup() {
        $elements = new DataObjectSet();

        if (!$this->SilvercartProductGroupPageID) {
            return $elements;
        }
        
        if (!$this->numberOfProductsToShow) {
            $this->numberOfProductsToShow = SilvercartProductGroupItemsWidget::$defaults['numberOfProductsToShow'];
        }

        $productgroupPage = DataObject::get_by_id(
            'SilvercartProductGroupPage',
            $this->SilvercartProductGroupPageID
        );

        if (!$productgroupPage) {
            return false;
        }
        $productgroupPageSiteTree = ModelAsController::controller_for($productgroupPage);
        
        switch ($this->fetchMethod) {
            case 'sortOrderAsc':
                $elements = $productgroupPageSiteTree->getProducts($this->numberOfProductsToShow, 'CASE WHEN SPGMSO.SortOrder THEN CONCAT(SPGMSO.SortOrder, SilvercartProduct.SortOrder) ELSE SilvercartProduct.SortOrder END ASC');
                break;
            case 'sortOrderDesc':
                $elements = $productgroupPageSiteTree->getProducts($this->numberOfProductsToShow, 'CASE WHEN SPGMSO.SortOrder THEN CONCAT(SPGMSO.SortOrder, SilvercartProduct.SortOrder) ELSE SilvercartProduct.SortOrder END DESC');
                break;
            case 'random':
            default:
                $elements = $productgroupPageSiteTree->getProducts($this->numberOfProductsToShow);
        } 
        
        return $elements;
    }
    
    /**
     * Returns the title of the product group that items are shown.
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.04.2011
     */
    public function ProductGroupTitle() {
        $title = '';
        
        if (!$this->SilvercartProductGroupPageID) {
            return $title;
        }
        
        $productgroupPage = DataObject::get_by_id(
            'SilvercartProductGroupPage',
            $this->SilvercartProductGroupPageID
        );
        
        if (!$productgroupPage) {
            return $title;
        }
        
        $title = $productgroupPage->MenuTitle;
        
        return $title;
    }
}
