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
SilvercartProduct::setRequiredAttributes("Price");

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
));

// ----------------------------------------------------------------------------
// Register extensions
// ----------------------------------------------------------------------------
Object::add_extension('ComponentSet',                               'SilvercartComponentSetDecorator');
Object::add_extension('SiteTree',                                   'Translatable');
Object::add_extension('SiteConfig',                                 'Translatable');
Object::add_extension('SiteConfig',                                 'SilvercartSiteConfig');
Object::add_extension('Member',                                     'SilvercartCustomer');
Object::add_extension('Member_Validator',                           'SilvercartCustomer_Validator');
Object::add_extension('Group',                                      'SilvercartGroupDecorator');
Object::add_extension('ModelAdmin',                                 'SilvercartModelAdminDecorator');
Object::add_extension('ModelAdmin_RecordController',                'SilvercartModelAdmin_RecordControllerDecorator');
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
// DataObject Translations
DataObject::add_extension('SilvercartAvailabilityStatusLanguage',       'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartCarrierLanguage',                  'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartCountryLanguage',                  'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartFileLanguage',                     'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartImageLanguage',                    'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartOrderStatusLanguage',              'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartPaymentMethodLanguage',            'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartProductConditionLanguage',         'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartProductLanguage',                  'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartQuantityUnitLanguage',             'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartShippingMethodLanguage',           'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartShopEmailLanguage',                'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartTaxLanguage',                      'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartZoneLanguage',                     'SilvercartLanguageDecorator');
// Widget Translations
DataObject::add_extension('SilvercartBargainProductsWidgetLanguage',        'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartImageSliderImageLanguage',             'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartImageSliderWidgetLanguage',            'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartLatestBlogPostsWidgetLanguage',        'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartProductGroupItemsWidgetLanguage',      'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartSlidorionProductGroupWidgetLanguage',  'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartTextWidgetLanguage',                   'SilvercartLanguageDecorator');
// Translatable DataObjects
DataObject::add_extension('SilvercartAvailabilityStatus',               'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartCarrier',                          'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartCountry',                          'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartFile',                             'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartImage',                            'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartOrderStatus',                      'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartProduct',                          'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartProductCondition',                 'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartQuantityUnit',                     'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartShippingMethod',                   'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartShopEmail',                        'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartTax',                              'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartZone',                             'SilvercartDataObjectMultilingualDecorator');
// Translatable Widgets
DataObject::add_extension('SilvercartBargainProductsWidget',        'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartImageSliderImage',             'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartImageSliderWidget',            'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartLatestBlogPostsWidget',        'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartProductGroupItemsWidget',      'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartSlidorionProductGroupWidget',  'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartTextWidget',                   'SilvercartDataObjectMultilingualDecorator');
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
        'SilverCart - ' . SilvercartConfig::SilvercartVersion() . ' | SilverStripe CMS',
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

/*
 * DO NOT ENABLE THE CREATION OF TEST DATA IN DEV MODE HERE!
 * THIS SHOULD BE PROJECT SPECIFIC.
 */


