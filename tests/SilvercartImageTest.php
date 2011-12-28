<?php

/**
 * test class for SilvercartImage
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 28.12.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartImageTest extends SapphireTest {
    
    public static $fixture_file = 'silvercart/tests/SilvercartImageTest.yml';
    
    /**
     * test for getFileIcon()
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.12.2011
     */
    public function testGetFileIcon() {
        $testimage = $this->objFromFixture('SilvercartImage', 'testimage');
        $this->assertEquals('<img src="'.SAPPHIRE_DIR . '/images/app_icons/image_32.gif" alt="JPEG image - good for photos" title="testimage" />', $testimage->getFileIcon());
    }
}

