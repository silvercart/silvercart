<?php

namespace SilverCart\Admin\Controllers;

use SilverStripe\Control\Director;
use SilverStripe\Core\Extension;
use SilverStripe\i18n\i18n;
use SilverStripe\View\Requirements;
use Translatable;

/**
 * Decorates the default ModelAdmin to inject some custom javascript.
 *
 * @package SilverCart
 * @subpackage Admin_Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class ModelAdminExtension extends Extension {
    
    /**
     * Injects some custom javascript to provide instant loading of DataObject
     * tables.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.01.2011
     */
    public function onAfterInit() {
        Translatable::set_current_locale(i18n::get_locale());
        if (Director::is_ajax()) {
            return true;
        }
        Requirements::css('silvercart/silvercart:client/admin/css/LeftAndMainExtension.css');
    }
    
}
