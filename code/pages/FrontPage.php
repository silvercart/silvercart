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

    public static $singular_name = "Frontpage";
    public static $plural_name = "Frontpages";

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
        $frontPage->Content = '<h2>Willkommen im <strong>SilverCart</strong> Webshop!</h2>';
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