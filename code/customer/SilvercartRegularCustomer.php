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
 * @subpackage Customer
 */

/**
 * abstract for a regular customer
 *
 * @package Silvercart
 * @subpackage Customer
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 22.10.2010
 */
class SilvercartRegularCustomer extends Member {

    /**
     * Set a new/reserved customernumber before writing
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.04.2011
     */
    public function onBeforeWrite() {
        parent::onBeforeWrite();
        if (empty ($this->CustomerNumber)) {
            $this->CustomerNumber = SilvercartNumberRange::useReservedNumberByIdentifier('CustomerNumber');
        }
    }

    /**
     * hook
     * every $this gets a shopping cart
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 22.10.2010
     * @return void
     */
    public function onAfterWrite() {
        parent::onAfterWrite();
        //create a cart for every user
        if ($this->SilvercartShoppingCartID == null) {
            $cart = new SilvercartShoppingCart();
            $cart->write();
            $this->SilvercartShoppingCartID = $cart->ID;
            $this->write();
        }
    }

    /**
     * hook
     * delete shopping cart too
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 22.10.2010
     * @return void
     */
    public function onAfterDelete() {
        parent::onAfterDelete();
        if ($this->SilvercartShoppingCartID) {
            $cart = DataObject::get_by_id('SilvercartShoppingCart', $this->SilvercartShoppingCartID);
            if ($cart) {
                $cart->delete();
            }
        }
        
    }

}
