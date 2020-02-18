<?php

namespace SilverCart\ORM\Filters;

use SilverStripe\ORM\DataQuery;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\Filters\SearchFilter;

/**
 * Filters by using the LOWER() string function.
 *
 * @package SilverCart
 * @subpackage ORM\Filters
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 06.02.2020
 * @copyright 2020 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class LowerCaseFilter extends SearchFilter
{
    /**
     * mandatory method, because it is an abstract method on the parent class
     * 
     * @param DataQuery $query The query object
     *
     * @return DataQuery
     */
    public function applyOne(DataQuery $query) : DataQuery
    {
        return $this->oneFilter($query, true);
    }
    
    /**
     * mandatory method, because it is an abstract method on the parent class
     * 
     * @param DataQuery $query The query object
     *
     * @return DataQuery
     */
    public function excludeOne(DataQuery $query) : DataQuery
    {
        return $this->oneFilter($query, false);
    }

    /**
     * Applies a single match, either as inclusive or exclusive
     *
     * @param DataQuery $query     The query object
     * @param bool      $inclusive True if this is inclusive, or false if exclusive
     * 
     * @return DataQuery
     */
    protected function oneFilter(DataQuery $query, $inclusive) : DataQuery
    {
        $this->model = $query->applyRelation($this->relation);
        $field = $this->getDbName();
        $value = $this->getValue();

        // Null comparison check
        if ($value === null) {
            $where = DB::get_conn()->nullCheckClause($field, $inclusive);
            return $query->where($where);
        }

        // Value comparison check
        $where = DB::get_conn()->comparisonClause(
            "LOWER({$field})",
            null,
            true, // exact?
            !$inclusive, // negate?
            $this->getCaseSensitive(),
            true
        );
        // for != clauses include IS NULL values, since they would otherwise be excluded
        if (!$inclusive) {
            $nullClause = DB::get_conn()->nullCheckClause($field, true);
            $where .= " OR {$nullClause}";
        }

        $clause = [$where => $value];
        
        return $this->aggregate ?
            $this->applyAggregate($query, $clause) :
            $query->where($clause);
    }

    /**
     * Checks whether the filter value is empty
     * 
     * @return bool
     */
    public function isEmpty() : bool
    {
        return $this->getValue() === [] || $this->getValue() === null || $this->getValue() === '';
    }
}