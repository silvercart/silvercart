<?php
/**
 * Copyright 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Products
 */

/**
 * Global extension of Image that provides means managing huge numbers of images
 * 
 * @package Silvercart
 * @subpackage Products
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 04.05.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartImageExtension extends DataObjectDecorator {
    
    /**
     * Extends the database fields and relations of the decorated class.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.05.2011
     */
    public function extraStatics() {
        return array(
            'casting' => array(
                'ImageThumbnail' => 'VarChar(255)'
            )
        );
    }
    
    /**
     * Add additional summary fields.
     *
     * @param array &$fields The field definitions
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.05.2011
     */
    public function updateSummaryFields(&$fields) {
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
     * @copyright 2011 pixeltricks GmbH
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
                $image = $this->owner;
            } else {
                $image = $this->owner->getFormattedImage('SetRatioSize', $width, $height);
            }
        } else if ($orientation == Image::ORIENTATION_PORTRAIT) {
            if ($this->owner->getHeight() <= $height) {
                $image = $this->owner;
            } else {
                $image = $this->owner->getFormattedImage('SetRatioSize', $width, $height);
            }
        } else {
            if ($this->owner->getWidth()  <= $width &&
                $this->owner->getHeight() <= $height) {
                $image = $this;
            } else {
                $image = $this->owner->getFormattedImage('SetRatioSize', $width, $height);
            }
        }

        return $image;
    }
}
