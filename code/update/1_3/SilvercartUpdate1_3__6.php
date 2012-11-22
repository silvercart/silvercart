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
 * Update 1.3 - 6
 * Generate SilvercartManufacturerLanguage objects for each
 * SilvercartManufacturer.
 *
 * @package Silvercart
 * @subpackage Update
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 22.11.2012
 * @copyright pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdate1_3__6 extends SilvercartUpdate {
    
    /**
     * Set the defaults for this update.
     *
     * @var array
     */
    public static $defaults = array(
        'SilvercartVersion'         => '1.3',
        'SilvercartUpdateVersion'   => '6',
        'Description'               => 'Make SilvercartManufacturers translatable',
    );
    
    /**
     * Executes the update logic.
     *
     * @return bool
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.11.2012
     */
    public function executeUpdate() {
        $manufacturers = DB::query(
            "
            SELECT
                *
            FROM
              SilvercartManufacturer
            "
        );

        if ($manufacturers) {
            foreach ($manufacturers as $manufacturer) {
                $manufacturerLanguage = new SilvercartManufacturerLanguage();
                $manufacturerLanguage->SilvercartManufacturerID = $manufacturer['ID'];
                $manufacturerLanguage->Locale                   = SilvercartConfig::DefaultLanguage();
                $manufacturerLanguage->write();
            }
        }

        return true;
    }
}