<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Search
 */

/**
 * Filters by a given set of values. Every value has an includable or excludable
 * indicator.
 *
 * @package Silvercart
 * @subpackage Search
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 11.10.2012
 * @license see license file in modules root directory
 */
class SilvercartExactMatchBooleanMultiFilter extends SearchFilter {

    /**
     * Applies the filter.
     * Builds the where clause with the given IDs and boolean values in
     * $this->value
     * 
     * @param DataQuery $query Query to build where clause for
     * 
     * @return DataQuery
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.10.2012
     */
    public function apply(DataQuery $query) {
        $result = false;
        $value  = $this->getValue();
        if (is_array($value) &&
            count($value) > 0) {
            $query  = $this->applyRelation($query);
            $values = array(
                0 => array(),
                1 => array(),
            );
            foreach ($value as $ID => $boolean) {
                $operator = '!=';
                if ($boolean) {
                    $operator = '=';
                }
                $values[$boolean][] = sprintf(
                        "%s %s '%s'",
                        $this->getDbName(),
                        $operator,
                        Convert::raw2sql($ID)
                );
            }
            
            $negativeWhereClause = implode(' AND ', $values[0]);
            $positiveWhereClause = implode(' OR ', $values[1]);
            
            if (count($values[0]) > 0 &&
                count($values[1]) > 0) {
                $where = sprintf(
                        '(%s) AND (%s)',
                        $negativeWhereClause,
                        $positiveWhereClause
                );
            } elseif (count($values[0]) > 0) {
                $where = $negativeWhereClause;
            } else {
                $where = $positiveWhereClause;
            }
            
            $result = $query->where($where);
        }
        return $result;
    }

    /**
     * Checks whether the filter value is empty
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.10.2012
     */
    public function isEmpty() {
        return $this->getValue() == null || $this->getValue() == '';
    }
    
    /**
     * mandatory method, because it is an abstract method on the parent class
     * 
     * @param DataQuery $query ???
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2013
     */
    public function applyOne(DataQuery $query) {
        
    }
    
    /**
     * mandatory method, because it is an abstract method on the parent class
     * 
     * @param DataQuery $query ???
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2013
     */
    public function excludeOne(DataQuery $query) {
        
    }

}