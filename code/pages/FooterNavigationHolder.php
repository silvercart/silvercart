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

    public static $singular_name = "";

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
        self::$singular_name = _t('FooterNavigationHolder.SINGULARNAME', 'footer navigation');
        parent::__construct($record, $isSingleton);
    }

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

        $record = DataObject::get_one($this->ClassName);
        if (!$record) {
            $page = new $this->ClassName();
            $page->Title = _t('FooterNavigationHolder.SINGULARNAME');
            $page->URLSegment = _t('FooterNavigationHolder.URL_SEGMENT', 'footernavigation');
            $page->Status = "Published";
            $page->ShowInMenus = 0;
            $page->write();
            $page->publish("Stage", "Live");

            /**
             * This page type has default children
             */
            $aboutPage = new Page();
            $aboutPage->Title = _t('Page.ABOUT_US', 'about us');
            $aboutPage->URLSegment = _t('Page.ABOUT_US_URL_SEGMENT', 'about-us');
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