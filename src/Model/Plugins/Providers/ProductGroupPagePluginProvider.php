<?php

namespace SilverCart\Model\Plugins\Providers;

use SilverCart\Model\Plugins\Plugin;

/**
 * Plugin-Provider for the the ProductGroupPageController object.
 *
 * @package SilverCart
 * @subpackage Model_Plugins_Providers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductGroupPageControllerPluginProvider extends Plugin {

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
     * ShoppingCart. The arguments contain the
     * ShoppingCartPosition that was created.
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

