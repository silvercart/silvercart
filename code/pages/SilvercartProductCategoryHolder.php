<?php

/**
 * gathers all product categories
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 * @since 23.10.2010
 */
class SilvercartProductCategoryHolder extends Page {

    public static $singular_name = "";
    public static $allowed_children = array(
        'SilvercartProductCategoryPage'
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

        $records = DataObject::get_one('SilvercartProductCategoryHolder');
        if (!$records) {
            $page = new SilvercartProductCategoryHolder();
            $page->Title = _t('SilvercartProductCategoryHolder.TITLE', 'category overview');
            $page->URLSegment = _t('SilvercartProductCategoryHolder.URL_SEGMENT', 'categoryoverview');
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
class SilvercartProductCategoryHolder_Controller extends Page_Controller {

}