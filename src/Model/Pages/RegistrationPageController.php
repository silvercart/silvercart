<?php

namespace SilverCart\Model\Pages;

use PageController;
use SilverCart\Admin\Model\Config;
use SilverCart\Forms\LoginForm;
use SilverCart\Forms\RegisterRegularCustomerForm;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

/**
 * RegistrationPage Controller class.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class RegistrationPageController extends PageController
{
    /**
     * List of allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = [
        'RegisterRegularCustomerForm',
        'welcome',
        'optin',
        'optinfailed',
        'optinpending',
        'optinresend',
    ];

    /**
     * initialisation of the form object
     * logged in members get logged out
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.09.2017
     */
    protected function init() : void
    {
        if (Config::EnableSSL()) {
            Director::forceSSL();
        }
        parent::init();
    }
    
    /**
     * Returns the LoginForm.
     * 
     * @return LoginForm
     */
    public function LoginForm() : LoginForm
    {
        return LoginForm::create($this);
    }
    
    /**
     * Returns the RegisterRegularCustomerForm.
     * 
     * @return RegisterRegularCustomerForm
     */
    public function RegisterRegularCustomerForm() : RegisterRegularCustomerForm
    {
        return RegisterRegularCustomerForm::create($this);
    }
    
    /**
     * Action to do the opt-in confirmation.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return DBHTMLText|null
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2019
     */
    public function optin(HTTPRequest $request) : ?DBHTMLText
    {
        $hash = $request->param('ID');
        if (empty($hash)) {
            $this->redirect($this->data()->Link('optinfailed'));
        } else {
            $customer = Security::getCurrentUser();
            if ($customer instanceof Member
             && $customer->exists()
            ) {
                if ($customer->RegistrationOptInConfirmed) {
                    $this->redirect($this->PageByIdentifierCodeLink('SilvercartMyAccountHolder'));
                } elseif (!$customer->confirmRegistrationOptIn($hash)) {
                    $this->redirect($this->data()->Link('optinfailed'));
                }
            }
        }
        return $this->render();
    }
    
    /**
     * Action to resend the opt-in confirmation link.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return DBHTMLText|null
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2019
     */
    public function optinresend(HTTPRequest $request) : ?DBHTMLText
    {
        $customer = Security::getCurrentUser();
        if ($customer instanceof Member) {
            if ($customer->RegistrationOptInConfirmed) {
                $this->redirect($this->PageByIdentifierCodeLink('SilvercartMyAccountHolder'));
            } else {
                $customer->sendRegistrationOptInEmail();
            }
        }
        return $this->render();
    }
    
    /**
     * Action for a failed opt-in.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return DBHTMLText|null
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2019
     */
    public function optinfailed(HTTPRequest $request) : ?DBHTMLText
    {
        return $this->defaultOptInHandling();
    }
    
    /**
     * Action to show the opt-in pending information.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return DBHTMLText|null
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2019
     */
    public function optinpending(HTTPRequest $request) : ?DBHTMLText
    {
        return $this->defaultOptInHandling();
    }
    
    /**
     * Action to resend the opt-in confirmation link.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return DBHTMLText|null
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2019
     */
    public function welcome(HTTPRequest $request) : ?DBHTMLText
    {
        return $this->defaultOptInHandling();
    }
    
    /**
     * Default opt-in action handling.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return DBHTMLText|null
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2019
     */
    public function defaultOptInHandling() : ?DBHTMLText
    {
        $customer = Security::getCurrentUser();
        if ($customer instanceof Member
         && $customer->RegistrationOptInConfirmed
        ) {
            $this->redirect($this->PageByIdentifierCodeLink('SilvercartMyAccountHolder'));
        }
        return $this->render();
    }
}