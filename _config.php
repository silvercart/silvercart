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
// Register extensions
// ----------------------------------------------------------------------------
Object::add_extension('SiteTree',           'Translatable');
Object::add_extension('SiteConfig',         'Translatable');
Object::add_extension('SiteConfig',         'SilvercartSiteConfig');
Object::add_extension('Member',             'SilvercartCustomer');
Object::add_extension('Group',              'SilvercartGroupDecorator');
Object::add_extension('ModelAdmin',         'SilvercartModelAdminDecorator');
Object::add_extension('CMSMain',            'SilvercartMain');
Object::add_extension('Security',           'SilvercartSecurityController');
Object::add_extension('Security',           'CustomHtmlFormPage_Controller');
DataObject::add_extension('SilvercartProductGroupHolder_Controller',    'SilvercartGroupViewDecorator');
DataObject::add_extension('SilvercartProductGroupPage_Controller',      'SilvercartGroupViewDecorator');
DataObject::add_extension('SilvercartSearchResultsPage_Controller',     'SilvercartGroupViewDecorator');
DataObject::add_extension('SilvercartDeeplinkPage_Controller',          'SilvercartGroupViewDecorator');
DataObject::add_extension('Image',                                      'SilvercartImageExtension');
DataObject::add_extension('SilvercartProductLanguage',                  'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartShippingMethodLanguage',           'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartProductConditionLanguage',         'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartOrderStatusLanguage',              'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartAvailabilityStatusLanguage',       'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartQuantityUnitLanguage',             'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartZoneLanguage',                     'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartFileLanguage',                     'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartImageLanguage',                    'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartImageSliderImageLanguage',         'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartTaxLanguage',                      'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartCarrierLanguage',                  'SilvercartLanguageDecorator');
DataObject::add_extension('SilvercartProduct',                          'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartShippingMethod',                   'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartProductCondition',                 'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartOrderStatus',                      'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartAvailabilityStatus',               'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartQuantityUnit',                     'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartZone',                             'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartFile',                             'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartImage',                            'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartImageSliderImage',                 'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartTax',                              'SilvercartDataObjectMultilingualDecorator');
DataObject::add_extension('SilvercartCarrier',                          'SilvercartDataObjectMultilingualDecorator');
SortableDataObject::add_sortable_classes(array(
    "SilvercartProduct",
    "SilvercartImage",
    "SilvercartImageSliderImage",
));

// ----------------------------------------------------------------------------
// Register SilvercartPlugins
// ----------------------------------------------------------------------------
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

SilvercartPlugin::registerPluginProvider('SilvercartContactMessage',                'SilvercartContactMessagePluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartIncrementPositionQuantityForm', 'SilvercartIncrementPositionQuantityFormPluginProvider');
SilvercartPlugin::registerPluginProvider('SilvercartOrder',                         'SilvercartOrderPluginProvider');
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
// Register TaskNotificationChannels
// ----------------------------------------------------------------------------
SilvercartTaskNotificationHandler::registerNotificationChannel('SilvercartProductPriceUpdate');

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
        '<p style="font-size: 11px; line-height: 11px;">eCommerce software.<br/>Open-source. You\'ll love it.</p>',
        'http://www.silvercart.org'
    );
    LeftAndMain::set_loading_image(
        '/silvercart/images/logo.jpg'
    );
}

/*
 * DO NOT ENABLE THE CREATION OF TEST DATA IN DEV MODE HERE!
 * THIS SHOULD BE PROJECT SPECIFIC.
 */


