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

    public static $singular_name = "";
    public static $can_be_root = false;
    public static $allowed_children = array(
        "OrderDetailPage"
    );

    /**
     * Constructor
     *
     * @param array|null $record      This will be null for a new database record.  Alternatively, you can pass an array of
     *                                field values.  Normally this contructor is only used by the internal systems that get objects from the database.
     * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.  Singletons
     *                                don't have their defaults set.
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 2.2.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        self::$singular_name = _t('OrderHolder.TITLE');
        parent::__construct($record, $isSingleton);
    }
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