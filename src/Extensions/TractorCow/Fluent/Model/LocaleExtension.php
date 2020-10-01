<?php

namespace SilverCart\Extensions\TractorCow\Fluent\Model;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Versioned\Versioned;
use TractorCow\Fluent\Model\Locale;
use TractorCow\Fluent\State\FluentState;

/**
 * 
 * @package SilverCart
 * @subpackage SubPackage
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 30.09.2020
 * @copyright 2020 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property Locale $owner Owner
 */
class LocaleExtension extends DataExtension
{
    /**
     * Adds a default locale if not done yet.
     * 
     * @return void
     */
    public function requireDefaultRecords() : void
    {
        $locales = Locale::get();
        if (!$locales->exists()) {
            $dLocale = i18n::config()->default_locale;
            $locale  = Locale::create();
            $locale->Locale          = $dLocale;
            $locale->Title           = i18n::getData()->localeName($dLocale);
            $locale->URLSegment      = $locale->getLocalePrefix();
            $locale->IsGlobalDefault = true;
            $locale->write();
            Locale::clearCached();
            $locale->publishSiteTree();
        }
    }

    /**
     * Get a short language segment of the locale code.
     *
     * @return string e.g. "en" for "en_NZ"
     */
    public function getLocalePrefix() : string
    {
        $bits = explode('_', $this->owner->Locale);
        return array_shift($bits);
    }
    
    /**
     * Publishs the already published pages for the locale of this object starting 
     * at the root pages (without a parent).
     * 
     * @return void
     */
    public function publishSiteTree() : void
    {
        $currentLocale = FluentState::singleton()->getLocale();
        FluentState::singleton()->setLocale($this->owner->Locale);
        $rootPages = SiteTree::get()->filter('ParentID', 0);
        foreach ($rootPages as $page) {
            $this->publishPageWithChildren($page);
        }
        FluentState::singleton()->setLocale($currentLocale);
    }
    
    /**
     * Publishs the already published pages for the locale of this object 
     * recursively.
     * 
     * @param SiteTree $page Page to publish
     * 
     * @return void
     */
    public function publishPageWithChildren(SiteTree $page) : void
    {
        if ($page->isPublished()
         && !$page->isPublishedInLocale($this->owner->Locale)
        ) {
            $page->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        }
        foreach ($page->AllChildren() as $child) {
            $this->publishPageWithChildren($child);
        }
    }
}