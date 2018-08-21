<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\ShoppingCart;
use SilverCart\Model\Pages\MetaNavigationHolder;
use SilverCart\Model\Payment\PaymentMethod;

/**
 * Page to display available payment methods.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class PaymentMethodsPage extends MetaNavigationHolder
{
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartPaymentMethodsPage';
    /**
     * Allowed children
     *
     * @var array
     */
    private static $allowed_children = 'none';
    /**
     * Page type icon
     *
     * @var string
     */
    private static $icon = "silvercart/silvercart:client/img/page_icons/metanavigation_page-file.gif";
    
    /**
     * i18n singular name of this object
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.05.2012
     */
    public function singular_name()
    {
        return Tools::singular_name_for($this);
    }
    
    /**
     * i18n plural name of this object
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.05.2012
     */
    public function plural_name()
    {
        return Tools::plural_name_for($this);
    }

    /**
     * Returns all payment methods
     *
     * @return DataList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.05.2012
     */
    public function PaymentMethods()
    {
        return PaymentMethod::getAllowedPaymentMethodsFor($this->ShippingCountry(), ShoppingCart::singleton(), true);
    }
    
    /**
     * Returns the current shipping country
     *
     * @return Country
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function ShippingCountry()
    {
        $customer        = Customer::currentUser();
        $shippingCountry = null;
        if ($customer) {
            $shippingCountry = $customer->ShippingAddress()->Country();
        }
        if (is_null($shippingCountry)
            || $shippingCountry->ID == 0
        ) {
            $shippingCountry = Country::get()->filter([
                'ISO2'   => substr(Tools::current_locale(), 3),
                'Active' => 1,
            ])->first();
        }
        return $shippingCountry;
    }
}