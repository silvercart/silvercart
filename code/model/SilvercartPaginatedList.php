<?php
/**
 * Copyright 2014 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Model
 */

/**
 * Extension for PaginatedList
 *
 * @package Silvercart
 * @subpackage Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2014 pixeltricks GmbH
 * @since 26.11.2014
 * @license see license file in modules root directory
 */
class SilvercartPaginatedList extends DataExtension {
    
    /**
     * Returns the paginated list itself.
     * 
     * @return PaginatedList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.11.2014
     */
    public function PaginatedList() {
        return $this->owner;
    }
    
    /**
     * Renders the paginated list with the template SilvercartPaginatedList.ss
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.11.2014
     */
    public function RenderDefaultPagination() {
        return $this->owner->renderWith('SilvercartPaginatedList');
    }
    
}