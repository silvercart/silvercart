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
 * Update 1.2 - 1
 * This update moves all Member objects without ClassName to the Member class.
 *
 * @package Silvercart
 * @subpackage Update
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright pixeltricks GmbH
 * @since 11.10.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdate1_2__1 extends SilvercartUpdate {

    /**
     * Set the defaults for this update.
     *
     * @var array
     */
    public static $defaults = array(
        'SilvercartVersion'         => '1.2',
        'SilvercartUpdateVersion'   => '1',
        'Description'               => 'This update moves all Member objects without ClassName to the Member class.',
    );
    
    /**
     * Executes the update logic.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 11.10.2011
     */
    public function executeUpdate() {
        $members = DataObject::get('Member');
        
        foreach ($members as $member) {
            if (empty($member->ClassName)) {
                $member->setField('ClassName', 'Member');
                $member->write();
            }
            if ($member->Groups()->Count() == 0) {
                $anonymousGroup = DataObject::get_one(
                    'Group',
                    "Code = 'anonymous'"
                );

                if ($anonymousGroup) {
                    $member->Groups()->add($anonymousGroup);
                }
            }
        }
        
        return true;
    }
}