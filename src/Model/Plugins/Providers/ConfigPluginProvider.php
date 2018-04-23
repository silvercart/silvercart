<?php

namespace SilverCart\Model\Plugins\Providers;

use SilverCart\Model\Plugins\Plugin;

/**
 * Plugin-Provider for the Config object.
 *
 * @package SilverCart
 * @subpackage Model_Plugins_Providers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ConfigPluginProvider extends Plugin {

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
