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
     */
    public function initValue() : void
    {
        $min_val = $this->getValue();
        $max_val = null;
        if (array_key_exists('filter', $_POST)) {
            foreach ($_POST['filter'] as $grid => $fields) {
                if (array_key_exists("{$this->getName()}__End", $fields)) {
                    $max_val = $fields["{$this->getName()}__End"];
                }
            }
        }
        if ($max_val === null) {
            $state   = null;
            foreach ($_GET as $name => $value) {
                if (strpos($name, 'gridState-') === 0) {
                    $state = json_decode($value);
                    break;
                }
            }
            if (is_object($state)
            && property_exists($state, 'GridFieldFilterHeader')
            ) {
                $filter = $state->GridFieldFilterHeader;
                if (is_object($filter)
                && property_exists($filter, 'Columns')
                ) {
                    $columns = (array) $filter->Columns;
                    if (array_key_exists("{$this->getName()}__End", $columns)) {
                        $max_val = $columns["{$this->getName()}__End"];
                    }
                }
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
            $query->where("{$tableName}.Created BETWEEN '{$min}' AND '{$max}'");
        } elseif ($this->min) {
            $max = Convert::raw2sql($this->min) . ' 23:59:59';
            $query->where("{$tableName}.Created BETWEEN '{$min}' AND '{$max}'");
        }
    }
    
    /**
     * mandatory method, because it is an abstract method on the parent class
     * 
     * @param DataQuery $query The query object
     *
     * @return void
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
     */
    public function excludeOne(DataQuery $query) : void
    {
        
    }
}