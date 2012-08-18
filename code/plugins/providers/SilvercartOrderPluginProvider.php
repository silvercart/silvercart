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
 * @subpackage Plugins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 22.09.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartOrderPluginProvider extends SilvercartPlugin {

    /**
     * Initialisation for plugin providers.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return sring
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public function init(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginInit', $arguments, $callingObject);
        
        return $this->returnExtensionResultAsString($result);
    }
    
    /**
     * This method gets called after the order object has been created from the
     * shoppingcart object and before the order positions get created.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = SilvercartOrder
     *                              $arguments[1] = SilvercartShoppingCart
     * @param mixed &$callingObject The calling object
     * 
     * @return sring
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public function createFromShoppingCart(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginCreateFromShoppingCart', $arguments, $callingObject);
        
        return $this->returnExtensionResultAsString($result);
    }
    
    /**
     * This method gets called while the SilvercartShoppingCartPositions are
     * converted to SilvercartOrderPositions.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = SilvercartShoppingCartPosition
     *                              $arguments[1] = SilvercartOrderPosition
     * @param mixed &$callingObject The calling object
     * 
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 17.11.2011
     */
    public function convertShoppingCartPositionToOrderPosition(&$arguments = array(), &$callingObject) {
        $this->extend('pluginConvertShoppingCartPositionToOrderPosition', $arguments[0], $arguments[1], $callingObject);
        
        return $arguments[1];
    }
    
    /**
     * Use this method to return additional information on the order in the
     * section "My Account" for the customer.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public function OrderDetailInformation(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginOrderDetailInformation', $arguments, $callingObject);
        
        return $this->returnExtensionResultAsString($result);
    }

    /**
     * This method gets called after the IsPriceTypeGross method has been called
     * and can alter the result.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = SilvercartOrder
     *                              $arguments[1] = IsPriceTypeGross (the original result)
     * @param mixed &$callingObject The calling object
     * 
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 17.11.2011
     */
    public function IsPriceTypeGross(&$arguments = array(), &$callingObject) {
       $this->extend('pluginIsPriceTypeGross', $arguments[0], $callingObject);
        
        return $arguments[0];
    }

    /**
     * This method gets called after the IsPriceTypeNet method has been called
     * and can alter the result.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = SilvercartOrder
     *                              $arguments[1] = IsPriceTypeNet (the original result)
     * @param mixed &$callingObject The calling object
     * 
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 17.11.2011
     */
    public function IsPriceTypeNet(&$arguments = array(), &$callingObject) {
       $this->extend('pluginIsPriceTypeNet', $arguments[0], $callingObject);
        
        return $arguments[0];
    }
}
