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
     * Constructor. Extension to overwrite the groupimage's "alt"-tag with the
     * name of the productgroup.
     *
     * @param array $record      Array of field values. Normally this contructor is only used by the internal systems that get objects from the database.
     * @param bool  $isSingleton This this to true if this is a singleton() object, a stub for calling methods. Singletons don't have their defaults set.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.02.2011
     */
    public function  __construct($record = null, $isSingleton = false) {
        parent::__construct($record, $isSingleton);
        $this->groupPicture()->Title = $this->Title;
    }

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

    /**
     * Returns true, when the articles count is equal $count
     *
     * @param int $count expected count of articles
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public function hasProductCount($count) {
        if ($this->articles()->Count() == $count) {
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2011
     */
    public function init() {
        // Get Articles for this category
        if (!isset($_GET['start']) ||
            !is_numeric($_GET['start']) ||
            (int)$_GET['start'] < 1) {
            $SQL_start = 0;
        } else {
            $SQL_start = (int) $_GET['start'];
        }

        $this->groupArticles = Article::get(sprintf("`articleGroupID` = '%s'",$this->ID), null, null, sprintf("%s,15",$SQL_start));

        // Initialise formobjects
        $articleIdx = 0;
        if ($this->groupArticles) {
            $productAddCartForm = $this->getCartFormName();
            foreach ($this->groupArticles as $article) {
                $this->registerCustomHtmlForm('ProductAddCartForm'.$articleIdx, new $productAddCartForm($this, array('articleID' => $article->ID)));
                $article->setField('Thumbnail', $article->image()->SetWidth(150));
                $article->productAddCartForm = $this->InsertCustomHtmlForm(
                    'ProductAddCartForm'.$articleIdx,
                    array(
                        $article
                    )
                );
                $articleIdx++;
            }
        }

        parent::init();
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