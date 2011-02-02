<?php

/**
 * Description of ArticleCategoryPage
 * Gathers Articles of the same theme
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class ArticleCategoryPage extends Page {

    static $singular_name = "";
    static $plural_name = "";
    public static $can_be_root = false;
    public static $allowed_children = array(
        'none'
    );
    public static $has_one = array(
        'categoryPicture' => 'Image'
    );
    public static $many_many = array(
        'articles' => 'Article'
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
        self::$singular_name = _t('ArticleCategoryPage.SINGULARNAME', 'article category');
        self::$singular_name = _t('ArticleCategoryPage.PLURALNAME', 'article categories');
        parent::__construct($record, $isSingleton);
    }

    /**
     * customizes the CMS
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     * @return FieldSet the CMS fields
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $tabParam = "Root.Content."._t('ArticleCategoryPage.CATEGORY_PICTURE', 'category picture');
        $fields->addFieldToTab($tabParam, new FileIFrameField('categoryPicture', 'Kategoriebild'));
        $articlesTableField = new ManyManyComplexTableField(
                        $this,
                        'articles',
                        'Article',
                        array(
                            'Title' => _t('ArticleCategoryPage.COLUMN_TITLE', 'title')
                        ),
                        'getCMSFields_forPopup'
        );
        $tabParam2 = "Root.Content."._t('ArticleCategoryPage.ARTICLES', 'articles');
        $fields->addFieldToTab($tabParam2, $articlesTableField);
        return $fields;
    }

}

/**
 * corelating controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 * @since 23.10.2010
 */
class ArticleCategoryPage_Controller extends Page_Controller {

    protected $categoryArticles;

    /**
     * statements to be called on instanciation
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
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
        $join = "LEFT JOIN ArticleCategoryPage_articles ON ArticleCategoryPage_articles.ArticleID = Article.ID";

        $this->categoryArticles = Article::get("\"ArticleCategoryPageID\" = {$this->ID}", null, $join, $limit = "{$SQL_start},15");

        // Initialise formobjects
        $templateArticleList = new DataObjectSet();
        $articleIdx = 0;
        if ($this->categoryArticles) {
            foreach ($this->categoryArticles as $article) {
                $this->registerCustomHtmlForm('ArticlePreviewForm' . $articleIdx, new ArticlePreviewForm($this, array('articleID' => $article->ID)));
                $articleIdx++;
            }
        }

        parent::init();

        $articleIdx = 0;
        if ($this->categoryArticles) {
            foreach ($this->categoryArticles as $article) {

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
     * returns all articles of this category
     * we use this way instead of a control over the relation because we need pagination
     *
     * @return DataObjectSet set of article objects
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     * @copyright 2010 pixeltricks GmbH
     */
    public function CategoriesArticles() {
        return $this->categoryArticles;
    }

}
