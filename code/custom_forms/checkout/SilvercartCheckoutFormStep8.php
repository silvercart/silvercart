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
 * CheckoutProcessPaymentAfterOrder
 *
 * @package Silvercart
 * @subpackage Forms Checkout
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCheckoutFormStep8 extends CustomHtmlForm {

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
        $member = Member::currentUser();
        $checkoutData = $this->controller->getCombinedStepData();
        $paymentSuccessful = false;

        if ($member) {
            // postprocessing of payment method
            if (!$this->controller->paymentMethodObj) {
                $this->controller->paymentMethodObj = DataObject::get_by_id(
                                'SilvercartPaymentMethod',
                                $checkoutData['PaymentMethod']
                );
            }

            $orderObj = DataObject::get_by_id(
                            'SilvercartOrder',
                            $checkoutData['orderId']
            );

            if ($this->controller->paymentMethodObj &&
                    $orderObj) {

                $this->controller->paymentMethodObj->setController($this->controller);
                $paymentSuccessful = $this->controller->paymentMethodObj->processPaymentAfterOrder($orderObj);
            } else {
                Director::redirect($this->controller->Link() . '/Cancel');
                exit();
            }

            if ($paymentSuccessful) {
                $this->controller->addCompletedStep();
                $this->controller->NextStep();
            }
        }
    }

}