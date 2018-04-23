<?php

namespace SilverCart\Model\Plugins\Providers;

use SilverCart\Model\Plugins\Plugin;

/**
 * Plugin-Provider for the IncrementPositionQuantityForm object.
 *
 * @package SilverCart
 * @subpackage Model_Plugins_Providers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class IncrementPositionQuantityFormPluginProvider extends Plugin {

    /**
     * Initialisation for plugin providers.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return sring
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.11.2011
     */
    public function init(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginInit', $arguments, $callingObject);
        
        return $this->returnExtensionResultAsString($result);
    }
    
    /**
     * Replaces the submitSucess method.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = $data     contains the frameworks form data
     *                              $arguments[1] = $form     not used
     *                              $arguments[2] = $formData contains the modules form data
     * 
     * @param mixed &$callingObject The calling object
     * 
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.11.2011
     */
    public function overwriteSubmitSuccess(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginOverwriteSubmitSuccess', $arguments, $callingObject);
        
        return $result;
    }
}
