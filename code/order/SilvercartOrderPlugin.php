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
 * @subpackage Order
 */

/**
 * A blueprint for a SilvercartOrderPlugin
 *
 * @package Silvercart
 * @subpacke Order
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 30.08.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
abstract class SilvercartOrderPlugin {
    
    /**
     * Contains the order object on which this class operates.
     *
     * @var SilvercartOrder
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.08.2011
     */
    protected static $silvercartOrder = null;

    /**
     * Gets the object on which the class should operate.
     *
     * @return void
     *
     * @param SilvercartOrder $silvercartOrder The order object to operate on.
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.08.2011
     */
    public function init(SilvercartOrder $silvercartOrder) {
        if ($silvercartOrder instanceOf SilvercartOrder) {
            self::$silvercartOrder = $silvercartOrder;
        }
    }
    
    /**
     * Should return informations regarding the order details.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.08.2011
     */
    abstract public static function OrderDetailInformation();
}