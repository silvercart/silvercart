<?php
/**
 * Copyright 2010, 2011, 2012 pixeltricks GmbH
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
 * @package Silvercart
 * @subpackage Base
 */

/**
 * provides method to have a configurable pagination summary via backend
 *
 * @package Silvercart
 * @subpackage Base
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 16.08.2012
 * @copyright 2012 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License 
 */
class SilvercartDataObjectSet extends DataObjectDecorator {
    
    /**
     * method to return pagination summary via configurated backend field
     * 
     * @param int $displayedPages number of pages to display simultaneously
     * 
     * @return DataObjectSet
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 16.08.2012
     */
    public function SilvercartPaginationSummary($displayedPages = null) {
        if (is_null($displayedPages)) {
            $displayedPages = SilvercartConfig::DisplayedPaginationPages();
        }
        return $this->owner->PaginationSummary($displayedPages);
    }

    /**
     * Resets the item indexes
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.12.2012
     */
    public function resetItemIndexes() {
        $this->owner->items = array_values($this->owner->items);
    }
    
}