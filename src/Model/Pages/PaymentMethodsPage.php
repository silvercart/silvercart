<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\ShoppingCart;
use SilverCart\Model\Pages\MetaNavigationHolder;
use SilverCart\Model\Payment\PaymentMethod;
use SilverStripe\ORM\SS_List;

/**
 * Page to display available payment methods.
 *
 * @package SilverCart
 * @subpackage Model\Pages
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
     * Class attached to page icons in the CMS page tree. Also supports font-icon set.
     * 
     * @var string
     */
    private static $icon_class = 'font-icon-p-posts';

    /**
     * Returns all payment methods
     *
     * @return DataList|ArrayList
     */
    public function PaymentMethods() : SS_List
    {
        return PaymentMethod::getAllowedPaymentMethodsFor($this->ShippingCountry(), ShoppingCart::singleton(), true);
    }
    
    /**
     * Returns the current shipping country
     *
     * @return Country
     */
    public function ShippingCountry() : ?Country
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