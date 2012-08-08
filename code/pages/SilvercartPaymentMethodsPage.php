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
 * @subpackage Pages
 */

/**
 * show the shipping fee matrix
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright pixeltricks GmbH
 * @since 14.05.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartPaymentMethodsPage extends SilvercartMetaNavigationHolder {
    
    /**
     * Allowed children
     *
     * @var array
     */
    public static $allowed_children = 'none';
    
    /**
     * Page type icon
     *
     * @var string
     */
    public static $icon = "silvercart/images/page_icons/metanavigation_page";
    
    /**
     * i18n singular name of this object
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.05.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }
    
    /**
     * i18n plural name of this object
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.05.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this);
    }
}

/**
 * corresponding controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright pixeltricks GmbH
 * @since 14.05.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartPaymentMethodsPage_Controller extends SilvercartMetaNavigationHolder_Controller {

    /**
     * Returns all payment methods
     *
     * @return DataObjectSet
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.05.2012
     */
    public function PaymentMethods() {
        $PaymentMethods = SilvercartPaymentMethod::getAllowedPaymentMethodsFor($this->ShippingCountry(), new SilvercartShoppingCart(), true);
        return $PaymentMethods;
    }
    
    /**
     * Returns the current shipping country
     *
     * @return SilvercartCountry
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.05.2012
     */
    public function ShippingCountry() {
        $customer           = Member::currentUser();
        $shippingCountry    = null;
        if ($customer) {
            $shippingCountry = $customer->SilvercartShippingAddress()->SilvercartCountry();
        }
        if (is_null($shippingCountry) ||
            $shippingCountry->ID == 0) {
            $shippingCountry = DataObject::get_one(
                    'SilvercartCountry',
                    sprintf(
                            "`ISO2` = '%s' AND `Active` = 1",
                            substr(Translatable::get_current_locale(), 3)
                    )
            );
        }
        return $shippingCountry;
    }
    
}

