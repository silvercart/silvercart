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
 * Provides a navigation that starts at a definable productgroup.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 26.05.2011
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
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
     * Has-many relationships.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartProductGroupNavigationWidgetLanguages' => 'SilvercartProductGroupNavigationWidgetLanguage'
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
     * retirieves the attribute FreeText from related language class depending
     * on the set locale
     *
     * @return string
     */
    public function getFrontTitle() {
        return $this->getLanguageFieldValue('FrontTitle');
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2013
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'FrontTitle'                => _t('WidgetSetWidget.FRONTTITLE'),
                    'FieldLabel'                   => _t('SilvercartProductGroupItemsWidget.STOREADMIN_FIELDLABEL'),
                    'levelsToShow'                 => _t('SilvercartProductGroupNavigationWidget.LEVELS_TO_SHOW'),
                    'ShowAllLevels'                => _t('SilvercartProductGroupNavigationWidget.SHOW_ALL_LEVELS'),
                    'Title'                     => _t('SilvercartProductGroupNavigationWidget.TITLE'),
                    'CMSTitle'                  => _t('SilvercartProductGroupNavigationWidget.CMSTITLE'),
                    'Description'               => _t('SilvercartProductGroupNavigationWidget.DESCRIPTION'),
                    'expandActiveSectionOnly'      => _t('SilvercartProductGroupNavigationWidget.EXPAND_ACTIVE_SECTION_ONLY'),
                    'SilvercartProductGroupPageID' => _t('SilvercartProductGroupPage.SINGULARNAME'),
                    'SilvercartProductGroupNavigationWidgetLanguages' => _t('SilvercartProductGroupNavigationWidgetLanguage.PLURALNAME'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns a list of fields to exclude from scaffolding
     * 
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.04.2013
     */
    public function excludeFromScaffolding() {
        $fields = array_merge(
            parent::excludeFromScaffolding(),
            array(
                'levelsToShow',
                'SilvercartProductGroupPageID'
            )
                );
        return $fields;
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this);
        $levels = array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '0' => $this->fieldLabel('ShowAllLevels')
        );
        $levelsToShow = new DropdownField(
                'levelsToShow',
                $this->fieldLabel('levelsToShow'),
                $levels
        );
        $fields->insertBefore($levelsToShow, 'ExtraCssClasses');
        $productGroupField = new GroupedDropdownField(
            'SilvercartProductGroupPageID',
            $this->fieldLabel('SilvercartProductGroupPageID'),
            SilvercartProductGroupHolder_Controller::getAllProductGroupsWithChildrenAsArray(),
            $this->SilvercartProductGroupPageID
        );
        $fields->insertBefore($productGroupField, 'ExtraCssClasses');
        return $fields;
    }
    
    /**
     * Returns the title of this widget.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2013
     */
    public function Title() {
        $title = $this->fieldLabel('Title');
        if (!empty($this->FrontTitle)) {
            $title .= ': ' . $this->FrontTitle;
        }
        return $title;
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
    
    /**
     * Returns the extra css classes.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.04.2013
     */
    public function ExtraCssClasses() {
        return 'silvercart-product-group-navigation-widget';
    }
}

/**
 * Provides a navigation that starts at a definable productgroup.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 20.03.2013
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
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
        
        $productgroupPage = SilvercartProductGroupPage::get()->byID($this->SilvercartProductGroupPageID);
        
        if (!$productgroupPage) {
            $productgroupPage = SilvercartProductGroupHolder::get()->byID($this->SilvercartProductGroupPageID);
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
                 SilvercartTools::findPageIdInHierarchy($rootPage->getParent()->ID) === false)) {
                
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

                $data = new ArrayData(array(
                    'MenuTitle'         => $rootPage->getMenuTitle(),
                    'Title'             => $rootPage->getTitle(),
                    'Link'              => $rootPage->Link(),
                    'LinkOrSection'     => $rootPage->LinkOrSection(),
                    'ChildPages'        => $childPageStr,
                    'IsActivePage'      => $isActivePage,
                    'IsActiveSection'   => $isActiveSection,
                    'Level'             => $level,
                ));
                $renderStr .= $data->renderWith('SilvercartProductGroupNavigationWidgetEntry');
                
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
        $key            = $this->SilvercartProductGroupPageID.'_'.$this->LastEdited.'_';
        $lastEditedPage = SilvercartProductGroupPage::get()->sort('LastEdited DESC')->first();

        if ($lastEditedPage) {
            $key .= '_'.$lastEditedPage->LastEdited;
        }

        $productGroupPage = SiteTree::get()->byID($this->SilvercartProductGroupPageID);

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