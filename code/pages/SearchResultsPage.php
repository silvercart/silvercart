<?php

/**
 * page type to display search results
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license LGPL
 * @copyright 2010 pixeltricks GmbH
 */
class SearchResultsPage extends Page {

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
        $records = DataObject::get_one($this->ClassName);
        if (!$records) {
            $page = new SearchResultsPage();
            $page->Title = _t('SearchResultsPage.TITLE', 'search results');
            $page->URLSegment = _t('SearchResultsPage.URL_SEGMENT', 'search-results');
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
class SearchResultsPage_Controller extends Page_Controller {

    protected $searchResultArticles;

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
        $whereClause = "\"Title\" LIKE '%$var%'
        OR \"ShortDescription\" LIKE '%$var%'
        OR \"LongDescription\" LIKE '%$var%'
        OR \"MetaKeywords\" LIKE '%$var%'
            ";
        if (!isset($_GET['start']) || !is_numeric($_GET['start']) || (int) $_GET['start'] < 1) {
            $_GET['start'] = 0;
        }
        $SQL_start = (int) $_GET['start'];
        $this->searchResultArticles = Article::get( $whereClause, null, null, $limit = "{$SQL_start},15");

        $articleIdx = 0;
        if ($this->searchResultArticles) {
            foreach ($this->searchResultArticles as $article) {

                $article->setField('Link', $article->Link());
                $article->setField('Thumbnail', $article->image()->SetWidth(150));

                $this->registerCustomHtmlForm('ArticlePreviewForm'.$articleIdx, new ArticlePreviewForm($this, array('articleID' => $article->ID)));

                $article->articlePreviewForm = $this->InsertCustomHtmlForm(
                    'ArticlePreviewForm'.$articleIdx,
                    array(
                        $article
                    )
                );
                $articleIdx++;
            }
        }
    }

    /**
     * Returns the articles that match the search result in any kind
     *
     * @return DataObjectSet|false the resulting articles of the search query
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.11.10
     */
    public function getArticles() {
        return $this->searchResultArticles;
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