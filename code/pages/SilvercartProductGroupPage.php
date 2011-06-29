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

    /**
     * Singular name.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.03.2011
     */
    public static $singular_name = "product group";

    /**
     * Plural name.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.03.2011
     */
    public static $plural_name = "product groups";

    /**
     * Set allowed childrens for this page.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.03.2011
     */
    public static $allowed_children = array('SilvercartProductGroupPage');

    /**
     * ???.
     *
     * @var boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.03.2011
     */
    public static $can_be_root = false;

    /**
     * Attributes.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.03.2011
     */
    public static $db = array(
        'productsPerPage'   => 'Int'
    );

    /**
     * Has-one relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.03.2011
     */
    public static $has_one = array(
        'GroupPicture' => 'Image'
    );

    /**
     * Has-many relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.03.2011
     */
    public static $has_many = array(
        'SilvercartProducts' => 'SilvercartProduct'
    );

    /**
     * Many-many relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.03.2011
     */
    public static $many_many = array(
        'SilvercartAttributes'      => 'SilvercartAttribute',
    );

    /**
     * Belongs-many-many relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.03.2011
     */
    public static $belongs_many_many = array(
        'SilvercartMirrorProducts'  => 'SilvercartProduct'
    );

    /**
     * Contains all manufacturers of the products contained in this product
     * group page.
     *
     * @var boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.03.2011
     */
    protected $manufacturers = null;

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
     * @copyright 2011 pixeltricks GmbH
     * @since 10.02.2011
     */
    public function  __construct($record = null, $isSingleton = false) {
        parent::__construct($record, $isSingleton);
        $this->drawCMSFields = true;
        $this->GroupPicture()->Title = $this->Title;
    }
    
    /**
     * Overwrites the function LinkingMode in SiteTree
     * Other than the default behavior current should be returned for the
     * product category defined via session. This is neccessary for products
     * that are mirrored into a category.
     * If the product category is not set in the session the method behaves like
     * the overwritten one.
     * 
     * @return string current, section or link; to be used in the template
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 29.6.2011
     */
    public function LinkingMode() {
        if (Session::get("SilvercartProductGroupPageID")) {
            if ($this->ID == Session::get("SilvercartProductGroupPageID")) {
                return 'current';
            }
        } elseif ($this->isCurrent()) {
            return "current";
        } elseif ($this->isSection()) {
            return 'section';
        } else {
            return 'link';
        }
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 20.04.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'productsPerPage' => _t('SilvercartProductGroupPage.PRODUCTSPERPAGE'),
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Return all fields of the backend.
     *
     * @return FieldSet Fields of the CMS
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 24.03.2011
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $mirroredProductIdList  = '';
        $mirroredProductIDs     = $this->getMirroredProductIDs();

        foreach ($mirroredProductIDs as $mirroredProductID) {
            $mirroredProductIdList .= sprintf(
                "'%s',",
                $mirroredProductID
            );
        }

        if (!empty($mirroredProductIdList)) {
            $mirroredProductIdList = substr($mirroredProductIdList, 0, -1);

            $filter = sprintf(
                "`SilvercartProductGroupID` = %d OR
                 `SilvercartProduct`.`ID` IN (%s)",
                $this->ID,
                $mirroredProductIdList
            );
        } else {
            $filter = sprintf(
                "`SilvercartProductGroupID` = %d",
                $this->ID
            );
        }

        if ($this->drawCMSFields()) {
            $productsTableField = new HasManyDataObjectManager(
                $this,
                'SilvercartProducts',
                'SilvercartProduct',
                array(
                    'Title' => _t('SilvercartProduct.COLUMN_TITLE'),
                    'Weight' => _t('SilvercartProduct.WEIGHT', 'weight')
                ),
                'getCMSFields',
                $filter
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
        }

        $productsPerPageField = new TextField('productsPerPage', _t('SilvercartProductGroupPage.PRODUCTSPERPAGE'));
        $fields->addFieldToTab('Root.Content.Main', $productsPerPageField, 'IdentifierCode');



        $this->extend('extendCMSFields', $fields);
        return $fields;
    }

    /**
     * Returns all SilvercartProductIDs that have this group set as mirror
     * group.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 24.03.2011
     */
    public function getMirroredProductIDs() {
        $mirroredProductIDs = array();

        $sqlQuery = new SQLQuery();
        $sqlQuery->select = array(
            'SP_SPGMP.SilvercartProductID'
        );
        $sqlQuery->from = array(
            'SilvercartProduct_SilvercartProductGroupMirrorPages SP_SPGMP'
        );
        $sqlQuery->where = array(
            sprintf(
                "SP_SPGMP.SilvercartProductGroupPageID = %d",
                $this->ID
            )
        );
        $result = $sqlQuery->execute();

        foreach ($result as $row) {
            $mirroredProductIDs[] = $row['SilvercartProductID'];
        }

        return $mirroredProductIDs;
    }

    /**
     * Indicates wether the CMS Fields should be drawn.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.03.2011
     */
    public function drawCMSFields() {
        $drawCMSFields   = true;
        $updateCMSFields = $this->extend('updateDrawCMSFields', $drawCMSFields);

        if (!empty($updateCMSFields)) {
            $drawCMSFields = $updateCMSFields[0];
        }

        return $drawCMSFields;
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

    /**
     * Returns all Manufacturers of the groups products.
     *
     * @return DataObjectSet
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.03.2011
     */
    public function getManufacturers() {
        if (is_null($this->manufacturers)) {
            $registeredManufacturers = array();
            $manufacturers = array();

            foreach ($this->SilvercartProducts() as $product) {
                if ($product->SilvercartManufacturer()) {
                    if (in_array($product->SilvercartManufacturer()->Title, $registeredManufacturers) == false) {
                        $registeredManufacturers[] = $product->SilvercartManufacturer()->Title;
                        $manufacturers[] = $product->SilvercartManufacturer();
                    }
                }
            }
            $this->manufacturers = new DataObjectSet($manufacturers);
        }
        return $this->manufacturers;
    }

    /**
     * Returns whether the actual view is filtered by this manufacturer or not.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.03.2011
     */
    public function isActive() {
        return Controller::curr()->Link() == $this->Link();
    }

    /**
     * Returns a sorted list of children of this node.
     *
     * @param string $sortField The field used for sorting
     * @param string $sortDir   The sort direction ('ASC' or 'DESC')
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 31.05.2011
     * 
     * @return DataObjectSet child pages
     */
    public function OrderedChildren($sortField = 'Title', $sortDir = 'ASC') {
        $children = $this->Children();
        $children->sort($sortField, $sortDir);

        return $children;
    }

    /**
     * All products of this group
     * 
     * @param int|bool $numberOfProducts The number of products to return
     * 
     * @return DataObjectSet all products of this group or FALSE
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.10.2010
     */
    public function getProducts($numberOfProducts = false) {
        $controller = new SilvercartProductGroupPage_Controller($this);

        return $controller->getProducts($numberOfProducts);
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

    protected $groupProducts = null;

    protected $detailViewProduct = null;

    protected $listFilters = array();

    /**
     * execute these statements on object call
     *
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2011
     */
    public function init() {
        parent::init();
        // there must be two way to initialize this controller:
        if ($this->isProductDetailView()) {
            // a product detail view is requested
            if (!$this->getDetailViewProduct()->isActive) {
                Director::redirect($this->PageByIdentifierCodeLink());
            }
            $this->registerCustomHtmlForm('SilvercartProductAddCartFormDetail', new SilvercartProductAddCartFormDetail($this, array('productID' => $this->getDetailViewProduct()->ID)));
        } else {
            // a product group view is requested
            $products = $this->getProducts();
            Session::set("SilvercartProductGroupPageID", $this->ID);
            Session::save();
            // Initialise formobjects
            $productIdx = 0;
            if ($products) {
                $productAddCartForm = $this->getCartFormName();
                foreach ($products as $product) {
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
    }

    /**
     * Uses the children of SilvercartMyAccountHolder to render a subnavigation
     * with the SilvercartSubNavigation.ss template.
     *
     * @return string
     */
    public function getSubNavigation() {
        $cachekey = 'SilvercartSubNavigation'.$this->ID;
        $cache    = SS_Cache::factory($cachekey);
        $result   = $cache->load($cachekey);

        if ($result) {
            $output = unserialize($result);
        } else {
            $menuElements = $this->getTopProductGroup($this)->Children();

            $extendedOutput = $this->extend('getSubNavigation', $menuElements);

            if (empty ($extendedOutput)) {
                $elements = array(
                    'SubElements' => $menuElements,
                );
                $output = $this->customise($elements)->renderWith(
                    array(
                        'SilvercartSubNavigation',
                    )
                );
            } else {
                $output = $extendedOutput[0];
            }

            $cache->save(serialize($output));
        }

        return $output;
    }

    /**
     * returns the top product group (first product group under SilvercartProductGroupHolder)
     *
     * @param SilvercartProductGroupPage $productGroup product group
     *
     * @return SilvercartProductGroupPage
     */
    public function getTopProductGroup($productGroup = false) {
        if (!$productGroup) {
            $productGroup = $this;
        }
        if ($productGroup->Parent()->ClassName == 'SilvercartProductGroupHolder' ||
            $productGroup->ParentID == 0) {
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
     * 
     * @see self::Link()
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
            if (Session::get("SilvercartProductGroupPageID")) {
                $dataRecord = DataObject::get_by_id("SilvercartProductGroupPage", Session::get("SilvercartProductGroupPageID"));
                $page = new SilvercartProductGroupPage_Controller($dataRecord);
            } else {
                $page = $this;
            }
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
     * Returns the offset of the current page for pagination.
     * 
     * @return int
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.06.2011
     */
    public function CurrentOffset() {
        if (!isset($_GET['start']) ||
            !is_numeric($_GET['start']) ||
            (int)$_GET['start'] < 1) {


            if (isset($_GET['offset'])) {
                if ($this->productsPerPage) {
                    $productsPerPage = $this->productsPerPage;
                } else {
                    $productsPerPage = SilvercartConfig::ProductsPerPage();
                }

                // --------------------------------------------------------
                // Use offset for getting the current item rage
                // --------------------------------------------------------
                $offset = (int) $_GET['offset'];

                if ($offset > 0) {
                    $offset -= 1;
                }

                // Prevent too high values
                if ($offset > 999999) {
                    $offset = 0;
                }

                $SQL_start = $offset * $productsPerPage;
            } else {
                // --------------------------------------------------------
                // Use item number for getting the current item range
                // --------------------------------------------------------
                $SQL_start = 0;
            }
        } else {
            $SQL_start = (int) $_GET['start'];
        }

        return $SQL_start;
    }

    /**
     * All products of this group
     * 
     * @param int|bool $numberOfProducts The number of products to return
     * 
     * @return DataObjectSet all products of this group or FALSE
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.10.2010
     */
    public function getProducts($numberOfProducts = false) {
        if (is_null($this->groupProducts)) {
            $SQL_start = $this->getSqlOffset();

            // ----------------------------------------------------------------
            // Get products that have this group set as mirror group
            // ----------------------------------------------------------------
            if ($this->productsPerPage) {
                $productsPerPage = $this->productsPerPage;
            } else {
                $productsPerPage = SilvercartConfig::ProductsPerPage();
            }

            if ($numberOfProducts !== false) {
                $productsPerPage = (int) $numberOfProducts;
            }

            $mirroredProductIdList  = '';
            $mirroredProductIDs     = $this->getMirroredProductIDs();

            foreach ($mirroredProductIDs as $mirroredProductID) {
                $mirroredProductIdList .= sprintf(
                    "'%s',",
                    $mirroredProductID
                );
            }

            if (!empty($mirroredProductIdList)) {
                $mirroredProductIdList = substr($mirroredProductIdList, 0, -1);
            }

            // ----------------------------------------------------------------
            // Get products that have this group set as main group
            // ----------------------------------------------------------------
            if ($this->isFilteredByManufacturer()) {
                $manufacturer = SilvercartManufacturer::getByUrlSegment($this->urlParams['ID']);
                if ($manufacturer) {
                    $this->addListFilter('SilvercartManufacturerID', $manufacturer->ID);
                }
            }

            if (empty($mirroredProductIdList)) {
                $filter = sprintf(
                    "`SilvercartProductGroupID` = '%s'",
                    $this->ID
                );
            } else {
                $filter = sprintf(
                    "(`SilvercartProductGroupID` = '%s' OR
                      `SilvercartProduct`.`ID` IN (%s))",
                    $this->ID,
                    $mirroredProductIdList
                );
            }

            foreach ($this->listFilters as $listFilter) {
                $filter .= ' ' . $listFilter;
            }

            $sort = 'CASE WHEN SPGMSO.SortOrder THEN CONCAT(SPGMSO.SortOrder, SilvercartProduct.SortOrder) ELSE SilvercartProduct.SortOrder END ASC';

            $join = sprintf(
                "LEFT JOIN SilvercartProductGroupMirrorSortOrder SPGMSO ON SPGMSO.SilvercartProductGroupPageID = %d AND SPGMSO.SilvercartProductID = SilvercartProduct.ID",
                $this->ID
            );

            $this->groupProducts = SilvercartProduct::get($filter, $sort, $join, sprintf("%d,%d", $SQL_start, $productsPerPage));

            // Inject additional methods into the DataObjectSet
            if ($this->groupProducts) {
                $this->groupProducts->HasMorePagesThan = $this->HasMorePagesThan;
            }
        }

        return $this->groupProducts;
    }

    /**
     * Return the start value for the limit part of the sql query that
     * retrieves the product list for the current product group page.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 12.06.2011
     */
    public function getSqlOffset() {
        if (!isset($_GET['start']) ||
            !is_numeric($_GET['start']) ||
            (int)$_GET['start'] < 1) {

            if (isset($_GET['offset'])) {
                // --------------------------------------------------------
                // Use offset for getting the current item rage
                // --------------------------------------------------------
                $offset = (int) $_GET['offset'];

                if ($offset > 0) {
                    $offset -= 1;
                }

                // Prevent too high values
                if ($offset > 999999) {
                    $offset = 0;
                }

                $SQL_start = $offset * $productsPerPage;
            } else {
                // --------------------------------------------------------
                // Use item number for getting the current item range
                // --------------------------------------------------------
                $SQL_start = 0;
            }
        } else {
            $SQL_start = (int) $_GET['start'];
        }

        return $SQL_start;
    }

    /**
     * Indicates wether the resultset of the product query returns more items
     * than the number given (defaults to 10).
     *
     * @param int $maxResults The number of results to check
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 20.04.2011
     */
    public function HasMorePagesThan($maxResults = 10) {
        $items = $this->getProducts()->Pages()->TotalItems();
        $hasMoreResults = false;

        if ($items > $maxResults) {
            $hasMoreResults = true;
        }

        return $hasMoreResults;
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
     * @param SS_HTTPRequest $request request data
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
        } elseif ($this->isFilteredByManufacturer()) {
            $url = str_replace($this->urlParams['Action'] . '/' . $this->urlParams['ID'], '', $_REQUEST['url']);
            $this->urlParams['Action'] = '';
            $this->urlParams['ID'] = '';
            $customRequest = new SS_HTTPRequest('GET', $url, array(), array(), null);
            return parent::handleAction($customRequest);
            exit();
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
    public function isProductDetailView() {
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
        if (is_numeric($this->urlParams['Action']) == false) {
            return null;
        }
        if (is_null($this->detailViewProduct)) {
            $this->detailViewProduct = DataObject::get_by_id('SilvercartProduct', Convert::raw2sql($this->urlParams['Action']));
        }
        return $this->detailViewProduct;
    }

    /**
     * Returns the HTML Code as string for all widgets in the given WidgetArea.
     * 
     * If thereÄs no WidgetArea for this page defined we try to get the
     * definition from its parent page.
     *
     * @param int $identifier target area
     * 
     * @return string
     * 
     * @author Sascha koehler <skoehler@pixeltricks.de>
     * @since 27.05.2011
     */
    public function InsertWidgetArea($identifier = 'Sidebar') {
        $output         = '';
        $controllerName = 'WidgetSet'.$identifier.'Controllers';

        if (!isset($this->$controllerName)) {
            return $output;
        }

        foreach ($this->$controllerName as $controller) {
            $output .= $controller->WidgetHolder();
        }

        if (empty($output)) {
            $parentPage = $this->getParent();

            if ($parentPage) {
                $parentPageController = ModelAsController::controller_for($parentPage);
                $parentPageController->init();
                $output               = $parentPageController->InsertWidgetArea($identifier);
            }
        }

        return $output;
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

    /**
     * Checks whether the product list should be filtered by manufacturer.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.03.2011
     */
    protected function isFilteredByManufacturer() {
        if ($this->urlParams['Action'] == _t('SilvercartProductGroupPage.MANUFACTURER_LINK','manufacturer') && !empty ($this->urlParams['ID'])) {
            return true;
        }
        return false;
    }

    /**
     * Adds a filter to filter the groups product list.
     *
     * @param string $property   The property to filter
     * @param string $value      The value of the property
     * @param string $comparison The comparison operator (default: '=')
     * @param string $operator   The logical operator (default: 'AND')
     *
     * @return void
     *
     * @example $productGroup->addListFilter('SilvercartManufacturerID','5');
     *          Will add the following filter: "AND `SilvercartManufacturerID` = '5'"
     * @example $productGroup->addListFilter('SilvercartManufacturerID','(5,6,7)','IN','OR');
     *          Will add the following filter: "OR `SilvercartManufacturerID` IN (5,6,7)"
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.03.2011
     */
    public function addListFilter($property, $value, $comparison = '=', $operator = 'AND') {
        if ($comparison == 'IN') {
            $this->listFilters[] = $operator . " `" . $property . "` " . $comparison . " (" . $value . ")";
        } else {
            $this->listFilters[] = $operator . " `" . $property . "` " . $comparison . " '" . $value . "'";
        }
    }

}
