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

    public static $singular_name = "Account holder";
    public static $allowed_children = array(
        "DataPage",
        "OrderHolder",
        "AddressHolder"
    );

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

            //create or load b2b group for pages access rights
            if (!DataObject::get_one('Group', "\"Code\" = 'b2b'")) {
                $b2b_group = new Group();
                $b2b_group->Title = _t('BusinessCustomer.BUSINESSCUSTOMER', 'business customer');
                $b2b_group->Code = "b2b";
                $b2b_group->write();
            } else {
                $b2b_group = DataObject::get_one('Group', "\"Code\" = 'b2b'");
            }

            //create or load b2c group for pages access rights
            if (!DataObject::get_one('Group', "\"Code\" = 'b2c'")) {
                $b2c_group = new Group();
                $b2c_group->Title = _t('RegularCustomer.REGULARCUSTOMER', 'regular customer');
                $b2c_group->Code = "b2c";
                $b2c_group->write();
            } else {
                $b2c_group = DataObject::get_one('Group', "\"Code\" = 'b2c'");
            }

            $page = new $this->ClassName();
            $page->Title = _t('MyAccountHolder.TITLE', 'my account');
            $page->URLSegment = 'my-account';
            $page->Status = "Published";
            $page->ShowInMenus = false;
            $page->ShowInSearch = false;
            $page->CanViewType = "OnlyTheseUsers";
            $page->write();
            $page->publish("Stage", "Live");
            $page->ViewerGroups()->add($b2b_group);
            $page->ViewerGroups()->add($b2c_group);

            /**
             * Create a DataPage as child of $this
             */
            $dataPage = new DataPage();
            $dataPage->Title = _t('DataPage.TITLE', 'my data');
            $dataPage->URLSegment = _t('DataPage.URL_SEGMENT', 'my-data');
            $dataPage->Status = "Published";
            $dataPage->ShowInMenus = true;
            $dataPage->ShowInSearch = false;
            $dataPage->CanViewType = "Inherit";
            $dataPage->ParentID = $page->ID;
            $dataPage->write();
            $dataPage->publish("Stage", "Live");

            /*
             * Create a OrderHolder as child of $this
             */
            $orderHolder = new OrderHolder();
            $orderHolder->Title = _t('OrderHolder.TITLE', 'my oders');
            $orderHolder->URLSegment = 'my-oders';
            $orderHolder->Status = "Published";
            $orderHolder->ShowInMenus = true;
            $orderHolder->ShowInSearch = false;
            $orderHolder->CanViewType = "Inherit";
            $orderHolder->ParentID = $page->ID;
            $orderHolder->write();
            $orderHolder->publish("Stage", "Live");

            /**
             * Create a OrderDetailPage as child of OrderHolder
             */
            $orderDetailPage = new OrderDetailPage();
            $orderDetailPage->Title = _t('OrderDetailPage.TITLE', 'order details');
            $orderDetailPage->URLSegment = 'order-details';
            $orderDetailPage->Status = "Published";
            $orderDetailPage->ShowInMenus = false;
            $orderDetailPage->ShowInSearch = false;
            $orderDetailPage->CanViewType = "Inherit";
            $orderDetailPage->ParentID = $orderHolder->ID;
            $orderDetailPage->write();
            $orderDetailPage->publish("Stage", "Live");

            /**
             * Create a AddressHolder as child of $this
             */
            $addressHolder = new AddressHolder();
            $addressHolder->Title = _t('AddressHolder.TITLE', 'address overview');
            $addressHolder->URLSegment = 'address-overview';
            $addressHolder->Status = "Published";
            $addressHolder->ShowInMenus = true;
            $addressHolder->ShowInSearch = false;
            $addressHolder->CanViewType = "Inherit";
            $addressHolder->ParentID = $page->ID;
            $addressHolder->write();
            $addressHolder->publish("Stage", "Live");

            /**
             * Create a AddressPage as a child of AddressHolder
             */
            $addressPage = new AddressPage();
            $addressPage->Title = _t('AddressPage.TITLE', 'address details');
            $addressPage->URLSegment = 'address-details';
            $addressPage->Status = "Published";
            $addressPage->ShowInMenus = false;
            $addressPage->ShowInSearch = false;
            $addressPage->CanViewType = "Inherit";
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
    public function init() {
        Session::clear("redirect"); //if customer has been to the checkout yet this is set to direct him back to the checkout after address editing
        parent::init();
    }

}