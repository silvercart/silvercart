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
 * @subpackage Widgets
 */

/**
 * Provides a slider for presenting product groups.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 13.12.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartProductGroupSliderWidget extends SilvercartWidget {
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2012 pixeltricks GmbH
     * @since 13.07.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),             array(
                    
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns the active product group object.
     *
     * @return DataObject
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.12.2011
     */
    public function getActiveProductGroup() {
        $activeProductGroup = false;
        $productGroups      = $this->getProductGroups();
        
        if ($productGroups->exists()) {
            $activeProductGroup = $productGroups->first();
        }
        
        return $activeProductGroup;
    }
    
    /**
     * Returns the product group object to the left of the active product
     * group.
     *
     * @return DataObject
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.12.2011
     */
    public function getLeftProductGroup() {
        $productGroup   = false;
        $productGroups  = $this->getProductGroups();
        
        if ($productGroups->exists()) {
            $productGroup = $productGroups->last();
        }
        
        return $productGroup;
    }
    
    /**
     * Returns the product group object to the right of the active product
     * group.
     *
     * @return DataObject
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.12.2011
     */
    public function getRightProductGroup() {
        $productGroup   = false;
        $productGroups  = $this->getProductGroups();
        
        if ($productGroups->exists()) {
            
            $productGroup = $productGroups->limit(2,1)->first();
        }
        
        return $productGroup;
    }
    
    /**
     * Returns all product groups
     *
     * @return DataList may be empty
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.12.2011
     */
    public function getProductGroups() {
        $productGroups = SilvercartProductGroupPage::get()->filter('ShowInMenus', 1);
        return $productGroups;
    }
    
    /**
     * We always want to use a content view for this widget.
     *
     * @return boolean true
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.12.2011
     */
    public function isContentView() {
        return true;
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.12.2011
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this);
        
        return $fields;
    }
}

/**
 * Provides a slider for presenting product groups.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 13.12.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartProductGroupSliderWidget_Controller extends SilvercartWidget_Controller {
    
    /**
     * Load javascript and css files.
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.12.2011
     */
    public function init() {
        $productGroups          = array();
        $productGroupObjects    = SilvercartProductGroupPage::get()->filter('ShowInMenus', 1);
        
        if ($productGroupObjects->exists()) {
            foreach ($productGroupObjects as $productGroupObject) {
                $groupPictureURL        = '';
                $groupPictureThumbURL   = '';
                if ($productGroupObject->GroupPicture()->ID > 0) {
                    $groupPictureURL        = $productGroupObject->GroupPicture()->SetRatioSize(600,400)->URL;
                    $groupPictureThumbURL   = $productGroupObject->GroupPicture()->SetRatioSize(100,100)->URL;
                }
                $productGroups[] = sprintf("
                    pr.addProduct(
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s'
                    );",
                    $productGroupObject->MenuTitle,
                    $productGroupObject->Link(),
                    $groupPictureURL,
                    $groupPictureThumbURL,
                    $productGroupObject->MenuTitle,
                    $productGroupObject->MenuTitle
                );
            }
        }
        
        Requirements::customScript(
            sprintf('
                var pr;
                var productRotatorAnimation;
                
                $(document).ready(function() {
                    pr = new ProductRotator();
                    
                    %s

                    pr.start();
                });
            ',
            implode("\n", $productGroups)
            )
        );
    }
}