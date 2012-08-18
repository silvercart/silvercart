<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * @subpackage Products
 */

/**
 * This object saves the sort order for products that are related to a
 * mirror productgroup page.
 *
 * @package Silvercart
 * @subpackage Products
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 24.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProductGroupMirrorSortOrder extends DataObject {
    
    /**
     * Attributes.
     * 
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     */
    public static $db = array(
        'SortOrder'                     => 'Int',
        'SilvercartProductID'           => 'Int',
        'SilvercartProductGroupPageID'  => 'Int'
    );
    
    /**
     * Indexes
     *
     * @var array 
     */
    public static $indexes = array(
        'SortOrder'                     => '(SortOrder)',
        'SilvercartProductID'           => '(SilvercartProductID)',
        'SilvercartProductGroupPageID'  => '(SilvercartProductGroupPageID)',
    );
}