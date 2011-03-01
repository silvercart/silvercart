<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * Displays products with similar attributes
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 20.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartProductGroupPage extends Page {

    public static $singular_name = "product group";
    public static $plural_name = "product groups";
    public static $allowed_children = array('SilvercartProductGroupPage');
    public static $can_be_root = false;
    public static $db = array(
    );
    public static $has_one = array(
        'GroupPicture' => 'Image'
    );
    public static $has_many = array(
        'SilvercartProducts' => 'SilvercartProduct'
    );
    public static $many_many = array(
        'SilvercartAttributes' => 'SilvercartAttribute'
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
        $this->GroupPicture()->Title = $this->Title;
    }

    /**
     * Return all fields of the backend
     *
     * @return FieldSet Fields of the CMS
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $productsTableField = new HasManyDataObjectManager(
                        $this,
                        'SilvercartProducts',
                        'SilvercartProduct',
                        array(
                            'Title' => _t('SilvercartProduct.COLUMN_TITLE'),
                            'PriceAmount' => _t('SilvercartProduct.PRICE', 'price'),
                            'Weight' => _t('SilvercartProduct.WEIGHT', 'weight')
                        ),
                        'getCMSFields',
                        "`SilvercartProductGroupID` = $this->ID"
        );
        $tabPARAM = "Root.Content."._t('SilvercartProduct.TITLE', 'product');
        $fields->addFieldToTab($tabPARAM, $productsTableField);
        
        $attributeTableField = new ManyManyDataObjectManager(
                        $this,
                        'SilvercartAttributes',
                        'SilvercartAttribute',
                        array(
                            'Title' => _t('SilvercartProduct.COLUMN_TITLE')
                        )
        );
        $tabPARAM2 = "Root.Content." . _t('SilvercartProductGroupPage.ATTRIBUTES', 'attributes');
        $fields->addFieldToTab($tabPARAM2, $attributeTableField);
        $tabPARAM3 = "Root.Content." . _t('SilvercartProductGroupPage.GROUP_PICTURE', 'group picture');
        $fields->addFieldToTab($tabPARAM3, new FileIFrameField('GroupPicture', _t('SilvercartProductGroupPage.GROUP_PICTURE', 'group picture')));
        
        return $fields;
    }

    /**
     * Checks if SilvercartProductGroup has children or products.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.02.2011
     */
    public function hasProductsOrChildren() {
        if ($this->ActiveSilvercartProducts()->Count() > 0
         || count($this->Children()) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Returns true, when the products count is equal $count
     *
     * @param int $count expected count of products
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public function hasProductCount($count) {
        if ($this->ActiveSilvercartProducts()->Count() == $count) {
            return true;
        }
        return false;
    }

    /**
     * Returns the active products for this page.
     *
     * @return DataObjectSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 25.02.2011
     */
    public function ActiveSilvercartProducts() {
        $activeProducts = array();

        foreach ($this->SilvercartProducts() as $product) {
            if ($product->isActive) {
                $activeProducts[] = $product;
            }
        }

        return new DataObjectSet($activeProducts);
    }
}

/**
 * Controller Class.
 * This controller handles the actions for product group views and product detail
 * views.
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 18.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartProductGroupPage_Controller extends Page_Controller {

    protected $groupProducts;

    protected $detailViewProduct = null;

    /**
     * execute these statements on object call
     *
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2011
     */
    public function init() {
        // there must be two way to initialize this controller:
        if ($this->isProductDetailView()) {
            // a product detail view is requested
            if (!$this->getDetailViewProduct()->isActive) {
                Director::redirect($this->PageByIdentifierCodeLink());
            }
            $this->registerCustomHtmlForm('SilvercartProductAddCartFormDetail', new SilvercartProductAddCartFormDetail($this, array('productID' => $this->getDetailViewProduct()->ID)));
        } else {
            // a product group view is requested
            // Get Products for this group
            if (!isset($_GET['start']) ||
                !is_numeric($_GET['start']) ||
                (int)$_GET['start'] < 1) {
                $SQL_start = 0;
            } else {
                $SQL_start = (int) $_GET['start'];
            }

            $this->groupProducts = SilvercartProduct::get(sprintf("`SilvercartProductGroupID` = '%s'",$this->ID), null, null, sprintf("%s,15",$SQL_start));

            // Initialise formobjects
            $productIdx = 0;
            if ($this->groupProducts) {
                $productAddCartForm = $this->getCartFormName();
                foreach ($this->groupProducts as $product) {
                    $this->registerCustomHtmlForm('ProductAddCartForm'.$productIdx, new $productAddCartForm($this, array('productID' => $product->ID)));
                    $product->setField('Thumbnail', $product->image()->SetWidth(150));
                    $product->productAddCartForm = $this->InsertCustomHtmlForm(
                        'ProductAddCartForm'.$productIdx,
                        array(
                            $product
                        )
                    );
                    $productIdx++;
                }
            }
        }

        parent::init();
    }

    /**
     * Uses the children of SilvercartMyAccountHolder to render a subnavigation
     * with the SilvercartSubNavigation.ss template.
     *
     * @return string
     */
    public function getSubNavigation() {
        $extendetOutput = $this->extend('getSubNavigation');
        if (empty ($extendetOutput)) {
            $elements = array(
                'SubElements' => $this->getTopProductGroup($this)->Children(),
            );
            $output = $this->customise($elements)->renderWith(
                array(
                    'SilvercartSubNavigation',
                )
            );
            return $output;
        } else {
            return $extendetOutput[0];
        }
    }

    /**
     * returns the top product group (first product group under SilvercartProductGroupHolder)
     *
     * @param SilvercartProductGroupPage $productGroup product group
     *
     * @return SilvercartProductGroupPage
     */
    public function getTopProductGroup($productGroup) {
        if ($productGroup->Parent()->ClassName == 'SilvercartProductGroupHolder') {
            return $productGroup;
        }
        return $this->getTopProductGroup($productGroup->Parent());
    }

    /**
     * builds the ProductPages link according to its custom URL rewriting rule
     *
     * @param string $action is ignored
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.02.2011
     */
    public function Link($action = null) {
        if ($this->isProductDetailView()) {
            return parent::Link($action) . $this->urlParams['Action'] . '/' . $this->urlParams['ID'];
        }
        return parent::Link($action);
    }

    /**
     * returns the original page link. This is needed by the breadcrumbs. When
     * a product detail view is requested, the default method self::Link() will
     * return a modified link to the products detail view. This controller handles
     * both (product group views and product detail views), so a product detail
     * view won't have a related parent to show in breadcrumbs. The controller
     * itself will be the parent, so there must be two different links for one
     * controller.
     *
     * @return string
     * @see self::Link()
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.02.2011
     */
    public function OriginalLink() {
        return parent::Link(null);
    }

    /**
     * manipulates the defaul logic of building the pages breadcrumbs if a
     * product detail view is requested.
     *
     * @param int    $maxDepth       maximum depth level of shown pages in breadcrumbs
     * @param bool   $unlinked       true, if the breadcrumbs should be displayed without links
     * @param string $stopAtPageType name of pagetype to stop at
     * @param bool   $showHidden     true, if hidden pages should be displayed in breadcrumbs
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.02.2011
     */
    public function Breadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
        if ($this->isProductDetailView()) {
            $page = $this;
            $parts = array();
            $parts[] = $this->getDetailViewProduct()->Title;
            $i = 0;
            while ($page
             && (!$maxDepth || sizeof($parts) < $maxDepth)
             && (!$stopAtPageType || $page->ClassName != $stopAtPageType)) {
                if ($showHidden || $page->ShowInMenus || ($page->ID == $this->ID)) {
                    if ($page->URLSegment == 'home') {
                        $hasHome = true;
                    }
                    if ($page->ID == $this->ID) {
                        $link = $page->OriginalLink();
                    } else {
                        $link = $page->Link();
                    }
                    $parts[] = ("<a href=\"" . $link . "\">" . Convert::raw2xml($page->Title) . "</a>");
                }
                $page = $page->Parent;
            }
            return implode(Page::$breadcrumbs_delimiter, array_reverse($parts));
        }
        return parent::Breadcrumbs($maxDepth, $unlinked, $stopAtPageType, $showHidden);
    }

    /**
     * All products of this group
     * 
     * @return DataObjectSet all products of this group or FALSE
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.10.2010
     */
    public function getProducts() {
       return $this->groupProducts;
    }

    /**
     * Getter for an products image.
     *
     * @return Image defined via a has_one relation in SilvercartProduct
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.10.2010
     */
    public function getProductImage() {

        return SilvercartProduct::image();
    }

    /**
     * handles the requested action.
     * If a product detail view is requested, the detail view template will be
     * rendered an displayed.
     *
     * @param array $request request data
     *
     * @return mixed
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.02.2011
     */
    public function handleAction($request) {
        if ($this->isProductDetailView()) {
            $this->urlParams['Action'] = (int) $this->urlParams['Action'];

            if (!empty($this->urlParams['OtherID']) &&
                    $this->hasMethod($this->urlParams['OtherID'])) {

                $methodName = $this->urlParams['OtherID'];

                return $this->$methodName($request);
            }

            $view = $this->ProductDetailView(
                    $this->urlParams['ID']
            );
            if ($view !== false) {
                return $view;
            }
        }
        return parent::handleAction($request);
    }

    /**
     * renders a product detail view template (if requested)
     *
     * @param string $urlEncodedProductName the url encoded product name
     *
     * @return string the redered template
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.02.2011
     */
    protected function ProductDetailView($urlEncodedProductName) {
        if ($this->isProductDetailView()) {
            $product = $this->getDetailViewProduct();
            $product->productAddCartForm = $this->InsertCustomHtmlForm('SilvercartProductAddCartFormDetail');
            $viewParams = array(
                'getProduct' => $product,
                'MetaTitle' => $this->DetailViewProductMetaTitle(),
                'MetaTags' => $this->DetailViewProductMetaTags(false),
            );
            return $this->customise($viewParams)->renderWith(array('SilvercartProductPage','Page'));
        }
        return false;
    }

    /**
     * checks whether the requested view is an product detail view or a product
     * group view.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.02.2011
     */
    protected function isProductDetailView() {
        if (empty($this->urlParams['Action'])) {
            return false;
        }
        if ($this->hasMethod($this->urlParams['Action'])) {
            return false;
        }
        if (!is_null($this->getDetailViewProduct())) {
            return true;
        }
        return false;
    }

    /**
     * returns the chosen product when requesting a product detail view.
     *
     * @return SilvercartProduct
     */
    public function getDetailViewProduct() {
        if (is_null($this->detailViewProduct)) {
            $this->detailViewProduct = DataObject::get_by_id('SilvercartProduct', Convert::raw2sql($this->urlParams['Action']));
        }
        return $this->detailViewProduct;
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
    protected function DetailViewProductMetaTags($includeTitle = false) {
        $tags = "";
        if ($includeTitle === true || $includeTitle == 'true') {
            $tags .= "<title>" . Convert::raw2xml(($this->MetaTitle) ? $this->MetaTitle : $this->Title) . "</title>\n";
        }

        $tags .= "<meta name=\"generator\" content=\"SilverStripe - http://silverstripe.org\" />\n";

        $charset = ContentNegotiator::get_encoding();
        $tags .= "<meta http-equiv=\"Content-type\" content=\"text/html; charset=$charset\" />\n";
        if ($this->urlParams['ID'] > 0) {
            $product = $this->getDetailViewProduct();
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
    protected function DetailViewProductMetaTitle() {
        $product = $this->getDetailViewProduct();
        if ($product && $product->MetaTitle) {
            if ($product->SilvercartManufacturer()->ID > 0) {
                return $product->MetaTitle ."/". $product->SilvercartManufacturer()->Title;
            }
            return $product->MetaTitle;
        } else {
            return false;
        }
    }

}
