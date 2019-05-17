<?php

namespace SilverCart\Model\Order;

use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Order\Order;

/**
 * abstract for an order address.
 *
 * @package SilverCart
 * @subpackage Model_Order
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class OrderAddress extends Address
{
    /**
     * 1:1 back relations
     *
     * @var array
     */
    private static $belongs_to = [
        'Order' => Order::class,
    ];

    /**
     * Returns the translated singular name of the object.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this);  
    }
}