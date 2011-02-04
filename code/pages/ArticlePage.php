<?php

/**
 * Shows a single article
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 * @since 23.10.2010
 */
class ArticlePage extends Page {

    public static $singular_name = "Artikeldetails";
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
            $page = new $this->ClassName();
            $page->Title = _t('ArticlePage.SINGULARNAME', 'article details');
            $page->URLSegment = _t('ArticlePage.URL_SEGMENT', 'articledetails');
            $page->Status = "Published";
            $page->ShowInMenus = false;
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
class ArticlePage_Controller extends Page_Controller {

    /**
     * statements called on object instanziation
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     * @return void
     */
    public function init() {
        parent::init();
        /**
         * save article ID in session if its in the url to work with in forms
         */
        if ($this->urlParams['ID'] > 0) {
            Session::set('articleID', (int) $this->urlParams['ID']);
        }

        if (isset($this->urlParams['ID']) &&
            isset($this->urlParams['Name'])) {

            $backLink = '/artikelansicht/'.$this->urlParams['ID'].'/'.urlencode($this->urlParams['Name']);
        } else {
            $backLink = $this->Link();
        }

        $this->registerCustomHtmlForm('ArticleAddCartForm', new ArticleAddCartForm($this, array('articleID' => Session::get('articleID'), 'backLink' => $backLink)));
        $this->articleAddCartForm = $this->InsertCustomHtmlForm('ArticleAddCartForm');
    }

    /* public static $url_handlers = array(
      'artikelansicht/$ID/$Name' => 'order'
      ); */

    /**
     * Returns one Article by ID
     *
     * @return Article returns an article identified via URL parameter ID or false
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     */
    public function getArticle() {
        $id = (int) $this->urlParams['ID'];
        if ($id) {
            $article = Article::get("\"ID\" = $id");
        } elseif (Session::get('articleID') > 0) {
            $id = Session::get('articleID');
            $article = Article::get("\"ID\" = $id");
        }
        if ($article == "") {
            Director::redirectBack();
        } else {
            return $article;
        }
    }

    /**
     * Form for adding an article to a cart
     *
     * @return Form add an article to a cart
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     */
    public function addToCartForm() {
        $fields = new FieldSet();
        $fields->push(new NumericField('articleAmount', _t('ArticlePage.QUANTITY', 'quantity'), $value = 1));
        $actions = new FieldSet();
        $actions->push(new FormAction('doAddToCart', _t('ArticlePage.ADD_TO_CART', 'add to cart')));
        $form = new Form($this, 'addToCartForm', $fields, $actions);
        return $form;
    }

    /**
     * Because of a url rule defined for this page type in the _config.php, the function MetaTags does not work anymore.
     * This function overloads it and parses the meta data attributes of Article
     *
     * @param boolean $includeTitle should the title tag be parsed?
     *
     * @return string with all meta tags
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     */
    public function MetaTags($includeTitle = false) {
        $tags = "";
        if ($includeTitle === true || $includeTitle == 'true') {
            $tags .= "<title>" . Convert::raw2xml(($this->MetaTitle) ? $this->MetaTitle : $this->Title) . "</title>\n";
        }

        $tags .= "<meta name=\"generator\" content=\"SilverStripe - http://silverstripe.org\" />\n";

        $charset = ContentNegotiator::get_encoding();
        $tags .= "<meta http-equiv=\"Content-type\" content=\"text/html; charset=$charset\" />\n";
        if ($this->urlParams['ID'] > 0) {
            $article = DataObject::get_by_id('Article', $this->urlParams['ID']);
            if ($article->MetaKeywords) {
                $tags .= "<meta name=\"keywords\" content=\"" . Convert::raw2att($article->MetaKeywords) . "\" />\n";
            }
            if ($article->MetaDescription) {
                $tags .= "<meta name=\"description\" content=\"" . Convert::raw2att($article->MetaDescription) . "\" />\n";
            }
        }
        return $tags;
    }

    /**
     * for SEO reasons this pages attribute MetaTitle gets overwritten with the articles MetaTitle
     * Remember: search engines evaluate 64 characters of the MetaTitle only
     *
     * @return string|false the articles MetaTitle
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.11.10
     */
    public function getMetaTitle() {
        $article = DataObject::get_by_id('Article', (int) $this->urlParams['ID']);
        if ($article && $article->MetaTitle) {
            return $article->MetaTitle ."/". $article->manufacturer()->Title;
        } else {
            return false;
        }
    }
}