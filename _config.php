<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or * (at your option) any later version.
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
 * @subpackage Config
 * @ignore 
 */

// requirenments for PHP 5.3
if (strpos(phpversion(), '5.3') === 0) {
    date_default_timezone_set('Europe/Berlin');
}

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
Director::addRules(50, array(
    'silvercart-print/$DataObjectName/$DataObjectID'        => 'SilvercartPrint_Controller',
    'silvercart-print-inline/$DataObjectName/$DataObjectID' => 'SilvercartPrint_Controller',
    'silvercart-print-many/$DataObjectName/$DataObjectID'   => 'SilvercartPrint_Controller',
    'api/silvercart'                                        => 'SilvercartRestfulServer',
));

// ----------------------------------------------------------------------------
// Set spam check for forms
// ----------------------------------------------------------------------------
CustomHtmlForm::useSpamCheckFor('SilvercartContactForm');

// ----------------------------------------------------------------------------
// Register CSS requirements
// ----------------------------------------------------------------------------
if (SilvercartConfig::DefaultLayoutEnabled()) {
    RequirementsEngine::registerBlockedFile('cms/css/layout.css');
    RequirementsEngine::registerBlockedFile('cms/css/typography.css');
    RequirementsEngine::registerBlockedFile('cms/css/form.css');
    // Require the default layout and its patches only if it is enabled
    RequirementsEngine::registerThemedCssFile('base');
    RequirementsEngine::registerThemedCssFile('basemod');
    RequirementsEngine::registerThemedCssFile('nav_shinybuttons');
    RequirementsEngine::registerThemedCssFile('nav_vlist');
    RequirementsEngine::registerThemedCssFile('content');
    RequirementsEngine::registerThemedCssFile('forms');
    RequirementsEngine::registerThemedCssFile('patch_forms');
    // Require head tags for IE patches
    RequirementsEngine::registerHeadTag('<!--[if lte IE 9]>',                                                                              'silvercart_iepatch_begin');
    RequirementsEngine::registerHeadTag('<link href="/silvercart/css/patches/patch_layout.css" rel="stylesheet" type="text/css" />',       'silvercart_iepatch');
    RequirementsEngine::registerHeadTag('<![endif]-->',                                                                                    'silvercart_iepatch_end');
    RequirementsEngine::registerHeadTag('<!--[if lte IE 7]>',                                                                              'silvercart_ie7patch_begin');
    RequirementsEngine::registerHeadTag('<link href="/silvercart/css/patches/patch_layout_ie7.css" rel="stylesheet" type="text/css" />',   'silvercart_ie7patch');
    RequirementsEngine::registerHeadTag('<![endif]-->',                                                                                    'silvercart_ie7patch_end');
}
RequirementsEngine::registerThemedCssFile('SilvercartAddressHolder');
RequirementsEngine::registerThemedCssFile('SilvercartBreadcrumbs');
RequirementsEngine::registerThemedCssFile('SilvercartCheckout');
RequirementsEngine::registerThemedCssFile('SilvercartFooter');
RequirementsEngine::registerThemedCssFile('SilvercartForms');
RequirementsEngine::registerThemedCssFile('SilvercartGeneral');
RequirementsEngine::registerThemedCssFile('SilvercartHeaderbar');
RequirementsEngine::registerThemedCssFile('SilvercartLanguageDropdownField');
RequirementsEngine::registerThemedCssFile('SilvercartPagination');
RequirementsEngine::registerThemedCssFile('SilvercartPrint');
RequirementsEngine::registerThemedCssFile('SilvercartProductGroupNavigation');
RequirementsEngine::registerThemedCssFile('SilvercartProductGroupPageControls');
RequirementsEngine::registerThemedCssFile('SilvercartProductGroupHolderList');
RequirementsEngine::registerThemedCssFile('SilvercartProductGroupHolderTile');
RequirementsEngine::registerThemedCssFile('SilvercartProductGroupPageList');
RequirementsEngine::registerThemedCssFile('SilvercartProductGroupPageTile');
RequirementsEngine::registerThemedCssFile('SilvercartProductGroupViewNavigation');
RequirementsEngine::registerThemedCssFile('SilvercartProductPage');
RequirementsEngine::registerThemedCssFile('SilvercartShoppingCart');
RequirementsEngine::registerThemedCssFile('SilvercartSiteMap');
RequirementsEngine::registerThemedCssFile('SilvercartWidget');
RequirementsEngine::registerThemedCssFile('jquery.fancybox-1.3.4');
RequirementsEngine::registerThemedCssFile('SilvercartProductGroupSliderWidget');
RequirementsEngine::registerThemedCssFile("slidorion");
RequirementsEngine::registerThemedCssFile('SilvercartAnythingSlider');

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
// Require i18n javascript
Requirements::add_i18n_javascript('silvercart/javascript/lang');

// ----------------------------------------------------------------------------
// Register extensions
// ----------------------------------------------------------------------------
Object::add_extension('SilvercartPage',                             'SilvercartPageListWidgetPage');

