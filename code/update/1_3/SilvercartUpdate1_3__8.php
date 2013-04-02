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
 * Update 1.3 - 8
 * Change relation from SilvercartPaymentMethod to SilvercartHandlingCost
 * from has_one to has_many and allow Zones for SilvercartHandlingCosts.
 *
 * @package Silvercart
 * @subpackage Update
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 2013-02-07
 * @copyright pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartUpdate1_3__8 extends SilvercartUpdate {

    /**
     * Set the defaults for this update.
     *
     * @var array
     */
    public static $defaults = array(
        'SilvercartVersion'         => '1.3',
        'SilvercartUpdateVersion'   => '8',
        'Description'               => 'Populate priorities for SilvercartShippingMethods, -Fees and -Carriers',
    );

    /**
     * Executes the update logic.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2013-02-07
     */
    public function executeUpdate() {
        $silvercartShippingMethods = DataObject::get(
            'SilvercartShippingMethod'
        );

        if ($silvercartShippingMethods) {
            $priorities  = array();
            $priorityIdx = 0;

            foreach ($silvercartShippingMethods as $silvercartShippingMethod) {
                $priorities[] = $silvercartShippingMethod->SilvercartCarrierID;
            }

            rsort($priorities);

            foreach ($silvercartShippingMethods as $silvercartShippingMethod) {
                $silvercartShippingMethod->priority = $priorities[$priorityIdx];
                $silvercartShippingMethod->write();
                $priorityIdx++;
            }
        }

        $silvercartCarriers = DataObject::get(
            'SilvercartCarrier'
        );

        if ($silvercartCarriers) {
            $priorities  = array();
            $priorityIdx = 0;

            foreach ($silvercartCarriers as $silvercartCarrier) {
                $priorities[] = $silvercartCarrier->ID;
            }

            rsort($priorities);

            foreach ($silvercartCarriers as $silvercartCarrier) {
                $silvercartCarrier->priority = $priorities[$priorityIdx];
                $silvercartCarrier->write();
                $priorityIdx++;
            }
        }

        $silvercartShippingFees = DataObject::get(
            'SilvercartShippingFee'
        );

        if ($silvercartShippingFees) {
            $priorities  = array();
            $priorityIdx = 0;

            foreach ($silvercartShippingFees as $silvercartShippingFee) {
                $priorities[] = $silvercartShippingFee->ID;
            }

            rsort($priorities);

            foreach ($silvercartShippingFees as $silvercartShippingFee) {
                $silvercartShippingFee->priority = $priorities[$priorityIdx];
                $silvercartShippingFee->write();
                $priorityIdx++;
            }
        }

        return true;
    }
}