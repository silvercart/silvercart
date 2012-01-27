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
 * Provides an image slider powered by AnythingSlider.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 19.10.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartImageSliderWidget extends SilvercartWidget {
    
    /**
     * Attributes.
     * 
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.10.2011
     */
    public static $db = array(
        'Autoplay'                      => 'Boolean(1)',
        'autoPlayDelayed'               => 'Boolean(1)',
        'autoPlayLocked'                => 'Boolean(0)',
        'buildArrows'                   => 'Boolean(1)',
        'buildNavigation'               => 'Boolean(1)',
        'buildStartStop'                => 'Boolean(1)',
        'slideDelay'                    => 'Int',
        'stopAtEnd'                     => 'Boolean(0)',
        'transitionEffect'              => "Enum('fade,horizontalSlide,verticalSlide','fade')",
        'useSlider'                     => "Boolean(0)"
    );
    
    public static $casting = array(
        'FrontTitle'                    => 'VarChar(255)',
        'FrontContent'                  => 'Text'
    );
    
    /**
     * 1:n relationships.
     *
     * @var array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 27.01.2012
     */
    public static $has_many = array(
        'SilvercartImageSliderWidgetLanguages' => 'SilvercartImageSliderWidgetLanguage'
    );
    
    /**
     * Many-many relationships
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 19.10.2011
     */
    public static $many_many = array(
        'slideImages' => 'SilvercartImageSliderImage'
    );
    
    /**
     * Getter for the multilingula FrontTitle
     *
     * @return string 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 27.01.2012
     */
    public function getFrontTitle() {
        $title = '';
        if ($this->getLanguage()) {
            $title = $this->getLanguage()->FrontTitle;
        }
        return $title;
    }
    
    /**
     * Getter for the multilingula FrontContent
     *
     * @return string 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 27.01.2012
     */
    public function getFrontContent() {
        $content = '';
        if ($this->getLanguage()) {
            $content = $this->getLanguage()->FrontContent;
        }
        return $content;
    }
    
    /**
     * HtmlEditorFields need an own save method
     *
     * @param string
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 27.01.2012
     */
    public function saveFrontContent($value) {
        $langObj = $this->getLanguage();
        $langObj->FrontContent = $value;
        $langObj->write();
    }
    
    /**
     * Returns the title of this widget.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 19.10.2011
     */
    public function Title() {
        return _t('SilvercartImageSliderWidget.TITLE');
    }
    
    /**
     * Returns the title of this widget for display in the WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 19.10.2011
     */
    public function CMSTitle() {
        return _t('SilvercartImageSliderWidget.CMSTITLE');
    }
    
    /**
     * Returns the description of what this template does for display in the
     * WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 19.10.2011
     */
    public function Description() {
        return _t('SilvercartImageSliderWidget.DESCRIPTION');
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 19.10.2011
     */
    public function getCMSFields() {
        $fields = new FieldSet();
        
        $imageField                 = new ManyManyFileDataObjectManager(
            $this,
            'slideImages',
            'SilvercartImageSliderImage'
        );
        $autoplay                   = new CheckboxField('Autoplay', _t('SilvercartProductGroupItemsWidget.AUTOPLAY'));
        $slideDelay                 = new TextField('slideDelay', _t('SilvercartProductGroupItemsWidget.SLIDEDELAY'));
        $buildArrows                = new CheckboxField('buildArrows', _t('SilvercartProductGroupItemsWidget.BUILDARROWS'));
        $buildNavigation            = new CheckboxField('buildNavigation', _t('SilvercartProductGroupItemsWidget.BUILDNAVIGATION'));
        $buildStartStop             = new CheckboxField('buildStartStop', _t('SilvercartProductGroupItemsWidget.BUILDSTARTSTOP'));
        $autoPlayDelayed            = new CheckboxField('autoPlayDelayed', _t('SilvercartProductGroupItemsWidget.AUTOPLAYDELAYED'));
        $autoPlayLocked             = new CheckboxField('autoPlayLocked', _t('SilvercartProductGroupItemsWidget.AUTOPLAYLOCKED'));
        $stopAtEnd                  = new CheckboxField('stopAtEnd', _t('SilvercartProductGroupItemsWidget.STOPATEND'));
        $transitionEffect           = new DropdownField(
            'transitionEffect',
            _t('SilvercartProductGroupItemsWidget.TRANSITIONEFFECT'),
            array(
                'fade'              => _t('SilvercartProductGroupItemsWidget.TRANSITION_FADE'),
                'horizontalSlide'   => _t('SilvercartProductGroupItemsWidget.TRANSITION_HORIZONTALSLIDE'),
                'verticalSlide'     => _t('SilvercartProductGroupItemsWidget.TRANSITION_VERTICALSLIDE')
            )
        );
        
        $rootTabSet = new TabSet('SilvercartProductGroupItemsWidget');
        $basicTab   = new Tab('basic', _t('SilvercartProductGroupItemsWidget.CMS_BASICTABNAME'));
        $sliderTab  = new Tab('anythingSlider', _t('SilvercartProductGroupItemsWidget.CMS_SLIDERTABNAME'));
        $imagesTab  = new Tab('slideImages', _t('SilvercartProductGroupItemsWidget.CMS_SLIDEIMAGESTABNAME', 'Images'));
        $translationsTab = new Tab('TranslationsTab');
        $translationsTab->setTitle(_t('SilvercartConfig.TRANSLATIONS'));
        
        $fields->push($rootTabSet);
        $rootTabSet->push($basicTab);
        $rootTabSet->push($imagesTab);
        $rootTabSet->push($sliderTab);
        $rootTabSet->push($translationsTab);
        
        //multilingual fields, in fact just the title
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguage());
        foreach ($languageFields as $languageField) {
            $basicTab->push($languageField);
        }
        
        $sliderTab->push($autoplay);
        $sliderTab->push($slideDelay);
        $sliderTab->push($buildArrows);
        $sliderTab->push($buildNavigation);
        $sliderTab->push($buildStartStop);
        $sliderTab->push($autoPlayDelayed);
        $sliderTab->push($autoPlayLocked);
        $sliderTab->push($stopAtEnd);
        $sliderTab->push($transitionEffect);
        
        $imagesTab->push($imageField);
        
        $translationsTab->push(new ComplexTableField($this, 'SilvercartImageSliderWidgetLanguages', 'SilvercartImageSliderWidgetLanguage'));
        
        return $fields;
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
     * @since 27.01.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),             array(
                'SilvercartImageSliderWidgetLanguages' => _t('SilvercartImageSliderWidgetLanguage.PLURALNAME')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
}

/**
 * Provides a view of items of a definable productgroup.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 20.10.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartImageSliderWidget_Controller extends SilvercartWidget_Controller {
    
    /**
     * Create javascript for the slider.
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.10.2011
     */
    public function init() {
        $autoplay           = 'false';
        $autoPlayDelayed    = 'false';
        $autoPlayLocked     = 'true';
        $stopAtEnd          = 'false';
        $buildArrows        = 'false';
        $buildStartStop     = 'false';
        $buildNavigation    = 'false';

        if ($this->Autoplay) {
            $autoplay = 'true';
        }
        if ($this->buildArrows) {
            $buildArrows = 'true';
        }
        if ($this->buildNavigation) {
            $buildNavigation = 'true';
        }
        if ($this->buildStartStop) {
            $buildStartStop = 'true';
        }
        if ($this->autoPlayDelayed) {
            $autoPlayDelayed = 'true';
        }
        if ($this->autoPlayLocked) {
            $autoPlayLocked = 'false';
        }
        if ($this->stopAtEnd) {
            $stopAtEnd = 'true';
        }

        switch ($this->transitionEffect) {
            case 'horizontalSlide':
                $vertical           = 'false';
                $animationTime      = 500;
                $delayBeforeAnimate = 0;
                $effect             = 'swing';
                break;
            case 'verticalSlide':
                $vertical           = 'true';
                $animationTime      = 500;
                $delayBeforeAnimate = 0;
                $effect             = 'swing';
                break;
            case 'fade':
            default:
                $vertical           = 'false';
                $animationTime      = 0;
                $delayBeforeAnimate = 500;
                $effect             = 'fade';
        }
            
        Requirements::css('silvercart/css/screen/sliders/theme-silvercart-default.css');
        Requirements::customScript(
            sprintf('
                $(document).ready(function() {
                    $("#SilvercartImageSliderWidget%d")
                    .anythingSlider({
                        startPanel:         1,
                        autoPlay:           %s,
                        autoPlayDelayed:    %s,
                        autoPlayLocked:     %s,
                        stopAtEnd:          %s,
                        buildArrows:        %s,
                        buildNavigation:    %s,
                        buildStartStop:     %s,
                        delay:              %d,
                        animationTime:      %s,
                        delayBeforeAnimate: %d,
                        theme:              \'silvercart-default\',
                        vertical:           %s,
                        navigationFormatter: function(index, panel){
                            panel.css("display", "block");
                            return index;
                        }
                    })
                    .anythingSliderFx({
                        // base FX definitions
                        // ".selector" : [ "effect(s)", "size", "time", "easing" ]
                        // "size", "time" and "easing" are optional parameters, but must be kept in order if added
                        \'.panel\' : [ \'%s\', \'\', 500, \'easeInOutCirc\' ]
                    });
                });
                ',
                $this->ID,
                $autoplay,
                $autoPlayDelayed,
                $autoPlayLocked,
                $stopAtEnd,
                $buildArrows,
                $buildNavigation,
                $buildStartStop,
                $this->slideDelay,
                $animationTime,
                $delayBeforeAnimate,
                $vertical,
                $effect
            )
        );
    }
    
    /**
     * This widget should always be a content view.
     *
     * @return boolean true
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.10.2011
     */
    public function isContentView() {
        return true;
    }
}