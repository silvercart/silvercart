<?php

/**
 * page type to display search results
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license LGPL
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartSearchResultsPage extends Page {

    public static $singular_name = "";
    public static $allowed_children = array(
        'none'
    );

    /** 
     * default instances related to $this
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     * @return void
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();
        $records = DataObject::get_one('SilvercartSearchResultsPage');
        if (!$records) {
            $page = new SilvercartSearchResultsPage();
            $page->Title = _t('SilvercartSearchResultsPage.TITLE', 'search results');
            $page->URLSegment = _t('SilvercartSearchResultsPage.URL_SEGMENT', 'search-results');
            $page->Status = "Published";
            $page->ShowInMenus = false;
            $page->ShowInSearch = false;
            $page->write();
            $page->publish("Stage", "Live");
        }
    }
}

/**
 * correlating controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license LGPL
 * @copyright 2010 pixeltricks GmbH
 * @since 23.10.2010
 */
class SilvercartSearchResultsPage_Controller extends Page_Controller {

    protected $searchResultProducts;

    /**
     * Diese Funktion wird beim Initialisieren ausgeführt
     *
     * @return <type>
     *
     * @author Oliver Scheer <oscheer@pixeltricks.de>
     * @since 11.11.2010
     */
    public function init() {
        parent::init();
        $var = Convert::raw2sql(Session::get('searchQuery')); // input data must be secured
        $whereClause = sprintf("`Title` LIKE '%%%s%%' OR `ShortDescription` LIKE '%%%s%%' OR `LongDescription` LIKE '%%%s%%' OR `MetaKeywords` LIKE '%%%s%%'", $var,$var,$var,$var);
        if (!isset($_GET['start']) || !is_numeric($_GET['start']) || (int) $_GET['start'] < 1) {
            $_GET['start'] = 0;
        }
        $SQL_start = (int) $_GET['start'];
        $this->searchResultProducts = SilvercartProduct::get( $whereClause, null, null, sprintf("%s,15", $SQL_start));

        $productIdx = 0;
        if ($this->searchResultProducts) {
            foreach ($this->searchResultProducts as $product) {
                $product->setField('Thumbnail', $product->image()->SetWidth(150));
                $this->registerCustomHtmlForm('SilvercartProductPreviewForm'.$productIdx, new SilvercartProductPreviewForm($this, array('productID' => $product->ID)));
                $product->productPreviewForm = $this->InsertCustomHtmlForm(
                    'SilvercartProductPreviewForm'.$productIdx,
                    array(
                        $product
                    )
                );
                $productIdx++;
            }
        }
    }

    /**
     * Returns the products that match the search result in any kind
     *
     * @return DataObjectSet|false the resulting products of the search query
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.11.10
     */
    public function getProducts() {
        return $this->searchResultProducts;
    }

    /**
     * returns the search query out of the session for the template.
     *
     * @return String the search query saved in the session
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.11.10
     */
    public function getSearchQuery() {
        return Session::get('searchQuery');
    }
}