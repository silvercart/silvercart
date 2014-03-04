<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * show details of a customers orders
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 20.10.2010
 * @license see license file in modules root directory
 */
class SilvercartOrderDetailPage extends SilvercartMyAccountHolder {
    
    /**
     * Indicates whether this page type can be root
     *
     * @var bool
     */
    public static $can_be_root = false;
    
    /**
     * The icon to use for this page in the storeadmin sitetree.
     *
     * @var string
     */
    public static $icon = "silvercart/images/page_icons/my_account_holder";

    /**
     * configure the class name of the DataObjects to be shown on this page
     *
     * @return string class name of the DataObject to be shown on this page
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 3.11.2010
     */
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
    }
    
    /**
     * section identifier
     *
     * @return void
     */
    public function getSection() {
        return 'SilvercartOrder';
    }

    /**
     * Returns the link to this detail page.
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.01.2012
     */
    public function Link() {
        $controller = Controller::curr();
        $link       = parent::Link();

        if ($controller instanceof SilvercartOrderDetailPage_Controller) {
            $link .= $controller->getOrderID();
        }

        return $link;
    }
}

/**
 * Controller of this page type
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 08.04.2013
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartOrderDetailPage_Controller extends SilvercartMyAccountHolder_Controller {

    /**
     * ID of the requested order
     *
     * @var int 
     */
    protected $orderID;
    
    private static $allowed_actions = array(
        'detail',
    );

    /**
     * statements to be called on instanciation
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 25.10.2010
     * @return void
     */
    public function init() {
        $orderID = $this->getRequest()->param('ID');
        $this->setOrderID($orderID);
        $this->setBreadcrumbElementID($orderID);
        // get the order to check whether it is related to the actual customer or not.
        $order = DataObject::get_by_id('SilvercartOrder', $this->getOrderID());
       
        if ($order && $order->MemberID > 0) {
            if ($order->Member()->ID != Member::currentUserID()) {
                // the order is not related to the customer, redirect elsewhere...
                $this->redirect($this->Parent()->Link());
            }
        } else {
            $this->redirect($this->Parent()->Link());
        }
        parent::init();
    }

    /**
     * returns a single order of a logged in member identified by url param id
     *
     * @return DataObject Order object
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.02.2011
     */
    public function CustomersOrder() {
        $id         = Convert::raw2sql($this->getOrderID());
        $memberID   = Member::currentUserID();
        $order      = false;
        
        if ($memberID && $id) {
            $order = DataObject::get_one(
                'SilvercartOrder',
                sprintf(
                    "\"ID\"= '%s' AND \"MemberID\" = '%s'",
                    $id,
                    $memberID
                )
            );

            return $order;
        }
    }

    /**
     * handles the given action. If the action is a numeric value, an order is
     * requested. This method manipulates the handling for this case.
     *
     * @param SS_HTTPRequest $request Request
     * @param string         $action  Action
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2013
     */
    public function handleAction($request, $action) {
        if (!$this->hasMethod($request->param('Action'))) {
            $secondaryAction = $request->param('ID');
            if ($this->hasMethod($secondaryAction) &&
                $this->hasAction($secondaryAction)) {
                $result = $this->{$secondaryAction}($request);
                if (is_array($result)) {
                    return $this->getViewer($this->action)->process($this->customise($result));
                } else {
                    return $result;
                }
            } elseif ($request->param('Action') == 'detail' &&
                      is_numeric($request->param('ID'))) {
                return $this->getViewer('index')->process($this);
            }
        }
        return parent::handleAction($request, $action);
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
     * Returns the link to the SilvercartOrderHolderPage.
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
     * @return SilvercartOrderInvoiceAddress 
     */
    public function getInvoiceAddress() {
        return $this->CustomersOrder()->SilvercartInvoiceAddress();
    }
    
    /**
     * Returns the shipping address of the customers order
     *
     * @return SilvercartOrderShippingAddress 
     */
    public function getShippingAddress() {
        return $this->CustomersOrder()->SilvercartShippingAddress();
    }
}
