<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @copyright 2013 pixeltricks GmbH
 * @since 24.03.2011
 * @license see license file in modules root directory
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
        'SortOrder'                     => '("SortOrder")',
        'SilvercartProductID'           => '("SilvercartProductID")',
        'SilvercartProductGroupPageID'  => '("SilvercartProductGroupPageID")',
    );
}