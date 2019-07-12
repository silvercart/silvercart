<?php

namespace SilverCart\Checkout;

use SilverCart\Checkout\CheckoutStep;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\ShoppingCart;
use SilverStripe\Security\Member;

/**
 * Checkout step 6.
 * Default checkout step to finalize the order.
 *
 * @package SilverCart
 * @subpackage Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 16.11.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CheckoutStep6 extends CheckoutStep
{
    use PaymentCheckoutStep;
    use ShippingCheckoutStep;
    use AddressCheckoutStep;
    use OrderCheckoutStep;
    /**
     * Is this step visible?
     * (default: true)
     *
     * @var bool
     */
    private static $is_visible = false;

    /**
     * Optional method to initialize a checkout step.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.04.2018
     */
    public function init() : void
    {
        $checkoutData = $this->getCheckout()->getData();
        $this->initPaymentMethod($checkoutData);
        $this->initShippingMethod($checkoutData);
        $this->initAddressData($checkoutData);
        $this->initOrder($checkoutData);
    }
    
    /**
     * Processes this checkout step.
     * The default payment handling is processed here.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function process() : void
    {
        $checkoutData = $this->getCheckout()->getData();
        $payment = $this->getPaymentMethod();
        $payment->doProcessBeforePaymentProvider($checkoutData);
        $payment->doProcessAfterPaymentProvider($checkoutData);
        $payment->doProcessBeforeOrder($checkoutData);
        if ($payment->canPlaceOrder($checkoutData)) {
            $this->placeOrder($checkoutData);
            $payment->doProcessAfterOrder($this->getOrder(), $checkoutData);
            $this->getCheckout()->finalize();
            if (!$this->getController()->redirectedTo()) {
                $this->getController()->redirect($this->getController()->Link('thanks'));
            }
        }
    }

    /**
     * Returns the current shopping cart.
     * 
     * @return ShoppingCart
     */
    public function getShoppingCart() : ?ShoppingCart
    {
        $customer = Customer::currentUser();
        if ($customer instanceof Member
         && $customer->exists()
        ) {
            return $customer->getCart();
        }
    }
}