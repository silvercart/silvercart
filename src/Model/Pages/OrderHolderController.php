<?php

namespace SilverCart\Model\Pages;

use SilverCart\Model\Order\Order;
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
class OrderHolderController extends MyAccountHolderController {

    /**
     * ID of the requested order
     *
     * @var int 
     */
    protected $orderID;
    
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
    public function detail(HTTPRequest $request) {
        $orderID = $request->param('ID');
        $this->setOrderID($orderID);
        // get the order to check whether it is related to the actual customer or not.
        $order = Order::get()->byID($this->getOrderID());
        /* @var $order Order */
        if ($order instanceof Order &&
            $order->Member()->exists()) {
            $customer = Security::getCurrentUser();
            if (!($customer instanceof Member) ||
                $order->Member()->ID != $customer->ID) {
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
     * @return Order
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.09.2017
     */
    public function CustomersOrder() {
        return Order::get_by_customer(Convert::raw2sql($this->getOrderID()));
    }

    /**
     * returns the id of the order requested by the Action.
     *
     * @return int
     */
    public function getOrderID() {
        return $this->orderID;
    }

    /**
     * sets the id of the order requested by the Action.
     *
     * @param int $orderID orderID
     *
     * @return void
     */
    public function setOrderID($orderID) {
        $this->orderID = $orderID;
    }

    /**
     * Returns the link to the OrderHolderPage.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.04.2011
     */
    public function OrderHolderLink() {
        return $this->Parent()->Link();
    }
    
    /**
     * Returns the invoice address of the customers order
     *
     * @return OrderInvoiceAddress 
     */
    public function getInvoiceAddress() {
        return $this->CustomersOrder()->InvoiceAddress();
    }
    
    /**
     * Returns the shipping address of the customers order
     *
     * @return OrderShippingAddress 
     */
    public function getShippingAddress() {
        return $this->CustomersOrder()->ShippingAddress();
    }
}