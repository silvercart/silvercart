<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Forms Checkout
 */

/**
 * form step for customers invoice/shipping address. Adds a form for LOGGED IN
 * or ANONYMOUS customers.
 *
 * @package Silvercart
 * @subpackage Forms Checkout
 * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright pixeltricks GmbH
 * @since 01.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCheckoutFormStep2 extends CustomHtmlForm {

    /**
     * init
     *
     * @param Controller $controller  the controller object
     * @param array      $params      additional parameters
     * @param array      $preferences array with preferences
     * @param bool       $barebone    is the form initialized completely?
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.01.2011
     */
    public function __construct($controller, $params = null, $preferences = null, $barebone = false) {

        parent::__construct($controller, $params, $preferences, $barebone);

        if (!$barebone) {
            /*
             * redirect a user if his cart is empty and no order exists
             */
            $checkoutData = $this->controller->getCombinedStepData();
            if (!Member::currentUser() ||
                (!Member::currentUser()->SilvercartShoppingCart()->isFilled() &&
                 !array_key_exists('orderId', $checkoutData))) {

                $frontPage = SilvercartPage_Controller::PageByIdentifierCode();
                Director::redirect($frontPage->RelativeLink());
            }
            
            if ($this->isCustomerLoggedIn()) {
                $this->registerCustomHtmlForm('SilvercartCheckoutFormStep2Regular', new SilvercartCheckoutFormStep2Regular($this->controller));
                Session::set("redirect", $this->controller->Link());
                $this->registerCustomHtmlForm('SilvercartAddAddressForm', new SilvercartAddAddressForm($this->controller));
            } else {
                $this->registerCustomHtmlForm('SilvercartCheckoutFormStep2Anonymous', new SilvercartCheckoutFormStep2Anonymous($this->controller));
            }
        }
    }

    /**
     * Is customer logged in?
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 01.07.2011
     */
    public function isCustomerLoggedIn() {
        $isLoggedIn = false;
        if (SilvercartCustomer::currentRegisteredCustomer()) {
            $isLoggedIn = true;
        }
        return $isLoggedIn;
    }

    /**
     * Here we set some preferences.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.03.2011
     */
    public function preferences() {
        $this->preferences['stepIsVisible']             = true;
        $this->preferences['stepTitle']                 = _t('SilvercartCheckoutFormStep2.TITLE', 'Addresses');
        $this->preferences['submitButtonTitle']         = _t('SilvercartCheckoutFormStep.FORWARD', 'Next');
        $this->preferences['fillInRequestValues']       = true;
        $this->preferences['loadShoppingcartModules']   = false;
        $this->preferences['createShoppingcartForms']   = false;

        parent::preferences();
    }
    
}

