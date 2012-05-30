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
     * Attributes
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.05.2012
     */
    public static $db = array(
        'widgetHeight' => 'Int',
        'speed'        => 'Int',
        'interval'     => 'Int',
        'hoverPause'   => 'Boolean',
        'autoPlay'     => 'Boolean',
        'effect'       => "Enum('fade,slideLeft,slideRight,slideUp,slideDown,overLeft,overRight,overUp,overDown', 'fade')"
    );
    
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.05.2012
     */
    public function getCMSFields() {
        $fields = new FieldSet();
        $rootTabSet     = new TabSet('Root');
        $basicTab       = new Tab('Basic', $this->fieldLabel('BasicTab'));
        $advancedTab    = new Tab('Advanced', $this->fieldLabel('AdvancedTab'));
        $translationTab = new Tab('Translations', $this->fieldLabel('TranslationsTab'));
        
        $titleField   = new TextField('FrontTitle',               $this->fieldLabel('FrontTitle'));
        $contentField = new TextareaField('FrontContent',         $this->fieldLabel('FrontContent'), 10);
        
        $productGroupHolder = SilvercartTools::PageByIdentifierCode('SilvercartProductGroupHolder');
        $productGroupDropdown = new SilvercartTreeMultiselectField(
                'SCProductGroupPages',
                $this->fieldLabel('SCProductGroupPages'),
                'SiteTree'
        );
        $productGroupDropdown->setTreeBaseID($productGroupHolder->ID);
        
        $translationsTableField = new ComplexTableField(
            $this,
            'SilvercartSlidorionProductGroupWidgetLanguages',
            'SilvercartSlidorionProductGroupWidgetLanguage'
        );
        $widgetHeightField = new TextField(
            'widgetHeight',
            $this->fieldLabel('widgetHeight')
        );
        $speedField = new TextField(
            'speed',
            $this->fieldLabel('speed')
        );
        $intervalField = new TextField(
            'interval',
            $this->fieldLabel('interval')
        );
        $effectField = new DropdownField(
            'effect',
            $this->fieldLabel('effect'),
            singleton('SilvercartSlidorionProductGroupWidget')->dbObject('effect')->enumValues(),
            $this->effect
        );
        $hoverPauseField = new CheckboxField(
            'hoverPause',
            $this->fieldLabel('hoverPause')
        );
        $autoPlayField = new CheckboxField(
            'autoPlay',
            $this->fieldLabel('autoPlay')
        );
        
        $basicTab->push($productGroupDropdown);
        $basicTab->push($titleField);
        $basicTab->push($contentField);

        $advancedTab->push($widgetHeightField);
        $advancedTab->push($speedField);
        $advancedTab->push($intervalField);
        $advancedTab->push($effectField);
        $advancedTab->push($hoverPauseField);
        $advancedTab->push($autoPlayField);
        
        $translationTab->push($translationsTableField);
        
        $fields->push($rootTabSet);
        $rootTabSet->push($basicTab);
        $rootTabSet->push($advancedTab);
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
            'SCProductGroupPages' => _t('SilvercartSlidorionProductGroupWidget.SCPRODUCTGROUPPAGES'),
            'BasicTab'            => _t('SilvercartSlidorionProductGroupWidget.CMS_BASICTABNAME'),
            'AdvancedTab'         => _t('SilvercartSlidorionProductGroupWidget.CMS_ADVANCEDTABNAME'),
            'TranslationsTab'     => _t('SilvercartConfig.TRANSLATIONS'),
            'FrontTitle'          => _t('SilvercartSlidorionProductGroupWidget.FRONT_TITLE'),
            'FrontContent'        => _t('SilvercartSlidorionProductGroupWidget.FRONT_CONTENT'),
            'widgetHeight'        => _t('SilvercartSlidorionProductGroupWidget.WIDGET_HEIGHT'),
            'speed'               => _t('SilvercartSlidorionProductGroupWidget.SPEED'),
            'interval'            => _t('SilvercartSlidorionProductGroupWidget.INTERVAL'),
            'hoverPause'          => _t('SilvercartSlidorionProductGroupWidget.HOVERPAUSE'),
            'autoPlay'            => _t('SilvercartSlidorionProductGroupWidget.AUTOPLAY'),
            'effect'              => _t('SilvercartSlidorionProductGroupWidget.EFFECT'),
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Returns the widget height.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.05.2012
     */
    public function getWidgetHeightValue() {
        $widgetHeight = 400;

        if (!empty($this->widgetHeight)) {
            $widgetHeight = (int) $this->widgetHeight;
        }

        if ($widgetHeight == 0) {
            $widgetHeight = 400;
        }

        return $widgetHeight;
    }

    /**
     * Returns the animation speed.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.05.2012
     */
    public function getSpeedValue() {
        $speed = 1000;

        if (!empty($this->speed)) {
            $speed = (int) $this->speed;
        }

        if ($speed == 0) {
            $speed = 1000;
        }

        return $speed;
    }

    /**
     * Returns the interval speed.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.05.2012
     */
    public function getIntervalValue() {
        $interval = 6000;

        if (!empty($this->interval)) {
            $interval = (int) $this->interval;
        }

        if ($interval == 0) {
            $interval = 6000;
        }

        return $interval;
    }

    /**
     * Returns whether to pause on hover as boolean string
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.05.2012
     */
    public function getHoverPauseValue() {
        $hoverPause = 'false';

        if (!empty($this->hoverPause)) {
            $hoverPause = $this->hoverPause;

            if ($hoverPause) {
                $hoverPause = 'true';
            } else {
                $hoverPause = 'false';
            }
        }

        return $hoverPause;
    }

    /**
     * Returns whether to autoplay as boolean string
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.05.2012
     */
    public function getAutoPlayValue() {
        $autoPlay = 'false';

        if (!empty($this->autoPlay)) {
            $autoPlay = $this->autoPlay;

            if ($autoPlay) {
                $autoPlay = 'true';
            } else {
                $autoPlay = 'false';
            }
        }

        return $autoPlay;
    }

    /**
     * Returns the effect type
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.05.2012
     */
    public function getEffectValue() {
        $effect = 'fade';

        if (!empty($this->effect)) {
            $effect = $this->effect;
        }

        return $effect;
    }

    /**
     * Returns the group picture list as HTML string.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.05.2012
     */
    public function getGroupPictureList() {
        $list = '';

        foreach ($this->SCProductGroupPages() as $SCProductGroupPage) {
            $list .= '<div class="silvercart-slidorion-slide">';
            $list .= $SCProductGroupPage->GroupPicture()->SetRatioSize(426, $this->getSliderHeight());
            $list .= '</div>';
        }

        return $list;
    }

    /**
     * Returns the slider height.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.05.2012
     */
    public function getSliderHeight() {
        return $this->getWidgetHeightValue() - 15;
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
                        speed:      %d,
                        interval:   %d,
                        effect:     '%s',
                        hoverPause: %s,
                        autoPlay:   %s
                    });
                })})(jQuery);
                ",
                $this->ID,
                $this->getSpeedValue(),
                $this->getIntervalValue(),
                $this->getEffectValue(),
                $this->getHoverPauseValue(),
                $this->getAutoPlayValue()
            )
        );
        
        $slidorionHeight        = $this->getWidgetHeightValue();
        $numberOfItems          = $this->SCProductGroupPages()->Count();
        $accordeonTitleHeight   = 30;
        $correctionHeight       = 16;
        $accordeonContentHeight = $slidorionHeight - $numberOfItems * $accordeonTitleHeight - $correctionHeight;
        
        Requirements::customCSS(
            sprintf(
                "
                #silvercart-slidorion-%d.silvercart-widget-slidorion-productgroup-slider {
                    height: %dpx;
                }
                #silvercart-slidorion-%d .silvercart-slidorion-slider {
                    height: %dpx;
                }
                #silvercart-slidorion-%d .silvercart-slidorion-accordeon {
                    height: %dpx;
                }
                #silvercart-slidorion-%d .silvercart-slidorion-accordeon > .silvercart-slidorion-link-content {
                    height: %dpx;
                }
                ",
                $this->ID,
                $this->getWidgetHeightValue(),
                $this->ID,
                $this->getSliderHeight(),
                $this->ID,
                $this->getWidgetHeightValue(),
                $this->ID,
                $accordeonContentHeight
            )
        );
    }
}
