<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilvercartPaymentIPayment.
 *
 * SilvercartPaymentIPayment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilvercartPaymentIPayment is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilvercartPaymentIPayment.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Forms Checkout
 */

/**
 * Default nested form for PaymentMethods
 *
 * @package Silvercart
 * @subpackage Forms Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 11.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCheckoutFormStep4DefaultPayment extends CustomHtmlForm {
    
    /**
     *
     * @var SilvercartPaymentMethod
     */
    protected $paymentMethod = null;
    
    /**
     * constructor. Set alternative form action here.
     *
     * @param Controller $controller  the controller object
     * @param array      $params      additional parameters
     * @param array      $preferences array with preferences
     * @param bool       $barebone    is the form initialized completely?
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2011
     */
    public function __construct($controller, $params = null, $preferences = null, $barebone = false) {
        $member = Member::currentUser();
        $checkoutData = $controller->getCombinedStepData();
        if ($member &&
            is_array($params) &&
            array_key_exists('PaymentMethod', $params)) {
            $paymentMethod = DataObject::get_by_id('SilvercartPaymentMethod', $params['PaymentMethod']);
            if ($paymentMethod) {
                $paymentMethod->setController($controller);
                
                $paymentMethod->setCancelLink(Director::absoluteURL($controller->Link()) . 'GotoStep/2');
                $paymentMethod->setReturnLink(Director::absoluteURL($controller->Link()));

                $paymentMethod->setCustomerDetailsByCheckoutData($checkoutData);
                $paymentMethod->setInvoiceAddressByCheckoutData($checkoutData);
                $paymentMethod->setShippingAddressByCheckoutData($checkoutData);
                if (array_key_exists('ShippingMethod', $checkoutData)) {
                    $member->SilvercartShoppingCart()->setShippingMethodID($checkoutData['ShippingMethod']);
                }
                $paymentMethod->setShoppingCart($member->SilvercartShoppingCart());
                $this->setPaymentMethod($paymentMethod);
            }
        }
        parent::__construct($controller, $params, $preferences, $barebone);
    }

    /**
     * Here we set some preferences.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2011
     */
    public function preferences() {
        $this->preferences['fillInRequestValues']       = true;

        parent::preferences();
    }

    /**
     * executed if there are no valdation errors on submit
     * Form data is saved in session
     *
     * @param SS_HTTPRequest $data     contains the frameworks form data
     * @param Form           $form     not used
     * @param array          $formData contains the modules form data
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 09.11.2010
     */
    public function submitSuccess($data, $form, $formData) {
        $this->controller->setStepData($formData);

        $stepData = $this->controller->getCombinedStepData();

        if ($stepData &&
            isset($stepData['PaymentMethod'])) {
            
            $paymentMethod = DataObject::get_by_id('SilvercartPaymentMethod', $stepData['PaymentMethod']);
        }

        if ($paymentMethod) {
            $this->controller->resetStepMapping();

            $this->controller->registerStepDirectory(
                $paymentMethod->getStepConfiguration()
            );

            $this->controller->generateStepMapping();
            $this->controller->addCompletedStep();
            $this->controller->NextStep();
        } else {
            Director::redirect($this->controller->Link());
        }
    }
    
    /**
     * Returns the related payment method
     *
     * @return SilvercartPaymentMethod 
     */
    public function getPaymentMethod() {
        return $this->paymentMethod;
    }

    /**
     * Sets the related payment method
     *
     * @param SilvercartPaymentMethod $paymentMethod Related payment method
     * 
     * @return void
     */
    public function setPaymentMethod(SilvercartPaymentMethod $paymentMethod) {
        $this->paymentMethod = $paymentMethod;
    }
    
}