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
 * form step for LOGGED IN customers invoice/shipping address
 *
 * @package Silvercart
 * @subpackage Forms Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright pixeltricks GmbH
 * @since 01.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCheckoutFormStep2Regular extends CustomHtmlForm {

    /**
     * The form field definitions.
     *
     * @var array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2011
     */
    protected $formFields = array(
        'InvoiceAddressAsShippingAddress' => array(
            'type'      => 'CheckboxField',
            'title'     => 'Rechnungsadresse als Versandadresse nutzen',
            'value'     => '1',
            'jsEvents'  => array(
                'setEventHandler' => array(
                    'type'          => 'click',
                    'callFunction'  => 'toggleShippingAddressSection'
                )
            )
        ),
        'InvoiceAddress' => array(
            'type'              => 'SilvercartAddressOptionsetField',
            'title'             => 'Rechnungsadresse',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'ShippingAddress' => array(
            'type'              => 'SilvercartAddressOptionsetField',
            'title'             => 'Lieferadresse',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
    );

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
        $this->preferences['stepTitle']                 = _t('SilvercartCheckoutFormStep2.TITLE', 'Addresses');
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2011
     */
    protected function fillInFieldValues() {

        // --------------------------------------------------------------------
        // Set i18n labels
        // --------------------------------------------------------------------
        $this->formFields['InvoiceAddressAsShippingAddress']['title'] = _t('SilvercartAddress.InvoiceAddressAsShippingAddress');
        $this->formFields['InvoiceAddress']['title'] = _t('SilvercartPage.BILLING_ADDRESS');
        $this->formFields['ShippingAddress']['title'] = _t('SilvercartPage.SHIPPING_ADDRESS');

        // --------------------------------------------------------------------
        // Insert values from customers saved addresses
        // --------------------------------------------------------------------
        $member = SilvercartCustomerRole::currentRegisteredCustomer(); //method located in decorator; can not be called via class Member
        if ($member) {
            $this->formFields['InvoiceAddress']['value'] = $member->SilvercartAddresses()->map();
            $this->formFields['ShippingAddress']['value'] = $member->SilvercartAddresses()->map();
            if ($member->SilvercartInvoiceAddress()) {
                $this->formFields['InvoiceAddress']['selectedValue'] = $member->SilvercartInvoiceAddress()->ID;
            }
            if ($member->SilvercartShippingAddress()) {
                $this->formFields['ShippingAddress']['selectedValue'] = $member->SilvercartShippingAddress()->ID;
            }
        }

        // --------------------------------------------------------------------
        // Insert values from previous entries the customer has made
        // --------------------------------------------------------------------
        $this->controller->fillFormFields($this->formFields);

        if ($this->formFields['InvoiceAddressAsShippingAddress']['value'] == '1') {
            $this->controller->addJavascriptOnloadSnippet(
                array(
                    'deactivateShippingAddressValidation();
                    $(\'#ShippingAddressFields\').css(\'display\', \'none\');',
                    'loadInTheEnd'
                )
            );
        }
    }

    /**
     * We intercept the submit handler since we have to alter some field
     * checks depending on the status of the field "InvoiceAddressAsShippingAddress".
     *
     * @param SS_HTTPRequest $data submit data
     * @param Form           $form form object
     *
     * @return ViewableData
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @copyright 2011 pxieltricks GmbH
     * @since 01.07.2011
     */
    public function submit($data, $form) {
        // Disable the check instructions if the shipping address shall be
        // the same as the invoice address.
        if ($data['InvoiceAddressAsShippingAddress'] == '1') {
            $this->deactivateValidationFor('ShippingAddress');
        }

        parent::submit($data, $form);
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 04.07.2011
     */
    public function submitSuccess($data, $form, $formData) {
        // Set invoice address as shipping address if desired
        if ($data['InvoiceAddressAsShippingAddress'] == '1') {
            $formData['ShippingAddress'] = $formData['InvoiceAddress'];
        }
        
        if (Member::currentUser()->SilvercartAddresses()->Find('ID', $formData['InvoiceAddress']) &&
            Member::currentUser()->SilvercartAddresses()->Find('ID', $formData['ShippingAddress'])) {
            $invoiceAddress = DataObject::get_by_id('SilvercartAddress', $formData['InvoiceAddress']);
            $formData = array_merge(
                    $formData,
                    $this->controller->joinAddressDataTo('Invoice', $invoiceAddress->toMap())
            );
            $shippingAddress = DataObject::get_by_id('SilvercartAddress', $formData['ShippingAddress']);
            $formData = array_merge(
                    $formData,
                    $this->controller->joinAddressDataTo('Shipping', $shippingAddress->toMap())
            );
            $this->controller->setStepData($formData);
            $this->controller->addCompletedStep();
            $this->controller->NextStep();
        } else {
            if (!Member::currentUser()->SilvercartAddresses()->Find('ID', $formData['InvoiceAddress'])) {
                $this->addErrorMessage('InvoiceAddress', _t('SilvercartCheckoutFormStep2.ERROR_ADDRESS_NOT_FOUND', 'The given address was not found.'));
            }
            if (!Member::currentUser()->SilvercartAddresses()->Find('ID', $formData['ShippingAddress'])) {
                $this->addErrorMessage('ShippingAddress', _t('SilvercartCheckoutFormStep2.ERROR_ADDRESS_NOT_FOUND', 'The given address was not found.'));
            }
        }
    }
}

