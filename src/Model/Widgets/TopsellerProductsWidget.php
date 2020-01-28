<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Dev\Tools;
use SilverCart\Model\{
    Order\OrderPosition,
    Product\Product,
    Widgets\Widget,
    Widgets\WidgetTools
};
use SilverStripe\ORM\ {
    ArrayList,
    FieldType\DBBoolean,
    FieldType\DBInt,
    Queries\SQLSelect
};

/**
 * Provides the a view of the topseller products.
 * 
 * You can define the number of products to be shown.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.08.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class TopsellerProductsWidget extends Widget
{
    /**
     * Indicates the number of products that shall be shown with this widget.
     * 
     * @var int
     */
    private static $db = [
        'numberOfProductsToShow' => DBInt::class,
        'UseAsSlider'            => DBBoolean::class,
    ];
    /**
     * Set default values.
     * 
     * @var array
     */
    private static $defaults = [
        'numberOfProductsToShow' => 5,
        'UseAsSlider'            => true,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartTopsellerProductsWidget';
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 22.08.2018
     */
    public function fieldLabels($includerelations = true)
    {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                [
                    'numberOfProductsToShow' => _t(TopsellerProductsWidget::class . '.STOREADMIN_FIELDLABEL', 'Number of products to show:'),
                    'UseAsSlider'            => _t(Widget::class . '.UseAsSlider', 'Use as a slider'),
                ]
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns a number of topseller products.
     * 
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.08.2018
     */
    public function Elements()
    {
        if (!$this->numberOfProductsToShow) {
            $defaults = $this->config()->get('defaults');
            $this->numberOfProductsToShow = $defaults['numberOfProductsToShow'];
        }
        
        $orderPositionTable = Tools::get_table_name(OrderPosition::class);
        $productTable       = Tools::get_table_name(Product::class);
        
        $products   = [];
        $sqlSelect  = SQLSelect::create('SOP.ProductID');
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

        $result = ArrayList::create($products);

        return $result;
    }
    
    /**
     * Creates the cache key for this widget.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.08.2018
     */
    public function WidgetCacheKey()
    {
        $key = WidgetTools::ProductWidgetCacheKey($this);
        return $key;
    }
}