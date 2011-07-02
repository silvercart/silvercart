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
 * checkout step for payment method
 *
 * @package Silvercart
 * @subpackage Forms Checkout
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCheckoutFormStep3 extends CustomHtmlForm {

    /**
     * The form field definitions.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 31.03.2011
     */
    protected $formFields = array(
        'PaymentMethod' => array(
            'type'              => 'SilvercartCheckoutOptionsetField',
            'title'             => 'Bezahlart',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        )
    );

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
    public function preferences() {
        $this->preferences['stepIsVisible']             = true;
        $this->preferences['stepTitle']                 = _t('SilvercartCheckoutFormStep3.TITLE', 'Payment');
        $this->preferences['submitButtonTitle']         = _t('SilvercartCheckoutFormStep.FORWARD', 'Next');
        $this->preferences['fillInRequestValues']       = true;
        $this->preferences['loadShoppingcartModules']   = false;
        $this->preferences['createShoppingcartForms']   = false;

        parent::preferences();
    }

    /**
     * Set initial form values
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 09.11.2010
     */
    protected function fillInFieldValues() {
        $allowedPaymentMethods = false;
        
        $this->controller->fillFormFields(&$this->formFields);
        $this->formFields['PaymentMethod']['title'] = _t('SilvercartCheckoutFormStep3.FIELDLABEL');

        $stepData = $this->controller->getCombinedStepData();

        if ($stepData) {
            if ($stepData['Shipping_Country'] != "") {
                $shippingCountry = DataObject::get_by_id('SilvercartCountry', $stepData['Shipping_Country']);
                if ($shippingCountry) {
                    $allowedPaymentMethods = $shippingCountry->SilvercartPaymentMethods('isActive = 1');
                    if ($allowedPaymentMethods) {
                        $this->formFields['PaymentMethod']['value'] = $allowedPaymentMethods->toDropDownMap('ID', 'Name');
                    }
                }
            }
        }

        if (isset($stepData['PaymentMethod'])) {
            $this->formFields['PaymentMethod']['selectedValue'] = $stepData['PaymentMethod'];
        } else {
            if (isset($allowedPaymentMethods) &&
                $allowedPaymentMethods &&
                $allowedPaymentMethods->Count() > 0) {
                $this->formFields['PaymentMethod']['selectedValue'] = $allowedPaymentMethods->First()->ID;
            }
        }
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
}

