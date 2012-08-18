<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
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
 * Update 1.0 - 3
 * This update sets the new configuration attribute productGroupsPerPage to the default value 6.
 *
 * @package Silvercart
 * @subpackage Update
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright pixeltricks GmbH
 * @since 05.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdate1_0__3 extends SilvercartUpdate {

    /**
     * Set the defaults for this update.
     *
     * @var array
     */
    public static $defaults = array(
        'SilvercartVersion' => '1.0',
        'SilvercartUpdateVersion' => '3',
        'Description' => 'This update sets the new configuration attribute productGroupsPerPage to the default value 6.',
    );

    /**
     * Executes the update logic.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.07.2011
     */
    public function executeUpdate() {
        $config = SilvercartConfig::getConfig();
        if ($config->productGroupsPerPage == 0) {
            $config->productGroupsPerPage = 6;
            $config->write();
            return true;
        } else {
            $this->skip();
            return false;
        }
    }

}