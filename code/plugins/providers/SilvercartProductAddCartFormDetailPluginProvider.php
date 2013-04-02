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
class SilvercartProductAddCartFormDetailPluginProvider extends SilvercartProductAddCartFormPluginProvider {

    /**
     * Use this method to insert additional fields into the
     * SilvercartProductAddCartFormDetail form.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.09.2012
     */
    public function AddCartFormDetailAdditionalFields(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginAddCartFormDetailAdditionalFields', $arguments, $callingObject);
        return $this->returnExtensionResultAsHtmlString($result);
    }
}
