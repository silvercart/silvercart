<?php

namespace SilverCart\ORM\Filters;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\DataQuery;
use SilverStripe\ORM\Filters\SearchFilter;

/**
 * Filters by a given set of values. Every value has an includable or excludable
 * indicator.
 *
 * @package SilverCart
 * @subpackage ORM_Filters
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 11.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ExactMatchBooleanMultiFilter extends SearchFilter {
    
    public function getValue() {
        $value   = [];
        $request = Controller::curr()->getRequest();
        $q       = $request->getVar('q');
        $boolKey = $this->getName() . '-BoolValues';
        if (array_key_exists($boolKey, $q)) {
            $value = $q[$boolKey];
        }
        return $value;
    }

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
     * @since 25.06.2014
     */
    public function apply(DataQuery $query) {
        $result = false;
        $value  = $this->getValue();
        if (is_array($value) &&
            count($value) > 0) {
            $this->model = $query->applyRelation($this->relation);
            $values = array(
                0 => array(),
                1 => array(),
            );
            foreach ($value as $ID => $boolean) {
                $operator = '!=';
                if ($boolean == '1') {
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
        return $this->getValue() === [] || $this->getValue() === null || $this->getValue() === '';
    }
    
    /**
     * mandatory method, because it is an abstract method on the parent class
     * 
     * @param DataQuery $query The query object
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
     * @param DataQuery $query The query object
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2013
     */
    public function excludeOne(DataQuery $query) {
        
    }

}