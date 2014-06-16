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
 * Provides an image slider powered by AnythingSlider.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 19.10.2011
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartImageSliderWidget extends SilvercartWidget {
    
    /**
     * Attributes.
     * 
     * @var array
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
    
    /**
     * Casted Attributes.
     * 
     * @var array
     */
    public static $casting = array(
        'FrontTitle'                    => 'VarChar(255)',
        'FrontContent'                  => 'Text'
    );
    
    /**
     * 1:n relationships.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartImageSliderWidgetLanguages' => 'SilvercartImageSliderWidgetLanguage'
    );
    
    /**
     * Many-many relationships
     *
     * @var array
     */
    public static $many_many = array(
        'slideImages' => 'SilvercartImageSliderImage'
    );
    
    /**
     * Getter for the multilingula FrontTitle
     *
     * @return string
     */
    public function getFrontTitle() {
        return $this->getLanguageFieldValue('FrontTitle');
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
        return $this->getLanguageFieldValue('FrontContent');
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.03.2014
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this, 'ExtraCssClasses', false);
        
        $slideImagesTable = new GridField(
                'slideImages',
                $this->fieldLabel('slideImages'),
                $this->slideImages(),
                SilvercartGridFieldConfig_RelationEditor::create()
        );
        
        $slideImagesTable->getConfig()->removeComponentsByType('GridFieldAddNewButton');
        $slideImagesTable->getConfig()->removeComponentsByType('GridFieldAddExistingAutocompleter');
        $slideImagesTable->getConfig()->addComponent(new GridFieldDeleteAction());
        
        $slideImagesUploadField = new SilvercartImageUploadField('UploadSlideImages', $this->fieldLabel('AddImage'));
        $slideImagesUploadField->setFolderName('Uploads/slider-images');
        $slideImagesUploadField->setRelationClassName('SilvercartImageSliderImage');
        
        $fields->findOrMakeTab('Root.slideImages', $this->fieldLabel('slideImages'));
        $fields->addFieldToTab('Root.slideImages', $slideImagesUploadField);
        $fields->addFieldToTab('Root.slideImages', $slideImagesTable);
        
        $this->getCMSFieldsSliderTab($fields);
        
        return $fields;
    }
    
    /**
     * Returns an array of field/relation names (db, has_one, has_many, 
     * many_many, belongs_many_many) to exclude from form scaffolding in
     * backend.
     * This is a performance friendly way to exclude fields.
     * Excludes all fields that are added in a ToggleCompositeField later.
     * 
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Ramon Kupper <rkupper@pixeltricks.de>
     * @since 04.01.2014
     */
    public function excludeFromScaffolding() {
        $parentExcludes = parent::excludeFromScaffolding();
        
        $excludeFromScaffolding = array_merge(
                $parentExcludes,
                array(
                    'slideImages',
                    'Autoplay',
                    'autoPlayDelayed',
                    'autoPlayLocked',
                    'buildArrows',
                    'buildNavigation',
                    'buildStartStop',
                    'slideDelay',
                    'stopAtEnd',
                    'transitionEffect',
                    'useSlider',
                    'useRoundabout'
                )
        );
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }
    
    /**
     * Returns the slider tab input fields for this widget.
     * 
     * @param TabSet &$rootTabSet The root tab set
     * 
     * @return FieldList
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public function getCMSFieldsSliderTab(&$rootTabSet) {
        SilvercartWidgetTools::getCMSFieldsSliderTabForProductSliderWidget($this, $rootTabSet);
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2013
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                SilvercartWidgetTools::fieldLabelsForProductSliderWidget($this),
                array(
                    'SilvercartImageSliderWidgetLanguages'  => _t('Silvercart.TRANSLATIONS'),
                    'FrontTitle'                            => _t('SilvercartWidget.FRONTTITLE'),
                    'FrontContent'                          => _t('SilvercartWidget.FRONTCONTENT'),
                    'Images'                                => _t('SilvercartImage.PLURALNAME'),
                    'SilvercartImageSliderImage'            => _t('SilvercartImageSliderImage.PLURALNAME'),
                    'slideImages'                           => _t('SilvercartProductSliderWidget.CMS_SLIDERIMAGES'),
                    'Translations'                          => _t('SilvercartConfig.TRANSLATIONS'),
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
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
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
        if (!SilvercartWidget::$use_anything_slider) {
            return;
        }
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