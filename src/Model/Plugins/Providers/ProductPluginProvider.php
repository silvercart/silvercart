<?php

namespace SilverCart\Model\Plugins\Providers;

use SilverCart\Model\Plugins\Plugin;

/**
 * Plugin-Provider for the the Product object.
 *
 * @package SilverCart
 * @subpackage Model_Plugins_Providers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductPluginProvider extends Plugin {

    /**
     * Initialisation for plugin providers.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public function init(&$arguments, &$callingObject) {
        $result = $this->extend('pluginInit', $arguments);
        
        return $this->returnExtensionResultAsString($result);
    }
    
    /**
     * This method gets called after a product has been added to the
     * ShoppingCart. The arguments contain the
     * ShoppingCartPosition that was created.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = ShoppingCartPosition
     * @param mixed &$callingObject The calling object
     * 
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public function onAfterAddToCart(&$arguments, &$callingObject) {
        $result = $this->extend('pluginOnAfterAddToCart', $arguments, $callingObject);
        
        return $result;
    }
    
    /**
     * returns a ArrayList with all plugged in tabs 
     * 
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return ArrayList
     */
    public function getPluggedInTabs(&$arguments, &$callingObject) {
        $result = $this->extend('pluginGetPluggedInTabs', $callingObject);
        return $this->returnExtensionResultAsArrayList($result);
    }
    
    /**
     * returns a ArrayList with all plugged in meta data for a product 
     * 
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return ArrayList
     */
    public function getPluggedInProductMetaData(&$arguments, &$callingObject) {
        $result = $this->extend('pluginGetPluggedInProductMetaData', $callingObject);
        return $this->returnExtensionResultAsArrayList($result);
    }
    
    /**
     * returns a ArrayList with all plugged in additional data for a product 
     * 
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return ArrayList
     */
    public function getPluggedInProductListAdditionalData(&$arguments, &$callingObject) {
        $result = $this->extend('pluginGetPluggedInProductListAdditionalData', $callingObject);
        return $this->returnExtensionResultAsArrayList($result);
    }
    
    /**
     * Returns a DataObjectSet with all additional information to display 
     * between Images and Content.
     * 
     * @param array &$arguments    The arguments to pass
     * @param mixed $callingObject The calling object
     * 
     * @return DataObjectSet
     */
    public function getPluggedInAfterImageContent(&$arguments, $callingObject) {
        $result = $this->extend('pluginGetPluggedInAfterImageContent', $callingObject);
        return $this->returnExtensionResultAsDataObjectSet($result);
    }
}