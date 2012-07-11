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
    
    public static $db = array(
        'isContentView'     => 'Boolean',
    );
    
    public static $casting = array(
        'Headline'          => 'Text',
        'FreeText'          => 'Text',
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
     * retirieves the attribute Headline from related language class depending
     * on the current locale
     *
     * @return string 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012
     */
    public function getHeadline() {
        $headline = '';
        if ($this->getLanguage()) {
            $headline = $this->getLanguage()->Headline;
        }
        return $headline;
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
                'ExtraCssClasses'               => _t('SilvercartText.CSSFIELD_LABEL'),
                'SilvercartTextWidgetLanguages' => _t('SilvercartTextWidgetLanguage.PLURALNAME'),
                'Headline'                      => _t('SilvercartText.HEADLINEFIELD_LABEL'),
                'FreeText'                      => _t('SilvercartText.FREETEXTFIELD_LABEL'),
                'isContentView'                 => _t('SilvercartTextWidget.IS_CONTENT_VIEW'),
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function getCMSFields() {
        $fields             = new FieldSet();
        $rootTabSet         = new TabSet('RootTabSet');
        $mainTab            = new Tab('Root',               _t('Silvercart.CONTENT'));
        $translationsTab    = new Tab('TranslationsTab',    _t('SilvercartConfig.TRANSLATIONS'));
        
        $cssField           = new TextField('ExtraCssClasses', $this->fieldLabel('ExtraCssClasses'));
        $languageTableField = new ComplexTableField($this, 'SilvercartTextWidgetLanguages', 'SilvercartTextWidgetLanguage');
        $isContentView      = new CheckboxField('isContentView', $this->fieldLabel('isContentView'));
        
        $fields->push($rootTabSet);
        $rootTabSet->push($mainTab);
        $rootTabSet->push($translationsTab);
        
        $mainTab->push($cssField);
        $mainTab->push($isContentView);
        $translationsTab->push($languageTableField);
        //multilingual fields, in fact just the title
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguage(true));
        foreach ($languageFields as $languageField) {
            $mainTab->push($languageField);
        }

        return $fields;
    }
}

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
class SilvercartTextWidget_Controller extends SilvercartWidget_Controller {

    /**
     * Overloaded from {@link Widget->Content()}
     * to allow for controller/form linking.
     *
     * @return string HTML
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.04.2012
     */
    public function Content() {
        $renderData = array(
            'Controller' => $this
        );
        $template = new SSViewer_FromString($this->getField('FreeText'));
        $freeText = HTTP::absoluteURLs($template->process(new ArrayData($renderData)));

        $data = new ArrayData(
            array(
                'FreeText' => $freeText
            )
        );

        return $this->customise($data)->renderWith(array_reverse(ClassInfo::ancestry($this->widget->class)));
    }
}
