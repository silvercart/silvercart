<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Admin\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverCart\Admin\Forms\ImageUploadField;
use SilverCart\Admin\Model\Config;
use SilverCart\Model\Product\Image;
use SilverCart\Model\Translation\TranslationTools;
use SilverCart\Model\Widgets\ImageSliderImage;
use SilverCart\Model\Widgets\ImageSliderWidgetTranslation;
use SilverCart\Model\Widgets\Widget;
use SilverCart\Model\Widgets\WidgetTools;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\ORM\ArrayList;

/**
 * Provides an image slider powered by AnythingSlider.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ImageSliderWidget extends Widget {
    
    /**
     * Attributes.
     * 
     * @var array
     */
    private static $db = array(
        'Autoplay'         => 'Boolean(1)',
        'autoPlayDelayed'  => 'Boolean(1)',
        'autoPlayLocked'   => 'Boolean(0)',
        'buildArrows'      => 'Boolean(1)',
        'buildNavigation'  => 'Boolean(1)',
        'buildStartStop'   => 'Boolean(1)',
        'slideDelay'       => 'Int',
        'stopAtEnd'        => 'Boolean(0)',
        'transitionEffect' => "Enum('fade,horizontalSlide,verticalSlide','fade')",
        'useSlider'        => "Boolean(0)"
    );
    
    /**
     * Casted Attributes.
     * 
     * @var array
     */
    private static $casting = array(
        'FrontTitle'   => 'Varchar(255)',
        'FrontContent' => 'Text'
    );
    
    /**
     * 1:n relationships.
     *
     * @var array
     */
    private static $has_many = array(
        'ImageSliderWidgetTranslations' => ImageSliderWidgetTranslation::class,
    );
    
    /**
     * Many-many relationships
     *
     * @var array
     */
    private static $many_many = array(
        'slideImages' => ImageSliderImage::class,
    );

    /**
     * Has_many relationships.
     *
     * @var array
     */
    private static $many_many_extraFields = array(
        'slideImages' => array(
            'Sort' => 'Int',
        ),
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartImageSliderWidget';
    
    /**
     * Getter for the multilingula FrontTitle
     *
     * @return string
     */
    public function getFrontTitle() {
        return $this->getTranslationFieldValue('FrontTitle');
    }
    
    /**
     * Getter for the multilingula FrontContent
     *
     * @return string
     */
    public function getFrontContent() {
        return $this->getTranslationFieldValue('FrontContent');
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = DataObjectExtension::getCMSFields($this, 'ExtraCssClasses', false);
        
        $slideImagesTable = new GridField(
                'slideImages',
                $this->fieldLabel('slideImages'),
                $this->slideImages(),
                GridFieldConfig_RelationEditor::create()
        );
        
        $slideImagesTable->getConfig()->removeComponentsByType('GridFieldAddNewButton');
        $slideImagesTable->getConfig()->removeComponentsByType('GridFieldAddExistingAutocompleter');
        $slideImagesTable->getConfig()->addComponent(new GridFieldDeleteAction());
        if (class_exists('\UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows')) {
            $slideImagesTable->getConfig()->addComponent(new \UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows('Sort'));
        }
        
        $slideImagesUploadField = new ImageUploadField('UploadslideImages', $this->fieldLabel('AddImage'));
        $slideImagesUploadField->setFolderName('assets/slider-images');
        $slideImagesUploadField->setRelationClassName(ImageSliderImage::class);
        
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
     */
    public function getCMSFieldsSliderTab(&$rootTabSet) {
        WidgetTools::getCMSFieldsSliderToggleForSliderWidget($this, $rootTabSet);
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
                WidgetTools::fieldLabelsForProductSliderWidget($this),
                array(
                    'ImageSliderWidgetTranslations' => _t(TranslationTools::class . '.TRANSLATIONS', 'Translations'),
                    'Images'                        => Image::singleton()->plural_name(),
                    'ImageSliderImage'              => ImageSliderImage::singleton()->plural_name(),
                    'slideImages'                   => _t(ProductSliderWidget::class . '.CMS_SLIDERIMAGES', 'Slideshow images'),
                    'Translations'                  => _t(Config::class . '.TRANSLATIONS', 'Translations'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Returns the images to display
     *
     * @return ArrayList
     */
    public function getSlideImages() {
        $imagesToDisplay = new ArrayList();

        foreach ($this->slideImages()->sort('Sort') as $image) {
            if ($image->Image()->exists()) {
                $imagesToDisplay->push($image);
            }
        }

        return $imagesToDisplay;
    }
}