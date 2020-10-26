<?php

namespace SilverCart\Checkout;

use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\NumberRange;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Order\ShoppingCart;
use SilverStripe\ORM\ArrayList;
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
 * 
 * @mixin CheckoutStep
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
     * Orders.
     *
     * @var \SilverStripe\ORM\ArrayList
     */
    protected $orders = null;
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
    public function getOrder() : ?Order
    {
        return $this->order;
    }
    
    /**
     * Returns the orders.
     * 
     * @return ArrayList
     */
    public function getOrders() : ArrayList
    {
        if (is_null($this->orders)) {
            $this->orders = ArrayList::create();
        }
        return $this->orders;
    }
    
    /**
     * Returns whether to send the order confirmation mail or not.
     * 
     * @return bool
     */
    public function getSendConfirmationMail() : bool
    {
        return (bool) $this->sendConfirmationMail;
    }

    /**
     * Sets the order.
     * 
     * @param \SilverCart\Model\Order\Order $order Order
     * 
     * @return \SilverCart\Checkout\OrderCheckoutStep
     */
    public function setOrder(Order $order) : object
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Sets the orders.
     * 
     * @param ArrayList $orders Orders
     * 
     * @return \SilverCart\Checkout\OrderCheckoutStep
     */
    public function setOrders(ArrayList $orders) : object
    {
        $this->orders = $orders;
        return $this;
    }
    
    /**
     * Sets the orders by the given ID list.
     * 
     * @param array $idList List of order IDs.
     * 
     * @return \SilverCart\Checkout\OrderCheckoutStep
     */
    public function setOrdersByIDList(array $idList) : object
    {
        $customer = Security::getCurrentUser();
        if ($customer instanceof Member) {
            foreach ($idList as $orderID) {
                $order = Order::get()->byID($orderID);
                if ($order instanceof Order
                 && $order->canView()
                ) {
                    $this->getOrders()->add($order);
                }
            }
        }
        return $this;
    }
    
    /**
     * Sets whether to send the order confirmation mail or not.
     * 
     * @param bool $sendConfirmationMail Send order confirmation mail or not?
     * 
     * @return \SilverCart\Checkout\OrderCheckoutStep
     */
    public function setSendConfirmationMail(bool $sendConfirmationMail) : object
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
    public function initOrder(array $checkoutData = null) : object
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
            if (array_key_exists('Orders', $checkoutData)) {
                $this->setOrdersByIDList($checkoutData['Orders']);
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
    public function placeOrder(array $checkoutData = null) : object
    {
        $order = $this->getOrder();
        if (($order instanceof Order
          && $order->exists())
         || $this->getOrders()->exists()
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
            
            $orders = $this->createOrders($customerNote, $checkoutData, $customerNote);
            if ($orders === null
             || !$orders->exists()
            ) {
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
            } else {
                $order = $orders->first();
                $this->setOrder($order);
                $this->setOrders($orders);
                $this->getCheckout()->addDataValue('Order', $order->ID);
                $this->getCheckout()->addDataValue('Orders', $orders->map('ID', 'ID')->toArray());
                $this->getCheckout()->saveInSession();
            }
            
        }
        return $this;
    }

    /**
     * Creates a Order object from the given parameters.
     *
     * @param string       $customerEmail The customers email address
     * @param array        $checkoutData  The checkout data
     * @param string       $customerNote  The optional note from the customer
     * @param ShoppingCart $shoppingCart  Optional shopping cart context
     *
     * @return \SilverCart\Model\Order\Order
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.09.2018
     */
    public function createOrder(string $customerEmail = null, array $checkoutData = null, string $customerNote = null, ShoppingCart $shoppingCart = null) : Order
    {
        $order = Order::create();
        $this->extend('onBeforeCreateOrder', $order, $customerEmail, $checkoutData, $customerNote);
        $order->setCustomerEmail($customerEmail);
        $order->setShippingMethod($checkoutData['ShippingMethod']);
        $order->setPaymentMethod($checkoutData['PaymentMethod']);
        $order->setNote($customerNote);
        $order->setWeight();
        $order->setHasAcceptedTermsAndConditions(true);
        $order->setHasAcceptedRevocationInstruction(true);
        $order->createFromShoppingCart($shoppingCart);
        $this->extend('onAfterCreateOrder', $order, $customerEmail, $checkoutData, $customerNote);
        return $order;
    }
    
    /**
     * Creates multiple orders if a multi order extension is installed.
     * 
     * @param string $customerEmail The customers email address
     * @param array  $checkoutData  The checkout data
     * @param string $customerNote  The optional note from the customer
     * 
     * @return ArrayList|null
     */
    public function createOrders(string $customerEmail = null, array $checkoutData = null, string $customerNote = null) : ?ArrayList
    {
        $orders = null;
        $this->extend('onBeforeCreateOrders', $orders, $customerEmail, $checkoutData, $customerNote);
        $this->extend('createOrders', $orders, $customerEmail, $checkoutData, $customerNote);
        $this->extend('onAfterCreateOrders', $orders, $customerEmail, $checkoutData, $customerNote);
        return $orders;
    }
}
