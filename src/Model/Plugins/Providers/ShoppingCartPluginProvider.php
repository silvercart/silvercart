<?php

namespace SilverCart\Model\Plugins\Providers;

use SilverCart\Model\Plugins\Plugin;

/**
 * Plugin-Provider for the ShoppingCart object.
 * 
 * @package SilverCart
 * @subpackage Model_Plugins_Providers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ShoppingCartPluginProvider extends Plugin {
    
    /**
     * Initialisation for plugin providers.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.11.2011
     */
    public function init(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginInit', $arguments, $callingObject);
        
        return $this->returnExtensionResultAsString($result);
    }
    
    /**
     * This method will replace ShoppingCart's method "addProduct".
     * In order to not execute the original "addProduct" method you have to
     * return boolean true in your plugin method.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.11.2011
     */
    public function overwriteAddProduct(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginOverwriteAddProduct', $arguments, $callingObject);

        if (is_array($result)) {
            if (count($result) > 0) {
                return $result[0];
            } else {
                return false;
            }
        }
        
        return $result;
    }
    
    /**
     * This method will replace ShoppingCart's method "removeProduct".
     * In order to not execute the original "removeProduct" method you have to
     * return boolean true in your plugin method.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.03.2013
     */
    public function overwriteRemoveProduct(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginOverwriteRemoveProduct', $arguments, $callingObject);

        if (is_array($result)) {
            if (count($result) > 0) {
                return $result[0];
            } else {
                return false;
            }
        }
        
        return $result;
    }

    /**
     * This method will return a DataObject with additional table row data to 
     * extend the editable shopping carts table (in template)
     *
     * @param array &$arguments     The arguments to pass:
     *                              $arguments[0] = $forSingleProduct
     * @param mixed &$callingObject The calling object
     * 
     * @return ArrayList
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.09.2012
     */
    public function addToEditableShoppingCartTable(&$arguments, &$callingObject) {
        $result = $this->extend('pluginAddToEditableShoppingCartTable', $callingObject);
        return $this->returnExtensionResultAsArrayList($result);
    }
    
}