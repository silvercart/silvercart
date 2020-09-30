<?php

namespace SilverCart\Extensions\TractorCow\Fluent\Model;

use SilverStripe\i18n\i18n;
use SilverStripe\ORM\DataExtension;
use TractorCow\Fluent\Model\Locale;

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
}