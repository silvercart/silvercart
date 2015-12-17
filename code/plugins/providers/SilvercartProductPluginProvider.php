<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Plugins
 */

/**
 * Plugin-Provider for the the SilvercartProduct object.
 *
 * @package Silvercart
 * @subpackage Plugins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 17.11.2011
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartProductPluginProvider extends SilvercartPlugin {

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
     * SilvercartShoppingCart. The arguments contain the
     * SilvercartShoppingCartPosition that was created.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = SilvercartShoppingCartPosition
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

/**
 * Plugin-Provider for the the SilvercartProduct_CollectionController object.
 *
 * @package Silvercart
 * @subpackage Plugins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 28.11.2011
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartProduct_CollectionControllerPluginProvider extends SilvercartPlugin {
    
    /**
     * Overwrites the findProductsbyNumbers method.
     *
     * @param array &$arguments     The arguments to pass
     *                              [0]: string $numbers  The number to search for
     *                              [1]: $mapNames
     * @param mixed &$callingObject The calling object
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.11.2011
     */
    public function overwriteFindProductsByNumbers(&$arguments, &$callingObject) {
        $result = $this->extend('pluginOverwriteFindProductsByNumbers', $arguments);
        
        if (is_array($result) &&
            count($result) > 0) {
            
            $result = $result[0];
        }
        
        return $result;
    }
    
}