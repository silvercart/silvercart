<?php

namespace SilverCart\Model\Pages;

use PageController;
use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\LoginForm;
use SilverCart\Forms\RegisterRegularCustomerForm;
use SilverCart\Model\Pages\Page as SilverCartPage;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use TractorCow\Fluent\Model\Locale;

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
    const SESSION_KEY_HTTP_REFERER = 'SilverCart.RegistrationPage.HttpReferer';
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
        $referer = Tools::Session()->get(self::SESSION_KEY_HTTP_REFERER);
        if ($referer === null
         && $this->getReferer() !== null
        ) {
            Tools::Session()->set(self::SESSION_KEY_HTTP_REFERER, $this->getReferer());
            Tools::saveSession();
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
            $link = Tools::Session()->get(self::SESSION_KEY_HTTP_REFERER);
            if (empty($link)) {
                $link = $this->PageByIdentifierCodeLink(SilverCartPage::IDENTIFIER_MY_ACCOUNT_HOLDER);
            }
            $this->redirect($link);
        }
        return $this->render();
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
        $link        = Tools::Session()->get(self::SESSION_KEY_HTTP_REFERER);
        $refererPage = $this->RefererPage();
        if (empty($link)
         || $refererPage instanceof MyAccountHolder
        ) {
            $link = $this->getDefaultHomepage()->Link();
        }
        return (string) $link;
    }
    
    /**
     * Returns the referer page.
     * 
     * @return SiteTree|null
     */
    public function RefererPage(string $link = null) : ?SiteTree
    {
        if ($link === null) {
            $link = Tools::Session()->get(self::SESSION_KEY_HTTP_REFERER);
        }
        $originalLink = $link;
        $page         = $this->data();
        $localeCode   = $page->getSourceQueryParam('Fluent.Locale');
        if (is_string($localeCode)
         && !empty($localeCode)
        ) {
            $localeObj = Locale::getByLocale($localeCode);
        }
        if (!($localeObj instanceof Locale)) {
            $localeObj = Locale::getCurrentLocale();
        }
        if ($localeObj instanceof Locale) {
            $URLSegment = $localeObj->getURLSegment();
            if (!empty($URLSegment)) {
                $relativeLink = Director::makeRelative($link);
                if (strpos($relativeLink, "{$URLSegment}/") === 0) {
                    $originalLink = substr($relativeLink, strlen("{$URLSegment}/"));
                }
            }

        }
        do {
            $refererPage  = SiteTree::get_by_link($originalLink);
            $parts        = explode('/', $originalLink);
            array_pop($parts);
            $originalLink = implode('/', $parts);
        } while ($refererPage === null
              && !empty($originalLink)
        );
        return $refererPage;
    }
}