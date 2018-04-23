<?php

namespace SilverCart\Security;

use SilverCart\Dev\Tools;
use SilverCart\Forms\QuickLoginForm;
use SilverCart\Forms\QuickSearchForm;
use SilverCart\Model\Customer\Customer;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Extension;
use SilverStripe\i18n\i18n;
use Translatable;

/**
 * Extension for the Security controller.
 *
 * @package SilverCart
 * @subpackage Security
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SecurityExtension extends Extension {
    
    /**
     * IdentifierCode of the page to redirect after a new password was set.
     *
     * @var string
     */
    public static $newPasswordBackURLIdentifierCode = 'SilvercartMyAccountHolder';

    /**
     * We register the common forms for Pages here.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.2018
     */
    public function onBeforeInit() {
        Tools::initSession();
        
        i18n::config()->merge('default_locale', Translatable::get_current_locale());
        i18n::set_locale(Translatable::get_current_locale());
        
        $controllerParams   = Controller::curr()->getURLParams();
        $anonymousCustomer  = Customer::currentAnonymousCustomer();
        
        if ($anonymousCustomer) {
            Tools::Session()->set('MemberLoginForm.force_message', true);
            if ($controllerParams['Action'] == 'changepassword') {
                $anonymousCustomer->logOut();
            }
        } else {
            Tools::Session()->set('MemberLoginForm.force_message', false);
            // used to redirect the logged in user to my-account page
            $backURL = Tools::PageByIdentifierCodeLink(self::$newPasswordBackURLIdentifierCode);
            $this->owner->extend('updateNewPasswordBackURL', $backURL);
            Tools::Session()->set('BackURL', $backURL);
            Tools::saveSession();
        }
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
    
    /**
     * Returns the QuickSearchForm.
     * 
     * @return QuickSearchForm
     */
    public function QuickSearchForm() {
        $form = new QuickSearchForm($this->owner);
        return $form;
    }
    
    /**
     * Returns the QuickLoginForm.
     * 
     * @return QuickLoginForm
     */
    public function QuickLoginForm() {
        $form = new QuickLoginForm($this->owner);
        return $form;
    }
    
}