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
CustomHtmlForm::$custom_error_box_css_class = 'help-inline';
CustomHtmlForm::$custom_error_box_selection_method = 'append';
CustomHtmlForm::$custom_error_box_sub_selector = ' .controls';

// Require i18n javascript
Requirements::add_i18n_javascript('silvercart/javascript/lang');

// ----------------------------------------------------------------------------
// Register SilvercartPluginProvider
// ----------------------------------------------------------------------------
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
// Enable DataObject validation
// ----------------------------------------------------------------------------
Config::inst()->update('Member', 'validation_enabled', true);

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
