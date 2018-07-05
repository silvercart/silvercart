<?php

namespace SilverCart\Model;

use SilverCart\Dev\Tools;
use SilverStripe\ORM\DataObject;

/**
 * A search query.
 *
 * @package SilverCart
 * @subpackage Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 10.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SearchQuery extends DataObject {
    
    /**
     * ORM attributes
     *
     * @var array
     */
    private static $db = [
        'SearchQuery'   => 'Varchar(255)',
        'Locale'        => \SilverCart\ORM\FieldType\DBLocale::class,
        'Count'         => 'Int',
        'Hits'          => 'Int',
    ];

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartSearchQuery';
    
    /**
     * Returns a SearchQuery with the given query. Creates one, if not exists.
     *
     * @param string $query Query to get SearchQuery for
     * 
     * @return SearchQuery 
     */
    public static function get_by_query($query) {
        $searchQuery = self::get()
                ->filter([
                    'SearchQuery' => $query,
                    'Locale'      => Tools::current_locale(),
                ])
                ->first();
        if (!($searchQuery instanceof SearchQuery)) {
            $searchQuery = new SearchQuery();
            $searchQuery->Locale        = Tools::current_locale();
            $searchQuery->SearchQuery   = $query;
            $searchQuery->Count         = 0;
            $searchQuery->Hits          = 0;
        }
        return $searchQuery;
    }
    
    /**
     * Returns a SearchQuery with the given query. Creates one, if not exists.
     *
     * @param int $limit Limit for the queries
     * 
     * @return ArrayList 
     */
    public static function get_most_searched($limit) {
        $searchQueries = self::get()
                ->filter('Locale', Tools::current_locale())
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
        $searchResultsLink  = Tools::PageByIdentifierCodeLink('SilvercartSearchResultsPage');
        return $searchResultsLink . 'SearchByQuery/' . $this->ID;
    }
}

