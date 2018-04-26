<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\ShoppingCart;
use SilverCart\Model\Pages\MetaNavigationHolderController;
use SilverCart\Model\Payment\PaymentMethod;

/**
 * PaymentMethodsPage Controller class.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class PaymentMethodsPageController extends MetaNavigationHolderController {

    /**
     * Returns all payment methods
     *
     * @return DataList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.05.2012
     */
    public function PaymentMethods() {
        $paymentMethods = PaymentMethod::getAllowedPaymentMethodsFor($this->ShippingCountry(), ShoppingCart::singleton(), true);
        return $paymentMethods;
    }
    
    /**
     * Returns the current shipping country
     *
     * @return Country
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function ShippingCountry() {
        $customer           = Customer::currentUser();
        $shippingCountry    = null;
        if ($customer) {
            $shippingCountry = $customer->ShippingAddress()->Country();
        }
        if (is_null($shippingCountry) ||
            $shippingCountry->ID == 0) {
            $shippingCountry = Country::get()->filter(array(
                'ISO2' => substr(Tools::current_locale(), 3),
                'Active' => 1,
            ))->first();
        }
        return $shippingCountry;
    }
    
}