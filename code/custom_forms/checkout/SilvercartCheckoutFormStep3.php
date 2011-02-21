<?php
/*
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
 */

/**
 * checkout step for shipping method
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCheckoutFormStep3 extends CustomHtmlForm {

    protected $formFields = array(
        'ShippingMethod' => array(
            'type'              => 'DropdownField',
            'title'             => 'Versandart',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        )
    );

    /**
     * preferences
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 26.1.2011
     */
    protected $preferences = array(
        'submitButtonTitle'     => 'weiter',
        'stepTitle'             => 'Versandart'
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
            // redirect a user if his cart is empty
            if (!Member::currentUser()->SilvercartShoppingCart()->isFilled()) {
                $frontPage = SilvercartPage_Controller::PageByIdentifierCode();
                Director::redirect($frontPage->RelativeLink());
            }
        }
    }

    /**
     * Set initial form values
     *
     * @return void
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 3.1.2011
     */
    protected function fillInFieldValues() {
        $this->controller->fillFormFields(&$this->formFields);
        $this->formFields['ShippingMethod']['title'] = _t('SilvercartShippingMethod.SINGULARNAME');

        $stepData       = $this->controller->getCombinedStepData();
        $paymentMethod  = DataObject::get_by_id('SilvercartPaymentMethod', $stepData['PaymentMethod']);
        
        if ($paymentMethod) {
            $shippingMethods = $paymentMethod->getAllowedShippingMethods();
            if ($shippingMethods) {
                //allow only activated shipping methods
                $activatedShippingMethods = new DataObjectSet();
                foreach ($shippingMethods as $shippingMethod) {
                    if ($shippingMethod->isActive == true) {
                        $activatedShippingMethods->push($shippingMethod);
                    }
                }
                $this->formFields['ShippingMethod']['value'] = $activatedShippingMethods->map('ID', 'TitleWithCarrierAndFee', _t('SilvercartCheckoutFormStep3.EMPTYSTRING_SHIPPINGMETHOD', '--choose shipping method--'));
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
     * @since 4.1.2011
     */
    public function submitSuccess($data, $form, $formData) {
        $this->controller->setStepData($formData);
        $this->controller->addCompletedStep();
        $this->controller->NextStep();
    }
}

