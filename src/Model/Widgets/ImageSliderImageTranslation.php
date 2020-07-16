<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Product\Image;
use SilverCart\Model\Translation\TranslationTools;
use SilverCart\Model\Widgets\ImageSliderImage;
use SilverStripe\Assets\Image as SilverStripeImage;
use SilverStripe\ORM\DataObject;

/**
 * Translations for ImageSliderImage.
 *
 * @package SilverCart
 * @subpackage Model\Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ImageSliderImageTranslation extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = [
        'Title'     => 'Varchar',
        'Content'   => 'HTMLText',
        'AltText'   => 'Varchar(256)',
    ];
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_one = [
        'Image'            => SilverStripeImage::class,
        'ImageSliderImage' => ImageSliderImage::class,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartImageSliderImageTranslation';
    
    /**
     * Returns the translated singular name of the object.
     * 
     * @return string 
     */
    public function singular_name() : string
    {
        return TranslationTools::singular_name();
    }


    /**
     * Returns the translated plural name of the object.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return TranslationTools::plural_name();
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
            'ImageSliderImage' => ImageSliderImage::singleton()->singular_name(),
            'AltText'          => ImageSliderImage::singleton()->fieldLabel('AltText'),
            'Title'            => Image::singleton()->fieldLabel('Title'),
            'Content'          => Image::singleton()->fieldLabel('Content'), 
        ]);
    }
}