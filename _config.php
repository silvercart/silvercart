<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Config
 * @ignore 
 */

// ----------------------------------------------------------------------------
// Define required attributes
// ----------------------------------------------------------------------------
SilvercartProduct::addRequiredAttribute("Price");

// ----------------------------------------------------------------------------
// disable default pages for SiteTree
// ----------------------------------------------------------------------------
SiteTree::set_create_default_pages(false);

// ----------------------------------------------------------------------------
// Add some URL rules for custom controllers
// ----------------------------------------------------------------------------
Director::addRules(100, array(
    'silvercart-print/$DataObjectName/$DataObjectID'        => 'SilvercartPrint_Controller',
    'silvercart-print-inline/$DataObjectName/$DataObjectID' => 'SilvercartPrint_Controller',
    'silvercart-print-many/$DataObjectName/$DataObjectID'   => 'SilvercartPrint_Controller',
    'api/silvercart'                                        => 'SilvercartRestfulServer',
));

if (!class_exists('RequirementsEngine')) {
    trigger_error('Missing dependency: module RequirementsEngine is missing!', E_USER_ERROR);
}
// ----------------------------------------------------------------------------
// Register CSS requirements
// ----------------------------------------------------------------------------
if (SilvercartConfig::DefaultLayoutEnabled()) {
    RequirementsEngine::registerBlockedFile('cms/css/layout.css');
    RequirementsEngine::registerBlockedFile('cms/css/typography.css');
    RequirementsEngine::registerBlockedFile('cms/css/form.css');
    // Require the default layout and its patches only if it is enabled
    RequirementsEngine::registerCssFile('silvercart/yaml/core/base.css');
    RequirementsEngine::registerThemedCssFile('basemod',            'silvercart');
    RequirementsEngine::registerThemedCssFile('nav_hlist',          'silvercart');
    RequirementsEngine::registerThemedCssFile('nav_vlist',          'silvercart');
    RequirementsEngine::registerThemedCssFile('content',            'silvercart');
    RequirementsEngine::registerThemedCssFile('forms',              'silvercart');
    // Require head tags for IE patches
    RequirementsEngine::registerHeadTag('<!--[if lte IE 9]>',                                                                              'silvercart_iepatch_begin');
    RequirementsEngine::registerHeadTag('<link rel="stylesheet" type="text/css" href="/silvercart/css/patches/patch_layout.css" />',       'silvercart_iepatch');
    RequirementsEngine::registerHeadTag('<![endif]-->',                                                                                    'silvercart_iepatch_end');
    RequirementsEngine::registerHeadTag('<!--[if lte IE 7]>',                                                                              'silvercart_ie7patch_begin');
    RequirementsEngine::registerHeadTag('<link rel="stylesheet" type="text/css" href="/silvercart/css/patches/patch_layout_ie7.css" />',   'silvercart_ie7patch');
    RequirementsEngine::registerHeadTag('<link rel="stylesheet" type="text/css" href="/silvercart/css/patches/patch_forms.css" />',        'silvercart_ie7patch2');
    RequirementsEngine::registerHeadTag('<![endif]-->',                                                                                    'silvercart_ie7patch_end');
}
RequirementsEngine::registerThemedCssFile('SilvercartAddressHolder',                'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartBreadcrumbs',                  'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartCheckout',                     'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartFooter',                       'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartForms',                        'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartGeneral',                      'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartHeaderbar',                    'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartLanguageDropdownField',        'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartPagination',                   'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartPrint',                        'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartProductGroupNavigation',       'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartProductGroupPageControls',     'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartProductGroupHolderList',       'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartProductGroupHolderTile',       'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartProductGroupPageList',         'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartProductGroupPageTile',         'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartProductGroupViewNavigation',   'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartProductPage',                  'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartShoppingCart',                 'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartSiteMap',                      'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartWidget',                       'silvercart');
RequirementsEngine::registerThemedCssFile('jquery.fancybox-1.3.4',                  'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartProductGroupSliderWidget',     'silvercart');
RequirementsEngine::registerThemedCssFile('slidorion',                              'silvercart');
RequirementsEngine::registerThemedCssFile('SilvercartAnythingSlider',               'silvercart');

