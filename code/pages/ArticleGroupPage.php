<?php
/**
 * Displays articles with similar attributes
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 20.10.2010
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class ArticleGroupPage extends Page {

    public static $singular_name = "article group";
    public static $plural_name = "article groups";
    public static $allowed_children = array('ArticleGroupPage');
    public static $can_be_root = false;
    public static $db = array(
    );
        public static $has_one = array(
        'groupPicture' => 'Image'
    );
    public static $has_many = array(
        'articles' => 'Article'
    );
    public static $many_many = array(
        'attributes' => 'Attribute'
    );

    /**
     * Return all fields of the backend
     *
     * @return FieldSet Fields of the CMS
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $articlesTableField = new HasManyDataObjectManager(
                        $this,
                        'articles',
                        'Article',
                        array(
                            'Title' => _t('ArticleCategoryPage.COLUMN_TITLE'),
                            'PriceAmount' => _t('Article.PRICE', 'price'),
                            'Weight' => _t('Article.WEIGHT', 'weight')
                        ),
                        'getCMSFields',
                        "`articleGroupID` = $this->ID"
        );
        $tabPARAM = "Root.Content."._t('Article.TITLE', 'article');
        $fields->addFieldToTab($tabPARAM, $articlesTableField);
        
        $attributeTableField = new ManyManyDataObjectManager(
                        $this,
                        'attributes',
                        'Attribute',
                        array(
                            'Title' => _t('ArticleCategoryPage.COLUMN_TITLE')
                        )
        );
        $tabPARAM2 = "Root.Content."._t('ArticleGroupPage.ATTRIBUTES', 'attributes');
        $fields->addFieldToTab($tabPARAM2, $attributeTableField);
        $tabPARAM3 = "Root.Content."._t('ArticleGroupPage.GROUP_PICTURE', 'group picture');
        $fields->addFieldToTab($tabPARAM3, new FileIFrameField('groupPicture', 'Gruppenlogo'));
        
        return $fields;
    }

    /**
     * Checks if ArticleGroup has children or articles.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.02.2011
     */
    public function hasArticlesOrChildren() {
        if ($this->articles()->Count() > 0
         || count($this->Children()) > 0) {
            return true;
        }
        return false;
    }

}

/**
 * Controller Class
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 18.10.2010
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class ArticleGroupPage_Controller extends Page_Controller {

    protected $groupArticles;

    /**
     * execute these statements on object call
     *
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.10.2010
     */
    public function init() {


        // Get Articles for this category
        if (!isset($_GET['start']) ||
            !is_numeric($_GET['start']) ||
            (int)$_GET['start'] < 1) {
            $_GET['start'] = 0;
        }

        $SQL_start = (int)$_GET['start'];
        
        $this->groupArticles = Article::get("\"articleGroupID\" = {$this->ID}", null, null, $limit = "{$SQL_start},15");

        // Initialise formobjects

        $articleIdx = 0;
        if ($this->groupArticles) {
            foreach ($this->groupArticles as $article) {
                $this->registerCustomHtmlForm('ArticlePreviewForm'.$articleIdx, new ArticlePreviewForm($this, array('articleID' => $article->ID)));
                $articleIdx++;
            }
        }

        parent::init();

        $articleIdx = 0;
        if ($this->groupArticles) {
            foreach ($this->groupArticles as $article) {

                $article->setField('Link', $article->Link());
                $article->setField('Thumbnail', $article->image()->SetWidth(150));

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
     * All articles of this group
     * 
     * @return DataObjectSet all articles of this group or FALSE
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.10.2010
     */
    public function getArticles() {
       return $this->groupArticles;
    }

    /**
     * Getter for an articles image.
     *
     * @return Image defined via a has_one relation in Article
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.10.2010
     */
    public function getArticleImage() {

        return Article::image();
    }
}