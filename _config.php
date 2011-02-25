<?php

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
// Set default language
// ----------------------------------------------------------------------------
Translatable::set_default_locale("de_DE");
i18n::enable();
i18n::set_default_locale('de_DE');

// ----------------------------------------------------------------------------
// Register extensions
// ----------------------------------------------------------------------------
Object::add_extension('SiteTree', 'Translatable');
Object::add_extension('SiteConfig', 'Translatable');
Object::add_extension('Member', 'SilvercartCustomerRole');
Object::add_extension('ModelAdmin', 'SilvercartModelAdminDecorator');
DataObject::add_extension('SilvercartProductGroupHolder_Controller', 'SilvercartGroupViewDecorator');
DataObject::add_extension('SilvercartProductGroupPage_Controller', 'SilvercartGroupViewDecorator');
DataObject::add_extension('SilvercartSearchResultsPage_Controller', 'SilvercartGroupViewDecorator');

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
        throw new Exception('Klasse "Page" muss von "SilvercartPage" erben.');
    }
}
if (class_exists('Page_Controller')) {
    $ext = new ReflectionClass('Page_Controller');

    if ($ext->getParentClass()->getName() != 'SilvercartPage_Controller') {
        throw new Exception('Klasse "Page_Controller" muss von "SilvercartPage_Controller" erben.');
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
SilvercartGroupViewHandler::setDefaultGroupView('SilvercartGroupViewList');
SilvercartGroupViewHandler::setDefaultGroupHolderView('SilvercartGroupViewList');

/*
 * DO NOT ENABLE THE CREATION OF TEST DATA IN DEV MODE HERE!
 * THIS SHOULD BE PROJECT SPECIFIC.
 */

