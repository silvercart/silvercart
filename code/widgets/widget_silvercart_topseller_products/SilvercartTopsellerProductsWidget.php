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
 * Provides the a view of the topseller products.
 * 
 * You can define the number of products to be shown.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 26.05.2011
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartTopsellerProductsWidget extends SilvercartWidget {
    
    /**
     * Indicates the number of products that shall be shown with this widget.
     * 
     * @var int
     */
    public static $db = array(
        'numberOfProductsToShow' => 'Int'
    );
    
    /**
     * Set default values.
     * 
     * @var array
     */
    public static $defaults = array(
        'numberOfProductsToShow' => 5
    );
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this);
        
        return $fields;
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'numberOfProductsToShow' => _t('SilvercartTopsellerProductsWidget.STOREADMIN_FIELDLABEL')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
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
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartTopsellerProductsWidget_Controller extends SilvercartWidget_Controller {
    
    /**
     * Returns a number of topseller products.
     * 
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.02.2015
     */
    public function Elements() {
        
        if (!$this->numberOfProductsToShow) {
            $this->numberOfProductsToShow = SilvercartTopsellerProductsWidget::$defaults['numberOfProductsToShow'];
        }
        
        $cachekey     = 'TopsellerProducts' . $this->numberOfProductsToShow;
        $cache        = SS_Cache::factory($cachekey);
        $cachedResult = $cache->load($cachekey);

        if ($cachedResult) {
            $result = unserialize($result);
        } else {
            $products   = array();
            $sqlQuery   = new SQLQuery();

            $sqlQuery->selectField('SOP.SilvercartProductID');
            $sqlQuery->selectField('SUM(SOP.Quantity) AS OrderedQuantity');
            $sqlQuery->addFrom('SilvercartOrderPosition SOP');
            $sqlQuery->addLeftJoin('SilvercartProduct', 'SP.ID = SOP.SilvercartProductID', 'SP');
            $sqlQuery->addWhere('SP.isActive = 1');
            $sqlQuery->addGroupBy('SOP.SilvercartProductID');
            $sqlQuery->addOrderBy('OrderedQuantity', 'DESC');
            $sqlQuery->setLimit($this->numberOfProductsToShow);

            $sqlResult = $sqlQuery->execute();

            foreach ($sqlResult as $row) {
                $product = DataObject::get_by_id(
                    'SilvercartProduct',
                    $row['SilvercartProductID']
                );
                $product->addCartFormIdentifier = $this->ID.'_'.$product->ID;

                $products[] = $product;
            }
            
            $result = new ArrayList($products);
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
        $key = SilvercartWidgetTools::ProductWidgetCacheKey($this);
        return $key;
    }
}