<?php

namespace SilverCart\Dev\Tasks;

use SilverCart\Model\Product\Product;
use SilverStripe\Control\Controller;

/**
 * Task to prime a SilverCart based sites cache.
 *
 * @package SilverCart
 * @subpackage Dev_Tasks
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 13.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class AssignProductKeywordsTask extends Controller
{
    use \SilverCart\Dev\CLITask;
    
    public function index()
    {
        $ccg = self::$CLI_COLOR_CHANGE_GREEN;
        $this->printInfo("Assigning product keywords...");
        $products     = Product::get();
        $productCount = $products->count();
        $currentIndex = 0;
        $this->printInfo(" • checking {$productCount} products...");
        foreach ($products as $product) {
            /* @var $product Product */
            $currentIndex++;
            $baseLogString = "{$this->getXofY($currentIndex, $productCount)} - checking SKU#{$product->ProductNumberShop} [#{$product->ID}]";
            $this->printProgressInfo($baseLogString);
            $assigned = $product->assignKeywords();
            if ($assigned) {
                $this->printInfo("{$baseLogString}: {$ccg}assigned.");
                $product->write();
            } else {
                $this->printProgressInfo("{$baseLogString}: skipped.");
            }
        }
        $this->printInfo("");
        $this->printInfo("done.", self::$CLI_COLOR_GREEN);
    }
}