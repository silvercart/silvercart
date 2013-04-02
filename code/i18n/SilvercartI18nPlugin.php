<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage i18n
 */

/**
 * plugin class to override some default translations from sapphire
 * 
 * @package Silvercart
 * @subpackage i18n
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 26.06.2012
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartI18nPlugin {
    
    /**
     * plugin function to override sapphire translations
     * 
     * @global array $lang 
     * 
     * @return void
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 26.06.2012
     */
    public static function de_DE() {
        global $lang;
        $lang['de_DE']['Member']['YOUROLDPASSWORD'] = 'Ihr altes Passwort';
    }    
}