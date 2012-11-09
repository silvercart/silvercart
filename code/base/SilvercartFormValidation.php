<?
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
 * @subpackage Base
 */

/**
 * Provides callbacks for a form validation
 *
 * @package Silvercart
 * @subpackage Base
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 09.11.2012
 * @copyright 2012 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartFormValidation extends Object {    
    
    /**
     * used as Form callback: Does the entered Email already exist?
     *
     * @param string $value the email address to be checked
     *
     * @return array to be rendered in the template
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Patrick Schneider <pschneider@pixeltricks.de>
     * @since 09.11.2012
     */
    public static function doesEmailExistAlready($value) { 
        $emailExistsAlready = false;

        $results = DataObject::get_one(
            'Member',
            "Email = '" . $value . "'"
        );

        if ($results) {
            $emailExistsAlready = true;
        }

        return array(
            'success' => !$emailExistsAlready,
            'errorMessage' => _t('SilvercartPage.EMAIL_ALREADY_REGISTERED', 'This Email address is already registered')
        );
    }
}