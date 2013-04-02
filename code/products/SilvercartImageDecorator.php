<?php
/**
 * Copyright 2013 pixeltricks GmbH
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
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
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
     * @since 04.05.2011
     */
    public function ImageThumbnail() {
        $imageTag = $this->owner->SetRatioSize(50,50);
        return $imageTag;
    }
}
