<?php

namespace SilverCart\Security;

use SilverCart\Dev\Tools;
use SilverCart\Forms\QuickLoginForm;
use SilverCart\Forms\QuickSearchForm;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Pages\Page;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Extension;
use SilverStripe\i18n\i18n;

/**
 * Extension for the Security controller.
 *
 * @package SilverCart
 * @subpackage Security
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property \SilverStripe\Security\Security $owner Owner
 */
class SecurityExtension extends Extension
{
    /**
     * IdentifierCode of the page to redirect after a new password was set.
     *
     * @var string
     */
    public static $new_password_back_url_identifierCode = Page::IDENTIFIER_MY_ACCOUNT_HOLDER;

    /**
     * We register the common forms for Pages here.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.2018
     */
    public function onBeforeInit()
    {
        Tools::initSession();
        
        i18n::config()->merge('default_locale', Tools::current_locale());
        i18n::set_locale(Tools::current_locale());
        
        $controllerParams  = Controller::curr()->getURLParams();
        $anonymousCustomer = Customer::currentAnonymousCustomer();
        
        if ($anonymousCustomer) {
            Tools::Session()->set('MemberLoginForm.force_message', true);
            if ($controllerParams['Action'] == 'changepassword') {
                $anonymousCustomer->logOut();
            }
        } else {
            Tools::Session()->set('MemberLoginForm.force_message', false);
            // used to redirect the logged in user to my-account page
            $backURL = Tools::PageByIdentifierCodeLink(self::$new_password_back_url_identifierCode);
            $this->owner->extend('updateNewPasswordBackURL', $backURL);
            Tools::Session()->set('BackURL', $backURL);
            Tools::saveSession();
        }
        
        $member  = $this->owner->getCurrentUser();
        $request = $this->owner->getRequest();
        $action  = $request->param('Action');
        if ($action === 'login') {
            $backURL = $request->getVar('BackURL');
            if (!empty($backURL)) {
                if (strpos($backURL, 'admin') !== 0
                 && strpos($backURL, '/admin') !== 0
                ) {
                    $this->owner->redirect($backURL);
                }
            }
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
    public function securityTokenEnabled()
    {
        return true;
    }
    
    /**
     * Returns the QuickSearchForm.
     * 
     * @param string $htmlID Optional HTML ID to avoid duplicate IDs when using 
     *                       a form multiple times.
     * 
     * @return QuickSearchForm
     */
    public function QuickSearchForm($htmlID = null)
    {
        $form = QuickSearchForm::create($this);
        if (!is_null($htmlID)) {
            $form->setHTMLID($htmlID);
        }
        return $form;
    }
    
    /**
     * Returns the QuickLoginForm.
     * 
     * @param string $htmlID Optional HTML ID to avoid duplicate IDs when using 
     *                       a form multiple times.
     * 
     * @return QuickLoginForm
     */
    public function QuickLoginForm($htmlID = null)
    {
        $form = QuickLoginForm::create($this);
        if (!is_null($htmlID)) {
            $form->setHTMLID($htmlID);
        }
        return $form;
    }
}