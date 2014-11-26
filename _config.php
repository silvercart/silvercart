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

// ----------------------------------------------------------------------------
// Set spam check for forms
// ----------------------------------------------------------------------------
CustomHtmlForm::useSpamCheckFor('SilvercartContactForm');
CustomHtmlForm::useSpamCheckFor('SilvercartRevocationForm');

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
RequirementsEngine::registerJsFile("silvercart/script/pDialog.js");
RequirementsEngine::registerJsFile("silvercart/script/SilvercartWidget.js");
// Require i18n javascript
Requirements::add_i18n_javascript('silvercart/javascript/lang');

// ----------------------------------------------------------------------------
// Register extensions
// ----------------------------------------------------------------------------
CheckboxSetField::add_extension('SilvercartCheckboxSetField');
DataObject::add_extension('SilvercartDataObject');
Group::add_extension('SilvercartGroupDecorator');
Image::add_extension('SilvercartImageDecorator');
LeftAndMain::add_extension('SilvercartLeftAndMainExtension');
Member::add_extension('SilvercartCustomer');
Member_Validator::add_extension('SilvercartCustomer_Validator');
ModelAdmin::add_extension('SilvercartModelAdminDecorator');
Money::add_extension('SilvercartMoneyExtension');
PaginatedList::add_extension('SilvercartPaginatedList');
Security::add_extension('SilvercartSecurityController');
Security::add_extension('CustomHtmlFormPage_Controller');
SilvercartDeeplinkPage_Controller::add_extension('SilvercartGroupViewDecorator');
SilvercartPage::add_extension('SilvercartPageListWidgetPage');
SilvercartProduct::add_extension('SilvercartDataObject');
SilvercartProductGroupHolder_Controller::add_extension('SilvercartGroupViewDecorator');
SilvercartProductGroupPage_Controller::add_extension('SilvercartGroupViewDecorator');
SilvercartProductLanguage::add_extension('SilvercartDataObject');
SilvercartSearchResultsPage_Controller::add_extension('SilvercartGroupViewDecorator');
SiteTree::add_extension('Translatable');
SiteConfig::add_extension('Translatable');
SiteConfig::add_extension('SilvercartSiteConfig');
WidgetSet::add_extension('SilvercartWidgetSet');
// DataObject Translations
SilvercartAvailabilityStatusLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartCarrierLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartCountryLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartFileLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartImageLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartManufacturerLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartOrderStatusLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartPaymentMethodLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartProductConditionLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartProductLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartQuantityUnitLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartShippingMethodLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartShopEmailLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartTaxLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartZoneLanguage::add_extension('SilvercartLanguageDecorator');

// Widget Translations
SilvercartBargainProductsWidgetLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartImageSliderImageLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartImageSliderWidgetLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartLatestBlogPostsWidgetLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartPageListWidgetLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartProductGroupChildProductsWidgetLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartProductGroupItemsWidgetLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartProductGroupManufacturersWidgetLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartProductGroupNavigationWidgetLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartSlidorionProductGroupWidgetLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartSubNavigationWidgetLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartTextWidgetLanguage::add_extension('SilvercartLanguageDecorator');
SilvercartTextWithLinkWidgetLanguage::add_extension('SilvercartLanguageDecorator');
// Translatable DataObjects
SilvercartAvailabilityStatus::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartCarrier::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartCountry::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartFile::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartImage::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartManufacturer::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartOrderStatus::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartProduct::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartProductCondition::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartQuantityUnit::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartShippingMethod::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartShopEmail::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartTax::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartZone::add_extension('SilvercartDataObjectMultilingualDecorator');
// Translatable Widgets
SilvercartBargainProductsWidget::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartImageSliderImage::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartImageSliderWidget::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartLatestBlogPostsWidget::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartPageListWidget::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartProductGroupChildProductsWidget::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartProductGroupItemsWidget::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartProductGroupManufacturersWidget::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartProductGroupNavigationWidget::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartSlidorionProductGroupWidget::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartSubNavigationWidget::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartTextWidget::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartTextWithLinkWidget::add_extension('SilvercartDataObjectMultilingualDecorator');
SilvercartSortableDataObject::add_sortable_classes(array(
    "SilvercartCarrier",
    "SilvercartProduct",
    "SilvercartImage",
    "SilvercartImageSliderImage",
));

