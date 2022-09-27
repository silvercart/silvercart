<?php

namespace SilverCart\Model\Pages;

use Page;

/**
 * represents a shopping cart. Every customer has one initially.
 *
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CartPage extends Page
{
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartCartPage';
    /**
     * Class attached to page icons in the CMS page tree. Also supports font-icon set.
     * 
     * @var string
     */
    private static $icon_class = 'font-icon-p-cart';
}