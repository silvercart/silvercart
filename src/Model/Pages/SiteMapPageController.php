<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\MetaNavigationHolder;
use SilverCart\Model\Pages\ProductGroupHolder;

/**
 * SiteMapPage Controller class.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SiteMapPageController extends MetaNavigationHolderController {
    
    /**
     * Generates the sitemap
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.04.2012
     */
    public function SiteMap() {
        $siteMap                = '';
        $productGroupHolders    = ProductGroupHolder::get()->filter('ClassName', ProductGroupHolder::class);
        $metaNavigationHolders  = MetaNavigationHolder::get()->filter('ClassName', MetaNavigationHolder::class);
        
        foreach ($productGroupHolders as $productGroupHolder) {
            $siteMap .= $this->generateSiteMap($productGroupHolder);
        }
        foreach ($metaNavigationHolders as $metaNavigationHolder) {
            $siteMap .= $this->generateSiteMap($metaNavigationHolder);
        }
        
        return Tools::string2html($siteMap);
    }
    
    /**
     * Generates the sitemap for the given root page
     *
     * @param SiteTree $page Page to get map for
     * 
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.04.2018
     */
    public function generateSiteMap($page) {
        $page->SiteMapChildren = '';
        if ($page->Children()->count() > 0) {
            foreach ($page->Children() as $child) {
                $child->SiteMapChildren = $this->generateSiteMap($child);
            }
        }
        $output = $page->renderWith('SilverCart/Model/Pages/Includes/SiteMapLevel');
        return Tools::string2html($output);
    }
    
}