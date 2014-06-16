<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartSlidorionProductGroupWidget extends SilvercartWidget {
    
    /**
     * Attributes
     *
     * @var array
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
     */
    public static $has_many = array(
        'SilvercartSlidorionProductGroupWidgetLanguages' => 'SilvercartSlidorionProductGroupWidgetLanguage'
    );
    
    /**
     * Has_many relationships.
     *
     * @var array
     */
    public static $many_many = array(
        'SilvercartImages' => 'SilvercartImage'
    );
    
    /**
     * Has_many relationships.
     *
     * @var array
     */
    private static $many_many_extraFields = array(
        'SilvercartImages' => array(
            'Sort' => 'Int',
        ),
    );
    /**
     * Default attributes
     *
     * @var array
     */
    private static $defaults = array(
        'widgetHeight' => 400,
        'speed'        => 500,
        'interval'     => 3000,
        'autoPlay'     => true,
    );
    
    /**
     * Castings.
     *
     * @var array
     */
    public static $casting = array(
        'FrontTitle'                    => 'VarChar(255)',
        'FrontContent'                  => 'Text',
    );
    
    /**
     * Width in pixel for the slidorion image
     *
     * @var int
     */
    public static $image_width = 426;
    
    /**
     * padding in pixel for the slidorion image
     *
     * @var int
     */
    public static $image_padding = 15;

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'SilvercartImages'            => _t('SilvercartSlidorionProductGroupWidget.SILVERCARTIMAGES'),
                    'SilvercartImagesDescription' => _t('SilvercartSlidorionProductGroupWidget.SilvercartImagesDescription'),
                    'BasicTab'                    => _t('SilvercartSlidorionProductGroupWidget.CMS_BASICTABNAME'),
                    'AdvancedTab'                 => _t('SilvercartSlidorionProductGroupWidget.CMS_ADVANCEDTABNAME'),
                    'TranslationsTab'             => _t('SilvercartConfig.TRANSLATIONS'),
                    'FrontTitle'                  => _t('SilvercartSlidorionProductGroupWidget.FRONT_TITLE'),
                    'FrontContent'                => _t('SilvercartSlidorionProductGroupWidget.FRONT_CONTENT'),
                    'widgetHeight'                => _t('SilvercartSlidorionProductGroupWidget.WIDGET_HEIGHT'),
                    'speed'                       => _t('SilvercartSlidorionProductGroupWidget.SPEED'),
                    'interval'                    => _t('SilvercartSlidorionProductGroupWidget.INTERVAL'),
                    'hoverPause'                  => _t('SilvercartSlidorionProductGroupWidget.HOVERPAUSE'),
                    'autoPlay'                    => _t('SilvercartSlidorionProductGroupWidget.AUTOPLAY'),
                    'effect'                      => _t('SilvercartSlidorionProductGroupWidget.EFFECT'),
                    'translations'                => _t('SilvercartConfig.TRANSLATIONS'),
                    'Images'                      => _t('SilvercartImage.PLURALNAME'),
                    'AddImage'                    => _t('SilvercartProductSliderWidget.AddImage'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns the input fields for this widget without scaffolding
     * 
     * @return FieldList
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.09.2012
     */
    public function getCMSFields() {
        $fields = new FieldList();
        $rootTabSet     = new TabSet('Root');
        $basicTab       = new Tab('Basic', $this->fieldLabel('BasicTab'));
        $translationTab = new Tab('Translations', $this->fieldLabel('TranslationsTab'));
        
        $titleField   = new TextField('FrontTitle',               $this->fieldLabel('FrontTitle'));
        $contentField = new TextareaField('FrontContent',         $this->fieldLabel('FrontContent'), 10);
        
        $imageTable = new GridField(
                'SilvercartImages',
                $this->fieldLabel('Images'),
                $this->SilvercartImages()->sort('Sort'),
                SilvercartGridFieldConfig_RelationEditor::create()
        );
        
        $imageTable->getConfig()->removeComponentsByType('GridFieldAddNewButton');
        $imageTable->getConfig()->removeComponentsByType('GridFieldAddExistingAutocompleter');
        $imageTable->getConfig()->addComponent(new GridFieldDeleteAction());
        $imageTable->getConfig()->addComponent(new GridFieldSortableRows('Sort'));
        
        $imagesUploadDescription = sprintf(
                $this->fieldLabel('SilvercartImagesDescription'),
                $this->getSliderHeight()
        );
        
        $imagesUploadField = new SilvercartImageUploadField('UploadSilvercartImages', $this->fieldLabel('AddImage'));
        $imagesUploadField->setFolderName('Uploads/silvercart-images');
        $imagesUploadField->setDescription($imagesUploadDescription);
                
        $translationsTableField = new GridField(
                'SilvercartSlidorionProductGroupWidgetLanguages',
                $this->fieldLabel('translations'),
                $this->SilvercartSlidorionProductGroupWidgetLanguages(),
                SilvercartGridFieldConfig_ExclusiveRelationEditor::create()
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
        
        $advancedToggle = ToggleCompositeField::create(
                'AdvancedToggle',
                $this->fieldLabel('AdvancedTab'),
                array(
                    $widgetHeightField,
                    $speedField,
                    $intervalField,
                    $effectField,
                    $hoverPauseField,
                    $autoPlayField,
                )
        )->setHeadingLevel(4);
        
        $basicTab->push($titleField);
        $basicTab->push($contentField);
        $basicTab->push($imagesUploadField);
        $basicTab->push($imageTable);
        $basicTab->push($advancedToggle);
        
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
        return $this->getLanguageFieldValue('FrontTitle');
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
        return $this->getLanguageFieldValue('FrontContent');
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.07.2013
     */
    public function getGroupPictureList() {
        $list = '';

        foreach ($this->getImagesToDisplay() as $imageToDisplay) {
            $list .= '<div class="silvercart-slidorion-slide" style="background: url(' . $imageToDisplay->resizedImage->getURL() . ') no-repeat center;">';
            $list .= '<div class="silvercart-slidorion-slide-prev"><div class="arrow"><div></div></div></div>';
            if ($imageToDisplay->Link()) {
                $list .= '<a class="silvercart-slidorion-slide-click" href="' . $imageToDisplay->Link() . '"></a>';
            }
            $list .= '<div class="silvercart-slidorion-slide-next"><div class="arrow_outer"><div class="arrow"><div></div></div></div></div>';
            $list .= $imageToDisplay->Content;
            $list .= '</div>';
        }

        return $list;
    }
    
    /**
     * Returns the images to display
     * 
     * @return ArrayList
     */
    public function getImagesToDisplay() {
        $imagesToDisplay = new ArrayList();

        foreach ($this->SilvercartImages()->sort('Sort') as $SilvercartImage) {
            if ($SilvercartImage->ImageID > 0) {
                $image          = $SilvercartImage->Image();
                $resizedImage   = $image->SetRatioSize(self::$image_width, $this->getSliderHeight());
                if ($resizedImage) {
                    $SilvercartImage->resizedImage = $resizedImage;
                    $imagesToDisplay->push($SilvercartImage);
                }
            }
        }

        return $imagesToDisplay;
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
        return $this->getWidgetHeightValue() - self::$image_padding;
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
     * Creates the cache key for this widget.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>, Sascha Koehler <skoehler@pixeltricks.de>
     * @since 02.07.2012
     */
    public function WidgetCacheKey() {
        $key = i18n::get_locale() . '_' . $this->ClassName() . '_' . $this->ID . '_' . $this->LastEdited;
        return $key;
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
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
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
        Requirements::customScript(
            sprintf(
                "
                (function($) {
                    jQuery(document).ready(function(){
                        var slidorionSelector = '#silvercart-slidorion-%d';
                        $(slidorionSelector).slidorion({
                            speed:      %d,
                            interval:   %d,
                            effect:     '%s',
                            hoverPause: %s,
                            autoPlay:   %s
                        });
                        $(slidorionSelector + ' .silvercart-slidorion-slide-prev').click(function() {
                            var prevObj = $(slidorionSelector + ' .silvercart-slidorion-link-header.active').prevAll('.silvercart-slidorion-link-header');
                            if (prevObj.length == 0) {
                                prevObj = $(slidorionSelector + ' .silvercart-slidorion-link-header').last();
                            }
                            prevObj.trigger('click');
                        });
                        $(slidorionSelector + ' .silvercart-slidorion-slide-next').click(function() {
                            var nextObj = $(slidorionSelector + ' .silvercart-slidorion-link-header.active').nextAll('.silvercart-slidorion-link-header');
                            if (nextObj.length == 0) {
                                nextObj = $(slidorionSelector + ' .silvercart-slidorion-link-header').first();
                            }
                            nextObj.trigger('click');
                        });
                    });
                })(jQuery);
                ",
                $this->ID,
                $this->getSpeedValue(),
                $this->getIntervalValue(),
                $this->getEffectValue(),
                $this->getHoverPauseValue(),
                $this->getAutoPlayValue()
            ),
            'silvercart-slidorion-' . $this->ID
        );
        
        $slidorionHeight        = $this->getWidgetHeightValue();
        $numberOfItems          = $this->getImagesToDisplay()->count();
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
            ),
            'silvercart-slidorion-' . $this->ID . '-css'
        );
    }
}
