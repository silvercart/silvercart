<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * CustomHtmlForm is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * CustomHtmlForms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with CustomHtmlForms.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
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
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartActionHandler extends DataObjectDecorator {
    
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
            Director::redirectBack();
        } else {
            Director::redirect($backLink, 302);
        }
    }
    
    /**
     * Action to execute a search query
     * 
     * @param SS_HTTPRequest $request Request to check for product data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.09.2013
     */
    public function doSearch(SS_HTTPRequest $request) {
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
        Director::redirect($searchResultsPage->RelativeLink());
    }
}