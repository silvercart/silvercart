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
 * test class for SilvercartManufacturer
 *
 * @package Silvercart
 * @subpackage Tests
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 28.12.2011
 * @license see license file in modules root directory
 */
class SilvercartManufacturerTest extends SapphireTest {
    
    /**
     * Fixture file
     *
     * @var string
     */
    public static $fixture_file = 'silvercart/tests/SilvercartManufacturerTest.yml';
    
    /**
     * test for getByUrlSegment()
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>, Carolin Wörner <cwoerner@pixeltricks.de>
     * @since 28.01.2013
     */
    public function testGetByUrlSegment() {
        $manufacturerFromFixture = $this->objFromFixture('SilvercartManufacturer', "testmanufacturer");
        $manufacturerFromMethod = SilvercartManufacturer::getByUrlSegment('test-manufacturer');
        $this->assertEquals($manufacturerFromFixture, $manufacturerFromMethod);
    }
    
    /**
     * test for title2urlSegment()
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>, Carolin Wörner <cwoerner@pixeltricks.de>
     * @since 28.01.2013
     */
    public function testTitle2urlSegment() {
        $manufacturer = $this->objFromFixture('SilvercartManufacturer', 'manufacturerWithUmlaut');
        $this->assertEquals($manufacturer->title2urlSegment(), 'ae-oe-ue-ae-oe-and-ue');
    }
}

