<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Widgets\Widget;
use SilverCart\Model\Widgets\WidgetController;
use SilverCart\ORM\DataObjectExtension;
use SilverCart\View\GroupView\GroupViewHandler;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Director;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\ {
    CheckboxField,
    DropdownField,
    Tab,
    TextField,
    ToggleCompositeField,
    TreeDropdownField
};
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\Map;
use SilverStripe\ORM\SS_List;
use SilverStripe\View\Requirements;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * Provides methods for common widget tasks in SilverCart.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class WidgetTools
{
    
    /**
     * Returns the input fields for this widget.
     * 
     * @param Widget $widget       Widget to initialize
     * @param array  $fetchMethods Optional list of product fetch methods
     * 
     * @return FieldList
     * 
     * @deprecated since version 4.1
     */
    public static function getCMSFieldsForProductSliderWidget(Widget $widget, $fetchMethods = [])
    {
        if (empty($fetchMethods)) {
            $fetchMethods = [
                    'random'        => $widget->fieldLabel('fetchMethodRandom'),
                    'sortOrderAsc'  => $widget->fieldLabel('fetchMethodSortOrderAsc'),
                    'sortOrderDesc' => $widget->fieldLabel('fetchMethodSortOrderDesc'),
            ];
        }
        $fields = DataObjectExtension::getCMSFields($widget, 'ExtraCssClasses', false);
        
        $productGroupDropdown = TreeDropdownField::create(
                'ProductGroupPageID',
                $widget->fieldLabel('ProductGroupPage'),
                SiteTree::class
        );
        $productGroupDropdown->setTreeBaseID(Tools::PageByIdentifierCode(Page::IDENTIFIER_PRODUCT_GROUP_HOLDER)->ID);
        
        $toggleFields = [
            $fields->dataFieldByName('numberOfProductsToShow'),
            $fields->dataFieldByName('numberOfProductsToFetch'),
            $fields->dataFieldByName('fetchMethod'),
            GroupViewHandler::getGroupViewDropdownField('GroupView', $widget->fieldLabel('GroupView'), $widget->GroupView),
        ];
        
        $fields->dataFieldByName('fetchMethod')->setSource($fetchMethods);
        $fields->dataFieldByName('numberOfProductsToShow')->setDescription($widget->fieldLabel('numberOfProductsToShowInfo'));
        $fields->dataFieldByName('isContentView')->setDescription($widget->fieldLabel('isContentViewInfo'));
        
        if (is_object($fields->dataFieldByName('useSelectionMethod'))) {
            $fields->dataFieldByName('useSelectionMethod')->setSource(
                        [
                            'productGroup' => $widget->fieldLabel('SelectionMethodProductGroup'),
                            'products'     => $widget->fieldLabel('SelectionMethodProducts')
                        ]
            );
            $toggleFields[] = $fields->dataFieldByName('useSelectionMethod');
            $productGroupDropdown->setDescription($widget->fieldLabel('ProductGroupPageDescription'));
        }
        $toggleFields[] = $productGroupDropdown;
        $productDataToggle = ToggleCompositeField::create(
                'ProductDataToggle',
                $widget->fieldLabel('ProductDataToggle'),
                $toggleFields
        )->setHeadingLevel(4);
        
        $productsGrid          = $fields->dataFieldByName('Products');
        /* @var $productsGrid \SilverStripe\Forms\GridField\GridField */
        $productsGridConfig    = $productsGrid->getConfig();
        $productRelationToggle = ToggleCompositeField::create(
                'ProductRelationToggle',
                $widget->fieldLabel('ProductRelationToggle'),
                [
                    $productsGrid,
                ]
        )->setHeadingLevel(4);
        $productsGridConfig->removeComponentsByType(GridFieldAddNewButton::class);
        $extraFields = $widget->manyManyExtraFields();
        if (array_key_exists('Products', $extraFields)
         && array_key_exists('Sort', $extraFields['Products'])
         && class_exists(GridFieldOrderableRows::class)
        ) {
            $productsGridConfig->addComponent(new GridFieldOrderableRows('Sort'));
        }
        
        $fields->removeByName('numberOfProductsToShow');
        $fields->removeByName('numberOfProductsToFetch');
        $fields->removeByName('fetchMethod');
        $fields->removeByName('useSelectionMethod');
        $fields->removeByName('Products');
        
        $fields->addFieldToTab("Root.Main", $productDataToggle);
        $fields->addFieldToTab("Root.Main", $productRelationToggle);
        
        $widget->getCMSFieldsSliderTab($fields);
        //$widget->getCMSFieldsRoundaboutTab($fields);
        
        return $fields;
    }
    
    /**
     * Adds the slider toggle input fields for this widget.
     * 
     * @param Widget  $widget Widget to initialize
     * @param TabList $fields Fields to add toggle to
     * 
     * @return void
     * 
     * @deprecated since version 4.1
     */
    public static function getCMSFieldsSliderToggleForSliderWidget(Widget $widget, $fields)
    {
        $useSlider          = CheckboxField::create('useSlider',        $widget->fieldLabel('useSlider'));
        $autoplay           = CheckboxField::create('Autoplay',         $widget->fieldLabel('Autoplay'));
        $slideDelay         = TextField::create('slideDelay',           $widget->fieldLabel('slideDelay'));
        $buildArrows        = CheckboxField::create('buildArrows',      $widget->fieldLabel('buildArrows'));
        $buildNavigation    = CheckboxField::create('buildNavigation',  $widget->fieldLabel('buildNavigation'));
        $buildStartStop     = CheckboxField::create('buildStartStop',   $widget->fieldLabel('buildStartStop'));
        $stopAtEnd          = CheckboxField::create('stopAtEnd',        $widget->fieldLabel('stopAtEnd'));
        $transitionEffect   = DropdownField::create(
            'transitionEffect',
            $widget->fieldLabel('transitionEffect'),
            [
                'fade'              => $widget->fieldLabel('transitionEffectFade'),
                'horizontalSlide'   => $widget->fieldLabel('transitionEffectHSlide'),
                'verticalSlide'     => $widget->fieldLabel('transitionEffectVSlide'),
            ]
        );
        
        $sliderToggle = ToggleCompositeField::create(
                'Slider',
                $widget->fieldLabel('SlideshowTab'),
                [
                    $useSlider,
                    $transitionEffect,
                    $autoplay,
                    $slideDelay,
                    $buildArrows,
                    $buildNavigation,
                    $buildStartStop,
                    $stopAtEnd,
                ]
        )->setHeadingLevel(4);
        $fields->addFieldToTab("Root.Main", $sliderToggle);
    }
    
    /**
     * Returns the slider tab input fields for this widget.
     * 
     * @param Widget $widget     Widget to initialize
     * @param TabSet $rootTabSet The root tab set
     * 
     * @return void
     * 
     * @deprecated since version 4.1
     */
    public static function getCMSFieldsRoundaboutTabForProductSliderWidget(Widget $widget, $rootTabSet)
    {
        $tab        = Tab::create('roundabout',                 $widget->fieldLabel('RoundaboutTab'));
        $useSlider  = CheckboxField::create('useRoundabout',    $widget->fieldLabel('useRoundabout'));
        
        $tab->push($useSlider);
        $rootTabSet->push($tab);
    }
    
    /**
     * Default initialization of a product slider widget
     * 
     * @param WidgetController $widget Widget to initialize
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     * @deprecated since version 4.1
     */
    public static function initProductSliderWidget(WidgetController $widget)
    {
        if (Widget::$use_product_pages_for_slider
            && ($widget->useSlider#
                || $widget->useRoundabout)
        ) {
            $widget->ProductPages();
        } else {
            $widget->Elements();
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
     * @param WidgetController $widget Widget to initialize
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     * @deprecated since version 4.1
     */
    public static function initAnythingSliderForProductSliderWidget(WidgetController $widget)
    {
        if (!Widget::$use_anything_slider) {
            return;
        }
        $autoplay           = 'false';
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
                        autoPlayDelayed:    false,
                        autoPlayLocked:     true,
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
     * @param WidgetController $widget Widget to initialize
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     * @deprecated since version 4.1
     */
    public static function initRoundaboutForProductSliderWidget(WidgetController $widget)
    {
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
     * @param WidgetController|Widget $widget              Widget to get template for
     * @param string                  $templateBaseContent Base name for the content widget template
     * @param string                  $templateBaseSidebar Base name for the sidebar widget template
     * 
     * @return string
     */
    public static function getGroupViewTemplateName($widget, $templateBaseContent = 'ProductGroupPage', $templateBaseSidebar = 'WidgetProductBox')
    {
        if (empty($widget->GroupView)) {
            $widget->GroupView = GroupViewHandler::getDefaultGroupViewInherited();
        }
        if ($widget->isContentView) {
            $groupViewTemplateName = GroupViewHandler::getProductGroupPageTemplateNameFor($widget->GroupView, $templateBaseContent);
        } else {
            $groupViewTemplateName = GroupViewHandler::getProductGroupPageTemplateNameFor($widget->GroupView, $templateBaseSidebar);
        }
        return $groupViewTemplateName;
    }
    
    /**
     * We set checkbox field values here to false if they are not in the post
     * data array.
     *
     * @param WidgetController $widget Widget to initialize
     * @param array            $data   The post data array
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public static function populateFromPostDataForProductSliderWidget(WidgetController $widget, $data)
    {
        $widget->write();
        if (!array_key_exists('isContentView', $data)) {
            $widget->isContentView = 0;
        }
        if (!array_key_exists('GroupView', $data)) {
            $widget->GroupView = GroupViewHandler::getDefaultGroupViewInherited();
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
     * @param Widget $widget Widget to initialize
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     * @deprecated since version 4.1
     */
    public static function fieldLabelsForProductSliderWidget(Widget $widget)
    {
        return [
            'FrontTitle'                    => _t(ProductSliderWidget::class . '.FRONTTITLE', 'Headline'),
            'FrontContent'                  => _t(ProductSliderWidget::class . '.FRONTCONTENT', 'Content'),
            'numberOfProductsToShow'        => _t(ProductSliderWidget::class . '.NUMBEROFPRODUCTSTOSHOW', 'Number of products to show:'),
            'numberOfProductsToShowInfo'    => _t(ProductSliderWidget::class . '.NUMBEROFPRODUCTSTOSHOW_INFO', ' '),
            'numberOfProductsToFetch'       => _t(ProductSliderWidget::class . '.NUMBEROFPRODUCTSTOFETCH', 'Number of products to fetch:'),
            'fetchMethod'                   => _t(ProductSliderWidget::class . '.FETCHMETHOD', 'Selection method for products'),
            'useListView'                   => _t(ProductSliderWidget::class . '.USE_LISTVIEW', 'Use listview'),
            'GroupView'                     => _t(ProductSliderWidget::class . '.GROUPVIEW', 'Product list view'),
            'isContentView'                 => _t(ProductSliderWidget::class . '.IS_CONTENT_VIEW', 'Use regular productview instead of widgetview'),
            'isContentViewInfo'             => _t(ProductSliderWidget::class . '.IS_CONTENT_VIEW_INFO', 'If this widget is created to display inside the content area of a page, this option should be checked.'),
            'Autoplay'                      => _t(ProductSliderWidget::class . '.AUTOPLAY', 'Activate automatic slideshow'),
            'buildArrows'                   => _t(ProductSliderWidget::class . '.BUILDARROWS', 'Show next/previous buttons'),
            'buildNavigation'               => _t(ProductSliderWidget::class . '.BUILDNAVIGATION', 'Show page navigation'),
            'buildStartStop'                => _t(ProductSliderWidget::class . '.BUILDSTARTSTOP', 'Show start/stop buttons'),
            'slideDelay'                    => _t(ProductSliderWidget::class . '.SLIDEDELAY', 'Duration of panel display for the automatic slideshow'),
            'stopAtEnd'                     => _t(ProductSliderWidget::class . '.STOPATEND', 'Stop automatic slideshow after the last panel'),
            'transitionEffect'              => _t(ProductSliderWidget::class . '.TRANSITIONEFFECT', 'Transition effect'),
            'useSlider'                     => _t(ProductSliderWidget::class . '.USE_SLIDER', 'Use slider'),
            'useRoundabout'                 => _t(ProductSliderWidget::class . '.USE_ROUNDABOUT', 'Use roundabout'),
            'AddImage'                      => _t(ProductSliderWidget::class . '.AddImage', 'Add Image'),

            'ProductDataToggle'             => _t(ProductSliderWidget::class . '.ProductDataToggle', 'Product Settings'),
            'ProductRelationToggle'         => _t(ProductSliderWidget::class . '.ProductRelationToggle', 'Product Relation'),
            'RoundaboutTab'                 => _t(ProductSliderWidget::class . '.CMS_ROUNDABOUTTABNAME', 'Roundabout'),
            'SliderTab'                     => _t(ProductSliderWidget::class . '.CMS_SLIDERTABNAME', 'Slideshow Settings'),
            'SlideshowTab'                  => _t(ProductSliderWidget::class . '.CMS_SLIDERTABNAME', 'Slideshow Settings'),
            'BasicTab'                      => _t(ProductSliderWidget::class . '.CMS_BASICTABNAME', 'Basic preferences'),
            'DisplayTab'                    => _t(ProductSliderWidget::class . '.CMS_DISPLAYTABNAME', 'Display'),

            'transitionEffectFade'          => _t(ProductSliderWidget::class . '.TRANSITION_FADE', 'Fade'),
            'transitionEffectHSlide'        => _t(ProductSliderWidget::class . '.TRANSITION_HORIZONTALSLIDE', 'Horizontal slide'),
            'transitionEffectVSlide'        => _t(ProductSliderWidget::class . '.TRANSITION_VERTICALSLIDE', 'Vertical slide'),

            'fetchMethodRandom'             => _t($widget->ClassName() . '.FETCHMETHOD_RANDOM',         _t(ProductSliderWidget::class . '.FETCHMETHOD_RANDOM', 'Random')),
            'fetchMethodSortOrderAsc'       => _t($widget->ClassName() . '.FETCHMETHOD_SORTORDERASC',   _t(ProductSliderWidget::class . '.FETCHMETHOD_SORTORDERASC', 'Ascending')),
            'fetchMethodSortOrderDesc'      => _t($widget->ClassName() . '.FETCHMETHOD_SORTORDERDESC',  _t(ProductSliderWidget::class . '.FETCHMETHOD_SORTORDERDESC', 'Descending')),
        ];
    }
    
    /**
     * Creates the cache key for this widget.
     * 
     * @param WidgetController|Widget $widget Widget to get cache key for
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.10.2018
     */
    public static function ProductWidgetCacheKey($widget)
    {
        $key = '';
        if ($widget->Elements() instanceof SS_List
            && $widget->Elements()->exists()
        ) {
            $productMap = $widget->Elements()->map('ID', 'LastEditedForCache');
            if ($productMap instanceof Map) {
                $productMap = $productMap->toArray();
            }
            if (!is_array($productMap)) {
                $productMap = [];
            }
            if ($widget->Elements()->exists()
                && (empty($productMap)
                    || (count($productMap) == 1
                        && array_key_exists('', $productMap)))
            ) {
                $productMap = [];
                foreach ($widget->Elements() as $page) {
                    $productMapToAdd = $page->Elements->map('ID', 'LastEditedForCache');
                    if ($productMapToAdd instanceof Map) {
                        $productMapToAdd = $productMapToAdd->toArray();
                    }
                    $productMap = array_merge( 
                            $productMap,
                            $productMapToAdd
                    );
                }
            }
            $productMapIDs        = implode('_', array_keys($productMap));
            sort($productMap);
            $productMapLastEdited = array_pop($productMap);
            
            $keyParts = [
                i18n::get_locale(),
                $productMapIDs,
                $productMapLastEdited,
                $widget->LastEdited,
                Customer::get_group_cache_key()
            ];
            
            if (Director::isDev()) {
                $keyParts[] = uniqid();
            }

            $key = implode('_', $keyParts);
        }
        
        return $key;
    }
    
}