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
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\Filters\PartialMatchFilter;

/**
 * DataObject to handle images added to a product or sth. else.
 * Provides additional (meta-)information about the image.
 *
 * @package SilverCart
 * @subpackage Model\Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $ProductNumberToReference Product Number To Reference
 * @property string $ExternalLink             External Link
 * @property string $Title                    Title
 * @property string $Content                  Content
 * @property string $AltText                  Alt Text
 * @property string $TableIndicator           Table Indicator
 * @property string $Thumbnail                Thumbnail
 * 
 * @method Image    Image()    Returns the related Image.
 * @method SiteTree SiteTree() Returns the related SiteTree.
 * 
 * @method \SilverStripe\ORM\HasManyList ImageSliderImageTranslations() Returns the related translations.
 * 
 * @method \SilverStripe\ORM\ManyManyList ImageSliderWidgets() Returns the related ImageSliderWidgets.
 */
class ImageSliderImage extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * DB properties
     *
     * @var array
     */
    private static $db = [
        'ProductNumberToReference'  => 'Varchar(128)',
        'ExternalLink'              => 'Text',
    ];
    /**
     * Casted properties
     *
     * @var array
     */
    private static $casting = [
        'Title'             => 'Varchar',
        'Content'           => 'HTMLText',
        'AltText'           => 'Varchar',
        'TableIndicator'    => 'Text',
        'Thumbnail'         => 'HTMLText',
    ];
    /**
     * Has-one relationships.
     * 
     * @var array
     */
    private static $has_one = [
        'Image'     => Image::class,
        'SiteTree'  => SiteTree::class,
    ];
    /**
     * 1:n relationships.
     *
     * @var array
     */
    private static $has_many = [
        'ImageSliderImageTranslations' => ImageSliderImageTranslation::class,
    ];
    /**
     * Belongs-many-many relationships.
     * 
     * @var array
     */
    private static $belongs_many_many = [
        'ImageSliderWidgets' => ImageSliderWidget::class,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartImageSliderImage';
    /**
     * Marker to check whether the CMS fields are called or not
     *
     * @var bool 
     */
    protected $getCMSFieldsIsCalled = false;
    
    /**
     * getter for the Title, looks for set translation
     * 
     * @return string The Title from the translation object or an empty string
     */
    public function getTitle() : string
    {
        return (string) $this->getTranslationFieldValue('Title');
    }
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     */
    public function plural_name() : string
    {
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
    public function excludeFromScaffolding() : array
    {
        $excludeFromScaffolding = [
            'ImageSliderWidgets',
            'SortOrder'
        ];
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
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
     * getter for the AltText, looks for set translation
     * 
     * @return string
     */
    public function getAltText() : string
    {
        $altText = $this->getTranslationFieldValue('AltText');
        if (empty($altText)) {
            $altText = $this->Title;
        }
        return (string) $altText;
    }
    
    /**
     * Returns the image respecting the current translation context.
     * 
     * @return Image
     */
    public function Image() : Image
    {
        $image = $this->getComponent('Image');
        if (!$this->getCMSFieldsIsCalled) {
            $translation = $this->getTranslation();
            if (is_object($translation)
             && $translation->Image()->exists()
            ) {
                $image = $translation->Image();
            }
        }
        return $image;
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->getCMSFieldsIsCalled = true;
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $siteTreeField = TreeDropdownField::create(
                'SiteTreeID',
                $this->fieldLabel('Linkpage'),
                SiteTree::class,
                'ID',
                'Title',
                false
            );
            $productNumberToReferenceField = TextField::create('ProductNumberToReference', $this->fieldLabel('ProductNumberToReference'));
            $productNumberToReferenceField->setDescription($this->fieldLabel('ProductNumberToReferenceInfo'));
            $fields->addFieldToTab('Root.Main', $siteTreeField, 'Title');
            $fields->addFieldToTab('Root.Main', $productNumberToReferenceField, 'SiteTreeID');
            $fields->removeByName('ExternalLink');
            $fields->addFieldToTab('Root.Main', TextField::create('ExternalLink', $this->fieldLabel('ExternalLink'))->setDescription($this->fieldLabel('ExternalLinkDesc')), 'Title');
            $fields->removeByName('ImageSliderWidgets');
        });
        return DataObjectExtension::getCMSFields($this, 'Image', false);
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
            'Thumbnail'      => $this->fieldLabel('Thumbnail'),
            'Title'          => $this->fieldLabel('Title'),
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
            'ImageSliderImageTranslations.Title' => [
                'title'  => $this->fieldLabel('Title'),
                'filter' => PartialMatchFilter::class,
            ],
        ];
        return $searchableFields;
    }
    
    /**
     * Returns the linked SiteTree or Product object or NULL.
     *
     * @return SiteTree|Product|null
     */
    public function LinkedSite() : ?DataObject
    {
        $linkedSite = null;
        if (!empty($this->ProductNumberToReference)) {
            $product = Product::get()->filter('ProductNumberShop', $this->ProductNumberToReference)->first();
            if ($product instanceof Product) {
                $linkedSite = $product;
            }
        }
        if ($linkedSite === null
         && $this->SiteTreeID > 0
        ) {
            $linkedSite = $this->SiteTree();
        }
        return $linkedSite;
    }
    
    /**
     * Returns the image's link.
     * 
     * @return string|null
     */
    public function Link() : ?string
    {
        $link       = null;
        $linkedSite = $this->LinkedSite();
        if ($linkedSite === null) {
            $link = $this->ExternalLink;
        } else {
            $link = $linkedSite->Link();
        }
        return $link;
    }
    
    /**
     * Returns the link target _blank if there is only an external link set.
     * 
     * @return string|null
     */
    public function LinkTarget() : ?string
    {
        $linkTarget = null;
        $linkedSite = $this->LinkedSite();
        if ($linkedSite === null
         && !empty($this->ExternalLink)
        ) {
            $linkTarget = '_blank';
        }
        return $linkTarget;
    }
    
    /**
     * Returns the URL to a thumbnail if an image is assigned.
     *
     * @return Image_Cached
     */
    public function getThumbnail()
    {
        $result = false;
        if ($this->Image()->isInDB()) {
            $result = $this->Image()->Pad(50, 50);
        }
        return $result;
    }
}