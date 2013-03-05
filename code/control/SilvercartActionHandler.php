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
 * @package CustomHtmlForm
 * @subpackage Controller
 */

/**
 * Central handler for form actions.
 *
 * @package SilverCart
 * @subpackage Controller
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 01.03.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartActionHandler extends DataExtension {
    
    /**
     * Allowed actions
     *
     * @var array
     */
    public static $allowed_actions = array(
        'addToCart',
    );
    
    /**
     * Action to add a product to cart.
     * 
     * @param SS_HTTPRequest $request Request to check for product data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.03.2013
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
            SilvercartShoppingCart::addProduct(
                    array(
                        'productID'         => $productID,
                        'productQuantity'   => $quantity,
                    )
            );
            
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
     * @since 01.03.2013
     */
    protected function redirectBack($backLink = null) {
        $postVars = $this->owner->getRequest()->postVars();
        if (is_null($backLink) &&
            array_key_exists('backLink', $postVars)) {
            $backLink = $postVars['backLink'];
        }

        if (is_null($backLink)) {
            Director::redirectBack();
        } else {
            Director::redirect($backLink, 302);
        }
    }
}