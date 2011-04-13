<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
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
 * @copyright Pixeltricks GmbH
 * @since 20.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartOrderDetailPage extends SilvercartMyAccountHolder {

    public static $singular_name = "";
    public static $can_be_root = false;

    /**
     * configure the class name of the DataObjects to be shown on this page
     *
     * @return string class name of the DataObject to be shown on this page
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 3.11.2010
     */
    public function getSection() {
        return 'SilvercartOrder';
    }
}

/**
 * Controller of this page type
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 19.10.2010
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartOrderDetailPage_Controller extends SilvercartMyAccountHolder_Controller {

    protected $orderID;

    /**
     * statements to be called on instanciation
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 25.10.2010
     * @return void
     */
    public function init() {
        $this->setOrderID($this->urlParams['Action']);
        $this->setBreadcrumbElementID($this->urlParams['Action']);
        // get the order to check whether it is related to the actual customer or not.
        $order = DataObject::get_by_id('SilvercartOrder', $this->getOrderID());
        if ($order->Member()->ID != Member::currentUserID()) {
            // the order is not related to the customer, redirect elsewhere...
            Director::redirect($this->PageByIdentifierCode()->Link());
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
                    "`ID`= '%s' AND `MemberID` = '%s'",
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
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.02.2011
     */
    public function handleAction($request) {
        if (!$this->hasMethod($this->urlParams['Action'])) {
            if (is_numeric($this->urlParams['Action'])) {
                return $this->getViewer('index')->process($this);
            }
        }
        return parent::handleAction($request);
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
     * @copyright 2011 pixeltricks GmbH
     * @since 13.04.2011
     */
    public function OrderHolderLink() {
        return $this->Parent()->Link();
    }
}
