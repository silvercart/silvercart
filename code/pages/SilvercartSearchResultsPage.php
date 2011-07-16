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
class SilvercartSearchResultsPage extends Page {

    public static $singular_name = "";
    public static $allowed_children = array(
        'none'
    );
    
    public static $icon = "silvercart/images/page_icons/search";

    /**
     * Attributes.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.04.2011
     */
    public static $db = array(
        'productsPerPage' => 'Int'
    );

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

        $productsPerPageField = new TextField('productsPerPage', _t('SilvercartProductGroupPage.PRODUCTSPERPAGE'));
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
class SilvercartSearchResultsPage_Controller extends Page_Controller {

    protected $searchResultProducts;

    /**
     * Diese Funktion wird beim Initialisieren ausgeführt
     *
     * @return void
     *
     * @author Sascha Köhler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2011
     */
    public function init() {
        parent::init();
        $searchQuery            = Convert::raw2sql($this->getSearchQuery());
        $searchResultProducts   = $this->searchResultProducts;

        if ($this->productsPerPage) {
            $productsPerPage = $this->productsPerPage;
        } else {
            $productsPerPage = SilvercartConfig::ProductsPerPage();
        }

        $SQL_start = $this->getSqlOffset();

        $cachekey = 'SilvercartSearchResultsPage'.sha1($searchQuery).'_'.md5($searchQuery).'_'.$SQL_start.'_'.SilvercartGroupViewHandler::getActiveGroupView();
        $cache    = SS_Cache::factory($cachekey);
        $result   = $cache->load($cachekey);
        
        if ($result) {
            $searchResultProducts= unserialize($result);
        } else {
            $useExtensionResults = $this->extend('updateSearchResult', $searchResultProducts, $searchQuery, $SQL_start);
            
            if (empty($useExtensionResults)) {
                $solr = new Apache_Solr_Service('localhost', SilvercartConfig::apacheSolrPort(), SilvercartConfig::apacheSolrUrl());
                
                if (SilvercartConfig::UseApacheSolrSearch() &&
                    $solr->ping()) {
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
                            // TODO: log solr error
                            echo $response->getHttpStatusMessage();
                        }
                    }
                    $searchResultProducts = new DataObjectSet($searchResultProducts);
                    $searchResultProducts->setPageLimits($SQL_start, $productsPerPage, $foundProductsTotal);
                } else {
                    // --------------------------------------------------------
                    // Regular search
                    // --------------------------------------------------------
                    $whereClause = sprintf("
                        `SilvercartProductGroupID` IS NOT NULL AND
                        `SilvercartProductGroupID` > 0 AND
                        `isActive` = 1 AND (
                            `Title` LIKE '%%%s%%' OR
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
                        $searchQuery,// ShortDescription
                        $searchQuery,// LongDescription
                        $searchQuery,// MetaKeywords
                        $searchQuery,// ProductNumberShop
                        $searchQuery// Title SOUNDEX
                    );
                    $searchResultProducts = SilvercartProduct::get( $whereClause, null, null, sprintf("%d,%d", $SQL_start, $productsPerPage));
                }
            }

            if (!$searchResultProducts) {
                $searchResultProducts = new DataObjectSet();
            }
            
            $cache->save(serialize($searchResultProducts));
        }

        $this->searchResultProducts = $searchResultProducts;
        
        $productIdx                 = 0;
        if ($searchResultProducts) {
            $productAddCartForm = $this->getCartFormName();
            foreach ($searchResultProducts as $product) {
                $this->registerCustomHtmlForm('ProductAddCartForm'.$productIdx, new $productAddCartForm($this, array('productID' => $product->ID)));
                $product->productAddCartForm = $this->InsertCustomHtmlForm(
                    'ProductAddCartForm' . $productIdx,
                    array(
                        $product
                    )
                );
                $productIdx++;
            }
        }
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
        if ($this->productsPerPage) {
            $productsPerPage = $this->productsPerPage;
        } else {
            $productsPerPage = SilvercartConfig::ProductsPerPage();
        }
        
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.11.10
     */
    public function getProducts() {
        return $this->searchResultProducts;
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
     * returns the search query out of the session for the template.
     *
     * @return String the search query saved in the session
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.11.10
     */
    public function getSearchQuery() {
        return stripslashes(Session::get('searchQuery'));
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
}
