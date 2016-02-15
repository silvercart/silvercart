<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * Standard Controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>,
 *         Jiri Ripa <jripa@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 08.04.2013
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartPage extends SiteTree {

    /**
     * extends statics
     * 
     * @var array
     */
    private static $db = array(
        'UseAsRootForMainNavigation' => 'Boolean(0)',
        'IdentifierCode'             => 'VarChar(50)',
    );
    
    /**
     * Has-one relationships.
     * 
     * @var array
     */
    private static $has_one = array(
        'HeaderPicture'     => 'Image'
    );
    
    /**
     * Has-many relationships.
     * 
     * @var array
     */
    private static $many_many = array(
        'WidgetSetSidebar'  => 'WidgetSet',
        'WidgetSetContent'  => 'WidgetSet'
    );

    /**
     * Define indexes.
     *
     * @var array
     */
    private static $indexes = array(
        'IdentifierCode' => '("IdentifierCode")'
    );
    
    /**
     * Indicator to check whether getCMSFields is called
     *
     * @var boolean
     */
    protected $getCMSFieldsIsCalled = false;
    
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
     * @return FieldList all related CMS fields
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Jiri Ripa <jripa@pixeltricks.de>
     * @since 06.10.2014
     */
    public function getCMSFields() {
        $this->getCMSFieldsIsCalled = true;
        $fields = parent::getCMSFields();

        if (Permission::check('ADMIN')) {
            $fields->addFieldToTab('Root.Main', new TextField('IdentifierCode', 'IdentifierCode'));
            $fields->dataFieldByName('IdentifierCode')->setRightTitle('<strong>' . _t('SilvercartPage.DO_NOT_EDIT', 'Do not edit this field unless you know exectly what you are doing!') . '</strong>');
        } else {
            $fields->addFieldToTab('Root.Main', new HiddenField('IdentifierCode', 'IdentifierCode'));
        }
        $fields->addFieldToTab('Root.Main', new CheckboxField('UseAsRootForMainNavigation', $this->fieldLabel('UseAsRootForMainNavigation')));
        
        return $fields;
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.10.2014
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'UseAsRootForMainNavigation' => _t('SilvercartPage.UseAsRootForMainNavigation'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Handles the UseAsRootForMainNavigation property on before write.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.10.2014
     */
    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        
        $request = Controller::curr()->getRequest();
        /* @var $request SS_HTTPRequest */
        if ($request->postVar('ID') == $this->ID &&
            $request->postVar('UseAsRootForMainNavigation') == '1') {
            $this->UseAsRootForMainNavigation = true;
        }
        
        if ($this->isChanged('UseAsRootForMainNavigation')) {
            $changed = $this->getChangedFields(false, 1);
            $ch      = $changed['UseAsRootForMainNavigation'];
            if ($this->UseAsRootForMainNavigation) {
                DB::query('UPDATE SilvercartPage SET UseAsRootForMainNavigation = 0 WHERE ID != ' . $this->ID);
            } elseif ($ch['before'] != $ch['after']) {
                $this->UseAsRootForMainNavigation = true;
            }
        }
    }
    
    /**
     * Returns the main navigation root page (set in backend).
     * 
     * @return SilvercartPage
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.10.2014
     */
    public function MainNavigationRootPage() {
        $mainNavigationRootPage = SilvercartPage::get()->filter('UseAsRootForMainNavigation', true)->first();
        if (is_null($mainNavigationRootPage)) {
            $mainNavigationRootPage = SilvercartTools::PageByIdentifierCode('SilvercartProductGroupHolder');
            DB::query('UPDATE SilvercartPage SET UseAsRootForMainNavigation = 1 WHERE ID = ' . $mainNavigationRootPage->ID);
            DB::query('UPDATE SilvercartPage_Live SET UseAsRootForMainNavigation = 1 WHERE ID = ' . $mainNavigationRootPage->ID);
        }
        return $mainNavigationRootPage;
    }
    
    /**
     * Returns the main navigation cache key.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.10.2014
     */
    public function MainNavigationCacheKey() {
        $cacheKeyParts = array(
            'SilvercartNavigation',
            $this->ID,
            i18n::get_locale(),
            $this->MainNavigationRootPage()->stageChildren(false)->max('LastEdited'),
        );
        $this->extend('updateMainNavigationCacheKeyParts', $cacheKeyParts);
        return implode('_', $cacheKeyParts);
    }
    
    /**
     * Dummy to provide enhanced product group functions.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.10.2014
     */
    public function hasProductsOrChildren() {
        return true;
    }
    
    /**
     * Returns the SilvercartConfig.
     *
     * @return SilvercartConfig
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.04.2014
     */
    public function SilvercartConfig() {
        return SilvercartConfig::getConfig();
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
     * getTreeTitle will return three <span> html DOM elements, an empty <span> with
     * the class 'jstree-pageicon' in front, following by a <span> wrapping around its
     * MenutTitle, then following by a <span> indicating its publication status. 
     *
     * @return string a html string ready to be directly used in a template
     */
    public function getTreeTitle() {
        $flags = $this->getStatusFlags();
        $treeTitle = sprintf(
            "<span class=\"jstree-pageicon\"></span>%s",
            Convert::raw2xml(str_replace(array("\n","\r"),"",$this->MenuTitle))
        );
        foreach ($flags as $class => $data) {
            if (is_string($data)) {
                $data = array('text' => $data);
            }
            $treeTitle .= sprintf(
                "<span class=\"badge %s\"%s>%s</span>",
                'status-' . Convert::raw2xml($class),
                (isset($data['title'])) ? sprintf(' title="%s"', Convert::raw2xml($data['title'])) : '',
                Convert::raw2xml($data['text'])
            );
        }

        return $treeTitle;
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
     * Returns all translated locales as a special ArrayList
     *
     * @return ArrayList 
     */
    public function getAllTranslations() {
        $currentLocale      = Translatable::get_current_locale();
        $translations       = $this->getTranslations();
        $translationSource  = new ArrayList();
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
     * Adds a decorator hook and returns the Content.
     * 
     * @return string
     */
    public function getContent() {
        $content = $this->getField('Content');
        if (!$this->getCMSFieldsIsCalled) {
            $this->extend('updateContent', $content);
        }
        return $content;
    }
    
    /**
     * Adds a decorator hook and returns the MetaDescription.
     * 
     * @return string
     */
    public function getMetaDescription() {
        $metaDescription = $this->getField('MetaDescription');
        if (!$this->getCMSFieldsIsCalled) {
            $this->extend('updateMetaDescription', $metaDescription);
        }
        return $metaDescription;
    }
}

/**
 * Standard Controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>,
 *         Jiri Ripa <jripa@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 08.04.2013
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartPage_Controller extends ContentController {

    /**
     * Prevents recurring rendering of this page's controller.
     *
     * @var array
     */
    public static $instanceMemorizer = array();

    /**
     * Contains HTML code from modules that shall be inserted on the Page.ss
     * template.
     *
     * @var array
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
     * @since 21.02.2013
     */
    public function __construct($dataRecord = null) {
        i18n::set_default_locale(Translatable::get_current_locale());
        i18n::set_locale(Translatable::get_current_locale());
        parent::__construct($dataRecord);
        
        $this->registerWidgetSet('WidgetSetContent', $this->WidgetSetContent());
        $this->registerWidgetSet('WidgetSetSidebar', $this->WidgetSetSidebar());
    }
    
    /**
     * On before init.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.10.2014
     */
    public function loadJSRequirements() {
        if (SilvercartTools::isIsolatedEnvironment()) {
            return;
        }

        Requirements::set_write_js_to_body(true);
        Requirements::javascript('https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
        Requirements::javascript('https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/jquery-ui.min.js');
        
        $jsFiles = array(
            'customhtmlform/script/jquery.pixeltricks.forms.checkFormData.js',
            'customhtmlform/script/jquery.pixeltricks.forms.events.js',
            'customhtmlform/script/jquery.pixeltricks.forms.validator.js',
            'silvercart/javascript/jquery.pixeltricks.tools.js',
            'silvercart/javascript/jquery.cookie.js',
            'silvercart/javascript/bootstrap.min.js',
            'silvercart/javascript/jquery.flexslider-min.js',
            'silvercart/javascript/jquery.cycle2.min.js',
            'silvercart/javascript/jquery.cycle2.carousel.min.js',
            'silvercart/javascript/jquery.cycle2.swipe.min.js',
            'silvercart/javascript/jquery.tweet.js',
            'silvercart/javascript/fancybox/jquery.fancybox.js',
            'silvercart/javascript/custom.js',
            'silvercart/javascript/silvercart.js',
        );
        if (SilvercartWidget::$use_anything_slider) {
            $jsFiles = array_merge(
                    $jsFiles,
                    array(
                        'silvercart/javascript/anythingslider/js/jquery.anythingslider.min.js',
                        'silvercart/javascript/anythingslider/js/jquery.anythingslider.fx.min.js',
                        'silvercart/javascript/anythingslider/js/jquery.easing.1.2.js',
                    )
            );
        }
        $this->extend('updatedJSRequirements', $jsFiles);
        
        Requirements::combine_files(
            'm.js.js',
            $jsFiles
        );
    }
    
    /**
     * Requires the color scheme CSS.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2014
     */
    public function RequireColorSchemeCSS() {
        Requirements::themedCSS('color_' . SilvercartConfig::getConfig()->ColorScheme, 'silvercart');
    }

    /**
     * standard page controller
     *
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
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
        
        SilvercartTools::initSession();
        $controller = Controller::curr();
        
        if ($this != $controller &&
            method_exists($controller, 'getRegisteredCustomHtmlForms')) {
            $registeredCustomHtmlForms = $controller->getRegisteredCustomHtmlForms();
        }
        
        if ($controller == $this || $controller->forceLoadOfWidgets) {
            $this->loadWidgetControllers();
        }
        
        $this->loadJSRequirements();
        
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
            Member::currentUserID() > 0 &&
            SilvercartCustomer::currentUser() instanceof Member) {
            SilvercartCustomer::currentUser()->logOut();
        }
        if (Member::currentUserID() > 0 &&
            !SilvercartCustomer::currentUser() instanceof Member) {
            Session::set('loggedInAs', 0);
            Session::save();
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
     * Returns the HTTP error response.
     * 
     * @param string $code    Error code
     * @param string $message Error message
     * 
     * @return void
     * 
     * @throws SS_HTTPResponse_Exception
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.05.2015
     */
    public function httpError($code, $message = null) {
        $combined_files = Requirements::get_combine_files();
        try {
            $response = parent::httpError($code, $message);
        } catch (SS_HTTPResponse_Exception $e) {
            $originalResponse = $e->getResponse();
            Requirements::restore();
            foreach ($combined_files as $combinedFileName => $files) {
                Requirements::combine_files($combinedFileName, $files);
                Requirements::process_combined_files();
            }
            $response = $this->request->isMedia() ? null : self::error_response_for($code);
            throw new SS_HTTPResponse_Exception($response ? $response : ($originalResponse ? $originalResponse : $message), $code);
        }
    }
    
    /**
     * Returns the error response for the given status code.
     * Workaround to include CSS requirements into response HTML code.
     * 
     * @param string $statusCode Status code
     * 
     * @return ContentController
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.05.2015
     */
    public static function error_response_for($statusCode) {
        $response  = null;
        $errorPage = ErrorPage::get()->filter(array(
            "ErrorCode" => $statusCode
        ))->first(); 

        if ($errorPage) {
            $response = ModelAsController::controller_for($errorPage)->handleRequest(
                new SS_HTTPRequest('GET', ''), DataModel::inst()
            );
        }
        
        return $response;
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
     * @return DataList with order objects or empty DataList
     */
    public function CurrentMembersOrders($limit = null) {
        $memberID = Member::currentUserID();
        if ($memberID) {
            if ($limit) {
                $orders = SilvercartOrder::get()->filter('MemberID', $memberID)->limit($limit);
            } else {
                $orders = SilvercartOrder::get()->filter('MemberID', $memberID);
            }
            return $orders;
        }
    }
    
    /**
     * Returns the HTML Code of Silvercart errors and clears the error list.
     *
     * @return string
     * 
     * @author Sascha koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2013
     */
    public function SilvercartErrors() {
        $errorStr = '';
        
        $silvercartSessionErrors = Session::get('Silvercart.errors');
        if (is_array($silvercartSessionErrors)) {
            foreach ($silvercartSessionErrors as $error) {
                $errorStr .= '<p>'.$error.'</p>';
            }
            Session::set('Silvercart.errors', array());
            Session::save();
        }
        
        return $errorStr;
    }

    /**
     * Provide permissions
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
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.06.2014
     */
    public function getBreadcrumbs() {
        $page = Page::get()->filter('URLSegment', $this->urlParams['URLSegment'])->first();

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

        return implode(" &raquo; ", array_reverse($parts));
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
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>, Patrick Schneider <pschneider@pixeltricks.de>
     * @since 09.10.2012
     */
    public function BreadcrumbParts($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
        $parts = new ArrayList();
        $page  = $this;

        while (
            $page
            && (!$maxDepth ||
                    $parts->count() < $maxDepth)
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

                if ($page->ID == $this->ID) {
                    $isActive = true;
                } else {
                    $isActive = false;
                }

                $parts->unshift(
                    new ArrayData(
                        array(
                            'MenuTitle' => $page->MenuTitle,
                            'Title'     => $page->Title,
                            'Link'      => $link,
                            'Parent'    => $page->Parent,
                            'IsActive'  => $isActive,
                        )
                    )
                );
            }
            $page = $page->Parent;
        }
        return $parts;
    }
    
    /**
     * returns the breadcrumbs as ArrayList for use in controls with product title
     * 
     * @param int    $maxDepth       maximum depth level of shown pages in breadcrumbs
     * @param bool   $unlinked       true, if the breadcrumbs should be displayed without links
     * @param string $stopAtPageType name of pagetype to stop at
     * @param bool   $showHidden     true, if hidden pages should be displayed in breadcrumbs
     *
     * @return ArrayList 
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
        $this->redirect($frontPage->RelativeLink());
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
                    'SubElements' => new ArrayList($items),
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
     * used to determine weather something should be shown on a template or not
     *
     * @return bool
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function showPricesGross() {
        $pricetype  = SilvercartConfig::Pricetype();
        $member     = SilvercartCustomer::currentUser();
        
        if ($member instanceof Member &&
            $member->doesNotHaveToPayTaxes()) {
            $pricetype = 'net';
        }
        
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
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function showPricesNet() {
        $pricetype  = SilvercartConfig::Pricetype();
        $member     = SilvercartCustomer::currentUser();
        
        if ($member instanceof Member &&
            $member->doesNotHaveToPayTaxes()) {
            $pricetype = 'net';
        }
        
        if ($pricetype == "net") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return the given number of topseller products as DataList.
     * 
     * We use caching here, so check the cache first if you don't get the
     * desired results.
     *
     * @param int $nrOfProducts The number of products to return
     *
     * @return mixed DataList|Boolean false
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
            
            $result = new DataList($products);
        }

        return $result;
    }

    /**
     * We load the special offers productgroup page here.
     *
     * @param string $groupIdentifier Identifier of the product group
     * @param int    $nrOfProducts    The number of products to return
     *
     * @return DataList
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

        $result = new DataList($products);

        return $result;
    }
    
    /**
     * Returns the shoppingcart of the current user or false if there's no
     * member object registered.
     * 
     * @return mixed false|SilvercartShoppingCart
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.11.2014
     */
    public function SilvercartShoppingCart() {
        $controller = Controller::curr();

        if ($this->class == $controller->class &&
            !SilvercartTools::isIsolatedEnvironment() &&
            !SilvercartTools::isBackendEnvironment()) {

            $member = SilvercartCustomer::currentUser();

            if (!$member) {
                return false;
            }

            return $member->getCart();
        } else {
            return false;
        }
    }
    
    /**
     * Alias for self::SilvercartShoppingCart().
     * 
     * @return SilvercartShoppingCart
     */
    public function getCart() {
        return $this->SilvercartShoppingCart();
    }
    
    /**
     * Builds an associative array of ProductGroups to use in GroupedDropDownFields.
     *
     * @param SiteTree $parent      Expects a SilvercartProductGroupHolder or a SilvercartProductGroupPage
     * @param boolean  $allChildren ???
     * @param boolean  $withParent  ???
     *
     * @return array
     * 
     * @deprecated no uses found. remove before release.
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
    
    /**
     * Returns all payment methods
     *
     * @return DataList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.05.2012
     */
    public function PaymentMethods() {
        $PaymentMethods = SilvercartPaymentMethod::getAllowedPaymentMethodsFor($this->ShippingCountry(), singleton('SilvercartShoppingCart'), true);
        return $PaymentMethods;
    }
    
    /**
     * Returns the current shipping country
     *
     * @return SilvercartCountry
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function ShippingCountry() {
        $customer           = SilvercartCustomer::currentUser();
        $shippingCountry    = null;
        if ($customer) {
            $shippingCountry = $customer->SilvercartShippingAddress()->SilvercartCountry();
        }
        if (is_null($shippingCountry) ||
            $shippingCountry->ID == 0) {
            $shippingCountry = SilvercartCountry::get()->filter(array(
                'ISO2'   => substr(Translatable::get_current_locale(), 3),
                'Active' => 1,
            ))->first();
        }
        return $shippingCountry;
    }
    
    /**
     * Returns the footer columns.
     * 
     * @return DataList
     */
    public function getFooterColumns() {
        $metanavigationHolder = SilvercartMetaNavigationHolder::get()->filter('ClassName', 'SilvercartMetaNavigationHolder');
        return $metanavigationHolder;
    }
    
    /**
     * Returns the link to lost password form dependant on the current locale.
     * 
     * @return string
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.07.2014
     */
    public function LostPasswordLink() {
        $link = Director::baseURL() . 'Security/lostpassword/?locale=' . Translatable::get_current_locale();
        return $link;
    }
    
    /**
     * Get the error message out of session and delete it (from session).
     *
     * @return string
     */
    public function getErrorMessage() {
        $errorMessage = Session::get('Silvercart.errorMessage');
        Session::clear('Silvercart.errorMessage');
        Session::save();
        return $errorMessage;
    }

    /**
     * Set the error message into the session.
     *
     * @param string $errorMessage Error message
     * 
     * @return void
     */
    public function setErrorMessage($errorMessage) {
        Session::set('Silvercart.errorMessage', $errorMessage);
        Session::save();
    }
    
    /**
     * Get the success message out of session and delete it (from session).
     *
     * @return string
     */
    public function getSuccessMessage() {
        $successMessage = Session::get('Silvercart.successMessage');
        Session::clear('Silvercart.successMessage');
        Session::save();
        return $successMessage;
    }

    /**
     * Set the success message into the session.
     *
     * @param string $successMessage Success message
     * 
     * @return void
     */
    public function setSuccessMessage($successMessage) {
        Session::set('Silvercart.successMessage', $successMessage);
        Session::save();
    }
    
}