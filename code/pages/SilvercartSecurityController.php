<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * Injects CustomHtmlForm objects into the security controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sascha Koehler <skoehler@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 08.04.2013
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartSecurityController extends DataExtension {
    
    /**
     * IdentifierCode of the page to redirect after a new password was set.
     *
     * @var string
     */
    public static $newPasswordBackURLIdentifierCode = 'SilvercartMyAccountHolder';

    /**
     * We register the common forms for SilvercartPages here.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>,
     *         Patrick Schneider <pschneider@pixeltricks.de>
     * @since 02.05.2018
     */
    public function onBeforeInit() {
        $request = $this->owner->getRequest();
        if (SilvercartTools::isBackendEnvironment() ||
            $request->param('Action') == 'ping') {
            return;
        }
        Page_Controller::singleton()->loadJSRequirements();
        SilvercartTools::initSession();
        
        i18n::set_default_locale(Translatable::get_current_locale());
        i18n::set_locale(Translatable::get_current_locale());
        
        $controllerParams   = Controller::curr()->getURLParams();
        $anonymousCustomer  = SilvercartCustomer::currentAnonymousCustomer();
        
        if ($anonymousCustomer) {
            Session::set('MemberLoginForm.force_message', true);
            if ($controllerParams['Action'] == 'changepassword') {
                $anonymousCustomer->logOut();
            }
        } else {
            Session::set('MemberLoginForm.force_message', false);
            // used to redirect the logged in user to my-account page
            $backURL = SilvercartTools::PageByIdentifierCodeLink(self::$newPasswordBackURLIdentifierCode);
            $this->owner->extend('updateNewPasswordBackURL', $backURL);
            Session::set('BackURL', $backURL);
            Session::save();
        }
        
        $this->owner->registerCustomHtmlForm('SilvercartQuickSearchForm', new SilvercartQuickSearchForm($this->owner));
        $this->owner->registerCustomHtmlForm('SilvercartQuickLoginForm',  new SilvercartQuickLoginForm($this->owner));
        
        SilvercartPlugin::call($this->owner, 'init', array($this->owner));
    }
    
    /**
     * Returns whether to enable or disable SecurityToken.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.11.2015
     */
    public function securityTokenEnabled() {
        return true;
    }
    
}