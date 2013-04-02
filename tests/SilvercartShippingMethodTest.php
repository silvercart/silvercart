<?php
/**
 * Copyright 2012 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Tests
 */
/**
 * tests for SilvercartShippingMethod
 *
 * @package Silvercart
 * @subpackage Tests
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 08.03.2012
 * @license see license file in modules root directory
 */
class SilvercartShippingMethodTest extends SapphireTest {
    
    /**
     * Fixture file
     *
     * @var string
     */
    public static $fixture_file = 'silvercart/tests/SilvercartShippingMethodTest.yml';
    
    /**
     * the method is not testable because of a call Controller::curr()
     * try to find the right fee by simulating different cart weights;
     *
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 08.03.2012
     */
    public function testGetShippingFee() {
        i18n::set_locale('en_US');
        $customer               = Member::currentUser();
        $shippingAddress        = $this->objFromFixture('SilvercartAddress', 'shippingAddress');
        $cart                   = $customer->getCart();
        $productWithZeroWeight  = $this->objFromFixture('SilvercartProduct', 'product');
        $productWith999Weight   = $this->objFromFixture('SilvercartProduct', 'product2');
        $productWith1000Weight  = $this->objFromFixture('SilvercartProduct', 'product3');
        $productWith10000Weight = $this->objFromFixture('SilvercartProduct', 'product4');
        $productDataZero        = array(
                                    'productID'         => $productWithZeroWeight->ID,
                                    'productQuantity'   => 10,
        );
        $productData999         = array(
                                    'productID'         => $productWith999Weight->ID,
                                    'productQuantity'   => 1,
        );
        $productData1000        = array(
                                    'productID'         => $productWith1000Weight->ID,
                                    'productQuantity'   => 1,
        );
        $productData10000       = array(
                                    'productID'         => $productWith10000Weight->ID,
                                    'productQuantity'   => 1,
        );
        
        //get a fee for a cart weight of zero
        SilvercartShoppingCart::addProduct($productDataZero);
        $shippingMethods        = SilvercartShippingMethod::getAllowedShippingMethods(null, $shippingAddress);
        $shippingFee            = $shippingMethods->first()->getShippingFee($cart->getWeightTotal());
        $targethippingFee       = $this->objFromFixture('SilvercartShippingFee', '499');
        $this->assertEquals((float) 0,  $cart->getWeightTotal(), 'The carts weight is not like expected.');
        $this->assertEquals('499',      $shippingFee->MaximumWeight, 'The matched shipping fee for a cart weight of zero is not the expected one.');
        
        //get a fee for a cart weight of 999g
        $cart->delete();
        SilvercartShoppingCart::addProduct($productData999);
        $shippingMethods        = SilvercartShippingMethod::getAllowedShippingMethods(null, $shippingAddress);
        $shippingFee            = $shippingMethods->first()->getShippingFee($cart->getWeightTotal());
        $targethippingFee       = $this->objFromFixture('SilvercartShippingFee', '999');
        $this->assertEquals((float) 999,    $cart->getWeightTotal(), 'The carts weight is not like expected.');
        $this->assertEquals('999',          $shippingFee->MaximumWeight, 'The matched shipping fee for a cart weight of 999g is not the expected one.');
        
        //get a fee for a cart weight of 1000g (corresponds to the max weight of one fee)
        $cart->delete();
        SilvercartShoppingCart::addProduct($productData1000);
        $shippingMethods        = SilvercartShippingMethod::getAllowedShippingMethods(null, $shippingAddress);
        $shippingFee            = $shippingMethods->first()->getShippingFee($cart->getWeightTotal());
        $targethippingFee       = $this->objFromFixture('SilvercartShippingFee', '9999');
        $this->assertEquals((float) 1000,   $cart->getWeightTotal(), 'The carts weight is not like expected.');
        $this->assertEquals('9999',         $shippingFee->MaximumWeight, 'The matched shipping fee for a cart weight of 1000g is not the expected one.');
        
        //get a fee for a cart weight of 10.000g (no fee set for this weight)
        $cart->delete();
        SilvercartShoppingCart::addProduct($productData10000);
        $shippingMethods        = SilvercartShippingMethod::getAllowedShippingMethods(null, $shippingAddress);
        $shippingFee            = $shippingMethods->first()->getShippingFee($cart->getWeightTotal());
        $targethippingFee       = $this->objFromFixture('SilvercartShippingFee', 'unlimited');
        $this->assertEquals((float) 10000,  $cart->getWeightTotal(), 'The carts weight is not like expected.');
        $this->assertEquals(1,              $shippingFee->UnlimitedWeight, 'The matched shipping fee for a cart weight of 1000g is not the expected one.');
        
    }
    
    /**
     * the method is not testable because of a call $this->getShippingFee()
     * returns the title + carrier + fee as a string
     *
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 08.03.2012
     */
    public function testGetTitleWithCarrierAndFee() {
        
    }
    
    /**
     * returns the title + carrier as a string [carrier] - [title]
     *
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>, Carolin Wörner <cwoerner@pixeltricks.de>
     * @since 30.01.2013
     */
    public function testGetTitleWithCarrier() {
        i18n::set_locale('en_US');
        $shippingMethod = $this->objFromFixture('SilvercartShippingMethod', 'ActiveShippingMethod');
        $this->assertEquals('DHL - Package', $shippingMethod->getTitleWithCarrier(), 'The title with carrier is not build correctly');
    }
    
    /**
     * returns the related zones as a string
     * used for display in backend tables
     *
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 08.03.2012
     */
    public function testAttributedZones() {
        $shippingMethod = $this->objFromFixture('SilvercartShippingMethod', 'ActiveShippingMethod');
        $this->assertEquals('domestic, EU', $shippingMethod->AttributedZones(), 'The string with attributed zones to a shipping method is not build correctly.');
    }
    
    /**
     * returns attributed payment methods as a string
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 08.03.2012
     */
    public function testAttributedPaymentMethods() {
        
    }
    
    /**
     * returns all activated shipping methods of the installation
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 08.03.2012
     */
    public function testGetAllowedShippingMethods() {
        
    }
}

