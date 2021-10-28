<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Admin\Dev\Install\RequireDefaultRecords;
use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverStripe\Admin\CMSMenu;
use SilverStripe\Control\Director;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;

/**
 * Extension for the LeftAndMain class.
 * 
 * @package SilverCart
 * @subpackage Admin_Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 * 
 * @property \SilverStripe\Admin\LeftAndMain $owner Owner
 */
class LeftAndMainExtension extends Extension
{
    /**
     * List of allowed actions
     *
     * @var array
     */
    private static $allowed_actions = [
        'isUpdateAvailable',
        'publishsitetree',
        'add_example_data',
        'add_example_config',
    ];
    /**
     * ModelAdmins to ignore.
     *
     * @var array
     */
    public static $model_admins_to_ignore = [];
    /**
     * List of additional CSS files to load in backend.
     *
     * @var array
     */
    public static $additional_css_files = [];
    
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
    public static function add_additional_css_file($file_name)
    {
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
    public function onAfterInit()
    {
        if (Director::is_ajax()) {
            return true;
        }
        Requirements::javascript('silvercart/silvercart:client/admin/javascript/LeftAndMainExtension.js');
        Requirements::css('silvercart/silvercart:client/admin/css/LeftAndMainExtension.css');
        foreach (self::$additional_css_files as $css_file) {
            Requirements::css($css_file);
        }
    }
    
    /**
     * Returns the full SilverCart version number (e.g. "4.3.0").
     * 
     * @return string
     */
    public function SilverCartFullVersionNumber() : string
    {
        return Config::SilverCartFullVersion();
    }
    
    /**
     * Returns the SilverCart minor version number (e.g. "4.3").
     * 
     * @return string
     */
    public function SilverCartVersionNumber() : string
    {
        return Config::SilverCartVersion();
    }
    
    /**
     * Returns the SilverCart and SilverStripe CMS version string.
     * 
     * @return string
     */
    public function SilverCartVersion() : string
    {
        $cmsVersion = $this->owner->CMSVersion();
        return "SilverCart {$this->SilverCartFullVersionNumber()}, {$cmsVersion}";
    }

    /**
     * Returns SilverCart specific menus.
     * 
     * @return ArrayList
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.02.2013
     */
    public function SilvercartMenus()
    {
        $silvercartMenus = ArrayList::create();
        $menuItems       = CMSMenu::get_viewable_menu_items();
        $hiddenMenus     = Config::getHiddenRegisteredMenus();
        $menuIconStyling = '';
        
        foreach (Config::getRegisteredMenus() as $menu) {
            if (in_array($menu['code'], $hiddenMenus)) {
                continue;
            }
            $modelAdmins = ArrayList::create();

            foreach ($menuItems as $code => $menuItem) {
                if (isset($menuItem->controller)
                 && $this->owner->hasMethod('alternateMenuDisplayCheck')
                 && !$this->owner->alternateMenuDisplayCheck($menuItem->controller)
                ) {
                    continue;
                }

                if (empty($menuItem->controller)) {
                    continue;
                }
                
                if (in_array($menuItem->controller, self::$model_admins_to_ignore)) {
                    continue;
                }

                $controllerObj = singleton($menuItem->controller);
                $menuCode      = $controllerObj->config()->get('menuCode');
                $menuSortIndex = $controllerObj->config()->get('menuSortIndex');
                $url_segment   = $controllerObj->config()->get('url_segment');
                
                if ($menuCode == $menu['code']
                 || is_null($menuCode)
                 && $menu['code'] == 'default'
                ) {
                    $defaultTitle = LeftAndMain::menu_title($menuItem->controller);
                    $title        = _t("{$menuItem->controller}.MENUTITLE", $defaultTitle);
                    $linkingmode  = "";

                    if (strpos($this->owner->Link(), $menuItem->url) !== false) {
                        if ($this->owner->Link() == $menuItem->url) {
                            $linkingmode = "current";
                        } elseif ($url_segment == '') {
                            if ($this->owner->Link() == $this->owner->stat('url_base').'/') {
                                $linkingmode = "current";
                            }
                        } else {
                            $linkingmode = "current";
                        }
                    }

                    if (empty($menuSortIndex )) {
                        $menuSortIndex = 1000;
                    }
                    
                    $iconClass = '';
                    $menuIcon  = LeftAndMain::menu_icon_for_class($menuItem->controller);
                    if (!empty($menuIcon)) {
                        $menuIconStyling .= $menuIcon;
                    } else {
                        $iconClass = LeftAndMain::menu_icon_class_for_class($menuItem->controller);
                    }

                    $modelAdmins->push(ArrayData::create([
                        "MenuItem"    => $menuItem,
                        "Title"       => Convert::raw2xml($title),
                        "Code"        => $code,
                        'MenuCode'    => $menu['code'],
                        "IsSection"   => false,
                        "SortIndex"   => $menuSortIndex,
                        "Link"        => $menuItem->url,
                        "LinkingMode" => $linkingmode,
                        "Icon"        => strtolower($code),
                        "IconClass"   => $iconClass,
                    ]));
                    unset($menuItems[$code]);
                }
            }

            $modelAdmins = $modelAdmins->sort('SortIndex', 'ASC');

            if ($modelAdmins->exists()) {
                $menu['name'] = _t(LeftAndMainExtension::class . '.' . strtoupper($menu['code']), $menu['name']);
                $silvercartMenus->push(
                    DataObject::create([
                        'name'        => $menu['name'],
                        'code'        => $menu['code'],
                        'Code'        => $menu['code'],
                        "Icon"        => strtolower($menu['code']),
                        'ModelAdmins' => $modelAdmins
                    ])
                );
            }
        }
        if ($menuIconStyling) {
            Requirements::customCSS($menuIconStyling);
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
    public function BaseUrl()
    {
        return Director::baseUrl();
    }
    
    /**
     * Returns the Link to check for an available update.
     * 
     * @return string
     */
    public function getUpdateAvailableLink()
    {
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
    public function UpdateAvailable()
    {
        $updateAvailable = Tools::checkForUpdate();
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
    public function isUpdateAvailable()
    {
        print (int) $this->UpdateAvailable();
        exit();
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
    public function publishsitetree($data, $form)
    {
        $request = $this->owner->getRequest();
        // Protect against CSRF on destructive action
        if (!SecurityToken::inst()->checkRequest($request)) {
            return $this->owner->httpError(400);
        }

        $langCode               = Convert::raw2sql($request->postVar('Locale'));
        $this->owner->Locale    = $langCode;

        RequireDefaultRecords::doPublishSiteTree($langCode);
        
        $url = $this->owner->Link('show');
        
        return $this->owner->redirect($url);
    }
    
    /**
     * Adds example data to SilverCart when triggered in ModelAdmin.
     *
     * @return \SilverStripe\Control\HTTPResponse 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.02.2013
     */
    public function add_example_data()
    {
        Config::enableTestData();
        $result = RequireDefaultRecords::createTestData();
        if ($result) {
            $responseText   = _t(Config::class . '.ADDED_EXAMPLE_DATA', 'Add example data');
        } else {
            $responseText   = _t(Config::class . '.EXAMPLE_DATA_ALREADY_ADDED', 'Example data already added');
        }
        $this->owner->getResponse()->addHeader('X-Status', rawurlencode($responseText));
        return $this->owner->getResponseNegotiator()->respond($this->owner->getRequest());
    }
    
    /**
     * Adds example configuration to SilverCart when triggered in ModelAdmin.
     *
     * @return \SilverStripe\Control\HTTPResponse 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2013
     */
    public function add_example_config()
    {
        Config::enableTestData();
        $result = RequireDefaultRecords::createTestConfiguration();
        if ($result) {
            $responseText   = _t(Config::class . '.ADDED_EXAMPLE_CONFIGURATION', 'Add example configuration');
        } else {
            $responseText   = _t(Config::class . '.EXAMPLE_CONFIGURATION_ALREADY_ADDED', 'Example configuration already added');
        }
        $this->owner->getResponse()->addHeader('X-Status', rawurlencode($responseText));
        return $this->owner->getResponseNegotiator()->respond($this->owner->getRequest());
    }
    
}
