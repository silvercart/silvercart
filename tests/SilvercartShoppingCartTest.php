<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Tests
 */

/**
 * tests for SilvercartShoppingCart
 *
 * @package Silvercart
 * @subpackage Tests
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 25.04.2011
 * @license see license file in modules root directory
 */
class SilvercartShoppingCartTest extends SapphireTest {
    
    /**
     * Fixture file
     *
     * @var string
     */
    public static $fixture_file = 'silvercart/tests/SilvercartShoppingCartTest.yml';
    
    /**
     * Do all positions of the cart get deleted?
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 25.4.2011
     * @return void
     */
    public function testDelete() {
        //do all 4 positions get loaded?
        $cart = $this->objFromFixture("SilvercartShoppingCart", "ShoppingCart");
        $this->assertEquals(4, $cart->SilvercartShoppingCartPositions()->count());
        
        //do all 4 positions get deleted?
        $cart->delete();
        $this->assertEquals(0, $cart->SilvercartShoppingCartPositions()->count());
    }
    
    /**
     * tests addProduct function with admins and anonymous users
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.4.2011
     * @return void
     */
    public function testAddProduct() {
        //add product to admins cart
        $product = $this->objFromFixture("SilvercartProduct", "product");
        $formData = array();
        $formData['productID'] = $product->ID;
        $formData['productQuantity'] = 10;
        $this->assertTrue(SilvercartShoppingCart::addProduct($formData));
        
        //add product to anonymous cart
        $member = Member::currentUser();
        if ($member) {
            $member->logOut();
        }
        $this->assertTrue(SilvercartShoppingCart::addProduct($formData), "adding a product to an anonymous users cart failed!");
        
        //log admin in again or session gets messed up and the test will not work on reload
        $member->logIn();
    }
    
    /**
     * test for getQuantity function
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.4.2011
     * @return void
     */
    public function testGetQuantity() {
        $cart = $this->objFromFixture("SilvercartShoppingCart", "ShoppingCart");
        $this->assertEquals(12, $cart->getQuantity());
    }
    
    /**
     * test for isFilled function
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.4.2011
     * @return void
     */
    public function testIsFilled() {
        $cart = $this->objFromFixture("SilvercartShoppingCart", "ShoppingCart");
        $this->assertTrue($cart->isFilled());
    }
    
    /**
     * test for getAmountTotal()
     * This cart has no shipping costs or payment costs
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <diel@pixeltricks.de>, Carolin Wörner <cwoerner@pixeltricks.de>
     * @since 29.01.2013
     */
    public function testGetAmountTotal() {
       $cart = $this->objFromFixture("SilvercartShoppingCart", "ShoppingCart");
       $tmp = $cart->getAmountTotal()->getAmount();
      
       $this->assertEquals(2129.93, $cart->getAmountTotal()->getAmount(), "The total amount of a cart without payment and shipping costs is NOT correct.");
    }
    
    

}

