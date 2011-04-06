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
 * Update 0.9 - 1
 * This update converts the Prices of existing SilverCart installations.
 * SilverCarts price handling changed because of changing the general tax
 * logic. The attribute 'Price' of SilvercartProduct was splitted into the
 * new attributes 'PriceNet' and 'PriceGross'. 'Price' is not used anymore.
 *
 * @package Silvercart
 * @subpackage Update
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 25.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdate0_9__1 extends SilvercartUpdate {

    /**
     * Set the defaults for this update.
     *
     * @var array
     */
    public static $defaults = array(
        'SilvercartVersion' => '0.9',
        'SilvercartUpdateVersion' => '1',
        'Description' => 'This update converts the Prices of existing SilverCart installations. SilverCarts price handling changed because of changing the general tax logic. The attribute "Price" of SilvercartProduct was splitted into the new attributes "PriceNet" and "PriceGross". "Price" is not used anymore.',
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
        // Get Products to check for changes
        $existingProducts = DataObject::get('SilvercartProduct');
        foreach ($existingProducts as $product) {
            if (is_null($product->PriceNetCurrency)
             && is_null($product->PriceNetAmount)
             && is_null($product->PriceGrossCurrency)
             && is_null($product->PriceGrossAmount)) {
                continue;
            }
            // Changes detected, skip this update
            $this->skipToPreventDamage();
            return false;
        }
        // No changes yet, do update
        DB::query("
            UPDATE `SilvercartProduct`
            SET
                `SilvercartProduct`.`PriceNetCurrency`=`SilvercartProduct`.`PriceCurrency`,
                `SilvercartProduct`.`PriceGrossCurrency`=`SilvercartProduct`.`PriceCurrency`,
                `SilvercartProduct`.`PriceGrossAmount`=`SilvercartProduct`.`PriceAmount`");
        $config = SilvercartConfig::getConfig();
        $config->PricetypeAnonymousCustomers = 'gross';
        $config->PricetypeRegularCustomers = 'gross';
        $config->PricetypeBusinessCustomers = 'net';
        $config->PricetypeAdmins = 'net';
        $config->write();
        return true;
    }

}