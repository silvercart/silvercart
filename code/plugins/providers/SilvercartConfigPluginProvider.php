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
 * Plugin-Provider for the SilvercartConfig object.
 * 
 * @package Silvercart
 * @subpackage Plugins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 30.01.2012
 * @license see license file in modules root directory
 */
class SilvercartConfigPluginProvider extends SilvercartPlugin {

    /**
     * Overwrites the Pricetype method
     * 
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.01.2012
     */
    public function overwritePricetype(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginOverwritePricetype', $arguments, $callingObject);

        return $this->returnExtensionResultAsString($result);
    }
}
