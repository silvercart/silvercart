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
class SilvercartMetaNavigationHolder extends Page {

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

        $record = DataObject::get_one('SilvercartMetaNavigationHolder');
        if (!$record) {
            $page = new SilvercartMetaNavigationHolder();
            $page->Title = _t('SilvercartMetaNavigationHolder.SINGULARNAME');
            $page->URLSegment = _t('SilvercartMetaNavigationHolder.URL_SEGMENT', 'metanavigation');
            $page->Status = "Published";
            $page->ShowInMenus = 0;
            $page->write();
            $page->publish("Stage", "Live");

            //The page "metanavigation" has also three children.
            $contactPage = new SilvercartContactFormPage();
            $contactPage->Title = _t('SilvercartContactFormPage.TITLE', 'contact');
            $contactPage->URLSegment = _t('SilvercartContactFormPage.URL_SEGMENT', 'contact');
            $contactPage->Status = "Published";
            $contactPage->ShowInMenus = 1;
            $contactPage->ParentID = $page->ID;
            $contactPage->write();
            $contactPage->publish("Stage", "Live");

            $termsOfServicePage = new Page();
            $termsOfServicePage->Title = _t('SilvercartPage.TITLE_TERMS', 'terms of service');
            $termsOfServicePage->URLSegment = _t('SilvercartPage.URL_SEGMENT_TERMS', 'terms-of-service');
            $termsOfServicePage->Status = "Published";
            $termsOfServicePage->ShowInMenus = 1;
            $termsOfServicePage->ParentID = $page->ID;
            $termsOfServicePage->write();
            $termsOfServicePage->publish("Stage", "Live");

            $imprintPage = new Page();
            $imprintPage->Title = _t('SilvercartPage.TITLE_IMPRINT', 'imprint');
            $imprintPage->URLSegment = _t('SilvercartPage.URL_SEGMENT_IMPRINT', 'imprint');
            $imprintPage->Status = "Published";
            $imprintPage->ShowInMenus = 1;
            $imprintPage->ParentID = $page->ID;
            $imprintPage->write();
            $imprintPage->publish("Stage", "Live");

            $dataPrivacyStatementPage = new SilvercartDataPrivacyStatementPage();
            $dataPrivacyStatementPage->Title = _t('SilvercartDataPrivacyStatementPage.TITLE', 'data privacy statement');
            $dataPrivacyStatementPage->URLSegment = _t('SilvercartDataPrivacyStatementPage.URL_SEGMENT','data-privacy-statement');
            $dataPrivacyStatementPage->Status = "Published";
            $dataPrivacyStatementPage->ShowInMenus = 1;
            $dataPrivacyStatementPage->ParentID = $page->ID;
            $dataPrivacyStatementPage->write();
            $dataPrivacyStatementPage->publish("Stage", "Live");

            $shippingFeesPage = new SilvercartShippingFeesPage();
            $shippingFeesPage->Title = _t('SilvercartShippingFeesPage.TITLE', 'shipping fees');
            $shippingFeesPage->URLSegment = _t('SilvercartShippingFeesPage.URL_SEGMENT', 'shipping-fees');
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
class SilvercartMetaNavigationHolder_Controller extends Page_Controller {
    
}