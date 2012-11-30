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
 * Provides a navigation of the current section and their children.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 05.10.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartSubNavigationWidget extends SilvercartWidget {

    /**
     * Contains the page hierarchy.
     *
     * @var array
     *
     * @since 18.10.2012
     */
    protected $pageHierarchy = null;

    /**
     * Attributes.
     * 
     * @var array
     */
    public static $db = array(
        'Title'        => 'VarChar(255)',
        'startAtLevel' => 'Int'
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
            parent::fieldLabels($includerelations),
            array(
                'Title'         => _t('SilvercartSubNavigationWidget.TITLE'),
                'CMSTitle'      => _t('SilvercartSubNavigationWidget.CMSTITLE'),
                'Description'   => _t('SilvercartSubNavigationWidget.DESCRIPTION'),
                'startAtLevel'  => _t('SilvercartSubNavigationWidget.STARTATLEVEL'),
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function getCMSFields() {
        $fields         = parent::getCMSFields();
        $fieldLabels    = $this->fieldLabels();
        
        $titleField         = new TextField('Title',        $fieldLabels['Title']);
        $startAtLevelField  = new TextField('startAtLevel', $fieldLabels['startAtLevel']);

        $fields->push($titleField);
        $fields->push($startAtLevelField);

        return $fields;
    }
    
    /**
     * Returns the title of this widget.
     * 
     * @return string
     * 
     * @author Carolin Woerner <cwoerner@pixeltricks.de>
     * @since 11.10.2012
     */
    public function Title() {
        $title = $this->fieldLabel('CMSTitle');
        if (!is_null($this->Title)) {
            $title = $this->Title;
        }
        return $title;
    }
    
    /**
     * Returns the title of this widget for display in the WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.07.2012
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
     * @since 13.07.2012
     */
    public function Description() {
        return $this->fieldLabel('Description');
    }

    /**
     * Returns start page for the submenu hierarchy.
     *
     * @return SiteTree
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.10.2012
     */
    public function getStartPage() {
        if ($this->startAtLevel != '0') {
            $startPage = $this->findStartPage($this->startAtLevel);
        } else {
            $startPage = Controller::curr();
        }

        return $startPage;
    }

    /**
     * Returns the rendered navigation as HTML string.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.10.2012
     */
    public function getNavigation() {
        $navigation = $this->renderNavigation($this->getStartPage());

        if (empty($navigation)) {
            $hasNavigation = false;
        } else {
            $hasNavigation = true;
        }

        return new DataObject(array(
            'RootPage' => $this->getStartPage(),
            'HasMenu'  => $hasNavigation,
            'Menu'     => $navigation
        ));
    }

    /**
     * Tries to find the start page by bubbling up from the current page and
     * comparing the given startAtLevel with it's parents.
     *
     * @param Int $startAtLevel The start level for the hierarchy
     *
     * @return SiteTree
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.10.2012
     */
    public function findStartPage($startAtLevel) {
        $page      = Controller::curr();
        $hierarchy = SilvercartTools::getPageHierarchy();

        foreach ($hierarchy as $pageID => $pageInfo) {
            if ($pageInfo['Level'] == $startAtLevel) {
                $page = $pageInfo['Page'];
                break;
            }
        }

        return $page;
    }

    /**
     * Returns the number of levels to show.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.10.2012
     */
    public function getLevelsToShow() {
        $level     = SilvercartTools::getPageLevelByPageId(Controller::curr()->ID);
        $level    += 1;

        return $level;
    }

    /**
     * Renders the navigation.
     *
     * @param SiteTree $rootPage The root page to start with
     * @param int      $level    The current level
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.05.2012
     */
    public function renderNavigation($rootPage, $level = 0) {
        $renderStr      = '';
        $isActivePage   = false;
        $level++;

        if ($this->getLevelsToShow() != 0 &&
            $level > $this->getLevelsToShow()) {

           return $renderStr;
        }

        $childPages = $rootPage->Children();
        $childPageStr = '';

        if ($childPages &&
            $childPages->Count() > 0) {

            foreach ($childPages as $childPage) {
                $childPageStr .= $this->renderNavigation($childPage, $level);
            }
        }

        if (Controller::curr()->ID === $rootPage->ID) {
            $isActivePage = true;
        }

        $showChildPages = false;
        $isSectionPage  = false;
        if (SilvercartTools::findPageIdInHierarchy($rootPage->ID)) {
            $showChildPages = true;
            $isSectionPage  = true;
        }

        if (method_exists($rootPage, 'OriginalLink')) {
            $link = $rootPage->OriginalLink();
        } else {
            $link = $rootPage->Link();
        }
 
        $data = array(
            'MenuTitle'         => $rootPage->getMenuTitle(),
            'Title'             => $rootPage->getTitle(),
            'Link'              => $link,
            'ShowChildPages'    => $showChildPages,
            'ChildPages'        => $childPageStr,
            'IsActivePage'      => $isActivePage,
            'IsSectionPage'     => $isSectionPage
        );

        $parser     = new SSViewer('SilvercartSubNavigationWidgetEntry');
        $renderStr .= $parser->process(new DataObject($data));

        return $renderStr;
    }

    /**
     * Returns the cache key for the current configuration.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.10.2012
     */
    public function NavigationCacheKey() {
        $page           = Controller::curr();
        $key            = $page->ID.'_'.$this->LastEdited.'_';
        $lastEditedPage = DataObject::get_one(
            'SilvercartProductGroupPage',
            '',
            true,
            "LastEdited DESC"
        );

        if ($lastEditedPage) {
            $key .= '_'.$lastEditedPage->LastEdited;
        }

        $key .= '_'.$page->LastEdited;

        return $key;
    }
}

/**
 * Provides a navigation of the current section and their childs.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 05.10.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartSubNavigationWidget_Controller extends SilvercartWidget_Controller {
}