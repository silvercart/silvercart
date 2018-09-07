<?php

namespace SilverCart\Checkout;

use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\NumberRange;
use SilverCart\Model\Order\Order;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

/**
 * OrderCheckoutStep.
 * Trait to provide order related methods to a checkout step.
 *
 * @package SilverCart
 * @subpackage Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 12.04.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
trait OrderCheckoutStep
{
    /**
     * Order.
     *
     * @var \SilverCart\Model\Order\Order
     */
    protected $order = null;
    /**
     * Set this option to false to prevent sending the order confimation before finishing payment 
     * module dependent order manipulations.
     *
     * @var bool
     */
    protected $sendConfirmationMail = true;

    /**
     * Returns the order.
     * 
     * @return \SilverCart\Model\Order\Order
     */
    public function getOrder()
    {
        return $this->order;
    }
    
    /**
     * Returns whether to send the order confirmation mail or not.
     * 
     * @return bool
     */
    public function getSendConfirmationMail()
    {
        return $this->sendConfirmationMail;
    }

    /**
     * Sets the order.
     * 
     * @param \SilverCart\Model\Order\Order $order Order
     * 
     * @return \SilverCart\Checkout\OrderCheckoutStep
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
        return $this;
    }
    
    /**
     * Sets whether to send the order confirmation mail or not.
     * 
     * @param bool $sendConfirmationMail Send order confirmation mail or not?
     * 
     * @return \SilverCart\Checkout\OrderCheckoutStep
     */
    public function setSendConfirmationMail($sendConfirmationMail)
    {
        $this->sendConfirmationMail = $sendConfirmationMail;
        return $this;
    }

    /**
     * Initializes the order by using the checkout data.
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return \SilverCart\Checkout\OrderCheckoutStep
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.04.2018
     */
    public function initOrder($checkoutData = null)
    {
        if (is_null($checkoutData)) {
            $checkoutData = $this->getCheckout()->getData();
        }
        if (array_key_exists('Order', $checkoutData)) {
            $orderID = $checkoutData['Order'];
            $order   = Order::get()->byID($orderID);
            if ($order instanceof Order) {
                $this->setOrder($order);
            }
        }
        return $this;
    }

    /**
     * Places the order.
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return \SilverCart\Checkout\OrderCheckoutStep
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.04.2018
     */
    public function placeOrder($checkoutData = null)
    {
        $order = $this->getOrder();
        if ($order instanceof Order
         && $order->exists()
        ) {
            return $this;
        }
        if (is_null($checkoutData)) {
            $checkoutData = $this->getCheckout()->getData();
        }
        $customer = Security::getCurrentUser();
        if ($customer instanceof Member) {
            $customerEmail = $customer->Email;
            $customerNote  = '';
            if (array_key_exists('Email', $checkoutData)) {
                $customerEmail = $checkoutData['Email'];
            }
            if (array_key_exists('Note', $checkoutData)) {
                $customerNote = $checkoutData['Note'];
            }
            
            $anonymousCustomer = Customer::currentAnonymousCustomer();
            if ($anonymousCustomer instanceof Member
             && $anonymousCustomer->exists()
            ) {
                // add a customer number to anonymous customer when ordering
                $anonymousCustomer->CustomerNumber = NumberRange::useReservedNumberByIdentifier('CustomerNumber');
                $anonymousCustomer->write();
            }
            
            $order = $this->createOrder($customerEmail, $checkoutData, $customerNote);
            
            $order->createShippingAddress($checkoutData['ShippingAddress']);
            $order->createInvoiceAddress($checkoutData['InvoiceAddress']);
            
            $order->convertShoppingCartPositionsToOrderPositions();
            
            // send order confirmation mail
            if ($this->getSendConfirmationMail()) {
                $order->sendConfirmationMail();
            }
            
            $this->setOrder($order);
            $this->getCheckout()->addDataValue('Order', $order->ID);
            $this->getCheckout()->saveInSession();
        }
        return $this;
    }

    /**
     * Creates a Order object from the given parameters.
     *
     * @param string $customerEmail The customers email address
     * @param array  $checkoutData  The checkout data
     * @param string $customerNote  The optional note from the customer
     *
     * @return \SilverCart\Model\Order\Order
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function createOrder($customerEmail, $checkoutData, $customerNote)
    {
        $order = Order::create();
        $this->extend('onBeforeCreateOrder', $order, $customerEmail, $checkoutData, $customerNote);
        $order->setCustomerEmail($customerEmail);
        $order->setShippingMethod($checkoutData['ShippingMethod']);
        $order->setPaymentMethod($checkoutData['PaymentMethod']);
        $order->setNote($customerNote);
        $order->setWeight();
        $order->setHasAcceptedTermsAndConditions($checkoutData['HasAcceptedTermsAndConditions'] == "1");
        $order->setHasAcceptedRevocationInstruction($checkoutData['HasAcceptedRevocationInstruction'] == "1");
        $order->createFromShoppingCart();
        $this->extend('onAfterCreateOrder', $order, $customerEmail, $checkoutData, $customerNote);
        return $order;
    }
}
