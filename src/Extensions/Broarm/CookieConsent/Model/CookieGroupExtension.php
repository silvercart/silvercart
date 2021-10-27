<?php

namespace SilverCart\Extensions\Broarm\CookieConsent\Model;

use Broarm\CookieConsent\Model\CookieGroup;
use SilverStripe\ORM\DataExtension;
use TractorCow\Fluent\Model\Locale;
use TractorCow\Fluent\State\FluentState;

/**
 * Extension for CookieGroup.
 * 
 * @package SilverCart
 * @subpackage Extensions\CookieConsent\Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 01.12.2020
 * @copyright 2020 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property \Broarm\CookieConsent\Model\CookieGroup $owner Owner
 */
class CookieGroupExtension extends DataExtension
{
    /**
     * DB attributes.
     *
     * @var array
     */
    private static $db = [
        'Sort' => 'Int',
    ];
    /**
     * Default sort.
     *
     * @var array
     */
    private static $default_sort = [
        'Sort ASC',
    ];
    
    /**
     * On before write.
     * 
     * @return void
     */
    public function onBeforeWrite() : void
    {
        parent::onBeforeWrite();
        if ((int) $this->owner->Sort === 0) {
            $this->owner->Sort = CookieGroup::get()->max('Sort') + 1;
        }
    }
    
    /**
     * Requires the default records.
     * 
     * @return void
     */
    public function requireDefaultRecords() : void
    {
        $defaultLocale = Locale::getDefault();
        if (!($defaultLocale instanceof Locale)) {
            return;
        }
        $currentLocale = FluentState::singleton()->getLocale();
        FluentState::singleton()->setLocale($defaultLocale->Locale);
        $localizedCookieGroups      = CookieGroup::get()->filter('Locale', $defaultLocale->Locale);
        $localizedCookieGroupsCount = $localizedCookieGroups->count();
        FluentState::singleton()->setLocale(null);
        $allCookieGroups = CookieGroup::get();
        if ($localizedCookieGroupsCount < $allCookieGroups->count()) {
            foreach ($allCookieGroups as $cookieGroup) {
                FluentState::singleton()->setLocale($defaultLocale->Locale);
                $cookieGroup->Locale = $defaultLocale->Locale;
                $cookieGroup->write();
            }
        }
        FluentState::singleton()->setLocale($currentLocale);
    }
}