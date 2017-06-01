<?php
/**
 * Copyright 2015 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Products
 */

/**
 * Global extension of Image that provides means managing huge numbers of images
 * 
 * @package Silvercart
 * @subpackage Products
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2015 pixeltricks GmbH
 * @since 18.12.2015
 * @license see license file in modules root directory
 */
class SilvercartImageExtension extends DataExtension {
    
    /**
     * attribute casting
     *
     * @var array
     */
    private static $casting = array(
        'ImageThumbnail' => 'VarChar(255)'
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
     * @return HTMLText
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 31.05.2017
     */
    public function ImageThumbnail($width = 50, $height = 50) {
        $image = $this->owner->SetRatioSize($width, $height);
        /* @var $image Image_Cached */
        if (!is_null($image)) {
            $html = new HTMLText();
            $html->setValue($image->getTagWithPreview($this->owner));
            return $html;
        }
        return $image;
    }

	/**
	 * Return an XHTML img tag for this Image,
	 * or NULL if the image file doesn't exist on the filesystem.
	 *
	 * @return string
	 */
	public function getTagWithPreview($originalImage = null) {
        if (is_null($originalImage)) {
            $originalImage = $this->owner;
        }
		if ($this->owner->exists()) {
            $originalUrl = $originalImage->getURL();
			$url   = $this->owner->getURL();
			$title = ($this->owner->Title) ? $this->owner->Title : $this->owner->Filename;
			if ($this->owner->Title) {
				$title = Convert::raw2att($this->owner->Title);
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
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2012-12-13
     */
    public function SetRatioSizeMax($width, $height) {
        $orientation = $this->owner->getOrientation();
        $image       = false;

        if ($orientation == Image::ORIENTATION_LANDSCAPE) {
            if ($this->owner->getWidth() <= $width) {
                $image = $this->owner->getTag();
            } else {
                $image = $this->owner->getFormattedImage('SetRatioSize', $width, $height);
            }
        } else if ($orientation == Image::ORIENTATION_PORTRAIT) {
            if ($this->owner->getHeight() <= $height) {
                $image = $this->owner->getTag();
            } else {
                $image = $this->owner->getFormattedImage('SetRatioSize', $width, $height);
            }
        } else {
            if ($this->owner->getWidth()  <= $width &&
                $this->owner->getHeight() <= $height) {
                $image = $this->owner->getTag();
            } else {
                $image = $this->owner->getFormattedImage('SetRatioSize', $width, $height);
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
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.04.2014
     */
    public function SetRatioSizeIfBigger($width, $height) {
        $image = false;

        if ($this->owner->getWidth()  <= $width &&
            $this->owner->getHeight() <= $height) {
            $image = $this->owner->getTag();
        } else {
            $image = $this->owner->getFormattedImage('SetRatioSize', $width, $height);
        }

        return $image;
    }
}
