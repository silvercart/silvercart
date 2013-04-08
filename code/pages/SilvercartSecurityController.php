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
     * We register the common forms for SilvercartPages here.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Patrick Schneider <pschneider@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2013
     */
    public function onBeforeInit() {
        SilvercartTools::initSession();
        
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
            Session::set('BackURL', SilvercartTools::PageByIdentifierCodeLink('SilvercartMyAccountHolder'));
        }
        
        $this->owner->registerCustomHtmlForm('SilvercartQuickSearchForm', new SilvercartQuickSearchForm($this->owner));
        $this->owner->registerCustomHtmlForm('SilvercartQuickLoginForm',  new SilvercartQuickLoginForm($this->owner));
        
        SilvercartPlugin::call($this->owner, 'init', array($this->owner));
    }
}