<?php

/**
 * test class for SilvercartManufacturer
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 28.12.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartManufacturerTest extends SapphireTest {
    
    public static $fixture_file = 'silvercart/tests/SilvercartManufacturerTest.yml';
    
    /**
     * test for getByUrlSegment()
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.12.2011
     */
    public function testGetByUrlSegment() {
        $manufacturerFromFixture = $this->objFromFixture('SilvercartManufacturer', "testmanufacturer");
        $manufacturerFromMethod = SilvercartManufacturer::getByUrlSegment('testmanufacturer');
        $this->assertEquals($manufacturerFromFixture, $manufacturerFromMethod);
    }
    
    /**
     * test for title2urlSegment()
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.12.2011
     */
    public function testTitle2urlSegment() {
        $manufacturer = $this->objFromFixture('SilvercartManufacturer', 'manufacturerWithUmlaut');
        $this->assertEquals($manufacturer->title2urlSegment(), 'aeoeueAeOeUe----');
    }
}

