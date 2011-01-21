<?php

/**
 * shows an overview of a customers orders
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 20.10.2010
 * @license BSD
 */
class OrderHolder extends Page {

    public static $singular_name = "Bestellübersicht";
    public static $plural_name = "Bestellübersichten";
    public static $can_be_root = false;
    public static $allowed_children = array(
        "OrderDetailPage"
    );
}

/**
 * Controller of this page type
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @since 19.10.2010
 * @copyright 2010 pixeltricks GmbH
 */
class OrderHolder_Controller extends Page_Controller {

    /**
     * template function: returns customers orders
     *
     * @since 27.10.10
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @return DataObjectSet DataObjectSet with order objects
     */
    public function CurrentMembersOrders() {
        $memberID = Member::currentUserID();
        if ($memberID) {
            $filter = sprintf("`customerID` = '%s'", $memberID);
            $orders = DataObject::get('Order', $filter);
            return $orders;
        }
    }
}