<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Pages\ProductGroupPage;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;

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
    public function singular_name()
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name()
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
    public function fieldLabels($includerelations = true)
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
    public function getCMSFields()
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
    public function getMetaTitle()
    {
        $metaTitle = $this->getField('MetaTitle');
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
        return $metaTitle;
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
    public function RelativeLink($action = null)
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
    public function PlainLink($action = null)
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
    public function QueryLink($searchQuery)
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
     * Returns the current search query out of the session store.
     * 
     * @return string
     */
    public static function getCurrentSearchQuery()
    {
        return trim(Tools::Session()->get(self::SESSION_KEY_SEARCH_QUERY));
    }
    
    /**
     * Sets the current search query.
     * 
     * @param string $searchQuery Search query.
     * 
     * @return void
     */
    public static function setCurrentSearchQuery($searchQuery)
    {
        Tools::Session()->set(self::SESSION_KEY_SEARCH_QUERY, $searchQuery);
        Tools::saveSession();
    }
    
    /**
     * Returns the current search category out of the session store.
     * 
     * @return string
     */
    public static function getCurrentSearchCategory()
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
    public static function setCurrentSearchCategory($searchCategory)
    {
        Tools::Session()->set(self::SESSION_KEY_SEARCH_CATEGORY, $searchCategory);
        Tools::saveSession();
    }
    
    /**
     * Returns the current search context out of the session store.
     * 
     * @return string
     */
    public static function getCurrentSearchContext()
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
    public static function setCurrentSearchContext($searchContext)
    {
        Tools::Session()->set(self::SESSION_KEY_SEARCH_CONTEXT, $searchContext);
        Tools::saveSession();
    }
}