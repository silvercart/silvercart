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
 * Update 1.3 - 4
 * Updates simultaneously shown pages field for site pagination in SilverCart's configuration.
 *
 * @package Silvercart
 * @subpackage Update
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 16.08.2012
 * @copyright pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdate1_3__4 extends SilvercartUpdate {
    
    /**
     * Set the defaults for this update.
     *
     * @var array
     */
    public static $defaults = array(
        'SilvercartVersion'         => '1.3',
        'SilvercartUpdateVersion'   => '4',
        'Description'               => 'Updates simultaneously shown pages field for site pagination in SilverCart\'s configuration.',
    );
    
    /**
     * Executes the update logic.
     *
     * @return bool
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 16.08.2012
     */
    public function executeUpdate() {
        DB::query("UPDATE `SilvercartConfig` SET `displayedPaginationPages` = 4");
        return true;
    }
}