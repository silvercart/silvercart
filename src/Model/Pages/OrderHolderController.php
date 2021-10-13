<?php

namespace SilverCart\Model\Pages;

use SilverCart\Checkout\Checkout;
use SilverCart\Checkout\CheckoutStep1;
use SilverCart\Checkout\CheckoutStep2;
use SilverCart\Checkout\CheckoutStep3;
use SilverCart\Checkout\CheckoutStep4;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Order\OrderInvoiceAddress;
use SilverCart\Model\Order\OrderShippingAddress;
use SilverCart\Model\Pages\MyAccountHolderController;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

/**
 * OrderHolder Controller class.
 *
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @method OrderHolder data() Returns the current page context.
 */
class OrderHolderController extends MyAccountHolderController
{
    /**
     * ID of the requested order
     *
     * @var int 
     */
    protected $orderID = 0;
    /**
     * Allowed actions
     *
     * @var array
     */
    private static $allowed_actions = [
        'detail',
        'placeorder',
        'placeorder_full',
    ];
    
    /**
     * Action to show an orders details.
     * 
     * @param HTTPRequest $request HTTP request
     * 
     * @return HTTPResponse
     */
    public function detail(HTTPRequest $request) : HTTPResponse
    {
        $response = $this->handleOrderDetailAction($request);
        if ($response instanceof HTTPResponse) {
            return $response;
        }
        return HTTPResponse::create($this->render());
    }
    
    /**
     * Action to re-order.
     * 
     * @param HTTPRequest $request HTTP request
     * 
     * @return HTTPResponse
     */
    public function placeorder(HTTPRequest $request) : HTTPResponse
    {
        if (!$this->data()->AllowReorder) {
            $this->httpError(403);
        }
        $response = $this->handleOrderDetailAction($request);
        if ($response instanceof HTTPResponse) {
            return $response;
        }
        $this->doPlaceOrder();
        return $this->redirect(CartPage::get()->first()->Link());
    }
    
    /**
     * Action to re-order with an already filled checkout.
     * 
     * @param HTTPRequest $request HTTP request
     * 
     * @return HTTPResponse
     */
    public function placeorder_full(HTTPRequest $request) : HTTPResponse
    {
        if (!$this->data()->AllowReorder) {
            $this->httpError(403);
        }
        $response = $this->handleOrderDetailAction($request);
        if ($response instanceof HTTPResponse) {
            return $response;
        }
        $this->doPlaceOrder();
        $customer        = Security::getCurrentUser();
        $order           = $this->CustomersOrder();
        $checkout        = Checkout::create_from_session();
        $invoiceAddress  = null;
        $shippingAddress = null;
        foreach ($customer->Addresses() as $address) {
            if ($order->InvoiceAddress()->isEqual($address)) {
                $invoiceAddress = $address;
                break;
            }
        }
        foreach ($customer->Addresses() as $address) {
            if ($order->ShippingAddress()->isEqual($address)) {
                $shippingAddress = $address;
                break;
            }
        }
        $data = [];
        if ($invoiceAddress !== null) {
            $data['InvoiceAddress']  = $invoiceAddress->toMap();
        }
        if ($shippingAddress !== null) {
            $data['ShippingAddress'] = $shippingAddress->toMap();
        }
        $data['ShippingMethod']  = $order->ShippingMethod()->ID;
        $data['PaymentMethod']   = $order->PaymentMethod()->ID;
        $checkout->addData($data);
        if ($invoiceAddress !== null
         && $shippingAddress !== null
        ) {
            CheckoutStep1::create($this)->complete();
            CheckoutStep2::create($this)->complete();
            CheckoutStep3::create($this)->complete();
            CheckoutStep4::create($this)->complete();
        }
        return $this->redirect(CheckoutStep::get()->first()->Link());
    }
    
    /**
     * Places the current order context positions into the shopping cart.
     * 
     * @return void
     */
    protected function doPlaceOrder() : void
    {
        $customer = Security::getCurrentUser();
        $order    = $this->CustomersOrder();
        $cartID   = $customer->getCart()->ID;
        foreach ($order->OrderPositions() as $orderPosition) {
            /* @var $orderPosition \SilverCart\Model\Order\OrderPosition */
            if ($orderPosition->Product()->exists()) {
                $orderPosition->Product()->addToCart($cartID, $orderPosition->Quantity);
            }
        }
    }
    
    /**
     * Handles an order detail response.
     * 
     * @param HTTPRequest $request HTTP request
     * 
     * @return HTTPResponse|null
     */
    protected function handleOrderDetailAction(HTTPRequest $request) : ?HTTPResponse
    {
        $customer = Security::getCurrentUser();
        if (!($customer instanceof Member)) {
            // there is no logged in customer
            return $this->redirect($this->Link());
        }
        $orderID = (int) $request->param('ID');
        $this->setOrderID($orderID);
        // get the order to check whether it is related to the actual customer or not.
        $order = Order::get()->byID($this->getOrderID());
        /* @var $order Order */
        if ($order instanceof Order
         && $order->Member()->exists()
        ) {
            if ($order->Member()->ID != $customer->ID) {
                // the order is not related to the customer, redirect elsewhere...
                return $this->redirect($this->Link());
            }
        } else {
            return $this->redirect($this->Link());
        }
        return null;
    }

    /**
     * returns a single order of a logged in member identified by url param id
     *
     * @return Order|null
     */
    public function CustomersOrder() : ?Order
    {
        return Order::get_by_customer($this->getOrderID());
    }

    /**
     * returns the id of the order requested by the Action.
     *
     * @return int
     */
    public function getOrderID() : int
    {
        return (int) $this->orderID;
    }

    /**
     * sets the id of the order requested by the Action.
     *
     * @param int $orderID orderID
     *
     * @return $this
     */
    public function setOrderID(int $orderID) : OrderHolderController
    {
        $this->orderID = $orderID;
        return $this;
    }

    /**
     * Returns the link to the OrderHolderPage.
     *
     * @return string
     */
    public function OrderHolderLink() : string
    {
        return (string) $this->Parent()->Link();
    }
    
    /**
     * Returns the invoice address of the customers order
     *
     * @return OrderInvoiceAddress|null
     */
    public function getInvoiceAddress() : ?OrderInvoiceAddress
    {
        $address = null;
        $order   = $this->CustomersOrder();
        if ($order instanceof Order) {
            $address = $order->InvoiceAddress();
        }
        return $address;
    }
    
    /**
     * Returns the shipping address of the customers order
     *
     * @return OrderShippingAddress|null
     */
    public function getShippingAddress() : ?OrderShippingAddress
    {
        $address = null;
        $order   = $this->CustomersOrder();
        if ($order instanceof Order) {
            $address = $order->ShippingAddress();
        }
        return $address;
    }
}