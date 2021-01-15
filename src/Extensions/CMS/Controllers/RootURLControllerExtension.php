<?php

namespace SilverCart\Extensions\CMS\Controllers;

use SilverStripe\CMS\Controllers\RootURLController;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Extension;
use TractorCow\Fluent\Model\Locale;

/**
 * Extension for SilverStripe RootURLController.
 * 
 * @package SilverCart
 * @subpackage SubPackage
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 15.01.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property \SilverStripe\CMS\Controllers\RootURLController $owner Owner
 */
class RootURLControllerExtension extends Extension
{
    /**
     * Returns the homepage link [@see RootURLController::get_homepage_link()]
     * without the current locale prefix.
     * 
     * @return string
     */
    public static function get_homepage_link_without_locale() : string
    {
        return self::get_link_without_locale(RootURLController::get_homepage_link());
    }
    
    /**
     * Returns the given $link without the current locale prefix.
     * 
     * @param string $link Link to manipulate
     * 
     * @return string
     */
    public static function get_link_without_locale(string $link) : string
    {
        $originalLink = $link;
        $localeObj    = null;
        $ctrl         = Controller::curr();
        if ($ctrl->hasMethod('data')) {
            $page       = Controller::curr()->data();
            $localeCode = $page->getSourceQueryParam('Fluent.Locale');
            if (is_string($localeCode)
             && !empty($localeCode)
            ) {
                $localeObj = Locale::getByLocale($localeCode);
            }
        }
        if (!($localeObj instanceof Locale)) {
            $localeObj = Locale::getCurrentLocale();
        }
        if ($localeObj instanceof Locale) {
            $URLSegment = $localeObj->getURLSegment();
            if (!empty($URLSegment)) {
                $relativeLink = Director::makeRelative($link);
                if (strpos($relativeLink, "{$URLSegment}/") === 0) {
                    $originalLink = substr($relativeLink, strlen("{$URLSegment}/"));
                }
            }

        }
        return (string) $originalLink;
    }
}