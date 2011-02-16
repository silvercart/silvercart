<?php

/**
 * Shows a single product
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 * @since 23.10.2010
 */
class SilvercartProductPage extends Page {

    public static $singular_name = "product details page";
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

        $records = DataObject::get_one('SilvercartProductPage');
        if (!$records) {
            $page = new SilvercartProductPage();
            $page->Title = _t('SilvercartProductPage.SINGULARNAME', 'product details');
            $page->URLSegment = _t('SilvercartProductPage.URL_SEGMENT', 'productdetails');
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
class SilvercartProductPage_Controller extends Page_Controller {

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
         * save product ID in session if its in the url to work with in forms
         */
        if ($this->urlParams['ID'] > 0) {
            Session::set('productID', (int) $this->urlParams['ID']);
        }

        if (isset($this->urlParams['ID']) &&
            isset($this->urlParams['Name'])) {

            $backLink = '/artikelansicht/'.$this->urlParams['ID'].'/'.urlencode($this->urlParams['Name']);
        } else {
            $backLink = $this->Link();
        }

        $this->registerCustomHtmlForm('ProductAddCartForm', new ProductAddCartFormDetail($this, array('productID' => Session::get('productID'), 'backLink' => $backLink)));
        $this->productAddCartForm = $this->InsertCustomHtmlForm('ProductAddCartForm');
    }

    /**
     * Returns one SilvercartProduct by ID
     *
     * @return SilvercartProduct returns an product identified via URL parameter ID or false
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     */
    public function getProduct() {
        $id = (int) $this->urlParams['ID'];
        if ($id) {
            $product = SilvercartProduct::get(sprintf("`SilvercartProduct`.`ID` = '%s'", $id));
        } elseif (Session::get('productID') > 0) {
            $id = Session::get('productID');
            $product = SilvercartProduct::get(sprintf("`SilvercartProduct`.`ID` = '%s'", $id));
        }
        if ($product == "") {
            Director::redirectBack();
        } else {
            return $product;
        }
    }

    /**
     * Form for adding an product to a cart
     *
     * @return Form add an product to a cart
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     */
    public function addToCartForm() {
        $fields = new FieldSet();
        $fields->push(new NumericField('productAmount', _t('SilvercartProductPage.QUANTITY', 'quantity'), $value = 1));
        $actions = new FieldSet();
        $actions->push(new FormAction('doAddToCart', _t('SilvercartProductPage.ADD_TO_CART', 'add to cart')));
        $form = new Form($this, 'addToCartForm', $fields, $actions);
        return $form;
    }

    /**
     * Because of a url rule defined for this page type in the _config.php, the function MetaTags does not work anymore.
     * This function overloads it and parses the meta data attributes of SilvercartProduct
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
            $product = DataObject::get_by_id('SilvercartProduct', $this->urlParams['ID']);
            if ($product->MetaKeywords) {
                $tags .= "<meta name=\"keywords\" content=\"" . Convert::raw2att($product->MetaKeywords) . "\" />\n";
            }
            if ($product->MetaDescription) {
                $tags .= "<meta name=\"description\" content=\"" . Convert::raw2att($product->MetaDescription) . "\" />\n";
            }
        }
        return $tags;
    }

    /**
     * for SEO reasons this pages attribute MetaTitle gets overwritten with the products MetaTitle
     * Remember: search engines evaluate 64 characters of the MetaTitle only
     *
     * @return string|false the products MetaTitle
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.11.10
     */
    public function getMetaTitle() {
        $product = DataObject::get_by_id('SilvercartProduct', (int) $this->urlParams['ID']);
        if ($product && $product->MetaTitle) {
            return $product->MetaTitle ."/". $product->manufacturer()->Title;
        } else {
            return false;
        }
    }
}