<?php

namespace SilverCart\Model\Product;

use SilverCart\Dev\Tools;
use SilverCart\Model\Product\Image;
use SilverStripe\Assets\Image as SilverStripeImage;
use SilverStripe\ORM\DataObject;

/**
 * Translations for Image.
 *
 * @package SilverCart
 * @subpackage Model_Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $Title       Title
 * @property string $Content     Content
 * @property string $Description Description
 * 
 * @method Image Image() Returns the related Image.
 */
class ImageTranslation extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = [
        'Title'       => 'Varchar',
        'Content'     => 'HTMLText',
        'Description' => 'HTMLText'
    ];
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_one = [
        'ImageFile' => SilverStripeImage::class,
        'Image'     => Image::class,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartImageTranslation';
    
    /**
     * Returns the translated singular name.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this); 
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
            'Image'       => Image::singleton()->singular_name(),
            'Title'       => _t(Product::class . '.COLUMN_TITLE', 'Title'),
            'Content'     => _t(Image::class . '.CONTENT', 'Content'),
            'Description' => _t(Image::class . '.DESCRIPTION', 'Description'),
        ]);
    }
}