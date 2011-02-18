<?php

/**
 * Displays the order details after order submission. The order will be identified via session ID
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 18.11.2010
 * @license BSD
 */
class SilvercartOrderConfirmationPage extends Page {

    public static $singular_name = "";
    public static $allowed_children = array(
        'none'
    );
}

/**
 * corresponding controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 18.11.2010
 * @license BSD
 */
class SilvercartOrderConfirmationPage_Controller extends Page_Controller {

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
            $order = DataObject::get_one('SilvercartOrder', $filter);
            return $order;
        }
    }
}