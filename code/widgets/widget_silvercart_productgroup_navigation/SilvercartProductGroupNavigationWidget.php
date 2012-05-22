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
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public static $db = array(
        'SilvercartProductGroupPageID'  => 'Int',
        'levelsToShow'                  => 'Int'
    );
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function getCMSFields() {
        $fields            = parent::getCMSFields();
        $productGroupField = new GroupedDropdownField(
            'SilvercartProductGroupPageID',
            _t('SilvercartProductGroupItemsWidget.STOREADMIN_FIELDLABEL'),
            SilvercartProductGroupHolder_Controller::getRecursiveProductGroupsForGroupedDropdownAsArray(null, false, true),
            $this->SilvercartProductGroupPageID
        );
        $levelsToShowField = new DropdownField(
            'levelsToShow',
            _t('SilvercartProductGroupNavigationWidget.LEVELS_TO_SHOW'),
            array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '0' => _t('SilvercartProductGroupNavigationWidget.SHOW_ALL_LEVELS')
            ),
            $this->levelsToShow
        );
        
        $fields->push($productGroupField);
        $fields->push($levelsToShowField);
        
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
        return _t('SilvercartProductGroupNavigationWidget.TITLE');
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
        return _t('SilvercartProductGroupNavigationWidget.CMSTITLE');
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
        return _t('SilvercartProductGroupNavigationWidget.DESCRIPTION');
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
        $productgroupPageSiteTree = ModelAsController::controller_for($productgroupPage);
        $navigation               = '';
        
        foreach ($productgroupPageSiteTree->Children() as $childPage) {
            $navigation .= $this->renderProductGroupNavigation($childPage);
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
     * @param SiteTree $rootPage The root page to start with
     * @param int      $level    The current level
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.05.2012
     */
    public function renderProductGroupNavigation($rootPage, $level = 0) {
        $renderStr = '';
        $level++;
        
        if ($this->levelsToShow != 0 &&
            $level > $this->levelsToShow) {
            
           return $renderStr; 
        }
        
        $childPages = $rootPage->Children();
        $childPageStr = '';
        
        if ($childPages &&
            $childPages->Count() > 0) {
            
            foreach ($childPages as $childPage) {
                $childPageStr .= $this->renderProductGroupNavigation($childPage, $level);
            }
        }
         
        $data = array(
            'MenuTitle'  => $rootPage->getMenuTitle(),
            'Title'      => $rootPage->getTitle(),
            'Link'       => $rootPage->Link(),
            'ChildPages' => $childPageStr
        );
        
        $parser     = new SSViewer('SilvercartProductGroupNavigationWidgetEntry');
        $renderStr .= $parser->process(new DataObject($data));
        
        return $renderStr;
    }
}