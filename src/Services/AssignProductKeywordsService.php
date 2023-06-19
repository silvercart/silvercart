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
class AssignProductKeywordsService extends Service
{
    /**
     * Runs this task.
     * 
     * @return void
     */
    public function run() : void
    {
        $this->addMessage("Assigning product keywords...");
        $products     = Product::get();
        $productCount = $products->count();
        $currentIndex = 0;
        $this->addMessage(" • checking {$productCount} products...");
        foreach ($products as $product) {
            /* @var $product Product */
            $currentIndex++;
            $baseLogString = "{$this->getXofY($currentIndex, $productCount)} - checking SKU#{$product->ProductNumberShop} [#{$product->ID}]";
            $this->addProgressMessage($baseLogString);
            $assigned = $product->assignKeywords();
            if ($assigned) {
                $this->addMessage("{$baseLogString}: assigned.");
                $product->write();
            } else {
                $this->addProgressMessage("{$baseLogString}: skipped.");
            }
        }
        $this->addMessage("");
    }
}