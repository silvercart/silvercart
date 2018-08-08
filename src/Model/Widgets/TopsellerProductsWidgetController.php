<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Dev\Tools;
use SilverCart\Model\Order\OrderPosition;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Widgets\TopsellerProductsWidget;
use SilverCart\Model\Widgets\WidgetController;
use SilverCart\Model\Widgets\WidgetTools;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\Queries\SQLSelect;

/**
 * TopsellerProductsWidget Controller.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class TopsellerProductsWidgetController extends WidgetController {
    
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
            $defaults = $this->getWidget()->config()->get('defaults');
            $this->numberOfProductsToShow = $defaults['numberOfProductsToShow'];
        }
        
        $orderPositionTable = Tools::get_table_name(OrderPosition::class);
        $productTable       = Tools::get_table_name(Product::class);
        
        $products   = array();
        $sqlSelect  = new SQLSelect();

        $sqlSelect->selectField('SOP.ProductID');
        $sqlSelect->selectField('SUM(SOP.Quantity) AS OrderedQuantity');
        $sqlSelect->addFrom($orderPositionTable . ' SOP');
        $sqlSelect->addLeftJoin($productTable, 'SP.ID = SOP.ProductID', 'SP');
        $sqlSelect->addWhere('SP.isActive = 1');
        $sqlSelect->addGroupBy('SOP.ProductID');
        $sqlSelect->addOrderBy('OrderedQuantity', 'DESC');
        $sqlSelect->setLimit($this->numberOfProductsToShow);

        $sqlResult = $sqlSelect->execute();

        foreach ($sqlResult as $row) {
            $product = Product::get()->byID($row['ProductID']);
            if ($product instanceof Product) {
                $products[] = $product;
            }
        }

        $result = new ArrayList($products);

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
        $key = WidgetTools::ProductWidgetCacheKey($this);
        return $key;
    }
}