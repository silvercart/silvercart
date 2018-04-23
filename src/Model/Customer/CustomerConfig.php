<?php

namespace SilverCart\Model\Customer;

use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;

/**
 * This is a configuration object and can be used to store individual configuration
 * options for each customer.
 *
 * @package SilverCart
 * @subpackage Model_Customer
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CustomerConfig extends DataObject {
 
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = array(
        'productsPerPage' => 'Int'
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_one = array(
        'Member' => Member::class,
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartCustomerConfig';
}