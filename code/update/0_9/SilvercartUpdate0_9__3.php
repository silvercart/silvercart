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
 * Update 0.9 - 3
 * This update sets customer- and ordernumbers for existing customers and orders.
 *
 * @package Silvercart
 * @subpackage Update
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 06.04.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdate0_9__3 extends SilvercartUpdate {

    /**
     * Set the defaults for this update.
     *
     * @var array
     */
    public static $defaults = array(
        'SilvercartVersion' => '0.9',
        'SilvercartUpdateVersion' => '3',
        'Description' => 'This update sets customer- and ordernumbers for existing customers and orders.',
    );

    /**
     * Executes the update logic.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.04.2011
     */
    public function executeUpdate() {
        $regularCustomers = DataObject::get('SilvercartRegularCustomer');
        if ($regularCustomers) {
            foreach ($regularCustomers as $regularCustomer) {
                $regularCustomer->write();
            }
        }
        $businessCustomers = DataObject::get('SilvercartBusinessCustomer');
        if ($businessCustomers) {
            foreach ($businessCustomers as $businessCustomer) {
                $businessCustomer->write();
            }
        }
        $orders = DataObject::get('SilvercartOrder');
        if ($orders) {
            foreach ($orders as $order) {
                $order->write();
            }
        }
        return true;
    }
    
}