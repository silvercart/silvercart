<?php

/**
 * collects all pages of the footer navigation: about, ...
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 20.10.2010
 * @license BSD
 */
class FooterNavigationHolder extends Page {

    public static $singular_name = "Footernavigation";

    /**
     * Default entries for a fresh installation
     * Child pages are also created
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.10.2010
     * @return void
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        $record = DataObject::get_one($this->ClassName, "\"URLSegment\" = 'footernavigation'");
        if (!$record) {
            $page = new $this->ClassName();
            $page->Title = "Footernavigation";
            $page->URLSegment = "footernavigation";
            $page->Status = "Published";
            $page->ShowInMenus = 0;
            $page->write();
            $page->publish("Stage", "Live");

            /**
             * This page type has default children
             */
            $aboutPage = new Page();
            $aboutPage->Title = "Über uns";
            $aboutPage->URLSegment = "ueber-uns";
            $aboutPage->Status = "Published";
            $aboutPage->ShowInMenus = 1;
            $aboutPage->ParentID = $page->ID;
            $aboutPage->write();
            $aboutPage->publish("Stage", "Live");
        }
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
class FooterNavigationHolder_Controller extends Page_Controller {

}