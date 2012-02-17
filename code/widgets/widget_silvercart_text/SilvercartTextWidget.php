<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * @subpackage Widgets
 */

/**
 * Provides a free text widget.
 * 
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 09.06.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartTextWidget extends SilvercartWidget {
    
    public static $casting = array(
        'FreeText'  => 'HTMLText'
    );
    
    /**
     * 1:n relationships.
     *
     * @var array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 22.01.2012
     */
    public static $has_many = array(
        'SilvercartTextWidgetLanguages' => 'SilvercartTextWidgetLanguage'
    );
    
    /**
     * saves the value of the field FreeText correctly into HTMLText
     * 
     * @param string $value the field value
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 22.01.2012
     */
    public function saveFreeText($value) {
        $languageObj = $this->getLanguage();
        $languageObj->FreeText = $value;
        $languageObj->write();
    }

    /**
     * retirieves the attribute FreeText from related language class depending
     * on the set locale
     *
     * @return string 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 22.01.2012
     */
    public function getFreeText() {
        $title = '';
        if ($this->getLanguage()) {
            $title = $this->getLanguage()->FreeText;
        }
        return $title;
    }

    /**
     * Returns the title of this widget.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Title() {
        return _t('SilvercartText.TITLE');
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2012 pixeltricks GmbH
     * @since 26.01.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'SilvercartTextWidgetLanguages' => _t('SilvercartTextWidgetLanguage.')
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns the title of this widget for display in the WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function CMSTitle() {
        return _t('SilvercartText.TITLE');
    }
    
    /**
     * Returns the description of what this template does for display in the
     * WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Description() {
        return _t('SilvercartText.DESCRIPTION');
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.06.2011
     */
    public function getCMSFields() {
        $fields = new FieldSet();
        $rootTabSet = new TabSet('RootTabSet');
        $mainTab = new Tab('Root');
        $translationsTab = new Tab('TranslationsTab');
        $translationsTab->push(new ComplexTableField($this, 'SilvercartTextWidgetLanguages', 'SilvercartTextWidgetLanguage'));
        $mainTab->setTitle(_t('Silvercart.CONTENT'));
        $translationsTab->setTitle(_t('SilvercartConfig.TRANSLATIONS'));
        $rootTabSet->push($mainTab);
        $rootTabSet->push($translationsTab);
        $fields->push($rootTabSet);
        //multilingual fields, in fact just the title
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguage());
        foreach ($languageFields as $languageField) {
            $mainTab->push($languageField);
        }
        return $fields;
    }
}
