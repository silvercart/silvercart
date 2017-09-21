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
 * form step for LOGGED IN customers invoice/shipping address
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 01.07.2011
 * @license see license file in modules root directory
 */
class SilvercartCheckoutFormStep2Regular extends CustomHtmlFormStep {

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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.11.2014
     */
    public function __construct($controller, $params = null, $preferences = null, $barebone = false) {
        parent::__construct($controller, $params, $preferences, $barebone);

        if (!$barebone) {
            /*
             * redirect a user if his cart is empty and no order exists
             */
            $checkoutData = $this->controller->getCombinedStepData();
            if (!SilvercartCustomer::currentUser() ||
                (!SilvercartCustomer::currentUser()->getCart()->isFilled() &&
                 !array_key_exists('orderId', $checkoutData))) {

                $frontPage = SilvercartPage_Controller::PageByIdentifierCode();
                if (!$this->getController()->redirectedTo()) {
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
        $this->preferences['stepIsVisible']             = true;
        $this->preferences['stepTitle']                 = _t('SilvercartCheckoutFormStep2.TITLE', 'Addresses');
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
     * @param bool $withUpdate Set to false to skip updates by decorator.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.07.2014
     */
    public function getFormFields($withUpdate = true) {

        // --------------------------------------------------------------------
        // Set i18n labels
        // --------------------------------------------------------------------
        $this->formFields['InvoiceAddressAsShippingAddress']['title'] = _t('SilvercartAddress.InvoiceAddressAsShippingAddress');
        $this->formFields['InvoiceAddress']['title'] = _t('SilvercartPage.BILLING_ADDRESS');
        $this->formFields['ShippingAddress']['title'] = _t('SilvercartPage.SHIPPING_ADDRESS');

        // --------------------------------------------------------------------
        // Insert values from customers saved addresses
        // --------------------------------------------------------------------
        $member = SilvercartCustomer::currentRegisteredCustomer(); //method located in decorator; can not be called via class Member
        if ($member) {
            if ($member->SilvercartInvoiceAddress()->ID > 0) {
                $this->formFields['InvoiceAddress']['value'] = array(
                    $member->SilvercartInvoiceAddress()->ID => $member->SilvercartInvoiceAddress()->ID
                );
            } else {
                $this->formFields['InvoiceAddress']['value'] = $member->SilvercartAddresses()->map()->toArray();
            }
            $this->formFields['ShippingAddress']['value'] = $member->SilvercartAddresses()->map()->toArray();
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
        
        if ($this->InvoiceAddressIsAlwaysShippingAddress()) {
            $this->formFields['InvoiceAddressAsShippingAddress']['type'] = 'HiddenField';
            unset($this->formFields['InvoiceAddressAsShippingAddress']['jsEvents']);
            $this->formFields['ShippingAddress']['selectedValue'] = $this->formFields['InvoiceAddress']['selectedValue'];
        }
        return parent::getFormFields($withUpdate);
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
     * @since 15.11.2014
     */
    public function submitSuccess($data, $form, $formData) {
        // Set invoice address as shipping address if desired
        if ($data['InvoiceAddressAsShippingAddress'] == '1') {
            $formData['ShippingAddress'] = $formData['InvoiceAddress'];
        }
        
        if (SilvercartCustomer::currentUser()->SilvercartAddresses()->Find('ID', $formData['InvoiceAddress']) &&
            SilvercartCustomer::currentUser()->SilvercartAddresses()->Find('ID', $formData['ShippingAddress'])) {
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
            if (!SilvercartCustomer::currentUser()->SilvercartAddresses()->Find('ID', $formData['InvoiceAddress'])) {
                $this->addErrorMessage('InvoiceAddress', _t('SilvercartCheckoutFormStep2.ERROR_ADDRESS_NOT_FOUND', 'The given address was not found.'));
            }
            if (!SilvercartCustomer::currentUser()->SilvercartAddresses()->Find('ID', $formData['ShippingAddress'])) {
                $this->addErrorMessage('ShippingAddress', _t('SilvercartCheckoutFormStep2.ERROR_ADDRESS_NOT_FOUND', 'The given address was not found.'));
            }
        }
    }
    
    /**
     * Returns whether invoice address is always shipping address.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.07.2014
     */
    public function InvoiceAddressIsAlwaysShippingAddress() {
        return SilvercartConfig::InvoiceAddressIsAlwaysShippingAddress();
    }
    
    /**
     * Executed an extension hook to add some HTML content after the invoice
     * address field.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.07.2016
     */
    public function AfterInvoiceAddressContent() {
        $contentParts = $this->extend('updateAfterInvoiceAddressContent');
        return implode(PHP_EOL, $contentParts);
    }
    
    /**
     * Executed an extension hook to add some HTML content after the invoice
     * address field.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.07.2016
     */
    public function BeforeInvoiceAddressContent() {
        $contentParts = $this->extend('updateBeforeInvoiceAddressContent');
        return implode(PHP_EOL, $contentParts);
    }
    
}

