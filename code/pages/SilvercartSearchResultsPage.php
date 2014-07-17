<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * page type to display search results
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartSearchResultsPage extends SilvercartProductGroupPage {
    
    public static $allowed_children = 'none';
    
    /**
     * We set a custom icon for this page type here
     *
     * @var string
     */
    public static $icon = "silvercart/images/page_icons/metanavigation_page_search";

    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'productsPerPage' => 'Int'
    );
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
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
     * @return FieldList Fields of the CMS
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->removeByName('useContentFromParent');
        $fields->removeByName('DoNotShowProducts');
        $fields->removeByName('productGroupsPerPage');
        $fields->removeByName('DefaultGroupHolderView');
        $fields->removeByName('UseOnlyDefaultGroupHolderView');

        return $fields;
    }
}

/**
 * correlating controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 * @since 23.10.2010
 */
class SilvercartSearchResultsPage_Controller extends SilvercartProductGroupPage_Controller {
    
    /**
     * list of allowed actions
     *
     * @var array
     */
    public static $allowed_actions = array(
        'SearchByQuery',
        'cc',
    );

    /**
     * Contains a list of all registered filter plugins.
     *
     * @var array
     */
    public static $registeredFilterPlugins = array();
    
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
    protected $listFilters = array();

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
    protected static $registeredSearchContexts = array();

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
                array($objectClass)
            );
        }
    }

    /**
     * set the current search context. default is SilvercartProduct
     *
     * @param string $searchContext search context
     *
     * @return void
     */
    public function setCurrentSearchContext($searchContext) {
        if (is_null($searchContext) ||
            !in_array($searchContext, self::getRegisteredSearchContexts())) {
            $searchContext = 'SilvercartProduct';
        }
        $this->currentSearchContext = $searchContext;
        Session::set('searchContext', $searchContext);
        Session::save();
    }

    /**
     * returns the current search context
     *
     * @return string
     */
    public function getCurrentSearchContext() {
        if (is_null($this->currentSearchContext)) {
            $this->setCurrentSearchContext(Session::get('searchContext'));
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
        return $this->getCurrentSearchContext() == 'SilvercartProduct';
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
    public function init($skip = false) {
        SilvercartProduct::addExtendedSortableFrontendFields(
                array(
                    'relevance' => _t('SilvercartSearchResultsPage.RELEVANCESORT'),
                )
        );
        parent::init(true);
        
        if ($this->isProductDetailView()) {
            // product detail views are not possible on SilvercartSearchResultsPage
            $this->redirect(ErrorPage::get()->filter('ErrorCode', '404')->first()->Link());
        }
       
        $this->searchObjectHandler();
    }
    
    /**
     * Action to change the current search context
     * 
     * @param SS_HTTPRequest $request Request
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.06.2013
     */
    public function cc(SS_HTTPRequest $request) {
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
            $this->searchSilvercartProducts();
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
     * [searchSilvercartProducts description]
     *
     * @return void
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.11.2013
     */
    protected function searchSilvercartProducts() {  
        $SQL_start              = $this->getSqlOffset();
        $searchResultProducts   = $this->buildSearchResultProducts();

        if (!$searchResultProducts) {
            $searchResultProducts = new ArrayList();
        }

        $this->searchResultProducts = $searchResultProducts;
        
        $productIdx                 = 0;
        if ($searchResultProducts) {
            $productAddCartFormName = $this->getCartFormName();
            foreach ($searchResultProducts as $product) {
                $backlink = $this->Link()."?start=" .  $SQL_start;
                $productAddCartForm = new $productAddCartFormName($this, array('productID' => $product->ID, 'backLink' => $backlink));
                $this->registerCustomHtmlForm('ProductAddCartForm'.$product->ID, $productAddCartForm);
                $product->productAddCartFormObj = $productAddCartForm;
                $productIdx++;
            }
        }
        
        // Register selector forms, e.g. the "products per page" selector
        $selectorForm = new SilvercartProductGroupPageSelectorsForm($this);
        $selectorForm->setSecurityTokenDisabled();
        $selectorFormBottom = new SilvercartProductGroupPageSelectorsForm($this);
        $selectorFormBottom->setSecurityTokenDisabled();

        $this->registerCustomHtmlForm(
            'SilvercartProductGroupPageSelectors',
            $selectorForm
        );
        $this->registerCustomHtmlForm(
            'SilvercartProductGroupPageSelectorsBottom',
            $selectorFormBottom
        );
    }
    
    /**
     * Builds the DataObject of filtered products
     *
     * @return DataList
     * 
     * @author Sebastian Diel <sdiel@πixeltricks.de>
     * @since 12.06.2013
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
            $this->listFilters['original'] = sprintf("
                \"SilvercartProductGroupID\" IS NOT NULL AND
                \"SilvercartProductGroupID\" > 0 AND
                \"SilvercartProductGroupPage_Live\".\"ID\" > 0 AND
                \"isActive\" = 1 AND (
                    (
                        Title LIKE '%s%%' OR
                        ShortDescription LIKE '%s%%' OR
                        LongDescription LIKE '%s%%' OR
                        Title LIKE '%%%s%%' OR
                        ShortDescription LIKE '%%%s%%' OR
                        LongDescription LIKE '%%%s%%'
                    ) OR
                    \"MetaKeywords\" LIKE '%%%s%%' OR
                    \"ProductNumberShop\" LIKE '%%%s%%' OR
                    \"EANCode\" LIKE '%%%s%%' OR
                    STRCMP(
                        SOUNDEX(\"Title\"), SOUNDEX('%s')
                    ) = 0
                )
                ",
                $searchQuery,
                $searchQuery,
                $searchQuery,
                $searchQuery,
                $searchQuery,
                $searchQuery,
                $searchQuery,// MetaKeywords
                $searchQuery,// ProductNumberShop
                $searchQuery,// EANCode
                $searchQuery// Title SOUNDEX
            );

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
                    $filter =  $listFilter;
                } else {
                    $filter = '(' . $filter . ') ' . $listFilter;
                }
            }

            if (SilvercartProduct::defaultSort() == 'relevance') {
                $sort = '';
            } else {
                $sort = SilvercartProduct::defaultSort();
            }
            
            $searchResultProductsRaw = SilvercartProduct::getProducts(
                $filter,
                $sort,
                "LEFT JOIN \"SilvercartProductGroupPage_Live\" ON \"SilvercartProductGroupPage_Live\".\"ID\" = \"SilvercartProductGroupID\""
            );
            $searchResultProducts = new PaginatedList($searchResultProductsRaw, $this->getRequest());
            $searchResultProducts->setPageStart($SQL_start);
            $searchResultProducts->setPageLength($productsPerPage);
        }
        
        $this->searchResultProducts  = $searchResultProducts;
        $this->totalNumberOfProducts = $searchResultProducts->count();
        return $this->searchResultProducts;
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
     * Returns the products that match the search result in any kind
     * 
     * @param int    $numberOfProducts only defined because it exists on parent::getProducts to avoid strict notice
     * @param string $sort             only defined because it exists on parent::getProducts to avoid strict notice
     * @param bool   $disableLimit     only defined because it exists on parent::getProducts to avoid strict notice
     * @param bool   $force            only defined because it exists on parent::getProducts to avoid strict notice
     *
     * @return DataList|false the resulting products of the search query
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.05.2012
     */
    public function getProducts($numberOfProducts = false, $sort = false, $disableLimit = false, $force = false) {
        if (is_null($this->searchResultProducts)) {
            $this->buildSearchResultProducts();
        }
        return $this->searchResultProducts;
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
     * returns the search query out of the session for the template.
     *
     * @return String the search query saved in the session
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.11.10
     */
    public function getEncodedSearchQuery() {
        return htmlentities(
            stripslashes(Session::get('searchQuery')),
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
        $searchQuery = trim(Convert::raw2sql(Session::get('searchQuery')));
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
     * configured we use the global setting from SilvercartConfig.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public function getProductsPerPageSetting() {
        $productsPerPage = 0;
        $member          = Member::currentUser();
        
        if ($member &&
            $member->getSilvercartCustomerConfig() &&
            $member->getSilvercartCustomerConfig()->productsPerPage !== null) {
            $productsPerPage = $member->getSilvercartCustomerConfig()->productsPerPage;
            
            if ($productsPerPage == 0) {
                $productsPerPage = SilvercartConfig::getProductsPerPageUnlimitedNumber();
            }
        } else if ($this->productsPerPage) {
            $productsPerPage = $this->productsPerPage;
        } else {
            $productsPerPage = SilvercartConfig::ProductsPerPage();
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
     * @example $productGroup->addListFilter('SilvercartManufacturerID','5');
     *          Will add the following filter: "AND \"SilvercartManufacturerID\" = '5'"
     * @example $productGroup->addListFilter('SilvercartManufacturerID','(5,6,7)','IN','OR');
     *          Will add the following filter: "OR \"SilvercartManufacturerID\" IN (5,6,7)"
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
     * @param SS_HTTPRequest $request HTTP request
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.06.2012
     */
    public function SearchByQuery(SS_HTTPRequest $request) {
        $redirectBack   = true;
        $searchQueryID  = $request->param('ID');
        if (is_numeric($searchQueryID)) {
            $searchQuery = DataObject::get_by_id('SilvercartSearchQuery', $searchQueryID);
            if ($searchQuery) {
                $redirectBack = false;
                Session::set('searchQuery', $searchQuery->SearchQuery);
                Session::save();
                $this->redirect($this->Link());
            }
        }
        if ($redirectBack) {
            $this->redirectBack();
        }
    }
}
