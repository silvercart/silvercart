<?php

namespace SilverCart\ORM\Filters;

use SilverStripe\ORM\DataQuery;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\Filters\SearchFilter;

/**
 * Search if a date is empty or not.
 * Field name should start wit "Is".
 *
 * @package SilverCart
 * @subpackage ORM\Filters
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 13.10.2023
 * @copyright 2023 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DateIsEmptyFilter extends SearchFilter
{
    /**
     * mandatory method, because it is an abstract method on the parent class
     * 
     * @param DataQuery $query The query object
     *
     * @return void
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
     * @return void
     */
    public function excludeOne(DataQuery $query) : DataQuery
    {
        return $this->oneFilter($query, false);
    }

    /**
     * Applies a single match, either as inclusive or exclusive
     *
     * @param DataQuery $query     Query
     * @param bool      $inclusive True if this is inclusive, or false if exclusive
     * 
     * @return DataQuery
     */
    protected function oneFilter(DataQuery $query, $inclusive) : DataQuery
    {
        $this->model = $query->applyRelation($this->relation);
        $field       = $this->getDbName();
        $value       = $this->getValue();
        $dbFieldName = $field;
        if (strpos($field, '"Is') === 0) {
            $dbFieldName = '"' . substr($field, 3);
        }
        if ($value === '0') {
            $where = DB::get_conn()->nullCheckClause($dbFieldName, true);
        } else {
            $where = DB::get_conn()->nullCheckClause($dbFieldName, false);
        }
        return $query->where($where);
    }
}