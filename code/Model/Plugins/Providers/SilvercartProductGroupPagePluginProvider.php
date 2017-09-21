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
 * Plugin-Provider for the the SilvercartProductGroupPage_Controller object.
 *
 * @package Silvercart
 * @subpackage Plugins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 09.01.2012
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartProductGroupPage_ControllerPluginProvider extends SilvercartPlugin {

    /**
     * Initialisation for plugin providers.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.01.2012
     */
    public function init(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginInit', $arguments);
        
        return $this->returnExtensionResultAsString($result);
    }
    
    /**
     * This method gets called after a product has been added to the
     * SilvercartShoppingCart. The arguments contain the
     * SilvercartShoppingCartPosition that was created.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = number of products to return
     *                              $arguments[1] = products per page
     *                              $arguments[2] = offset
     *                              $arguments[3] = SQL sort statement
     * @param mixed &$callingObject The calling object
     * 
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.01.2012
     */
    public function overwriteGetProducts(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginOverwriteGetProducts', $arguments, $callingObject);
        
        if (is_array($result) &&
            !empty($result)) {
            $result = $result[0];
        }

        return $result;
    }
}

