<?php
/**
 * Copyright 2013 pixeltricks GmbH
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
 * Update 1.3 - 7
 * Change relation from SilvercartPaymentMethod to SilvercartHandlingCost
 * from has_one to has_many and allow Zones for SilvercartHandlingCosts.
 *
 * @package Silvercart
 * @subpackage Update
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 2013-01-18
 * @copyright pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartUpdate1_3__7 extends SilvercartUpdate {

    /**
     * Set the defaults for this update.
     *
     * @var array
     */
    public static $defaults = array(
        'SilvercartVersion'         => '1.3',
        'SilvercartUpdateVersion'   => '7',
        'Description'               => 'Change relation from SilvercartPaymentMethod to SilvercartHandlingCost from has_one to has_many and allow Zones for SilvercartHandlingCosts.',
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
        $silvercartPaymentMethods = DataObject::get(
            'SilvercartPaymentMethod'
        );

        if ($silvercartPaymentMethods) {
            foreach ($silvercartPaymentMethods as $silvercartPaymentMethod) {
                if ($silvercartPaymentMethod->SilvercartHandlingCostID > 0) {
                    $handlingCost = DataObject::get_by_id(
                        'SilvercartHandlingCost',
                        $silvercartPaymentMethod->SilvercartHandlingCostID
                    );

                    $handlingCost->SilvercartPaymentMethodID = $silvercartPaymentMethod->ID;
                    $handlingCost->write();
                }
            }
        }

        return true;
    }
}