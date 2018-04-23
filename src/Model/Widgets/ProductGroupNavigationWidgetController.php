<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\ProductGroupPage;
use SilverCart\Model\Pages\ProductGroupHolder;
use SilverCart\Model\Widgets\WidgetController;
use SilverStripe\CMS\Controllers\ModelAsController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\ArrayData;

/**
 * ProductGroupNavigationWidget Controller.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductGroupNavigationWidgetController extends WidgetController {

    /**
     * Returns a page that acts as the root node for a navigation block.
     * 
     * @return ProductGroupPage
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Navigation() {
        if (!$this->ProductGroupPageID) {
            return false;
        }
        
        $productgroupPage = ProductGroupPage::get()->byID($this->ProductGroupPageID);
        
        if (!$productgroupPage) {
            $productgroupPage = ProductGroupHolder::get()->byID($this->ProductGroupPageID);
        }
        
        if (!$productgroupPage) {
            return false;
        }

        $currentPage              = Controller::curr();
        $branchSitetree           = Tools::getPageHierarchy(Controller::curr());
        $productgroupPageSiteTree = ModelAsController::controller_for($productgroupPage);
        $navigation               = '';
        
        foreach ($productgroupPageSiteTree->Children() as $childPage) {
            $navigation .= $this->renderProductGroupNavigation($childPage, $currentPage, 0, $branchSitetree);
        }
        
        if (empty($navigation)) {
            $hasNavigation = false;
        } else {
            $hasNavigation = true;
        }
        
        return new DataObject([
            'RootPage' => $productgroupPageSiteTree,
            'HasMenu'  => $hasNavigation,
            'Menu'     => Tools::string2html($navigation),
        ]);
    }
    
    /**
     * Renders the product group navigation.
     *
     * @param SiteTree $rootPage    The root page to start with
     * @param SiteTree $currentPage The current SiteTree object
     * @param int      $level       The current level
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.11.2014
     */
    public function renderProductGroupNavigation($rootPage, $currentPage, $level = 0) {
        $renderStr      = '';
        $isActivePage   = false;
        $level++;

        if ($this->levelsToShow == 0 ||
            $level <= $this->levelsToShow) {
            
            if (!($this->expandActiveSectionOnly &&
                 (($this->levelsToShow != 0 &&
                   $level > $this->levelsToShow) ||
                  $level > 1) &&
                 Tools::findPageIdInHierarchy($rootPage->getParent()->ID) === false)) {
                
                $childPages   = $rootPage->Children();
                $childPageStr = '';

                if ($childPages &&
                    $childPages->Count() > 0) {

                    foreach ($childPages as $childPage) {
                        $childPageStr .= $this->renderProductGroupNavigation($childPage, $currentPage, $level);
                    }
                }

                if (Controller::curr()->ID === $rootPage->ID) {
                    $isActivePage = true;
                }

                $isActiveSection = false;
                if (Tools::findPageIdInHierarchy($rootPage->ID) ||
                    $rootPage->ID === $currentPage->ID) {
                    $isActiveSection = true;
                }

                $data = new ArrayData([
                    'MenuTitle'         => $rootPage->getMenuTitle(),
                    'Title'             => $rootPage->getTitle(),
                    'Link'              => $rootPage->Link(),
                    'LinkOrSection'     => $rootPage->LinkOrSection(),
                    'ChildPages'        => Tools::string2html($childPageStr),
                    'IsActivePage'      => $isActivePage,
                    'IsActiveSection'   => $isActiveSection,
                    'Level'             => $level,
                ]);
                $renderStr .= $data->renderWith($this->getWidget()->ClassName . 'Entry');
                
            }
            
        }
        
        return $renderStr;
    }

    /**
     * Returns the cache key for the current configuration.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.06.2014
     */
    public function NavigationCacheKey() {
        $key            = $this->ProductGroupPageID . '_' . $this->LastEdited . '_';
        $lastEditedPage = ProductGroupPage::get()->sort('LastEdited DESC')->first();

        if ($lastEditedPage) {
            $key .= '_' . $lastEditedPage->LastEdited;
        }

        $productGroupPage = SiteTree::get()->byID($this->ProductGroupPageID);

        if ($productGroupPage) {
            $key .= '_' . $productGroupPage->LastEdited;
        }

        $currentPage = Controller::curr();

        if ($currentPage) {
            $key .= '_' . $currentPage->ID;
        }

        return $key;
    }
}