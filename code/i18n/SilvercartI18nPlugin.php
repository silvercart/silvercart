<?php
/**
 * Copyright 2010 - 2012 pixeltricks GmbH
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
 * @subpackage i18n
 */

/**
 * plugin class to override some default translations from sapphire
 * 
 * @package Silvercart
 * @subpackage i18n
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 26.06.2012
 * @copyright 2012 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartI18nPlugin {
    
    /**
     * plugin function to override sapphire translations
     * 
     * @global array $lang 
     * 
     * @return void
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 26.06.2012
     */
    public static function de_DE() {
        global $lang;
        $lang['de_DE']['Member']['YOUROLDPASSWORD'] = 'Ihr altes Passwort';
    }    
}