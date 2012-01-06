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
 * @package SilverCart
 * @subpackage Translation
 */

/**
 * Defines reqired methods for language classes eg SilvercartProductLanguage
 *
 * @package Silvercart
 * @subpackage translation
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since DD.MM.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
 */
interface SilvercartLanguageInterface {
    
    /**
     * must return true to work with the Translatable extension
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2012
     */
    public function canTranslate();
    
    /**
     * The relation to the SilvercartProduct must be removed and the Locale should be a dropdown
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2012
     */
    public function getCMSFields_forPopup();
    
    /**
     * The summary fields should contain the Locale
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2012
     */
    public function summaryFields();
    
    /**
     * As we are in to i18n we need multilingual field labels
     * 
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since DD.MM.2012
     */
    public function fieldLabels();
    
    /**
     * return the locale as native name, eg "deutsch" in stead of "de_DE"
     *
     * @return string native name for the locale 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 06.01.2012
     */
    public function getNativeNameForLocale();
}
