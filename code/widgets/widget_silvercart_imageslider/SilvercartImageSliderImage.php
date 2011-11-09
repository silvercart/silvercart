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
 * DataObject to handle images added to a product or sth. else.
 * Provides additional (meta-)information about the image.
 *
 * @package Silvercart
 * @subpackage Products
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 21.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartImageSliderImage extends DataObject {
    
    /**
     * Attributes.
     * 
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.10.2011
     */
    public static $db = array(
        'Title' => 'VarChar',
    );
    
    /**
     * Has-one relationships.
     * 
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.10.2011
     */
    public static $has_one = array(
        'Image'     => 'Image',
        'SiteTree'  => 'SiteTree'
    );
    
    /**
     * Belongs-many-many relationships.
     * 
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.10.2011
     */
    public static $belongs_many_many = array(
        'SilvercartImageSliderWidgets' => 'SilvercartImageSliderWidget'
    );
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 19.10.2011
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        $siteTreeField = new TreeDropdownField(
            'SiteTreeID',
            _t('SilvercartImageSliderImage.LINKPAGE'),
            'SiteTree',
            'ID',
            'Title',
            false
        );
        
        $fields->addFieldToTab('Root.Main', $siteTreeField, 'Title');
        $fields->removeByName('SilvercartImageSliderWidgets');
        $fields->removeByName('SortOrder');
        
        return $fields;
    }
    
    /**
     * Returns the linked SiteTree object.
     *
     * @return mixed SiteTree|boolean false
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.10.2011
     */
    public function LinkedSite() {
        if ($this->SiteTreeID > 0) {
            return $this->SiteTree();
        }
        
        return false;
    }
}