<?php

/**
 * page type for a contact form response
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 21.10.2010
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartContactFormResponsePage extends Page {

    public static $singular_name = "contact form response page";
    public static $allowed_children = array(
        'none'
    );

    /**
     * default instance of this page type; You may change the URLSegment without any cause.
     *
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 21.10.2010
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        $records = DataObject::get_one('SilvercartContactFormResponsePage');
        if (!$records) {
            $page = new SilvercartContactFormResponsePage();
            $page->Title = _t('SilvercartContactFormResponsePage.CONTACT_CONFIRMATION', 'contact confirmation');
            $page->URLSegment = _t('SilvercartContactFormResponsePage.URL_SEGMENT', 'contactconfirmation');
            $page->Status = "Published";
            $page->ShowInMenus = false;
            $page->ShowInSearch = false;
            $page->write();
            $page->publish("Stage", "Live");
        }
    }

}

/**
 * controller peer
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 * @since 21.10.2010
 */
class SilvercartContactFormResponsePage_Controller extends Page_Controller {

}