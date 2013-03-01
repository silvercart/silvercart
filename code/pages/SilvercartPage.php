<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
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
 * @subpackage Pages
 */

/**
 * Standard Controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>, Jiri Ripa <jripa@pixeltricks.de>
 * @since 20.09.2010
 * @copyright 2010 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartPage extends SiteTree {

    /**
     * extends statics
     * 
     * @var array
     */
    public static $db = array(
        'IdentifierCode' => 'VarChar(50)'
    );
    
    /**
     * Has-one relationships.
     * 
     * @var array
     */
    public static $has_one = array(
        'HeaderPicture'     => 'Image'
    );
    
    /**
     * Has-many relationships.
     * 
     * @var array
     */
    public static $many_many = array(
        'WidgetSetSidebar'  => 'SilvercartWidgetSet',
        'WidgetSetContent'  => 'SilvercartWidgetSet'
    );

    /**
     * Define indexes.
     *
     * @var array
     */
    public static $indexes = array(
        'IdentifierCode' => '(IdentifierCode)'
    );
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
    }

    /**
     * Always enable translations for this page.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.11.2012
     */
    public function canTranslate() {
        return true;
    }

    /**
     * Define editing fields for the storeadmin.
     *
     * @return FieldSet all related CMS fields
     * 
     * @author Jiri Ripa <jripa@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 15.10.2010
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        if (Member::currentUser()->isAdmin()) {
            $fields->addFieldToTab('Root.Content.Main', new TextField('IdentifierCode', 'IdentifierCode'));
            $fields->addFieldToTab('Root.Content.Main', new LiteralField('ForIdentifierCode', '<strong>' . _t('SilvercartPage.DO_NOT_EDIT', 'Do not edit this field unless you know exectly what you are doing!') . '</strong>'));
        } else {
            $fields->addFieldToTab('Root.Content.Main', new HiddenField('IdentifierCode', 'IdentifierCode'));
        }
        
        // prevent edit/add/show/delete actions for widget sets in CMS area.
        $permissions = array();
        
        $widgetSetInfoValue = _t('SilvercartWidgetSet.INFO');
        $widgetSetInfo = new LiteralField('WidgetSetInfo', $widgetSetInfoValue);
        
        $widgetSetSidebarLabel = new HeaderField('WidgetSetSidebarLabel', _t('SilvercartWidgets.WIDGETSET_SIDEBAR_FIELD_LABEL'));
        $widgetSetSidebarField = new ManyManyComplexTableField($this, 'WidgetSetSidebar', 'SilvercartWidgetSet');
        $widgetSetSidebarField->setPopupSize(900,600);
        $widgetSetSidebarField->setPermissions($permissions);
        $widgetSetContentlabel = new HeaderField('WidgetSetContentLabel', _t('SilvercartWidgets.WIDGETSET_CONTENT_FIELD_LABEL'));
        $widgetSetContentField = new ManyManyComplexTableField($this, 'WidgetSetContent', 'SilvercartWidgetSet');
        $widgetSetContentField->setPopupSize(900,600);
        $widgetSetContentField->setPermissions($permissions);
        
        $fields->addFieldToTab("Root.Content.Widgets", $widgetSetInfo);
        $fields->addFieldToTab("Root.Content.Widgets", $widgetSetSidebarLabel);
        $fields->addFieldToTab("Root.Content.Widgets", $widgetSetSidebarField);
        $fields->addFieldToTab("Root.Content.Widgets", $widgetSetContentlabel);
        $fields->addFieldToTab("Root.Content.Widgets", $widgetSetContentField);

        return $fields;
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = parent::fieldLabels($includerelations);

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Returns the given WidgetSet many-to-many relation.
     * If there is no relation, the parent relation will be recursively used
     *
     * @param string $widgetSetName The name of the widget set relation
     *
     * @return SilvercartWidgetSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.10.2012
     */
    public function getWidgetSetRelation($widgetSetName) {
        $widgetSet = $this->getManyManyComponents($widgetSetName);

        return $widgetSet;
    }
    
    /**
     * Returns the generic image for products without an own image. If none is
     * defined, boolean false is returned.
     *
     * @return mixed Image|bool false
     * 
     * @author Sascha koehler <skoehler@pixeltricks.de>
     * @since 27.06.2011
     */
    public function SilvercartNoImage() {
        $noImageObj = SilvercartConfig::getNoImage();
        
        if ($noImageObj) {
            return $noImageObj;
        }
        
        return false;
    }

    /**
     * configure the class name of the DataObjects to be shown on this page
     * this is needed to show correct breadcrumbs. This is used as fall back.
     *
     * @return string class name of the DataObject to be shown on this page
     */
    public function getSection() {
        return 'SilvercartAddress';
    }
    
    /**
     * Return the title, description, keywords and language metatags.
     * 
     * @param bool $includeTitle Show default <title>-tag, set to false for custom templating
     * 
     * @return string The XHTML metatags
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.04.2012
     */
    public function MetaTags($includeTitle = true) {
        $tags = parent::MetaTags($includeTitle);
        $tags = str_replace('SilverStripe - http://silverstripe.org', 'SilverCart - http://www.silvercart.org - SilverStripe - http://silverstripe.org', $tags);
        return $tags;
    }
    
    /**
     * Returns all translated locales as a special DataObjectSet
     *
     * @return DataObjectSet 
     */
    public function getAllTranslations() {
        $currentLocale      = Translatable::get_current_locale();
        $translations       = $this->getTranslations();
        $translationSource  = new DataObjectSet();
        if ($translations) {
            $translationSource->push(new DataObject(
                array(
                    'Name'          => SilvercartLanguageHelper::getLanguageName($currentLocale, $currentLocale),
                    'NativeName'    => SilvercartLanguageHelper::getLanguageName($currentLocale, $currentLocale),
                    'Code'          => $this->getIso2($currentLocale),
                    'RFC1766'       => i18n::convert_rfc1766($currentLocale),
                    'Link'          => $this->Link(),
                )
            ));
            foreach ($translations as $translation) {
                $translationSource->push(new DataObject(
                    array(
                        'Name'          => SilvercartLanguageHelper::getLanguageName($translation->Locale, $currentLocale),
                        'NativeName'    => SilvercartLanguageHelper::getLanguageName($translation->Locale, $translation->Locale),
                        'Code'          => $this->getIso2($translation->Locale),
                        'RFC1766'       => i18n::convert_rfc1766($translation->Locale),
                        'Link'          => $translation->Link(),
                    )
                ));
            }
        }
        return $translationSource;
    }
    
    /**
     * Returns the ISO2 for the given locale
     *
     * @param string $locale Locale
     * 
     * @return string
     */
    public function getIso2($locale) {
        $parts = explode('_', $locale);
        return strtolower($parts[1]);
    }

    /**
     * Intercepts calls to widget set relations and delegates them to the generic
     * method "getWidgetSetRelation".
     *
     * @param string $name      The method name
     * @param mixed  $arguments optional argument(s)
     *
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.10.2012
     */
    public function __call($name, $arguments) {
        if (substr($name, 0, 9) == 'WidgetSet') {
            return $this->getWidgetSetRelation($name);
        }

        return parent::__call($name, $arguments);
    }
}

