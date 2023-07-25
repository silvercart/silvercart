<?php

namespace SilverCart\Model\Pages;

use PageController;
use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\Page as SilverCartPage;
use SilverCart\Model\Product\Product;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\PaginatedList;
use function _t;

/**
 * ProductGroupHolder Controller class.
 *
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductGroupHolderController extends PageController
{
    use ProductGroupSorting;
    /**
     * Contains the total number of products for this page.
     *
     * @var int
     */
    protected int $totalNumberOfProducts = 0;
    /**
     * Contains the viewable children of this page for caching purposes.
     *
     * @var ArrayList|PaginatedList|null
     */
    protected ArrayList|PaginatedList|null $viewableChildren = null;

    /**
     * statements to be called on oject instantiation
     *
     * @return void
     */
    protected function init() : void
    {
        // Get Products for this group
        if (!isset($_GET['start'])
         || !is_numeric($_GET['start'])
         || (int) $_GET['start'] < 1
        ) {
            $_GET['start'] = 0;
        }
        parent::init();
        $redirectionLink = $this->redirectionLink();
        if ($redirectionLink !== false
         && Controller::curr() == $this
        ) {
            $this->redirect($redirectionLink, 301);
        }
    }

    /**
     * Builds an associative array of ProductGroups to use in GroupedDropDownFields.
     *
     * @param SiteTree $parent      Expects a ProductGroupHolder or a ProductGroupPage
     * @param bool     $allChildren Indicate wether all children or only the visible ones should be included
     * @param bool     $withParent  Indicate wether the parent should be included
     *
     * @return array
     */
    public static function getRecursiveProductGroupsForGroupedDropdownAsArray(SiteTree|null $parent = null, bool $allChildren = false, bool $withParent = false) : array
    {
        $productGroups = [];
        if (is_null($parent)) {
            $productGroups['']  = '';
            $parent             = Tools::PageByIdentifierCode(SilverCartPage::IDENTIFIER_PRODUCT_GROUP_HOLDER);
        }
        if ($parent) {
            if ($withParent) {
                $productGroups[$parent->ID] = $parent->Title;
            }
            if ($allChildren) {
                $children = $parent->AllChildren();
            } else {
                $children = $parent->Children();
            }
            foreach ($children as $child) {
                $productGroups[$child->ID] = $child->Title;
                $subs                      = self::getRecursiveProductGroupsForGroupedDropdownAsArray($child);
                if (!empty ($subs)) {
                    $productGroups[_t(ProductGroupHolder::class . '.SUBGROUPS_OF','Subgroups of ') . $child->Title] = $subs;
                }
            }
        }
        return $productGroups;
    }

    /**
     * Aggregates an array with ID => Title of all product groups that have children.
     * The product group holder is included.
     * This is needed for the product group widget
     *
     * @param SiteTree|null $parent needed for recursion
     *
     * @return array
     */
    public static function getAllProductGroupsWithChildrenAsArray(SiteTree|null $parent = null) : array
    {
        $productGroups = [];
        if (is_null($parent)) {
            $productGroups['']          = '';
            $parent                     = Tools::PageByIdentifierCode(SilverCartPage::IDENTIFIER_PRODUCT_GROUP_HOLDER);
            $productGroups[$parent->ID] = $parent->Title;
        }
        $children = $parent->Children();
        if ($children->exists()) {
            foreach ($children as $child) {
                $grandChildren = $child->Children();
                if ($grandChildren->count() > 0) {
                    $productGroups[$child->ID] = $child->Title;
                    $grandChildrenArray        = self::getAllProductGroupsWithChildrenAsArray($child);
                    if (!empty ($grandChildrenArray)) {
                        $productGroups[_t(ProductGroupHolder::class . '.SUBGROUPS_OF', 'Subgroups of ') . $child->Title] = $grandChildrenArray;
                    }
                }
            }
        }
        return $productGroups;
    }

    /**
     * All viewable product groups of this group.
     *
     * @param int $numberOfProductGroups Number of product groups to display
     *
     * @return ArrayList|PaginatedList|bool
     */
    public function getViewableChildren($numberOfProductGroups = false) : ArrayList|PaginatedList|bool
    {
        if ($this->viewableChildren === null) {
            $viewableChildren = ArrayList::create();
            foreach ($this->Children() as $child) {
                if ($child->hasProductsOrChildren()) {
                    $viewableChildren->push($child);
                }
            }
            if ($viewableChildren->count() > 0) {
                if ($numberOfProductGroups == false) {
                    if ($this->productGroupsPerPage) {
                        $pageLength = $this->productGroupsPerPage;
                    } else {
                        $pageLength = Config::ProductGroupsPerPage();
                    }
                } else {
                    $pageLength = $numberOfProductGroups;
                }
                $pageStart            = $this->getSqlOffsetForProductGroups($numberOfProductGroups);
                $viewableChildrenPage = PaginatedList::create($viewableChildren, $this->getRequest());
                $viewableChildrenPage->setPaginationGetVar('groupStart');
                $viewableChildrenPage->setPageStart($pageStart);
                $viewableChildrenPage->setPageLength($pageLength);
                $this->viewableChildren = $viewableChildrenPage;
            } else {
                return false;
            }
        }
        return $this->viewableChildren;
    }

    /**
     * Indicates wether there are more viewable product groups than the given
     * number.
     *
     * @param int $nrOfViewableChildren The number to check against
     *
     * @return bool
     */
    public function HasMoreViewableChildrenThan(int $nrOfViewableChildren) : bool
    {
        return $this->getViewableChildren()->getTotalItems() > $nrOfViewableChildren;
    }

    /**
     * All products of all children groups
     *
     * @param int    $numberOfProducts The number of products to return
     * @param string $sort             An SQL sort statement
     * @param bool   $disableLimit     Disables the product limitation
     * @param bool   $force            Forces to get the products
     *
     * @return PaginatedList
     */
    public function getProducts($numberOfProducts = false, $sort = false, $disableLimit = false, $force = false) : PaginatedList
    {
        $hashKey = md5("{$numberOfProducts}-{$sort}-{$disableLimit}-{$force}-" . Tools::current_locale());
        if ($this->data()->DoNotShowProducts
         && !$force
        ) {
            $this->groupProducts[$hashKey] = PaginatedList::create(ArrayList::create());
        } elseif (!array_key_exists($hashKey, $this->groupProducts)
               || $force
        ) {
            $filterParts     = [];
            $productsPerPage = Config::ProductsPerPage();
            foreach ($this->Children() as $productGroup) {
                /* @var $productGroup ProductGroupPage */
                $filterParts[]   = "({$productGroup->getProductsFilter()})";
                $productsPerPage = $productGroup->getProductsPerPageSetting();
            }
            $filter = implode(' OR ', $filterParts);
            if (!$sort) {
                $sort = Product::defaultSort();
                $this->extend('updateGetProductsSort', $sort);
            }
            $paginatedProducts = PaginatedList::create(Product::getProductsList($filter, $sort), $_GET);
            $paginatedProducts->setPageLength($productsPerPage);
            $this->extend('onAfterGetProducts', $paginatedProducts);
            $this->groupProducts[$hashKey] = $paginatedProducts;
            $this->totalNumberOfProducts   = $paginatedProducts->count();

        }
        return $this->groupProducts[$hashKey];
    }

    /**
     * Indicates wether the resultset of the product query returns more
     * products than the number given (defaults to 10).
     *
     * @param int $maxResults The maximum count of results
     *
     * @return bool
     */
    public function HasMoreProductsThan(int $maxResults = 10) : bool
    {
        $products = $this->getProducts();
        return $products
            && $products->count() > $maxResults;
    }
}
