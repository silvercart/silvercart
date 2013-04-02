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
 * Search between two dates.
 *
 * @package Silvercart
 * @subpackage Search
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 11.03.2012
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
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.03.2012
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
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.03.2012
     */
    public function setMax($max) {
        $this->max = $max;
    }

    /**
     * Apply filter query SQL to a search query
     * Date range filtering between min and max values
     *
     * @param DataQuery $query The query object
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 11.03.2012
     */
    public function apply(DataQuery $query) {
        $min = Convert::raw2sql($this->min).' 00:00:00';
        $max = Convert::raw2sql($this->max).' 23:59:59';

        if ($this->min && $this->max) {
            $query->where(sprintf(
                "%s >= STR_TO_DATE('%s', '%%d.%%m.%%Y') AND %s <= STR_TO_DATE('%s', '%%d.%%m.%%Y %%H:%%i:%%s')",
                'SilvercartOrder.Created',
                $min,
                'SilvercartOrder.Created',
                $max
            ));
        } else if ($this->min) {
            $query->where(sprintf(
                "DATEDIFF(%s, STR_TO_DATE('%s', '%%d.%%m.%%Y')) = 0",
                'SilvercartOrder.Created',
                $min
            ));
        }
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

