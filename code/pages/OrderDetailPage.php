<?php

/**
 * show details of a customers orders
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 20.10.2010
 * @license BSD
 */
class OrderDetailPage extends Page {

    public static $singular_name = "";
    public static $can_be_root = false;

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
        self::$singular_name = _t('OrderDetailPage.TITLE');
        parent::__construct($record, $isSingleton);
    }

    /**
     * configure the class name of the DataObjects to be shown on this page
     *
     * @return string class name of the DataObject to be shown on this page
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 3.11.2010
     */
    public function getSection() {
        return 'Order';
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
class OrderDetailPage_Controller extends Page_Controller {

    /**
     * returns a single order of a logged in member identified by url param id
     *
     * @return DataObject Order object
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 27.10.10
     */
    public function CustomersOrder() {
        $id = $this->urlParams['ID'];
        $memberID = Member::currentUserID();
        if ($id) {
            $filter = sprintf("`ID`= '%s' AND `customerID` = '%s'", $id, $memberID);
            $order = DataObject::get_one('Order', $filter);
            return $order;
        }
    }
}