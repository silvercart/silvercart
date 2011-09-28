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
 * Plugin-Provider for the order object.
 *
 * @package Silvercart
 * @subpacke Plugins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 22.09.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartOrderPluginProvider extends SilvercartPlugin {

    /**
     * Initialisation for plugin providers.
     *
     * @param array $arguments The arguments to pass
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public function init(&$arguments = array()) {
        $result = $this->extend('pluginInit', $arguments);
        
        return $this->returnExtensionResultAsString($result);
    }
    
    /**
     * This method gets called after the order object has been created from the
     * shoppingcart object and before the order positions get created.
     *
     * @param array $arguments The arguments to pass
     *                         $arguments[0] = SilvercartOrder
     *                         $arguments[1] = SilvercartShoppingCart
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public function createFromShoppingCart(&$arguments = array()) {
        $result = $this->extend('pluginCreateFromShoppingCart', $arguments);
        
        return $this->returnExtensionResultAsString($result);
    }
    
    /**
     * Use this method to return additional information on the order in the
     * section "My Account" for the customer.
     *
     * @param array $arguments The arguments to pass
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public function OrderDetailInformation(&$arguments = array()) {
        $result = $this->extend('pluginOrderDetailInformation', $arguments);
        
        return $this->returnExtensionResultAsString($result);
    }
}
