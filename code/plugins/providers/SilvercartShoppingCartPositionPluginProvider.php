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
 * Plugin-Provider for the SilvercartShoppingCartPosition object.
 *
 * @package Silvercart
 * @subpackage Plugins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 23.11.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartShoppingCartPositionPluginProvider extends SilvercartPlugin {
    
    /**
     * Initialisation for plugin providers.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.11.2011
     */
    public function init(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginInit', $arguments, $callingObject);
        
        return $this->returnExtensionResultAsString($result);
    }
    
    /**
     * This method will replace SilvercartShoppingCartPosition's method "getPrice".
     * In order to not execute the original "addProduct" method you have to
     * return something other than boolean false in your plugin method.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.11.2011
     */
    public function overwriteGetPrice(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginOverwriteGetPrice', $arguments, $callingObject);

        if (is_array($result)) {
            if (count($result) > 0) {
                return $result[0];
            } else {
                return false;
            }
        }
        
        return $result;
    }
    
}