<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Dev\Tools;
use SilverStripe\Control\Director;
use SilverStripe\Core\Extension;
use SilverStripe\i18n\i18n;
use SilverStripe\View\Requirements;

/**
 * Decorates the default ModelAdmin to inject some custom javascript.
 *
 * @package SilverCart
 * @subpackage Admin\Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 * 
 * @property \SilverStripe\Admin\ModelAdmin $owner Owner
 */
class ModelAdminExtension extends Extension
{
    /**
     * Injects some custom javascript to provide instant loading of DataObject
     * tables.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.01.2011
     */
    public function onAfterInit() : void
    {
        Tools::set_current_locale(i18n::get_locale());
        if (Director::is_ajax()) {
            return;
        }
        Requirements::css('silvercart/silvercart:client/admin/css/LeftAndMainExtension.css');
        Requirements::add_i18n_javascript('silvercart/silvercart:client/javascript/lang');
    }
}