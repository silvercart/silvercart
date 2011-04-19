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
        
    }
}

