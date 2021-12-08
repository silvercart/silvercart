<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Widgets\WidgetController;
use SilverStripe\Control\Director;
use SilverStripe\i18n\i18n;

/**
 * HTMLSliderWidget Controller.
 *
 * @package SilverCart
 * @subpackage Model\Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.11.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class HTMLSliderWidgetController extends WidgetController
{
    /**
     * Creates the cache key for this widget.
     *
     * @return string
     */
    public function WidgetCacheKey() : string
    {
        $widget     = $this->data();
        $slideMap   = $widget->Slides()->map('ID', 'LastEdited')->toArray();
        $slideIDs   = implode('_', array_keys($slideMap));
        sort($slideMap);
        $lastEdited = array_pop($slideMap);
        $keyParts   = [
            i18n::get_locale(),
            $slideIDs,
            $lastEdited,
            $widget->LastEdited,
        ];
        if (Director::isDev()) {
            $keyParts[] = uniqid();
        }
        $this->extend('updateWidgetCacheKeyParts', $keyParts);
        return implode('_', $keyParts);
    }
}