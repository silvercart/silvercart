<?php
/**
 * Copyright 2012 pixeltricks GmbH
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
 * @package SilverCart
 * @subpackage Search
 */

/**
 * Search between two dates.
 *
 * @package SilverCart
 * @subpackage Search
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 11.03.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
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
     * @param SQLQuery $query The query object
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
}

