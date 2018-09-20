<?php

namespace SilverCart\Model\Pages;

use ReflectionClass;
use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\ProductGroupPageSelectorsForm;
use SilverCart\Model\SearchQuery;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Pages\ProductGroupPage;
use SilverCart\Model\Pages\ProductGroupPageController;
use SilverCart\Model\Pages\SearchResultsPage;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\ProductTranslation;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Convert;
use SilverStripe\ErrorPage\ErrorPage;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\PaginatedList;

/**
 * SearchResultsPage Controller class.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SearchResultsPageController extends ProductGroupPageController {
    
    const SESSION_KEY_SEARCH_QUERY = 'SilverCart.SearchQuery';
    const SESSION_KEY_SEARCH_CONTEXT = 'SilverCart.SearchContext';
    
    /**
     * list of allowed actions
     *
     * @var array
     */
    private static $allowed_actions = [
        'SearchByQuery',
        'cc',
        'ProductGroupPageSelectorsForm',
    ];

    /**
     * Contains a list of all registered filter plugins.
     *
     * @var array
     */
    public static $registeredFilterPlugins = [];
    
    /**
     * pagination start value
     * 
     * @var integer 
     */
    protected $SQL_start = 0;
    
    /**
     * Contains filters for the SQL query that retrieves the products for this
     * page.
     *
     * @var array
     */
    protected $listFilters = [];

    /**
     * Contains the list of found products for this search. This is used for
     * caching purposes.
     *
     * @var DataList
     */
    protected $searchResultProducts;

    /**
     * Contains all classes to use as search context
     *
     * @var array
     */
    protected static $registeredSearchContexts = [
        Product::class,
    ];

    /**
     * current search context used
     *
     * @var string
     */
    protected $currentSearchContext = null;

    /**
     * current search context object used
     *
     * @var string
     */
    protected $currentSearchContextObject = null;

    /**
     * search context objects 
     *
     * @var string
     */
    protected $searchContextObjects = null;

    /**
     * Determines whether to use product runtime cache or not.
     *
     * @var bool
     */
    protected $productRuntimeCacheEnabled = true;

    /**
     * returns a array with all registered search contexts
     *
     * @return array
     */
    public static function getRegisteredSearchContexts() {
        return self::$registeredSearchContexts;
    }

    /**
     * add a object class to use as search context in SilverCart's search
     *
     * @param string $objectClass classname of the object to search for
     *
     * @return void
     *
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 07.06.2013
     */
    public static function addSearchContext($objectClass) {
        if (class_exists($objectClass) &&
            !in_array($objectClass, self::getRegisteredSearchContexts())) {
            self::$registeredSearchContexts = array_merge(
                self::getRegisteredSearchContexts(),
                [$objectClass]
            );
        }
    }

    /**
     * set the current search context. default is Product
     *
     * @param string $searchContext search context
     *
     * @return void
     */
    public function setCurrentSearchContext($searchContext) {
        if (is_null($searchContext) ||
            !in_array($searchContext, self::getRegisteredSearchContexts())) {
            $searchContext = Product::class;
        }
        $this->currentSearchContext = $searchContext;
        Tools::Session()->set(static::SESSION_KEY_SEARCH_CONTEXT, $searchContext);
        Tools::saveSession();
    }

    /**
     * returns the current search context
     *
     * @return string
     */
    public function getCurrentSearchContext() {
        if (is_null($this->currentSearchContext)) {
            $this->setCurrentSearchContext(Tools::Session()->get(static::SESSION_KEY_SEARCH_CONTEXT));
        }
        return $this->currentSearchContext;
    }

    /**
     * returns the current search context
     *
     * @return string
     */
    public function getCurrentSearchContextObject() {
        if (is_null($this->currentSearchContextObject)) {
            $contextObject = $this->getCurrentSearchContext();
            $this->currentSearchContextObject = singleton($contextObject);
        }
        return $this->currentSearchContextObject;
    }

    /**
     * returns the current search context objects
     *
     * @return ArrayList
     */
    public function getSearchContextObjects() {
        if (is_null($this->searchContextObjects)) {
            $this->searchContextObjects = new ArrayList();
            $contexts                   = self::getRegisteredSearchContexts();
            foreach ($contexts as $context) {
                $contextObject = new $context();
                $contextObject->IsCurrentSearchContext = $this->getCurrentSearchContext() == $context;
                $this->searchContextObjects->push($contextObject);
            }
        }
        return $this->searchContextObjects;
    }
    
    /**
     * Returns whether the default search context is active.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.06.2013
     */
    public function IsDefaultSearchContext() {
        return $this->getCurrentSearchContext() == Product::class;
    }

    /**
     * Registers an object as a filter plugin. Before getting the result set
     * the method 'filter' is called on the plugin. It has to return an array
     * with filters to deploy on the query.
     *
     * @param Object $plugin The filter plugin object
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.05.2012
     */
    public static function registerFilterPlugin($plugin) {
        $reflectionClass = new ReflectionClass($plugin);
        
        if ($reflectionClass->hasMethod('filter')) {
            self::$registeredFilterPlugins[] = new $plugin();
        }
    }
    
    /**
     * Indicates wether a filter plugin can be registered for the current view.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.08.2011
     */
    public function canRegisterFilterPlugin() {
        return true;
    }
    
    /**
     * Diese Funktion wird beim Initialisieren ausgeführt
     * 
     * @param string $skip param only added because it exists on parent::init()
     *                     to avoid strict notice
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.06.2014
     */
    protected function init($skip = false) {
        Product::addExtendedSortableFrontendFields(
                [
                    '' => _t(SearchResultsPage::class . '.RELEVANCESORT', 'Relevance'),
                ]
        );
        parent::init(true);
        
        if ($this->isProductDetailView()) {
            // product detail views are not possible on SearchResultsPage
            $this->redirect(ErrorPage::get()->filter('ErrorCode', '404')->first()->Link());
        }
       
        $this->searchObjectHandler();
    }
    
    /**
     * Action to change the current search context
     * 
     * @param HTTPRequest $request Request
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.06.2013
     */
    public function cc(HTTPRequest $request) {
        $newContext = $request->param('ID');
        $this->setCurrentSearchContext($newContext);
        return $this->render();
    }

    /**
     * Returns the cache key parts for this product group
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2012
     */
    public function CacheKeyParts() {
        if (is_null($this->cacheKeyParts)) {
            parent::CacheKeyParts();
            $this->cacheKeyParts[] = sha1($this->getSearchQuery()) . md5($this->getSearchQuery());
        }
        return $this->cacheKeyParts;
    }

    /**
     * Search object handler
     *
     * @return void
     *
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 07.06.2013
     */
    protected function searchObjectHandler() {
        if ($this->IsDefaultSearchContext()) {
            $this->searchProducts();
        } else {
            $context = $this->getCurrentSearchContextObject();
            $context->initSearchHandler($this);
        }
    }
    
    /**
     * Returns the rendered search results.
     * 
     * @return string
     */
    public function getRenderedSearchResults() {
        $context = $this->getCurrentSearchContextObject();
        $context->initSearchHandler($this);
        return $context->renderSearchResults();
    }

    /**
     * Executes a product search.
     *
     * @return void
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.11.2013
     */
    protected function searchProducts() {  
        $SQL_start              = $this->getSqlOffset();
        $searchResultProducts   = $this->buildSearchResultProducts();

        if (!$searchResultProducts) {
            $searchResultProducts = new ArrayList();
        }

        $this->searchResultProducts = $searchResultProducts;
    }
    
    /**
     * Builds the DataObject of filtered products
     *
     * @return DataList
     * 
     * @author Sebastian Diel <sdiel@πixeltricks.de>
     * @since 23.09.2014
     */
    public function buildSearchResultProducts() {
        $searchResultProducts       = $this->searchResultProducts;
        $productsPerPage            = $this->getProductsPerPageSetting();

        $SQL_start                  = $this->getSqlOffset();
        $searchQuery                = $this->getSearchQuery();
        $searchTerms                = explode(' ', $searchQuery);
        $filter                     = '';
        $useExtensionResults        = $this->extend('updateSearchResult', $searchResultProducts, $searchQuery, $SQL_start);

        if (empty($useExtensionResults)) {
            $productTable = Tools::get_table_name(Product::class);
            $productTranslationTable = Tools::get_table_name(ProductTranslation::class);
            $this->listFilters['original'] = sprintf('
               "%s"."ProductGroupID" IS NOT NULL AND
               "%s"."ProductGroupID" > 0 AND
               "PGPL"."ID" > 0 AND
               "%s"."isActive" = 1 AND (
                    (
                        "%s"."Title" LIKE \'%s%%\' OR
                        "%s"."ShortDescription" LIKE \'%s%%\' OR
                        "%s"."LongDescription" LIKE \'%s%%\' OR
                        "%s"."Title" LIKE \'%%%s%%\' OR
                        "%s"."ShortDescription" LIKE \'%%%s%%\' OR
                        "%s"."LongDescription" LIKE \'%%%s%%\'
                    ) OR
                   "%s"."MetaKeywords" LIKE \'%%%s%%\' OR
                   "%s"."ProductNumberShop" LIKE \'%%%s%%\' OR
                   "%s"."EANCode" LIKE \'%%%s%%\' OR
                    STRCMP(
                        SOUNDEX("%s"."Title"), SOUNDEX(\'%s\')
                    ) = 0
                )
                ',
                $productTable,
                $productTable,
                $productTable,
                $productTranslationTable,
                $searchQuery,
                $productTranslationTable,
                $searchQuery,
                $productTranslationTable,
                $searchQuery,
                $productTranslationTable,
                $searchQuery,
                $productTranslationTable,
                $searchQuery,
                $productTranslationTable,
                $searchQuery,
                $productTranslationTable,
                $searchQuery,// MetaKeywords
                $productTable,
                $searchQuery,// ProductNumberShop
                $productTable,
                $searchQuery,// EANCode
                $productTranslationTable,
                $searchQuery// Title SOUNDEX
            );
            if (count($searchTerms) > 1) {
                $this->listFilters['original-soft'] = $this->getSoftSearchFilter($searchTerms);
            }

            if (count(self::$registeredFilterPlugins) > 0) {
                foreach (self::$registeredFilterPlugins as $registeredPlugin) {
                    $pluginFilters = $registeredPlugin->filter();

                    if (is_array($pluginFilters)) {
                        $this->listFilters = array_merge(
                            $this->listFilters,
                            $pluginFilters
                        );
                    }
                }
            }
            $this->extend('updateListFilters', $this->listFilters, $searchTerms);

            foreach ($this->listFilters as $listFilter) {
                if (empty($filter)) {
                    $filter = '(' . $listFilter . ')';
                } else {
                    if (strpos(trim($listFilter), 'AND') !== 0 &&
                        strpos(trim($listFilter), 'OR') !== 0) {
                        $listFilter = 'AND (' . $listFilter . ')';
                    }
                    $filter .= ' ' . $listFilter;
                }
            }

            if (Product::defaultSort() == 'relevance') {
                $sort = '';
            } else {
                $sort = Product::defaultSort();
            }
            
            $searchResultProductsRaw = Product::getProducts(
                $filter,
                $sort,
                [
                    [
                        'table' =>  Tools::get_table_name(ProductGroupPage::class) . '_Live',
                        'on'    => '"PGPL"."ID" = "' . $productTable . '"."ProductGroupID"',
                        'alias' => 'PGPL',
                    ]
                ]
            );
            $searchResultProducts = new PaginatedList($searchResultProductsRaw, $this->getRequest());
            $searchResultProducts->setPageStart($SQL_start);
            $searchResultProducts->setPageLength($productsPerPage);
        }
        
        $this->searchResultProducts  = $searchResultProducts;
        $this->totalNumberOfProducts = $searchResultProducts->count();
        
        $searchQueryObject = SearchQuery::get_by_query(Convert::raw2sql($searchQuery));
        if ($searchQueryObject->Hits != $this->totalNumberOfProducts) {
            $searchQueryObject->Hits = $this->totalNumberOfProducts;
            $searchQueryObject->write();
        }
        
        return $this->searchResultProducts;
    }
    
    /**
     * Returns a more soft search filter to match more results.
     * 
     * @param array $searchTerms List of search terms (originally combined by a white space).
     * 
     * @return string
     */
    protected function getSoftSearchFilter($searchTerms) {
        $softSearchQuery         = implode('%', $searchTerms);
        $productTranslationTable = Tools::get_table_name(ProductTranslation::class);
        $softSearchFilter        = sprintf('OR (
                "%s"."Title" LIKE \'%s%%\' OR
                "%s"."ShortDescription" LIKE \'%s%%\' OR
                "%s"."LongDescription" LIKE \'%s%%\' OR
                "%s"."Title" LIKE \'%%%s%%\' OR
                "%s"."ShortDescription" LIKE \'%%%s%%\' OR
                "%s"."LongDescription" LIKE \'%%%s%%\' OR
                "%s"."MetaKeywords" LIKE \'%%%s%%\'
            )',
            $productTranslationTable, // Title [starts with]
            $softSearchQuery,         // Title [starts with]
            $productTranslationTable, // ShortDescription [starts with]
            $softSearchQuery,         // ShortDescription [starts with]
            $productTranslationTable, // LongDescription [starts with]
            $softSearchQuery,         // LongDescription [starts with]
            $productTranslationTable, // Title
            $softSearchQuery,         // Title
            $productTranslationTable, // ShortDescription
            $softSearchQuery,         // ShortDescription
            $productTranslationTable, // LongDescription
            $softSearchQuery,         // LongDescription
            $productTranslationTable, // MetaKeywords
            $softSearchQuery          // MetaKeywords
        );
        $this->extend('updateSoftSearchFilter', $softSearchFilter, $searchTerms);
        return $softSearchFilter;
    }

    /**
     * Return the start value for the limit part of the sql query that
     * retrieves the product list for the current product group page.
     * 
     * @param int|bool $numberOfProducts only defined because it exists on parent::getSqlOffset() to avoid strict notice
     *
     * @return int
     */
    public function getSqlOffset($numberOfProducts = false) {
        $productsPerPage = $this->getProductsPerPageSetting();
        
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
     * Sets the results.
     * 
     * @param SS_List $searchResultProducts Result list.
     * 
     * @return void
     */
    public function setSearchResultProducts($searchResultProducts) {
        $this->searchResultProducts = $searchResultProducts;
    }

    /**
     * Returns the products that match the search result in any kind
     * 
     * @param int    $numberOfProducts only defined because it exists on parent::getProducts to avoid strict notice
     * @param string $sort             only defined because it exists on parent::getProducts to avoid strict notice
     * @param bool   $disableLimit     only defined because it exists on parent::getProducts to avoid strict notice
     * @param bool   $force            only defined because it exists on parent::getProducts to avoid strict notice
     *
     * @return PaginatedList
     */
    public function getProducts($numberOfProducts = false, $sort = false, $disableLimit = false, $force = false) {
        if (is_null($this->searchResultProducts) || $force) {
            $this->buildSearchResultProducts();
        }
        $searchResultProducts = $this->searchResultProducts;
        if (!$this->productRuntimeCacheEnabled()) {
            $this->searchResultProducts = null;
        }
        return $searchResultProducts;
    }
    
    /**
     * Disables the product runtime cache.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.12.2015
     */
    public function disableProductRuntimeCache() {
        $this->productRuntimeCacheEnabled = false;
    }
    
    /**
     * Enables the product runtime cache.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.12.2015
     */
    public function enableProductRuntimeCache() {
        $this->productRuntimeCacheEnabled = true;
    }
    
    /**
     * Returns whether the product runtime cache is enabled.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.12.2015
     */
    public function productRuntimeCacheEnabled() {
        return $this->productRuntimeCacheEnabled;
    }
    
    /**
     * Returns the SQL filter statement for the current query.
     *
     * @param boolean $excludeFilter Optionally the name of the filter to exclude
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.08.2011
     */
    public function getListFilters($excludeFilter = false) {
        $filter = '';
        
        foreach ($this->listFilters as $listFilterIdenfitier => $listFilter) {
            if ($listFilterIdenfitier != $excludeFilter) {
                $filter .= ' ' . $listFilter;
            }
        }
        
        return $filter;
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
     * @since 20.04.2011
     */
    public function HasMorePagesThan($maxResults = 10) {
        $items = $this->getProducts()->Pages()->count();
        $hasMoreResults = false;

        if ($items > $maxResults) {
            $hasMoreResults = true;
        }

        return $hasMoreResults;
    }
    
    /**
     * Indicates wether the resultset of the product query returns more
     * products than the number given (defaults to 10).
     *
     * @param int $maxResults The number of results to check
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.08.2011
     */
    public function HasMoreProductsThan($maxResults = 10) {
        $products = $this->getProducts();
        if ($products &&
            $products->count() > $maxResults) {
            return true;
        }
        
        return false;
    }

    /**
     * returns the search query out of the session.
     *
     * @return string
     */
    public function getPlainSearchQuery() {
        return Tools::Session()->get(static::SESSION_KEY_SEARCH_QUERY);
    }

    /**
     * returns the search query out of the session for the template.
     *
     * @return string
     */
    public function getEncodedSearchQuery() {
        return htmlentities(
            stripslashes(Tools::Session()->get(static::SESSION_KEY_SEARCH_QUERY)),
            ENT_COMPAT,
            'UTF-8'
        );
    }
    
    /**
     * Returns the search query
     * 
     * @return string
     */
    public function getSearchQuery() {
        $searchQuery = trim(Convert::raw2sql(Tools::Session()->get(static::SESSION_KEY_SEARCH_QUERY)));
        return $searchQuery;
    }

        /**
     * Returns the total number of search results.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.06.2011
     */
    public function TotalSearchResults() {
        $totalItems = 0;
        
        if ($this->IsDefaultSearchContext()) {
            if ($this->getProducts()) {
                $totalItems = $this->getProducts()->count();
            }
        } else {
            $context    = $this->getCurrentSearchContextObject();
            $totalItems = $context->TotalSearchResults();
        }
        
        return $totalItems;
    }
    
    /**
     * Returns the number of products per page according to where it is set.
     * Highest priority has the customer's configuration setting if available.
     * Next comes the shop owners setting for this page; if that's not
     * configured we use the global setting from Config.
     *
     * @return int
     */
    public function getProductsPerPageSetting() {
        $productsPerPage = 0;
        $member          = Customer::currentUser();
        
        if ($member &&
            $member->getCustomerConfig() &&
            $member->getCustomerConfig()->productsPerPage !== null) {
            $productsPerPage = $member->getCustomerConfig()->productsPerPage;
            
            if ($productsPerPage == 0) {
                $productsPerPage = Config::getProductsPerPageUnlimitedNumber();
            }
        } else if ($this->productsPerPage) {
            $productsPerPage = $this->productsPerPage;
        } else {
            $productsPerPage = Config::ProductsPerPage();
        }
        
        return $productsPerPage;
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
     * @example $productGroup->addListFilter('ManufacturerID','5');
     *          Will add the following filter: "AND \"ManufacturerID\" = '5'"
     * @example $productGroup->addListFilter('ManufacturerID','(5,6,7)','IN','OR');
     *          Will add the following filter: "OR \"ManufacturerID\" IN (5,6,7)"
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.08.2011
     */
    public function addListFilter($property, $value, $comparison = '=', $operator = 'AND') {
        if ($comparison == 'IN') {
            $this->listFilters[] = $operator . " \"" . $property . "\" " . $comparison . " (" . $value . ")";
        } else {
            $this->listFilters[] = $operator . " \"" . $property . "\" " . $comparison . " '" . $value . "'";
        }
    }
    
    /**
     * URL action to search by a saved search query
     *
     * @param HTTPRequest $request HTTP request
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.06.2012
     */
    public function SearchByQuery(HTTPRequest $request) {
        $redirectBack   = true;
        $searchQueryID  = $request->param('ID');
        if (is_numeric($searchQueryID)) {
            $searchQuery = SearchQuery::get()->byID($searchQueryID);
            if ($searchQuery) {
                $redirectBack = false;
                Tools::Session()->set(static::SESSION_KEY_SEARCH_QUERY, $searchQuery->SearchQuery);
                Tools::saveSession();
                $this->redirect($this->Link());
            }
        }
        if ($redirectBack) {
            $this->redirectBack();
        }
    }
    
    /**
     * Returns the ProductGroupPageSelectorsForm.
     * 
     * @return ProductGroupPageSelectorsForm
     */
    public function ProductGroupPageSelectorsForm() {
        $form = new ProductGroupPageSelectorsForm($this);
        return $form;
    }
    
}