<?php
/**
 * Copyright 2017 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package SilverCart
 * @subpackage Config
 * @ignore 
 */

use Broarm\CookieConsent\CookieConsent;
use Broarm\CookieConsent\Model\CookieGroup;
use SilverCart\Admin\Controllers\LeftAndMainExtension;
use SilverCart\Admin\Forms\GridField\GridFieldBatchAction_ActivateDataObject;
use SilverCart\Admin\Forms\GridField\GridFieldBatchAction_ChangeAvailabilityStatus;
use SilverCart\Admin\Forms\GridField\GridFieldBatchAction_ChangeManufacturer;
use SilverCart\Admin\Forms\GridField\GridFieldBatchAction_ChangeOrderStatus;
use SilverCart\Admin\Forms\GridField\GridFieldBatchAction_ChangePaymentStatus;
use SilverCart\Admin\Forms\GridField\GridFieldBatchAction_ChangeProductGroup;
use SilverCart\Admin\Forms\GridField\GridFieldBatchAction_DeactivateDataObject;
use SilverCart\Admin\Forms\GridField\GridFieldBatchAction_Delete;
use SilverCart\Admin\Forms\GridField\GridFieldBatchAction_MarkAsNotSeen;
use SilverCart\Admin\Forms\GridField\GridFieldBatchAction_MarkAsSeen;
use SilverCart\Admin\Forms\GridField\GridFieldBatchAction_PrintOrders;
use SilverCart\Admin\Forms\GridField\GridFieldBatchController;
use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Extensions\Broarm\CookieConsent\Model\CookieGroupExtension;
use SilverCart\Extensions\Model\CookieConsent\BroarmContentControllerExtension;
use SilverCart\Extensions\Model\CookieConsent\BroarmExternalResourceExtension;
use SilverCart\Extensions\Model\CookieConsent\BroarmSiteTreeExtension;
use SilverCart\Model\CookieConsent\ExternalResource;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Product\Product;
use SilverCart\View\GroupView\GroupViewHandler;
use SilverCart\View\GroupView\GroupViewList;
use SilverCart\View\GroupView\GroupViewTile;
use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Admin\SecurityAdmin;
use SilverStripe\AssetAdmin\Controller\AssetAdmin;
use SilverStripe\CMS\Controllers\CMSPagesController;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Reports\ReportAdmin;
use SilverStripe\Security\Member;
use SilverStripe\SiteConfig\SiteConfigLeftAndMain;
use SilverStripe\View\Parsers\ShortcodeParser;
use TractorCow\Fluent\Extension\FluentExtension;
use WidgetSets\Admin\Controllers\WidgetSetAdmin;

