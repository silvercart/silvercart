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
 * tests for methods of the class SilvercartProduct
 *
 * @package Silvercart
 * @subpackage Tests
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 18.04.2011
 * @license see license file in modules root directory
 */
class SilvercartProductTest extends SapphireTest {
    
    /**
     * Fixture file
     *
     * @var string
     */
    public static $fixture_file = 'silvercart/tests/SilvercartProductTest.yml';
    
    /**
     * test for the wrapper function SilvercartProduct::get()
     * -filtering via SilvercartProduct::setRequiredAttributes
     * -filtering via where clause
     * -isActive
     * -up to three required attributes
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <diel@pixeltricks.de>, Carolin Wörner <cwoerner@pixeltricks.de>
     * @since 25.01.2013
     */
    public function testGet() {
        //Only active products with a price or free of charge must be loaded. 
        SilvercartProduct::setRequiredAttributes("Price");
        $productsWithPrice = SilvercartProduct::get();
        $this->assertEquals(5, (int) $productsWithPrice->count(), "The quantity of products with a price is not correct.");
        
        //Only active products with short description and price defined as required attributes must be loaded
        SilvercartProduct::setRequiredAttributes("Price, ShortDescription");
        $productsWithPriceAndShortDescription = SilvercartProduct::get();
        $this->assertEquals(4, (int) $productsWithPriceAndShortDescription->count(), "The quantity of products with price and short description is not correct.");
        
        //Only one specific product with Title = 'Product with price'
        $productsWithPriceTitle = SilvercartProduct::get()->filter(array("Title" => 'Product with price'));
        $this->assertTrue($productsWithPriceTitle->count() == 1, "Quantity of products with Title 'product with price' not correct");
        
        //inactive products must not be loaded
        $productsWithInactiveTitle = SilvercartProduct::get()->filter(array("Title" => 'inactive product'));
        $this->assertTrue($productsWithInactiveTitle->count() == 0, "An inactive product can be loaded via SilvercartProduct::get()");
        
        //load products with three required attributes defined
        SilvercartProduct::setRequiredAttributes("Price, ShortDescription, LongDescription");
        $productsWithPriceAndShortDescriptionAndLongDescription = SilvercartProduct::get();
        $this->assertEquals(3, $productsWithPriceAndShortDescriptionAndLongDescription->count(), "The quantity of products with price, short description and long description set is not correct.");
        
    }
    
    /**
     * tests the function getPrice which should return prices dependent on pricetypes
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <diel@pixeltricks.de>, Carolin Wörner <cwoerner@pixeltricks.de>
     * @since 28.01.2013
     */
    public function testGetPrice() {
        $productWithPrice = $this->objFromFixture("SilvercartProduct", "ProductWithPrice");
        
        //check price for admins
        $this->assertEquals(99.99, $productWithPrice->getPrice()->getAmount(), 'Error: A admin user without address gets net prices shown.');
        
        //check for anonymous users, test runner makes an auto login, so we have to log out first
        $member = Member::currentUser();
        if ($member) {
            $member->logOut();
        }
        
        $this->assertEquals(99.99, $productWithPrice->getPrice()->getAmount());
        
        //check price for business customers
        $businessCustomer = $this->objFromFixture("Member", "BusinessCustomer");
        $businessCustomer->logIn();
        $productWithPriceWithoutShortDescription = $this->objFromFixture("SilvercartProduct", "ProductWithPriceWithoutShortDescription");
        $this->assertEquals(9.00, $productWithPriceWithoutShortDescription->getPrice()->getAmount(), "business customers price is not correct.");
        $businessCustomer->logOut();
        
        //check price for regular customers
        $regularCustomer = $this->objFromFixture("Member", "RegularCustomer");
        $regularCustomer->logIn();
        $this->assertEquals(99.99, $productWithPrice->getPrice()->getAmount());
        $regularCustomer->logOut();
    }
    
    /**
     * add a new product to a cart
     * increase existing shopping cart positions amount
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <diel@pixeltricks.de>, Carolin Wörner <cwoerner@pixeltricks.de>
     * @since 25.01.2013
     */
    public function testAddToCart() {
        $cart = $this->objFromFixture("SilvercartShoppingCart", "ShoppingCart");
        $cartPosition = $this->objFromFixture("SilvercartShoppingCartPosition", "ShoppingCartPosition");
        $productWithPrice = $this->objFromFixture("SilvercartProduct", "ProductWithPrice");
        
        //existing position
        $productWithPrice->addToCart($cart->ID, 2);
        $position = DataObject::get_by_id("SilvercartShoppingCartPosition", $cartPosition->ID);
        $this->assertEquals(3, (int) $position->Quantity, "The quantity of the overwritten shopping cart position is incorrect.");
        
        //new position
        $productWithPriceWithoutLongDescription = $this->objFromFixture("SilvercartProduct", "ProductWithPriceWithoutLongDescription");
        $productWithPriceWithoutLongDescription->addToCart($cart->ID);
        $refreshedPosition = DataObject::get_one("SilvercartShoppingCartPosition", "SilvercartProductID = $productWithPriceWithoutLongDescription->ID");
        $this->assertEquals(1, $refreshedPosition->Quantity, "The quantity of the newly created shopping cart position is incorrect.");
        
    }
    
    /**
     * tests the reqired attributes system for products
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <diel@pixeltricks.de>, Carolin Wörner <cwoerner@pixeltricks.de>
     * @since 25.01.2013
     */
    public function testRequiredAttributes() {
        
        //two attributes
        SilvercartProduct::resetRequiredAttributes();
        SilvercartProduct::setRequiredAttributes("Price, Weight");
        $twoAttributes = SilvercartProduct::getRequiredAttributes();
        $this->assertEquals(array("Price", "Weight"), $twoAttributes, "Something went wrong setting two required attributes.");
        
        //four attributes
        SilvercartProduct::resetRequiredAttributes();
        SilvercartProduct::setRequiredAttributes("Price, Weight, ShortDescription, LongDescription");
        $fourAttributes = SilvercartProduct::getRequiredAttributes();
        $this->assertEquals(array("Price", "Weight", "ShortDescription", "LongDescription"), $fourAttributes, "Something went wrong setting four required attributes.");
    }
    
    /**
     * Is tax rate returned correctly?
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 24.4.2011
     */
    public function testGetTaxRate() {
        $productWithTax = $this->objFromFixture("SilvercartProduct", "ProductWithPrice");
        $taxRate = $productWithTax->getTaxRate();
        $this->assertEquals(19, $taxRate, "The tax rate is not correct.");
    }
    
    /**
     * Does the method return the correct boolean answer?
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 24.4.2011
     */
    public function testShowPricesGross() {
        $product = $this->objFromFixture("SilvercartProduct", "ProductWithPrice");
        
        //admin is logged in
        $admin = Member::currentUser();
        
        //admin logged out
        $admin->logOut();
        $this->assertTrue($product->showPricesGross(), "Inspite nobody is logged in prices are shown net.");
    }
}

