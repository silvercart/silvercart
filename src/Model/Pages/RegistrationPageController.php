<?php

namespace SilverCart\Model\Pages;

use PageController;
use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\LoginForm;
use SilverCart\Forms\RegisterRegularCustomerForm;
use SilverCart\Model\Pages\Page as SilverCartPage;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
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
     * Returns the checkout page.
     * 
     * @return CheckoutStep
     */
    public function getCheckoutPage() : CheckoutStep
    {
        return $this->PageByIdentifierCode(SilverCartPage::IDENTIFIER_CHECKOUT_PAGE);
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
                    if (RegistrationPage::getIsInCheckout()) {
                        $link = $this->getCheckoutPage()->Link();
                    } else {
                        $link = Tools::Session()->get(self::SESSION_KEY_HTTP_REFERER);
                        if (empty($link)) {
                            $link = $this->PageByIdentifierCode(SilverCartPage::IDENTIFIER_MY_ACCOUNT_HOLDER)->Link();
                        }
                    }
                    $this->redirect($link);
                } elseif (!$customer->confirmRegistrationOptIn($hash)) {
                    $this->redirect($this->data()->Link('optinfailed'));
                }
            }
        }
        if (RegistrationPage::getIsInCheckout()
         && !$this->redirectedTo()
        ) {
            $this->redirect($this->getCheckoutPage()->Link('welcome'));
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
                $this->redirect($this->PageByIdentifierCodeLink(SilverCartPage::IDENTIFIER_MY_ACCOUNT_HOLDER));
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
     * @return HTTPResponse
     */
    public function optinfailed(HTTPRequest $request) : HTTPResponse
    {
        return $this->defaultOptInHandling($request);
    }
    
    /**
     * Action to show the opt-in pending information.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return HTTPResponse
     */
    public function optinpending(HTTPRequest $request) : HTTPResponse
    {
        return $this->defaultOptInHandling($request);
    }
    
    /**
     * Action to resend the opt-in confirmation link.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return HTTPResponse
     */
    public function welcome(HTTPRequest $request) : HTTPResponse
    {
        return $this->defaultOptInHandling($request);
    }
    
    /**
     * Default opt-in action handling.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return HTTPResponse
     */
    public function defaultOptInHandling(HTTPRequest $request) : HTTPResponse
    {
        $customer = Security::getCurrentUser();
        if ($customer instanceof Member
         && $customer->RegistrationOptInConfirmed
        ) {
            $link = Tools::Session()->get(self::SESSION_KEY_HTTP_REFERER);
            if (empty($link)
             || Director::absoluteURL($link) === Director::absoluteURL($request->getURL())
            ) {
                $link = $this->PageByIdentifierCodeLink(SilverCartPage::IDENTIFIER_MY_ACCOUNT_HOLDER);
            }
            return $this->redirect($link);
        }
        return HTTPResponse::create()->setBody($this->render());
    }
    
    /**
     * Returns the referer link.
     * The referer link is the link to the page the customer visited right before
     * accessing the registration page.
     * 
     * @return string
     */
    public function RefererLink() : string
    {
        $link        = parent::RefererLink();
        $refererPage = $this->RefererPage();
        if (empty($link)
         || $refererPage instanceof MyAccountHolder
        ) {
            $link = $this->getDefaultHomepage()->Link();
        }
        return (string) $link;
    }
}