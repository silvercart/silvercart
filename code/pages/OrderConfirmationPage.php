<?php

/**
 * Displays the order details after order submission. The order will be identified via session ID
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 18.11.2010
 * @license BSD
 */
class OrderConfirmationPage extends Page {

    public static $singular_name = "Bestellbestätigungsseite";
    public static $allowed_children = array(
        'none'
    );

    /**
     * default instances related to $this
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @return void
     * @since 18.11.2010
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        $records = DataObject::get_one($this->ClassName);
        if (!$records) {
            $page = new $this->ClassName();
            $page->Title = "Bestellbestätigung";
            $page->URLSegment = "bestellbestaetigung";
            $page->Status = "Published";
            $page->ShowInMenus = false;
            $page->ShowInSearch = false;
            $page->CanViewType = "LoggedInUsers";
            $page->write();
            $page->publish("Stage", "Live");
        }
    }
}

/**
 * corresponding controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 18.11.2010
 * @license BSD
 */
class OrderConfirmationPage_Controller extends Page_Controller {

    /**
     * returns an order identified by session id
     *
     * @return Order order or false
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.11.2010
     */
    public function CustomersOrder() {
        $id = Session::get('OrderIdForConfirmation');
        $memberID = Member::currentUserID();
        if ($id && $memberID) {
            $filter = sprintf("`ID`= '%s' AND `customerID` = '%s'", $id, $memberID);
            $order = DataObject::get_one('Order', $filter);
            return $order;
        }
    }
}