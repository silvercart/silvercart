<?php

namespace SilverCart\Model\Pages;

use SilverCart\Model\Order\Order;
use SilverCart\Model\Order\OrderInvoiceAddress;
use SilverCart\Model\Order\OrderShippingAddress;
use SilverCart\Model\Pages\MyAccountHolderController;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Convert;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

/**
 * OrderHolder Controller class.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
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
    ];
    
    /**
     * Action to show an orders details.
     * 
     * @param HTTPRequest $request HTTP request
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 11.06.2018
     */
    public function detail(HTTPRequest $request)
    {
        $customer = Security::getCurrentUser();
        if (!($customer instanceof Member)) {
            // there is no logged in customer
            $this->redirect($this->Link());
            return;
        }
        $orderID = $request->param('ID');
        $this->setOrderID($orderID);
        // get the order to check whether it is related to the actual customer or not.
        $order = Order::get()->byID($this->getOrderID());
        /* @var $order Order */
        if ($order instanceof Order
         && $order->Member()->exists()
        ) {
            if ($order->Member()->ID != $customer->ID) {
                // the order is not related to the customer, redirect elsewhere...
                $this->redirect($this->Link());
            }
        } else {
            $this->redirect($this->Link());
        }
        return $this->render();
    }

    /**
     * returns a single order of a logged in member identified by url param id
     *
     * @return Order|null
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.09.2017
     */
    public function CustomersOrder() : ?Order
    {
        return Order::get_by_customer(Convert::raw2sql($this->getOrderID()));
    }

    /**
     * returns the id of the order requested by the Action.
     *
     * @return int
     */
    public function getOrderID() : int
    {
        return $this->orderID;
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
        return $this->CustomersOrder()->InvoiceAddress();
    }
    
    /**
     * Returns the shipping address of the customers order
     *
     * @return OrderShippingAddress|null
     */
    public function getShippingAddress() : ?OrderShippingAddress
    {
        return $this->CustomersOrder()->ShippingAddress();
    }
}