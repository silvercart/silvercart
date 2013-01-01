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
 * test for SilvercartFile
 *
 * @package Silvercart
 * @subpackage Tests
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 19.12.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
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
        $testfile = $this->objFromFixture("SilvercartFile", "testfile");
        $this->assertEquals('<img src="'.SAPPHIRE_DIR . '/images/app_icons/pdf_32.gif" alt="Adobe Acrobat PDF file" title="Testfile" />', $testfile->getFileIcon());
    }
}

