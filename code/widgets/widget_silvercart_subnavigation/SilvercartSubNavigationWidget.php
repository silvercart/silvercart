<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
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
     * Cache for $this->getLevelsToShow()
     *
     * @var SiteTree
     */
    protected $levelsToShow = null;

    /**
     * Attributes.
     * 
     * @var array
     */
    public static $db = array(
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
     * Casted attributes.
     * 
     * @var array
     */
    public static $casting = array(
        'FrontTitle' => 'Text',
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
     * retirieves the attribute FreeText from related language class depending
     * on the set locale
     *
     * @return string
     */
    public function getFrontTitle() {
        return $this->getLanguageFieldValue('FrontTitle');
    }
    
    /**
     * getter for multilingual Title
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.06.2014
     */
    public function Title() {
        $title = $this->fieldLabel('CMSTitle');
        if (!is_null($this->Title)) {
            $title = $this->Title;
        }
        if (!empty($this->FrontTitle)) {
            $title .= ': ' . $this->FrontTitle;
        }
        return $title;
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
                'startAtLevel'                           => _t('SilvercartSubNavigationWidget.STARTATLEVEL'),
                'showSiblings'                           => _t('SilvercartSubNavigationWidget.SHOW_SIBLINGS'),
                'SilvercartSubNavigationWidgetLanguages' => _t('Silvercart.TRANSLATIONS'),
                'Title'                                  => _t('SilvercartSubNavigationWidget.LABEL_TITLE')
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * evade scaffolding performance friendly
     *
     * @return array name of fields that should be excluded 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 15.03.2013
     */
    public function excludeFromScaffolding() {
        $fields = array_merge(
            parent::excludeFromScaffolding(),
            array(
                'startAtLevel'
            )
        );
        return $fields;
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList CMS fields
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this, 'ExtraCssClasses', false);
        $source = array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7'
        );
        $startAtLevel = new DropdownField(
                'startAtLevel', 
                $this->fieldLabel('startAtLevel'), 
                $source, 
                $this->startAtLevel
        );
        $fields->insertAfter($startAtLevel, 'Title');
        return $fields;
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
        $page = false;

        foreach ($this->pageHierarchy as $pageID => $pageInfo) {
            if ((int) $pageInfo['Level'] === (int) $startAtLevel) {
                $page = $pageInfo['Page'];
                break;
            }
        }

        if ($page === false) {
            $page = Controller::curr();
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
        if ($this->levelsToShow === null) {
            $level     = SilvercartTools::getPageLevelByPageId($this->getStartPage()->ID);
            $level    += 1;

            if ($this->showSiblings) {
                $level += 1;
            }

            $this->levelsToShow = $level;
        }

        return $this->levelsToShow;
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

        if ($childPages->exists()) {

            $childLevel = $level + 1;

            foreach ($childPages as $childPage) {
                if ($childPage->ShowInMenus) {
                    $childPageStr .= $this->renderNavigation($childPage, $childLevel);
                }
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
        $lastEditedPage = DataObject::get_one('SilvercartProductGroupPage', '', true, 'LastEdited DESC');

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
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartSubNavigationWidget_Controller extends SilvercartWidget_Controller {
}