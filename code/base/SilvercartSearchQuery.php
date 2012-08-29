<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * @subpackage Base
 */

/**
 * A search query
 *
 * @package Silvercart
 * @subpackage Base
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 05.06.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartSearchQuery extends DataObject {
    
    /**
     * ORM attributes
     *
     * @var array
     */
    public static $db = array(
        'SearchQuery'   => 'VarChar(255)',
        'Locale'        => 'DbLocale',
        'Count'         => 'Int',
    );
    
    /**
     * Returns a SilvercartSearchQuery with the given query. Creates one, if not exists.
     *
     * @param string $query Query to get SilvercartSearchQuery for
     * 
     * @return SilvercartSearchQuery 
     */
    public static function get_by_query($query) {
        $searchQuery = self::get_one(
                'SilvercartSearchQuery',
                sprintf(
                        "\"SilvercartSearchQuery\".\"SearchQuery\" = '%s' AND \"SilvercartSearchQuery\".\"Locale\" = '%s'",
                        Convert::raw2sql($query),
                        Translatable::get_current_locale()
                        
                )
        );
        if (!$searchQuery) {
            $searchQuery = new SilvercartSearchQuery();
            $searchQuery->Locale        = Translatable::get_current_locale();
            $searchQuery->SearchQuery   = $query;
            $searchQuery->Count         = 0;
        }
        return $searchQuery;
    }
    
    /**
     * Returns a SilvercartSearchQuery with the given query. Creates one, if not exists.
     *
     * @param int $limit Limit for the queries
     * 
     * @return DataObjectSet 
     */
    public static function get_most_searched($limit) {
        $searchQueries = self::get(
                'SilvercartSearchQuery',
                sprintf(
                        "\"SilvercartSearchQuery\".\"Locale\" = '%s'",
                        Translatable::get_current_locale()
                        
                ),
                "\"SilvercartSearchQuery\".\"Count\" DESC",
                "",
                "0," . $limit
        );
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

