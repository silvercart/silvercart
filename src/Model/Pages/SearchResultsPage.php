<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Pages\ProductGroupPage;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Forms\FieldList;

/**
 * page type to display search results.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SearchResultsPage extends ProductGroupPage
{
    const SESSION_KEY_SEARCH_QUERY    = 'SilverCart.SearchQuery';
    const SESSION_KEY_SEARCH_CATEGORY = 'SilverCart.SearchCategory';
    const SESSION_KEY_SEARCH_CONTEXT  = 'SilverCart.SearchContext';

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartSearchResultsPage';
    /**
     * Set allowed children for this page.
     *
     * @var array
     */
    private static $allowed_children = 'none';
    /**
     * We set a custom icon for this page type here
     *
     * @var string
     */
    private static $icon = "silvercart/silvercart:client/img/page_icons/metanavigation_page_search-file.gif";
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = [
        'productsPerPage' => 'Int'
    ];
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this); 
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.09.2018
     */
    public function fieldLabels($includerelations = true) : array
    {
        $this->beforeUpdateFieldLabels(function(&$labels) {
            $labels = array_merge(
                $labels,
                [
                    'productsPerPage' => _t(ProductGroupPage::class . '.PRODUCTSPERPAGE', 'Products per page'),
                ]
            );
        });
        return parent::fieldLabels($includerelations);
    }

    /**
     * Return all fields of the backend.
     *
     * @return FieldList Fields of the CMS
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function($fields) {
            $fields->removeByName('useContentFromParent');
            $fields->removeByName('DoNotShowProducts');
            $fields->removeByName('productGroupsPerPage');
            $fields->removeByName('DefaultGroupHolderView');
            $fields->removeByName('UseOnlyDefaultGroupHolderView');
        });
        return parent::getCMSFields();
    }
    
    /**
     * Returns the meta title. If not set, the meta-title of the 
     * single product in detail view or the title of the SiteTree object 
     * will be returned
     * 
     * @return string
     */
    public function getMetaTitle() : string
    {
        $metaTitle = (string) $this->getField('MetaTitle');
        if (!$this->getCMSFieldsIsCalled
         && !Tools::isBackendEnvironment()
        ) {
            if (empty($metaTitle)) {
                $ctrl = Controller::curr();
                if ($ctrl instanceof SearchResultsPageController) {
                    $searchTitle  = _t(self::class . '.SearchTitle', '{count} search results for "{title}"', [
                        'count' => $ctrl->TotalSearchResults(),
                        'title' => $ctrl->getPlainSearchQuery(),
                    ]);
                    $searchCategory = $ctrl->getSearchCategory();
                    if ($searchCategory instanceof ProductGroupPage) {
                        $searchTitle = "{$searchTitle} ({$searchCategory->Title})";
                    }
                    if ($ctrl->HasMorePagesThan(1)) {
                        $searchTitle .= ', ' . _t(Page::class . '.PageXofY', 'Page {x} of {y}', [
                            'x' => $ctrl->getProducts()->CurrentPage(),
                            'y' => $ctrl->getProducts()->Pages()->count(),
                        ]);
                    }
                    $metaTitle = "{$this->Title} | {$searchTitle}";
                }
            }
            $this->extend('updateMetaTitle', $metaTitle);
        }
        return (string) $metaTitle;
    }
    
    /**
     * Adds the current search query to the link.
     * (e.g. "?q=my+search+term" / "&q=my+search+term")
     * 
     * @param string $action Action to call
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.09.2018
     */
    public function RelativeLink($action = null) : string
    {
        $relativeLink = parent::RelativeLink($action);
        $query = self::getCurrentSearchQuery();
        if (!empty($query)) {
            $urlencodedQuery = urlencode($query);
            if (strpos($relativeLink, '?') === false) {
                $relativeLink = "{$relativeLink}?q={$urlencodedQuery}";
            } else {
                $relativeLink = "{$relativeLink}&q={$urlencodedQuery}";
            }
        }
        return $relativeLink;
    }
    
    /**
     * Returns the plain link without search query.
     * 
     * @param string $action Action to call
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.09.2018
     */
    public function PlainLink($action = null) : string
    {
        $relativeLink = parent::RelativeLink($action);
        $plainLink    = Controller::join_links(Director::baseURL(), $relativeLink);
        $this->extend('updatePlainLink', $plainLink, $action, $relativeLink);
        return $plainLink;
    }
    
    /**
     * Returns the link for the given search query.
     * 
     * @param string $searchQuery Search query to get link for
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.09.2018
     */
    public function QueryLink($searchQuery) : string
    {
        $queryLink = $this->PlainLink();
        if (!empty($searchQuery)) {
            $urlencodedQuery = urlencode($searchQuery);
            if (strpos($queryLink, '?') === false) {
                $queryLink = "{$queryLink}?q={$urlencodedQuery}";
            } else {
                $queryLink = "{$queryLink}&q={$urlencodedQuery}";
            }
        }
        return $queryLink;
    }

    /**
     * Returns the cache key parts for the current search context query and
     * category.
     * 
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.10.2018
     */
    public function CacheKeyParts() : array
    {
        if (is_null($this->cacheKeyParts)) {
            $ctrl = Controller::curr();
            /* @var $ctrl SearchResultsPageController */
            parent::CacheKeyParts();
            $this->cacheKeyParts[] = sha1($ctrl->getSearchQuery()) . md5($ctrl->getSearchQuery());
            $this->cacheKeyParts[] = self::getCurrentSearchCategory();
        }
        return $this->cacheKeyParts;
    }
    
    /**
     * Returns the current search query out of the session store.
     * 
     * @return string
     */
    public static function getCurrentSearchQuery() : string
    {
        $searchQueryByRequest = Controller::curr()->getRequest()->getVar('q');
        if (!is_null($searchQueryByRequest)) {
            $searchQuery = $searchQueryByRequest;
        } else {
            $searchQuery = trim(Tools::Session()->get(self::SESSION_KEY_SEARCH_QUERY));
        }
        return $searchQuery;
    }
    
    /**
     * Sets the current search query.
     * 
     * @param string $searchQuery Search query.
     * 
     * @return void
     */
    public static function setCurrentSearchQuery($searchQuery) : void
    {
        Tools::Session()->set(self::SESSION_KEY_SEARCH_QUERY, $searchQuery);
        Tools::saveSession();
    }
    
    /**
     * Returns the current search category out of the session store.
     * 
     * @return string
     */
    public static function getCurrentSearchCategory() : string
    {
        return trim(Tools::Session()->get(self::SESSION_KEY_SEARCH_CATEGORY));
    }
    
    /**
     * Sets the current search category.
     * 
     * @param string $searchCategory Search category.
     * 
     * @return void
     */
    public static function setCurrentSearchCategory($searchCategory) : void
    {
        Tools::Session()->set(self::SESSION_KEY_SEARCH_CATEGORY, $searchCategory);
        Tools::saveSession();
    }
    
    /**
     * Returns the current search context out of the session store.
     * 
     * @return string
     */
    public static function getCurrentSearchContext() : string
    {
        return trim(Tools::Session()->get(self::SESSION_KEY_SEARCH_CONTEXT));
    }
    
    /**
     * Sets the current search context.
     * 
     * @param string $searchContext Search context.
     * 
     * @return void
     */
    public static function setCurrentSearchContext($searchContext) : void
    {
        Tools::Session()->set(self::SESSION_KEY_SEARCH_CONTEXT, $searchContext);
        Tools::saveSession();
    }
}