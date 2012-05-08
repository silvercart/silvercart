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
 * @subpackage tests
 */
/**
 * tests for SilvercartShippingMethod
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 08.03.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartShippingMethodTest extends SapphireTest {
    
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
        //get a fee for a cart weight of zero
        
        //get a fee for a cart weight of 999g
        
        //get a fee for a cart weight of 1000g (corresponds to the max weight of one fee)
        
        //get a fee for a cart weight of 10.000g (no fee set for this weight)
        
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
     * returns the title + carrier as a string [carrier]-[title]
     *
     *
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 08.03.2012
     */
    public function testGetTitleWithCarrier() {
        $shippingMethod = $this->objFromFixture('SilvercartShippingMethod', 'ActiveShippingMethod');
        $this->assertEquals('DHL-Package', $shippingMethod->getTitleWithCarrier(), 'The title with carrier is not build correctly');
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

