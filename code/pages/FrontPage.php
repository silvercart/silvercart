<?php

/**
 * represents a shopping cart. Every customer has one initially.
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license BSD
 * @since 23.10.2010
 */
class FrontPage extends Page {

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
        self::$singular_name = _t('FrontPage.SINGULARNAME', 'front page');
        parent::__construct($record, $isSingleton);
    }

    /**
     * creates a default silvercart startpage.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>, Sascha Köhler <skoehler@pixeltricks.de>
     * @since 28.01.2011
     */
    public function  requireDefaultRecords() {
        parent::requireDefaultRecords();
        if (SiteTree::get_by_link('home')) {
            return;
        }
        $frontPage = new FrontPage();
        $frontPage->URLSegment = 'home';
        $frontPage->ShowInMenue = true;
        $frontPage->ShowInSearch = true;
        $frontPage->Title = 'Start';
        $frontPage->Content = _t('FrontPage.DEFAULT_CONTENT', '<h2>Welcome to <strong>SilverCart</strong> Webshop!</h2>');
        $frontPage->write();
        $frontPage->publish("Stage", "Live");
    }

}
/**
 * related controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class FrontPage_Controller extends Page_Controller {

}