// ----------------------------------------------------------------------------
// Register JS requirements
// ----------------------------------------------------------------------------
RequirementsEngine::registerJsFile("silvercart/script/document.ready_scripts.js");
RequirementsEngine::registerJsFile("silvercart/script/jquery.pixeltricks.tools.js");
RequirementsEngine::registerJsFile("silvercart/script/fancybox/jquery.fancybox-1.3.4.pack.js");
RequirementsEngine::registerJsFile("silvercart/script/anythingslider/js/jquery.anythingslider.min.js");
RequirementsEngine::registerJsFile("silvercart/script/anythingslider/js/jquery.anythingslider.fx.min.js");
RequirementsEngine::registerJsFile("silvercart/script/anythingslider/js/jquery.easing.1.2.js");
RequirementsEngine::registerJsFile("silvercart/script/jquery.roundabout.min.js");
RequirementsEngine::registerJsFile("silvercart/script/jquery.roundabout-shapes.min.js");
RequirementsEngine::registerJsFile("silvercart/script/jquery.easing.1.3.js");
RequirementsEngine::registerJsFile("silvercart/script/SilvercartProductGroupSliderWidget.js");
RequirementsEngine::registerJsFile("silvercart/script/reflection.js");
RequirementsEngine::registerJsFile("silvercart/script/slidorion/js/jquery.slidorion.js");
// Require i18n javascript
Requirements::add_i18n_javascript('silvercart/javascript/lang');

// ----------------------------------------------------------------------------
// Register extensions
// ----------------------------------------------------------------------------
Object::add_extension('DataObject',                                 'SilvercartDataObject');
Object::add_extension('Member',                                     'SilvercartCustomer');
Object::add_extension('SilvercartPage',                             'SilvercartPageListWidgetPage');
Object::add_extension('SiteTree',                                   'Translatable');
Object::add_extension('SiteConfig',                                 'Translatable');
Object::add_extension('SiteConfig',                                 'SilvercartSiteConfig');
Object::add_extension('Group',                                      'SilvercartGroupDecorator');
Object::add_extension('ModelAdmin',                                 'SilvercartModelAdminDecorator');
Object::add_extension('Money',                                      'SilvercartMoneyExtension');
Object::add_extension('LeftAndMain',                                'SilvercartMain');
Object::add_extension('LeftAndMain',                                'SilvercartLeftAndMainExtension');
Object::add_extension('Security',                                   'SilvercartSecurityController');
Object::add_extension('Security',                                   'CustomHtmlFormPage_Controller');
Object::add_extension('SilvercartProductGroupHolder_Controller',    'SilvercartGroupViewDecorator');
Object::add_extension('SilvercartProductGroupPage_Controller',      'SilvercartGroupViewDecorator');
Object::add_extension('SilvercartSearchResultsPage_Controller',     'SilvercartGroupViewDecorator');
Object::add_extension('SilvercartDeeplinkPage_Controller',          'SilvercartGroupViewDecorator');
Object::add_extension('Image',                                      'SilvercartImageDecorator');
Object::add_extension('SilvercartProduct',                          'SilvercartDataObject');
Object::add_extension('SilvercartProductLanguage',                  'SilvercartDataObject');
Object::add_extension('WidgetSet',                                  'SilvercartWidgetSet');
// DataObject Translations
Object::add_extension('SilvercartAvailabilityStatusLanguage',       'SilvercartLanguageDecorator');
Object::add_extension('Member_Validator',                           'SilvercartCustomer_Validator');
Object::add_extension('Security',                                   'SilvercartSecurityController');
Object::add_extension('SilvercartCarrierLanguage',                  'SilvercartLanguageDecorator');
Object::add_extension('SilvercartCountryLanguage',                  'SilvercartLanguageDecorator');
Object::add_extension('SilvercartFileLanguage',                     'SilvercartLanguageDecorator');
Object::add_extension('SilvercartImageLanguage',                    'SilvercartLanguageDecorator');
Object::add_extension('SilvercartManufacturerLanguage',             'SilvercartLanguageDecorator');
Object::add_extension('SilvercartOrderStatusLanguage',              'SilvercartLanguageDecorator');
Object::add_extension('SilvercartPaymentMethodLanguage',            'SilvercartLanguageDecorator');
Object::add_extension('SilvercartProductConditionLanguage',         'SilvercartLanguageDecorator');
Object::add_extension('SilvercartProductLanguage',                  'SilvercartLanguageDecorator');
Object::add_extension('SilvercartQuantityUnitLanguage',             'SilvercartLanguageDecorator');
Object::add_extension('SilvercartShippingMethodLanguage',           'SilvercartLanguageDecorator');
Object::add_extension('SilvercartShopEmailLanguage',                'SilvercartLanguageDecorator');
Object::add_extension('SilvercartTaxLanguage',                      'SilvercartLanguageDecorator');
Object::add_extension('SilvercartZoneLanguage',                     'SilvercartLanguageDecorator');

