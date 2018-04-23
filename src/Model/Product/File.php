<?php

namespace SilverCart\Model\Product;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\DownloadPage;
use SilverCart\Model\Product\FileTranslation;
use SilverCart\Model\Product\Product;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Control\Director;
use SilverStripe\ORM\DataObject;

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
 */
class File extends DataObject {

    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_one = array(
        'Product'      => Product::class,
        'File'         => \SilverStripe\Assets\File::class,
        'DownloadPage' => DownloadPage::class,
        'Thumbnail'    => \SilverStripe\Assets\Image::class,
    );
    
    /**
     * Castings
     *
     * @var array
     */
    private static $casting = array(
        'Title'             => 'Varchar',
        'Description'       => 'HTMLText',
        'FileIcon'          => 'HTMLText',
    );
    
    /**
     * 1:n relationships.
     *
     * @var array
     */
    private static $has_many = array(
        'FileTranslations' => FileTranslation::class,
    );

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
    public function getTitle() {
        return $this->getTranslationFieldValue('Title');
    }
    
    /**
     * getter for the Description, looks for set translation
     * 
     * @return string The description from the translation object or an empty string
     */
    public function getDescription() {
        return $this->getTranslationFieldValue('Description');
    }

    /**
     * Returns a HTML snippet for the related Files icon.
     *
     * @return string
     */
    public function getFileIcon() {
        $file = $this->File();
		$ext  = strtolower($file->getExtension());
        if (Director::fileExists(project() . "/images/app_icons/{$ext}_32.png")) {
            $icon = project() . "/images/app_icons/{$ext}_32.png";
        } else {
            $icon = $this->File()->getIcon();
        }
        return Tools::string2html('<img src="' . $icon . '" alt="' . $this->File()->FileType . '" title="' . $this->File()->Title . '" />');
    }
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2012
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
     * @since 27.06.2012
     */
    public function plural_name() {
        return Tools::plural_name_for($this);
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
                'Title'            => _t(File::class . '.TITLE', 'Display name'),
                'FileIcon'         => _t(File::class . '.FILEICON', 'File icon'),
                'FileType'         => _t(File::class . '.TYPE', 'File type'),
                'FileTranslations' => FileTranslation::singleton()->plural_name(),
                'Product'          => Product::singleton()->singular_name(),
                'File'             => \SilverStripe\Assets\File::singleton()->singular_name(),
                'DownloadPage'     => DownloadPage::singleton()->singular_name(),
                'Description'      => _t(File::class . '.DESCRIPTION', 'Description (e.g. for Slidorion textfield)'),
                
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
     * @since 27.06.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'FileIcon'      => $this->fieldLabel('FileIcon'),
            'File.FileType' => $this->fieldLabel('FileType'),
            'Title'         => $this->fieldLabel('Title'),
        );


        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     */
    public function getCMSFields() {
        $fields = DataObjectExtension::getCMSFields($this);
        $fields->removeByName('ProductID');
        $fields->removeByName('DownloadPageID');
        return $fields;
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
        if ($this->FileID == 0 &&
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.03.2013
     */
    public function onBeforeDelete() {
        parent::onBeforeDelete();
        if ($this->File()->exists()) {
            $this->File()->delete();
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
        if ($this->File()->exists() &&
            empty($this->Title)) {
            $this->Title = $this->File()->Title;
        }
    }
}