<?php

/**
 * to display a group of articles
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license BSD
 * @since 23.10.2010
 */
class ArticleGroupHolder extends Page {

    public static $singular_name = "";
    public static $plural_name = "";
    public static $allowed_children = array(
        'ArticleGroupPage'
    );

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
        self::$singular_name = _t('ArticleGroupHolder.SINGULARNAME', 'article group overview');
        self::$plural_name = _t('ArticleGroupHolder.PLURALNAME', 'article groups');
        parent::__construct($record, $isSingleton);
    }

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
            $page = new $this->ClassName();
            $page->Title = _t('ArticleGroupHolder.PLURALNAME', 'article groups');
            $page->URLSegment = _t('ArticleGroupHolder.URL_SEGMENT', 'articlegroups');
            $page->Status = "Published";
            $page->write();
            $page->publish("Stage", "Live");
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
class ArticleGroupHolder_Controller extends Page_Controller {

    protected $groupArticles;

    /**
     * statements to be called on oject instantiation
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.10.2010
     * @return void
     */
    public function init() {


        // Get Articles for this category
        if (!isset($_GET['start']) ||
                !is_numeric($_GET['start']) ||
                (int) $_GET['start'] < 1) {
            $_GET['start'] = 0;
        }

        $SQL_start = (int) $_GET['start'];

        $this->groupArticles = Article::getRandomArticles(5);

        // Initialise formobjects
        $templateArticleList = new DataObjectSet();
        $articleIdx = 0;
        if ($this->groupArticles) {
            foreach ($this->groupArticles as $article) {
                $this->registerCustomHtmlForm('ArticlePreviewForm' . $articleIdx, new ArticlePreviewForm($this, array('articleID' => $article->ID)));
                $articleIdx++;
            }
        }

        parent::init();

        $articleIdx = 0;
        if ($this->groupArticles) {
            foreach ($this->groupArticles as $article) {

                $article->setField('Link', $article->Link());
                $article->articlePreviewForm = $this->InsertCustomHtmlForm(
                                'ArticlePreviewForm' . $articleIdx,
                                array(
                                    $article
                                )
                );

                $articleIdx++;
            }
        }
    }

    /**
     * to be called on a template
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @return DataObjectSet set of randomly choosen article objects
     * @since 23.10.2010
     */
    public function randomArticles() {
        return $this->groupArticles;
    }

}