<?php

/**
 * This site is not visible in the frontend.
 * Its purpose is to gather the meta navigation sites in the backend for better usability.
 * Now a shop admin has a correspondence between front end site order and backend tree structure.
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 23.10.2010
 * @license BSD
 */
class MetaNavigationHolder extends Page {

    public static $singular_name = "Metanavigation";

    /**
     * For this page type is one entry with this exact URLSegment needed.
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     * @return void
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        $record = DataObject::get_one($this->ClassName, "`URLSegment` = 'metanavigation'");
        if (!$record) {
            $page = new $this->ClassName();
            $page->Title = "Metanavigation";
            $page->URLSegment = "metanavigation";
            $page->Status = "Published";
            $page->ShowInMenus = 0;
            $page->write();
            $page->publish("Stage", "Live");

            //The page "metanavigation" has also three children.
            $contactPage = new ContactFormPage();
            $contactPage->Title = "Kontakt";
            $contactPage->URLSegment = "kontakt";
            $contactPage->Status = "Published";
            $contactPage->ShowInMenus = 1;
            $contactPage->ParentID = $page->ID;
            $contactPage->write();
            $contactPage->publish("Stage", "Live");

            $termsOfServicePage = new Page();
            $termsOfServicePage->Title = "AGB";
            $termsOfServicePage->URLSegment = "agb";
            $termsOfServicePage->Status = "Published";
            $termsOfServicePage->ShowInMenus = 1;
            $termsOfServicePage->ParentID = $page->ID;
            $termsOfServicePage->write();
            $termsOfServicePage->publish("Stage", "Live");

            $imprintPage = new Page();
            $imprintPage->Title = "Impressum";
            $imprintPage->URLSegment = "impressum";
            $imprintPage->Status = "Published";
            $imprintPage->ShowInMenus = 1;
            $imprintPage->ParentID = $page->ID;
            $imprintPage->write();
            $imprintPage->publish("Stage", "Live");

            $shippingFeesPage = new ShippingFeesPage();
            $shippingFeesPage->Title = "Versandkosten";
            $shippingFeesPage->URLSegment = "versandkosten";
            $shippingFeesPage->Status = "Published";
            $shippingFeesPage->ShowInMenus = 1;
            $shippingFeesPage->ParentID = $page->ID;
            $shippingFeesPage->write();
            $shippingFeesPage->publish("Stage", "Live");
        }
    }

}
/**
 * correlating controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class MetaNavigationHolder_Controller extends Page_Controller {
    
}