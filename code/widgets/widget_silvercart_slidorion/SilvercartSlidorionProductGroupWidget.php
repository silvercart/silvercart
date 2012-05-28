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
 * @subpackage Widgets
 */

/**
 * Provides a slidorion box for product groups.
 * See "http://www.slidorion.com/".
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 28.05.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
 */
class SilvercartSlidorionProductGroupWidget extends SilvercartWidget {
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    public static $has_many = array(
        'SilvercartSlidorionProductGroupWidgetLanguages' => 'SilvercartSlidorionProductGroupWidgetLanguage'
    );
    
    /**
     * Has_many relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    public static $many_many = array(
        'SCProductGroupPages' => 'SilvercartProductGroupPage'
    );
    
    /**
     * Castings.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    public static $casting = array(
        'FrontTitle'                    => 'VarChar(255)',
        'FrontContent'                  => 'HTMLText',
    );
    
    /**
     * Returns the title of this widget.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    public function Title() {
        return _t('SilvercartSlidorionProductGroupWidget.TITLE');
    }
    
    /**
     * Returns the title of this widget for display in the WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    public function CMSTitle() {
        return _t('SilvercartSlidorionProductGroupWidget.CMSTITLE');
    }
    
    /**
     * Returns the description of what this template does for display in the
     * WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    public function Description() {
        return _t('SilvercartSlidorionProductGroupWidget.DESCRIPTION');
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    public function getCMSFields() {
        $fields = new FieldSet();
        $rootTabSet                 = new TabSet('Root');
        $basicTab                   = new Tab('Basic', $this->fieldLabel('BasicTab'));
        $translationTab             = new Tab('Translations', $this->fieldLabel('TranslationsTab'));
        
        $titleField            = new TextField('FrontTitle',               $this->fieldLabel('FrontTitle'));
        $contentField          = new TextareaField('FrontContent',         $this->fieldLabel('FrontContent'), 10);
        $productGroupPageTable = new ManyManyComplexTableField(
            $this,
            'SCProductGroupPages',
            'SilvercartProductGroupPage'
        );
        $translationsTableField = new ComplexTableField(
            $this,
            'SilvercartSlidorionProductGroupWidgetLanguages',
            'SilvercartSlidorionProductGroupWidgetLanguage'
        );
        
        $basicTab->push($titleField);
        $basicTab->push($contentField);
        $basicTab->push($productGroupPageTable);
        
        $translationTab->push($translationsTableField);
        
        $fields->push($rootTabSet);
        $rootTabSet->push($basicTab);
        $rootTabSet->push($translationTab);
        
        return $fields;
    }
    
    /**
     * Getter for the front title depending on the set language
     *
     * @return string  
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    public function getFrontTitle() {
        $frontTitle = '';
        if ($this->getLanguage()) {
            $frontTitle = $this->getLanguage()->FrontTitle;
        }
        return $frontTitle;
    }
    
    /**
     * Getter for the FrontContent depending on the set language
     *
     * @return string The HTML front content 
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    public function getFrontContent() {
        $frontContent = '';
        if ($this->getLanguage()) {
            $frontContent = $this->getLanguage()->FrontContent;
        }
        return $frontContent;
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array(
            'BasicTab'          => _t('SilvercartSlidorionProductGroupWidget.CMS_BASICTABNAME'),
            'TranslationsTab'   => _t('SilvercartConfig.TRANSLATIONS'),
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * This widget is for content view only.
     *
     * @return boolean true
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    public function isContentView() {
        return true;
    }
    
    /**
     * HtmlEditorFields need an own save method
     *
     * @param string $value content
     *
     * @return void 
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    public function saveFrontContent($value) {
        $langObj = $this->getLanguage();
        $langObj->FrontContent = $value;
        $langObj->write();
    }
    
    /**
     * Save relations
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    public function onBeforeWrite() {
        parent::onBeforeWrite();
        
        $this->SCProductGroupPages()->removeAll();
        
        if (array_key_exists('SCProductGroupPages', $_REQUEST) &&
            is_array($_REQUEST['SCProductGroupPages'])) {
            
            foreach ($_REQUEST['SCProductGroupPages'] as $idx => $productGroupPageId) {
                
                $silvercartProductGroupPage = DataObject::get_by_id(
                    'SilvercartProductGroupPage',
                    Convert::raw2sql($productGroupPageId)
                );
                
                if ($silvercartProductGroupPage) {
                    $this->SCProductGroupPages()->add($silvercartProductGroupPage);
                }
            }
        }
    }
}

/**
 * Provides a slidorion box for product groups.
 * See "http://www.slidorion.com/".
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 28.05.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
 */
class SilvercartSlidorionProductGroupWidget_Controller extends SilvercartWidget_Controller {
    
    /**
     * Load javascript and css files.
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    public function init() {
        Requirements::themedCSS("slidorion");
        Requirements::javascript(SilvercartTools::getBaseURLSegment()."silvercart/script/slidorion/js/jquery.slidorion.js");
        
        Requirements::customScript(
            sprintf(
                "
                (function($) {jQuery(document).ready(function(){
                    $('#silvercart-slidorion-%d').slidorion({
                        speed:      1000,
                        interval:   4000,
                        effect:     'slideDown',
                        hoverPause: true,
                        autoPlay:   true
                    });
                })})(jQuery);
                ",
                $this->ID
            )
        );
    }
}