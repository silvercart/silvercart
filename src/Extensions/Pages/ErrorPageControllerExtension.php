<?php

namespace SilverCart\Extensions\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Product\Product;
use SilverStripe\Core\Extension;

/**
 * Extension for ErrorPageController.
 * Checks the invalid URL for product ID hints. If a product ID was found, the
 * customer will be redirected to the product detail page.
 * 
 * @package SilverCart
 * @subpackage Extensions_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 19.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ErrorPageControllerExtension extends Extension
{
    /**
     * Checks if the requested URL contains a product ID as the second last part.
     * If there is a match, a redirect to the product detail page will be forced.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.09.2018
     */
    public function onAfterInit()
    {
        if ($this->owner->data()->ErrorCode === "404"
         && array_key_exists('REQUEST_URI', $_SERVER)
        ) {
            $url = $_SERVER['REQUEST_URI'];
            if (strpos($url, "?") !== false) {
                $url = substr($url, 0, strpos($url, "?"));
            }
            $urlParts = explode('/', $url);
            array_pop($urlParts);
            $productID = array_pop($urlParts);
            if (is_numeric($productID)) {
                $product = Product::get()->byID((int) $productID);
                if ($product instanceof Product
                 && $product->exists()
                ) {
                    Tools::redirectPermanentlyTo($product->Link());
                }
            }
        }
    }
}