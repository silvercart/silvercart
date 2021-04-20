<?php

namespace SilverCart\Forms\Checkout;

use SilverCart\Admin\Model\Config;
use SilverCart\Checkout\Checkout;
use SilverCart\Forms\CustomForm;
use SilverCart\Forms\FormFields\AddressOptionsetField;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Pages\CheckoutStep;
use SilverCart\Model\Pages\Page;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Security\Member;

/**
 * Form for logged in customers to choose an address in checkout.
 *
 * @package SilverCart
 * @subpackage Forms_Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CheckoutRegularCustomerAddressForm extends CustomForm {
    
    /**
     * Custom extra CSS classes.
     *
     * @var array
     */
    protected $customExtraClasses = [
        'form-horizontal',
    ];
    
    /**
     * List of required fields.
     *
     * @var array
     */
    private static $requiredFields = [
        'InvoiceAddress',
        'ShippingAddress',
    ];

    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields() {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $registeredCustomer = Customer::currentRegisteredCustomer();
            if (!($registeredCustomer instanceof Member) ||
                !$registeredCustomer->exists()) {
                return;
            }
            $invoiceAddressFieldValue = [];
            $this->extend('overwriteInvoiceAddressFieldValue', $invoiceAddressFieldValue, $registeredCustomer);
            if (empty($invoiceAddressFieldValue)) {
                if ($registeredCustomer->InvoiceAddress()->ID > 0) {
                    $invoiceAddressFieldValue = [
                        $registeredCustomer->InvoiceAddress()->ID => $registeredCustomer->InvoiceAddress()->ID
                    ];
                } else {
                    $invoiceAddressFieldValue = $registeredCustomer->Addresses()->map()->toArray();
                }
                $this->extend('updateInvoiceAddressFieldValue', $invoiceAddressFieldValue, $registeredCustomer);
            }
            $shippingAddressFieldValue = [];
            $this->extend('overwriteShippingAddressFieldValue', $shippingAddressFieldValue, $registeredCustomer);
            if (empty($shippingAddressFieldValue)) {
                $shippingAddressFieldValue = $registeredCustomer->Addresses()->map()->toArray();
                $this->extend('updateShippingAddressFieldValue', $shippingAddressFieldValue, $registeredCustomer);
            }
            $invoiceAddressFieldSelectedValue  = $registeredCustomer->InvoiceAddress()->ID;
            $shippingAddressFieldSelectedValue = $registeredCustomer->ShippingAddress()->ID;
            if ($this->InvoiceAddressIsAlwaysShippingAddress()) {
                $invoiceAddressAsShippingAddressField = HiddenField::create('InvoiceAddressAsShippingAddress', 'InvoiceAddressAsShippingAddress', '1');
                $shippingAddressFieldSelectedValue    = $invoiceAddressFieldSelectedValue;
            } else {
                $invoiceAddressAsShippingAddressField = CheckboxField::create('InvoiceAddressAsShippingAddress', Address::singleton()->fieldLabel('InvoiceAddressAsShippingAddress'), '1');
            }
            
            $checkout = $this->getController()->getCheckout();
            /* @var $checkout \SilverCart\Checkout\Checkout */
            $invoiceAddress                  = $checkout->getDataValue('InvoiceAddress');
            $shippingAddress                 = $checkout->getDataValue('ShippingAddress');
            $invoiceAddressAsShippingAddress = $checkout->getDataValue('InvoiceAddressAsShippingAddress');
            if (is_array($invoiceAddress) &&
                array_key_exists('ID', $invoiceAddress)) {
                $invoiceAddressFieldSelectedValue = $invoiceAddress['ID'];
            }
            if (is_array($shippingAddress) &&
                array_key_exists('ID', $shippingAddress)) {
                $shippingAddressFieldSelectedValue = $shippingAddress['ID'];
            }
            if (!is_null($invoiceAddressAsShippingAddress)) {
                $invoiceAddressAsShippingAddressField->setValue($invoiceAddressAsShippingAddress ? '1' : '0');
            }
            
            $fields += [
                $invoiceAddressAsShippingAddressField,
                AddressOptionsetField::create('InvoiceAddress', Page::singleton()->fieldLabel('InvoiceAddress'), $invoiceAddressFieldValue, $invoiceAddressFieldSelectedValue),
                AddressOptionsetField::create('ShippingAddress', Page::singleton()->fieldLabel('ShippingAddress'), $shippingAddressFieldValue, $shippingAddressFieldSelectedValue),
            ];
        });
        return parent::getCustomFields();
    }
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomActions() {
        $this->beforeUpdateCustomActions(function (array &$actions) {
            $actions += [
                FormAction::create('submit', CheckoutStep::singleton()->fieldLabel('Forward'))
                    ->setUseButtonTag(true)->addExtraClass('btn-primary')
            ];
        });
        return parent::getCustomActions();
    }
    
    /**
     * Submits the form.
     * 
     * @param array      $data Submitted data
     * @param CustomForm $form Form
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function doSubmit($data, CustomForm $form) {
        // Set invoice address as shipping address if desired
        if (array_key_exists('InvoiceAddressAsShippingAddress', $data) &&
            $data['InvoiceAddressAsShippingAddress'] == '1') {
            $data['ShippingAddress'] = $data['InvoiceAddress'];
        }
        
        if (Customer::currentUser()->Addresses()->find('ID', $data['InvoiceAddress']) &&
            Customer::currentUser()->Addresses()->find('ID', $data['ShippingAddress'])) {
            $invoiceAddress  = Address::get()->byID($data['InvoiceAddress']);
            $shippingAddress = Address::get()->byID($data['ShippingAddress']);
            $data['InvoiceAddress']  = $invoiceAddress->toMap();
            $data['ShippingAddress'] = $shippingAddress->toMap();
            
            $checkout = $this->getController()->getCheckout();
            $currentStep = $checkout->getCurrentStep();
            /* @var $checkout \SilverCart\Checkout\Checkout */
            $checkout->addData($data);
            $currentStep->complete();
            $currentStep->redirectToNextStep();
        } else {
            if (!Customer::currentUser()->Addresses()->Find('ID', $data['InvoiceAddress'])) {
                $this->setErrorMessage(_t(CheckoutFormStep2::class . '.ERROR_ADDRESS_NOT_FOUND', 'The given address was not found.'));
            }
            if (!Customer::currentUser()->Addresses()->Find('ID', $data['ShippingAddress'])) {
                $this->setErrorMessage(_t(CheckoutFormStep2::class . '.ERROR_ADDRESS_NOT_FOUND', 'The given address was not found.'));
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
        return Config::InvoiceAddressIsAlwaysShippingAddress();
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
