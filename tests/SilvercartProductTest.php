<?php

/**
 * test for product behaviour
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 18.04.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProductTest extends SapphireTest {
    
    public static $fixture_file = 'silvercart/tests/SilvercartProductTest.yml';
    
    /**
     * test for the wrapper function SilvercartProduct::get()
     * -isFreeOfCharge
     * -filtering via SilvercartProduct::setRequiredAttributes
     * -filtering via where clause
     * -isActive
     * -up to three required attributes
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.4.2011
     * @return void
     */
    public function testGet() {
        //Only active products with a price or free of charge must be loaded. 
        SilvercartProduct::setRequiredAttributes("Price");
        $products = SilvercartProduct::get();
        $this->assertEquals(5, $products->Count(), "The quantity of products with a price is not correct.");
        
        //Only active products with short description and price defined as required attributes must be loaded
        SilvercartProduct::setRequiredAttributes("Price, ShortDescription");
        $products = SilvercartProduct::get();
        $this->assertEquals(4, $products->Count(), "The quantity of products with price and short description is not correct.");
        
        //Only one specific product with Title = 'product with price'
        $products = SilvercartProduct::get("`Title` = 'Product with price'");
        $this->assertTrue(1 == $products->Count(), "Quantity of products with Title 'product with price' not correct");
        
        //inactive products must not be loaded
        $products = SilvercartProduct::get("`Title` = 'inactive product'");
        $this->assertTrue(false === $products, "An inactive product can be loaded via SilvercartProduct::get()");
        
        //products free of charge must be loaded with and without price
        $products = SilvercartProduct::get("`isFreeOfCharge` = 1");
        $this->assertTrue(2 == $products->Count(), "The number of products free of charge is not correct");
        
        //load products with three required attributes defined
        SilvercartProduct::setRequiredAttributes("Price, ShortDescription, LongDescription");
        $products = SilvercartProduct::get();
        $this->assertEquals(3, $products->Count(), "The quantity of products with price, short description and long description set is not correct.");
        
    }
    
    /**
     * tests the function getPrice which should return prices dependent on pricetypes
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.4.2011
     * @return void
     */
    public function testGetPrice() {
        $productWithPrice = $this->objFromFixture("SilvercartProduct", "ProductWithPrice");
        
        //check price for admins
        $this->assertEquals(90.00, $productWithPrice->getPrice()->getAmount());
        
        //check for anonymous users, test runner makes an auto login, so we have to log out first
        $member = Member::currentUser();
        if ($member) {
            $member->logOut();
        }
        $this->assertEquals(99.99, $productWithPrice->getPrice()->getAmount());
        
        //check price for business customers
        $businessCustomer = $this->objFromFixture("SilvercartBusinessCustomer", "BusinessCustomer");
        $businessCustomer->logIn();
        $this->assertEquals(90.00, $productWithPrice->getPrice()->getAmount(), "business customers price is not correct.");
        $businessCustomer->logOut();
        
        //check price for regular customers
        $regularCustomer = $this->objFromFixture("SilvercartRegularCustomer", "RegularCustomer");
        $regularCustomer->logIn();
        $this->assertEquals(99.99, $productWithPrice->getPrice()->getAmount());
        $regularCustomer->logOut();
        
        //log in admin again
        $member->logIn();
    }
    
    /**
     * add a new product to a cart
     * increase existing shopping cart positions amount
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.4.2011
     * @return void
     */
    public function testAddToCart() {
        $cart = $this->objFromFixture("SilvercartShoppingCart", "ShoppingCart");
        $cartPosition = $this->objFromFixture("SilvercartShoppingCartPosition", "ShoppingCartPosition");
        $productWithPrice = $this->objFromFixture("SilvercartProduct", "ProductWithPrice");
        
        //existing position
        $this->assertTrue($productWithPrice->addToCart($cart->ID, 2), "The return value of addToCart() is not correct if an existing position is overwritten.");
        $position = DataObject::get_by_id("SilvercartShoppingCartPosition", $cartPosition->ID);
        $this->assertEquals(3, $position->Quantity, "The quantity of the overwritten shopping cart position is incorrect.");
        
        //new position
        $productWithPriceWithoutLongDescription = $this->objFromFixture("SilvercartProduct", "ProductWithPriceWithoutLongDescription");
        $this->assertTrue($productWithPriceWithoutLongDescription->addToCart($cart->ID), "The return value of addToCart() is not correct if a new position is created.");
        $position = DataObject::get_one("SilvercartShoppingCartPosition", "`SilvercartProductID` = $productWithPriceWithoutLongDescription->ID");
        $this->assertEquals(1, $position->Quantity, "The quantity of the newly created shopping cart position is incorrect.");
        
    }
    
    /**
     * tests the reqired attributes system for products
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.4.2011
     * @return void
     */
    public function testRequiredAttributes() {
        
        //two attributes
        SilvercartProduct::setRequiredAttributes("Price, Weight");
        $attributes = SilvercartProduct::getRequiredAttributes();
        $this->assertEquals(array("Price", "Weight"), $attributes, "Something went wrong setting two required attributes.");
        
        //four attributes
        SilvercartProduct::setRequiredAttributes("Price, Weight, ShortDescription, LongDescription");
        $attributes = SilvercartProduct::getRequiredAttributes();
        $this->assertEquals(array("Price", "Weight", "ShortDescription", "LongDescription"), $attributes, "Something went wrong setting four required attributes.");
    }
    
    /**
     * Is tax rate returned correctly?
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 24.4.2011
     * @return void
     */
    public function testGetTaxRate() {
        $productWithTax = $this->objFromFixture("SilvercartProduct", "ProductWithPrice");
        $taxRate = $productWithTax->getTaxRate();
        $this->assertEquals(19, $taxRate, "The tax rate is not correct.");
    }
    
    /**
     * Does the method return the correct boolean answer?
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 24.4.2011
     * @return void
     */
    public function testShowPricesGross() {
        $product = $this->objFromFixture("SilvercartProduct", "ProductWithPrice");
        
        //admin is logged in
        $admin = Member::currentUser();
        $this->assertEquals("Member", $admin->ClassName);
        $this->assertTrue(false === $product->showPricesGross(), "Admins get prices shown net.");
        
        //admin logged out
        $admin->logOut();
        $this->assertTrue($product->showPricesGross(), "Inspite nobody is logged in prices are shown net.");
    }
}

