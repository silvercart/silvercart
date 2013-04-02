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
 * test for SilvercartFile
 *
 * @package Silvercart
 * @subpackage Tests
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 19.12.2011
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartFileTest extends SapphireTest {
    
    /**
     * Fixture file
     *
     * @var string
     */
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
        i18n::set_locale('en_US');
        $testfile = $this->objFromFixture("SilvercartFile", "testfile");
        $this->assertEquals('<img src="'.SAPPHIRE_DIR . '/images/app_icons/pdf_32.gif" alt="Adobe Acrobat PDF file" title="Testfile" />', $testfile->getFileIcon());
    }
}