// Check if the page.php descends from the SilverCart\Model\Pages\Page
if (class_exists('Page')) {
    $ext = new ReflectionClass('Page');
    if ($ext->getParentClass()->getName() != \SilverCart\Model\Pages\Page::class) {
        throw new Exception('Class "Page" has to extend "SilverCart\Model\Pages\Page".');
    }
}
if (class_exists('PageController')) {
    $ext = new ReflectionClass('PageController');

    if ($ext->getParentClass()->getName() != \SilverCart\Model\Pages\PageController::class) {
        throw new Exception('Class "PageController" has to extend "SilverCart\Model\Pages\PageController::class".');
    }
}
if (class_exists(CookieConsent::class)) {
    ExternalResource::add_extension(BroarmExternalResourceExtension::class);
    SiteTree::add_extension(BroarmSiteTreeExtension::class);
    ContentController::add_extension(BroarmContentControllerExtension::class);
    CookieGroup::add_extension(CookieGroupExtension::class);
    CookieGroup::add_extension(FluentExtension::class);
}
// Define required attributes to display a product in frontend
Product::addRequiredAttribute("Price");
Product::addRequiredAttribute("ProductGroupID");
// disable default pages for SiteTree
SiteTree::config()->set('create_default_pages', false);
// Enable validation for Member
Member::config()->set('validation_enabled', true);
// configure WidgetSet
if (class_exists('WidgetSets\\Extensions\\WidgetSetWidgetExtension')) {
    \WidgetSets\Extensions\WidgetSetWidgetExtension::prevent_widget_creation_by_class(SilverCart\Model\Widgets\Widget::class);
}
// Register GridField batch controllers
GridFieldBatchController::addBatchActionFor(Order::class, GridFieldBatchAction_ChangeOrderStatus::class);
GridFieldBatchController::addBatchActionFor(Order::class, GridFieldBatchAction_ChangePaymentStatus::class);
GridFieldBatchController::addBatchActionFor(Order::class, GridFieldBatchAction_PrintOrders::class);
GridFieldBatchController::addBatchActionFor(Order::class, GridFieldBatchAction_MarkAsSeen::class);
GridFieldBatchController::addBatchActionFor(Order::class, GridFieldBatchAction_MarkAsNotSeen::class);
GridFieldBatchController::addBatchActionFor(Product::class, GridFieldBatchAction_ActivateDataObject::class);
GridFieldBatchController::addBatchActionFor(Product::class, GridFieldBatchAction_DeactivateDataObject::class);
GridFieldBatchController::addBatchActionFor(Product::class, GridFieldBatchAction_ChangeAvailabilityStatus::class);
GridFieldBatchController::addBatchActionFor(Product::class, GridFieldBatchAction_ChangeManufacturer::class);
GridFieldBatchController::addBatchActionFor(Product::class, GridFieldBatchAction_ChangeProductGroup::class);
GridFieldBatchController::addBatchActionFor(Product::class, GridFieldBatchAction_Delete::class);
// add possible group views
GroupViewHandler::addGroupView(GroupViewList::class);
GroupViewHandler::addGroupView(GroupViewTile::class);
GroupViewHandler::addGroupHolderView(GroupViewList::class);
GroupViewHandler::addGroupHolderView(GroupViewTile::class);
// set default group view if not done in project yet
if (is_null(GroupViewHandler::getDefaultGroupView())) {
    GroupViewHandler::setDefaultGroupView(GroupViewTile::class);
}
if (is_null(GroupViewHandler::getDefaultGroupHolderView())) {
    GroupViewHandler::setDefaultGroupHolderView(GroupViewTile::class);
}
// Add product detail pages to Google Sitemap.
if (class_exists("Wilr\\GoogleSitemaps\\GoogleSitemap") &&
    method_exists("Wilr\\GoogleSitemaps\\GoogleSitemap", "register_dataobject")) {
    Wilr\GoogleSitemaps\GoogleSitemap::register_dataobject(Product::class, 'daily', '0.2');
}
// add silvercart branding if no other branding is set
if (LeftAndMain::config()->get('application_name') == 'SilverStripe') {
    LeftAndMain::config()->set('application_name', 'SilverCart');
    LeftAndMain::config()->set('application_link', 'https://www.silvercart.org');
}
// Register menus for the storeadmin
Config::registerMenu('default',   _t(LeftAndMainExtension::class . '.DEFAULT', 'CMS'));
Config::registerMenu('files',     _t(LeftAndMainExtension::class . '.FILES', 'Files'));
Config::registerMenu('orders',    _t(LeftAndMainExtension::class . '.ORDERS', 'Orders'));
Config::registerMenu('products',  _t(LeftAndMainExtension::class . '.PRODUCTS', 'Products'));
Config::registerMenu('handling',  _t(LeftAndMainExtension::class . '.HANDLING', 'Handling'));
Config::registerMenu('customer',  _t(LeftAndMainExtension::class . '.CUSTOMER', 'Customers'));
Config::registerMenu('config',    _t(LeftAndMainExtension::class . '.CONFIG', 'Configuration'));
Config::registerMenu('modules',   _t(LeftAndMainExtension::class . '.MODULES', 'Modules'));
AssetAdmin::config()->set('menuCode',      'files');
AssetAdmin::config()->set('menuSortIndex', 1);
SecurityAdmin::config()->set('menuCode',      'customer');
SecurityAdmin::config()->set('menuSortIndex', 30);
CMSPagesController::config()->set('menuSortIndex', 10);
WidgetSetAdmin::config()->set('menuSortIndex', 20);
ReportAdmin::config()->set('menuSortIndex', 30);
SiteConfigLeftAndMain::config()->set('menuCode', 'config');
SiteConfigLeftAndMain::config()->set('menuSortIndex', 1);
ShortcodeParser::get('default')->register(
    'searchresults_link',
    array(Page::class, 'link_shortcode_handler')
);
// prepare a posted email address
if (array_key_exists('Email', $_POST)) {
    $_POST['Email'] = Tools::prepareEmailAddress($_POST['Email']);
}
define('SILVERCART_PATH',        realpath(__DIR__));
define('SILVERCART_CLIENT_PATH', SILVERCART_PATH . DIRECTORY_SEPARATOR . 'client');
define('SILVERCART_IMG_PATH',    SILVERCART_CLIENT_PATH . DIRECTORY_SEPARATOR . 'img');
define('SILVERCART_LOG_PATH',    SILVERCART_PATH . DIRECTORY_SEPARATOR . 'log');
/*
 * DO NOT ENABLE THE CREATION OF TEST DATA IN DEV MODE HERE!
 * THIS SHOULD BE PROJECT SPECIFIC.
 */