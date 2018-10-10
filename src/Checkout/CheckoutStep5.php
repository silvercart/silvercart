<?php

namespace SilverCart\Checkout;

use SilverCart\Checkout\CheckoutStep;
use SilverCart\Forms\Checkout\CheckoutConfirmOrderForm;
use SilverCart\Model\Customer\Customer;
use SilverStripe\Security\Member;

/**
 * Checkout step 5.
 * Shows an overview of all checkout data and asks to confirm and place the order.
 *
 * @package SilverCart
 * @subpackage Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 16.11.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CheckoutStep5 extends CheckoutStep
{
    use PaymentCheckoutStep;
    use ShippingCheckoutStep;
    use AddressCheckoutStep;
    
    /**
     * List of allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = [
        'CheckoutConfirmOrderForm',
    ];

    /**
     * Optional method to initialize a checkout step.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.04.2018
     */
    public function init()
    {
        $checkoutData = $this->getCheckout()->getData();
        $this->initPaymentMethod($checkoutData);
        $this->initShippingMethod($checkoutData);
        $this->initAddressData($checkoutData);
        $this->resetPaymentProgress();
    }
    
    /**
     * Returns the CheckoutConfirmOrderForm.
     * 
     * @return CheckoutConfirmOrderForm
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function CheckoutConfirmOrderForm()
    {
        $form = CheckoutConfirmOrderForm::create($this->getController());
        return $form;
    }

    /**
     * Returns the current shopping cart.
     * 
     * @return \SilverCart\Model\Order\ShoppingCart
     */
    public function getShoppingCart()
    {
        $customer = Customer::currentUser();
        if ($customer instanceof Member
         && $customer->exists()
        ) {
            return $customer->getCart();
        }
    }

    /**
     * Due to a but we had to render the template here. If we would have included
     * it in the *.ss file it would have been rendered twice and our logic would
     * not work propery.
     * 
     * @return string
     */
    public function getShoppingCartFull()
    {
        $customer = Customer::currentUser();
        if ($customer instanceof Member
         && $customer->exists()
        ) {
            return $this->getShoppingCart()->renderWith('SilverCart/Model/Pages/Includes/ShoppingCartFull');
        }
    }
}