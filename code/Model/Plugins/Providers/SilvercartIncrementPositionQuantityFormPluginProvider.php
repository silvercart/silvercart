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
 * Plugin-Provider for the SilvercartIncrementPositionQuantityForm object.
 *
 * @package Silvercart
 * @subpackage Plugins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 23.11.2011
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartIncrementPositionQuantityFormPluginProvider extends SilvercartPlugin {

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
