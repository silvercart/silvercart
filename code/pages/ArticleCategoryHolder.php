<?php

/**
 * gathers all article categories
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 * @since 23.10.2010
 */
class ArticleCategoryHolder extends Page {

    public static $singular_name = "Kategorieübersicht";
    public static $allowed_children = array(
        'ArticleCategoryPage'
    );

    /**
     * creates default instances related to $this
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     * @return void
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        $records = DataObject::get_one($this->ClassName);
        if (!$records) {
            $page = new $this->ClassName();
            $page->Title = "Kategorienübersicht";
            $page->URLSegment = "kategorienuebersicht";
            $page->Status = "Published";
            $page->ShowInMenus = true;
            $page->ShowInSearch = true;
            $page->write();
            $page->publish("Stage", "Live");
        }
    }

}

/**
 * correlated controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class ArticleCategoryHolder_Controller extends Page_Controller {

}