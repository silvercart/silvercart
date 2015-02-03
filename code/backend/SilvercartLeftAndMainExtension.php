<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Backend
 */

/**
 * Extension for the LeftAndMain class.
 * 
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 04.04.2013
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartLeftAndMainExtension extends DataExtension {
    
    /**
     * List of allowed actions
     *
     * @var array
     */
    public static $allowed_actions = array(
        'isUpdateAvailable',
        'createsitetreetranslation',
        'publishsitetree',
    );
    
    /**
     * ModelAdmins to ignore.
     *
     * @var array
     */
    public static $model_admins_to_ignore = array();
    
    /**
     * List of additional CSS files to load in backend.
     *
     * @var array
     */
    public static $additional_css_files = array();
    
    /**
     * Adds an additional CSS file to load in backend.
     * 
     * @param string $file_name File name
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.02.2015
     */
    public static function add_additional_css_file($file_name) {
        self::$additional_css_files[] = $file_name;
    }

    /**
     * Injects some custom javascript to provide instant loading of DataObject
     * tables.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2013
     */
    public function onAfterInit() {
        if (Director::is_ajax()) {
            return true;
        }
        $baseUrl = SilvercartTools::getBaseURLSegment();
        Requirements::javascript($baseUrl . 'silvercart/script/SilvercartLeftAndMain.js');
        Requirements::css('silvercart/css/backend/SilvercartMain.css');
        foreach (self::$additional_css_files as $css_file) {
            Requirements::css($css_file);
        }
    }
    
    /**
     * Returns the used SilverCart version
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.02.2013
     */
    public function SilvercartVersion() {
        return SilvercartConfig::SilvercartVersion();
    }

    /**
     * Returns Silvercart specific menus.
     * 
     * @return ArrayList
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.02.2013
     */
    public function SilvercartMenus() {
        $silvercartMenus = new ArrayList();
        $menuItems       = CMSMenu::get_viewable_menu_items();
        
        foreach (SilvercartConfig::getRegisteredMenus() as $menu) {
            $modelAdmins          = new ArrayList();

            foreach ($menuItems as $code => $menuItem) {
                if (isset($menuItem->controller) &&
                    $this->owner->hasMethod('alternateMenuDisplayCheck') &&
                    !$this->owner->alternateMenuDisplayCheck($menuItem->controller)) {
                    continue;
                }

                if (empty($menuItem->controller)) {
                    continue;
                }
                
                if (in_array($menuItem->controller, self::$model_admins_to_ignore)) {
                    continue;
                }

                $menuCode       = Config::inst()->get($menuItem->controller, 'menuCode');
                $menuSection    = Config::inst()->get($menuItem->controller, 'menuSection');
                $menuSortIndex  = Config::inst()->get($menuItem->controller, 'menuSortIndex');
                $url_segment    = Config::inst()->get($menuItem->controller, 'url_segment');

                if ($menuCode == $menu['code'] ||
                    (is_null($menuCode)) &&
                     $menu['code'] == 'default') {
                    $defaultTitle = LeftAndMain::menu_title_for_class($menuItem->controller);
                    $title = _t("{$menuItem->controller}.MENUTITLE", $defaultTitle);

                    $linkingmode = "";

                    if (strpos($this->owner->Link(), $menuItem->url) !== false) {
                        if ($this->owner->Link() == $menuItem->url) {
                            $linkingmode = "current";

                        // default menu is the one with a blank {@link url_segment}
                        } elseif ($url_segment == '') {
                            if ($this->owner->Link() == $this->owner->stat('url_base').'/') {
                                $linkingmode = "current";
                            }
                        } else {
                            $linkingmode = "current";
                        }
                    }

                    if (empty($menuSection)) {
                        $menuSection = 'base';
                    }

                    if (empty($menuSortIndex )) {
                        $menuSortIndex = 1000;
                    }

                    $modelAdmins->push(
                        new ArrayData(
                            array(
                                "MenuItem"    => $menuItem,
                                "Title"       => Convert::raw2xml($title),
                                "Code"        => $code,
                                'MenuCode'    => $menu['code'],
                                "IsSection"   => false,
                                "Section"     => $menuSection,
                                "SortIndex"   => $menuSortIndex,
                                "Link"        => $menuItem->url,
                                "LinkingMode" => $linkingmode
                            )
                        )
                    );
                    unset($menuItems[$code]);
                }
            }

            $modelAdmins = $modelAdmins->sort('SortIndex', 'ASC');

            if ($modelAdmins->exists()) {
                $menu['name'] = _t('SilvercartStoreAdminMenu.' . strtoupper($menu['code']), $menu['name']);
                $silvercartMenus->push(
                    new DataObject(
                        array(
                            'name'        => $menu['name'],
                            'code'        => $menu['code'],
                            'Code'        => $menu['code'],
                            'ModelAdmins' => $modelAdmins
                        )
                    )
                );
            }
        }

        return $silvercartMenus;
    }

    /**
     * Returns the base url.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.05.2012
     */
    public function BaseUrl() {
        return Director::baseUrl();
    }
    
    /**
     * Returns the Link to check for an available update.
     * 
     * @return string
     */
    public function getUpdateAvailableLink() {
        $updateAvailableLink = Controller::curr()->Link();
        if (strpos(strrev($updateAvailableLink), '/') !== 0) {
            $updateAvailableLink .= '/';
        }
        $updateAvailableLink .= 'isUpdateAvailable';
        return $updateAvailableLink;
    }
    
    /**
     * Returns whether there is an update available or not
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.01.2013
     */
    public function UpdateAvailable() {
        $updateAvailable = SilvercartTools::checkForUpdate();
        return $updateAvailable;
    }
    
    /**
     * Action to print 1 or 0 to the output to determine whether there is an
     * update available or not.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.01.2013
     */
    public function isUpdateAvailable() {
        print (int) $this->UpdateAvailable();
        exit();
    }
    
    /**
     * This action will create a translation template for all pages of the 
     * SiteTree for the given language.
     * 
     * @param array $data Request data
     * @param Form  $form Request form
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2013
     */
    public function createsitetreetranslation($data, $form) {
        $request = $this->owner->getRequest();
        // Protect against CSRF on destructive action
        if (!SecurityToken::inst()->checkRequest($request)) {
            return $this->owner->httpError(400);
        }

        $langCode               = Convert::raw2sql($request->postVar('NewTransLang'));
        $record                 = $this->owner->getRecord($request->postVar('ID'));
        $this->owner->Locale    = $langCode;

        if ($record instanceof SiteConfig) {
            $translatedRecord = $record->createTranslation($langCode);
            SilvercartRequireDefaultRecords::doTranslateSiteTree($langCode);

            $url = Controller::join_links(
                $this->owner->Link('show'),
                $translatedRecord->ID
            );

            // set the X-Pjax header to Content, so that the whole admin panel will be refreshed
            $this->owner->getResponse()->addHeader('X-Pjax', 'Content');

            $result = $this->owner->redirect($url);
        } else {
            $result = $this->owner->httpError(404);
        }
        return $result;
    }
    
    /**
     * This action will publish all pages for the given language
     * 
     * @param array $data Request data
     * @param Form  $form Request form
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2013
     */
    public function publishsitetree($data, $form) {
        $request = $this->owner->getRequest();
        // Protect against CSRF on destructive action
        if (!SecurityToken::inst()->checkRequest($request)) {
            return $this->owner->httpError(400);
        }

        $langCode               = Convert::raw2sql($request->postVar('Locale'));
        $this->owner->Locale    = $langCode;

        SilvercartRequireDefaultRecords::doPublishSiteTree($langCode);
        
        $url = $this->owner->Link('show');
        
        return $this->owner->redirect($url);
    }
    
}
