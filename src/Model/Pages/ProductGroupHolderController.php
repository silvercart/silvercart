<?php

namespace SilverCart\Model\Pages;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\Page as SilverCartPage;
use SilverCart\Model\Pages\ProductGroupHolder;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\PaginatedList;

/**
 * ProductGroupHolder Controller class.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductGroupHolderController extends \PageController {

    /**
     * List of the products
     *
     * @var ArrayList 
     */
    protected $groupProducts;
    
    /**
     * Contains the viewable children of this page for caching purposes.
     *
     * @var ArrayList
     */
    protected $viewableChildren = null;

    /**
     * statements to be called on oject instantiation
     *
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.10.2010
     */
    protected function init() {


        // Get Products for this group
        if (!isset($_GET['start']) ||
                !is_numeric($_GET['start']) ||
                (int) $_GET['start'] < 1) {
            $_GET['start'] = 0;
        }

        $SQL_start = (int) $_GET['start'];
        
        parent::init();
        
        $redirectionLink = $this->redirectionLink();
        if ($redirectionLink !== false &&
            Controller::curr() == $this) {
            $this->redirect($redirectionLink, 301);
        }
    }

    /**
     * Builds an associative array of ProductGroups to use in GroupedDropDownFields.
     *
     * @param SiteTree $parent      Expects a ProductGroupHolder or a ProductGroupPage
     * @param boolean  $allChildren Indicate wether all children or only the visible ones should be included
     * @param boolean  $withParent  Indicate wether the parent should be included
     *
     * @return array
     */
    public static function getRecursiveProductGroupsForGroupedDropdownAsArray($parent = null, $allChildren = false, $withParent = false) {
        $productGroups = array();
        
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
     * @param Page $parent needed for recursion
     *
     * @return array
     */
    public static function getAllProductGroupsWithChildrenAsArray($parent = null) {
        $productGroups = array();
        
        if (is_null($parent)) {
            $productGroups['']  = '';
            $parent = Tools::PageByIdentifierCode(SilverCartPage::IDENTIFIER_PRODUCT_GROUP_HOLDER);
            $productGroups[$parent->ID] = $parent->Title;
        }
        $children = $parent->Children();
        if ($children->exists()) {
            foreach ($children as $child) {
                $grandChildren = $child->Children();
                if ($grandChildren->count() > 0) {
                    $productGroups[$child->ID] = $child->Title;
                    $grandChildrenArray = self::getAllProductGroupsWithChildrenAsArray($child);
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
     * @return PaginatedList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.07.2011
     */
    public function getViewableChildren($numberOfProductGroups = false) {
        if ($this->viewableChildren === null) {
            $viewableChildren = new ArrayList();
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

                $pageStart = $this->getSqlOffsetForProductGroups($numberOfProductGroups);

                $viewableChildrenPage = new PaginatedList($viewableChildren, $this->getRequest());
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
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.11.2011
     */
    public function HasMoreViewableChildrenThan($nrOfViewableChildren) {
        if ($this->getViewableChildren()->getTotalItems() > $nrOfViewableChildren) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Return the start value for the limit part of the sql query that
     * retrieves the product group list for the current product group page.
     * 
     * @param int|bool $numberOfProductGroups The number of product groups to return
     *
     * @return int
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.07.2011
     */
    public function getSqlOffsetForProductGroups($numberOfProductGroups = false) {
        if ($this->productGroupsPerPage) {
            $productGroupsPerPage = $this->productGroupsPerPage;
        } else {
            $productGroupsPerPage = Config::ProductsPerPage();
        }

        if ($numberOfProductGroups !== false) {
            $productGroupsPerPage = (int) $numberOfProductGroups;
        }
            
        if (!isset($_GET['groupStart']) ||
            !is_numeric($_GET['groupStart']) ||
            (int)$_GET['groupStart'] < 1) {

            if (isset($_GET['groupOffset'])) {
                // --------------------------------------------------------
                // Use offset for getting the current item rage
                // --------------------------------------------------------
                $offset = (int) $_GET['groupOffset'];

                if ($offset > 0) {
                    $offset -= 1;
                }

                // Prevent too high values
                if ($offset > 999999) {
                    $offset = 0;
                }

                $SQL_start = $offset * $productGroupsPerPage;
            } else {
                // --------------------------------------------------------
                // Use item number for getting the current item range
                // --------------------------------------------------------
                $SQL_start = 0;
            }
        } else {
            $SQL_start = (int) $_GET['groupStart'];
        }
        
        return $SQL_start;
    }

    /**
     * Returns the cache key parts for this product group
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.03.2018
     */
    public function CacheKeyParts() {
        $cacheKeyParts = $this->data()->CacheKeyParts();
        $this->extend('updateCacheKeyParts', $cacheKeyParts);
        return $cacheKeyParts;
    }
    
    /**
     * Returns the cache key for this product group
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.03.2018
     */
    public function CacheKey() {
        $cacheKey = $this->data()->CacheKey();
        $this->extend('updateCacheKey', $cacheKey);
        return $cacheKey;

    }
    
}