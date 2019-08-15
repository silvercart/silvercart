<?php

namespace SilverCart\Model\Product;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\DownloadPage;
use SilverCart\Model\Product\FileTranslation;
use SilverCart\Model\Product\Product;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Assets\File as SilverstripeFile;
use SilverStripe\Assets\Image as SilverstripeImage;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\HasManyList;

/**
 * DataObject to handle files added to a product or sth. else.
 * Provides additional (meta-)information about the file.
 * It's used to add PDF datasheets or other files.
 *
 * @package SilverCart
 * @subpackage Model_Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $Title       Title (current locale context)
 * @property string $Description Description (current locale context)
 * 
 * @method Product           Product()      Returns the related product.
 * @method SilverstripeFile  File()         Returns the related file.
 * @method DownloadPage      DownloadPage() Returns the related DownloadPage.
 * @method SilverstripeImage Thumbnail()    Returns the related thumbnail.
 * 
 * @method HasManyList FileTranslations() Returns a list of translations for this file.
 */
class File extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_one = [
        'Product'      => Product::class,
        'File'         => SilverstripeFile::class,
        'DownloadPage' => DownloadPage::class,
        'Thumbnail'    => SilverstripeImage::class,
    ];
    /**
     * Castings
     *
     * @var array
     */
    private static $casting = [
        'Title'             => 'Varchar',
        'Description'       => 'HTMLText',
        'FileIcon'          => 'HTMLText',
    ];
    /**
     * 1:n relationships.
     *
     * @var array
     */
    private static $has_many = [
        'FileTranslations' => FileTranslation::class,
    ];

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartFile';
    
    /**
     * getter for the Title, looks for set translation
     * 
     * @return string The Title from the translation object or an empty string
     */
    public function getTitle()
    {
        return $this->getTranslationFieldValue('Title');
    }
    
    /**
     * getter for the Description, looks for set translation
     * 
     * @return string The description from the translation object or an empty string
     */
    public function getDescription()
    {
        return $this->getTranslationFieldValue('Description');
    }

    /**
     * Returns a HTML snippet for the related Files icon.
     *
     * @return string
     */
    public function getFileIconURL() : ?string
    {
        $fileName = $this->File()->getFilename();
        $fileExt  = pathinfo($fileName, PATHINFO_EXTENSION);
        return SilverstripeFile::get_icon_for_extension($fileExt);
    }

    /**
     * Returns a HTML snippet for the related Files icon.
     *
     * @return DBHTMLText
     */
    public function getFileIcon() : DBHTMLText
    {
        return Tools::string2html("<img src=\"{$this->getFileIconURL()}\" alt=\"{$this->File()->FileType}\" title=\"{$this->File()->Title}\" />");
    }
    
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
            'Title'            => _t(File::class . '.TITLE', 'Display name'),
            'FileIcon'         => _t(File::class . '.FILEICON', 'File icon'),
            'FileType'         => _t(File::class . '.TYPE', 'File type'),
            'FileTranslations' => FileTranslation::singleton()->plural_name(),
            'Product'          => Product::singleton()->singular_name(),
            'File'             => SilverstripeFile::singleton()->singular_name(),
            'DownloadPage'     => DownloadPage::singleton()->singular_name(),
            'Description'      => _t(File::class . '.DESCRIPTION', 'Description (e.g. for Slidorion textfield)'),
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
            'FileIcon'      => $this->fieldLabel('FileIcon'),
            'File.FileType' => $this->fieldLabel('FileType'),
            'Title'         => $this->fieldLabel('Title'),
        ];
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $fields->removeByName('ProductID');
            $fields->removeByName('DownloadPageID');
        });
        return DataObjectExtension::getCMSFields($this);
    }
    
    /**
     * Was the object just accidently written?
     * object without attribute or file appended
     *
     * @return bool
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 14.07.2012
     */
    public function isEmptyObject() : bool
    {
        $result = false;
        if ($this->FileID == 0
         && $this->isEmptyMultilingualAttributes()
        ) {
            $result = true;
        }
        return $result;
    }
    
    /**
     * Deletes the related file before deleting this object.
     *
     * @return void 
     */
    public function onBeforeDelete() : void
    {
        parent::onBeforeDelete();
        if ($this->File()->exists()) {
            $this->File()->delete();
        }
    }
    
    /**
     * Sets the related file's title as title if no custom title is set before 
     * writing.
     * 
     * @return void
     */
    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        if ($this->File()->exists()
         && empty($this->Title)
        ) {
            $this->Title = $this->File()->Title;
        }
    }
}