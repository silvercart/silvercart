<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;

/**
 * represents a shopping cart. Every customer has one initially.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CartPage extends \Page {

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartCartPage';
    
    /**
     * icon for site tree
     *
     * @var array
     */
    private static $icon = "silvercart/silvercart:client/img/page_icons/cart-file.gif";
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return Tools::plural_name_for($this); 
    }

}