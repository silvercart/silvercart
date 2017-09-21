<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @copyright 2013 pixeltricks GmbH
 * @since 14.05.2012
 * @license see license file in modules root directory
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
    public static $icon = "silvercart/img/page_icons/metanavigation_page";
    
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
 * @copyright 2013 pixeltricks GmbH
 * @since 14.05.2012
 * @license see license file in modules root directory
 */
class SilvercartPaymentMethodsPage_Controller extends SilvercartMetaNavigationHolder_Controller {

    /**
     * Returns all payment methods
     *
     * @return DataList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.05.2012
     */
    public function PaymentMethods() {
        $PaymentMethods = SilvercartPaymentMethod::getAllowedPaymentMethodsFor($this->ShippingCountry(), singleton('SilvercartShoppingCart'), true);
        return $PaymentMethods;
    }
    
    /**
     * Returns the current shipping country
     *
     * @return SilvercartCountry
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function ShippingCountry() {
        $customer           = SilvercartCustomer::currentUser();
        $shippingCountry    = null;
        if ($customer) {
            $shippingCountry = $customer->SilvercartShippingAddress()->SilvercartCountry();
        }
        if (is_null($shippingCountry) ||
            $shippingCountry->ID == 0) {
            $shippingCountry = SilvercartCountry::get()->filter(array(
                'ISO2' => substr(Translatable::get_current_locale(), 3),
                'Active' => 1,
            ))->first();
        }
        return $shippingCountry;
    }
    
}

