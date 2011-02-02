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

    public static $singular_name = "";
    public static $allowed_children = array(
        'none'
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
        self::$singular_name = _t('OrderConfirmationPage.SINGULARNAME', 'order conirmation page');
        parent::__construct($record, $isSingleton);
    }

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
            $page->Title = _t('OrderConfirmationPage.SINGULARNAME', 'order conirmation page');
            $page->URLSegment = _t('OrderConfirmationPage.URL_SEGMENT', 'order-conirmation');
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