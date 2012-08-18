<?php
/**
 * Copyright 2012 pixeltricks GmbH
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
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.06.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
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
    
}
