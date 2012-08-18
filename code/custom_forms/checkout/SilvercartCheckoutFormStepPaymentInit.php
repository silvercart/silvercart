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
 * CheckoutProcessPaymentBeforeOrder
 *
 * Ruft die Methode "processPaymentBeforeOrder" im gewaehlten Zahlungsmodul
 * auf.
 *
 * @package Silvercart
 * @subpackage Forms Checkout
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCheckoutFormStepPaymentInit extends CustomHtmlForm {

    protected $paymentMethodObj = null;

    /**
     * constructor
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
        $member = Member::currentUser();
        $checkoutData = $controller->getCombinedStepData();
        if ($member) {
            if (array_key_exists('PaymentMethod', $checkoutData)) {
                $this->paymentMethodObj = DataObject::get_by_id(
                    'SilvercartPaymentMethod',
                    $checkoutData['PaymentMethod']
                );
                if ($this->paymentMethodObj) {
                    $this->paymentMethodObj->setController($controller);

                    $this->paymentMethodObj->setCancelLink(Director::absoluteURL($controller->Link()) . 'GotoStep/2');
                    $this->paymentMethodObj->setReturnLink(Director::absoluteURL($controller->Link()));

                    $this->paymentMethodObj->setCustomerDetailsByCheckoutData($checkoutData);
                    $this->paymentMethodObj->setInvoiceAddressByCheckoutData($checkoutData);
                    $this->paymentMethodObj->setShippingAddressByCheckoutData($checkoutData);
                    $this->paymentMethodObj->setShoppingCart($member->SilvercartShoppingCart());
                }
            }
        }
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
                $this->getController()->redirect($frontPage->RelativeLink());
            }
        }
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
    public function  preferences() {
        $this->preferences['stepIsVisible'] = false;

        parent::preferences();
    }

    /**
     * processor method
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.11.2010
     */
    public function process() {
        if ($this->paymentMethodObj) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Render the error template.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.04.2011
     */
    public function renderError() {
        return $this->renderWith('SilvercartCheckoutFormStepPaymentError');
    }
    
    /**
     * Returns the PaymentMethod to use in template
     *
     * @return SilvercartPaymentMethod
     */
    public function getPaymentMethod() {
        return $this->paymentMethodObj;
    }
}

