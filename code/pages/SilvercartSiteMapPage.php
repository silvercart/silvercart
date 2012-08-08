<?php
/**
 * Copyright 2012 pixeltricks GmbH
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
 * @subpackage Pages
 */

/**
 * SiteMapPage
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 25.04.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartSiteMapPage extends SilvercartMetaNavigationHolder {
    
    public static $allowed_children = 'none';
    
    public static $icon = "silvercart/images/page_icons/metanavigation_page";
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
    }
}

/**
 * SiteMapPage_Controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 25.04.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartSiteMapPage_Controller extends SilvercartMetaNavigationHolder_Controller {
    
    /**
     * Generates the sitemap
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.04.2012
     */
    public function SiteMap() {
        $siteMap                = '';
        $productGroupHolders    = DataObject::get('SilvercartProductGroupHolder',   "ClassName = 'SilvercartProductGroupHolder'");
        $metaNavigationHolders  = DataObject::get('SilvercartMetaNavigationHolder', "ClassName = 'SilvercartMetaNavigationHolder'");
        
        foreach ($productGroupHolders as $productGroupHolder) {
            $siteMap .= $this->generateSiteMap($productGroupHolder);
        }
        foreach ($metaNavigationHolders as $metaNavigationHolder) {
            $siteMap .= $this->generateSiteMap($metaNavigationHolder);
        }
        
        return $siteMap;
    }
    
    /**
     * Generates the sitemap for the given root page
     *
     * @param SiteTree $page Page to get map for
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.04.2012
     */
    public function generateSiteMap($page) {
        $page->SiteMapChildren = '';
        if ($page->Children()->Count() > 0) {
            foreach ($page->Children() as $child) {
                if ($child->ShowInMenus) {
                }
                    $child->SiteMapChildren .= $this->generateSiteMap($child);
            }
        }
        $output = $page->renderWith('SilvercartSiteMapLevel');
        return $output;
    }
    
}