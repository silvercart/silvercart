<?php

namespace SilverCart\ORM\Filters;

use SilverStripe\ORM\DataQuery;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\Filters\PartialMatchFilter;

/**
 * Acts exactly like the default PartialMatchFilter but adds null value support to
 * exclude one.
 * 
 * @package SilverCart
 * @subpackage ORM\Filters
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 13.12.2019
 * @copyright 2019 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class PartialMatchOrNullFilter extends PartialMatchFilter
{
    /**
     * Exclude one operation.
     * 
     * @param DataQuery $query Query
     * 
     * @return type
     */
    protected function excludeOne(DataQuery $query) : DataQuery
    {
        $this->model      = $query->applyRelation($this->relation);
        $field            = $this->getDbName();
        $comparisonClause = DB::get_conn()->comparisonClause(
            $field,
            null,
            false, // exact?
            true, // negate?
            $this->getCaseSensitive(),
            true
        );
        
        $nullClause        = DB::get_conn()->nullCheckClause($field, true);
        $comparisonClause .= " OR {$nullClause}";
        
        return $query->where([
            $comparisonClause => $this->getMatchPattern($this->getValue())
        ]);
    }
}