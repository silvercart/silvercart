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
     * Attributes.
     * 
     * @var array
     */
    public static $db = array(
    );
    
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
                    'Title'         => _t('SilvercartProductGroupSliderWidget.TITLE'),
                    'CMSTitle'      => _t('SilvercartProductGroupSliderWidget.CMSTITLE'),
                    'Description'   => _t('SilvercartProductGroupSliderWidget.DESCRIPTION'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns the title of this widget.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Title() {
        return $this->fieldLabel('Title');
    }
    
    /**
     * Returns the title of this widget for display in the WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function CMSTitle() {
        return $this->fieldLabel('CMSTitle');
    }
    
    /**
     * Returns the description of what this template does for display in the
     * WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Description() {
        return $this->fieldLabel('Description');
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
        
        if ($productGroups) {
            $activeProductGroup = $productGroups->First();
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
        
        if ($productGroups) {
            $productGroup = $productGroups->Last();
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
        
        if ($productGroups &&
            $productGroups->Count() > 1) {
            
            $productGroup = $productGroups->getRange(2,1)->First();
        }
        
        return $productGroup;
    }
    
    /**
     * Returns all product groups
     *
     * @return DataObjectSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.12.2011
     */
    public function getProductGroups() {
        $productGroups = DataObject::get(
            'SilvercartProductGroupPage',
            'ShowInMenus = 1'
        );
        
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
     * @return FieldSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.12.2011
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        return $fields;
    }
    
    /**
     * Loads the requirements for this object
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.06.2012
     */
    public static function loadRequirements() {
        Requirements::css('silvercart/css/screen/sliders/SilvercartProductGroupSliderWidget.css');
        Requirements::javascript('silvercart/script/SilvercartProductGroupSliderWidget.js');
        Requirements::javascript('silvercart/script/reflection.js');
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
        $productGroupObjects    = DataObject::get(
            'SilvercartProductGroupPage',
            'ShowInMenus = 1'
        );
        
        if ($productGroupObjects) {
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