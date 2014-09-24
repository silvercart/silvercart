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
class SilvercartActionHandler extends DataExtension {
    
    /**
     * Allowed actions
     *
     * @var array
     */
    public static $allowed_actions = array(
        'addToCart',
        'doSearch',
    );
    
    /**
     * Action to add a product to cart.
     * 
     * @param SS_HTTPRequest $request Request to check for product data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.03.2013
     */
    public function addToCart(SS_HTTPRequest $request) {
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
                SilvercartShoppingCart::removeProduct($postVars);
            } else {
                SilvercartShoppingCart::addProduct($postVars);
            }
            
            if (SilvercartConfig::getRedirectToCartAfterAddToCartAction()) {
                $backLink = SilvercartTools::PageByIdentifierCodeLink('SilvercartCartPage');
            }
        }
        $this->redirectBack($backLink);
    }
    
    /**
     * Executes a redirect to the given back link or the referer page.
     * 
     * @param string $backLink Back link to redirect to
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.03.2013
     */
    protected function redirectBack($backLink = null) {
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
     * @since 23.06.2014
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
        $searchQuery        = SilvercartSearchQuery::get_by_query(Convert::raw2sql($quickSearchQuery));
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
}