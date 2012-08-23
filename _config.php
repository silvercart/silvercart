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
//SilvercartProduct::setRequiredAttributes("Price");

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
));

// ----------------------------------------------------------------------------
// Register extensions
// ----------------------------------------------------------------------------
Object::add_extension('Member',                                     'SilvercartCustomer');
Object::add_extension('SiteTree',                                   'Translatable');
Object::add_extension('SiteConfig',                                 'Translatable');
Object::add_extension('SiteConfig',                                 'SilvercartSiteConfig');
Object::add_extension('Group',                                      'SilvercartGroupExtension');
Object::add_extension('ModelAdmin',                                 'SilvercartModelAdminExtension');
Object::add_extension('CMSMain',                                    'SilvercartMainExtension');
Object::add_extension('LeftAndMain',                                'SilvercartLeftAndMainExtension');
Object::add_extension('Security',                                   'CustomHtmlFormPage_Controller');
Object::add_extension('SilvercartProductGroupHolder_Controller',    'SilvercartGroupViewExtension');
Object::add_extension('SilvercartProductGroupPage_Controller',      'SilvercartGroupViewExtension');
Object::add_extension('SilvercartSearchResultsPage_Controller',     'SilvercartGroupViewExtension');
Object::add_extension('SilvercartDeeplinkPage_Controller',          'SilvercartGroupViewExtension');
Object::add_extension('Image',                                      'SilvercartImageExtension');

// DataObject Translations
Object::add_extension('SilvercartAvailabilityStatusLanguage',       'SilvercartLanguageExtension');
Object::add_extension('Member_Validator',                           'SilvercartCustomer_Validator');
Object::add_extension('Security',                                   'SilvercartSecurityController');
Object::add_extension('SilvercartCarrierLanguage',                  'SilvercartLanguageExtension');
Object::add_extension('SilvercartCountryLanguage',                  'SilvercartLanguageExtension');
Object::add_extension('SilvercartFileLanguage',                     'SilvercartLanguageExtension');
Object::add_extension('SilvercartImageLanguage',                    'SilvercartLanguageExtension');
Object::add_extension('SilvercartOrderStatusLanguage',              'SilvercartLanguageExtension');
Object::add_extension('SilvercartPaymentMethodLanguage',            'SilvercartLanguageExtension');
Object::add_extension('SilvercartProductConditionLanguage',         'SilvercartLanguageExtension');
Object::add_extension('SilvercartProductLanguage',                  'SilvercartLanguageExtension');
Object::add_extension('SilvercartQuantityUnitLanguage',             'SilvercartLanguageExtension');
Object::add_extension('SilvercartShippingMethodLanguage',           'SilvercartLanguageExtension');
Object::add_extension('SilvercartShopEmailLanguage',                'SilvercartLanguageExtension');
Object::add_extension('SilvercartTaxLanguage',                      'SilvercartLanguageExtension');
Object::add_extension('SilvercartZoneLanguage',                     'SilvercartLanguageExtension');

// Widget Translations
Object::add_extension('SilvercartBargainProductsWidgetLanguage',        'SilvercartLanguageExtension');
Object::add_extension('SilvercartImageSliderImageLanguage',             'SilvercartLanguageExtension');
Object::add_extension('SilvercartImageSliderWidgetLanguage',            'SilvercartLanguageExtension');
Object::add_extension('SilvercartLatestBlogPostsWidgetLanguage',        'SilvercartLanguageExtension');
Object::add_extension('SilvercartProductGroupItemsWidgetLanguage',      'SilvercartLanguageExtension');
Object::add_extension('SilvercartSlidorionProductGroupWidgetLanguage',  'SilvercartLanguageExtension');
Object::add_extension('SilvercartTextWidgetLanguage',                   'SilvercartLanguageExtension');

// Translatable DataObjects
Object::add_extension('SilvercartAvailabilityStatus',               'SilvercartDataObjectMultilingualExtension');

Object::add_extension('SilvercartCarrier',                          'SilvercartDataObjectMultilingualExtension');
Object::add_extension('SilvercartCountry',                          'SilvercartDataObjectMultilingualExtension');
Object::add_extension('SilvercartFile',                             'SilvercartDataObjectMultilingualExtension');
Object::add_extension('SilvercartImage',                            'SilvercartDataObjectMultilingualExtension');
Object::add_extension('SilvercartOrderStatus',                      'SilvercartDataObjectMultilingualExtension');