Object::add_extension('ComponentSet',                               'SilvercartComponentSetDecorator');
Object::add_extension('SiteTree',                                   'Translatable');
Object::add_extension('SiteConfig',                                 'Translatable');
Object::add_extension('SiteConfig',                                 'SilvercartSiteConfig');
Object::add_extension('Member',                                     'SilvercartCustomer');
Object::add_extension('Member_Validator',                           'SilvercartCustomer_Validator');
Object::add_extension('Group',                                      'SilvercartGroupDecorator');
Object::add_extension('ModelAdmin',                                 'SilvercartModelAdminDecorator');
Object::add_extension('ModelAdmin_CollectionController',            'SilvercartModelAdmin_CollectionController');
Object::add_extension('ModelAdmin_RecordController',                'SilvercartModelAdmin_RecordControllerDecorator');
Object::add_extension('Money',                                      'SilvercartMoney');
Object::add_extension('TableListField',                             'SilvercartTableListFieldDecorator');
Object::add_extension('TableListField_Item',                        'SilvercartTableListField_ItemDecorator');
Object::add_extension('TableListField_ItemRequest',                 'SilvercartTableListField_ItemRequestDecorator');
Object::add_extension('CMSMain',                                    'SilvercartMain');
Object::add_extension('LeftAndMain',                                'SilvercartLeftAndMain');
Object::add_extension('Security',                                   'SilvercartSecurityController');
Object::add_extension('Security',                                   'CustomHtmlFormPage_Controller');
Object::add_extension('SilvercartProductGroupHolder_Controller',    'SilvercartGroupViewDecorator');
Object::add_extension('SilvercartProductGroupPage_Controller',      'SilvercartGroupViewDecorator');
Object::add_extension('SilvercartSearchResultsPage_Controller',     'SilvercartGroupViewDecorator');
Object::add_extension('SilvercartDeeplinkPage_Controller',          'SilvercartGroupViewDecorator');
Object::add_extension('Image',                                      'SilvercartImageExtension');
Object::add_extension('SilvercartProduct',                          'SilvercartDataObject');
Object::add_extension('SilvercartProductLanguage',                  'SilvercartDataObject');
Object::add_extension('DataObject',                                 'SilvercartDataObject');
Object::add_extension('DataObjectSet',                              'SilvercartDataObjectSet');
// DataObject Translations
Object::add_extension('SilvercartAvailabilityStatusLanguage',       'SilvercartLanguageDecorator');
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
Object::add_extension('SilvercartConfig',                           'SilvercartPluginObjectExtension');
Object::add_extension('SilvercartContactMessage',                   'SilvercartPluginObjectExtension');
Object::add_extension('SilvercartIncrementPositionQuantityForm',    'SilvercartPluginObjectExtension');
Object::add_extension('SilvercartOrder',                            'SilvercartPluginObjectExtension');
Object::add_extension('SilvercartProduct',                          'SilvercartPluginObjectExtension');
Object::add_extension('SilvercartProduct_CollectionController',     'SilvercartPluginObjectExtension');
Object::add_extension('SilvercartProductAddCartFormDetail',         'SilvercartPluginObjectExtension');
Object::add_extension('SilvercartProductAddCartFormList',           'SilvercartPluginObjectExtension');
Object::add_extension('SilvercartProductAddCartFormTile',           'SilvercartPluginObjectExtension');
Object::add_extension('SilvercartProductCsvBulkLoader',             'SilvercartPluginObjectExtension');
Object::add_extension('SilvercartProductGroupPage_Controller',      'SilvercartPluginObjectExtension');
Object::add_extension('SilvercartRemovePositionForm',               'SilvercartPluginObjectExtension');
Object::add_extension('SilvercartShoppingCart',                     'SilvercartPluginObjectExtension');
Object::add_extension('SilvercartShoppingCartPosition',             'SilvercartPluginObjectExtension');

SilvercartPlugin::registerPluginProvider('SilvercartConfig',                        'SilvercartConfigPluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartContactMessage',                'SilvercartContactMessagePluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartIncrementPositionQuantityForm', 'SilvercartIncrementPositionQuantityFormPluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartOrder',                         'SilvercartOrderPluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartOrderPosition',                 'SilvercartOrderPositionPluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartProduct',                       'SilvercartProductPluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartProduct_CollectionController',  'SilvercartProduct_CollectionControllerPluginProvider');
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

SilvercartRestfulServer::addApiAccessBlackListFields('Member', array(
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
 ));

// ----------------------------------------------------------------------------
// Register i18n plugins
// ----------------------------------------------------------------------------
i18n::register_plugin('silvercart_i18n_de_DE_plugin', array('SilvercartI18nPlugin', 'de_DE'), 99);


if (is_null(TableListField_ItemRequest::$allowed_actions)) {
    TableListField_ItemRequest::$allowed_actions = array('printDataObject');
}

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

// ----------------------------------------------------------------------------
// Register menus for the storeadmin
// ----------------------------------------------------------------------------
SilvercartConfig::registerMenu('orders', _t('SilvercartStoreAdminMenu.ORDERS'));
SilvercartConfig::registerMenu('products', _t('SilvercartStoreAdminMenu.PRODUCTS'));
SilvercartConfig::registerMenu('modules', _t('SilvercartStoreAdminMenu.MODULES'));
SilvercartConfig::registerMenu('config', _t('SilvercartStoreAdminMenu.CONFIG'));

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
