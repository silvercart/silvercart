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
        return $this->CtrlPaginationSummary($displayedPages);
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

    /**
     * Returns the URL of the previous page.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2015
     */
    public function CtrlPrevLink() {
        if ($this->owner->pageStart - $this->owner->pageLength >= 0) {
            return HTTP::setGetVar($this->owner->paginationGetVar, $this->owner->pageStart - $this->owner->pageLength, Director::makeRelative(Controller::curr()->Link()));
        }
    }

    /**
     * Returns the URL of the next page.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2015
     */
    public function CtrlNextLink() {
        if ($this->owner->pageStart + $this->owner->pageLength < $this->owner->totalSize) {
            return HTTP::setGetVar($this->owner->paginationGetVar, $this->owner->pageStart + $this->owner->pageLength, Director::makeRelative(Controller::curr()->Link()));
        }
    }

    /**
     * Return a datafeed of page-links, good for use in search results, etc.
     * $maxPages will put an upper limit on the number of pages to return.  It will
     * show the pages surrounding the current page, so you can still get to the deeper pages.
     * 
     * @param int $maxPages The maximum number of pages to return
     * 
     * @return DataObjectSet
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2015
     */
    public function CtrlPages($maxPages = 0) {
        $ret = new DataObjectSet();

        if ($maxPages) {
            $startPage = ($this->owner->CurrentPage() - floor($maxPages / 2)) - 1;
            $endPage = $this->owner->CurrentPage() + floor($maxPages / 2);

            if ($startPage < 0) {
                $startPage = 0;
                $endPage = $maxPages;
            }
            if ($endPage > $this->owner->TotalPages()) {
                $endPage = $this->owner->TotalPages();
                $startPage = max(0, $endPage - $maxPages);
            }

        } else {
            $startPage = 0;
            $endPage = $this->owner->TotalPages();
        }

        for ($i=$startPage; $i < $endPage; $i++) {
            $link = HTTP::setGetVar($this->owner->paginationGetVar, $i*$this->owner->pageLength, Director::makeRelative(Controller::curr()->Link()));
            $thePage = new ArrayData(array(
                    "PageNum" => $i+1,
                    "Link" => $link,
                    "CurrentBool" => ($this->owner->CurrentPage() == $i+1)?true:false,
                    )
            );
            $ret->push($thePage);
        }

        return $ret;
    }

    /**
	 * Display a summarized pagination which limits the number of pages shown
	 * "around" the currently active page for visual balance.
	 * In case more paginated pages have to be displayed, only 
	 * 
	 * Example: 25 pages total, currently on page 6, context of 4 pages
	 * [prev] [1] ... [4] [5] [[6]] [7] [8] ... [25] [next]
	 * 
	 * Example template usage:
	 * <code>
	 * <% if MyPages.MoreThanOnePage %>
	 * 	<% if MyPages.NotFirstPage %>
	 * 		<a class="prev" href="$MyPages.PrevLink">Prev</a>
	 * 	<% end_if %>
	 *  <% control MyPages.PaginationSummary(4) %>
	 * 		<% if CurrentBool %>
	 * 			$PageNum
	 * 		<% else %>
	 * 			<% if Link %>
	 * 				<a href="$Link">$PageNum</a>
	 * 			<% else %>
	 * 				...
	 * 			<% end_if %>
	 * 		<% end_if %>
	 * 	<% end_control %>
	 * 	<% if MyPages.NotLastPage %>
	 * 		<a class="next" href="$MyPages.NextLink">Next</a>
	 * 	<% end_if %>
	 * <% end_if %>
	 * </code>
	 * 
	 * @param integer $context Number of pages to display "around" the current page. Number should be even,	because its halved to either side of the current page.
     * 
	 * @return 	DataObjectSet
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2015
	 */
    public function CtrlPaginationSummary($context = 4) {
        $ret = new DataObjectSet();

        // convert number of pages to even number for offset calculation
        if ($context % 2) {
            $context--;
        }

        // find out the offset
        $current = $this->owner->CurrentPage();
        $totalPages = $this->owner->TotalPages();

        // if the first or last page is shown, use all content on one side (either left or right of current page)
        // otherwise half the number for usage "around" the current page
        $offset = ($current == 1 || $current == $totalPages) ? $context : floor($context/2);

        $leftOffset = $current - ($offset);
        if ($leftOffset < 1) {
            $leftOffset = 1;
        }
        if ($leftOffset + $context > $totalPages) {
            $leftOffset = $totalPages - $context;
        }

        for ($i=0; $i < $totalPages; $i++) {
            $link = HTTP::setGetVar($this->owner->paginationGetVar, $i*$this->owner->pageLength, Director::makeRelative(Controller::curr()->Link()));
            $num = $i+1;
            $currentBool = ($current == $i+1) ? true:false;
            if (
                ($num == $leftOffset-1 && $num != 1 && $num != $totalPages)
                || ($num == $leftOffset+$context+1 && $num != 1 && $num != $totalPages)
            ) {
                $ret->push(new ArrayData(array(
                        "PageNum" => null,
                        "Link" => null,
                        "CurrentBool" => $currentBool,
                    )
                ));
            } elseif ($num == 1 || $num == $totalPages || in_array($num, range($current-$offset,$current+$offset))) { 
                $ret->push(new ArrayData(array(
                        "PageNum" => $num,
                        "Link" => $link,
                        "CurrentBool" => $currentBool,
                    )
                ));
            }
        }
        return $ret;
    }

}