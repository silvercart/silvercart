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
use SilverStripe\Assets\Image as SilverStripeImage;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\Filters\PartialMatchFilter;
use SilverStripe\ORM\HasManyList;

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
 * 
 * @property string $ProductNumberToReference Product number of the product to reference to
 * @property int    $SortOrder                Sort order
 * 
 * @property string $Title       Title (current locale context)
 * @property string $Content     Content (current locale context)
 * @property string $Description Description (current locale context)
 * 
 * @method Product           Product()       Returns the related Product.
 * @method PaymentMethod     PaymentMethod() Returns the related Payment Method.
 * 
 * @method HasManyList ImageTranslations() Returns a list of translations for this image.
 */
class Image extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * DB properties
     *
     * @var array
     */
    private static $db = [
        'ProductNumberToReference' => 'Varchar(128)',
        'SortOrder'                => 'Int',
    ];
    /**
     * Has one relations
     *
     * @var array
     */
    private static $has_one = [
        'Product'       => Product::class,
        'PaymentMethod' => PaymentMethod::class,
        'Image'         => SilverStripeImage::class,
    ];
    /**
     * 1:n relationships.
     *
     * @var array
     */
    private static $has_many = [
        'ImageTranslations' => ImageTranslation::class
    ];
    /**
     * Belongs many many relations.
     *
     * @var array
     */
    private static $belongs_many_many = [
        'SlidorionProductGroupWidgets' => SlidorionProductGroupWidget::class,
    ];
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
    private static $casting = [
        'Title'       => 'Varchar',
        'Content'     => 'HTMLText',
        'Description' => 'HTMLText',
    ];
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
     * Marker to check whether the CMS fields are called or not
     *
     * @var bool 
     */
    protected $getCMSFieldsIsCalled = false;

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
    public function __construct($record = null, $isSingleton = false)
    {
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
    public function getTitle()
    {
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
    public function getContent()
    {
        return $this->getTranslationFieldValue('Content');
    }
    
    /**
     * getter for the description, looks for set translation
     * 
     * @return string The description from the translation object or an empty string
     */
    public function getDescription()
    {
        return $this->getTranslationFieldValue('Description');
    }
    
    /**
     * Returns the image respecting the current translation context.
     * 
     * @return Image
     */
    public function Image() : SilverStripeImage
    {
        $image = $this->getComponent('Image');
        if (!$this->getCMSFieldsIsCalled) {
            $translation = $this->getTranslation();
            if (is_object($translation)
             && $translation->ImageFile()->exists()
            ) {
                $image = $translation->ImageFile();
            }
        }
        return $image;
    }
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this);
    }
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->getCMSFieldsIsCalled = true;
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $fields->removeByName('ProductID');
            $fields->removeByName('PaymentMethodID');
            $controller = Controller::curr();
            if ($controller instanceof ProductAdmin
             || $controller instanceof PaymentMethodAdmin
            ) {
                $fields->removeByName('Content');
                $fields->removeByName('Description');
            }
            $fields->removeByName('SlidorionProductGroupWidgets');
            $fields->addFieldToTab('Root.Main', TextField::create('ProductNumberToReference', $this->fieldLabel('ProductNumberToReference')));
        });
        return DataObjectExtension::getCMSFields($this);
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'ImageTranslations'            => ImageTranslation::singleton()->plural_name(),
            'PaymentMethod'                => PaymentMethod::singleton()->singular_name(),
            'Product'                      => Product::singleton()->singular_name(),
            'Thumbnail'                    => _t(Image::class . '.THUMBNAIL', 'Preview'),
            'Title'                        => _t(Image::class . '.TITLE', 'Display name'),
            'Content'                      => _t(Image::class . '.CONTENT', 'Text content'),
            'Description'                  => _t(Image::class . '.DESCRIPTION', 'Description (e.g. for Slidorion textfield)'),
            'SortOrder'                    => _t(Image::class . '.SORTORDER', 'Sort order'),
            'Image'                        => SilverStripeImage::singleton()->singular_name(),
            'ProductNumberToReference'     => _t(ImageSliderImage::class . '.ProductNumberToReference', 'Productnumber of the product to link to'),
            'ProductNumberToReferenceInfo' => _t(ImageSliderImage::class . '.ProductNumberToReferenceInfo', 'Will be used instead the page.'),
        ]);
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     */
    public function summaryFields() : array
    {
        $summaryFields = [
            'Image.ImageThumbnail' => $this->fieldLabel('Thumbnail'),
            'Title'                => $this->fieldLabel('Title')
        ];
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Searchable fields definition
     *
     * @return array
     */
    public function searchableFields() : array
    {
        $searchableFields = [
            'ImageTranslations.Title' => [
                'title'  => $this->fieldLabel('Title'),
                'filter' => PartialMatchFilter::class,
            ]
        ];
            
        return $searchableFields;
    }
    
    /**
     * Returns a HTML snippet for the related Files icon.
     *
     * @return string
     */
    public function getFileIcon() : string
    {
        return "<img src=\"{$this->Image()->Icon()}\" alt=\"{$this->Image()->FileType}\" title=\"{$this->Image()->Title}\" />";
    }
    
    /**
     * Returns the products link
     *
     * @return string
     */
    public function getProductLink() : string
    {
        $link = "";
        if ($this->Product()->exists()) {
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
    public function isEmptyObject() : bool
    {
        $result = false;
        if (!$this->Image()->exists()
         && $this->isEmptyMultilingualAttributes()
        ) {
            $result = true;
        }
        return $result;
    }
    
    /**
     * Deletes the related SilverStripe Image before deleting $this.
     *
     * @return void 
     */
    public function onBeforeDelete() : void
    {
        parent::onBeforeDelete();
        $image = $this->Image();
        if ($image instanceof SilverStripeImage
         && $image->exists()
        ) {
            $image->deleteFile();
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
    protected function onBeforeWrite() : void
    {
        parent::onBeforeWrite();
        if ($this->Product()->exists()
         && empty($this->Title)
        ) {
            $this->Title = $this->Product()->Title;
        }
    }
    
    /**
     * Returns the link.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.06.2014
     */
    public function Link() : string
    {
        if (is_null($this->link)) {
            $this->link = "";
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