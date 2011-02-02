<?php

/**
 * holder for customers private area
 * 
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 * @since 23.10.2010
 */
class MyAccountHolder extends Page {

    public static $singular_name = "Accountseite";
    
    public static $allowed_children = array(
        "DataPage",
        "OrderHolder",
        "AddressHolder"
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
        self::$singular_name = _t('MyAccountHolder.SINGULARNAME', 'account page');
        parent::__construct($record, $isSingleton);
    }

    /**
     * default instances related to $this
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @return void
     * @since 23.10.2010
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        $records = DataObject::get_one($this->ClassName);
        if (!$records) {
            $page = new $this->ClassName();
            $page->Title = _t('MyAccountHolder.TITLE', 'my account');
            $page->URLSegment = _t('MyAccountHolder.URL_SEGMENT', 'my account');
            $page->Status = "Published";
            $page->ShowInMenus = false;
            $page->ShowInSearch = false;
            $page->CanViewType = "LoggedInUsers";
            $page->write();
            $page->publish("Stage", "Live");

            /**
             * Create a DataPage as child of $this
             */
            $dataPage = new DataPage();
            $dataPage->Title = _t('DataPage.TITLE', 'my data');
            $dataPage->URLSegment = _t('DataPage.URL_SEGMENT', 'my-data');
            $dataPage->Status = "Published";
            $dataPage->ShowInMenus = false;
            $dataPage->ShowInSearch = false;
            $dataPage->CanViewType = "LoggedInUsers";
            $dataPage->ParentID = $page->ID;
            $dataPage->write();
            $dataPage->publish("Stage", "Live");

            /*
             * Create a OrderHolder as child of $this
             */
            $orderHolder = new OrderHolder();
            $orderHolder->Title = _t('OrderHolder.TITLE', 'my oders');
            $orderHolder->URLSegment = _t('OrderHolder.URL_SEGMENT', 'my-oders');
            $orderHolder->Status = "Published";
            $orderHolder->ShowInMenus = true;
            $orderHolder->ShowInSearch = false;
            $orderHolder->CanViewType = "LoggedInUsers";
            $orderHolder->ParentID = $page->ID;
            $orderHolder->write();
            $orderHolder->publish("Stage", "Live");

            /**
             * Create a OrderDetailPage as child of OrderHolder
             */
            $orderDetailPage = new OrderDetailPage();
            $orderDetailPage->Title = _t('OrderDetailPage.TITLE', 'order details');
            $orderDetailPage->URLSegment = _t('OrderDetailPage.URL_SEGMENT', 'order-details');
            $orderDetailPage->Status = "Published";
            $orderDetailPage->ShowInMenus = true;
            $orderDetailPage->ShowInSearch = false;
            $orderDetailPage->CanViewType = "LoggedInUsers";
            $orderDetailPage->ParentID = $orderHolder->ID;
            $orderDetailPage->write();
            $orderDetailPage->publish("Stage", "Live");

            /**
             * Create a AddressHolder as child of $this
             */
            $addressHolder = new AddressHolder();
            $addressHolder->Title = _t('AddressHolder.TITLE', 'address overview');
            $addressHolder->URLSegment = _t('AddressHolder.URL_SEGMENT', 'address-overview');
            $addressHolder->Status = "Published";
            $addressHolder->ShowInMenus = true;
            $addressHolder->ShowInSearch = false;
            $addressHolder->CanViewType = "LoggedInUsers";
            $addressHolder->ParentID = $page->ID;
            $addressHolder->write();
            $addressHolder->publish("Stage", "Live");

            /**
             * Create a AddressPage as a child of AddressHolder
             */
            $addressPage = new AddressPage();
            $addressPage->Title = _t('AddressPage.TITLE', 'address details');
            $addressPage->URLSegment = _t('AddressPage.URL_SEGMENT', 'address-details');
            $addressPage->Status = "Published";
            $addressPage->ShowInMenus = true;
            $addressPage->ShowInSearch = false;
            $addressPage->CanViewType = "LoggedInUsers";
            $addressPage->ParentID = $addressHolder->ID;
            $addressPage->write();
            $addressPage->publish("Stage", "Live");
        }
    }

}

/**
 * correlating controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 * @since 23.10.2010
 */
class MyAccountHolder_Controller extends Page_Controller {

    /**
     * statements to be called on object initialisation
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.11.2010
     * @return void
     */
    public function  init() {
        Session::clear("redirect"); //if customer has been to the checkout yet this is set to direct him back to the checkout after address editing
        parent::init();
    }
}