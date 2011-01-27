<?php
//Pflichtattribute eines Artikels definieren
Article::setRequiredAttributes("Price");

// ----------------------------------------------------------------------------
// Rewrite Rules Definitions
// ----------------------------------------------------------------------------
//Die URL Interpretation muss angepasst werden
//artikelansicht ist das URL Segment der Seiteninstanz
//$ID ist die ID des Produkts
//$Name ist der nicht zu verwertende Artikelname, der nur aus SEO Gründen vorhanden ist.
Director::addRules(100, array(
    'artikelansicht/$ID/$Name'                      => 'ArticlePage_Controller',
    'meinkonto/adressuebersicht/$URLSegment!/$ID'   => 'AddressPage_Controller',
    'meinkonto/bestelluebersicht/$URLSegment!/$ID'  => 'OrderDetailPage_Controller'
));

// disable default pages for SiteTree
SiteTree::set_create_default_pages(false);

// ----------------------------------------------------------------------------
// Logging
// ----------------------------------------------------------------------------

// ----------------------------------------------------------------------------
// Extensions registrieren
// ----------------------------------------------------------------------------
Object::add_extension('SiteTree', 'Translatable');
Object::add_extension('SiteConfig', 'Translatable');
Object::add_extension('Member', 'CustomerRole');

$path    = dirname(__FILE__).'/';
$relPath = substr(Director::makeRelative($path), 1);

define('PIXELTRICKS_CHECKOUT_BASE_PATH', $path);
define('PIXELTRICKS_CHECKOUT_BASE_PATH_REL', $relPath);