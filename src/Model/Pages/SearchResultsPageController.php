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
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Convert;
use SilverStripe\ErrorPage\ErrorPage;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\ArrayData;

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
class SearchResultsPageController extends ProductGroupPageController
{
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
     * pagination start value
     * 
     * @var integer 
     */
    protected $SQL_start = 0;
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
    public static function getRegisteredSearchContexts() : array
    {
        return self::$registeredSearchContexts;
    }

    /**
     * add a object class to use as search context in SilverCart's search
     *
     * @param string $objectClass classname of the object to search for
     *
     * @return void
     */
    public static function addSearchContext(string $objectClass) : void
    {
        if (class_exists($objectClass)
         && !in_array($objectClass, self::getRegisteredSearchContexts())
        ) {
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
     * @return SearchResultsPageController
     */
    public function setCurrentSearchContext(string $searchContext) : SearchResultsPageController
    {
        if (is_null($searchContext)
         || !in_array($searchContext, self::getRegisteredSearchContexts())
        ) {
            $searchContext = Product::class;
        }
        $this->currentSearchContext = $searchContext;
        SearchResultsPage::setCurrentSearchContext($searchContext);
        return $this;
    }

    /**
     * returns the current search context
     *
     * @return string
     */
    public function getCurrentSearchContext() : string
    {
        if (is_null($this->currentSearchContext)) {
            $this->setCurrentSearchContext(SearchResultsPage::getCurrentSearchContext());
        }
        return $this->currentSearchContext;
    }

    /**
     * returns the current search context
     *
     * @return object
     */
    public function getCurrentSearchContextObject() : object
    {
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
    public function getSearchContextObjects() : ArrayList
    {
        if (is_null($this->searchContextObjects)) {
            $this->searchContextObjects = ArrayList::create();
            $contexts                   = self::getRegisteredSearchContexts();
            foreach ($contexts as $context) {
                $contextObject = new $context();
                $contextObject->IsCurrentSearchContext = $this->getCurrentSearchContext() === $context;
                $this->searchContextObjects->push($contextObject);
            }
        }
        return $this->searchContextObjects;
    }
    
    /**
     * Returns whether the default search context is active.
     * 
     * @return bool
     */
    public function IsDefaultSearchContext() : bool
    {
        return $this->getCurrentSearchContext() === Product::class;
    }
    
    /**
     * Indicates wether a filter plugin can be registered for the current view.
     *
     * @return bool
     */
    public function canRegisterFilterPlugin() : bool
    {
        return true;
    }
    
    /**
     * Initializes this controller.
     * 
     * @param string $skip param only added because it exists on parent::init()
     *                     to avoid strict notice
     *
     * @return void
     */
    protected function init(bool $skip = false) : void
    {
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
        
        $getQuery     = $this->getRequest()->getVar('q');
        $sessionQuery = SearchResultsPage::getCurrentSearchQuery();
        if (!empty($getQuery)
         && $getQuery != $sessionQuery
        ) {
            SearchQuery::update_by_query(trim(Convert::raw2sql($getQuery)));
            SearchResultsPage::setCurrentSearchQuery($getQuery);
        }
        
        $getCategory     = $this->getRequest()->getVar('c');
        $sessionCategory = SearchResultsPage::getCurrentSearchCategory();
        if ($getCategory != $sessionCategory) {
            SearchResultsPage::setCurrentSearchCategory($getCategory);
        }
        
        $this->searchObjectHandler();
    }
    
    /**
     * Index action. Will redirect to a product detail if a matching product 
     * number was searched for.
     * 
     * @return HTTPResponse
     */
    public function index() : HTTPResponse
    {
        $product = Product::get_by_product_number((string) $this->getSearchQuery());
        if ($product instanceof Product
         && $product->exists()
         && !$product->HideFromSearchResults
        ) {
            return $this->redirect($product->Link());
        }
        return HTTPResponse::create($this->render());
    }
    
    /**
     * Action to change the current search context
     * 
     * @param HTTPRequest $request Request
     * 
     * @return HTTPResponse
     */
    public function cc(HTTPRequest $request) : HTTPResponse
    {
        $newContext = $request->param('ID');
        $this->setCurrentSearchContext($newContext);
        return HTTPResponse::create($this->render());
    }

    /**
     * Search object handler
     *
     * @return void
     */
    protected function searchObjectHandler() : void
    {
        if (!$this->IsDefaultSearchContext()) {
            $context = $this->getCurrentSearchContextObject();
            $context->initSearchHandler($this);
        }
    }
    
    /**
     * Returns the rendered search results.
     * 
     * @return string
     */
    public function getRenderedSearchResults()
    {
        $context = $this->getCurrentSearchContextObject();
        $context->initSearchHandler($this);
        return $context->renderSearchResults();
    }
    
    /**
     * Builds the DataObject of filtered products
     *
     * @return PaginatedList
     */
    public function buildSearchResultProducts() : PaginatedList
    {
        $searchResultProducts       = $this->searchResultProducts;
        $productsPerPage            = $this->getProductsPerPageSetting();

        $SQL_start                  = $this->getSqlOffset();
        $searchQuery                = $this->getSearchQuery();
        $searchCategory             = $this->getSearchCategory();
        $searchTerms                = explode(' ', $searchQuery);
        $filter                     = '';
        $useExtensionResults        = $this->extend('updateSearchResult', $searchResultProducts, $searchQuery, $SQL_start);
        $softSearchFilter           = '';

        if (empty($searchQuery)) {
            $searchResultProducts = PaginatedList::create(ArrayList::create());
        } elseif (empty($useExtensionResults)) {
            $productStageTable             = Product::singleton()->getStageTableName();
            $productTranslationStageTable  = ProductTranslation::singleton()->getStageTableName();
            if (count($searchTerms) > 1) {
                $softSearchFilter = $this->getSoftSearchFilter($searchTerms);
            }
            $this->listFilters['original'] = ""
                    . "{$productStageTable}.ProductGroupID IS NOT NULL"
                    . " AND {$productStageTable}.ProductGroupID > 0"
                    . " AND PGPL.ID > 0"
                    . " AND {$productStageTable}.isActive = 1"
                    . " AND ("
                        . " ("
                            . " {$productTranslationStageTable}.Title LIKE '{$searchQuery}%'"
                            . " OR {$productTranslationStageTable}.ShortDescription LIKE '{$searchQuery}%'"
                            . " OR {$productTranslationStageTable}.LongDescription LIKE '{$searchQuery}%'"
                            . " OR {$productTranslationStageTable}.Title LIKE '%{$searchQuery}%'"
                            . " OR {$productTranslationStageTable}.ShortDescription LIKE '%{$searchQuery}%'"
                            . " OR {$productTranslationStageTable}.LongDescription LIKE '%{$searchQuery}%'"
                        . " )"
                        . " OR {$productStageTable}.Keywords LIKE '%{$searchQuery}%'"
                        . " OR {$productStageTable}.ProductNumberShop LIKE '%{$searchQuery}%'"
                        . " OR {$productStageTable}.EANCode LIKE '%{$searchQuery}%'"
                        . " OR STRCMP(SOUNDEX({$productTranslationStageTable}.Title), SOUNDEX('{$searchQuery}')) = 0"
                        . " {$softSearchFilter}"
                    . " )";

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
                    $filter = "({$listFilter})";
                } else {
                    if (strpos(trim($listFilter), 'AND') !== 0
                     && strpos(trim($listFilter), 'OR') !== 0
                    ) {
                        $listFilter = "AND ({$listFilter})";
                    }
                    $filter .= " {$listFilter}";
                }
            }
            
            if ($searchCategory instanceof ProductGroupPage) {
                $categoryIDs    = $searchCategory->getFlatChildPageIDsForPage($searchCategory->ID);
                $categoryIDList = implode(",", $categoryIDs);
                $filter = "({$filter}) AND {$productStageTable}.ProductGroupID IN ({$categoryIDList})";
            }

            if (Product::defaultSort() == 'relevance') {
                $sort = '';
            } else {
                $sort = Product::defaultSort();
            }
            
            $searchResultProductsRaw = Product::getProductsList(
                "{$productStageTable}.HideFromSearchResults = false AND ({$filter})",
                $sort,
                [
                    [
                        'table' => ProductGroupPage::singleton()->getStageTableName(),
                        'on'    => "PGPL.ID = {$productStageTable}.ProductGroupID",
                        'alias' => 'PGPL',
                    ]
                ]
            );
            $searchResultProducts = PaginatedList::create($searchResultProductsRaw, $this->getRequest());
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
    protected function getSoftSearchFilter(array $searchTerms) : string
    {
        $softSearchQuery               = implode('%', $searchTerms);
        $productStageTable             = Product::singleton()->getStageTableName();
        $productTranslationStageTable  = ProductTranslation::singleton()->getStageTableName();
        $softSearchFilter              = "OR ("
                . "{$productTranslationStageTable}.Title LIKE '{$softSearchQuery}%'" 
                . " OR {$productTranslationStageTable}.ShortDescription LIKE '{$softSearchQuery}%'" 
                . " OR {$productTranslationStageTable}.LongDescription LIKE '{$softSearchQuery}%'" 
                . " OR {$productTranslationStageTable}.Title LIKE '%{$softSearchQuery}%'" 
                . " OR {$productTranslationStageTable}.ShortDescription LIKE '%{$softSearchQuery}%'" 
                . " OR {$productTranslationStageTable}.LongDescription LIKE '%{$softSearchQuery}%'" 
                . " OR {$productStageTable}.Keywords LIKE '%{$softSearchQuery}%'"
                . ")";
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
    public function getSqlOffset($numberOfProducts = false) : int
    {
        $productsPerPage = $this->getProductsPerPageSetting();
        
        if (!isset($_GET['start'])
         || !is_numeric($_GET['start'])
         || (int)$_GET['start'] < 1
        ) {
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
    public function setSearchResultProducts($searchResultProducts) : SearchResultsPageController
    {
        $this->searchResultProducts = $searchResultProducts;
        return $this;
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
    public function getProducts($numberOfProducts = false, $sort = false, $disableLimit = false, $force = false) : PaginatedList
    {
        if (is_null($this->searchResultProducts)
         || $force
        ) {
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
    public function disableProductRuntimeCache() : SearchResultsPageController
    {
        $this->productRuntimeCacheEnabled = false;
        return $this;
    }
    
    /**
     * Enables the product runtime cache.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.12.2015
     */
    public function enableProductRuntimeCache() : SearchResultsPageController
    {
        $this->productRuntimeCacheEnabled = true;
        return $this;
    }
    
    /**
     * Returns whether the product runtime cache is enabled.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.12.2015
     */
    public function productRuntimeCacheEnabled() : bool
    {
        return $this->productRuntimeCacheEnabled;
    }
    
    /**
     * Returns the SQL filter statement for the current query.
     *
     * @param bool $excludeFilter Optionally the name of the filter to exclude
     *
     * @return string
     */
    public function getListFilters($excludeFilter = false) : string
    {
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
    public function HasMorePagesThan(int $maxResults = 10) : bool
    {
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
    public function HasMoreProductsThan(int $maxResults = 10) : bool
    {
        $products = $this->getProducts();
        if ($products
         && $products->count() > $maxResults
        ) {
            return true;
        }
        
        return false;
    }

    /**
     * returns the search query out of the session.
     *
     * @return string
     */
    public function getPlainSearchQuery() : string
    {
        return SearchResultsPage::getCurrentSearchQuery();
    }

    /**
     * returns the search query out of the session for the template.
     *
     * @return string
     */
    public function getEncodedSearchQuery() : string
    {
        return htmlentities(
            stripslashes(SearchResultsPage::getCurrentSearchQuery()),
            ENT_COMPAT,
            'UTF-8'
        );
    }
    
    /**
     * Returns the search query
     * 
     * @return string
     */
    public function getSearchQuery() : string
    {
        $searchQuery = trim(Convert::raw2sql(SearchResultsPage::getCurrentSearchQuery()));
        return $searchQuery;
    }
    
    /**
     * Returns the search category ID
     * 
     * @return int
     */
    public function getSearchCategoryID() : int
    {
        return (int) SearchResultsPage::getCurrentSearchCategory();
    }
    
    /**
     * Returns the search category
     * 
     * @return ProductGroupPage|null
     */
    public function getSearchCategory() : ?ProductGroupPage
    {
        return ProductGroupPage::get()->byID($this->getSearchCategoryID());
    }

    /**
     * Returns the total number of search results.
     *
     * @return int
     */
    public function TotalSearchResults() : int
    {
        $totalItems = 0;
        if ($this->IsDefaultSearchContext()) {
            if ($this->getProducts()) {
                $totalItems = $this->getProducts()->count();
            }
        } else {
            $context    = $this->getCurrentSearchContextObject();
            $totalItems = $context->TotalSearchResults();
        }
        return (int) $totalItems;
    }
    
    /**
     * Returns the number of products per page according to where it is set.
     * Highest priority has the customer's configuration setting if available.
     * Next comes the shop owners setting for this page; if that's not
     * configured we use the global setting from Config.
     *
     * @return int
     */
    public function getProductsPerPageSetting()
    {
        $productsPerPage = 0;
        $member          = Customer::currentUser();
        if ($member
         && $member->getCustomerConfig()
         && $member->getCustomerConfig()->productsPerPage !== null
        ) {
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
    public function addListFilter($property, $value, $comparison = '=', $operator = 'AND') : ProductGroupPageController
    {
        if ($comparison == 'IN') {
            $this->listFilters[] = "{$operator} \"{$property}\" {$comparison} ({$value})";
        } else {
            $this->listFilters[] = "{$operator} \"{$property}\" {$comparison} '{$value}'";
        }
        return $this;
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
    public function SearchByQuery(HTTPRequest $request) : void
    {
        $redirectBack   = true;
        $searchQueryID  = $request->param('ID');
        if (is_numeric($searchQueryID)) {
            $searchQuery = SearchQuery::get()->byID($searchQueryID);
            if ($searchQuery) {
                $redirectBack = false;
                SearchResultsPage::setCurrentSearchQuery($searchQuery->SearchQuery);
                $this->redirect($this->data()->Link());
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
    public function ProductGroupPageSelectorsForm() : ProductGroupPageSelectorsForm
    {
        return ProductGroupPageSelectorsForm::create($this);
    }
    
    /**
     * Returns a tree of search context product groups.
     * 
     * @return ArrayList
     */
    public function getProductGroups() : ArrayList
    {
        $productGroups = ArrayList::create();
        $products      = $this->getProducts();
        $ids           = array_keys($products->map()->toArray());
        if (strpos($this->data()->Link(), '?') === false) {
            $linkBase = "{$this->data()->Link()}?";
        } else {
            $linkBase = "{$this->data()->Link()}&";
        }
        if (count($ids) > 0) {
            $idList            = implode(',', $ids);
            $productTableName  = Product::config()->get('table_name');
            $siteTreeTableName = $this->stageTable("SiteTree", Versioned::get_stage());
            $productGroupPages = ProductGroupPage::get()
                    ->where("{$siteTreeTableName}.ID IN (SELECT ProductGroupID FROM {$productTableName} AS P WHERE P.ID IN ({$idList}))");
            foreach ($productGroupPages as $productGroupPage) {
                /* @var $productGroupPage ProductGroupPage */
                $rootProductGroup = $productGroupPage;
                $groups           = [$rootProductGroup];
                while ($rootProductGroup->Parent() instanceof ProductGroupPage) {
                    $rootProductGroup = $rootProductGroup->Parent();
                    $groups[] = $rootProductGroup;
                }
                $existing = $productGroups->find('ID', $rootProductGroup->ID);
                if (is_null($existing)) {
                    $existing = ArrayData::create([
                        'ID'        => $rootProductGroup->ID,
                        'Sort'      => $rootProductGroup->Sort,
                        'Title'     => $rootProductGroup->Title,
                        'MenuTitle' => $rootProductGroup->Title,
                        'Link'      => "{$linkBase}c={$rootProductGroup->ID}",
                        'Children'  => ArrayList::create(),
                    ]);
                    $productGroups->push($existing);
                }
                $groups = array_reverse($groups);
                array_shift($groups);
                if (count($groups) > 0) {
                    $currentGroup = $existing;
                    foreach ($groups as $group) {
                        $existingChild = $currentGroup->Children->find('ID', $group->ID);
                        if (is_null($existingChild)) {
                            $existingChild = ArrayData::create([
                                'ID'        => $group->ID,
                                'Sort'      => $group->Sort,
                                'Title'     => $group->Title,
                                'MenuTitle' => $group->Title,
                                'Link'      => "{$linkBase}c={$group->ID}",
                                'Children'  => ArrayList::create(),
                            ]);
                            $currentGroup->Children->push($existingChild);
                            $currentGroup->Children->sort("Sort");
                        }
                        $currentGroup = $existingChild;
                    }
                }
            }
        }
        return $productGroups->sort("Sort");
    }
}