// Widget Translations
Object::add_extension('SilvercartBargainProductsWidgetLanguage',            'SilvercartLanguageDecorator');
Object::add_extension('SilvercartImageSliderImageLanguage',                 'SilvercartLanguageDecorator');
Object::add_extension('SilvercartImageSliderWidgetLanguage',                'SilvercartLanguageDecorator');
Object::add_extension('SilvercartLatestBlogPostsWidgetLanguage',            'SilvercartLanguageDecorator');
Object::add_extension('SilvercartPageListWidgetLanguage',                   'SilvercartLanguageDecorator');
Object::add_extension('SilvercartProductGroupChildProductsWidgetLanguage',  'SilvercartLanguageDecorator');
Object::add_extension('SilvercartProductGroupItemsWidgetLanguage',          'SilvercartLanguageDecorator');
Object::add_extension('SilvercartProductGroupManufacturersWidgetLanguage',  'SilvercartLanguageDecorator');
Object::add_extension('SilvercartSlidorionProductGroupWidgetLanguage',      'SilvercartLanguageDecorator');
Object::add_extension('SilvercartSubNavigationWidgetLanguage',              'SilvercartLanguageDecorator');
Object::add_extension('SilvercartTextWidgetLanguage',                       'SilvercartLanguageDecorator');
// Translatable DataObjects
Object::add_extension('SilvercartAvailabilityStatus',               'SilvercartDataObjectMultilingualDecorator');

Object::add_extension('SilvercartCarrier',                          'SilvercartDataObjectMultilingualDecorator');
Object::add_extension('SilvercartCountry',                          'SilvercartDataObjectMultilingualDecorator');
Object::add_extension('SilvercartFile',                             'SilvercartDataObjectMultilingualDecorator');
Object::add_extension('SilvercartImage',                            'SilvercartDataObjectMultilingualDecorator');
Object::add_extension('SilvercartManufacturer',                     'SilvercartDataObjectMultilingualDecorator');
Object::add_extension('SilvercartOrderStatus',                      'SilvercartDataObjectMultilingualDecorator');

Object::add_extension('SilvercartProduct',                          'SilvercartDataObjectMultilingualDecorator');
Object::add_extension('SilvercartProductCondition',                 'SilvercartDataObjectMultilingualDecorator');

Object::add_extension('SilvercartQuantityUnit',                     'SilvercartDataObjectMultilingualDecorator');
Object::add_extension('SilvercartShippingMethod',                   'SilvercartDataObjectMultilingualDecorator');
Object::add_extension('SilvercartShopEmail',                        'SilvercartDataObjectMultilingualDecorator');

Object::add_extension('SilvercartTax',                              'SilvercartDataObjectMultilingualDecorator');

Object::add_extension('SilvercartZone',                             'SilvercartDataObjectMultilingualDecorator');

