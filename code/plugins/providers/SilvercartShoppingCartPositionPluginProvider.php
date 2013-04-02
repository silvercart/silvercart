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
 * @subpackage Plugins
 */

/**
 * Plugin-Provider for the SilvercartShoppingCartPosition object.
 *
 * @package Silvercart
 * @subpackage Plugins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 23.11.2011
 * @license see license file in modules root directory
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartShoppingCartPositionPluginProvider extends SilvercartPlugin {
    
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
     * This method will replace SilvercartShoppingCartPosition's method "getPrice".
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
     * This method will replace SilvercartShoppingCartPosition's method 
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
     * This method will replace SilvercartShoppingCartPosition's method "getTitle".
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
     * This method will replace SilvercartShoppingCartPosition's method "getProductNumberShop".
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
     * This method will replace SilvercartShoppingCartPosition's method "getTitle".
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
     * This method will replace SilvercartShoppingCartPosition's method "getTitle".
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
