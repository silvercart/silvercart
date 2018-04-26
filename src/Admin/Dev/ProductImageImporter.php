<?php

namespace SilverCart\Admin\Dev;

use SilverCart\Model\Product\Product;
use SilverStripe\Assets\FileNameFilter;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Dev\BuildTask;

/**
 * Provides a task to assign the uploaded product images.
 * 
 * @package SilverCart
 * @subpackage Admin_Dev
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class ProductImageImporter extends BuildTask {

        /**
     * Relative path to the upload folder (relative to assets).
     *
     * @var string
     */
    private static $relative_upload_folder = 'assets/unassigned-product-images';
    
    /**
     * Name of the temporary file that determines a running import process.
     *
     * @var string
     */
    private static $import_is_running_file_name = 'scpii-is-running';
    
    /**
     * Name of the file that determines that the script installation is
     * completed.
     *
     * @var string
     */
    private static $import_is_installed_file_name = 'scpii-is-installed';
    
    /**
     * Runs this task.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return void
     */
    public function run($request) {
        if (self::is_running()) {
            return;
        }
        $this->markAsInstalled();
        $this->markAsRunning();
        
        $uploadedFiles = $this->getUploadedFiles();
        if (count($uploadedFiles) > 0) {
            $imageData = array();
            foreach ($uploadedFiles as $uploadedFile) {
                $consecutiveNumber = 1;
                $nameWithoutEnding = strrev(substr(strrev($uploadedFile), strpos(strrev($uploadedFile), '.') + 1));
                $description       = '';
                
                if (strpos($nameWithoutEnding, '-') !== false) {
                    $parts = explode('-', $nameWithoutEnding);
                    $productnumber     = array_shift($parts);
                    $consecutiveNumber = array_shift($parts);
                    if (count($parts) > 0) {
                        $description = str_replace('   ', ' - ', str_replace('-', ' ', implode('-', $parts)));
                    }
                } else {
                    $productnumber = $nameWithoutEnding;
                }
                
                if (!array_key_exists($productnumber, $imageData)) {
                    $imageData[$productnumber] = array();
                }
                $imageData[$productnumber][$consecutiveNumber] = array(
                    'filename'    => $uploadedFile,
                    'description' => $description,
                );
            }
            
            foreach ($imageData as $productnumber => $data) {
                $product = Product::get_by_product_number($productnumber);
                if ($product instanceof Product &&
                    $product->exists()) {
                    $this->deleteExistingImages($product);
                    ksort($data);
                    foreach ($data as $consecutiveNumber => $imageInfo) {
                        $this->addNewImage($product, $imageInfo['filename'], $imageInfo['description'], $consecutiveNumber);
                    }
                }
            }
        }
        
        $this->unmarkAsRunning();
    }
    
    /**
     * Adds a new image to the given product.
     * 
     * @param Product $product           Product to add image to
     * @param string  $filename          Filename
     * @param string  $description       Description
     * @param int     $consecutiveNumber Consecutive number
     */
    protected function addNewImage(Product $product, $filename, $description, $consecutiveNumber) {
        $fileEnding     = strrev(substr(strrev($filename), 0, strpos(strrev($filename), '.')));
		$nameFilter     = FileNameFilter::create();
        $targetFilename = $product->ProductNumberShop . '-' . $nameFilter->filter($product->Title) . '-' . $consecutiveNumber . '.' . $fileEnding;
        $originalFile   = self::get_absolute_upload_folder() . '/' . $filename;
        $targetFile     = self::get_absolute_product_image_folder() . '/' . $targetFilename;
        $parentFolder   = Folder::find_or_make('assets/product-images');

        rename($originalFile, $targetFile);

        $image = new Image();
        $image->Name     = basename($targetFilename);
        $image->ParentID = $parentFolder->ID;
        $image->write();
        
        $silvercartImage = new \SilverCart\Model\Product\Image();
        $silvercartImage->ImageID = $image->ID;
        $silvercartImage->Title   = $description;
        $silvercartImage->write();
            
        $product->Images()->add($silvercartImage);
    }
    
    /**
     * Deletes the existing images of the given product.
     * 
     * @param Product $product Product to delete images for
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.07.2016
     */
    protected function deleteExistingImages(Product $product) {
        foreach ($product->Images() as $existingImage) {
            /* @var $existingImage SilverCart\Model\Product\Image */
            $existingImage->delete();
        }
    }
    
    /**
     * Returns a list of not yet handled product image uploads.
     * 
     * @return array
     */
    protected function getUploadedFiles() {
        $files  = array();
        $ignore = array(
            '.',
            '..',
            '_resampled',
            self::get_import_is_installed_file_name(),
            self::get_import_is_running_file_name(),
        );
        $dir = self::get_absolute_upload_folder();
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if (in_array($entry, $ignore)) {
                    continue;
                }
                $files[] = $entry;
            }

            closedir($handle);
        }
        return $files;
    }
    
    /**
     * Adds the file that determines that the script installation is completed.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.07.2016
     */
    protected function markAsInstalled() {
        file_put_contents(self::get_import_is_installed_file_path(), '1');
    }
    
    /**
     * Adds the file that determines a running import process.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.07.2016
     */
    protected function markAsRunning() {
        file_put_contents(self::get_import_is_running_file_path(), '1');
    }
    
    /**
     * Removes the file that determines a running import process.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.07.2016
     */
    protected function unmarkAsRunning() {
        unlink(self::get_import_is_running_file_path());
    }

    /**
     * Returns the source path to import the images from.
     * 
     * @return string
     */
    public static function get_import_source_path() {
        return Director::baseFolder();
    }
    
    /**
     * Returns the name of the file that determines a running import process.
     * 
     * @return string
     */
    public static function get_import_is_running_file_name() {
        return self::$import_is_running_file_name;
    }
    
    /**
     * Returns the path to the file that determines a running import process.
     * 
     * @return string
     */
    public static function get_import_is_running_file_path() {
        return self::get_import_source_path() . self::get_import_is_running_file_name();
    }
    
    /**
     * Returns the name of the file that determines that the script installation 
     * is completed.
     * 
     * @return string
     */
    public static function get_import_is_installed_file_name() {
        return self::$import_is_installed_file_name;
    }
    
    /**
     * Returns the path to the file that determines that the script installation 
     * is completed.
     * 
     * @return string
     */
    public static function get_import_is_installed_file_path() {
        return self::get_import_source_path() . self::get_import_is_installed_file_name();
    }
    
    /**
     * Returns whether the script installation is completed.
     * 
     * @return bool
     */
    public static function is_installed() {
        return file_exists(self::get_import_is_installed_file_path());
    }
    
    /**
     * Returns whether there is a currently running import process.
     * 
     * @return bool
     */
    public static function is_running() {
        return file_exists(self::get_import_is_running_file_path());
    }
    
    /**
     * Returns the absolute path to the upload folder.
     * 
     * @return string
     */
    public static function get_absolute_upload_folder() {
        return Director::publicFolder() . '/assets/' . self::$relative_upload_folder;
    }
    
    /**
     * Returns the absolute path to the product image folder.
     * 
     * @return string
     */
    public static function get_absolute_product_image_folder() {
        return Director::publicFolder() . '/assets/product-images';
    }
    
    /**
     * Returns the relative path to the upload folder.
     * 
     * @return string
     */
    public static function get_relative_upload_folder() {
        return self::$relative_upload_folder;
    }
    
    /**
     * Sets the relative path to the upload folder.
     * 
     * @param string $relative_upload_folder Relative path to the upload folder.
     * 
     * @return void
     */
    public static function set_relative_upload_folder($relative_upload_folder) {
        self::$relative_upload_folder = $relative_upload_folder;
    }
}