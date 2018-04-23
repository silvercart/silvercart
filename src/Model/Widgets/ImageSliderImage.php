<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Dev\Tools;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Widgets\Widget;
use SilverCart\Model\Widgets\ImageSliderImageTranslation;
use SilverCart\Model\Widgets\ImageSliderWidget;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Assets\Image;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\Filters\PartialMatchFilter;

/**
 * DataObject to handle images added to a product or sth. else.
 * Provides additional (meta-)information about the image.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ImageSliderImage extends DataObject {
    
    /**
     * DB properties
     *
     * @var array
     */
    private static $db = array(
        'ProductNumberToReference'  => 'Varchar(128)',
    );
    
    /**
     * Casted properties
     *
     * @var array
     */
    private static $casting = array(
        'Title'             => 'Varchar',
        'Content'           => 'HTMLText',
        'AltText'           => 'Varchar',
        'TableIndicator'    => 'Text',
        'Thumbnail'         => 'HTMLText',
    );
    
    /**
     * Has-one relationships.
     * 
     * @var array
     */
    private static $has_one = array(
        'Image'     => Image::class,
        'SiteTree'  => SiteTree::class,
    );
    
    /**
     * 1:n relationships.
     *
     * @var array
     */
    private static $has_many = array(
        'ImageSliderImageTranslations' => ImageSliderImageTranslation::class,
    );
    
    /**
     * Belongs-many-many relationships.
     * 
     * @var array
     */
    private static $belongs_many_many = array(
        'ImageSliderWidgets' => ImageSliderWidget::class,
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartImageSliderImage';
    
    /**
     * getter for the Title, looks for set translation
     * 
     * @return string The Title from the translation object or an empty string
     */
    public function getTitle() {
        return $this->getTranslationFieldValue('Title');
    }
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.03.2014
     */
    public function singular_name() {
        return Tools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.03.2014
     */
    public function plural_name() {
        return Tools::plural_name_for($this);
    }
    
    /**
     * Returns an array of field/relation names (db, has_one, has_many, 
     * many_many, belongs_many_many) to exclude from form scaffolding in
     * backend.
     * This is a performance friendly way to exclude fields.
     * 
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 10.02.2013
     */
    public function excludeFromScaffolding() {
        $excludeFromScaffolding = array(
            'ImageSliderWidgets',
            'SortOrder'
        );
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }
    
    /**
     * getter for the content, looks for set translation
     * 
     * @return string The content from the translation object or an empty string
     */
    public function getContent() {
        return $this->getTranslationFieldValue('Content');
    }
    
    /**
     * getter for the AltText, looks for set translation
     * 
     * @return string
     */
    public function getAltText() {
        $altText = $this->getTranslationFieldValue('AltText');
        if (empty($altText)) {
            $altText = $this->Title;
        }
        return $altText;
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = DataObjectExtension::getCMSFields($this, 'Image', false);
        $siteTreeField = new TreeDropdownField(
            'SiteTreeID',
            $this->fieldLabel('Linkpage'),
            SiteTree::class,
            'ID',
            'Title',
            false
        );
        $productNumberToReferenceField = new TextField('ProductNumberToReference', $this->fieldLabel('ProductNumberToReference'));
        $productNumberToReferenceField->setDescription($this->fieldLabel('ProductNumberToReferenceInfo'));
        $fields->addFieldToTab('Root.Main', $siteTreeField, 'Title');
        $fields->addFieldToTab('Root.Main', $productNumberToReferenceField, 'SiteTreeID');
        $fields->removeByName('ImageSliderWidgets');
        
        return $fields;
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 06.03.2014
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'AltText'                            => _t(ImageSliderImage::class . '.AltText', 'Text for ALT-Tag'),
                'ImageSliderImageTranslations'       => ImageSliderImageTranslation::singleton()->plural_name(),
                'Image'                              => Image::singleton()->singular_name(),
                'Linkpage'                           => _t(ImageSliderImage::class . '.LINKPAGE', 'Page that shall be linked to'),
                'ProductNumberToReference'           => _t(ImageSliderImage::class . '.ProductNumberToReference', 'Productnumber of the product to link to'),
                'ProductNumberToReferenceInfo'       => _t(ImageSliderImage::class . '.ProductNumberToReferenceInfo', 'Will be used instead the page.'),
                'SortOrder'                          => _t(Widget::class . '.SORT_ORDER_LABEL', 'Sort order'),
                'Thumbnail'                          => \SilverCart\Model\Product\Image::singleton()->fieldLabel('Thumbnail'),
                'Title'                              => \SilverCart\Model\Product\Image::singleton()->fieldLabel('Title'),
                'ImageSliderImageTranslations.Title' => \SilverCart\Model\Product\Image::singleton()->fieldLabel('Title'),
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.01.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'Thumbnail'      => $this->fieldLabel('Thumbnail'),
            'Title'          => $this->fieldLabel('Title'),
        );


        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Searchable fields definition
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.03.2014
     */
    public function searchableFields() {
        $searchableFields = array(
            'ImageSliderImageTranslations.Title' => array(
                'title'  => $this->fieldLabel('Title'),
                'filter' => PartialMatchFilter::class,
            )
        );
            
        return $searchableFields;
    }
    
    /**
     * Returns the linked SiteTree object.
     *
     * @return mixed SiteTree|boolean false
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.06.2014
     */
    public function LinkedSite() {
        $linkedSite = false;
        if (!empty($this->ProductNumberToReference)) {
            $product = Product::get()->filter('ProductNumberShop', $this->ProductNumberToReference)->first();
            if ($product instanceof Product) {
                $linkedSite = $product;
            }
        }
        if ($linkedSite == false &&
            $this->SiteTreeID > 0) {
            $linkedSite = $this->SiteTree();
        }
        
        return $linkedSite;
    }
    
    /**
     * Returns the URL to a thumbnail if an image is assigned.
     *
     * @return Image_Cached
     */
    public function getThumbnail() {
        $result = false;

        if ($this->Image()->isInDB()) {
            $result = $this->Image()->Pad(50, 50);
        }
        return $result;
    }
}