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
 * test class for SilvercartManufacturer
 *
 * @package Silvercart
 * @subpackage Tests
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 28.12.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
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

