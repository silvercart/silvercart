<?php

namespace SilverCart\ORM\Filters;

use SilverCart\Dev\Tools;
use SilverCart\Model\Order\Order;
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
class DateRangeSearchFilter extends SearchFilter {

    /**
     * Minimum date
     *
     * @var String
     */
    protected $min;

    /**
     * Maximum date
     *
     * @var String
     */
    protected $max;

    /**
     * Setter for min date value
     *
     * @param string $min The min value
     *
     * @return void
     */
    public function setMin($min) {
        $this->min = $min;
    }

    /**
     * Setter for max date value
     *
     * @param string $max The max value
     *
     * @return void
     */
    public function setMax($max) {
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
    public function initValue() {
        $value = $this->getValue();
        if (strpos($value, '-') === false) {
            $min_val = $value;
        } else {
            preg_match('/([^\s]*)(\s-\s(.*))?/i', $value, $matches);

            $min_val = (isset($matches[1])) ? $matches[1] : null;

            if (isset($matches[3])) {
                $max_val = $matches[3];
            }
        }

        if ($min_val && $max_val) {
            $this->setMin($min_val);
            $this->setMax($max_val);
        } else if ($min_val) {
            $this->setMin($min_val);
        } else if ($max_val) {
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
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.06.2014
     */
    public function apply(DataQuery $query) {
        $this->initValue();
        $min = Convert::raw2sql($this->min).' 00:00:00';
        $max = Convert::raw2sql($this->max).' 23:59:59';
        $orderTable = Tools::get_table_name(Order::class);

        if ($this->min && $this->max) {
            $query->where(sprintf(
                "%s >= STR_TO_DATE('%s', '%%d.%%m.%%Y') AND %s <= STR_TO_DATE('%s', '%%d.%%m.%%Y %%H:%%i:%%s')",
                $orderTable . '.Created',
                $min,
                $orderTable . '.Created',
                $max
            ));
        } else if ($this->min) {
            $query->where(sprintf(
                "DATEDIFF(%s, STR_TO_DATE('%s', '%%d.%%m.%%Y')) = 0",
                $orderTable . '.Created',
                $min
            ));
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

