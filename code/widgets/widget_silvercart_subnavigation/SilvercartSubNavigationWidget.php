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
     * Cache for $this->getStartPage()
     *
     * @var SiteTree
     */
    protected $startPage = null;

    /**
     * Attributes.
     * 
     * @var array
     */
    public static $db = array(
        'FrontTitle'   => 'VarChar(255)',
        'Title'        => 'VarChar(255)',
        'startAtLevel' => 'Int',
        'showSiblings' => 'Boolean(0)',
    );

    /**
     * Has-many relationships.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartSubNavigationWidgetLanguages' => 'SilvercartSubNavigationWidgetLanguage'
    );

    /**
     * Load the page hierarchy.
     *
     * @param array|null $record      This will be null for a new database record.
     * @param boolean    $isSingleton Set this to true if this is a singleton() object, a stub for calling methods.
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2012-12-10
     */
    public function __construct($record = null, $isSingleton = false) {
        parent::__construct($record, $isSingleton);

        $this->pageHierarchy = SilvercartTools::getPageHierarchy(Controller::curr());
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
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
                'showSiblings'  => _t('SilvercartSubNavigationWidget.SHOW_SIBLINGS'),
                'FrontTitle'    => _t('SilvercartWidget.FRONTTITLE'),
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

        $frontTitleField    = new TextField('FrontTitle',   $this->fieldLabel('FrontTitle'));
        $startAtLevelField  = new TextField('startAtLevel', $fieldLabels['startAtLevel']);
        $siblingsField      = new CheckboxField('showSiblings', $fieldLabels['showSiblings']);

        $fields->push($frontTitleField);
        $fields->push($startAtLevelField);
        $fields->push($siblingsField);

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
        if ($this->startPage === null) {
            if ($this->startAtLevel != '0') {
                $startPage = $this->findStartPage($this->startAtLevel);
            } else {
                $startPage = Controller::curr();
            }

            if ($this->showSiblings) {
                $parentPage = $startPage->getParent();

                if ($parentPage) {
                    $startPage = $parentPage;
                }
            }

            $this->startPage = $startPage;
        }

        return $this->startPage;
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
        $page = Controller::curr();

        foreach ($this->pageHierarchy as $pageID => $pageInfo) {
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
        $level     = SilvercartTools::getPageLevelByPageId($this->getStartPage()->ID);
        $level    += 1;

        if ($this->showSiblings) {
            $level += 1;
        }

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

        if ($this->getLevelsToShow() != 0 &&
            $level > $this->getLevelsToShow()) {

           return $renderStr;
        }

        $childPages = $rootPage->Children();
        $childPageStr = '';

        if ($childPages &&
            $childPages->Count() > 0) {

            $childLevel = $level + 1;

            foreach ($childPages as $childPage) {
                $childPageStr .= $this->renderNavigation($childPage, $childLevel);
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

        if ($rootPage->ID === $this->getStartPage()->ID) {
            $isRootPage = true;
        } else {
            $isRootPage = false;
        }

        $data = array(
            'MenuTitle'         => $rootPage->getMenuTitle(),
            'Title'             => $rootPage->getTitle(),
            'Link'              => $link,
            'ShowChildPages'    => $showChildPages,
            'ShowSiblings'      => $this->showSiblings,
            'ChildPages'        => $childPageStr,
            'IsActivePage'      => $isActivePage,
            'IsSectionPage'     => $isSectionPage,
            'IsRootPage'        => $isRootPage,
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
     * @since 03.07.2012
     */
    public function NavigationCacheKey() {
        $key            = $this->SilvercartProductGroupPageID.'_'.$this->LastEdited.'_';
        $lastEditedPage = false;

        foreach ($this->pageHierarchy as $pageId => $pageInfo) {
            if (!$lastEditedPage ||
                 $lastEditedPage->LastEdited < $pageInfo['Page']->LastEdited) {

                $lastEditedPage = $pageInfo['Page'];
            }
        }

        if ($lastEditedPage) {
            $key .= '_'.$lastEditedPage->LastEdited;
        }

        $currentPage = Controller::curr();

        if ($currentPage) {
            $key .= '_'.$currentPage->ID;
        }

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