// Translatable Widgets
Object::add_extension('SilvercartBargainProductsWidget',            'SilvercartDataObjectMultilingualDecorator');
Object::add_extension('SilvercartImageSliderImage',                 'SilvercartDataObjectMultilingualDecorator');
Object::add_extension('SilvercartImageSliderWidget',                'SilvercartDataObjectMultilingualDecorator');
Object::add_extension('SilvercartLatestBlogPostsWidget',            'SilvercartDataObjectMultilingualDecorator');
Object::add_extension('SilvercartPageListWidget',                   'SilvercartDataObjectMultilingualDecorator');
Object::add_extension('SilvercartProductGroupChildProductsWidget',  'SilvercartDataObjectMultilingualDecorator');
Object::add_extension('SilvercartProductGroupItemsWidget',          'SilvercartDataObjectMultilingualDecorator');
Object::add_extension('SilvercartProductGroupManufacturersWidget',  'SilvercartDataObjectMultilingualDecorator');
Object::add_extension('SilvercartSlidorionProductGroupWidget',      'SilvercartDataObjectMultilingualDecorator');
Object::add_extension('SilvercartSubNavigationWidget',              'SilvercartDataObjectMultilingualDecorator');
Object::add_extension('SilvercartTextWidget',                       'SilvercartDataObjectMultilingualDecorator');
SilvercartSortableDataObject::add_sortable_classes(array(
    "SilvercartCarrier",
    "SilvercartProduct",
    "SilvercartImage",
    "SilvercartImageSliderImage",
));

// ----------------------------------------------------------------------------
// Register SilvercartPlugins
// ----------------------------------------------------------------------------
Object::add_extension('SilvercartConfig',                           'SilvercartPluginObjectDecorator');
Object::add_extension('SilvercartContactMessage',                   'SilvercartPluginObjectDecorator');
Object::add_extension('SilvercartIncrementPositionQuantityForm',    'SilvercartPluginObjectDecorator');
Object::add_extension('SilvercartOrder',                            'SilvercartPluginObjectDecorator');
Object::add_extension('SilvercartProduct',                          'SilvercartPluginObjectDecorator');
Object::add_extension('SilvercartProductAddCartFormDetail',         'SilvercartPluginObjectDecorator');
Object::add_extension('SilvercartProductAddCartFormList',           'SilvercartPluginObjectDecorator');
Object::add_extension('SilvercartProductAddCartFormTile',           'SilvercartPluginObjectDecorator');
Object::add_extension('SilvercartProductCsvBulkLoader',             'SilvercartPluginObjectDecorator');
Object::add_extension('SilvercartProductGroupPage_Controller',      'SilvercartPluginObjectDecorator');
Object::add_extension('SilvercartRemovePositionForm',               'SilvercartPluginObjectDecorator');
Object::add_extension('SilvercartShoppingCart',                     'SilvercartPluginObjectDecorator');
Object::add_extension('SilvercartShoppingCartPosition',             'SilvercartPluginObjectDecorator');

SilvercartPlugin::registerPluginProvider('SilvercartConfig',                        'SilvercartConfigPluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartContactMessage',                'SilvercartContactMessagePluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartIncrementPositionQuantityForm', 'SilvercartIncrementPositionQuantityFormPluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartOrder',                         'SilvercartOrderPluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartOrderPosition',                 'SilvercartOrderPositionPluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartProduct',                       'SilvercartProductPluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartProductAddCartFormDetail',      'SilvercartProductAddCartFormDetailPluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartProductAddCartFormList',        'SilvercartProductAddCartFormListPluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartProductAddCartFormTile',        'SilvercartProductAddCartFormTilePluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartProductAddCartForm',            'SilvercartProductAddCartFormPluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartProductCsvBulkLoader',          'SilvercartProductCsvBulkLoaderPluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartProductGroupPage_Controller',   'SilvercartProductGroupPage_ControllerPluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartRemovePositionForm',            'SilvercartRemovePositionFormPluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartShoppingCart',                  'SilvercartShoppingCartPluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartShoppingCartPosition',          'SilvercartShoppingCartPositionPluginProvider');

// use custom classes
Object::useCustomClass('Member_ForgotPasswordEmail', 'SilvercartCustomer_ForgotPasswordEmail');

// configure WidgetSet
WidgetSetWidgetExtension::preventWidgetCreationByClass('SilvercartWidget');

