<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Base
 */

/**
 * A search query
 *
 * @package Silvercart
 * @subpackage Base
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 05.06.2012
 * @license see license file in modules root directory
 */
class SilvercartSearchQuery extends DataObject {
    
    /**
     * ORM attributes
     *
     * @var array
     */
    private static $db = array(
        'SearchQuery'   => 'VarChar(255)',
        'Locale'        => 'DbLocale',
        'Count'         => 'Int',
        'Hits'          => 'Int',
    );
    
    /**
     * Returns a SilvercartSearchQuery with the given query. Creates one, if not exists.
     *
     * @param string $query Query to get SilvercartSearchQuery for
     * 
     * @return SilvercartSearchQuery 
     */
    public static function get_by_query($query) {
        $searchQuery = self::get()
                ->filter(array(
                    'SearchQuery' => $query,
                    'Locale'      => Translatable::get_current_locale(),
                ))
                ->first();
        if (!($searchQuery instanceof SilvercartSearchQuery)) {
            $searchQuery = new SilvercartSearchQuery();
            $searchQuery->Locale        = Translatable::get_current_locale();
            $searchQuery->SearchQuery   = $query;
            $searchQuery->Count         = 0;
            $searchQuery->Hits          = 0;
        }
        return $searchQuery;
    }
    
    /**
     * Returns a SilvercartSearchQuery with the given query. Creates one, if not exists.
     *
     * @param int $limit Limit for the queries
     * 
     * @return ArrayList 
     */
    public static function get_most_searched($limit) {
        $searchQueries = self::get()
                ->filter('Locale', Translatable::get_current_locale())
                ->exclude('SearchQuery', '')
                ->sort('Count', 'DESC')
                ->limit($limit);
        return $searchQueries;
    }
    
    /**
     * Returns a link to trigger the search with this query
     *
     * @return string 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2012
     */
    public function Link() {
        $searchResultsLink  = SilvercartTools::PageByIdentifierCodeLink('SilvercartSearchResultsPage');
        return $searchResultsLink . 'SearchByQuery/' . $this->ID;
    }
}

