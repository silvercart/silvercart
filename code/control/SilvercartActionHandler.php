<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package CustomHtmlForm
 * @subpackage Controller
 */

/**
 * Central handler for form actions.
 *
 * @package Silvercart
 * @subpackage Controller
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.06.2013
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartActionHandler extends DataExtension
{
    /**
     * Allowed actions
     *
     * @var array
     */
    public static $allowed_actions = [
        'addToCart',
        'doSearch',
        'doLogin',
    ];
    
    /**
     * Loads the theme set in SiteConfig on before init.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.01.2019
     */
    public function onBeforeInit()
    {
        $config = SiteConfig::current_site_config();
        if ($config instanceof SiteConfig
         && $config->Theme
        ) {
            Config::inst()->update('SSViewer', 'theme', $config->Theme);
        }
    }

    /**
     * returns a single page by IdentifierCode
     * used to retrieve links dynamically
     *
     * @param string $identifierCode the classes name
     * 
     * @return SiteTree | false a single object of the site tree; without param the SilvercartFrontPage will be returned
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.01.2019
     */
    public static function PageByIdentifierCode($identifierCode = "SilvercartFrontPage")
    {
        return SilvercartTools::PageByIdentifierCode($identifierCode);
    }

    /**
     * returns a page link by IdentifierCode
     *
     * @param string $identifierCode the DataObjects IdentifierCode
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.01.2019
     */
    public static function PageByIdentifierCodeLink($identifierCode = "SilvercartFrontPage")
    {
        return SilvercartTools::PageByIdentifierCodeLink($identifierCode);
    }
    
    /**
     * Action to add a product to cart.
     * 
     * @param SS_HTTPRequest $request Request to check for product data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.01.2019
     */
    public function addToCart(SS_HTTPRequest $request)
    {
        $isValidRequest = false;
        $backLink       = null;
        $postVars       = $request->postVars();
        $isAjax         = $request->postVar('isAjax');
        $params         = $request->allParams();
        $productID      = $params['ID'];
        $quantity       = $params['OtherID'];
        
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
                SilvercartShoppingCart::removeProduct($postVars);
            } else {
                SilvercartShoppingCart::addProduct($postVars);
            }
            
            if (SilvercartConfig::getRedirectToCartAfterAddToCartAction()) {
                $backLink = SilvercartTools::PageByIdentifierCodeLink('SilvercartCartPage');
            }
        }
        
        if ($isAjax) {
            $product              = SilvercartProduct::get()->byID($productID);
            $totalCartQuantity    = 0;
            $quantityInCartString = '';
            $htmlDropdown         = '';
            $htmlModal            = '';
            if ($product instanceof SilvercartProduct) {
                $member               = SilvercartCustomer::currentUser();
                $totalCartQuantity    = $member->getCart()->getQuantity();
                $quantityInCartString = $product->getQuantityInCartString();
                $htmlDropdown         = (string) $this->owner->renderWith('SilvercartShoppingCartDropdown');
                $htmlModal            = (string) $this->owner->renderWith('SilvercartShoppingCartAjaxResponse', [
                    'Product'  => $product,
                    'Quantity' => $quantity,
                ]);
            }
            $json = [
                'TotalCartQuantity'    => $totalCartQuantity,
                'QuantityInCartString' => $quantityInCartString,
                'HTMLDropdown'         => $htmlDropdown,
                'HTMLModal'            => $htmlModal,
            ];
            print json_encode($json);
            exit();
        } else {
            $this->redirectBack($backLink, '#product' . $productID);
        }
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
    protected function redirectBack($backLink = null, $anchor = '') {
        $postVars = $this->owner->getRequest()->postVars();
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
            $this->owner->redirectBack();
        } else {
            $this->owner->redirect($backLink, 302);
        }
    }
    
    /**
     * Action to execute a search query
     * 
     * @param SS_HTTPRequest $request    Request to check for product data
     * @param bool           $doRedirect Redirect after setting search settings?
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.06.2014
     */
    public function doSearch(SS_HTTPRequest $request, $doRedirect = true) {
        $postVars           = $request->postVars();
        if (!array_key_exists('locale', $postVars) ||
            empty($postVars['locale'])) {
            $postVars['locale'] = Translatable::default_locale();
        }
        Translatable::set_current_locale($postVars['locale']);
        i18n::set_locale($postVars['locale']);
        $quickSearchQuery   = trim($postVars['quickSearchQuery']);
        $searchContext      = 'SilvercartProduct';
        $searchResultsPage  = SilvercartTools::PageByIdentifierCode("SilvercartSearchResultsPage");
        $searchQuery        = SilvercartSearchQuery::get_by_query(trim(Convert::raw2sql($quickSearchQuery)));
        $searchQuery->Count++;
        $searchQuery->write();
        SilvercartProduct::setDefaultSort('relevance');
        Session::set("searchQuery",     $quickSearchQuery);
        Session::set('searchContext',   $searchContext);
        Session::save();
        if ($doRedirect) {
            $this->owner->redirect($searchResultsPage->RelativeLink());
        }
    }
    
    /**
     * Action to do a login
     * 
     * @param SS_HTTPRequest $request    Request to check for product data
     * @param bool           $doRedirect Redirect after setting search settings?
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.06.2014
     */
    public function doLogin(SS_HTTPRequest $request, $doRedirect = true) {
        $postVars     = $request->postVars();
        if (array_key_exists('emailaddress', $postVars) &&
            array_key_exists('password', $postVars)) {
            $emailAddress = $postVars['emailaddress'];
            $password     = $postVars['password'];
        } else {
            $emailAddress = $postVars['Email'];
            $password     = $postVars['Password'];
        }
        $member       = Member::get()->filter('Email', $emailAddress)->first();

        if ($member instanceof Member &&
            $member->exists()) {
            $customer = MemberAuthenticator::authenticate(
                array(
                    'Email'    => $emailAddress,
                    'Password' => $password
                )
            );

            if ($customer instanceof Member &&
                $customer->exists()) {
                //transfer cart positions from an anonymous user to the one logging in
                $anonymousCustomer = SilvercartCustomer::currentAnonymousCustomer();
                if ($anonymousCustomer) {
                    if ($anonymousCustomer->getCart()->SilvercartShoppingCartPositions()->count() > 0) {
                        //delete registered customers cart positions
                        if ($customer->getCart()->SilvercartShoppingCartPositions()) {
                            foreach ($customer->getCart()->SilvercartShoppingCartPositions() as $position) {
                                $position->delete();
                            }
                        }
                        //add anonymous positions to the registered user

                        foreach ($anonymousCustomer->getCart()->SilvercartShoppingCartPositions() as $position) {
                            $customer->getCart()->SilvercartShoppingCartPositions()->add($position);
                        }
                    }
                    $anonymousCustomer->delete();
                }
                
                $customer->logIn();
                $customer->write();
            } else {
                $messages = array(
                    'Authentication' => array(
                        'message' => _t('SilvercartPage.CREDENTIALS_WRONG'),
                    )
                );
            }
        } else {
                $messages = array(
                    'Authentication' => array(
                        'message' => _t('SilvercartPage.CREDENTIALS_WRONG'),
                    )
                );
        }
        $this->redirectBack($postVars['redirect_to']);
    }
}