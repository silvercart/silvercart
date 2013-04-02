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
 * Plugin-Provider for the SilvercartProductAddCartFormDetail object.
 *
 * @package Silvercart
 * @subpackage Plugins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 16.11.2011
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartProductAddCartFormListPluginProvider extends SilvercartProductAddCartFormPluginProvider {

    /**
     * Use this method to insert additional fields into the
     * SilvercartProductAddCartFormDetail form.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.11.2011
     */
    public function AddCartFormListAdditionalFields(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginAddCartFormListAdditionalFields', $arguments, $callingObject);
        
        if (is_array($result)) {
            if (count($result) > 0) {
                return $result[0];
            } else {
                return false;
            }
        } else {
            return $result;
        }
    }
}