// ----------------------------------------------------------------------------
// Register SilvercartPlugins
// ----------------------------------------------------------------------------
SilvercartConfig::add_extension('SilvercartPluginObjectExtension');
SilvercartContactMessage::add_extension('SilvercartPluginObjectExtension');
SilvercartIncrementPositionQuantityForm::add_extension('SilvercartPluginObjectExtension');
SilvercartOrder::add_extension('SilvercartPluginObjectExtension');
SilvercartProduct::add_extension('SilvercartPluginObjectExtension');
SilvercartProductAddCartFormDetail::add_extension('SilvercartPluginObjectExtension');
SilvercartProductAddCartFormList::add_extension('SilvercartPluginObjectExtension');
SilvercartProductAddCartFormTile::add_extension('SilvercartPluginObjectExtension');
SilvercartProductCsvBulkLoader::add_extension('SilvercartPluginObjectExtension');
SilvercartProductGroupPage_Controller::add_extension('SilvercartPluginObjectExtension');
SilvercartRemovePositionForm::add_extension('SilvercartPluginObjectExtension');
SilvercartShoppingCart::add_extension('SilvercartPluginObjectExtension');
SilvercartShoppingCartPosition::add_extension('SilvercartPluginObjectExtension');

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
// Blacklists for SilvercartRestfulServer
// ----------------------------------------------------------------------------
SilvercartRestfulServer::addApiAccessBlackListFields(
    'Group',
    array(
         'Locked',
         'Sort',
         'IPRestrictions',
         'HtmlEditorConfig',
    )
);
SilvercartRestfulServer::addApiAccessBlackListFields(
    'Member',
    array(
        'NewsletterConfirmationHash',
        'HasAcceptedTermsAndConditions',
        'HasAcceptedRevocationInstruction',
        'Password',
        'RememberLoginToken',
        'NumVisit',
        'LastVisited',
        'Bounced',
        'AutoLoginHash',
        'AutoLoginExpired',
        'PasswordEncryption',
        'Salt',
        'PasswordExpiry',
        'LockedOutUntil',
        'Locale',
        'FailedLoginCount',
        'DateFormat',
        'TimeFormat',
    )
);
SilvercartRestfulServer::addApiAccessBlackListFields(
    'SilvercartOrder',
    array(
        'HasAcceptedTermsAndConditions',
        'HasAcceptedRevocationInstruction',
        'IsSeen',
        'Version',
));
SilvercartRestfulServer::addApiAccessBlackListFields(
    'SilvercartOrderPosition',
    array(
        'numberOfDecimalPlaces',
    )
);
SilvercartRestfulServer::addApiAccessBlackListFields(
    'SilvercartShippingMethod',
    array(
         'isActive',
         'priority',
    )
);

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

if (class_exists('GoogleSitemap') &&
    method_exists('GoogleSitemap', 'register_dataobject')) {
    GoogleSitemap::register_dataobject('SilvercartProduct', null, '0.2');
}

// ----------------------------------------------------------------------------
// add silvercart branding if no other branding is set
// ----------------------------------------------------------------------------
if (Config::inst()->get('LeftAndMain', 'application_name') == 'SilverStripe') {
    Config::inst()->update('LeftAndMain', 'application_name', 'SilverCart - ' . SilvercartConfig::SilvercartFullVersion());
    Config::inst()->update('LeftAndMain', 'application_link', 'http://www.silvercart.org');
}
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
Config::inst()->update('AssetAdmin',            'menuCode', 'files');
Config::inst()->update('CMSFileAddController',  'menuCode', 'files');
Config::inst()->update('CMSSettingsController', 'menuCode', 'config');
Config::inst()->update('SecurityAdmin',         'menuCode', 'customer');

Config::inst()->update('CMSPagesController',    'menuSortIndex', 10);
Config::inst()->update('WidgetSetAdmin',        'menuSortIndex', 20);
Config::inst()->update('ReportAdmin',           'menuSortIndex', 30);

Config::inst()->update('AssetAdmin',            'menuSortIndex', 1);

Config::inst()->update('SecurityAdmin',         'menuSortIndex', 30);

Config::inst()->update('CMSSettingsController', 'menuSortIndex', 1);


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
    SilvercartCleanCacheTask::register_cache_directory($cacheDirectory);
    if (!is_dir($cacheDirectory)) {
        mkdir($cacheDirectory);
    }

    SS_Cache::add_backend(
        $cacheName,
        'File',
        array(
            'cache_dir'              => $cacheDirectory,
            'hashed_directory_level' => 1,
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
