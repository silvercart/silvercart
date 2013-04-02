<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Config
 */

/**
 * This is a configuration object and can be used to store individual configuration
 * options for each customer.
 *
 * @package Silvercart
 * @subpackage Config
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 23.08.2011
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartCustomerConfig extends DataObject {
 
    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'productsPerPage' => 'Int'
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'Member' => 'Member'
    );
}