Object::add_extension('SilvercartProduct',                          'SilvercartDataObjectMultilingualExtension');
Object::add_extension('SilvercartProductCondition',                 'SilvercartDataObjectMultilingualExtension');

Object::add_extension('SilvercartQuantityUnit',                     'SilvercartDataObjectMultilingualExtension');
Object::add_extension('SilvercartShippingMethod',                   'SilvercartDataObjectMultilingualExtension');
Object::add_extension('SilvercartShopEmail',                        'SilvercartDataObjectMultilingualExtension');

Object::add_extension('SilvercartTax',                              'SilvercartDataObjectMultilingualExtension');

Object::add_extension('SilvercartZone',                             'SilvercartDataObjectMultilingualExtension');

// Translatable Widgets
Object::add_extension('SilvercartBargainProductsWidget',        'SilvercartDataObjectMultilingualExtension');
Object::add_extension('SilvercartImageSliderImage',             'SilvercartDataObjectMultilingualExtension');
Object::add_extension('SilvercartImageSliderWidget',            'SilvercartDataObjectMultilingualExtension');
Object::add_extension('SilvercartLatestBlogPostsWidget',        'SilvercartDataObjectMultilingualExtension');
Object::add_extension('SilvercartProductGroupItemsWidget',      'SilvercartDataObjectMultilingualExtension');
Object::add_extension('SilvercartSlidorionProductGroupWidget',  'SilvercartDataObjectMultilingualExtension');
Object::add_extension('SilvercartTextWidget',                   'SilvercartDataObjectMultilingualExtension');
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
/*
SilvercartGroupViewHandler::addGroupView('SilvercartGroupViewList');
SilvercartGroupViewHandler::addGroupView('SilvercartGroupViewTile');
SilvercartGroupViewHandler::addGroupHolderView('SilvercartGroupViewList');
SilvercartGroupViewHandler::addGroupHolderView('SilvercartGroupViewTile');
*/
// ----------------------------------------------------------------------------
// set default group view if not existant
// ----------------------------------------------------------------------------
/*
if (is_null(SilvercartGroupViewHandler::getDefaultGroupView())) {
    SilvercartGroupViewHandler::setDefaultGroupView('SilvercartGroupViewList');
}
if (is_null(SilvercartGroupViewHandler::getDefaultGroupHolderView())) {
    SilvercartGroupViewHandler::setDefaultGroupHolderView('SilvercartGroupViewList');
}

if (method_exists('GoogleSitemap', 'register_dataobject')) {
    GoogleSitemap::register_dataobject('SilvercartProduct', null, '0.2');
}
*/
// ----------------------------------------------------------------------------
// add silvercart branding if no other branding is set
// ----------------------------------------------------------------------------
/*
if (LeftAndMain::$application_link == 'http://www.silverstripe.org/' &&
    LeftAndMain::$application_logo == 'cms/images/mainmenu/logo.gif' &&
    LeftAndMain::$application_name == 'SilverStripe CMS' &&
    LeftAndMain::$application_logo_text = 'SilverStripe') {
    LeftAndMain::setApplicationName(
        'SilverCart - ' . SilvercartConfig::SilvercartVersion() . ' | SilverStripe CMS',
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
/*
SilvercartConfig::registerMenu('orders', _t('SilvercartStoreAdminMenu.ORDERS'));
SilvercartConfig::registerMenu('products', _t('SilvercartStoreAdminMenu.PRODUCTS'));
SilvercartConfig::registerMenu('modules', _t('SilvercartStoreAdminMenu.MODULES'));
SilvercartConfig::registerMenu('config', _t('SilvercartStoreAdminMenu.CONFIG'));
*/

// ----------------------------------------------------------------------------
// Dirty bugfixes ....
// ----------------------------------------------------------------------------
if (array_key_exists('Email', $_POST)) {
    $_POST['Email'] = SilvercartTools::prepareEmailAddress($_POST['Email']);
}

/*
 * DO NOT ENABLE THE CREATION OF TEST DATA IN DEV MODE HERE!
 * THIS SHOULD BE PROJECT SPECIFIC.
 */


