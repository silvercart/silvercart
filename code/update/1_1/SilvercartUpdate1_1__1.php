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
 * @subpackage Update
 */

/**
 * Update 1.1 - 1
 * This update adjusts the newsletter opt-in status of existing customers
 * according to their classname and their opt-in-status.
 *
 * @package Silvercart
 * @subpackage Update
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright pixeltricks GmbH
 * @since 26.08.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdate1_1__1 extends SilvercartUpdate {

    /**
     * Set the defaults for this update.
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.08.2011
     */
    public static $defaults = array(
        'SilvercartVersion'         => '1.1',
        'SilvercartUpdateVersion'   => '1',
        'Description'               => 'This update adjusts the newsletter opt-in status of existing customers according to their classname and their opt-in-status.',
    );

    /**
     * Executes the update logic.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.08.2011
     */
    public function executeUpdate() {
        if (class_exists('SilvercartRegularCustomer')) {
            $className = 'SilvercartRegularCustomer';
        } else {
            $className = 'Member';
        }
        $members = DataObject::get($className);
        
        // Set the newsletter opt-in status according to the class of the customers
        if ($members) {
            foreach ($members as $member) {
                if ($member->hasField('OptInStatus') &&
                    $member->ClassName == $className &&
                    $member->OptInStatus) {

                    $member->NewsletterOptInStatus = 1;
                    $member->write();
                }
            }
        }
        
        return true;
    }
}