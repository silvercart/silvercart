<?php

namespace SilverCart\Model\Pages;

use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * Adds some basic page controller features.
 * 
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.06.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 */
trait PageControllable
{
    /**
     * Loads all PHP side SilverCart JS requirements.
     * Additional JS files can still be loaded elsewhere.
     * 
     * @return void
     */
    public function RequireFullJavaScript() : void
    {
        PageController::singleton()->RequireFullJavaScript();
    }
    
    /**
     * Loads SilverStripe framework i18n.js and registers the SilverCart i18n JS
     * folder.
     * 
     * @param bool $force Force requirement
     * 
     * @return void
     */
    public function RequireI18nJavaScript(bool $force = false) : void
    {
        PageController::singleton()->RequireI18nJavaScript($force);
    }
    
    /**
     * Loads the SilverCart core (default) JS requirements.
     * 
     * @return void
     */
    public function RequireCoreJavaScript() : void
    {
        PageController::singleton()->RequireCoreJavaScript($force);
    }
    
    /**
     * Loads the SilverCart extended JS requirements.
     * Extended JS files are loaded by modules or custom project extensions
     * using the updateRequireExtendedJavaScript hook.
     * 
     * @param bool $force Force requirement
     * 
     * @return void
     */
    public function RequireExtendedJavaScript(bool $force = false) : void
    {
        PageController::singleton()->RequireExtendedJavaScript($force);
    }
    
    /**
     * Loads the SilverCart cookie policy (banner) JS requirements.
     * 
     * @param bool $force Force requirement
     * 
     * @return void
     */
    public function RequireCookieBannerJavaScript(bool $force = false) : void
    {
        PageController::singleton()->RequireCookieBannerJavaScript($force);
    }
    
    /**
     * Requires the color scheme CSS.
     * 
     * @return bool
     */
    public function RequireColorSchemeCSS() : void
    {
        PageController::singleton()->RequireColorSchemeCSS();
    }
    
    /**
     * Returns custom HTML code to place within the <head> tag, injected by
     * extensions.
     * 
     * @return DBHTMLText
     */
    public function HeadCustomHtmlContent() : DBHTMLText
    {
        return PageController::singleton()->HeadCustomHtmlContent();
    }
    
    /**
     * Returns custom HTML code to place right after the <body> tag, injected by
     * extensions.
     * 
     * @return DBHTMLText
     */
    public function HeaderCustomHtmlContent() : DBHTMLText
    {
        return PageController::singleton()->HeaderCustomHtmlContent();
    }
    
    /**
     * Returns custom HTML code to place right before the footer (first line in
     * Footer.ss) injected by extensions.
     * 
     * @return DBHTMLText
     */
    public function BeforeFooterContent() : DBHTMLText
    {
        return PageController::singleton()->BeforeFooterContent();
    }
    
    /**
     * Returns custom HTML code to place right before the closing </body> tag, 
     * injected by extensions.
     * 
     * @return DBHTMLText
     */
    public function FooterCustomHtmlContent() : DBHTMLText
    {
        return PageController::singleton()->FooterCustomHtmlContent();
    }
}