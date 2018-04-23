<?php

namespace SilverCart\Control;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\ShoppingCart;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Product\Product;
use SilverCart\Model\SearchQuery;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Convert;
use SilverStripe\i18n\i18n;
use SilverStripe\Security\Member;
use SilverStripe\Security\MemberAuthenticator\MemberAuthenticator;
use Translatable;

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
class ActionHandler extends Controller {
    
    /**
     * Allowed actions
     *
     * @var array
     */
    private static $allowed_actions = array(
        'addToCart',
        'doSearch',
        'doLogin',
    );
    
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
    public function addToCart(HTTPRequest $request) {
        $isValidRequest = false;
        $backLink       = null;
        $postVars       = $request->postVars();
        $params         = $request->allParams();
        $productID      = $params['ID'];
        $quantity       = $params['OtherID'];
        
        if (is_null($productID) ||
            is_null($quantity)) {
            if (array_key_exists('productID',       $postVars) &&
                array_key_exists('productQuantity', $postVars)) {
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
                ShoppingCart::removeProduct($postVars);
            } else {
                ShoppingCart::addProduct($postVars);
            }
            
            if (Config::getRedirectToCartAfterAddToCartAction()) {
                $backLink = Tools::PageByIdentifierCodeLink('SilvercartCartPage');
            }
        }
        
        $this->redirectBack($backLink, '#product' . $productID);
    }
    
    /**
     * Executes a redirect to the given back link or the referer page.
     * 
     * @param string $backLink Back link to redirect to
     * @param string $anchor   Optional anchor to scroll to after redirect
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.03.2013
     */
    public function redirectBack($backLink = null, $anchor = '') {
        $postVars = $this->getRequest()->postVars();
        if (is_null($backLink) &&
            array_key_exists('backLink', $postVars)) {
            if (array_key_exists('HTTP_REFERER', $_SERVER) &&
                array_key_exists('backLink', $postVars)) {
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

                if ((strpos($relativeReferer, $relativeBackLink) === 0 ||
                     strpos($relativeReferer, $relativeUrlEncodedBackLink) === 0) &&
                    strpos($relativeReferer, '?') > 0) {
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
     * @param bool           $doRedirect Redirect after setting search settings?
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.06.2014
     */
    public function doSearch(HTTPRequest $request, $doRedirect = true) {
        $postVars           = $request->postVars();
        if (!array_key_exists('locale', $postVars) ||
            empty($postVars['locale'])) {
            $postVars['locale'] = Translatable::default_locale();
        }
        Translatable::set_current_locale($postVars['locale']);
        i18n::set_locale($postVars['locale']);
        $quickSearchQuery   = trim($postVars['quickSearchQuery']);
        $searchContext      = Product::class;
        $searchResultsPage  = Tools::PageByIdentifierCode("SilvercartSearchResultsPage");
        $searchQuery        = SearchQuery::get_by_query(trim(Convert::raw2sql($quickSearchQuery)));
        $searchQuery->Count++;
        $searchQuery->write();
        Product::setDefaultSort('relevance');
        Tools::Session()->set("searchQuery",     $quickSearchQuery);
        Tools::Session()->set('searchContext',   $searchContext);
        Tools::saveSession();
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
    public function doLogin(HTTPRequest $request) {
        $postVars   = $request->postVars();
        $rememberMe = false;
        if (array_key_exists('emailaddress', $postVars) &&
            array_key_exists('password', $postVars)) {
            $emailAddress = $postVars['emailaddress'];
            $password     = $postVars['password'];
        } else {
            $emailAddress = $postVars['Email'];
            $password     = $postVars['Password'];
        }
        if (array_key_exists('Remember', $postVars)) {
            $rememberMe = $postVars['Remember'];
        }
        $member = Member::get()->filter('Email', $emailAddress)->first();

        if ($member instanceof Member &&
            $member->exists()) {
            $authenticator = new MemberAuthenticator();
            $loginData = array(
                'Email'    => $emailAddress,
                'Password' => $password,
                'Remember' => $rememberMe,
            );
            $customer = $authenticator->authenticate($loginData, $this->getRequest(), $result);

            if ($customer instanceof Member &&
                $customer->exists()) {
                //transfer cart positions from an anonymous user to the one logging in
                $anonymousCustomer = Customer::currentAnonymousCustomer();
                if ($anonymousCustomer) {
                    if ($anonymousCustomer->getCart()->ShoppingCartPositions()->count() > 0) {
                        //delete registered customers cart positions
                        if ($customer->getCart()->ShoppingCartPositions()) {
                            foreach ($customer->getCart()->ShoppingCartPositions() as $position) {
                                $position->delete();
                            }
                        }
                        //add anonymous positions to the registered user

                        foreach ($anonymousCustomer->getCart()->ShoppingCartPositions() as $position) {
                            $customer->getCart()->ShoppingCartPositions()->add($position);
                        }
                    }
                    $anonymousCustomer->delete();
                }
                
                $authenticator->getLoginHandler($postVars['redirect_to'])->performLogin($customer, $loginData, $this->getRequest());
            } else {
                $messages = array(
                    'Authentication' => array(
                        Page::singleton()->fieldLabel('CredentialsWrong'),
                    )
                );
            }
        } else {
            $messages = array(
                'Authentication' => array(
                    Page::singleton()->fieldLabel('CredentialsWrong'),
                )
            );
        }
        $this->redirectBack($postVars['redirect_to']);
    }
}