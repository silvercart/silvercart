<?php

namespace SilverCart\Model\Plugins\Providers;

use SilverCart\Model\Plugins\Plugin;

/**
 * Plugin-Provider for the ShoppingCartPosition object.
 *
 * @package SilverCart
 * @subpackage Model_Plugins_Providers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ShoppingCartPositionPluginProvider extends Plugin {
    
    /**
     * Initialisation for plugin providers.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.11.2011
     */
    public function init(&$arguments, &$callingObject) {
        $result = $this->extend('pluginInit', $arguments, $callingObject);
        
        return $this->returnExtensionResultAsString($result);
    }
    
    /**
     * This method will replace ShoppingCartPosition's method "getPrice".
     * In order to not execute the original "addProduct" method you have to
     * return something other than boolean false in your plugin method.
     *
     * @param array &$arguments     The arguments to pass:
     *                              $arguments[0] = $forSingleProduct
     * @param mixed &$callingObject The calling object
     * 
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.06.2012
     */
    public function overwriteGetPrice(&$arguments, &$callingObject) {
        $result = $this->extend('pluginOverwriteGetPrice', $arguments, $callingObject);
        
        if (is_array($result)) {
            if (count($result) > 0) {
                return $this->returnFirstNotNull($result);
            } else {
                return false;
            }
        }
        
        return $result;
    }
    
    /**
     * This method will replace ShoppingCartPosition's method 
     * "isQuantityIncrementableBy".
     *
     * @param array &$arguments     The arguments to pass:
     *                              $arguments[0] = $forSingleProduct
     * @param mixed &$callingObject The calling object
     * 
     * @return mixed
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.06.2012
     */
    public function overwriteIsQuantityIncrementableBy(&$arguments, &$callingObject) {
        $quantity   = $arguments[0];
        $result     = $this->extend('pluginOverwriteIsQuantityIncrementableBy', $quantity, $callingObject);

        if (is_array($result) &&
            count($result) > 0) {
            $result = $result[0];
        } else {
            $result = null;
        }
        
        return $result;
    }

    /**
     * This method will replace ShoppingCartPosition's method "getTitle".
     * In order to not execute the original "addProduct" method you have to
     * return something other than an empty string in your plugin method.
     *
     * @param array &$arguments     The arguments to pass:
     *                              $arguments[0] = $forSingleProduct
     * @param mixed &$callingObject The calling object
     * 
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.04.2012
     */
    public function overwriteGetTitle(&$arguments, &$callingObject) {
        $result = $this->extend('pluginOverwriteGetTitle', $arguments, $callingObject);
        return $this->returnExtensionResultAsHtmlString($result);
    }

    /**
     * This method will replace ShoppingCartPosition's method "getProductNumberShop".
     * In order to not execute the original "addProduct" method you have to
     * return something other than an empty string in your plugin method.
     *
     * @param array &$arguments     The arguments to pass:
     *                              $arguments[0] = $forSingleProduct
     * @param mixed &$callingObject The calling object
     *
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.12.2012
     */
    public function overwriteGetProductNumberShop(&$arguments, &$callingObject) {
        $result = $this->extend('pluginOverwriteGetProductNumberShop', $arguments, $callingObject);
        return $this->returnExtensionResultAsHtmlString($result);
    }

    /**
     * This method will replace ShoppingCartPosition's method "getTitle".
     * In order to not execute the original "addProduct" method you have to
     * return something other than an empty string in your plugin method.
     *
     * @param array &$arguments     The arguments to pass:
     *                              $arguments[0] = $forSingleProduct
     * @param mixed &$callingObject The calling object
     * 
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.04.2012
     */
    public function addToTitle(&$arguments, &$callingObject) {
        $result = $this->extend('pluginAddToTitle', $callingObject);
        return $this->returnExtensionResultAsHtmlString($result);
    }

    /**
     * This method will replace ShoppingCartPosition's method "getTitle".
     * In order to not execute the original "addProduct" method you have to
     * return something other than an empty string in your plugin method.
     *
     * @param array &$arguments     The arguments to pass:
     *                              $arguments[0] = $forSingleProduct
     * @param mixed &$callingObject The calling object
     * 
     * @return mixed
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.07.2012
     */
    public function addToTitleForWidget(&$arguments, &$callingObject) {
        $result = $this->extend('pluginAddToTitleForWidget', $callingObject);
        return $this->returnExtensionResultAsHtmlString($result);
    }
    
}
