<?php

namespace SilverCart\ORM\Filters;

use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\DataQuery;
use SilverStripe\ORM\Filters\SearchFilter;

/**
 * Search between two dates.
 *
 * @package SilverCart
 * @subpackage ORM_Filters
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 11.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DateRangeSearchFilter extends SearchFilter
{
    /**
     * Minimum date
     *
     * @var string
     */
    protected $min = null;
    /**
     * Maximum date
     *
     * @var string
     */
    protected $max = null;

    /**
     * Setter for min date value
     *
     * @param string $min The min value
     *
     * @return void
     */
    public function setMin(string $min) : void
    {
        $this->min = $min;
    }

    /**
     * Setter for max date value
     *
     * @param string $max The max value
     *
     * @return void
     */
    public function setMax(string $max) : void
    {
        $this->max = $max;
    }
    
    /**
     * Initializes the min and max value.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.06.2014
     */
    public function initValue() : void
    {
        $value   = $this->getValue();
        $max_val = null;
        if (strpos($value, '-') === false) {
            $min_val = $value;
        } else {
            preg_match('/([^\s]*)(\s-\s(.*))?/i', $value, $matches);
            $min_val = (isset($matches[1])) ? $matches[1] : null;
            if (isset($matches[3])) {
                $max_val = $matches[3];
            }
        }
        if ($min_val
         && $max_val
        ) {
            $this->setMin($min_val);
            $this->setMax($max_val);
        } elseif ($min_val) {
            $this->setMin($min_val);
        } elseif ($max_val) {
            $this->setMax($max_val);
        }
    }

    /**
     * Apply filter query SQL to a search query
     * Date range filtering between min and max values
     *
     * @param DataQuery $query The query object
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.09.2020
     */
    public function apply(DataQuery $query) : void
    {
        $this->initValue();
        $min        = Convert::raw2sql($this->min) . ' 00:00:00';
        $max        = Convert::raw2sql($this->max) . ' 23:59:59';
        $tableName  = Config::inst()->get($query->dataClass(), 'table_name');
        if ($this->min
         && $this->max
        ) {
            $query->where("{$tableName}.Created >= STR_TO_DATE('{$min}', '%d.%m.%Y') AND {$tableName}.Created <= STR_TO_DATE('{$max}', '%d.%m.%Y %H:%i:%s')");
        } elseif ($this->min) {
            $query->where("DATEDIFF({$tableName}.Created, STR_TO_DATE('{$min}', '%d.%m.%Y')) = 0");
        }
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
    public function applyOne(DataQuery $query) : void
    {
        
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
    public function excludeOne(DataQuery $query) : void
    {
        
    }
}