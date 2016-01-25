<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Backend
 */

/**
 * Decorates the default ModelAdmin to inject some custom javascript.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 24.02.2011
 * @license see license file in modules root directory
 */
class SilvercartModelAdminExtension extends DataExtension {
    
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
        Requirements::css('silvercart/admin/css/SilvercartMain.css');
    }
    
}
