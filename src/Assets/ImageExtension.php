<?php

namespace SilverCart\Assets;

use SilverCart\Dev\Tools;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\Image;
use SilverStripe\Assets\Image_Backend;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Versioned\Versioned;

/**
 * Extension for SilverStripe\Assets\Image.
 *
 * @package SilverCart
 * @subpackage Assets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ImageExtension extends DataExtension {
    
    /**
     * attribute casting
     *
     * @var array
     */
    private static $casting = array(
        'ImageThumbnail' => 'HTMLText',
    );
    
    /**
     * Add additional summary fields.
     *
     * @param array &$fields The field definitions
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.12.2015
     */
    public function updateSummaryFields(&$fields) {
        if (array_key_exists('ImagePreview', $fields)) {
            return;
        }
        $fields = array_merge(
            $fields,
            array(
                'ImageThumbnail' => 'ImageThumbnail',
            )
        );
    }
    
    /**
     * Returns the nicely formatted Price of the product.
     * 
     * @param int $width  width
     * @param int $height height
     * 
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 31.05.2017
     */
    public function ImageThumbnail($width = 50, $height = 50) {
        $image = $this->owner->Pad($width, $height);
        /* @var $image \SilverStripe\Assets\Storage\DBFile */
        if (!is_null($image)) {
            return Tools::string2html($this->getTagWithPreview($image, $this->owner));
        }
        return $image;
    }

	/**
	 * Return an XHTML img tag for this Image,
	 * or NULL if the image file doesn't exist on the filesystem.
	 *
	 * @return string
	 */
	public function getTagWithPreview($image, $originalImage = null) {
        if (is_null($originalImage)) {
            $originalImage = $this->owner;
        }
		if ($image->exists()) {
            $originalUrl = $originalImage->getURL();
			$url   = $image->getURL();
			$title = ($image->Title) ? $image->Title : $image->Filename;
			if ($image->Title) {
				$title = Convert::raw2att($image->Title);
			} elseif (preg_match("/([^\/]*)\.[a-zA-Z0-9]{1,6}$/", $title, $matches)) {
                $title = Convert::raw2att($matches[1]);
			}
			return "<img src=\"$url\" alt=\"$title\" data-img-src=\"$originalUrl\" class=\"hover-image-preview\" />";
		}
	}

    /**
     * Returns a resized version of the image if the image is bigger
     * than the given dimensions.
     * Otherwise the original image is returned.
     *
     * @param int $width  The width
     * @param int $height The height
     *
     * @return Image
     */
    public function PadMax($width, $height) {
        $orientation = $this->owner->getOrientation();
        $image       = false;

        if ($orientation == Image_Backend::ORIENTATION_LANDSCAPE) {
            if ($this->owner->getWidth() <= $width) {
                $image = $this->owner->getTag();
            } else {
                $image = $this->owner->Pad($width, $height);
            }
        } else if ($orientation == Image_Backend::ORIENTATION_PORTRAIT) {
            if ($this->owner->getHeight() <= $height) {
                $image = $this->owner->getTag();
            } else {
                $image = $this->owner->Pad($width, $height);
            }
        } else {
            if ($this->owner->getWidth()  <= $width &&
                $this->owner->getHeight() <= $height) {
                $image = $this->owner->getTag();
            } else {
                $image = $this->owner->Pad($width, $height);
            }
        }

        return $image;
    }
    
    /**
     * Returns a resized version of the image if the image is bigger
     * than the given dimensions.
     * Otherwise the original image is returned.
     *
     * @param int $width  The width
     * @param int $height The height
     *
     * @return Image
     */
    public function PadIfBigger($width, $height) {
        $image = false;

        if ($this->owner->getWidth()  <= $width &&
            $this->owner->getHeight() <= $height) {
            $image = $this->owner->getTag();
        } else {
            $image = $this->owner->Pad($width, $height);
        }

        return $image;
    }
    
    /**
     * Creates a SilverStripe File using an existing file with the given
     * $sourceFilePath on the filesystem or as an URL.
     * $targetFolderPath is usually a subdirectory of ASSETS_PATH.
     * If the optional parameter $targetFilename is not given, the file name of 
     * the $sourceFilePath will be used.
     * If the optional parameter $targetFileTitle is not given, $targetFilename 
     * will be used without its dot seperated ending.
     * 
     * @param string $sourceFilePath   Absolute source file path or URL
     * @param string $targetFolderPath Absolute target folder path (without file name)
     * @param string $targetFilename   Optional target file name (without path)
     * @param string $targetFileTitle  Optional target file title
     * 
     * @return Image
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.08.2018
     */
    public static function create_from_path($sourceFilePath, $targetFolderPath, $targetFilename = null, $targetFileTitle = null) {
        if (is_null($targetFilename)) {
            $targetFilename = basename($sourceFilePath);
        }
        if (is_null($targetFileTitle)) {
            $targetFileTitle = strrev(substr(strrev($targetFilename), strpos(strrev($targetFilename), '.') + 1));
        }
        $fileContent    = file_get_contents($sourceFilePath);
        $fileHash       = sha1($fileContent);
        $hashDir        = substr($fileHash, 0, 10);
        $hashPath       = str_replace('//', '/', $targetFolderPath . DIRECTORY_SEPARATOR . $hashDir);
        $targetFilePath = $hashPath . DIRECTORY_SEPARATOR . $targetFilename;
        $targetFolder   = Folder::find_or_make(str_replace(ASSETS_PATH, '', $targetFolderPath));
        if (!file_exists($hashPath)) {
            mkdir($hashPath, 0777, true);
        }
        file_put_contents($targetFilePath, $fileContent);
        
        $image = new Image();
        $image->FileFilename = str_replace('assets/', '', $targetFolder->Filename . $targetFilename);
        $image->FileHash     = sha1_file($targetFilePath);
        $image->Name         = $targetFilename;
        $image->Title        = $targetFileTitle;
        $image->ParentID     = $targetFolder->ID;
        $image->write();
        $image->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        if (!file_exists($targetFilePath)) {
            mkdir($hashPath, 0777, true);
            file_put_contents($targetFilePath, $fileContent);
        }
        return $image;
    }
}
