<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @copyright 2013 pixeltricks GmbH
 * @since 25.04.2012
 * @license see license file in modules root directory
 */
class SilvercartSiteMapPage extends SilvercartMetaNavigationHolder {
    
    public static $allowed_children = 'none';
    
    /**
     * Icon to display in CMS site tree
     *
     * @var string
     */
    public static $icon = "silvercart/img/page_icons/metanavigation_page";
    
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
 * @copyright 2013 pixeltricks GmbH
 * @since 25.04.2012
 * @license see license file in modules root directory
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
        $productGroupHolders    = SilvercartProductGroupHolder::get()->filter('ClassName', 'SilvercartProductGroupHolder');
        $metaNavigationHolders  = SilvercartMetaNavigationHolder::get()->filter('ClassName', 'SilvercartMetaNavigationHolder');
        
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
        if ($page->Children()->count() > 0) {
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
