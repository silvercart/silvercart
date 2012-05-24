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
 * Update 1.2 - 3
 * Updates the customer groups price type settings
 *
 * @package Silvercart
 * @subpackage Update
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 05.04.2012
 * @copyright pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdate1_2__3 extends SilvercartUpdate {

    /**
     * Set the defaults for this update.
     *
     * @var array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public static $defaults = array(
        'SilvercartVersion'         => '1.2',
        'SilvercartUpdateVersion'   => '3',
        'Description'               => 'Updates the customer groups price type settings.',
    );
    
    /**
     * Executes the update logic.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function executeUpdate() {
        $b2b        = DataObject::get_one('Group', "`Code` = 'b2b'");
        $b2c        = DataObject::get_one('Group', "`Code` = 'b2c'");
        $anonymous  = DataObject::get_one('Group', "`Code` = 'anonymous'");
        
        $query = DB::query("SELECT `PricetypeBusinessCustomers`, `PricetypeRegularCustomers`, `PricetypeAnonymousCustomers` FROM `SilvercartConfig`");
        if ($query) {
            foreach ($query as $result) {
                if ($b2b) {
                    $b2b->Pricetype = $result['PricetypeBusinessCustomers'];
                    $b2b->write();
                }
                if ($b2c) {
                    $b2c->Pricetype = $result['PricetypeRegularCustomers'];
                    $b2c->write();
                }
                if ($anonymous) {
                    $anonymous->Pricetype   = $result['PricetypeAnonymousCustomers'];
                    $anonymous->write();
                }
            }
        }
        DB::query("ALTER TABLE `SilvercartConfig` DROP `PricetypeBusinessCustomers`");
        DB::query("ALTER TABLE `SilvercartConfig` DROP `PricetypeRegularCustomers`");
        DB::query("ALTER TABLE `SilvercartConfig` DROP `PricetypeAnonymousCustomers`");
        
        $config = SilvercartConfig::getConfig();
        $config->DefaultPriceType = 'gross';
        $config->write();
        
        return true;
    }
}