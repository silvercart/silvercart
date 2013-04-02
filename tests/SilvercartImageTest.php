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
 * test class for SilvercartImage
 *
 * @package Silvercart
 * @subpackage Tests
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 28.12.2011
 * @license see license file in modules root directory
 */
class SilvercartImageTest extends SapphireTest {
    
    /**
     * Fixture file
     *
     * @var string
     */
    public static $fixture_file = 'silvercart/tests/SilvercartImageTest.yml';
    
    /**
     * test for getFileIcon()
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>, Carolin Wörner <cwoerner@pixeltricks.de>
     * @since 29.01.2013
     */
    public function testGetFileIcon() {
        i18n::set_locale('en_US');
        $testimage = $this->objFromFixture('SilvercartImage', 'testimage');
        $this->assertEquals('<img src="'.SAPPHIRE_DIR . '/images/app_icons/image_32.gif" alt="JPEG image - good for photos" title="testimage" />', $testimage->getFileIcon());
    }
}

