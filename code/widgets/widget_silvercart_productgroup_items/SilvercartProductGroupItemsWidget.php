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
class SilvercartProductGroupItemsWidget extends SilvercartWidget {
    
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
        'SilvercartProductGroupPageID'  => 'Int',
        'useListView'                   => 'Boolean',
        'isContentView'                 => 'Boolean'
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
        'numberOfProductsToShow' => 5
    );
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        $productGroupField = new SilvercartGroupedDropdownField(
            'SilvercartProductGroupPageID',
            _t('SilvercartProductGroupItemsWidget.STOREADMIN_FIELDLABEL'),
            SilvercartProductGroupHolder_Controller::getRecursiveProductGroupsForGroupedDropdownAsArray(null, true),
            $this->SilvercartProductGroupPageID
        );
        $numberOfProductsField  = new TextField('numberOfProductsToShow', _t('SilvercartProductGroupItemsWidget.STOREADMIN_NUMBEROFPRODUCTS'));
        $useListViewField       = new CheckboxField('useListView', _t('SilvercartProductGroupItemsWidget.USE_LISTVIEW'));
        $isContentView          = new CheckboxField('isContentView', _t('SilvercartProductGroupItemsWidget.IS_CONTENT_VIEW'));
        
        $fields->push($productGroupField);
        $fields->push($numberOfProductsField);
        $fields->push($useListViewField);
        $fields->push($isContentView);
        
        return $fields;
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
     * We set checkbox field values here to false if they are not in the post
     * data array.
     *
     * @return void
     *
     * @param array $data The post data array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.08.2011
     */
    function populateFromPostData($data) {
        if (!array_key_exists('isContentView', $data)) {
            $this->isContentView = 0;
        }
        if (!array_key_exists('useListView', $data)) {
            $this->useListView = 0;
        }
        
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
class SilvercartProductGroupItemsWidget_Controller extends SilvercartWidget_Controller {

    protected $elements = null;
    
    /**
     * Register forms for the contained products.
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.07.2011
     */
    public function init() {
        $this->elements = $this->ProductPages();
        
        if ($this->elements) {
            $controller = Controller::curr();
            $elementIdx = 0;

            if ($this->useListView) {
                $productAddCartFormName = 'SilvercartProductAddCartFormList';
            } else {
                $productAddCartFormName = 'SilvercartProductAddCartFormTile';
            }

            foreach ($this->elements as $element) {
                $formIdentifier = 'ProductAddCartForm'.$this->ID.'_'.$elementIdx;
                
                $controller->registerCustomHtmlForm(
                    $formIdentifier,
                    new $productAddCartFormName(
                        $controller,
                        array('productID' => $element->ID)
                    )
                );
                
                $element->productAddCartForm = $controller->InsertCustomHtmlForm(
                    $formIdentifier,
                    array(
                        $element
                    )
                );
                $elementIdx++;
            }
        }
        
        Requirements::customScript(
            sprintf('
            $(document).ready(function() {
                $("#SilvercartProductGroupItemsWidgetSlider%d").anythingSlider({
                    \'easing\':     \'easeInOutBack\'
                });
            });
            ',
            $this->ID)
        );
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
        if ($this->elements) {
            return $this->elements;
        }
        
        if (!$this->SilvercartProductGroupPageID) {
            return false;
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
        $productgroupPageSiteTree                  = ModelAsController::controller_for($productgroupPage);
        $products                                  = $productgroupPageSiteTree->getProducts(999);
        
        $pages          = array();
        $pageProducts   = array();
        $pageNr         = 0;
        $PageProductIdx = 1;
        
        foreach ($products as $product) {
            $pageProducts['Element'.$PageProductIdx] = $product;
            $PageProductIdx++;

            if ($PageProductIdx > $this->numberOfProductsToShow) {
                $pages['Page'.$pageNr] = array('Elements' => new DataObjectSet($pageProducts));
                $PageProductIdx = 1;
                $pageProducts   = array();
                $pageNr++;
            }
        }
        
        if (!array_key_exists('Page'.$pageNr, $pages)) {
            $pages['Page'.$pageNr] = array('Elements' => new DataObjectSet($pageProducts));
        }
        
        $this->elements = new DataObjectSet($pages);
        
        return $this->elements;
    }
    
    /**
     * Returns the title of the product group that items are shown.
     *
     * @return string
     *
     * @param 
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