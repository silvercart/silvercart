<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\ProductGroupPage;
use SilverCart\Model\Translation\TranslationTools;
use SilverCart\Model\Widgets\Widget;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\SSViewer;

/**
 * Provides a navigation of the current section and their children.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SubNavigationWidget extends Widget {

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
    private static $db = array(
        'startAtLevel' => 'Int',
        'showSiblings' => 'Boolean(0)',
    );

    /**
     * Has-many relationships.
     *
     * @var array
     */
    private static $has_many = array(
        'SubNavigationWidgetTranslations' => SubNavigationWidgetTranslation::class,
    );

    /**
     * Casted attributes.
     * 
     * @var array
     */
    private static $casting = array(
        'FrontTitle' => 'Text',
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartSubNavigationWidget';
    
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

        $this->pageHierarchy = Tools::getPageHierarchy(Controller::curr());
    }

    /**
     * retirieves the attribute FreeText from related language class depending
     * on the set locale
     *
     * @return string
     */
    public function getFrontTitle() {
        return $this->getTranslationFieldValue('FrontTitle');
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
                'startAtLevel'                    => _t(SubNavigationWidget::class . '.STARTATLEVEL', 'Start at level'),
                'showSiblings'                    => _t(SubNavigationWidget::class . '.SHOW_SIBLINGS', 'Show pages on same level'),
                'SubNavigationWidgetTranslations' => _t(TranslationTools::class . '.TRANSLATIONS', 'Translations'),
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
        $fields = parent::getCMSFields();
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
        $fields->insertAfter($startAtLevel, 'FrontTitle');
        return $fields;
    }

    /**
     * Returns start page for the submenu hierarchy.
     *
     * @return SiteTree
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
            $level     = Tools::getPageLevelByPageId($this->getStartPage()->ID);
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
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 19.04.2018
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
        if (Tools::findPageIdInHierarchy($rootPage->ID)) {
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
            'ChildPages'        => Tools::string2html($childPageStr),
            'IsActivePage'      => $isActivePage,
            'IsSectionPage'     => $isSectionPage,
            'IsRootPage'        => $isRootPage,
            'ProductGroup'      => $rootPage,
        );

        $parser     = new SSViewer($this->ClassName . 'Entry');
        $renderStr .= $parser->process(new DataObject($data));
        
        return Tools::string2html($renderStr);
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
            $key .= '_'.$lastEditedPage->LastEdited;
        }

        $currentPage = Controller::curr();

        if ($currentPage) {
            $key .= '_'.$currentPage->ID;
        }

        return $key;
    }
}