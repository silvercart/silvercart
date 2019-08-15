<?php

namespace SilverCart\Model\Product;

use SilverCart\Dev\Tools;
use SilverCart\Model\Product\File;
use SilverStripe\ORM\DataObject;

/**
 * Translations for File.
 *
 * @package SilverCart
 * @subpackage Model_Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $Title       Title
 * @property string $Description Description
 * 
 * @method File File() Returns the related File.
 */
class FileTranslation extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = [
        'Title'       => 'Varchar',
        'Description' => 'HTMLText'
    ];
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_one = [
        'File' => File::class
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartFileTranslation';
    
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
            'Title'       => _t(Product::class . '.COLUMN_TITLE', 'Title'),
            'Description' => _t(File::class . '.DESCRIPTION', 'Description'),
            'File'        => File::singleton()->singular_name(),
        ]);
    }
}