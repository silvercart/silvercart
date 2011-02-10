<?php
// ----------------------------------------------------------------------------
// Define required attributes
// ----------------------------------------------------------------------------
Article::setRequiredAttributes("Price");

// ----------------------------------------------------------------------------
// Rewrite Rules Definitions
// ----------------------------------------------------------------------------
Director::addRules(100, array(
    'artikelansicht/$ID/$Name'                      => 'ArticlePage_Controller',
    'meinkonto/adressuebersicht/$URLSegment!/$ID'   => 'AddressPage_Controller',
    'meinkonto/bestelluebersicht/$URLSegment!/$ID'  => 'OrderDetailPage_Controller'
));

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
Object::add_extension('Member', 'CustomerRole');

// ----------------------------------------------------------------------------
// Define path constants
// ----------------------------------------------------------------------------
$path    = dirname(__FILE__).'/';
$relPath = substr(Director::makeRelative($path), 1);

define('PIXELTRICKS_CHECKOUT_BASE_PATH', $path);
define('PIXELTRICKS_CHECKOUT_BASE_PATH_REL', $relPath);

// ----------------------------------------------------------------------------
// Register at required modules
// ----------------------------------------------------------------------------
CustomHtmlForm::registerModule('silvercart',49);

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
