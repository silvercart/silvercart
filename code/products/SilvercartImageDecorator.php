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
 * @license see license file in modules root directory
 */
class SilvercartImageDecorator extends DataExtension {
    
    /**
     * attribute casting
     *
     * @var array
     */
    public static $casting = array(
                'ImageThumbnail' => 'VarChar(255)'
    );
    
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
     * @return Image_Cached
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.05.2011
     */
    public function ImageThumbnail() {
        $imageTag = $this->owner->SetRatioSize(50,50);
        return $imageTag;
    }
}
