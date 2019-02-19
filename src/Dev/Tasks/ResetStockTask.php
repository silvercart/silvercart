<?php

namespace SilverCart\Dev\Tasks;

use SilverCart\Model\Product\Product;
use SilverStripe\Control\Controller;

/**
 * Task to reset the stock of SilverCart products from the actually assigned value
 * to the dynamically calculated value by related StockItemEntries.
 *
 * @package SilverCart
 * @subpackage Dev\Tasks
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 19.02.2019
 * @copyright 2019 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ResetStockTask extends Controller
{
    use \SilverCart\Dev\CLITask;
    
    /**
     * Initializes the CLI arguments.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.02.2019
     */
    protected function init() : void
    {
        parent::init();
        $this->initArgs();
    }
    
    /**
     * Executes the task.
     * Compares the value of Product::$StockQuantity with the calculated stock
     * quantity based on a product's related StockItemEntries.
     * If the calculated value differs from the assigned value, the assigned value
     * will be reset.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.02.2019
     */
    public function index() : void
    {
        $ccg = self::$CLI_COLOR_CHANGE_GREEN;
        $this->printInfo("Checking product stock...");
        $products     = Product::get();
        $productCount = $products->count();
        $currentIndex = 0;
        $this->printInfo(" • checking {$productCount} products...");
        foreach ($products as $product) {
            /* @var $product Product */
            $currentIndex++;
            $baseLogString  = "{$this->getXofY($currentIndex, $productCount)} - checking SKU#{$product->ProductNumberShop} [#{$product->ID}]";
            $stockByEntries = $product->getStockQuantityByItemEntries();
            $stock          = $product->StockQuantity;
            $skip           = false;
            $this->extend('onBeforeResetStockQuantityForProduct', $product, $skip, $baseLogString);
            if ($skip) {
                continue;
            }
            if ($stockByEntries != $stock) {
                $this->printProgressInfo($baseLogString);
                $this->printInfo("{$baseLogString}: {$ccg} updating stock from {$stock} to {$stockByEntries}.");
                $product->StockQuantity = $stockByEntries;
                $product->setUpdateStockQuantity(true);
                $product->write();
            }
        }
        $this->printInfo("");
        $this->printInfo("done.", self::$CLI_COLOR_GREEN);
    }
}