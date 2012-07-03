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
 * Provides the a view of the topseller products.
 * 
 * You can define the number of products to be shown.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 26.05.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartTopsellerProductsWidget extends SilvercartWidget {
    
    /**
     * Indicates the number of products that shall be shown with this widget.
     * 
     * @var int
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public static $db = array(
        'numberOfProductsToShow' => 'Int'
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
        
        $numberOfProductsField = new TextField('numberOfProductsToShow', _t('SilvercartTopsellerProductsWidget.STOREADMIN_FIELDLABEL'));
        
        $fields->push($numberOfProductsField);
        
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
        return _t('SilvercartTopsellerProductsWidget.TITLE');
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
        return _t('SilvercartTopsellerProductsWidget.CMSTITLE');
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
        return _t('SilvercartTopsellerProductsWidget.DESCRIPTION');
    }
}

/**
 * Provides the a view of the topseller products.
 * 
 * You can define the number of products to be shown.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 26.05.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartTopsellerProductsWidget_Controller extends SilvercartWidget_Controller {
    
    /**
     * Returns a number of topseller products.
     * 
     * @return DataObjectSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Elements() {
        
        if (!$this->numberOfProductsToShow) {
            $this->numberOfProductsToShow = SilvercartTopsellerProductsWidget::$defaults['numberOfProductsToShow'];
        }
        
        $cachekey = 'TopsellerProducts'.$this->numberOfProductsToShow;
        $cache    = SS_Cache::factory($cachekey);
        $result   = $cache->load($cachekey);

        if ($result) {
            $result = unserialize($result);
        } else {
            $products   = array();
            $sqlQuery   = new SQLQuery();

            $sqlQuery->select = array(
                'SOP.SilvercartProductID',
                'SUM(SOP.Quantity) AS Quantity'
            );
            $sqlQuery->from = array(
                'SilvercartOrderPosition SOP',
                'LEFT JOIN SilvercartProduct SP on SP.ID = SOP.SilvercartProductID'
            );
            $sqlQuery->where = array(
                'SP.isActive = 1'
            );
            $sqlQuery->groupby = array(
                'SOP.SilvercartProductID'
            );
            $sqlQuery->orderby  = 'Quantity DESC';
            $sqlQuery->limit    = $this->numberOfProductsToShow;

            $result = $sqlQuery->execute();

            foreach ($result as $row) {
                $products[] = DataObject::get_by_id(
                    'SilvercartProduct',
                    $row['SilvercartProductID']
                );
            }
            
            $result = new DataObjectSet($products);
        }

        return $result;
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
        $key           = i18n::get_locale().'_';
        $productMap    = $this->Elements()->map('ID', 'LastEdited');
        $productMapIDs = implode('_', array_flip($productMap));
        sort($productMap);
        $productMapLastEdited = array_pop($productMap);
        
        $key .= $productMapIDs.'_'.$productMapLastEdited.'_'.$this->LastEdited;
        
        return $key;
    }
}