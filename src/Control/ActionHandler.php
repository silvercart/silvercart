<?php

namespace SilverCart\Control;

use Broarm\CookieConsent\Model\CookiePolicyPage;
use ReflectionClass;
use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\LoginForm;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\ShoppingCart;
use SilverCart\Model\Order\ShoppingCartPosition;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Pages\ProductGroupPage;
use SilverCart\Model\Pages\SearchResultsPage;
use SilverCart\Model\Product\Product;
use SilverCart\Model\SearchQuery;
use SilverStripe\CMS\Controllers\ModelAsController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Convert;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\Member;
use SilverStripe\Security\MemberAuthenticator\MemberAuthenticator;

/**
 * Central handler for form actions.
 *
 * @package SilverCart
 * @subpackage Control
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 25.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ActionHandler extends Controller
{
    /**
     * Allowed actions
     *
     * @var array
     */
    private static $allowed_actions = [
        'addToCart',
        'doSearch',
        'doLogin',
        'decrementPositionQuantity',
        'incrementPositionQuantity',
        'loadSubNavigation',
        'cookieManager',
    ];
    /**
     * URL segment
     *
     * @var string
     */
    private static $url_segment = 'sc-action';

    /**
     * returns a single page by IdentifierCode
     * used to retrieve links dynamically
     *
     * @param string $identifierCode the classes name
     * 
     * @return Page|null
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.03.2019
     */
    public static function PageByIdentifierCode($identifierCode = Page::IDENTIFIER_FRONT_PAGE) : ?Page
    {
        return Tools::PageByIdentifierCode($identifierCode);
    }

    /**
     * returns a page link by IdentifierCode
     *
     * @param string $identifierCode the DataObjects IdentifierCode
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.03.2019
     */
    public static function PageByIdentifierCodeLink($identifierCode = Page::IDENTIFIER_FRONT_PAGE) : string
    {
        return Tools::PageByIdentifierCodeLink($identifierCode);
    }
    
    /**
     * Action to add a product to cart.
     * 
     * @param HTTPRequest $request Request to check for product data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.03.2013
     */
    public function addToCart(HTTPRequest $request) : void
    {
        $isValidRequest = false;
        $backLink       = null;
        $postVars       = $request->postVars();
        $isAjax         = $request->postVar('isAjax');
        $params         = $request->allParams();
        $productID      = $params['ID'];
        $quantity       = $params['OtherID'];
        $position       = null;
        
        if (is_null($productID)
         || is_null($quantity)
        ) {
            if (array_key_exists('productID',       $postVars)
             && array_key_exists('productQuantity', $postVars)
            ) {
                $isValidRequest = true;
                $productID      = $postVars['productID'];
                $quantity       = $postVars['productQuantity'];
            }
        } else {
            $isValidRequest = true;
        }

        if ($isValidRequest) {
            $postVars['productID']       = $productID;
            $postVars['productQuantity'] = $quantity;

            if ($quantity == 0) {
                ShoppingCart::removeProduct($postVars, $position);
            } else {
                $position = ShoppingCart::addProduct($postVars);
            }
            
            if (Config::getRedirectToCartAfterAddToCartAction()) {
                $backLink = Tools::PageByIdentifierCodeLink(Page::IDENTIFIER_CART_PAGE);
            }
        }
        
        if ($isAjax) {
            $product              = Product::get()->byID($productID);
            $totalCartQuantity    = 0;
            $quantityInCartString = '';
            $htmlDropdown         = '';
            $htmlModal            = '';
            if ($product instanceof Product) {
                if ($position === null) {
                    $position          = $product;
                    $position->Product = $product;
                }
                $member               = Customer::currentUser();
                $totalCartQuantity    = $member->getCart()->getQuantity();
                $quantityInCartString = $product->getQuantityInCartString();
                $pageReflection       = new ReflectionClass(Page::class);
                $cartReflection       = new ReflectionClass(ShoppingCart::class);
                $htmlDropdown         = (string) $this->renderWith("{$pageReflection->getNamespaceName()}\\Includes\\ShoppingCartDropdown");
                $htmlModal            = (string) $this->renderWith(ShoppingCart::class . "_AjaxResponse", [
                    'Product'  => $product,
                    'Quantity' => $quantity,
                    'Position' => $position,
                ]);
            }
            $json = [
                'TotalCartQuantity'    => $totalCartQuantity,
                'QuantityInCartString' => $quantityInCartString,
                'HTMLDropdown'         => $htmlDropdown,
                'HTMLModal'            => $htmlModal,
                'Redirect'             => $this->redirectedTo() ? $this->getResponse()->getHeader('Location') : '',
            ];
            print json_encode($json);
            exit();
        } else {
            $this->redirectBack($backLink, '#product' . $productID);
        }
    }
    
    /**
     * Decrements the shopping cart position quantity by 1.
     * 
     * @param HTTPRequest $request Request to check for product data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.01.2019
     */
    public function decrementPositionQuantity(HTTPRequest $request) : void
    {
        $this->extend('onBeforeDecrementPositionQuantity', $request);
        $this->changePositionQuantity($request, false);
        $this->extend('onAfterDecrementPositionQuantity', $request);
    }
    
    /**
     * Increments the shopping cart position quantity by 1.
     * 
     * @param HTTPRequest $request Request to check for product data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.01.2019
     */
    public function incrementPositionQuantity(HTTPRequest $request) : void
    {
        $this->extend('onBeforeIncrementPositionQuantity', $request);
        $this->changePositionQuantity($request, true);
        $this->extend('onAfterIncrementPositionQuantity', $request);
    }
    
    /**
     * Decrements the shopping cart position quantity by 1.
     * 
     * @param HTTPRequest $request   Request to check for product data
     * @param bool        $increment Set to true to increment the position's 
     *                               quantity by 1, set to fals to decrement the 
     *                               position's quantity by 1
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.01.2019
     */
    public function changePositionQuantity(HTTPRequest $request, bool $increment = true) : void
    {
        $positionID = $request->param('ID');
        $backLinkID = $request->param('OtherID');
        if (is_numeric($positionID)) {
            //check if the position belongs to this user. Malicious people could manipulate it.
            $member   = Customer::currentUser();
            $position = ShoppingCartPosition::get()->byID($positionID);
            if ($member instanceof Member
             && $member->exists()
             && $position instanceof ShoppingCartPosition
             && $position->exists()
             && $position->ShoppingCartID == $member->getCart()->ID
            ) {
                if ($increment) {
                    $position->Product()->addToCart($member->getCart()->ID, 1, true);
                } else {
                    if ($position->Quantity <= 1) {
                        $position->delete();
                    } else {
                        $position->Quantity--;
                        $position->write();
                    }
                }
                $backLink = null;
                if (!is_null($backLinkID)) {
                    $backLinkPage = SiteTree::get()->byID($backLinkID);
                    if ($backLinkPage instanceof SiteTree
                     && $backLinkPage->exists()
                    ) {
                        $backLink = $backLinkPage->Link();
                    }
                }
                $this->redirectBack($backLink);
            }
        }
    }
    
    /**
     * Executes a redirect to the given back link or the referer page.
     * 
     * @param string $backLink Back link to redirect to
     * @param string $anchor   Optional anchor to scroll to after redirect
     * 
     * @return HTTPResponse|null
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.03.2013
     */
    public function redirectBack(string $backLink = null, string $anchor = '') : ?HTTPResponse
    {
        $postVars = $this->getRequest()->postVars();
        if (is_null($backLink)
         && array_key_exists('backLink', $postVars)
        ) {
            if (array_key_exists('HTTP_REFERER', $_SERVER)
             && array_key_exists('backLink', $postVars)
            ) {
                // add potential HTTP GET params to back link
                $referer                    = $_SERVER['HTTP_REFERER'];
                $relativeReferer            = '/' . Director::makeRelative($referer);
                $backLink                   = $postVars['backLink'];
                $relativeBackLink           = '/' . Director::makeRelative($backLink);
                $urlParts                   = explode('/', Director::makeRelative($backLink));
                $relativeUrlEncodedBackLink = '';
                foreach ($urlParts as $urlPart) {
                    $relativeUrlEncodedBackLink .= '/' . str_replace('+', '%20', urlencode($urlPart));
                }

                if ((strpos($relativeReferer, $relativeBackLink) === 0
                  || strpos($relativeReferer, $relativeUrlEncodedBackLink) === 0)
                 && strpos($relativeReferer, '?') > 0
                ) {
                    $refererParts           = explode('?', $relativeReferer);
                    $paramPart              = $refererParts[1];
                    $postVars['backLink']   = $backLink . '?' . $paramPart;
                }
            }
            $backLink = $postVars['backLink'];
            if (!empty($anchor)) {
                $backLink .= $anchor;
            }
        }

        if (is_null($backLink)) {
            return parent::redirectBack();
        } else {
            return $this->redirect($backLink, 302);
        }
    }
    
    /**
     * Action to execute a search query
     * 
     * @param HTTPRequest $request    Request to check for product data
     * @param bool        $doRedirect Redirect after setting search settings?
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.10.2018
     */
    public function doSearch(HTTPRequest $request, bool $doRedirect = true) : void
    {
        $postVars           = $request->postVars();
        if (!array_key_exists('locale', $postVars)
         || empty($postVars['locale'])
        ) {
            $defaultLocale      = Tools::default_locale();
            $postVars['locale'] = $defaultLocale->getLocale();
        }
        Tools::set_current_locale($postVars['locale']);
        i18n::set_locale($postVars['locale']);
        $searchQuery       = trim($postVars['quickSearchQuery']);
        $searchContext     = Product::class;
        $searchResultsPage = Tools::PageByIdentifierCode(Page::IDENTIFIER_SEARCH_RESULTS_PAGE);
        SearchQuery::update_by_query(trim(Convert::raw2sql($searchQuery)));
        Product::setDefaultSort('');
        SearchResultsPage::setCurrentSearchQuery($searchQuery);
        SearchResultsPage::setCurrentSearchContext($searchContext);
        if ($doRedirect) {
            $this->redirect($searchResultsPage->RelativeLink());
        }
    }
    
    /**
     * Action to do a login
     * 
     * @param HTTPRequest $request Request to check for product data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.06.2014
     */
    public function doLogin(HTTPRequest $request) : void
    {
        $postVars   = $request->postVars();
        $rememberMe = false;
        if (array_key_exists('emailaddress', $postVars)
         && array_key_exists('password', $postVars)
        ) {
            $emailAddress = $postVars['emailaddress'];
            $password     = $postVars['password'];
        } else {
            $emailAddress = $postVars['Email'];
            $password     = $postVars['Password'];
        }
        if (array_key_exists('Remember', $postVars)) {
            $rememberMe = $postVars['Remember'];
        }
        $formClassName = $request->postVar('cn');
        if (class_exists($formClassName)) {
            $loginForm = singleton($formClassName);
        } else {
            $loginForm = LoginForm::singleton();
        }
        $member   = Member::get()->filter('Email', $emailAddress)->first();
        $canLogin = true;
        $this->extend('updateCanLogin', $canLogin, $member);
        if ($canLogin
         && $member instanceof Member
         && $member->exists()
        ) {
            if ($member->isLockedOut()) {
                $loginForm->setErrorMessage(_t(
                    Member::class . '.ERRORLOCKEDOUT2',
                    'Your account has been temporarily disabled because of too many failed attempts at ' . 'logging in. Please try again in {count} minutes.',
                    null,
                    ['count' => Member::config()->get('lock_out_delay_mins')]
                ));
                $this->redirectBack($postVars['redirect_to']);
            }
            $authenticator = new MemberAuthenticator();
            $loginData = [
                'Email'    => $emailAddress,
                'Password' => $password,
                'Remember' => $rememberMe,
            ];
            $customer = $authenticator->authenticate($loginData, $this->getRequest(), $result);

            if ($customer instanceof Member
             && $customer->exists()
            ) {
                //transfer cart positions from an anonymous user to the one logging in
                $anonymousCustomer = Customer::currentAnonymousCustomer();
                if ($anonymousCustomer) {
                    $anonymousCustomer->moveShoppingCartTo($customer);
                    $anonymousCustomer->delete();
                }
                
                $authenticator->getLoginHandler($postVars['redirect_to'])->performLogin($customer, $loginData, $this->getRequest());
            } else {
                $failedLoginCount = $member->FailedLoginCount + 1;
                $addToError       = '';
                if ($failedLoginCount >= 3
                 && $failedLoginCount < $member->config()->lock_out_after_incorrect_logins
                ) {
                    $attempts   = $member->config()->lock_out_after_incorrect_logins - $failedLoginCount;
                    $addToError = ' ' . _t(
                        Member::class . '.FailedLoginCountWarning',
                        'Please be aware that your account will be temporarily blocked after {attempts} more failed login attempts.',
                        null,
                        ['attempts' => $attempts]
                    );
                }
                $loginForm->setErrorMessage(Page::singleton()->fieldLabel('CredentialsWrong') . $addToError);
            }
        } else {
            $loginForm->setErrorMessage(Page::singleton()->fieldLabel('CredentialsWrong'));
        }
        $redirectedTo = $this->redirectedTo();
        if (is_null($redirectedTo)
         || $redirectedTo === false
        ) {
            $this->redirectBack($postVars['redirect_to']);
        }
    }
    
    /**
     * Renders the main menu sub navigation for the given product group (by URL ID).
     * 
     * @param HTTPRequest $request Request
     * 
     * @return DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.08.2019
     */
    public function loadSubNavigation(HTTPRequest $request) : DBHTMLText
    {
        $productGroupID = (int) $request->param('ID');
        $productGroup   = ProductGroupPage::get()->byID($productGroupID);
        return $this->renderWith(self::class . '_loadSubNavigation', [
            'ProductGroup' => $productGroup,
        ]);
    }
    
    /**
     * Loads the cookie manager template requested by an AJAX call.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return HTTPResponse
     */
    public function cookieManager(HTTPRequest $request) : HTTPResponse
    {
        $cookiePolicyPage           = CookiePolicyPage::instance();
        $cookiePolicyPageController = ModelAsController::controller_for($cookiePolicyPage);
        /* @var $cookiePolicyPageController \Broarm\CookieConsent\Control\CookiePolicyPageController */
        return HTTPResponse::create($cookiePolicyPageController->renderWith(CookiePolicyPage::class . '_ajax', $cookiePolicyPageController->index($request)));
    }

    /**
     * Returns the link to accept all cookies.
     * 
     * @return string
     */
    public function getAcceptAllCookiesLink() : string
    {
        return Controller::join_links('acceptAllCookies', 'acceptAllCookies');
    }
    
    /**
     * Returns whether the current request was called via AJAX.
     * 
     * @return bool
     */
    public function isAjaxRequest() : bool
    {
        return (bool) $this->getRequest()->isAjax();
    }
}