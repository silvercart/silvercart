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
 * @subpackage Update
 */

/**
 * Update 1.3 - 3
 * Updates the price type of all orders to the default price type set in SilverCart's configuration
 *
 * @package Silvercart
 * @subpackage Update
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 18.06.2012
 * @copyright pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdate1_3__3 extends SilvercartUpdate {

    /**
     * Set the defaults for this update.
     *
     * @var array
     */
    public static $defaults = array(
        'SilvercartVersion'         => '1.3',
        'SilvercartUpdateVersion'   => '3',
        'Description'               => 'Updates the price type of all orders to the default price type set in SilverCart\'s configuration.',
    );
    
    /**
     * Executes the update logic.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.06.2012
     */
    public function executeUpdate() {
        $priceType = SilvercartConfig::DefaultPriceType();
        
        DB::query(
                sprintf(
                        "UPDATE `SilvercartOrder` SET `PriceType` = '%s'",
                        $priceType
                )
        );
        
        return true;
    }
}