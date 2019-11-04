<?php

namespace SilverCart\Model;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\Page;
use SilverStripe\ORM\DataList;
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
class SearchQuery extends DataObject
{
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
    public static function get_by_query($query) : SearchQuery
    {
        $searchQuery = self::get()
                ->filter([
                    'SearchQuery' => $query,
                    'Locale'      => Tools::current_locale(),
                ])
                ->first();
        if (!($searchQuery instanceof SearchQuery)) {
            $searchQuery = SearchQuery::create();
            $searchQuery->Locale        = Tools::current_locale();
            $searchQuery->SearchQuery   = $query;
            $searchQuery->Count         = 0;
            $searchQuery->Hits          = 0;
        }
        return $searchQuery;
    }
    
    /**
     * Updates the SearchQuery with the given query. Creates one, if not exists.
     *
     * @param string $query Query to get SearchQuery for
     * 
     * @return SearchQuery
     */
    public static function update_by_query($query) : SearchQuery
    {
        $searchQuery = self::get_by_query($query);
        if (!empty($searchQuery->SearchQuery)) {
            $searchQuery->Count++;
            $searchQuery->write();
        }
        return $searchQuery;
    }
    
    /**
     * Returns a SearchQuery with the given query. Creates one, if not exists.
     *
     * @param int $limit Limit for the queries
     * 
     * @return DataList 
     */
    public static function get_most_searched($limit) : DataList
    {
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
     * @since 26.09.2018
     */
    public function Link() : string
    {
        return Tools::PageByIdentifierCode(Page::IDENTIFIER_SEARCH_RESULTS_PAGE)->QueryLink($this->SearchQuery);
    }
}