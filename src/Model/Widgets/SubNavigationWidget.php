<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\ProductGroupPage;
use SilverCart\Model\Translation\TranslationTools;
use SilverCart\Model\Widgets\Widget;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\ArrayData;
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
 * 
 * @property int    $startAtLevel Start At Level
 * @property bool   $showSiblings Show Siblings
 * @property string $FrontTitle   Front Title
 * 
 * @method \SilverStripe\ORM\HasManyList SubNavigationWidgetTranslations() Returns the related translations.
 */
class SubNavigationWidget extends Widget
{
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
    private static $db = [
        'startAtLevel' => 'Int',
        'showSiblings' => 'Boolean(0)',
    ];
    /**
     * Has-many relationships.
     *
     * @var array
     */
    private static $has_many = [
        'SubNavigationWidgetTranslations' => SubNavigationWidgetTranslation::class,
    ];
    /**
     * Casted attributes.
     * 
     * @var array
     */
    private static $casting = [
        'FrontTitle' => 'Text',
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartSubNavigationWidget';
    
    /**
     * Load the page hierarchy.
     *
     * @param array    $record       Initial record content, or rehydrated record content, depending on $creationType
     * @param int|bool $creationType Set to DataObject::CREATE_OBJECT, DataObject::CREATE_HYDRATED, or DataObject::CREATE_SINGLETON. Used by SilverStripe internals as best left as the default by regular users.
     * @param array    $queryParams  List of DataQuery params necessary to lazy load, or load related objects.
     */
    public function __construct($record = [], $creationType = self::CREATE_OBJECT, $queryParams = [])
    {
        parent::__construct($record, $creationType, $queryParams);
        if (Tools::isBackendEnvironment()) {
            return;
        }
        $this->pageHierarchy = Tools::getPageHierarchy(Controller::curr()->data());
    }

    /**
     * retirieves the attribute FreeText from related language class depending
     * on the set locale
     *
     * @return string
     */
    public function getFrontTitle() : string
    {
        return (string) $this->getTranslationFieldValue('FrontTitle');
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param bool $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            [
                'startAtLevel'                    => _t(SubNavigationWidget::class . '.STARTATLEVEL', 'Start at level'),
                'showSiblings'                    => _t(SubNavigationWidget::class . '.SHOW_SIBLINGS', 'Show pages on same level'),
                'SubNavigationWidgetTranslations' => _t(TranslationTools::class . '.TRANSLATIONS', 'Translations'),
            ]
        );
        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * evade scaffolding performance friendly
     *
     * @return array name of fields that should be excluded 
     */
    public function excludeFromScaffolding() : array
    {
        $fields = array_merge(
            parent::excludeFromScaffolding(),
            [
                'startAtLevel'
            ]
        );
        return $fields;
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList CMS fields
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $source = [
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
            ];
            $startAtLevel = DropdownField::create(
                    'startAtLevel', 
                    $this->fieldLabel('startAtLevel'), 
                    $source, 
                    $this->startAtLevel
            );
            $fields->insertAfter($startAtLevel, 'FrontTitle');
        });
        return parent::getCMSFields();
    }

    /**
     * Returns start page for the submenu hierarchy.
     *
     * @return SiteTree
     */
    public function getStartPage() : SiteTree
    {
        if ($this->startPage === null) {
            if ($this->startAtLevel != '0') {
                $startPage = $this->findStartPage($this->startAtLevel);
            } else {
                $startPage = Controller::curr()->data();
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
     * @return ArrayData
     */
    public function getNavigation() : ArrayData
    {
        $navigation = $this->renderNavigation($this->getStartPage());
        if (empty($navigation)) {
            $hasNavigation = false;
        } else {
            $hasNavigation = true;
        }
        return ArrayData::create([
            'RootPage' => $this->getStartPage(),
            'HasMenu'  => $hasNavigation,
            'Menu'     => $navigation
        ]);
    }

    /**
     * Tries to find the start page by bubbling up from the current page and
     * comparing the given startAtLevel with it's parents.
     *
     * @param int $startAtLevel The start level for the hierarchy
     *
     * @return SiteTree|null
     */
    public function findStartPage(int $startAtLevel) : ?SiteTree
    {
        $page = null;
        foreach ($this->pageHierarchy as $pageID => $pageInfo) {
            if ((int) $pageInfo['Level'] === (int) $startAtLevel) {
                $page = $pageInfo['Page'];
                break;
            }
        }
        if ($page === false) {
            $page = Controller::curr()->data();
        }
        return $page;
    }

    /**
     * Returns the number of levels to show.
     *
     * @return int
     */
    public function getLevelsToShow() : int
    {
        if ($this->levelsToShow === null) {
            $level  = Tools::getPageLevelByPageId($this->getStartPage()->ID);
            $level += 1;
            if ($this->showSiblings) {
                $level += 1;
            }
            $this->levelsToShow = $level;
        }
        return (int) $this->levelsToShow;
    }

    /**
     * Renders the navigation.
     *
     * @param SiteTree $rootPage The root page to start with
     * @param int      $level    The current level
     *
     * @return DBHTMLText
     */
    public function renderNavigation(SiteTree $rootPage, int $level = 0) : DBHTMLText
    {
        $renderStr      = '';
        $isActivePage   = false;

        if ($this->getLevelsToShow() !== 0
         && $level > $this->getLevelsToShow()
        ) {
           return DBHTMLText::create()->setValue($renderStr);
        }
        $childPages   = $rootPage->Children();
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
        $data = [
            'MenuTitle'         => $rootPage->getMenuTitle(),
            'Title'             => $rootPage->getTitle(),
            'Link'              => $link,
            'ShowChildPages'    => $showChildPages,
            'ShowSiblings'      => $this->showSiblings,
            'ChildPages'        => DBHTMLText::create()->setValue($childPageStr),
            'IsActivePage'      => $isActivePage,
            'IsSectionPage'     => $isSectionPage,
            'IsRootPage'        => $isRootPage,
            'ProductGroup'      => $rootPage,
        ];
        $parser     = SSViewer::create("{$this->ClassName}Entry");
        $renderStr .= $parser->process(ArrayData::create($data));
        return DBHTMLText::create()->setValue($renderStr);
    }

    /**
     * Returns the cache key for the current configuration.
     *
     * @return string
     */
    public function NavigationCacheKey() {
        $key            = "{$this->ProductGroupPageID}_{$this->LastEdited}_";
        $lastEditedPage = ProductGroupPage::get()->sort('LastEdited DESC')->first();
        if ($lastEditedPage) {
            $key .= "_{$lastEditedPage->LastEdited}";
        }
        if (Controller::has_curr()) {
            $currentPage = Controller::curr()->data();
            $key        .= "_{$currentPage->ID}";
        }
        if (Director::isDev()) {
            $key .= uniqid();
        }
        return $key;
    }
}