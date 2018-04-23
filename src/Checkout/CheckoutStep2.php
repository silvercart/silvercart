<?php

namespace SilverCart\Checkout;

use SilverCart\Checkout\CheckoutStep;
use SilverCart\Forms\AddAddressForm;
use SilverCart\Forms\Checkout\CheckoutAnonymousCustomerAddressForm;
use SilverCart\Forms\Checkout\CheckoutRegularCustomerAddressForm;
use SilverCart\Model\Customer\Customer;
use SilverStripe\Security\Member;

/**
 * Checkout step 2.
 * Provides either the available invoice and shipping addresses for registered customers or a form 
 * to enter address data for anonymous customers.
 *
 * @package SilverCart
 * @subpackage Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 16.11.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CheckoutStep2 extends CheckoutStep {
    
    /**
     * List of allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = [
        'CheckoutRegularCustomerAddressForm',
        'CheckoutAnonymousCustomerAddressForm',
    ];

    /**
     * Is customer logged in?
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2011
     */
    public function IsCustomerLoggedIn() {
        $isLoggedIn = false;
        $registeredCustomer = Customer::currentRegisteredCustomer();
        if ($registeredCustomer instanceof Member &&
            $registeredCustomer->exists()) {
            $isLoggedIn = true;
        }
        return $isLoggedIn;
    }
    
    /**
     * Returns the AddAddressForm.
     * 
     * @return AddAddressForm
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.11.2017
     */
    public function AddAddressForm() {
        $form = new AddAddressForm($this->getController());
        return $form;
    }
    
    /**
     * Returns the CheckoutRegularCustomerAddressForm.
     * 
     * @return CheckoutRegularCustomerAddressForm
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.11.2017
     */
    public function CheckoutRegularCustomerAddressForm() {
        $form = new CheckoutRegularCustomerAddressForm($this->getController());
        return $form;
    }
    
    /**
     * Returns the CheckoutAnonymousCustomerAddressForm.
     * 
     * @return CheckoutAnonymousCustomerAddressForm
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function CheckoutAnonymousCustomerAddressForm() {
        $form = new CheckoutAnonymousCustomerAddressForm($this->getController());
        return $form;
    }
    
    /**
     * Returns whether to show the RegisterRegularCustomerForm or not.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.11.2017
     */
    public function IsAnonymousCheckout() {
        return $this->getCheckout()->getDataValue('IsAnonymousCheckout') === true;
    }
    
}
