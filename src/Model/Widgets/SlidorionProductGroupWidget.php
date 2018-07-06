<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Dev\Tools;
use SilverCart\Admin\Forms\GridField\GridFieldConfig_ExclusiveRelationEditor;
use SilverCart\Admin\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverCart\Admin\Forms\ImageUploadField;
use SilverCart\Admin\Model\Config;
use SilverCart\Model\Product\Image;
use SilverCart\Model\Widgets\SlidorionProductGroupWidgetTranslation;
use SilverCart\Model\Widgets\Widget;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\ArrayList;

/**
 * Provides a slidorion box for product groups.
 * See "http://www.slidorion.com/".
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SlidorionProductGroupWidget extends Widget {
    
    /**
     * Attributes
     *
     * @var array
     */
    private static $db = [
        'widgetHeight' => 'Int',
        'speed'        => 'Int',
        'interval'     => 'Int',
        'hoverPause'   => 'Boolean',
        'autoPlay'     => 'Boolean',
        'effect'       => "Enum('fade,slideLeft,slideRight,slideUp,slideDown,overLeft,overRight,overUp,overDown', 'fade')"
    ];
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_many = [
        'SlidorionProductGroupWidgetTranslations' => SlidorionProductGroupWidgetTranslation::class,
    ];
    
    /**
     * Has_many relationships.
     *
     * @var array
     */
    private static $many_many = [
        'Images' => Image::class,
    ];
    
    /**
     * Has_many relationships.
     *
     * @var array
     */
    private static $many_many_extraFields = [
        'Images' => [
            'Sort' => 'Int',
        ],
    ];
    
    /**
     * Default attributes
     *
     * @var array
     */
    private static $defaults = [
        'widgetHeight' => 400,
        'speed'        => 500,
        'interval'     => 5000,
        'autoPlay'     => true,
    ];
    
    /**
     * Castings.
     *
     * @var array
     */
    private static $casting = [
        'FrontTitle'   => 'Varchar(255)',
        'FrontContent' => 'Text',
    ];

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartSlidorionProductGroupWidget';
    
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
                [
                    'ImagesDescription' => _t(SlidorionProductGroupWidget::class . '.ImagesDescription', '<strong>Caution:</strong> Optimal dimensions to display an image are <strong><u>426x%s pixels</u></strong>.'),
                    'BasicTab'          => _t(SlidorionProductGroupWidget::class . '.CMS_BASICTABNAME', 'Basic preferences'),
                    'AdvancedTab'       => _t(SlidorionProductGroupWidget::class . '.CMS_ADVANCEDTABNAME', 'Advanced preferences'),
                    'TranslationsTab'   => _t(Config::class . '.TRANSLATIONS', 'Translations'),
                    'FrontTitle'        => _t(SlidorionProductGroupWidget::class . '.FRONT_TITLE', 'Title'),
                    'FrontContent'      => _t(SlidorionProductGroupWidget::class . '.FRONT_CONTENT', 'Content'),
                    'widgetHeight'      => _t(SlidorionProductGroupWidget::class . '.WIDGET_HEIGHT', 'Height of the widget (in pixels)'),
                    'speed'             => _t(SlidorionProductGroupWidget::class . '.SPEED', 'Animation speed'),
                    'interval'          => _t(SlidorionProductGroupWidget::class . '.INTERVAL', 'Interval for transitions'),
                    'hoverPause'        => _t(SlidorionProductGroupWidget::class . '.HOVERPAUSE', 'Pause on hover'),
                    'autoPlay'          => _t(SlidorionProductGroupWidget::class . '.AUTOPLAY', 'Start playing automatically'),
                    'effect'            => _t(SlidorionProductGroupWidget::class . '.EFFECT', 'Type of effect'),
                    'translations'      => _t(Config::class . '.TRANSLATIONS', 'Translations'),
                    'Images'            => Image::singleton()->plural_name(),
                    'AddImage'          => _t(ProductSliderWidget::class . '.AddImage', 'Add Image'),
                ]
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns the input fields for this widget without scaffolding
     * 
     * @return FieldList
     */
    public function getCMSFields() {
        $fields         = FieldList::create();
        $rootTabSet     = TabSet::create('Root');
        $basicTab       = Tab::create('Basic', $this->fieldLabel('BasicTab'));
        $translationTab = Tab::create('Translations', $this->fieldLabel('TranslationsTab'));
        
        $titleField      = TextField::create('Title',                    $this->fieldLabel('Title'));
        $frontTitleField = TextField::create('FrontTitle',               $this->fieldLabel('FrontTitle'));
        $contentField    = TextareaField::create('FrontContent',         $this->fieldLabel('FrontContent'), 10);
        
        $imageTable = GridField::create(
                'Images',
                $this->fieldLabel('Images'),
                $this->Images()->sort('Sort'),
                GridFieldConfig_RelationEditor::create()
        );
        
        $imageTable->getConfig()->removeComponentsByType('GridFieldAddNewButton');
        $imageTable->getConfig()->removeComponentsByType('GridFieldAddExistingAutocompleter');
        $imageTable->getConfig()->addComponent(new GridFieldDeleteAction());
        if (class_exists('GridFieldSortableRows')) {
            $imageTable->getConfig()->addComponent(new GridFieldSortableRows('Sort'));
        }
        
        $imagesUploadDescription = sprintf(
                $this->fieldLabel('ImagesDescription'),
                $this->getSliderHeight()
        );
        
        $imagesUploadField = ImageUploadField::create('UploadImages', $this->fieldLabel('AddImage'));
        $imagesUploadField->setFolderName('assets/silvercart-images');
        $imagesUploadField->setDescription($imagesUploadDescription);
                
        $translationsTableField = GridField::create(
                'SlidorionProductGroupWidgetTranslations',
                $this->fieldLabel('translations'),
                $this->SlidorionProductGroupWidgetTranslations(),
                GridFieldConfig_ExclusiveRelationEditor::create()
        );
        
        $widgetHeightField = TextField::create('widgetHeight',   $this->fieldLabel('widgetHeight'));
        $speedField        = TextField::create('speed',          $this->fieldLabel('speed'));
        $intervalField     = TextField::create('interval',       $this->fieldLabel('interval'));
        $effectField       = DropdownField::create('effect',     $this->fieldLabel('effect'), SlidorionProductGroupWidget::singleton()->dbObject('effect')->enumValues(), $this->effect);
        $hoverPauseField   = CheckboxField::create('hoverPause', $this->fieldLabel('hoverPause'));
        $autoPlayField     = CheckboxField::create('autoPlay',   $this->fieldLabel('autoPlay'));

        $advancedToggle = ToggleCompositeField::create(
                'AdvancedToggle',
                $this->fieldLabel('AdvancedTab'),
                [
                    $widgetHeightField,
                    $speedField,
                    $intervalField,
                    $effectField,
                    $hoverPauseField,
                    $autoPlayField,
                ]
        )->setHeadingLevel(4);
        
        $basicTab->push($titleField);
        $basicTab->push($frontTitleField);
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
     */
    public function getFrontTitle() {
        return $this->getTranslationFieldValue('FrontTitle');
    }
    
    /**
     * Getter for the FrontContent depending on the set language
     *
     * @return string
     */
    public function getFrontContent() {
        return $this->getTranslationFieldValue('FrontContent');
    }

    /**
     * Returns the widget height.
     *
     * @return int
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
     */
    public function getEffectValue() {
        $effect = 'fade';

        if (!empty($this->effect)) {
            $effect = $this->effect;
        }

        return $effect;
    }
    
    /**
     * Returns the images to display
     * 
     * @return ArrayList
     */
    public function getImagesToDisplay() {
        $imagesToDisplay = ArrayList::create();

        foreach ($this->Images()->sort('Sort') as $silvercartImage) {
            if ($silvercartImage->ImageID > 0) {
                $image          = $silvercartImage->Image();
                $resizedImage   = $image->Pad(self::$image_width, $this->getSliderHeight());
                if ($resizedImage) {
                    $silvercartImage->resizedImage = $resizedImage;
                    $imagesToDisplay->push($silvercartImage);
                }
            }
        }

        return $imagesToDisplay;
    }

    /**
     * Returns the slider height.
     *
     * @return int
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