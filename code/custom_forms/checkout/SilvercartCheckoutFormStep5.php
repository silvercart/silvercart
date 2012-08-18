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
 * checkout step for order confirmation
 *
 * @package Silvercart
 * @subpackage Forms Checkout
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCheckoutFormStep5 extends SilvercartCheckoutFormStepPaymentInit {

    /**
     * The form field definitions.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 31.03.2011
     */
    protected $formFields = array(
        'ChosenShippingMethod' => array(
            'type'  => 'ReadonlyField',
            'title' => 'gewählte Versandart'
        ),
        'ChosenPaymentMethod' => array(
            'type'  => 'ReadonlyField',
            'title' => 'gewählte Bezahlart'
        ),
        'Note' => array(
            'type' => 'TextareaField'
        ),
        'HasAcceptedTermsAndConditions' => array(
            'type'              => 'CheckboxField',
            'title'             => 'Ich akzeptiere die allgemeinen Geschäftsbedingungen',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'HasAcceptedRevocationInstruction' => array(
            'type'              => 'CheckboxField',
            'title'             => 'Ich habe die Widerrufsbelehrung gelesen',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'SubscribedToNewsletter' => array(
            'type'  => 'CheckboxField',
            'title' => 'Ich möchte den Newsletter abonnieren'
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

                if (!$this->getController()->redirectedTo()) {
                    $frontPage = SilvercartPage_Controller::PageByIdentifierCode();
                    $this->getController()->redirect($frontPage->RelativeLink());
                }
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
        parent::preferences();

        $this->preferences['stepIsVisible']             = true;
        $this->preferences['stepTitle']                 = _t('SilvercartCheckoutFormStep5.TITLE', 'Overview');
        $this->preferences['submitButtonTitle']         = _t('SilvercartCheckoutFormStep.ORDER_NOW', 'Order now');
        $this->preferences['fillInRequestValues']       = true;
        $this->preferences['createShoppingcartForms']   = false;

        $checkoutData = $this->controller->getCombinedStepData();

        if (isset($checkoutData['PaymentMethod'])) {
            $this->paymentMethodObj = DataObject::get_by_id(
                'SilvercartPaymentMethod',
                $checkoutData['PaymentMethod']
            );

            if ($this->paymentMethodObj &&
                $this->paymentMethodObj->hasMethod('getOrderConfirmationSubmitButtonTitle')) {
                $this->preferences['submitButtonTitle'] = $this->paymentMethodObj->getOrderConfirmationSubmitButtonTitle();
            }
        }
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
        $this->controller->fillFormFields($this->formFields);
        $this->formFields['ChosenShippingMethod']['title'] = _t('SilvercartCheckoutFormStep.CHOOSEN_SHIPPING', 'choosen shipping method');
        $this->formFields['ChosenPaymentMethod']['title'] = _t('SilvercartCheckoutFormStep.CHOOSEN_PAYMENT', 'choosen payment method');
        $this->formFields['HasAcceptedTermsAndConditions']['title'] = _t('SilvercartCheckoutFormStep.I_ACCEPT_TERMS', 'I accept the terms and conditions.');
        $this->formFields['HasAcceptedRevocationInstruction']['title'] = _t('SilvercartCheckoutFormStep.I_ACCEPT_REVOCATION', 'I accept the revocation instructions');
        $this->formFields['SubscribedToNewsletter']['title'] = _t('SilvercartCheckoutFormStep.I_SUBSCRIBE_NEWSLETTER', 'I subscribe to the newsletter');

        $stepData = $this->controller->getCombinedStepData();

        if ($stepData &&
            isset($stepData['ShippingMethod']) &&
            isset($stepData['PaymentMethod'])) {
            $chosenShippingMethod = DataObject::get_by_id('SilvercartShippingMethod', $stepData['ShippingMethod']);
            if ($chosenShippingMethod) {
                $this->formFields['ChosenShippingMethod']['value'] = $chosenShippingMethod->Title;
            }

            $chosenPaymentMethod = DataObject::get_by_id('SilvercartPaymentMethod', $stepData['PaymentMethod']);
            if ($chosenPaymentMethod) {
                $this->formFields['ChosenPaymentMethod']['value'] = $chosenPaymentMethod->Name;
            }
        }
    }

    /**
     * returns address data as ArrayData to control in template
     *
     * @return ArrayData
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function AddressData() {
        $checkoutData = $this->controller->getCombinedStepData();
        
        if (array_key_exists('ShippingAddress', $checkoutData) &&
            Member::currentUser()->SilvercartAddresses()->Find('ID', $checkoutData['ShippingAddress'])) {
            
            $shippingAddress    = Member::currentUser()->SilvercartAddresses()->Find('ID', $checkoutData['ShippingAddress']);
        } else {
            /**
             * @deprecated Fallback for potential dependencies 
             */
            $shippingAddress    = $this->controller->extractAddressDataFrom('Shipping', $checkoutData);
            $shippingAddress    = $this->getAssociativeAddressData($shippingAddress);
            $shippingAddress    = new SilvercartAddress($shippingAddress);
            $shippingAddress->setIsAnonymousShippingAddress(true);
        }
        if (array_key_exists('InvoiceAddress', $checkoutData) &&
            Member::currentUser()->SilvercartAddresses()->Find('ID', $checkoutData['InvoiceAddress'])) {
            
            $invoiceAddress = Member::currentUser()->SilvercartAddresses()->Find('ID', $checkoutData['InvoiceAddress']);
        } else {
            /**
             * @deprecated Fallback for potential dependencies 
             */
            $invoiceAddress = $this->controller->extractAddressDataFrom('Invoice', $checkoutData);
            $invoiceAddress = $this->getAssociativeAddressData($invoiceAddress, 'Invoice');
            $invoiceAddress = new SilvercartAddress($invoiceAddress);
            $invoiceAddress->setIsAnonymousInvoiceAddress(true);
        }
        
        if (array_key_exists('InvoiceAddress',$checkoutData) &&
            array_key_exists('ShippingAddress',$checkoutData)) {
            
        
            if ($checkoutData['InvoiceAddress'] === $checkoutData['ShippingAddress']) {
                if (is_array($invoiceAddress)) {
                    /**
                     * @deprecated Fallback for potential dependencies 
                     */
                    $invoiceAddress['isInvoiceAndShippingAddress'] = true;
                } else {
                    Member::currentUser()->SilvercartShippingAddressID  = $checkoutData['ShippingAddress'];
                    Member::currentUser()->SilvercartInvoiceAddressID   = $checkoutData['InvoiceAddress'];
                }
            }
        }
        
        $addressData = new ArrayData(
            array(
                'SilvercartShippingAddress' => $shippingAddress,
                'SilvercartInvoiceAddress'  => $invoiceAddress
            )
        );
        
        return $addressData;
    }
    
    /**
     * Sets some context specific fields in an associative address array
     *
     * @param array  $associativeAddress Associative address array
     * @param string $type               Type of address (Shipping or Invoice)
     * 
     * @return array 
     */
    public function getAssociativeAddressData($associativeAddress, $type = 'Shipping') {
        $country = DataObject::get_by_id(
            'SilvercartCountry',
            $associativeAddress['CountryID']
        );

        if ($country) {
            $associativeAddress['country']             = $country;
            $associativeAddress['SilvercartCountry']   = $country;
            $associativeAddress['SilvercartCountryID'] = $country->ID;
            $associativeAddress['hasAddressData']      = true;
            if ($type == 'Shipping') {
                $associativeAddress['isShippingAddress']    = true;
            } else {
                $associativeAddress['isInvoiceAddress']    = true;
            }
        }
        if (!empty($associativeAddress['TaxIdNumber']) &&
            !empty($associativeAddress['Company'])) {
            $associativeAddress['isCompanyAddress'] = true;
        } else {
            $associativeAddress['isCompanyAddress'] = false;
        }
        
        $silvercartAddress = new SilvercartAddress();
        return $associativeAddress;
    }
    
    /**
     * Indicates wether the invoice and shipping address are the same
     * SilvercartAddress object.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.10.2011
     */
    public function hasOnlyOneStandardAddress() {
        $hasOnlyOneStandardAddress = false;
        $checkoutData              = $this->controller->getCombinedStepData();
        
        if (array_key_exists('InvoiceAddress',$checkoutData) &&
            array_key_exists('ShippingAddress',$checkoutData)) {
            
            if ($checkoutData['InvoiceAddress'] === $checkoutData['ShippingAddress']) {
                $hasOnlyOneStandardAddress = true;
            }
        }
        
        return $hasOnlyOneStandardAddress;
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

    /**
     * Wrapper for the pages method to deside to display prices gross or net.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.03.2011
     */
    public function showPricesGross() {
        return $this->controller->showPricesGross();
    }

    /**
     * Due to a but we had to render the template here. If we would have included
     * it in the *.ss file it would have been rendered twice and our logic would
     * not work propery.
     * 
     * @return string Rendered html code 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sascha Köher <skoehler@pixeltricks.de>
     * @since 19.7.2011
     */
    public function getSilvercartShoppingCartFull() {
        $member = Member::currentUser();
        
        if ($member) {
            return $this->customise($member->SilvercartShoppingCart())->renderWith('SilvercartShoppingCartFull');
        }
    }
    
    /**
     * The newsletter checkbox should not be shown if a registered customer has
     * already subscribed to the newsletter.
     * 
     * @return boolean answer 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 22.7.2011
     */
    public function showNewsletterCheckbox() {
        $customer = SilvercartCustomer::currentRegisteredCustomer();
        if ($customer && $customer->SubscribedToNewsletter == 1) {
            return false;
        }
        return true;
    }
}

