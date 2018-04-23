<?php

namespace SilverCart\Admin\Forms;

use SilverCart\Admin\Forms\FileUploadField;

/**
 * Same as SilverCart\Admin\Forms\FileUploadField but uses SilverCart\Model\Product\Image instead of 
 * SilverCart\Model\Product\File.
 *
 * @package SilverCart
 * @subpackage Admin_Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 25.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ImageUploadField extends FileUploadField {
    
    /**
     * Class name of the file object
     *
     * @var string
     */
    protected $fileClassName = 'Image';
    
    /**
     * Class name of the relation object
     *
     * @var string
     */
    protected $relationClassName = \SilverCart\Model\Product\Image::class;

}
