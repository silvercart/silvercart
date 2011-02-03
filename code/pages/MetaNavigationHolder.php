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

        $record = DataObject::get_one($this->ClassName);
        if (!$record) {
            $page = new $this->ClassName();
            $page->Title = _t('MetaNavigationHolder.SINGULARNAME');
            $page->URLSegment = _t('MetaNavigationHolder.URL_SEGMENT', 'metanavigation');
            $page->Status = "Published";
            $page->ShowInMenus = 0;
            $page->write();
            $page->publish("Stage", "Live");

            //The page "metanavigation" has also three children.
            $contactPage = new ContactFormPage();
            $contactPage->Title = _t('ContactFormPage.TITLE', 'contact');
            $contactPage->URLSegment = _t('ContactFormPage.URL_SEGMENT', 'contact');
            $contactPage->Status = "Published";
            $contactPage->ShowInMenus = 1;
            $contactPage->ParentID = $page->ID;
            $contactPage->write();
            $contactPage->publish("Stage", "Live");

            $termsOfServicePage = new Page();
            $termsOfServicePage->Title = _t('Page.TITLE_TERMS', 'terms of service');
            $termsOfServicePage->URLSegment = _t('Page.URL_SEGMENT_TERMS', 'terms-of-service');
            $termsOfServicePage->Status = "Published";
            $termsOfServicePage->ShowInMenus = 1;
            $termsOfServicePage->ParentID = $page->ID;
            $termsOfServicePage->write();
            $termsOfServicePage->publish("Stage", "Live");

            $imprintPage = new Page();
            $imprintPage->Title = _t('Page.TITLE_IMPRINT', 'imprint');
            $imprintPage->URLSegment = _t('Page.URL_SEGMENT_IMPRINT', 'imprint');
            $imprintPage->Status = "Published";
            $imprintPage->ShowInMenus = 1;
            $imprintPage->ParentID = $page->ID;
            $imprintPage->write();
            $imprintPage->publish("Stage", "Live");

            $dataPrivacyStatementPage = new DataPrivacyStatementPage();
            $dataPrivacyStatementPage->Title = _t('DataPrivacyStatementPage.TITLE', 'data privacy statement');
            $dataPrivacyStatementPage->URLSegment = _t('DataPrivacyStatementPage.URL_SEGMENT','data-privacy-statement');
            $dataPrivacyStatementPage->Status = "Published";
            $dataPrivacyStatementPage->ShowInMenus = 1;
            $dataPrivacyStatementPage->ParentID = $page->ID;
            $dataPrivacyStatementPage->write();
            $dataPrivacyStatementPage->publish("Stage", "Live");

            $shippingFeesPage = new ShippingFeesPage();
            $shippingFeesPage->Title = _t('ShippingFeesPage.TITLE', 'shipping fees');
            $shippingFeesPage->URLSegment = _t('ShippingFeesPage.URL_SEGMENT', 'shipping-fees');
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