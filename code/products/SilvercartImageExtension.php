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
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 04.05.2011
     */
    public function ImageThumbnail() {
        $imageTag = $this->owner->SetRatioSize(50,50);
        return $imageTag;
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
