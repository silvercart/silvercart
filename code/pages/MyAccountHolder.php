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
    public static $plural_name = "Accountseiten";
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
            $page = new $this->ClassName();
            $page->Title = "Mein Konto";
            $page->URLSegment = "meinkonto";
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
            $dataPage->Title = "Meine Daten";
            $dataPage->URLSegment = "meinedaten";
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
            $orderHolder->Title = "Bestellübersicht";
            $orderHolder->URLSegment = "bestelluebersicht";
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
            $orderDetailPage->Title = "Bestellansicht";
            $orderDetailPage->URLSegment = "bestellansicht";
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
            $addressHolder->Title = "Adressübersicht";
            $addressHolder->URLSegment = "adressuebersicht";
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
            $addressPage->Title = "Adressansicht";
            $addressPage->URLSegment = "adressansicht";
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