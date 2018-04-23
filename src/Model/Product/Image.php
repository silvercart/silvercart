<?php

namespace SilverCart\Model\Product;

use SilverCart\Admin\Controllers\ProductAdmin;
use SilverCart\Admin\Controllers\PaymentMethodAdmin;
use SilverCart\Dev\Tools;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Product\ImageTranslation;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Widgets\ImageSliderImage;
use SilverCart\Model\Widgets\SlidorionProductGroupWidget;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\Filters\PartialMatchFilter;

/**
 * DataObject to handle images added to a product or sth. else.
 * Provides additional (meta-)information about the image.
 *
 * @package SilverCart
 * @subpackage Model_Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Image extends DataObject {
    
    /**
     * DB properties
     *
     * @var array
     */
    private static $db = array(
        'ProductNumberToReference'  => 'Varchar(128)',
        'SortOrder'                 => 'Int',
    );

    /**
     * Has one relations
     *
     * @var array
     */
    private static $has_one = array(
        'Product'       => Product::class,
        'PaymentMethod' => PaymentMethod::class,
        'Image'         => \SilverStripe\Assets\Image::class,
    );
    
    /**
     * 1:n relationships.
     *
     * @var array
     */
    private static $has_many = array(
        'ImageTranslations' => ImageTranslation::class
    );
    
    /**
     * Belongs many many relations.
     *
     * @var array
     */
    private static $belongs_many_many = array(
        'SlidorionProductGroupWidgets' => SlidorionProductGroupWidget::class,
    );
    
    /**
     * Default sort field and direction.
     *
     * @var string
     */
    private static $default_sort = 'SortOrder ASC';

    /**
     * Casted properties
     *
     * @var array
     */
    private static $casting = array(
        'Title'          => 'Varchar',
        'Content'        => 'HTMLText',
        'Description'    => 'HTMLText',
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartImage';
    
    /**
     * Link
     *
     * @var string
     */
    protected $link = null;

    /**
     * Constructor. Overwrites some basic attributes.
     *
     * @param array $record      Record to fill Object with
     * @param bool  $isSingleton Is this a singleton?
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2012
     */
    public function __construct($record = null, $isSingleton = false) {
        parent::__construct($record, $isSingleton);
        if ($this->Image()->exists()) {
            $this->Image()->Title = $this->Title;
        }
    }
    
    /**
     * getter for the Title, looks for set translation
     * 
     * @return string The Title from the translation object or an empty string
     */
    public function getTitle() {
        $title = $this->getTranslationFieldValue('Title');
        if ($this->Product()->ID &&
            empty($title)) {
            $title = $this->Product()->Title;
        }
        return $title;
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
     * getter for the description, looks for set translation
     * 
     * @return string The description from the translation object or an empty string
     */
    public function getDescription() {
        return $this->getTranslationFieldValue('Description');
    }
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.05.2012
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.05.2012
     */
    public function plural_name() {
        return Tools::plural_name_for($this);
    }
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     */
    public function getCMSFields() {
        $fields = DataObjectExtension::getCMSFields($this);
        $fields->removeByName('ProductID');
        $fields->removeByName('PaymentMethodID');
        
        $controller = Controller::curr();
        if ($controller instanceof ProductAdmin ||
            $controller instanceof PaymentMethodAdmin) {
            $fields->removeByName('Content');
            $fields->removeByName('Description');
        }
        $fields->removeByName('SlidorionProductGroupWidgets');
        
        $fields->addFieldToTab('Root.Main', new TextField('ProductNumberToReference', $this->fieldLabel('ProductNumberToReference')));
        
        return $fields;
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.03.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'ImageTranslations'            => ImageTranslation::singleton()->plural_name(),
                'PaymentMethod'                => PaymentMethod::singleton()->singular_name(),
                'Product'                      => Product::singleton()->singular_name(),
                'Thumbnail'                    => _t(Image::class . '.THUMBNAIL', 'Preview'),
                'Title'                        => _t(Image::class . '.TITLE', 'Display name'),
                'Content'                      => _t(Image::class . '.CONTENT', 'Text content'),
                'Description'                  => _t(Image::class . '.DESCRIPTION', 'Description (e.g. for Slidorion textfield)'),
                'SortOrder'                    => _t(Image::class . '.SORTORDER', 'Sort order'),
                'Image'                        => \SilverStripe\Assets\Image::singleton()->singular_name(),
                'ProductNumberToReference'     => _t(ImageSliderImage::class . '.ProductNumberToReference', 'Productnumber of the product to link to'),
                'ProductNumberToReferenceInfo' => _t(ImageSliderImage::class . '.ProductNumberToReferenceInfo', 'Will be used instead the page.'),
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.05.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'Image.ImageThumbnail' => $this->fieldLabel('Thumbnail'),
            'Title'                => $this->fieldLabel('Title')
        );

        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Searchable fields definition
     *
     * @return array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 31.05.2012
     */
    public function searchableFields() {
        $searchableFields = array(
            'ImageTranslations.Title' => array(
                'title'  => $this->fieldLabel('Title'),
                'filter' => PartialMatchFilter::class,
            )
        );
            
        return $searchableFields;
    }
    
    /**
     * Returns a HTML snippet for the related Files icon.
     *
     * @return string
     */
    public function getFileIcon() {
        return '<img src="' . $this->Image()->Icon() . '" alt="' . $this->Image()->FileType . '" title="' . $this->Image()->Title . '" />';
    }
    
    /**
     * Returns the products link
     *
     * @return string
     */
    public function getProductLink() {
        $link = "";
        if ($this->ProductID) {
            $link = $this->Product()->Link();
        }
        return $link;
    }
    
    /**
     * Was the object just accidently written?
     * object without attribute or file appended
     *
     * @return bool $result
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 14.07.2012
     */
    public function isEmptyObject() {
        $result = false;
        if ($this->ImageID == 0 &&
            $this->isEmptyMultilingualAttributes()) {
            $result = true;
        }
        return $result;
    }
    
    /**
     * hook
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.07.2012
     */
    public function onBeforeDelete() {
        parent::onBeforeDelete();
        $image = $this->Image();

        if ($image &&
            $image->ID > 0) {
            $image->delete();
        }
    }
    
    /**
     * On before write hook.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.03.2013
     */
    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        if ($this->Product()->exists() &&
            empty($this->Title)) {
            $this->Title = $this->Product()->Title;
        }
    }
    
    /**
     * Returns the link.
     *
     * @return mixed SiteTree|boolean false
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.06.2014
     */
    public function Link() {
        if (is_null($this->link)) {
            $this->link = false;
            if (!empty($this->ProductNumberToReference)) {
                $product = Product::get()->filter('ProductNumberShop', $this->ProductNumberToReference)->first();
                if ($product instanceof Product) {
                    $this->link = $product->Link();
                }
            }
        }
        
        return $this->link;
    }
    
}