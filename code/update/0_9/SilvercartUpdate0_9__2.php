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
 * Update 0.9 - 2
 * This update sets default values for the new configuration parameters of the
 * interface GeoNames.
 *
 * @package Silvercart
 * @subpackage Update
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 25.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdate0_9__2 extends SilvercartUpdate {

    /**
     * Set the defaults for this update.
     *
     * @var array
     */
    public static $defaults = array(
        'SilvercartVersion' => '0.9',
        'SilvercartUpdateVersion' => '2',
        'Description' => 'This update sets default values for the new configuration parameters of the interface GeoNames and converts the countries ISO2 & ISO3 into uppercase. This update also creates all new countries (if not exists).',
    );

    /**
     * Executes the update logic.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.03.2011
     */
    public function executeUpdate() {
        $config = SilvercartConfig::getConfig();
        if (is_null($config->GeoNamesAPI)) {
            // do update, no changes yet
            $config->GeoNamesAPI = 'http://api.geonames.org/';
            $config->write();
        }
        $countries = DataObject::get('SilvercartCountry');
        foreach ($countries as $country) {
            $country->ISO2 = strtoupper($country->ISO2);
            $country->ISO3 = strtoupper($country->ISO3);
            $country->Locale = Translatable::get_current_locale();
            $country->write();
        }
        require_once(Director::baseFolder() . '/silvercart/code/config/SilvercartRequireDefaultCountries.php');
        return true;
    }
    
}