SilvercartGridFieldBatchController::addBatchActionFor('SilvercartOrder', 'SilvercartGridFieldBatchAction_ChangeOrderStatus');
SilvercartGridFieldBatchController::addBatchActionFor('SilvercartOrder', 'SilvercartGridFieldBatchAction_PrintOrders');
SilvercartGridFieldBatchController::addBatchActionFor('SilvercartOrder', 'SilvercartGridFieldBatchAction_MarkAsSeen');
SilvercartGridFieldBatchController::addBatchActionFor('SilvercartOrder', 'SilvercartGridFieldBatchAction_MarkAsNotSeen');

SilvercartGridFieldBatchController::addBatchActionFor('SilvercartProduct', 'SilvercartGridFieldBatchAction_ActivateDataObject');
SilvercartGridFieldBatchController::addBatchActionFor('SilvercartProduct', 'SilvercartGridFieldBatchAction_DeactivateDataObject');
SilvercartGridFieldBatchController::addBatchActionFor('SilvercartProduct', 'SilvercartGridFieldBatchAction_ChangeAvailabilityStatus');
SilvercartGridFieldBatchController::addBatchActionFor('SilvercartProduct', 'SilvercartGridFieldBatchAction_ChangeManufacturer');
SilvercartGridFieldBatchController::addBatchActionFor('SilvercartProduct', 'SilvercartGridFieldBatchAction_ChangeProductGroup');

// ----------------------------------------------------------------------------
// Register i18n plugins
// ----------------------------------------------------------------------------
/*
i18n::register_plugin('silvercart_i18n_de_DE_plugin', array('SilvercartI18nPlugin', 'de_DE'), 99);


if (is_null(TableListField_ItemRequest::$allowed_actions)) {
    TableListField_ItemRequest::$allowed_actions = array('printDataObject');
}
*/
// ----------------------------------------------------------------------------
// Enable DataObject validation
// ----------------------------------------------------------------------------
Member::set_validation_enabled(true);

// ----------------------------------------------------------------------------
// Define path constants
// ----------------------------------------------------------------------------
$path = dirname(__FILE__) . '/';
$relPath = substr(Director::makeRelative($path), 1);

define('PIXELTRICKS_CHECKOUT_BASE_PATH', $path);
define('PIXELTRICKS_CHECKOUT_BASE_PATH_REL', $relPath);

// ----------------------------------------------------------------------------
// Register at required modules
// ----------------------------------------------------------------------------
CustomHtmlForm::registerModule('silvercart', 49);
CustomHtmlFormActionHandler::addHandler('SilvercartActionHandler');

// ----------------------------------------------------------------------------
// Set spam check for forms
// ----------------------------------------------------------------------------
CustomHtmlForm::useSpamCheckFor('SilvercartContactForm');

// ----------------------------------------------------------------------------
// Check if the page.php descends from the SilvercartPage
// ----------------------------------------------------------------------------

if (class_exists('Page')) {
    $ext = new ReflectionClass('Page');

    if ($ext->getParentClass()->getName() != 'SilvercartPage') {
        throw new Exception('Class "Page" has to extend "SilvercartPage".');
    }
}
if (class_exists('Page_Controller')) {
    $ext = new ReflectionClass('Page_Controller');

    if ($ext->getParentClass()->getName() != 'SilvercartPage_Controller') {
        throw new Exception('Class "Page_Controller" has to extend "SilvercartPage_Controller".');
    }
}

// ----------------------------------------------------------------------------
// add possible group views
// ----------------------------------------------------------------------------
SilvercartGroupViewHandler::addGroupView('SilvercartGroupViewList');
SilvercartGroupViewHandler::addGroupView('SilvercartGroupViewTile');
SilvercartGroupViewHandler::addGroupHolderView('SilvercartGroupViewList');
SilvercartGroupViewHandler::addGroupHolderView('SilvercartGroupViewTile');
// ----------------------------------------------------------------------------
// set default group view if not existant
// ----------------------------------------------------------------------------

if (is_null(SilvercartGroupViewHandler::getDefaultGroupView())) {
    SilvercartGroupViewHandler::setDefaultGroupView('SilvercartGroupViewList');
}
if (is_null(SilvercartGroupViewHandler::getDefaultGroupHolderView())) {
    SilvercartGroupViewHandler::setDefaultGroupHolderView('SilvercartGroupViewList');
}

