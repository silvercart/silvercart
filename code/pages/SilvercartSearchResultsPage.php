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
 * page type to display search results
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartSearchResultsPage extends SilvercartProductGroupPage {
    
    public static $allowed_children = array(
        'none'
    );
    
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
     * @since 20.04.2011
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $productsPerPageField = new TextField('productsPerPage', $this->fieldLabel('productsPerPage'));
        $fields->addFieldToTab('Root.Content.Main', $productsPerPageField, 'IdentifierCode');

        return $fields;
    }
}

/**
 * correlating controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2010 pixeltricks GmbH
 * @since 23.10.2010
 */
class SilvercartSearchResultsPage_Controller extends SilvercartProductGroupPage_Controller {
    
    public static $allowed_actions = array(
        'SearchByQuery',
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
     * @var DataObjectSet
     */
    protected $searchResultProducts;

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
     * @return void
     *
     * @author Sascha Köhler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.05.2012
     */
    public function init() {
        parent::init(true);
        if (isset($_GET['start'])) {
            $this->SQL_start = (int)$_GET['start'];
        }
        $searchQuery            = Convert::raw2sql(Session::get('searchQuery'));
        $searchResultProducts   = $this->searchResultProducts;
        $productsPerPage        = $this->getProductsPerPageSetting();

        $SQL_start = $this->getSqlOffset();

        $cachekey = 'SilvercartSearchResultsPage'.sha1($searchQuery).'_'.md5($searchQuery).'_'.$SQL_start.'_'.SilvercartGroupViewHandler::getActiveGroupView();
        $cache    = SS_Cache::factory($cachekey);
        $result   = $cache->load($cachekey);
        
        
        // Cache is deactivated because of form registration problems.
        if (1 == 2 && $result) {
            $searchResultProducts= unserialize($result);
        } else {
            $searchResultProducts = $this->buildSearchResultProducts();

            if (!$searchResultProducts) {
                $searchResultProducts = new DataObjectSet();
            }
            
            $cache->save(serialize($searchResultProducts));
        }

        $this->searchResultProducts = $searchResultProducts;
        
        $productIdx                 = 0;
        if ($searchResultProducts) {
            $productAddCartFormName = $this->getCartFormName();
            foreach ($searchResultProducts as $product) {
                $backlink = $this->Link()."?start=".  $this->SQL_start;
                $productAddCartForm = new $productAddCartFormName($this, array('productID' => $product->ID, 'backLink' => $backlink));
                $this->registerCustomHtmlForm('ProductAddCartForm'.$productIdx, $productAddCartForm);
                $product->productAddCartForm = $this->InsertCustomHtmlForm(
                    'ProductAddCartForm' . $productIdx,
                    array(
                        $product
                    )
                );
                $product->productAddCartFormObj = $productAddCartForm;
                $productIdx++;
            }
        }
        
        // Register selector forms, e.g. the "products per page" selector
        $selectorForm = new SilvercartProductGroupPageSelectorsForm($this);
        $selectorForm->setSecurityTokenDisabled();

        $this->registerCustomHtmlForm(
            'SilvercartProductGroupPageSelectors',
            $selectorForm
        );
    }
    
    /**
     * Builds the DataObject of filtered products
     *
     * @return DataObjectSet
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.05.2012
     */
    public function buildSearchResultProducts() {
        if (isset($_GET['start'])) {
            $this->SQL_start = (int)$_GET['start'];
        }
        $searchResultProducts       = $this->searchResultProducts;
        $productsPerPage            = $this->getProductsPerPageSetting();

        $SQL_start                  = $this->getSqlOffset();
        $searchQuery                = trim(Convert::raw2sql(Session::get('searchQuery')));
        $searchTerms                = explode(' ', $searchQuery);
        $filter                     = '';
        $filteredQuerySearchQuery   = '';
        $useExtensionResults        = $this->extend('updateSearchResult', $searchResultProducts, $searchQuery, $SQL_start);

        if (empty($useExtensionResults)) {
            if (SilvercartConfig::UseApacheSolrSearch()) {
                $solr = new Apache_Solr_Service('localhost', SilvercartConfig::apacheSolrPort(), SilvercartConfig::apacheSolrUrl());
                if ($solr->ping()) {
                    // --------------------------------------------------------
                    // Apache Solr search
                    // --------------------------------------------------------
                    $searchResultProducts = array();
                    $foundProductsTotal   = 0;
                    $queries              = array(
                        sprintf(
                            "Title: %s",
                            $searchQuery
                        )
                    );

                    foreach ($queries as $query) {
                        $response = $solr->search($query, $SQL_start, $productsPerPage);

                        if ($response->getHttpStatus() == 200) {
                            $foundProductsTotal += $response->response->numFound;

                            if ($foundProductsTotal > 0) {
                                foreach ($response->response->docs as $doc ) {
                                    $product = DataObject::get_by_id(
                                        'SilvercartProduct',
                                        $doc->ID
                                    );

                                    if ($product) {
                                        $searchResultProducts[] = $product;
                                    }
                                }
                            }
                        } else {
                            echo $response->getHttpStatusMessage();
                        }
                    }
                    $searchResultProducts = new DataObjectSet($searchResultProducts);
                    $searchResultProducts->setPageLimits($SQL_start, $productsPerPage, $foundProductsTotal);
                }
            } else {
                // remove words with less than 3 chars
                foreach ($searchTerms as $value) {
                    if (strlen($value) >= 3) {
                        $filteredQuerySearchQuery .= '+' . $value;
                    }
                }
                $this->listFilters['original'] = sprintf("
                    `SilvercartProductGroupID` IS NOT NULL AND
                    `SilvercartProductGroupID` > 0 AND
                    `SilvercartProductGroupPage_Live`.`ID` > 0 AND
                    `isActive` = 1 AND (
                        `Title` LIKE '%%%s%%' OR
                        MATCH(Title) AGAINST ('%%%s%%' IN BOOLEAN MODE) > 1 OR
                        MATCH(ShortDescription) AGAINST ('%%%s%%' IN BOOLEAN MODE) > 1 OR
                        MATCH(LongDescription) AGAINST ('%%%s%%' IN BOOLEAN MODE) > 1 OR
                        `ShortDescription` LIKE '%%%s%%' OR
                        `LongDescription` LIKE '%%%s%%' OR
                        `MetaKeywords` LIKE '%%%s%%' OR
                        `ProductNumberShop` LIKE '%%%s%%' OR
                        STRCMP(
                            SOUNDEX(`Title`), SOUNDEX('%s')
                        ) = 0
                    )
                    ",
                    $searchQuery,// Title
                    $filteredQuerySearchQuery, // Title via Match Against
                    $filteredQuerySearchQuery, // ShortDescription via Match Against
                    $filteredQuerySearchQuery, // LongDescription via Match Against
                    $searchQuery,// ShortDescription
                    $searchQuery,// LongDescription
                    $searchQuery,// MetaKeywords
                    $searchQuery,// ProductNumberShop
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

                foreach ($this->listFilters as $listFilterIdentifier => $listFilter) {
                    $filter .= ' ' . $listFilter;
                }
                
                $searchResultProducts = SilvercartProduct::get(
                    $filter,
                    null,
                    "LEFT JOIN `SilvercartProductGroupPage_Live` ON `SilvercartProductGroupPage_Live`.`ID` = `SilvercartProductGroupID`"
                    ,
                    sprintf(
                        "%d,%d",
                        $SQL_start,
                        $productsPerPage
                    )
                );
            }
        }
        $this->searchResultProducts = $searchResultProducts;
        return $this->searchResultProducts;
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
     * @return DataObjectSet|false the resulting products of the search query
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.05.2012
     */
    public function getProducts() {
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
            $products->TotalItems() > $maxResults) {
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
     * Returns the total number of search results.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 12.06.2011
     */
    public function TotalSearchResults() {
        $totalItems = 0;
        
        if ($this->getProducts()) {
            $totalItems = $this->getProducts()->TotalItems();
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
     *          Will add the following filter: "AND `SilvercartManufacturerID` = '5'"
     * @example $productGroup->addListFilter('SilvercartManufacturerID','(5,6,7)','IN','OR');
     *          Will add the following filter: "OR `SilvercartManufacturerID` IN (5,6,7)"
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.08.2011
     */
    public function addListFilter($property, $value, $comparison = '=', $operator = 'AND') {
        if ($comparison == 'IN') {
            $this->listFilters[] = $operator . " `" . $property . "` " . $comparison . " (" . $value . ")";
        } else {
            $this->listFilters[] = $operator . " `" . $property . "` " . $comparison . " '" . $value . "'";
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
                Director::redirect($this->Link());
            }
        }
        if ($redirectBack) {
            Director::redirectBack();
        }
    }
}