/**
 * Standard Controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>, Jiri Ripa <jripa@pixeltricks.de>
 * @since 20.09.2010
 * @copyright 2010 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartPage_Controller extends ContentController {

    /**
     * Prevents recurring rendering of this page's controller.
     *
     * @var array
     */
    public static $instanceMemorizer = array();

    /**
     * Contains the output of all WidgetSets of the parent page
     *
     * @var array
     */
    protected $widgetOutput = array();
    
    /**
     * Contains all registered widget sets.
     * 
     * @var array
     * 
     * @since 2012-10-10
     */
    protected $registeredWidgetSets;
    
    /**
     * Contains all registered widget set controllers.
     * 
     * @var array
     * 
     * @since 2012-10-10
     */
    protected $registeredWidgetSetControllers;

    /**
     * Contains HTML code from modules that shall be inserted on the Page.ss
     * template.
     *
     * @var array
     *
     * @since 2013-01-03
     */
    protected static $moduleHtmlInjections = array();
    
    /**
     * Creates a SilvercartPage_Controller
     *
     * @param array $dataRecord Data record
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.04.2012
     */
    public function __construct($dataRecord = null) {
        i18n::set_default_locale(Translatable::get_current_locale());
        i18n::set_locale(Translatable::get_current_locale());
        parent::__construct($dataRecord);
        
        $this->registerWidgetSet('WidgetSetContent', $this->getWidgetSetRelation('WidgetSetContent'));
        $this->registerWidgetSet('WidgetSetSidebar', $this->getWidgetSetRelation('WidgetSetSidebar'));
    }

    /**
     * standard page controller
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 08.02.2011
     * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
     * @return void
     * @copyright 2010 pixeltricks GmbH
     */
    public function init() {
        if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
            if (SilvercartConfig::isUserAgentBlacklisted($_SERVER['HTTP_USER_AGENT'])) {
                exit();
            }
        }

        if (array_key_exists($this->ID, self::$instanceMemorizer)) {
            parent::init();
            return true;
        }

        $controller = Controller::curr();
        
        if ($this != $controller &&
            method_exists($controller, 'getRegisteredCustomHtmlForms')) {
            $registeredCustomHtmlForms = $controller->getRegisteredCustomHtmlForms();
        }
        
        if (!isset($_SESSION['Silvercart'])) {
            $_SESSION['Silvercart'] = array();
        }
        if (!isset($_SESSION['Silvercart']['errors'])) {
            $_SESSION['Silvercart']['errors'] = array();
        }
        if ($controller == $this || $controller->forceLoadOfWidgets) {
            $this->loadWidgetControllers();
        }
        
        if (!SilvercartConfig::DefaultLayoutLoaded() ||
            SilvercartConfig::$forceLoadingOfDefaultLayout) {
            RequirementsEngine::handleRegisteredFiles();
            Requirements::customScript('
                jQuery(window).focus(function() {jQuery.fx.off = false;});
                jQuery(window).blur(function(){jQuery.fx.off = true;});
            ');
            // set default layout loaded in SilvercartConfig to prevent multiple
            // loading of requirements
            SilvercartConfig::setDefaultLayoutLoaded(true);
        }
        
        // We have to check if we are in a customised controller (that's the
        // case for all Security pages). If so, we use the registered forms of
        // the outermost controller.
        if (empty($registeredCustomHtmlForms)) {
            $this->registerCustomHtmlForm('SilvercartQuickSearchForm', new SilvercartQuickSearchForm($this));
            $this->registerCustomHtmlForm('SilvercartQuickLoginForm',  new SilvercartQuickLoginForm($this));
            if ($this->getTranslations()) {
                $this->registerCustomHtmlForm('SilvercartChangeLanguageForm',  new SilvercartChangeLanguageForm($this));
            }
        } else {
            $this->setRegisteredCustomHtmlForms($registeredCustomHtmlForms);
        }
        
        $allParams = Controller::curr()->getRequest()->allParams();
        if (Controller::curr() instanceof Security &&
            array_key_exists('Action', $allParams) &&
            strtolower($allParams['Action']) == 'lostpassword' &&
            Member::currentUserID() > 0) {
            Security::logout(false);
        }

        // check the SilverCart configuration
        if (!SilvercartTools::isIsolatedEnvironment()) {
            SilvercartConfig::Check();
        }

        // Delete checkout session data if user is not in the checkout process.
        if ($this->class != 'SilvercartCheckoutStep' &&
            $this->class != 'SilvercartCheckoutStep_Controller' &&
            $this->class != 'ErrorPage_Controller' &&
            $this->class != 'Security' &&
            !$this->class instanceof SilvercartCheckoutStep_Controller &&
            !$this->class instanceof Security &&
            !is_subclass_of($this->class, 'SilvercartCheckoutStep_Controller')
        ) {
            SilvercartCheckoutStep::deleteSessionStepData();
        }

        // Decorator can use this method to add custom forms and other stuff
        $this->extend('updateInit');

        SilvercartPlugin::call($this, 'init', array($this));
        self::$instanceMemorizer[$this->ID] = true;
        parent::init();
    }

    /**
     * Returns the protocol for the current page.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2013-01-23
     */
    public function getProtocol() {
        return Director::protocol();
    }

    /**
     * Returns HTML code that has been created by SilverCart modules.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2013-01-03
     */
    public function ModuleHtmlInjections() {
        $injections = '';

        foreach (self::$moduleHtmlInjections as $injectionId => $injectionCode) {
            $injections .= $injectionCode;
        }

        return $injections;
    }

    /**
     * Saves HTML code for injection on the Page.ss template.
     *
     * @param string $identifier The identifier for the injection
     * @param string $code       The code to inject
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2013-01-03
     */
    public static function injectHtmlCode($identifier, $code) {
        self::$moduleHtmlInjections[$identifier] = $code;
    }

    /**
     * Indicates wether the site is in live mode.
     * 
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.01.2012
     */
    public function isLive() {
        return Director::isLive();
    }
    
    /**
     * template function: returns customers orders
     * 
     * @param int $limit Limit
     *
     * @since 27.10.10
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @return DataObjectSet DataObjectSet with order objects
     */
    public function CurrentMembersOrders($limit = null) {
        $memberID = Member::currentUserID();
        if ($memberID) {
            $filter = sprintf("`MemberID` = '%s'", $memberID);
            $orders = DataObject::get('SilvercartOrder', $filter, null, null, $limit);
            return $orders;
        }
    }
    
    /**
     * Returns the HTML Code of Silvercart errors and clears the error list.
     *
     * @return string
     * 
     * @author Sascha koehler <skoehler@pixeltricks.de>
     * @since 09.06.2011
     */
    public function SilvercartErrors() {
        $errorStr = '';
        
        if (!empty($_SESSION['Silvercart']['errors'])) {
            foreach ($_SESSION['Silvercart']['errors'] as $error) {
                $errorStr .= '<p>'.$error.'</p>';
            }
        }
        
        $_SESSION['Silvercart']['errors'] = array();
        
        return $errorStr;
    }
    
    /**
     * Returns the HTML Code as string for all widgets in the given WidgetArea.
     *
     * If there's no WidgetArea for this page defined we try to get the
     * definition from its parent page.
     * 
     * @param string $identifier The identifier of the widget area to insert
     * 
     * @return string
     * 
     * @author Sascha koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function InsertWidgetArea($identifier = 'Sidebar') {
        $output = '';

        if ($this->registeredWidgetSetControllers) {
            $controllerName = 'WidgetSet'.$identifier;

            if (!array_key_exists($controllerName, $this->registeredWidgetSetControllers)) {
                return $output;
            }

            foreach ($this->registeredWidgetSetControllers[$controllerName] as $controller) {
                $output .= $controller->WidgetHolder();
            }

            if (empty($output)) {
                if (array_key_exists($identifier, $this->widgetOutput)) {
                    $output = $this->widgetOutput[$identifier];
                }
            }
        }
        
        return $output;
    }

    /**
     * Eigene Zugriffsberechtigungen definieren.
     * 
     * @return array configuration of API permissions
     * 
     * @author Sascha koehler <skoehler@pixeltricks.de>
     * @since 12.10.2010
     */
    public function providePermissions() {
        return array(
            'API_VIEW' => _t('Page.API_VIEW', 'can read objects via the API'),
            'API_CREATE' => _t('Page.API_CREATE', 'can create objects via the API'),
            'API_EDIT' => _t('Page.API_EDIT', 'can edit objects via the API'),
            'API_DELETE' => _t('Page.API_DELETE', 'can delete objects via the API')
        );
    }

    /**
     * template method for breadcrumbs
     * show breadcrumbs for pages which show a DataObject determined via URL parameter ID
     * see _config.php
     *
     * @return string html for breadcrumbs
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 3.11.2010
     */
    public function getBreadcrumbs() {
        $page = DataObject::get_one(
            'Page',
            sprintf(
                    '"URLSegment" LIKE \'%s\'',
                    $this->urlParams['URLSegment']
            )
        );

        return $this->ContextBreadcrumbs($page);
    }

    /**
     * pages with own url rewriting need their breadcrumbs created in a different way
     *
     * @param Controller $context        the current controller
     * @param int        $maxDepth       maximum levels
     * @param bool       $unlinked       link breadcrumbs elements
     * @param bool       $stopAtPageType ???
     * @param bool       $showHidden     show pages that will not show in menus
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 3.11.2010
     * @return string html for breadcrumbs
     */
    public function ContextBreadcrumbs($context, $maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
        $page = $context;
        $parts = array();

        // Get address type
        $address = DataObject::get_by_id($context->getSection(), $this->urlParams['ID']);
        $parts[] = $address->i18n_singular_name();

        $i = 0;
        while (
            $page
            && (!$maxDepth || sizeof($parts) < $maxDepth)
            && (!$stopAtPageType || $page->ClassName != $stopAtPageType)
        ) {
            if ($showHidden || $page->ShowInMenus || ($page->ID == $this->ID)) {
                if ($page->URLSegment == 'home') {
                    $hasHome = true;
                }
                if (($page->ID == $this->ID) || $unlinked) {
                    $parts[] = Convert::raw2xml($page->Title);
                } else {
                    $parts[] = ("<a href=\"" . $page->Link() . "\">" . Convert::raw2xml($page->Title) . "</a>");
                }
            }
            $page = $page->Parent;
        }

        return implode(SiteTree::$breadcrumbs_delimiter, array_reverse($parts));
    }
    
    /**
     * manipulates the parts the pages breadcrumbs if a product detail view is 
     * requested.
     *
     * @param int    $maxDepth       maximum depth level of shown pages in breadcrumbs
     * @param bool   $unlinked       true, if the breadcrumbs should be displayed without links
     * @param string $stopAtPageType name of pagetype to stop at
     * @param bool   $showHidden     true, if hidden pages should be displayed in breadcrumbs
     *
     * @return DataObjectSet
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>, Patrick Schneider <pschneider@pixeltricks.de>
     * @since 09.10.2012
     */
    public function BreadcrumbParts($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
        $parts = new DataObjectSet();
        $page  = $this;
            
        while (
            $page
            && (!$maxDepth ||
                    $parts->Count() < $maxDepth)
            && (!$stopAtPageType ||
                    $page->ClassName != $stopAtPageType)
        ) {
            if ($showHidden ||
                $page->ShowInMenus ||
                ($page->ID == $this->ID)) {
                
                if ($page->hasMethod('OriginalLink')) {
                    $link = $page->OriginalLink();
                } else {
                    $link = $page->Link();
                }
                
                $parts->unshift(
                        new ArrayData(
                                array(
                                    'Title'  => $page->Title,
                                    'Link'   => $link,
                                    'Parent' => $page->Parent,
                                )
                        )
                );
            }
            $page = $page->Parent;
        }
        return $parts;
    }
    
    /**
     * returns the breadcrumbs as DataObjectSet for use in controls with product title
     * 
     * @param int    $maxDepth       maximum depth level of shown pages in breadcrumbs
     * @param bool   $unlinked       true, if the breadcrumbs should be displayed without links
     * @param string $stopAtPageType name of pagetype to stop at
     * @param bool   $showHidden     true, if hidden pages should be displayed in breadcrumbs
     *
     * @return DataObjectSet 
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 09.10.2012
     */
    public function DropdownBreadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
        return $this->BreadcrumbParts($maxDepth, $unlinked, $stopAtPageType, $showHidden);
    }

    /**
     * Function similar to Member::currentUser(); Determins if we deal with a
     * registered customer who has opted in. Returns the member object or
     * false.
     *
     * @return mixed Member|boolean(false)
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.11.2010
     * @since 13.05.2011 - replaced logic with call to the appropriate method
     *                     in the SilvercartRole object (SK).
     */
    public function CurrentRegisteredCustomer() {
        return SilvercartCustomer::currentRegisteredCustomer();
    }

    /**
     * This function is replacing the default SilverStripe Logout Form. This form is used to logout the customer and direct
     * the user to the startpage
     *
     * @return null
     *
     * @author Oliver Scheer <oscheer@pixeltricks.de>
     * @since 11.11.2010
     */
    public function logOut() {
        Security::logout(false);
        $frontPage = SilvercartPage_Controller::PageByIdentifierCode();
        Director::redirect($frontPage->RelativeLink());
    }

    /**
     * returns a single page by IdentifierCode
     * used to retrieve links dynamically
     *
     * @param string $identifierCode the classes name
     * 
     * @return SiteTree | false a single object of the site tree; without param the SilvercartFrontPage will be returned
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public static function PageByIdentifierCode($identifierCode = "SilvercartFrontPage") {
        return SilvercartTools::PageByIdentifierCode($identifierCode);
    }

    /**
     * returns a page link by IdentifierCode
     *
     * @param string $identifierCode the DataObjects IdentifierCode
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public static function PageByIdentifierCodeLink($identifierCode = "SilvercartFrontPage") {
        return SilvercartTools::PageByIdentifierCodeLink($identifierCode);
    }

    /**
     * Uses the children of SilvercartProductGroupHolder to render a subnavigation
     * with the SilvercartSubNavigation.ss template. This is the default sub-
     * navigation.
     *
     * @param string $identifierCode The code of the parent page.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.03.2011
     */
    public function getSubNavigation($identifierCode = 'SilvercartProductGroupHolder') {
        $output = '';
        $this->extend('updateSubNavigation', $output);
        if (empty($output)) {
            $items              = array();
            $productGroupPage   = $this->PageByIdentifierCode($identifierCode);

            if ($productGroupPage) {
                foreach ($productGroupPage->Children() as $child) {
                    if ($child->hasmethod('hasProductsOrChildren') &&
                        $child->hasProductsOrChildren()) {
                        $items[] = $child;
                    }
                }
                $elements = array(
                    'SubElements' => new DataObjectSet($items),
                );
                $output = $this->customise($elements)->renderWith(
                    array(
                        'SilvercartSubNavigation',
                    )
                );
            }
        }
        return $output;
    }
    
    /**
     * Adds a widget output to the class variable "$this->widgetOutput".
     *
     * @param string $key    The key for the output
     * @param string $output The actual output of the widget
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.09.2011
     */
    public function saveWidgetOutput($key, $output) {
        $this->widgetOutput[$key] = $output;
    }

    /**
     * used to determine weather something should be shown on a template or not
     *
     * @return bool
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.3.2011
     */
    public function showPricesGross() {
        $pricetype = SilvercartConfig::Pricetype();
        if ($pricetype == "gross") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * used to determine weather something should be shown on a template or not
     *
     * @return bool
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.3.2011
     */
    public function showPricesNet() {
        $pricetype = SilvercartConfig::Pricetype();
        if ($pricetype == "net") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return the given number of topseller products as DataObjectSet.
     * 
     * We use caching here, so check the cache first if you don't get the
     * desired results.
     *
     * @param int $nrOfProducts The number of products to return
     *
     * @return mixed DataObjectSet|Boolean false
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 29.03.2011
     */
    public function getTopsellerProducts($nrOfProducts = 5) {
        $cachekey = 'TopsellerProducts'.$nrOfProducts;
        $cache    = SS_Cache::factory($cachekey);
        $result   = $cache->load($cachekey);

        if ($result) {
            $result = unserialize($result);
        } else {
            $products   = array();
            $sqlQuery   = new SQLQuery();

            $sqlQuery->select = array(
                'SOP.SilvercartProductID',
                'SUM(SOP.Quantity) AS Quantity'
            );
            $sqlQuery->from = array(
                'SilvercartOrderPosition SOP',
                'LEFT JOIN SilvercartProduct SP on SP.ID = SOP.SilvercartProductID'
            );
            $sqlQuery->where = array(
                'SP.isActive = 1'
            );
            $sqlQuery->groupby = array(
                'SOP.SilvercartProductID'
            );
            $sqlQuery->orderby  = 'Quantity DESC';
            $sqlQuery->limit    = $nrOfProducts;

            $result = $sqlQuery->execute();

            foreach ($result as $row) {
                $products[] = DataObject::get_by_id(
                    'SilvercartProduct',
                    $row['SilvercartProductID']
                );
            }
            
            $result = new DataObjectSet($products);
        }

        return $result;
    }

    /**
     * We load the special offers productgroup page here.
     *
     * @param string $groupIdentifier Identifier of the product group
     * @param int    $nrOfProducts    The number of products to return
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 24.03.2011
     */
    public function getProductGroupItems($groupIdentifier = 'SilvercartOffers', $nrOfProducts = 4) {
        $products = array();

        $records = DB::query(
            sprintf(
                "
                SELECT
                    SilvercartProductID
                FROM
                    (
                        SELECT
                            SilvercartProduct.ID AS SilvercartProductID
                        FROM
                            SilvercartProduct
                        LEFT JOIN
                            SilvercartPage
                        ON
                            SilvercartPage.ID = SilvercartProduct.SilvercartProductGroupID
                        WHERE
                            SilvercartPage.IdentifierCode = '%s'
                    ) AS DirectRelations
                UNION SELECT
                    SilvercartProductID
                FROM
                    (
                        SELECT
                            SP_SPGMP.SilvercartProductID AS SilvercartProductID
                        FROM
                            SilvercartProduct_SilvercartProductGroupMirrorPages AS SP_SPGMP
                        LEFT JOIN
                            SilvercartPage
                        ON
                            SilvercartPage.ID = SP_SPGMP.SilvercartProductGroupPageID
                        WHERE
                            SilvercartPage.IdentifierCode = '%s'
                    ) AS MirrorRelations
                GROUP BY
                    SilvercartProductID
                ORDER BY
                    RAND()
                LIMIT
                    %d
                ",
                $groupIdentifier,
                $groupIdentifier,
                $nrOfProducts
            )
        );

        foreach ($records as $record) {
            $product = DataObject::get_by_id(
                'SilvercartProduct',
                $record['SilvercartProductID']
            );

            if ($product) {
                $products[] = $product;
            }
        }

        $result = new DataObjectSet($products);

        return $result;
    }
    
    /**
     * Returns the shoppingcart of the current user or false if there's no
     * member object registered.
     * 
     * @return mixed false|SilvercartShoppingCart
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2011
     */
    public function SilvercartShoppingCart() {
        $controller = Controller::curr();

        if ($this->class == $controller->class &&
            !SilvercartTools::isIsolatedEnvironment() &&
            !SilvercartTools::isBackendEnvironment()) {

            $member = Member::currentUser();

            if (!$member) {
                return false;
            }

            return $member->SilvercartShoppingCart();
        } else {
            return false;
        }
    }
    
    /**
     * Loads the widget controllers into class variables so that we can use
     * them in method 'InsertWidgetArea'.
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.05.2011
     */
    protected function loadWidgetControllers() {
        $registeredWidgetSets = $this->getRegisteredWidgetSets();

        foreach ($registeredWidgetSets as $registeredWidgetSetName => $registeredWidgetSetItems) {
            $controllers = new DataObjectSet();

            foreach ($registeredWidgetSetItems as $registeredWidgetSetItem) {
                $widgets = $registeredWidgetSetItem->WidgetArea()->WidgetControllers();
                $widgets->sort('Sort', 'ASC');
                $controllers->merge(
                    $widgets
                );
            }

            $this->registeredWidgetSetControllers[$registeredWidgetSetName] = $controllers;
        }
    }

    /**
     * Returns all registered widget sets as associative array.
     * 
     * @return array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2012-10-10
     */
    public function getRegisteredWidgetSets() {
        return $this->registeredWidgetSets;
    }
    
    /**
     * Registers a WidgetSet.
     * 
     * @param string        $widgetSetName  The name of the widget set (used as array key)
     * @param DataObjectSet $widgetSetItems The widget set items (usually coming from a relation)
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.05.2011
     */
    public function registerWidgetSet($widgetSetName, $widgetSetItems) {
        $this->registeredWidgetSets[$widgetSetName] = $widgetSetItems;
    }
    
    /**
     * Builds an associative array of ProductGroups to use in GroupedDropDownFields.
     *
     * @param SiteTree $parent      Expects a SilvercartProductGroupHolder or a SilvercartProductGroupPage
     * @param boolean  $allChildren ???
     * @param boolean  $withParent  ???
     *
     * @return array
     */
    public static function getRecursivePagesForGroupedDropdownAsArray($parent = null, $allChildren = false, $withParent = false) {
        $pages = array();
        
        if (is_null($parent)) {
            $pages['']  = '';
            $parent             = self::PageByIdentifierCode('SilverCartPageHolder');
        }
        
        if ($parent) {
            if ($withParent) {
                $pages[$parent->ID] = $parent->Title;
            }
            if ($allChildren) {
                $children = $parent->AllChildren();
            } else {
                $children = $parent->Children();
            }
            foreach ($children as $child) {
                $pages[$child->ID] = $child->Title;
                $subs                      = self::getRecursivePagesForGroupedDropdownAsArray($child);
                
                if (!empty ($subs)) {
                    $pages[_t('SilvercartProductGroupHolder.SUBGROUPS_OF','Subgroups of ') . $child->Title] = $subs;
                }
            }
        }
        return $pages;
    }
}