if (method_exists('GoogleSitemap', 'register_dataobject')) {
    GoogleSitemap::register_dataobject('SilvercartProduct', null, '0.2');
}

// ----------------------------------------------------------------------------
// add silvercart branding if no other branding is set
// ----------------------------------------------------------------------------
/*
if (LeftAndMain::$application_link == 'http://www.silverstripe.org/' &&
    LeftAndMain::$application_logo == 'cms/images/mainmenu/logo.gif' &&
    LeftAndMain::$application_name == 'SilverStripe CMS' &&
    LeftAndMain::$application_logo_text = 'SilverStripe') {
    LeftAndMain::setApplicationName(
        'SilverCart - ' . SilvercartConfig::SilvercartFullVersion() . ' | SilverStripe CMS',
        'SilverCart<br />eCommerce software',
        'http://www.silvercart.org'
    );
    LeftAndMain::set_loading_image(
        '/silvercart/images/logo.jpg'
    );
}
*/
// ----------------------------------------------------------------------------
// Register menus for the storeadmin
// ----------------------------------------------------------------------------
SilvercartConfig::registerMenu('default',   _t('SilvercartStoreAdminMenu.DEFAULT'));
SilvercartConfig::registerMenu('files',     _t('SilvercartStoreAdminMenu.FILES'));
SilvercartConfig::registerMenu('orders',    _t('SilvercartStoreAdminMenu.ORDERS'));
SilvercartConfig::registerMenu('products',  _t('SilvercartStoreAdminMenu.PRODUCTS'));
SilvercartConfig::registerMenu('handling',  _t('SilvercartStoreAdminMenu.HANDLING'));
SilvercartConfig::registerMenu('customer',  _t('SilvercartStoreAdminMenu.CUSTOMER'));
SilvercartConfig::registerMenu('config',    _t('SilvercartStoreAdminMenu.CONFIG'));
SilvercartConfig::registerMenu('modules',   _t('SilvercartStoreAdminMenu.MODULES'));

//AssetAdmin::$menuCode = 'files';
Object::set_static('AssetAdmin',            'menuCode', 'files');
Object::set_static('CMSFileAddController',  'menuCode', 'files');
Object::set_static('CMSSettingsController', 'menuCode', 'config');
Object::set_static('SecurityAdmin',         'menuCode', 'customer');

Object::set_static('CMSPagesController',    'menuSortIndex', 10);
Object::set_static('WidgetSetAdmin',        'menuSortIndex', 20);
Object::set_static('ReportAdmin',           'menuSortIndex', 30);

Object::set_static('AssetAdmin',            'menuSortIndex', 1);

Object::set_static('SecurityAdmin',         'menuSortIndex', 30);

Object::set_static('CMSSettingsController', 'menuSortIndex', 1);


// ----------------------------------------------------------------------------
// Dirty bugfixes ....
// ----------------------------------------------------------------------------
if (array_key_exists('Email', $_POST)) {
    $_POST['Email'] = SilvercartTools::prepareEmailAddress($_POST['Email']);
}

$cacheDirectories = array(
    'cacheblock' => getTempFolder() . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'cacheblock',
    'silvercart' => getTempFolder() . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'silvercart',
);

if (Director::isDev()) {
    $cachelifetime = 1;
} else {
    $cachelifetime = 86400;
}

foreach ($cacheDirectories as $cacheName => $cacheDirectory) {
    if (!is_dir($cacheDirectory)) {
        mkdir($cacheDirectory);
    }

    SS_Cache::add_backend(
        $cacheName,
        'File',
        array(
            'cache_dir'              => $cacheDirectory,
            'hashed_directory_level' => 2,
        )
    );
    SS_Cache::set_cache_lifetime($cacheName, $cachelifetime);
    SS_Cache::pick_backend($cacheName, $cacheName);
}
SS_Cache::set_cache_lifetime('aggregate', $cachelifetime);

/*
 * DO NOT ENABLE THE CREATION OF TEST DATA IN DEV MODE HERE!
 * THIS SHOULD BE PROJECT SPECIFIC.
 */
