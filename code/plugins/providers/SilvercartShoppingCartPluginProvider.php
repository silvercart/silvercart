<?php
/**
 * Copyright 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Plugins
 */

/**
 * Plugin-Provider for the SilvercartShoppingCart object.
 *
 * @package Silvercart
 * @subpackage Plugins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 16.11.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartShoppingCartPluginProvider extends SilvercartPlugin {
    
    /**
     * Initialisation for plugin providers.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.11.2011
     */
    public function init(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginInit', $arguments, $callingObject);
        
        return $this->returnExtensionResultAsString($result);
    }
    
    /**
     * This method will replace SilvercartShoppingCart's method "addProduct".
     * In order to not execute the original "addProduct" method you have to
     * return boolean true in your plugin method.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.11.2011
     */
    public function overwriteAddProduct(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginOverwriteAddProduct', $arguments, $callingObject);

        if (is_array($result)) {
            if (count($result) > 0) {
                return $result[0];
            } else {
                return false;
            }
        }
        
        return $result;
    }
    
    /**
     * This method will replace SilvercartShoppingCart's method "removeProduct".
     * In order to not execute the original "removeProduct" method you have to
     * return boolean true in your plugin method.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.03.2013
     */
    public function overwriteRemoveProduct(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginOverwriteRemoveProduct', $arguments, $callingObject);

        if (is_array($result)) {
            if (count($result) > 0) {
                return $result[0];
            } else {
                return false;
            }
        }
        
        return $result;
    }

    /**
     * This method will return a DataObject with additional table row data to 
     * extend the editable shopping carts table (in template)
     *
     * @param array &$arguments     The arguments to pass:
     *                              $arguments[0] = $forSingleProduct
     * @param mixed &$callingObject The calling object
     * 
     * @return DataObjectSet
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.09.2012
     */
    public function addToEditableShoppingCartTable(&$arguments, &$callingObject) {
        $result = $this->extend('pluginAddToEditableShoppingCartTable', $callingObject);
        return $this->returnExtensionResultAsDataObjectSet($result);
    }
    
}