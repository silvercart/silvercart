<?php

namespace SilverCart\Model\Order;

use SilverStripe\Security\Member;
use SilverStripe\ORM\ArrayList;

/**
 * Used to create a sub shopping cart dependent on the "normal" shopping cart.
 * A sub shopping cart can be used to apply shopping cart plugins on a subset of
 * shopping cart positions (e.g. when splitting a single order into multiple orders
 * dependen on a product's / shopping cart position's distributor).
 * 
 * @package SilverCart
 * @subpackage Model\Order
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.10.2020
 * @copyright 2020 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SubShoppingCart extends ShoppingCart
{
    /**
     * Initializes a new sub shopping cart by the given $shoppingCart context.
     * 
     * @param \SilverCart\Model\Order\ShoppingCart $shoppingCart Context shopping cart
     * 
     * @return \SilverCart\Model\Order\SubShoppingCart
     */
    public static function initBy(ShoppingCart $shoppingCart) : SubShoppingCart
    {
        $subCart = self::create();
        $subCart->setShoppingCart($shoppingCart);
        return $subCart;
    }

    /**
     * The context shopping cart.
     *
     * @var ShoppingCart
     */
    protected $shoppingCart = null;
    /**
     * List of shopping cart positions to place an order for.
     *
     * @var ArrayList
     */
    protected $shoppingCartPositions = null;

    /**
     * Adds the given $position.
     * 
     * @param ShoppingCartPosition $position Position to add
     * 
     * @return void
     */
    public function addShoppingCartPosition(ShoppingCartPosition $position) : void
    {
        if ($this->shoppingCartPositions === null) {
            $this->shoppingCartPositions = ArrayList::create();
        }
        $this->shoppingCartPositions->add($position);
    }
    
    /**
     * Sets the list of shopping cart positions.
     * 
     * @param ArrayList $positions Positions to set
     * 
     * @return \SilverCart\Model\Order\SubShoppingCart
     */
    public function setShoppingCartPositions(ArrayList $positions) : SubShoppingCart
    {
        $this->shoppingCartPositions = $positions;
        return $this;
    }
    
    /**
     * Returns the shopping cart positions.
     * 
     * @return ArrayList
     */
    public function ShoppingCartPositions() : ArrayList
    {
        if ($this->shoppingCartPositions === null) {
            $this->shoppingCartPositions = ArrayList::create();
        }
        return $this->shoppingCartPositions;
    }
    
    /**
     * Returns the related member.
     * 
     * @return Member
     */
    public function Member() : Member
    {
        return $this->getShoppingCart()->Member();
    }
    
    /**
     * Returns the related shopping cart context.
     * 
     * @return \SilverCart\Model\Order\ShoppingCart
     */
    public function getShoppingCart() : ShoppingCart
    {
        return $this->shoppingCart;
    }

    /**
     * Sets the related shopping cart context.
     * 
     * @param \SilverCart\Model\Order\ShoppingCart $shoppingCart Shopping cart
     * 
     * @return \SilverCart\Model\Order\SubShoppingCart
     */
    public function setShoppingCart(ShoppingCart $shoppingCart) : SubShoppingCart
    {
        $this->shoppingCart = $shoppingCart;
        $this->ID           = $shoppingCart->ID;
        $this->MemberID     = $shoppingCart->MemberID;
        return $this;
    }
    
    /**
     * A sub shopping cart can't be written.
     * 
     * @param bool $showDebug       Show debug output?
     * @param bool $forceInsert     Force insert?
     * @param bool $forceWrite      Force write?
     * @param bool $writeComponents Write related components?
     * 
     * @return int
     */
    public function write($showDebug = false, $forceInsert = false, $forceWrite = false, $writeComponents = false) : int
    {
        return 0;
    }
}