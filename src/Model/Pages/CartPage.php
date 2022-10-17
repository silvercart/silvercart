<?php

namespace SilverCart\Model\Pages;

use Page;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\ShoppingCart;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\Member;

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
    /**
     * Shopping cart.
     * 
     * @var ShoppingCart|null
     */
    protected $cart = null;

    /**
     * Returns the shopping cart.
     * 
     * @return ShoppingCart|null
     */
    public function getCart() : ShoppingCart|null
    {
        if ($this->cart === null) {
            $customer = Customer::currentUser();
            if ($customer instanceof Member) {
                $this->cart = $customer->getCart();
            }
        }
        return $this->cart;
    }
    
    /**
     * Returns some additional content to insert before the ShoppingCartFull
     * template.
     * 
     * @return DBHTMLText
     */
    public function BeforeShoppingCartFull() : DBHTMLText
    {
        $content = '';
        $this->extend('updateBeforeShoppingCartFull', $content);
        return DBHTMLText::create()->setValue($content);
    }
    
    /**
     * Returns some additional content to insert before the ShoppingCartFull
     * template.
     * 
     * @return DBHTMLText
     */
    public function AfterShoppingCartFull() : DBHTMLText
    {
        $content = '';
        $this->extend('updateAfterShoppingCartFull', $content);
        return DBHTMLText::create()->setValue($content);
    }
}