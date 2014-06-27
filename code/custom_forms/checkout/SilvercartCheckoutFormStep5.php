<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 */

/**
 * checkout step for order confirmation
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 03.01.2011
 * @license see license file in modules root directory
 */
class SilvercartCheckoutFormStep5 extends SilvercartCheckoutFormStepPaymentInit {

    /**
     * Don't cache this form.
     *
     * @var bool
     */
    protected $excludeFromCache = true;

    /**
     * Returns the Cache Key for the current step
     * 
     * @return string
     */
    public function getCacheKeyExtension() {
        return $this->Controller()->getCacheKey();
    }

    /**
     * The form field definitions.
     *
     * @var array
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
            'type' => 'SilvercartTextareaField',
            'rows' => '3',
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.06.2014
     */
    public function __construct($controller, $params = null, $preferences = null, $barebone = false) {
        parent::__construct($controller, $params, $preferences, $barebone);
        if (!$barebone) {
            /*
             * redirect a user if his cart is empty and no order exists
             */
            $checkoutData = $this->controller->getCombinedStepData();
            if (!Member::currentUser() ||
                (!Member::currentUser()->getCart()->isFilled() &&
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
     * @since 31.03.2011
     */
    public function preferences() {
        parent::preferences();

        $this->preferences['stepIsVisible']             = true;
        $this->preferences['stepTitle']                 = _t('SilvercartCheckoutFormStep5.TITLE', 'Overview');
        $this->preferences['submitButtonTitle']         = _t('SilvercartCheckoutFormStep.ORDER_NOW', 'Order now');
        $this->preferences['fillInRequestValues']       = true;
        $this->preferences['createShoppingcartForms']   = false;
        $this->preferences['submitButtonUseButtonTag']  = true;

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
     * @since 09.11.2010
     */
    protected function fillInFieldValues() {
        $this->controller->fillFormFields($this->formFields);
        $this->formFields['ChosenShippingMethod']['title'] = _t('SilvercartCheckoutFormStep.CHOOSEN_SHIPPING', 'choosen shipping method');
        $this->formFields['ChosenPaymentMethod']['title'] = _t('SilvercartCheckoutFormStep.CHOOSEN_PAYMENT', 'choosen payment method');
        $this->formFields['HasAcceptedTermsAndConditions']['title'] = _t('SilvercartCheckoutFormStep.I_ACCEPT_TERMS', 'I accept the terms and conditions.');
        $this->formFields['HasAcceptedRevocationInstruction']['title'] = _t('SilvercartCheckoutFormStep.I_ACCEPT_REVOCATION', 'I accept the revocation instructions');
        $this->formFields['SubscribedToNewsletter']['title'] = _t('SilvercartCheckoutFormStep.I_SUBSCRIBE_NEWSLETTER', 'I subscribe to the newsletter');
        $this->formFields['Note']['placeholder'] = _t('SilvercartPage.YOUR_REMARKS') . '...';

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
            $shippingAddress    = SilvercartTools::extractAddressDataFrom('Shipping', $checkoutData);
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
            $invoiceAddress = SilvercartTools::extractAddressDataFrom('Invoice', $checkoutData);
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sascha Köher <skoehler@pixeltricks.de>
     * @since 27.06.2014
     */
    public function getSilvercartShoppingCartFull() {
        $member = Member::currentUser();
        
        if ($member) {
            return $this->customise($member->getCart())->renderWith('SilvercartShoppingCartFull');
        }
    }

    /**
     * Returns the current shopping cart.
     * 
     * @return SilvercartShoppingCart
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2014
     */
    public function SilvercartShoppingCart() {
        $member = Member::currentUser();
        
        if ($member) {
            return $member->getCart();
        }
    }
    
    /**
     * Alias for self::SilvercartShoppingCart().
     * 
     * @return SilvercartShoppingCart
     */
    public function getCart() {
        return $this->SilvercartShoppingCart();
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

