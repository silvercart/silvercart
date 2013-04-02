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
 * Plugin-Provider for the SilvercartShoppingCartPosition object.
 *
 * @package Silvercart
 * @subpackage Plugins
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.06.2012
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartOrderPositionPluginProvider extends SilvercartPlugin {

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
     * @author Sebastian Diel <sdiel@üixeltricks.de>
     * @since 26.06.2012
     */
    public function addToTitle(&$arguments, &$callingObject) {
        $result = $this->extend('pluginAddToTitle', $callingObject);
        return $this->returnExtensionResultAsHtmlString($result, '<br/>');
    }

    /**
     * This method is called after the convertShoppingCartPositionsToOrderPositions
     * method is done and before order notification emails get sent.
     *
     * @param array &$arguments     The arguments to pass:
     *                              $arguments[0] = $forSingleProduct
     * @param mixed &$callingObject The calling object
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.11.2012
     */
    public function convertShoppingCartPositionsToOrderPositions(&$arguments, &$callingObject) {
        $result = $this->extend('pluginConvertShoppingCartPositionsToOrderPositions', $callingObject, $arguments[0]);
        return $this->returnExtensionResultAsHtmlString($result, '<br/>');
    }
}
