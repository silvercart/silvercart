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
 * Provides a navigation that starts at a definable productgroup.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 26.05.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartProductGroupNavigationWidget extends SilvercartWidget {
    
    /**
     * Attributes.
     * 
     * @var array
     */
    public static $db = array(
        'SilvercartProductGroupPageID'  => 'Int',
        'levelsToShow'                  => 'Int',
        'expandActiveSectionOnly'       => 'Boolean(0)'
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
                    'FieldLabel'                => _t('SilvercartProductGroupItemsWidget.STOREADMIN_FIELDLABEL'),
                    'levelsToShow'              => _t('SilvercartProductGroupNavigationWidget.LEVELS_TO_SHOW'),
                    'ShowAllLevels'             => _t('SilvercartProductGroupNavigationWidget.SHOW_ALL_LEVELS'),
                    'Title'                     => _t('SilvercartProductGroupNavigationWidget.TITLE'),
                    'CMSTitle'                  => _t('SilvercartProductGroupNavigationWidget.CMSTITLE'),
                    'Description'               => _t('SilvercartProductGroupNavigationWidget.DESCRIPTION'),
                    'expandActiveSectionOnly'   => _t('SilvercartProductGroupNavigationWidget.EXPAND_ACTIVE_SECTION_ONLY'),

                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function getCMSFields() {
        $fields            = parent::getCMSFields();
        $productGroupField = new GroupedDropdownField(
            'SilvercartProductGroupPageID',
            $this->fieldLabel('FieldLabel'),
            SilvercartProductGroupHolder_Controller::getAllProductGroupsWithChildrenAsArray(),
            $this->SilvercartProductGroupPageID
        );
        $levelsToShowField = new DropdownField(
            'levelsToShow',
            $this->fieldLabel('LevelsToShow'),
            array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '0' => $this->fieldLabel('ShowAllLevels')
            ),
            $this->levelsToShow
        );
        $expandActiveSectionOnlyField = new CheckboxField(
            'expandActiveSectionOnly',
            _t('SilvercartProductGroupNavigationWidget.EXPAND_ACTIVE_SECTION_ONLY'),
            $this->expandActiveSectionOnly
        );

        $fields->push($productGroupField);
        $fields->push($levelsToShowField);
        $fields->push($expandActiveSectionOnlyField);

        return $fields;
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
}

/**
 * Provides a navigation that starts at a definable productgroup.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 26.05.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartProductGroupNavigationWidget_Controller extends SilvercartWidget_Controller {

    /**
     * Returns a page that acts as the root node for a navigation block.
     * 
     * @return SilvercartProductGroupPage
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Navigation() {
        if (!$this->SilvercartProductGroupPageID) {
            return false;
        }
        
        $productgroupPage = DataObject::get_by_id(
            'SilvercartProductGroupPage',
            $this->SilvercartProductGroupPageID
        );
        
        if (!$productgroupPage) {
            $productgroupPage = DataObject::get_by_id(
                'SilvercartProductGroupHolder',
                $this->SilvercartProductGroupPageID
            );
        }
        
        if (!$productgroupPage) {
            return false;
        }

        $currentPage              = Controller::curr();
        $branchSitetree           = SilvercartTools::getPageHierarchy(Controller::curr());
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
        
        return new DataObject(array(
            'RootPage' => $productgroupPageSiteTree,
            'HasMenu'  => $hasNavigation,
            'Menu'     => $navigation
        ));
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.05.2012
     */
    public function renderProductGroupNavigation($rootPage, $currentPage, $level = 0) {
        $renderStr      = '';
        $isActivePage   = false;
        $level++;

        if (($this->levelsToShow != 0 &&
             $level > $this->levelsToShow
            )) {
            
           return $renderStr; 
        }

        if (
            (
                (
                    $this->levelsToShow != 0 &&
                    $level > $this->levelsToShow
                ) ||
                $level > 1
            ) &&
            SilvercartTools::findPageIdInHierarchy($rootPage->getParent()->ID) === false
        ) {

            return $renderStr;
        }
        
        $childPages = $rootPage->Children();
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

        if (SilvercartTools::findPageIdInHierarchy($rootPage->ID) ||
            $rootPage->ID === $currentPage->ID) {

            $isActiveSection = true;
        } else {
            $isActiveSection = false;
        }
         
        $data = array(
            'MenuTitle'         => $rootPage->getMenuTitle(),
            'Title'             => $rootPage->getTitle(),
            'Link'              => $rootPage->Link(),
            'ChildPages'        => $childPageStr,
            'IsActivePage'      => $isActivePage,
            'IsActiveSection'   => $isActiveSection
        );
        
        $parser     = new SSViewer('SilvercartProductGroupNavigationWidgetEntry');
        $renderStr .= $parser->process(new DataObject($data));
        
        return $renderStr;
    }

    /**
     * Returns the cache key for the current configuration.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.07.2012
     */
    public function NavigationCacheKey() {
        $key            = $this->SilvercartProductGroupPageID.'_'.$this->LastEdited.'_';
        $lastEditedPage = DataObject::get_one(
            'SilvercartProductGroupPage',
            '',
            true,
            "LastEdited DESC"
        );

        if ($lastEditedPage) {
            $key .= '_'.$lastEditedPage->LastEdited;
        }

        $productGroupPage = DataObject::get_by_id('SiteTree', $this->SilvercartProductGroupPageID);

        if ($productGroupPage) {
            $key .= '_'.$productGroupPage->LastEdited;
        }

        $currentPage = Controller::curr();

        if ($currentPage) {
            $key .= '_'.$currentPage->ID;
        }

        return $key;
    }
}