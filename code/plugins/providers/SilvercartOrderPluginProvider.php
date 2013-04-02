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
 * Plugin-Provider for the order object.
 *
 * @package Silvercart
 * @subpackage Plugins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 22.09.2011
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartOrderPluginProvider extends SilvercartPlugin {

    /**
     * Initialisation for plugin providers.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return sring
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public function init(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginInit', $arguments, $callingObject);
        
        return $this->returnExtensionResultAsString($result);
    }
    
    /**
     * This method gets called after the order object has been created from the
     * shoppingcart object and before the order positions get created.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = SilvercartOrder
     *                              $arguments[1] = SilvercartShoppingCart
     * @param mixed &$callingObject The calling object
     * 
     * @return sring
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public function createFromShoppingCart(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginCreateFromShoppingCart', $arguments, $callingObject);
        
        return $this->returnExtensionResultAsString($result);
    }

    /**
     * This method gets called before the order object has been created from the
     * shoppingcart object.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = SilvercartShoppingCart
     * @param mixed &$callingObject The calling object
     *
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.11.2012
     */
    public function overwriteCreateFromShoppingCart(&$arguments = array(), &$callingObject) {
        $orderObj = false;

        $this->extend('pluginOverwriteCreateFromShoppingCart', $arguments[0], $orderObj, $callingObject);

        return $orderObj;
    }
    
    /**
     * This method gets called while the SilvercartShoppingCartPositions are
     * converted to SilvercartOrderPositions.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = SilvercartShoppingCartPosition
     *                              $arguments[1] = SilvercartOrderPosition
     * @param mixed &$callingObject The calling object
     * 
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 17.11.2011
     */
    public function convertShoppingCartPositionToOrderPosition(&$arguments = array(), &$callingObject) {
        $this->extend('pluginConvertShoppingCartPositionToOrderPosition', $arguments[0], $arguments[1], $callingObject);
        
        return $arguments[1];
    }
    
    /**
     * Use this method to return additional information on the order in the
     * section "My Account" for the customer.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public function OrderDetailInformation(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginOrderDetailInformation', $arguments, $callingObject);
        
        return $this->returnExtensionResultAsString($result);
    }

    /**
     * This method gets called after the IsPriceTypeGross method has been called
     * and can alter the result.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = SilvercartOrder
     *                              $arguments[1] = IsPriceTypeGross (the original result)
     * @param mixed &$callingObject The calling object
     * 
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 17.11.2011
     */
    public function IsPriceTypeGross(&$arguments = array(), &$callingObject) {
       $this->extend('pluginIsPriceTypeGross', $arguments[0], $callingObject);
        
        return $arguments[0];
    }

    /**
     * This method gets called after the IsPriceTypeNet method has been called
     * and can alter the result.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = SilvercartOrder
     *                              $arguments[1] = IsPriceTypeNet (the original result)
     * @param mixed &$callingObject The calling object
     * 
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 17.11.2011
     */
    public function IsPriceTypeNet(&$arguments = array(), &$callingObject) {
       $this->extend('pluginIsPriceTypeNet', $arguments[0], $callingObject);
        
        return $arguments[0];
    }
}
