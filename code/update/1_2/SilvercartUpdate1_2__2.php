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
 * Update 1.2 - 2
 * Moves all existing zone - carrier one to many relations to the new many to many relations.
 *
 * @package Silvercart
 * @subpackage Update
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.03.2012
 * @copyright pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdate1_2__2 extends SilvercartUpdate {

    /**
     * Set the defaults for this update.
     *
     * @var array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2012
     */
    public static $defaults = array(
        'SilvercartVersion'         => '1.2',
        'SilvercartUpdateVersion'   => '2',
        'Description'               => 'Moves all existing zone - carrier one to many relations to the new many to many relations.',
    );
    
    /**
     * Executes the update logic.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2012
     */
    public function executeUpdate() {
        $query = DB::query("SELECT `ID`, `SilvercartCarrierID` FROM `SilvercartZone`");
        if ($query) {
            foreach ($query as $result) {
                $zone       = DataObject::get_by_id('SilvercartZone', $result['ID']);
                $carrier    = DataObject::get_by_id('SilvercartCarrier', $result['SilvercartCarrierID']);
                
                if ($zone && $carrier) {
                    $zone->SilvercartCarriers()->add($carrier);
                }
            }
        }
        DB::query("ALTER TABLE `SilvercartZone` DROP `SilvercartCarrierID`");
        return true;
    }
}