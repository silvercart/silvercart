<?php

/**
 * test for SilvercartFile
 *
 * @package Silvercart
 * @subpacke Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 19.12.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartFileTest extends SapphireTest {
    
    public static $fixture_file = 'silvercart/tests/SilvercartFileTest.yml';
    
    /**
     * test for getFileIcon()
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.12.2011
     */
    public function testGetFileIcon() {
        $testfile = $this->objFromFixture("SilvercartFile", "testfile");
        $this->assertEquals('<img src="'.SAPPHIRE_DIR . '/images/app_icons/pdf_32.gif" alt="Adobe Acrobat PDF file" title="Testfile" />', $testfile->getFileIcon());
    }
}

