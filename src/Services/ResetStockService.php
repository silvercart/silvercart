<?php

namespace SilverCart\Services;

use SilverCart\Model\Product\Product;

/**
 * Provides a service to reset the stock of SilverCart products from the actually
 * assigned value to the dynamically calculated value by related StockItemEntries.
 * 
 * @package SilverCart
 * @subpackage Services
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2023 pixeltricks GmbH
 * @since 13.06.2023
 * @license see license file in modules root directory
 */
class ResetStockService extends Service
{
    /**
     * Runs this task.
     * 
     * @return void
     */
    public function run() : void
    {
        $this->addMessage("Checking product stock...");
        $products     = Product::get();
        $productCount = $products->count();
        $currentIndex = 0;
        $this->addMessage(" • checking {$productCount} products...");
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
                $this->addProgressMessage($baseLogString);
                $this->addMessage("{$baseLogString}: updating stock from {$stock} to {$stockByEntries}.");
                $product->StockQuantity = $stockByEntries;
                $product->setUpdateStockQuantity(true);
                $product->write();
            }
        }
    }
}