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
 * @subpackage Translation
 */

/**
 * Adds methods that are common to all language classes eg SilvercartProductLanguage
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 06.01.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartLanguageDecorator extends DataObjectDecorator {
    
    /**
     * Extends the database fields and relations of the decorated class.
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 06.01.2012
     */
    public function extraStatics() {
        return array(
            'db' => array(
                'Locale' => 'DBLocale'
            )
        );
    }
    
    /**
     * Field lable for Locale should always be multilingual
     *
     * @param
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since DD.MM.2012
     */
    public function updateFieldLabels(&$lables) {
        parent::updateFieldLabels($lables);
        $lables['Locale'] = _t('SilvercartProductLanguage.LOCALE');
    }
    
    /**
     * must return true for the LanguageDropdown field to work properly
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2012
     */
    public function canTranslate() {
        return true;
    }
    
    /**
     * The summary fields should at least show the locale
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 06.01.2012
     */
    public function updateSummaryFields(&$fields) {
        $fields = array_merge(
                array(
                    'NativeNameForLocale' => _t('SilvercartConfig.TRANSLATION')
                ),
                $fields
        );
    }
    
    /**
     * adjust CMS fields for display in the popup window
     *
     * @param FieldSet &$fields the FieldSet from getCMSFields()
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 06.01.2012
     */
    public function updateCMSFields(FieldSet &$fields) {
        $fields = SilvercartLanguageHelper::prepareCMSFields($this->owner);
        foreach ($this->owner->has_one() as $has_oneName => $has_oneObject) {
            $fields->removeByName($has_oneName . 'ID');
        }
        $localeDropdown = SilvercartLanguageHelper::prepareLanguageDropdownField($this->owner);
        $fields->insertFirst($localeDropdown);
    }
    
    /**
     * return the locale as native name
     *
     * @return string native name for the locale 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 06.01.2012
     */
    public function getNativeNameForLocale() {
        return $this->owner->dbObject('Locale')->getNativeName();
    }
}

