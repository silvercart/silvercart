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
 * @subpackage Base
 */

/**
 * Provides methods for common widget tasks in SilverCart.
 * 
 * @package Silvercart
 * @subpackage Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 28.03.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartWidgetTools extends Object {
    
    /**
     * Returns the input fields for this widget.
     * 
     * @param SilvercartWidget_Controller $widget Widget to initialize
     * 
     * @return FieldSet
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public static function getCMSFieldsForProductSliderWidget(SilvercartWidget_Controller $widget) {
        $fetchMethods               = array(
                'random'        => $widget->fieldLabel('fetchMethodRandom'),
                'sortOrderAsc'  => $widget->fieldLabel('fetchMethodSortOrderAsc'),
                'sortOrderDesc' => $widget->fieldLabel('fetchMethodSortOrderDesc'),
        );
        
        $fields                     = new FieldSet();
        $titleField                 = new TextField('FrontTitle',               $widget->fieldLabel('FrontTitle'));
        $contentField               = new TextareaField('FrontContent',         $widget->fieldLabel('FrontContent'), 10);
        $numberOfProductsShowField  = new TextField('numberOfProductsToShow',   $widget->fieldLabel('numberOfProductsToShow'));
        $numberOfProductsFetchField = new TextField('numberOfProductsToFetch',  $widget->fieldLabel('numberOfProductsToFetch'));
        $isContentView              = new CheckboxField('isContentView',        $widget->fieldLabel('isContentView'));
        $fetchMethod                = new DropdownField('fetchMethod',          $widget->fieldLabel('fetchMethod'), $fetchMethods);
        $groupViewField             = SilvercartGroupViewHandler::getGroupViewDropdownField('GroupView', $widget->fieldLabel('GroupView'), $widget->GroupView);
        
        $rootTabSet                 = new TabSet('Root');
        $basicTab                   = new Tab('Basic',          $widget->fieldLabel('BasicTab'));
        $displayTabSet              = new TabSet('DisplaySet');
        $displayTab                 = new Tab('Display',        $widget->fieldLabel('DisplayTab'));
        
        $fields->push($rootTabSet);
        $rootTabSet->push($basicTab);
        $basicTab->push($displayTabSet);
        $displayTabSet->push($displayTab);

        $displayTab->push($titleField);
        $displayTab->push($contentField);
        $displayTab->push($groupViewField);
        $displayTab->push($isContentView);
        $displayTab->push($fetchMethod);
        $displayTab->push($numberOfProductsShowField);
        $displayTab->push($numberOfProductsFetchField);
        
        $widget->getCMSFieldsSliderTab($rootTabSet);
        /*
         * does not work on a standard installation yet
         */
        //$widget->getCMSFieldsRoundaboutTab($rootTabSet);
        
        return $fields;
    }
    
    /**
     * Returns the slider tab input fields for this widget.
     * 
     * @param SilvercartWidget_Controller $widget      Widget to initialize
     * @param TabSet                      &$rootTabSet The root tab set
     * 
     * @return FieldSet
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public static function getCMSFieldsSliderTabForProductSliderWidget(SilvercartWidget_Controller $widget, &$rootTabSet) {
        $sliderTab          = new Tab('anythingSlider',             $widget->fieldLabel('SliderTab'));
        $useSlider          = new CheckboxField('useSlider',        $widget->fieldLabel('useSlider'));
        $autoplay           = new CheckboxField('Autoplay',         $widget->fieldLabel('Autoplay'));
        $slideDelay         = new TextField('slideDelay',           $widget->fieldLabel('slideDelay'));
        $buildArrows        = new CheckboxField('buildArrows',      $widget->fieldLabel('buildArrows'));
        $buildNavigation    = new CheckboxField('buildNavigation',  $widget->fieldLabel('buildNavigation'));
        $buildStartStop     = new CheckboxField('buildStartStop',   $widget->fieldLabel('buildStartStop'));
        $autoPlayDelayed    = new CheckboxField('autoPlayDelayed',  $widget->fieldLabel('autoPlayDelayed'));
        $autoPlayLocked     = new CheckboxField('autoPlayLocked',   $widget->fieldLabel('autoPlayLocked'));
        $stopAtEnd          = new CheckboxField('stopAtEnd',        $widget->fieldLabel('stopAtEnd'));
        $transitionEffect   = new DropdownField(
            'transitionEffect',
            $widget->fieldLabel('transitionEffect'),
            array(
                'fade'              => $widget->fieldLabel('transitionEffectFade'),
                'horizontalSlide'   => $widget->fieldLabel('transitionEffectHSlide'),
                'verticalSlide'     => $widget->fieldLabel('transitionEffectVSlide'),
            )
        );
        
        $sliderTab->push($useSlider);
        $sliderTab->push($autoplay);
        $sliderTab->push($slideDelay);
        $sliderTab->push($buildArrows);
        $sliderTab->push($buildNavigation);
        $sliderTab->push($buildStartStop);
        $sliderTab->push($autoPlayDelayed);
        $sliderTab->push($autoPlayLocked);
        $sliderTab->push($stopAtEnd);
        $sliderTab->push($transitionEffect);
        $rootTabSet->push($sliderTab);
    }
    
    /**
     * Returns the slider tab input fields for this widget.
     * 
     * @param SilvercartWidget_Controller $widget      Widget to initialize
     * @param TabSet                      &$rootTabSet The root tab set
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public static function getCMSFieldsRoundaboutTabForProductSliderWidget(SilvercartWidget_Controller $widget, &$rootTabSet) {
        $tab        = new Tab('roundabout',                 $widget->fieldLabel('RoundaboutTab'));
        $useSlider  = new CheckboxField('useRoundabout',    $widget->fieldLabel('useRoundabout'));
        
        $tab->push($useSlider);
        $rootTabSet->push($tab);
    }
    
    /**
     * Default initialization of a product slider widget
     * 
     * @param SilvercartWidget_Controller $widget Widget to initialize
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public static function initProductSliderWidget(SilvercartWidget_Controller $widget) {
        if ($widget->useSlider ||
            $widget->useRoundabout) {
            $widget->ProductPages();
        } else {
            $widget->Elements();
        }
        
        if ($widget->getElements()) {
            $elementIdx = 0;

            if ($widget->useSlider ||
                $widget->useRoundabout) {
                // Roundabout / Slider
                foreach ($widget->getElements() as $productPage) {
                    foreach ($productPage as $elementHolder) {
                        foreach ($elementHolder->Elements as $element) {
                            self::registerAddCartFormForProductSliderWidget($widget, $element, $elementIdx);
                        }
                    }
                }
            } else {
                // Standard view
                foreach ($widget->getElements() as $element) {
                    self::registerAddCartFormForProductSliderWidget($widget, $element, $elementIdx);
                }
            }
        }
        
        if ($widget->useSlider) {
            $widget->initAnythingSlider();
        } elseif ($widget->useRoundabout) {
            $widget->initRoundabout();
        }
    }
    
    /**
     * Insert the javascript necessary for the anything slider.
     * 
     * @param SilvercartWidget_Controller $widget Widget to initialize
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public static function initAnythingSliderForProductSliderWidget(SilvercartWidget_Controller $widget) {
        $autoplay           = 'false';
        $autoPlayDelayed    = 'false';
        $autoPlayLocked     = 'true';
        $stopAtEnd          = 'false';
        $buildArrows        = 'false';
        $buildStartStop     = 'false';
        $buildNavigation    = 'false';

        if ($widget->Autoplay) {
            $autoplay = 'true';
        }
        if ($widget->buildArrows) {
            $buildArrows = 'true';
        }
        if ($widget->buildNavigation) {
            $buildNavigation = 'true';
        }
        if ($widget->buildStartStop) {
            $buildStartStop = 'true';
        }
        if ($widget->autoPlayDelayed) {
            $autoPlayDelayed = 'true';
        }
        if ($widget->autoPlayLocked) {
            $autoPlayLocked = 'false';
        }
        if ($widget->stopAtEnd) {
            $stopAtEnd = 'true';
        }

        switch ($widget->transitionEffect) {
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

        $jsID = $widget->ClassName . 'Slider' . $widget->ID;
        Requirements::customScript(
            sprintf('
                $(document).ready(function() {
                    $("#%s")
                    .anythingSlider({
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
                $jsID,
                $autoplay,
                $autoPlayDelayed,
                $autoPlayLocked,
                $stopAtEnd,
                $buildArrows,
                $buildNavigation,
                $buildStartStop,
                $widget->slideDelay,
                $animationTime,
                $delayBeforeAnimate,
                $vertical,
                $effect
            )
        );
    }
    
    /**
     * Insert the javascript necessary for the roundabout slider.
     * 
     * @param SilvercartWidget_Controller $widget Widget to initialize
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public static function initRoundaboutForProductSliderWidget(SilvercartWidget_Controller $widget) {
        $jsID = $widget->ClassName . 'Slider' . $widget->ID;
        Requirements::customScript(
            sprintf('
                $(document).ready(function() {
                    $("#%s").roundabout({
                        shape:        "square",
                        duration:     500,
                        minScale:     1.0,
                        maxScale:     1.0,
                        minOpacity:   0.9,
                        tilt:         0.0,
                        degree:       0
                    });
                    $("#%s .roundabout-in-focus").css({
                        width: \'670px\',
                        height: \'252px\',
                        margin: \'-13px 0px 0px -150px\'
                    });
                    $("#%s .roundabout-in-focus .c20r").css("display", "block");
                    $("#%s .roundabout-in-focus .c30l").css("display", "block");
                    
                    $("#%s .roundabout-moveable-item").bind("focus", function() {
                        $(this).animate({
                                width: \'+=335\',
                                height: \'+=26\',
                                marginLeft: \'-=150\',
                                marginTop: \'-=13\'
                            },
                            400,
                            ""
                        );
                        $(this).find(".c20r").show();
                        $(this).find(".c30l").show();
                        
                        return true;
                    });
                    $("#%s .roundabout-moveable-item").bind("blur", function() {
                        $(this).find(".c20r").hide();
                        $(this).find(".c30l").hide();
                        
                        $(this).css({
                            width: \'-=335\',
                            height: \'-=26\',
                            marginLeft: \'+=150\',
                            marginTop: \'+=13\'
                        });
                        
                        return true;
                    });
                });
                ',
                $jsID,
                $jsID,
                $jsID,
                $jsID,
                $jsID,
                $jsID
            )
        );
    }
    
    /**
     * Returns the template to render the products with
     *
     * @param SilvercartWidget_Controller $widget              Widget to get template for
     * @param string                      $templateBaseContent Base name for the content widget template
     * @param string                      $templateBaseSidebar Base name for the sidebar widget template
     * 
     * @return string
     */
    public static function getGroupViewTemplateName(SilvercartWidget_Controller $widget, $templateBaseContent = 'SilvercartProductGroupPage', $templateBaseSidebar = 'SilvercartWidgetProductBox') {
        if (empty($widget->GroupView)) {
            $widget->GroupView = SilvercartGroupViewHandler::getDefaultGroupView();
        }
        if ($widget->isContentView) {
            $groupViewTemplateName = SilvercartGroupViewHandler::getProductGroupPageTemplateNameFor($widget->GroupView, $templateBaseContent);
        } else {
            $groupViewTemplateName = SilvercartGroupViewHandler::getProductGroupPageTemplateNameFor($widget->GroupView, $templateBaseSidebar);
        }
        return $groupViewTemplateName;
    }

    /**
     * Default form registration routine of a product slider widget
     *
     * @param SilvercartWidget_Controller $widget      Widget to initialize
     * @param DataObjectSet               $element     Element to add cart form for
     * @param int                         &$elementIdx Element counter to use as ID and increment
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public static function registerAddCartFormForProductSliderWidget(SilvercartWidget_Controller $widget, $element, &$elementIdx) {
        if ($element instanceof SilvercartProduct) {
            if (empty($widget->GroupView)) {
                $widget->GroupView = SilvercartGroupViewHandler::getDefaultGroupView();
            }
            $controller             = Controller::curr();
            $groupView              = $widget->GroupView;
            $productAddCartFormName = SilvercartGroupViewHandler::getCartFormNameFor($groupView);
            $formIdentifier         = 'ProductAddCartForm' . $widget->ID . '_' . $element->ID;
            $productAddCartForm     = new $productAddCartFormName(
                $controller,
                array('productID' => $element->ID)
            );

            $controller->registerCustomHtmlForm(
                $formIdentifier,
                $productAddCartForm
            );

            $element->productAddCartForm = $controller->InsertCustomHtmlForm(
                $formIdentifier,
                array(
                    $element
                )
            );
            $element->productAddCartFormObj = $productAddCartForm;
            $elementIdx++;
        }
    }
    
    /**
     * We set checkbox field values here to false if they are not in the post
     * data array.
     *
     * @param SilvercartWidget_Controller $widget Widget to initialize
     * @param array                       $data   The post data array
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public static function populateFromPostDataForProductSliderWidget(SilvercartWidget_Controller $widget, $data) {
        $widget->write();
        if (!array_key_exists('isContentView', $data)) {
            $widget->isContentView = 0;
        }
        if (!array_key_exists('GroupView', $data)) {
            $widget->GroupView = SilvercartGroupViewHandler::getDefaultGroupView();
        }
        if (!array_key_exists('Autoplay', $data)) {
            $widget->autoplay = 0;
        }
        if (!array_key_exists('buildArrows', $data)) {
            $widget->buildArrows = 0;
        }
        if (!array_key_exists('buildNavigation', $data)) {
            $widget->buildNavigation = 0;
        }
        if (!array_key_exists('buildStartStop', $data)) {
            $widget->buildStartStop = 0;
        }
        if (!array_key_exists('autoPlayDelayed', $data)) {
            $widget->autoPlayDelayed = 0;
        }
        if (!array_key_exists('autoPlayLocked', $data)) {
            $widget->autoPlayLocked = 0;
        }
        if (!array_key_exists('stopAtEnd', $data)) {
            $widget->stopAtEnd = 0;
        }
        if (!array_key_exists('useSlider', $data)) {
            $widget->useSlider = 0;
        }
    }

    /**
     * Field labels for display in tables.
     * 
     * @param SilvercartWidget_Controller $widget Widget to initialize
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public static function fieldLabelsForProductSliderWidget(SilvercartWidget_Controller $widget) {
        return array(
            'FrontTitle'                    => _t('SilvercartProductSliderWidget.FRONTTITLE'),
            'FrontContent'                  => _t('SilvercartProductSliderWidget.FRONTCONTENT'),
            'numberOfProductsToShow'        => _t('SilvercartProductSliderWidget.NUMBEROFPRODUCTSTOSHOW'),
            'numberOfProductsToFetch'       => _t('SilvercartProductSliderWidget.NUMBEROFPRODUCTSTOFETCH'),
            'fetchMethod'                   => _t('SilvercartProductSliderWidget.FETCHMETHOD'),
            'useListView'                   => _t('SilvercartProductSliderWidget.USE_LISTVIEW'),
            'GroupView'                     => _t('SilvercartProductSliderWidget.GROUPVIEW'),
            'isContentView'                 => _t('SilvercartProductSliderWidget.IS_CONTENT_VIEW'),
            'Autoplay'                      => _t('SilvercartProductSliderWidget.AUTOPLAY'),
            'autoPlayDelayed'               => _t('SilvercartProductSliderWidget.AUTOPLAYDELAYED'),
            'autoPlayLocked'                => _t('SilvercartProductSliderWidget.AUTOPLAYLOCKED'),
            'buildArrows'                   => _t('SilvercartProductSliderWidget.BUILDARROWS'),
            'buildNavigation'               => _t('SilvercartProductSliderWidget.BUILDNAVIGATION'),
            'buildStartStop'                => _t('SilvercartProductSliderWidget.BUILDSTARTSTOP'),
            'slideDelay'                    => _t('SilvercartProductSliderWidget.SLIDEDELAY'),
            'stopAtEnd'                     => _t('SilvercartProductSliderWidget.STOPATEND'),
            'transitionEffect'              => _t('SilvercartProductSliderWidget.TRANSITIONEFFECT'),
            'useSlider'                     => _t('SilvercartProductSliderWidget.USE_SLIDER'),
            'useRoundabout'                 => _t('SilvercartProductSliderWidget.USE_ROUNDABOUT'),

            'RoundaboutTab'                 => _t('SilvercartProductSliderWidget.CMS_ROUNDABOUTTABNAME'),
            'SliderTab'                     => _t('SilvercartProductSliderWidget.CMS_SLIDERTABNAME'),
            'BasicTab'                      => _t('SilvercartProductSliderWidget.CMS_BASICTABNAME'),
            'DisplayTab'                    => _t('SilvercartProductSliderWidget.CMS_DISPLAYTABNAME'),

            'transitionEffectFade'          => _t('SilvercartProductSliderWidget.TRANSITION_FADE'),
            'transitionEffectHSlide'        => _t('SilvercartProductSliderWidget.TRANSITION_HORIZONTALSLIDE'),
            'transitionEffectVSlide'        => _t('SilvercartProductSliderWidget.TRANSITION_VERTICALSLIDE'),

            'fetchMethodRandom'             => _t($widget->ClassName() . '.FETCHMETHOD_RANDOM',         _t('SilvercartProductSliderWidget.FETCHMETHOD_RANDOM')),
            'fetchMethodSortOrderAsc'       => _t($widget->ClassName() . '.FETCHMETHOD_SORTORDERASC',   _t('SilvercartProductSliderWidget.FETCHMETHOD_SORTORDERASC')),
            'fetchMethodSortOrderDesc'      => _t($widget->ClassName() . '.FETCHMETHOD_SORTORDERDESC',  _t('SilvercartProductSliderWidget.FETCHMETHOD_SORTORDERDESC')),
        );
    }
    
    /**
     * Loads the requirements for this object
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.06.2012
     */
    public static function loadRequirements() {
        Requirements::themedCSS('SilvercartAnythingSlider');
    }
    
    /**
     * Creates the cache key for this widget.
     * 
     * @param SilvercartWidget_Controller $widget Widget to get cache key for
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2012
     */
    public static function ProductWidgetCacheKey($widget) {
        $key                    = '';
        if ($widget->Elements() instanceof DataObjectSet &&
            $widget->Elements()->Count() > 0) {
            $productMap             = $widget->Elements()->map('ID', 'LastEdited');
            if (!is_array($productMap)) {
                $productMap = array();
            }
            if ($widget->Elements()->Count() > 0 &&
                (empty($productMap) ||
                (count($productMap) == 1 &&
                array_key_exists('', $productMap)))) {
                $productMap = array();
                foreach ($widget->Elements() as $page) {
                    $productMap = array_merge(
                            $productMap,
                            $page->Elements->map('ID', 'LastEdited')
                    );
                }
            }
            $productMapIDs          = implode('_', array_keys($productMap));
            sort($productMap);
            $productMapLastEdited   = array_pop($productMap);
            $groupIDs               = '';

            if (Member::currentUserID() > 0) {
                $groupIDs = implode('-', Member::currentUser()->getGroupIDs());
            }
            $keyParts = array(
                i18n::get_locale(),
                $productMapIDs,
                $productMapLastEdited,
                $widget->LastEdited,
                $groupIDs
            );

            $key = implode('_', $keyParts);
        }
        
        return $key;
    }
    
}