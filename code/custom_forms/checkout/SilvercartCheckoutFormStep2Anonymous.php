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
 * form step for ANONYMOUS customers invoice/shipping address
 *
 * @package Silvercart
 * @subpackage Forms Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright pixeltricks GmbH
 * @since 01.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCheckoutFormStep2Anonymous extends SilvercartAddressForm {

    /**
     * Don't cache this form.
     *
     * @var bool
     */
    protected $excludeFromCache = true;

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

                if (!Director::redirected_to()) {
                    $frontPage = SilvercartPage_Controller::PageByIdentifierCode();
                    Director::redirect($frontPage->RelativeLink());
                }
            }
        }
    }
    
    /**
     * Modifies the default address form fields
     * 
     * @param bool $withUpdate Execute update method of decorators?
     * 
     * @return array
     */
    public function getFormFields($withUpdate = true) {
        parent::getFormFields(false);
        if (!array_key_exists('InvoiceAddressAsShippingAddress', $this->formFields)) {
            foreach ($this->formFields as $fieldName => $fieldData) {
                $this->formFields['Invoice_' . $fieldName]  = $fieldData;
                $this->formFields['Shipping_' . $fieldName] = $fieldData;
                unset($this->formFields[$fieldName]);
            }
            if (array_key_exists('Invoice_IsPackstation', $this->formFields)) {
                unset($this->formFields['Invoice_PostNumber']);
                unset($this->formFields['Invoice_Packstation']);
                unset($this->formFields['Invoice_IsPackstation']);
            }
            $this->formFields['InvoiceAddressAsShippingAddress'] = array(
                'type'      => 'CheckboxField',
                'title'     => _t('SilvercartAddress.InvoiceAddressAsShippingAddress'),
                'value'     => '1',
                'jsEvents'  => array(
                    'setEventHandler' => array(
                        'type'          => 'click',
                        'callFunction'  => 'toggleShippingAddressSection'
                    )
                )
            );
            $this->formFields['Email'] = array(
                'type'              => 'TextField',
                'title'             => _t('SilvercartAddress.EMAIL'),
                'checkRequirements' => array(
                    'isEmailAddress'    => true,
                    'isFilledIn'        => true
                )
            );
            
        }

        if ($this->EnableBusinessCustomers()) {
            $this->formFields['Invoice_TaxIdNumber']['checkRequirements']['isFilledInDependantOn']['field'] = 'Invoice_IsBusinessAccount';
            $this->formFields['Shipping_TaxIdNumber']['checkRequirements']['isFilledInDependantOn']['field'] = 'Shipping_IsBusinessAccount';

            $this->formFields['Invoice_Company']['checkRequirements']['isFilledInDependantOn']['field'] = 'Invoice_IsBusinessAccount';
            $this->formFields['Shipping_Company']['checkRequirements']['isFilledInDependantOn']['field'] = 'Shipping_IsBusinessAccount';
        }

        if ($withUpdate && !empty($this->class)) {
            $this->extend('updateFormFields', $this->formFields);
        }
        return $this->formFields;
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
        parent::preferences();
        $this->preferences['stepIsVisible']             = true;
        $this->preferences['stepTitle']                 = _t('SilvercartCheckoutFormStep2.TITLE', 'Addresses');
        $this->preferences['submitButtonTitle']         = _t('SilvercartCheckoutFormStep.FORWARD', 'Next');
        $this->preferences['fillInRequestValues']       = true;
        $this->preferences['loadShoppingcartModules']   = false;
        $this->preferences['createShoppingcartForms']   = false;
        $this->preferences['doJsValidationScrolling']   = false;
        return $this->preferences;
    }

    /**
     * Set initial form values
     *
     * @return void
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.12.2012
     */
    protected function fillInFieldValues() {
        $countries = DataObject::get('SilvercartCountry', "`SilvercartCountry`.`Active`=1");
        if ($countries) {
            $this->formFields['Shipping_Country']['value']  = SilvercartCountry::getPrioritiveDropdownMap(true, _t('SilvercartCheckoutFormStep2.EMPTYSTRING_COUNTRY'));
            $this->formFields['Invoice_Country']['value']   = SilvercartCountry::getPrioritiveDropdownMap(true, _t('SilvercartCheckoutFormStep2.EMPTYSTRING_COUNTRY'));
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pxieltricks GmbH
     * @since 13.03.2011
     */
    public function submit($data, $form) {

        // Disable the check instructions if the shipping address shall be
        // the same as the invoice address.
        if ($data['InvoiceAddressAsShippingAddress'] == '1') {
            $this->deactivateValidationFor('Shipping_Salutation');
            $this->deactivateValidationFor('Shipping_FirstName');
            $this->deactivateValidationFor('Shipping_Surname');
            $this->deactivateValidationFor('Shipping_Addition');
            $this->deactivateValidationFor('Shipping_Street');
            $this->deactivateValidationFor('Shipping_StreetNumber');
            $this->deactivateValidationFor('Shipping_Postcode');
            $this->deactivateValidationFor('Shipping_City');
            $this->deactivateValidationFor('Shipping_PhoneAreaCode');
            $this->deactivateValidationFor('Shipping_Phone');
            $this->deactivateValidationFor('Shipping_Country');
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 09.11.2010
     */
    public function submitSuccess($data, $form, $formData) {

        // Set invoice address as shipping address if desired
        if ($data['InvoiceAddressAsShippingAddress'] == '1') {
            $formData['Shipping_Salutation']       = $formData['Invoice_Salutation'];
            $formData['Shipping_FirstName']        = $formData['Invoice_FirstName'];
            $formData['Shipping_Surname']          = $formData['Invoice_Surname'];
            $formData['Shipping_Addition']         = $formData['Invoice_Addition'];
            $formData['Shipping_Street']           = $formData['Invoice_Street'];
            $formData['Shipping_StreetNumber']     = $formData['Invoice_StreetNumber'];
            $formData['Shipping_Postcode']         = $formData['Invoice_Postcode'];
            $formData['Shipping_City']             = $formData['Invoice_City'];
            $formData['Shipping_PhoneAreaCode']    = $formData['Invoice_PhoneAreaCode'];
            $formData['Shipping_Phone']            = $formData['Invoice_Phone'];
            $formData['Shipping_Country']          = $formData['Invoice_Country'];

            if ($this->EnableBusinessCustomers()) {
                $formData['Shipping_Company']           = $formData['Invoice_Company'];
                $formData['Shipping_TaxIdNumber']       = $formData['Invoice_TaxIdNumber'];
                $formData['Shipping_IsBusinessAccount'] = $formData['Invoice_IsBusinessAccount'];
            }
        }

        if (array_key_exists('Invoice_IsBusinessAccount', $formData)) {
            unset($formData['Invoice_IsBusinessAccount']);
        }
        if (array_key_exists('Shipping_IsBusinessAccount', $formData)) {
            unset($formData['Shipping_IsBusinessAccount']);
        }

        $this->controller->setStepData($formData);
        $this->controller->addCompletedStep();
        $this->controller->NextStep();
    }
}

