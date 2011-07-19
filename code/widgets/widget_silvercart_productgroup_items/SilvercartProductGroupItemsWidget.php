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
        'isContentView'                  => 'Boolean'
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
        $useListViewField       = new TextField('useListView', _t('SilvercartProductGroupItemsWidget.USE_LISTVIEW'));
        $isContentView          = new TextField('isContentView', _t('SilvercartProductGroupItemsWidget.IS_CONTENT_VIEW'));
        
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

    /**
     * Returns a number of products from the chosen productgroup.
     * 
     * @return DataObjectSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Elements() {
        
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
        $productgroupPageSiteTree->productsPerPage = $this->numberOfProductsToShow;
        $products                                  = $productgroupPageSiteTree->getProducts();
        
        return $products;
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