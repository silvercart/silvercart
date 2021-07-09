<?php

namespace SilverCart\Admin\Dev\Tasks;

use SilverCart\Extensions\Assets\ImageExtension;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\File as SilverCartFile;
use SilverCart\Model\Product\Image as SilverCartImage;
use SilverStripe\Assets\FileNameFilter;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Versioned\Versioned;

/**
 * Provides a task to assign the uploaded product images.
 * 
 * @package SilverCart
 * @subpackage Admin_Dev_Tasks
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class ProductImageImportTask extends BuildTask
{
    use \SilverCart\Dev\CLITask;
    /**
     * Set a custom url segment (to follow dev/tasks/)
     *
     * @var string
     */
    private static $segment = 'sc-image-importer';
    /**
     * Shown in the overview on the {@link TaskRunner}.
     * HTML or CLI interface. Should be short and concise, no HTML allowed.
     * 
     * @var string
     */
    protected $title = 'Import SilverCart Product Images';
    /**
     * Describe the implications the task has, and the changes it makes. Accepts 
     * HTML formatting.
     * 
     * @var string
     */
    protected $description = 'Task to import SilverCart product images. By '
            . 'default, images should be stored in <strong><i>/public/assets/unassigned-product-images</i></strong>.<br/>'
            . ' The importer will run through the files and assign the images by'
            . ' file name. The file name should be equal with the product number.<br/>'
            . 'By default you can use the <i>"-"</i> sign to separate product number'
            . ' and a numeric index to add multiple images to one product.<br/>'
            . 'By adding another <i>"-"</i> behind the numeric index, you can also'
            . ' add an optional description (e.g. used as ALT tag).<br/>'
            . '<br/>'
            . 'Example image file names for a product with the product number'
            . ' <i>ABC1234D56</i>:'
            . '<ul>'
            . '<li>ABC1234D56.jpg</li>'
            . '<li>ABC1234D56-1.jpg</li>'
            . '<li>ABC1234D56-2.jpg</li>'
            . '<li>ABC1234D56-3-Left-side-view-of-my-awesome-product.jpg</li>'
            . '</ul>';

    /**
     * Relative path to the upload folder (relative to assets).
     *
     * @var string
     */
    private static $relative_upload_folder = 'unassigned-product-images';
    /**
     * Name of the temporary file that determines a running import process.
     *
     * @var string
     */
    private static $import_is_running_file_name = '.scpii-is-running';
    /**
     * Name of the file that determines that the script installation is
     * completed.
     *
     * @var string
     */
    private static $import_is_installed_file_name = '.scpii-is-installed';
    /**
     * Character to use to separate the prduct number and image name.
     *
     * @var string
     */
    private static $image_name_separator = '-';
    /**
     * File endings to use for the image import. Other endings will be imported as files.
     *
     * @var string[]
     */
    private static $image_endings = ['jpg', 'jpeg', 'png'];
    
    /**
     * Returns the files (including drafts) from the given $dir.
     * 
     * @param string $dir        Directory
     * @param bool   $forPreview For preview?
     * 
     * @return array
     */
    public static function getFilesFromDir(string $dir, bool $forPreview = false, string $subDir = null) : array
    {
        $files  = [];
        $ignore = [
            '.',
            '..',
            '_resampled',
            self::get_import_is_installed_file_name(),
            self::get_import_is_running_file_name(),
        ];
        if ($forPreview) {
            $folder = Folder::find_or_make(self::get_relative_upload_folder());
        }
        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while (false !== ($entry = readdir($handle))) {
                    if (in_array($entry, $ignore)) {
                        continue;
                    }
                    if (strpos($entry, '__FitMax') !== false) {
                        if (!$forPreview) {
                            unlink("{$dir}/{$entry}");
                        }
                        continue;
                    }
                    if (is_dir("{$dir}/{$entry}")) {
                        $files = array_merge($files, self::getFilesFromDir("{$dir}/{$entry}", $forPreview, $entry));
                        continue;
                    }
                    if ($forPreview) {
                        $file = $folder->myChildren()->filter('FileHash:StartsWith', $entry)->first();
                        if ($file instanceof File
                         && $file->exists()
                        ) {
                            $entry = $file->Name;
                        }
                        $files[] = $entry;
                    } else {
                        $files[] = [
                            'filename' => $entry,
                            'filepath' => $dir,
                        ];
                    }
                }

                closedir($handle);
            }
        }
        sort($files);
        return $files;
    }
    
    /**
     * Runs this task.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return void
     */
    public function run($request) : void
    {
        self::$log_file_name = 'ProductImageImportTask';
        if (self::is_running()) {
            $this->printInfo('quit, import is already running.');
            return;
        }
        $this->markAsInstalled();
        $this->markAsRunning();
        $importedImagesCount = 0;
        $found               = [];
        $notFound            = [];
        $importedFilesCount  = 0;
        $foundFiles          = [];
        $notFoundFiles       = [];
        $uploadedFiles       = $this->getUploadedFiles();
        $folder              = Folder::find_or_make($this->config()->relative_upload_folder);
        $uploadedFilesCount  = count($uploadedFiles);
        if ($uploadedFilesCount > 0) {
            $this->printInfo("found {$uploadedFilesCount} files to import.");
            $this->printInfo("");
            $this->printInfo("analyzing files...", self::$CLI_COLOR_MAGENTA);
            $imageData    = [];
            $fileData     = [];
            $currentIndex = 0;
            foreach ($uploadedFiles as $uploadedFileData) {
                $uploadedFile = $uploadedFileData['filename'];
                $uploadedPath = $uploadedFileData['filepath'];
                $currentIndex++;
                $logString = "{$this->getXofY($currentIndex, $uploadedFilesCount)} | handling file {$uploadedFile}";
                $this->printProgressInfo("{$logString}");
                $consecutiveNumber = 1;
                $nameWithoutEnding = strrev(substr(strrev($uploadedFile), strpos(strrev($uploadedFile), '.') + 1));
                $ending            = substr($uploadedFile, strpos($uploadedFile, '.') + 1);
                $description       = '';
                $separator         = self::get_image_name_separator();
                $file              = $folder->myChildren()->filter('FileHash:StartsWith', $nameWithoutEnding)->first();
                if ($file instanceof Image
                 && $file->exists()
                ) {
                    $name              = basename($file->FileFilename);
                    $nameWithoutEnding = strrev(substr(strrev($name), strpos(strrev($name), '.') + 1));
                }
                if (strpos($nameWithoutEnding, $separator) !== false) {
                    $parts = explode($separator, $nameWithoutEnding);
                    $productnumber     = array_shift($parts);
                    $consecutiveNumber = array_shift($parts);
                    if (!is_numeric($consecutiveNumber)
                     && !empty($consecutiveNumber)
                    ) {
                        array_unshift($parts, $consecutiveNumber);
                        $consecutiveNumber = 1;
                    } elseif (is_numeric($consecutiveNumber)) {
                        $consecutiveNumber = (int) $consecutiveNumber;
                    }
                    if (count($parts) > 0) {
                        $description = str_replace('   ', ' ' . $separator . ' ', str_replace($separator, ' ', implode($separator, $parts)));
                    }
                } else {
                    $productnumber = $nameWithoutEnding;
                }
                $fileType = 'IMAGE';
                if (in_array($ending, self::config()->image_endings)) {
                    $isImage = true;
                    if (!array_key_exists($productnumber, $imageData)) {
                        $imageData[$productnumber] = [];
                    }
                    $imageData[$productnumber][$consecutiveNumber] = [
                        'filename'    => $uploadedFile,
                        'filepath'    => $uploadedPath,
                        'description' => $description,
                        'file'        => $file,
                    ];
                } else {
                    $fileType = 'FILE';
                    if (!array_key_exists($productnumber, $fileData)) {
                        $fileData[$productnumber] = [];
                    }
                    $fileData[$productnumber][$consecutiveNumber] = [
                        'filename'    => $uploadedFile,
                        'filepath'    => $uploadedPath,
                        'description' => $description,
                        'file'        => $file,
                    ];
                }
                
                $newOrExisting = ($file instanceof Image) ? 'exists' : 'new';
                $this->printInfo("{$logString} - #{$productnumber} -{$newOrExisting}- ({$consecutiveNumber}) {$description} [{$fileType}]", self::$CLI_COLOR_GREEN);
            }
            $this->printInfo("");
            $this->printInfo("looking for matching products...", self::$CLI_COLOR_MAGENTA);
            $this->printInfo("images:", self::$CLI_COLOR_MAGENTA);
            $this->importFileData($imageData, $importedImagesCount, $found, $notFound, true);
            $this->printInfo("files:", self::$CLI_COLOR_MAGENTA);
            $this->importFileData($fileData, $importedFilesCount, $foundFiles, $notFoundFiles, false);
        } else {
            $this->printInfo("quit, nothing to import");
        }
        $foundCount    = count($found);
        $notFoundCount = count($notFound);
        $notFoundList  = implode(', ', $notFound);
        $this->printInfo('');
        $this->printInfo("imported {$importedImagesCount} image(s) for {$foundCount} product(s).");
        if ($notFoundCount > 0) {
            $this->printInfo("did not find {$notFoundCount} product(s).");
            $this->printInfo("- product number(s): {$notFoundList}");
        }
        $foundFilesCount    = count($foundFiles);
        $notFoundFilesCount = count($notFoundFiles);
        $notFoundFilesList  = implode(', ', $notFoundFiles);
        $this->printInfo('');
        $this->printInfo("imported {$importedFilesCount} file(s) for {$foundFilesCount} product(s).");
        if ($notFoundFilesCount > 0) {
            $this->printInfo("did not find {$notFoundFilesCount} product(s).");
            $this->printInfo("- product number(s): {$notFoundFilesList}");
        }
        $this->printInfo('');
        foreach ($folder->myChildren() as $file) {
            if (!$file->exists()) {
                $file->delete();
            }
        }
        $this->unmarkAsRunning();
    }
    
    protected function importFileData(array $fileData, int &$importedCount, array &$found, array &$notFound, bool $isImageImport = true) : void
    {
        $fileDataCount = count($fileData);
        $currentIndex  = 0;
        foreach ($fileData as $productnumber => $data) {
            $currentIndex++;
            $logString = "{$this->getXofY($currentIndex, $fileDataCount)} | handling productnumber {$productnumber}";
            $this->printProgressInfo("{$logString}");
            $product = Product::get_by_product_number($productnumber);
            if ($product instanceof Product
             && $product->exists()
            ) {
                $this->printInfo("{$logString} - found \"{$product->Title}\"", self::$CLI_COLOR_GREEN);
                $found[] = $productnumber;
                if ($isImageImport) {
                    $this->deleteExistingImages($product);
                    ksort($data);
                    foreach ($data as $consecutiveNumber => $imageInfo) {
                        $importedCount++;
                        $this->addNewImage($product, $imageInfo['filename'], $imageInfo['filepath'], $imageInfo['description'], $consecutiveNumber, $imageInfo['file']);
                    }
                } else {
                    $this->deleteExistingFiles($product);
                    ksort($data);
                    foreach ($data as $consecutiveNumber => $imageInfo) {
                        $importedCount++;
                        $this->addNewFile($product, $imageInfo['filename'], $imageInfo['filepath'], $imageInfo['description'], $consecutiveNumber, $imageInfo['file']);
                    }
                }
            } else {
                $notFound[] = $productnumber;
                $this->printInfo("{$logString} - not found", self::$CLI_COLOR_RED);
            }
        }
    }
    
    /**
     * Adds a new image to the given product.
     * 
     * @param Product $product           Product to add image to
     * @param string  $filename          Filename
     * @param string  $filepath          Filepath
     * @param string  $description       Description
     * @param int     $consecutiveNumber Consecutive number
     */
    protected function addNewImage(Product $product, string $filename, string $filepath, string $description, int $consecutiveNumber, Image $file = null) : void
    {
        $nameFilter     = FileNameFilter::create();
        $targetFilename = "{$product->ProductNumberShop}-{$nameFilter->filter($product->Title)}-{$consecutiveNumber}.";
        if ($file instanceof Image
         && $file->exists()
        ) {
            $this->printInfo("\t   updating existing file #{$file->ID}", self::$CLI_COLOR_GREEN);
            $image           = $file;
            $fileEnding      = strrev(substr(strrev($image->Name), 0, strpos(strrev($image->Name), '.')));
            $targetFilename .= $fileEnding;
            $targetFolder    = Folder::find_or_make(str_replace(ASSETS_PATH, '', self::get_absolute_product_image_folder()));
            $image->Name     = $targetFilename;
            $image->ParentID = $targetFolder->ID;
            $image->write();
            $image->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        } else {
            $fileEnding      = strrev(substr(strrev($filename), 0, strpos(strrev($filename), '.')));
            $targetFilename .= $fileEnding;
            $originalFile    = self::get_absolute_upload_folder() . "/{$filename}";
            $targetFile      = self::get_absolute_product_image_folder() . "/{$targetFilename}";
            $this->printInfo("\t • moving file from {$originalFile}", self::$CLI_COLOR_GREEN);
            $this->printInfo("\t                 to {$targetFile}", self::$CLI_COLOR_GREEN);
            $image = ImageExtension::create_from_path($originalFile, self::get_absolute_product_image_folder(), $targetFilename);
            unlink($originalFile);
            $this->printInfo("\t   created new image #{$image->ID}", self::$CLI_COLOR_GREEN);
        }
        $silvercartImage = SilverCartImage::create();
        $silvercartImage->ImageID = $image->ID;
        $silvercartImage->Title   = $description;
        $silvercartImage->write();
        $product->Images()->add($silvercartImage);
    }
    
    /**
     * Adds a new file to the given product.
     * 
     * @param Product $product           Product to add image to
     * @param string  $filename          Filename
     * @param string  $filepath          Filepath
     * @param string  $description       Description
     * @param int     $consecutiveNumber Consecutive number
     */
    protected function addNewFile(Product $product, string $filename, string $filepath, string $description, int $consecutiveNumber, File $file = null) : void
    {
        $nameFilter     = FileNameFilter::create();
        $targetFilename = "{$product->ProductNumberShop}-{$nameFilter->filter($product->Title)}-{$consecutiveNumber}.";
        if ($file instanceof File
         && $file->exists()
        ) {
            $this->printInfo("\t   updating existing file #{$file->ID}", self::$CLI_COLOR_GREEN);
            $fileEnding      = strrev(substr(strrev($file->Name), 0, strpos(strrev($file->Name), '.')));
            $targetFilename .= $fileEnding;
            $targetFolder    = Folder::find_or_make(str_replace(ASSETS_PATH, '', self::get_absolute_product_file_folder()));
            $file->Name      = $targetFilename;
            $file->ParentID  = $targetFolder->ID;
            $file->write();
            $file->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        } else {
            $fileEnding      = strrev(substr(strrev($filename), 0, strpos(strrev($filename), '.')));
            $targetFilename .= $fileEnding;
            $originalFile    = "{$filepath}/{$filename}";
            $targetFile      = self::get_absolute_product_file_folder() . "/{$targetFilename}";
            $this->printInfo("\t • moving file from {$originalFile}", self::$CLI_COLOR_GREEN);
            $this->printInfo("\t                 to {$targetFile}", self::$CLI_COLOR_GREEN);
            $file = ImageExtension::create_from_path($originalFile, self::get_absolute_product_file_folder(), $targetFilename, null, true);
            unlink($originalFile);
            $this->printInfo("\t   created new file #{$file->ID}", self::$CLI_COLOR_GREEN);
        }
        $silvercartFile = SilverCartFile::create();
        $silvercartFile->FileID = $file->ID;
        $silvercartFile->Title  = $description;
        $silvercartFile->write();
        $product->Files()->add($silvercartFile);
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
    protected function deleteExistingImages(Product $product) : void
    {
        if (!$product->Images()->exists()) {
            return;
        }
        $this->printInfo("\t • deleting {$product->Images()->count()} existing images", self::$CLI_COLOR_RED);
        foreach ($product->Images() as $existingImage) {
            /* @var $existingImage SilverCart\Model\Product\Image */
            $existingImage->delete();
        }
    }
    
    /**
     * Deletes the existing files of the given product.
     * 
     * @param Product $product Product to delete files for
     * 
     * @return void
     */
    protected function deleteExistingFiles(Product $product) : void
    {
        if (!$product->Files()->exists()) {
            return;
        }
        $this->printInfo("\t • deleting {$product->Files()->count()} existing files", self::$CLI_COLOR_RED);
        foreach ($product->Files() as $existingFile) {
            /* @var $existingFile SilverCart\Model\Product\File */
            $existingFile->delete();
        }
    }
    
    /**
     * Returns a list of not yet handled product image uploads.
     * 
     * @return array
     */
    protected function getUploadedFiles() : array
    {
        $files = [];
        $dirs  = [
            self::get_absolute_upload_folder(),
            self::get_absolute_protected_upload_folder(),
        ];
        foreach ($dirs as $dir) {
            $files = array_merge($files, self::getFilesFromDir($dir));
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
    protected function markAsInstalled() : void
    {
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
    protected function markAsRunning() : void
    {
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
    protected function unmarkAsRunning() : void
    {
        unlink(self::get_import_is_running_file_path());
    }

    /**
     * Returns the source path to import the images from.
     * 
     * @return string
     */
    public static function get_import_source_path() : string
    {
        return Director::baseFolder();
    }
    
    /**
     * Returns the name of the file that determines a running import process.
     * 
     * @return string
     */
    public static function get_import_is_running_file_name() : string
    {
        return self::$import_is_running_file_name;
    }
    
    /**
     * Returns the path to the file that determines a running import process.
     * 
     * @return string
     */
    public static function get_import_is_running_file_path() : string
    {
        return self::get_import_source_path() . DIRECTORY_SEPARATOR . self::get_import_is_running_file_name();
    }
    
    /**
     * Returns the name of the file that determines that the script installation 
     * is completed.
     * 
     * @return string
     */
    public static function get_import_is_installed_file_name() : string
    {
        return self::$import_is_installed_file_name;
    }
    
    /**
     * Returns the path to the file that determines that the script installation 
     * is completed.
     * 
     * @return string
     */
    public static function get_import_is_installed_file_path() : string
    {
        return self::get_import_source_path() . DIRECTORY_SEPARATOR . self::get_import_is_installed_file_name();
    }
    
    /**
     * Returns whether the script installation is completed.
     * 
     * @return bool
     */
    public static function is_installed() : bool
    {
        return file_exists(self::get_import_is_installed_file_path());
    }
    
    /**
     * Returns whether there is a currently running import process.
     * 
     * @return bool
     */
    public static function is_running() : bool
    {
        return file_exists(self::get_import_is_running_file_path());
    }
    
    /**
     * Returns the absolute path to the upload folder.
     * 
     * @return string
     */
    public static function get_absolute_upload_folder() : string
    {
        return Director::publicFolder() . '/assets/' . self::$relative_upload_folder;
    }
    
    /**
     * Returns the absolute path to the upload folder.
     * 
     * @return string
     */
    public static function get_absolute_protected_upload_folder() : string
    {
        return Director::publicFolder() . '/assets/.protected/' . self::$relative_upload_folder;
    }
    
    /**
     * Returns the absolute path to the product image folder.
     * 
     * @return string
     */
    public static function get_absolute_product_image_folder() : string
    {
        return Director::publicFolder() . '/assets/' . Product::DEFAULT_IMAGE_FOLDER;
    }
    
    /**
     * Returns the absolute path to the product image folder.
     * 
     * @return string
     */
    public static function get_absolute_product_file_folder() : string
    {
        return Director::publicFolder() . '/assets/' . Product::DEFAULT_FILES_FOLDER;
    }
    
    /**
     * Returns the relative path to the upload folder.
     * 
     * @return string
     */
    public static function get_relative_upload_folder() : string
    {
        return self::$relative_upload_folder;
    }
    
    /**
     * Sets the relative path to the upload folder.
     * 
     * @param string $relative_upload_folder Relative path to the upload folder.
     * 
     * @return void
     */
    public static function set_relative_upload_folder($relative_upload_folder) : void
    {
        self::$relative_upload_folder = $relative_upload_folder;
    }
    
    /**
     * Returns the character to use to separate the prduct number and image name.
     * 
     * @return string
     */
    public static function get_image_name_separator() : string
    {
        return self::config()->image_name_separator;
    }
    
    /**
     * Sets the character to use to separate the prduct number and image name.
     * 
     * @param string $image_name_separator Character to use to separate the prduct number and image name.
     * 
     * @return void
     */
    public static function set_image_name_separator($image_name_separator) : void
    {
        self::config()->update('image_name_separator', $image_name_separator);
    }
}