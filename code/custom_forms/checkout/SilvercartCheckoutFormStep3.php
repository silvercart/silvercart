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
 * checkout step for shipping method
 *
 * @package Silvercart
 * @subpackage Forms Checkout
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCheckoutFormStep3 extends CustomHtmlFormStep {

    /**
     * Don't cache this form.
     *
     * @var bool
     */
    protected $excludeFromCache = true;

    /**
     * The form field definitions.
     *
     * @var array
     */
    protected $formFields = array(
        'ShippingMethod' => array(
            'type'              => 'SilvercartShippingOptionsetField',
            'title'             => 'Versandart',
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
                $this->getController()->redirect($frontPage->RelativeLink());
            }
        }
    }

    /**
     * Here we set some preferences.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.02.2013
     */
    public function preferences() {
        $shippingMethods    = SilvercartShippingMethod::getAllowedShippingMethods(null, $this->getShippingAddress());
        $stepIsVisible      = true;
        if ($shippingMethods->count() === 1) {
            $stepIsVisible = false;
        } elseif ($shippingMethods->count() === 0) {
            $shippingMethods = SilvercartShippingMethod::get();
            if ($shippingMethods->count() === 1) {
                $stepIsVisible = false;
            }
        }
        $this->preferences['stepIsVisible']             = $stepIsVisible;
        $this->preferences['stepTitle']                 = _t('SilvercartCheckoutFormStep3.TITLE', 'Shipment');
        $this->preferences['submitButtonTitle']         = _t('SilvercartCheckoutFormStep.FORWARD', 'Next');
        $this->preferences['fillInRequestValues']       = true;
        $this->preferences['loadShoppingcartModules']   = false;
        $this->preferences['createShoppingcartForms']   = false;
        $this->preferences['doJsValidationScrolling']   = false;

        parent::preferences();
    }

    /**
     * Set initial form values
     *
     * @return void
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.02.2013
     */
    protected function fillInFieldValues() {
        $this->controller->fillFormFields($this->formFields);

        $shippingAddress= $this->getShippingAddress();

        if ($shippingAddress) {
            $title = sprintf(
                _t('SilvercartShippingMethod.CHOOSE_SHIPPING_METHOD'),
                $this->getShippingAddress()->SilvercartCountry()->Title
            );
        } else {
            $title = _t('SilvercartShippingMethod.CHOOSE_SHIPPING_METHOD');
        }

        $this->formFields['ShippingMethod']['title'] = $title;
                    
        $shippingMethods = SilvercartShippingMethod::getAllowedShippingMethods(null, $this->getShippingAddress());
        if ($shippingMethods->count() > 0) {
            $map = $shippingMethods->map('ID', 'TitleWithCarrierAndFee');
            if ($map instanceof SS_Map) {
                $map = $map->toArray();
            }
            $this->formFields['ShippingMethod']['value'] = $map;
        }
        
        $stepData = $this->controller->getCombinedStepData();

        if (isset($stepData['ShippingMethod'])) {
            $this->formFields['ShippingMethod']['selectedValue'] = $stepData['ShippingMethod'];
        } else {
            if (isset($shippingMethods) &&
                $shippingMethods &&
                $shippingMethods->exists()) {
                $this->formFields['ShippingMethod']['selectedValue'] = $shippingMethods->First()->ID;
            }
        }
    }
    
    /**
     * If there is only one shipping method, set the shipping method and skip 
     * this step
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.09.2012
     */
    public function process() {
        $shippingMethods = SilvercartShippingMethod::getAllowedShippingMethods(null, $this->getShippingAddress());
        if ($shippingMethods->count() === 1) {
            // there is only one shipping method, set it and skip this step
            $formData = array(
                'ShippingMethod' => $shippingMethods->First()->ID,
            );
            $this->controller->setStepData($formData);
            $this->controller->addCompletedStep();
            $this->controller->NextStep();
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
     * @since 4.1.2011
     */
    public function submitSuccess($data, $form, $formData) {
        if ($this->controller->stepDataChanged($formData, 'ShippingMethod')) {
            $this->controller->removeCompletedStep($this->controller->getCurrentStep() + 1,true);
        }
        $this->controller->setStepData($formData);
        $this->controller->addCompletedStep();
        $this->controller->NextStep();
    }
    
    /**
     * Returns the checkouts current shipping address
     * 
     * @return SilvercartAddress
     */
    public function getShippingAddress() {
        return $this->Controller()->getShippingAddress();
    }

    /**
     * Returns the Cache Key for the current step
     * 
     * @return string
     */
    public function getCacheKeyExtension() {
        return $this->Controller()->getCacheKey();
    }
}

