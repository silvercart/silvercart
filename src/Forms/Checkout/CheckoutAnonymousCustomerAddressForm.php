<?php

namespace SilverCart\Forms\Checkout;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\CustomForm;
use SilverCart\Forms\AddressForm;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Pages\CheckoutStep;
use SilverCart\Model\Pages\Page;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;

/**
 * Form for anonymous customers to enter an address in checkout.
 *
 * @package SilverCart
 * @subpackage Forms_Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 23.11.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CheckoutAnonymousCustomerAddressForm extends AddressForm {
    
    /**
     * Custom extra CSS classes.
     *
     * @var array
     */
    protected $customExtraClasses = [
        'form-vertical',
    ];
    
    /**
     * List of fields to remove on invoice address.
     *
     * @var array
     */
    private static $remove_invoice_fields = [
        'PostNumber',
        'Packstation',
        'IsPackstation',
    ];

    /**
     * Returns the required fields.
     * 
     * @return array
     */
    public function getRequiredFields() {
        $originalFields = parent::getRequiredFields();
        
        foreach ($originalFields as $key => $value) {
            if (is_numeric($key)) {
                $originalFields[$key] = 'InvoiceAddress[' . $value . ']';
                $originalFields['ShippingAddress[' . $value . ']'] = [
                    'isFilledInDependentOn' => [
                        'field'     => 'InvoiceAddressAsShippingAddress',
                        'hasValue'  => '0'
                    ],
                ];
            } else {
                $originalFields['InvoiceAddress[' . $key . ']']   = $value;
                $originalFields['ShippingAddress[' . $key . ']'] = $value;
                unset($originalFields[$key]);
            }
        }
        $requiredFields = array_merge(
                $originalFields,
                [
                    'Email',
                ]
        );
        
        return $requiredFields;
    }

    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields() {
        $originalFields     = parent::getCustomFields();
        $invoiceAdressData  = $this->getController()->getCheckout()->getDataValue('InvoiceAddress');
        $shippingAdressData = $this->getController()->getCheckout()->getDataValue('ShippingAddress');
        $emailAddress       = $this->getController()->getCheckout()->getDataValue('Email');
        $invoiceAsShipping  = $this->getController()->getCheckout()->getDataValue('InvoiceAddressAsShippingAddress');
        if (is_null($invoiceAsShipping)) {
            $invoiceAsShipping = '1';
        }
        foreach ($originalFields as $key => $customField) {
            /* @var $customField \SilverStripe\Forms\FormField */
            $shippingField = clone $customField;
            $fieldName     = $customField->getName();
            if (in_array($fieldName, self::config()->get('remove_invoice_fields'))) {
                unset($originalFields[$key]);
            } else {
                $customField->setName('InvoiceAddress[' . $fieldName . ']');
            }
            $shippingField->setName('ShippingAddress[' . $fieldName . ']');
            $originalFields[] = $shippingField;
            if (is_array($invoiceAdressData) &&
                array_key_exists($fieldName, $invoiceAdressData)) {
                $customField->setValue($invoiceAdressData[$fieldName]);
            }
            if (is_array($shippingAdressData) &&
                array_key_exists($fieldName, $shippingAdressData) &&
                $invoiceAsShipping == '0') {
                $shippingField->setValue($shippingAdressData[$fieldName]);
            }
        }
        
        if ($this->InvoiceAddressIsAlwaysShippingAddress()) {
            $invoiceAsShippingField = HiddenField::create('InvoiceAddressAsShippingAddress', 'InvoiceAddressAsShippingAddress', $invoiceAsShipping);
        } else {
            $invoiceAsShippingField = CheckboxField::create('InvoiceAddressAsShippingAddress', Address::singleton()->fieldLabel('InvoiceAddressAsShippingAddress'), $invoiceAsShipping);
        }
        
        $customFields = array_merge(
                $originalFields,
                [
                    $invoiceAsShippingField,
                    EmailField::create('Email', Address::singleton()->fieldLabel('Email'), $emailAddress),
                ],
                $this->getBirthdayFields()
        );
        
        return $customFields;
    }
    
    /**
     * Returns the birthday fields if enabled.
     * 
     * @return array
     */
    protected function getBirthdayFields() {
        $birthdayFields = [];
        if ($this->UseMinimumAgeToOrder()) {
            $birthdayDays = [
                '' => Tools::field_label('PleaseChoose')
            ];
            for ($idx = 1; $idx < 32; $idx++) {
                $birthdayDays[$idx] = $idx;
            }

            $birthdayFields = [
                DropdownField::create('BirthdayDay', Page::singleton()->fieldLabel('Day'), $birthdayDays),
                DropdownField::create('BirthdayMonth', Page::singleton()->fieldLabel('Month'), Tools::getMonthMap()),
                TextField::create('BirthdayYear', Page::singleton()->fieldLabel('Year'), '', 4),
            ];
        }
        return $birthdayFields;
    }
    
    /**
     * Returns the form actions.
     * 
     * @return FieldList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.04.2018
     */
    public function Actions() {
        $actions = parent::Actions();
        $actions->dataFieldByName('action_submit')->setTitle(CheckoutStep::singleton()->fieldLabel('Forward'));
        return $actions;
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
        if ($data['InvoiceAddressAsShippingAddress'] == '1') {
            $data['ShippingAddress'] = $data['InvoiceAddress'];
        } else {
            $data['InvoiceAddressAsShippingAddress'] = '0';
        }

        if (array_key_exists('IsBusinessAccount', $data['InvoiceAddress'])) {
            unset($data['InvoiceAddress']['IsBusinessAccount']);
        }
        if (array_key_exists('IsBusinessAccount', $data['ShippingAddress'])) {
            unset($data['ShippingAddress']['IsBusinessAccount']);
        }
        
        if (!array_key_exists('CountryID', $data['InvoiceAddress'])) {
            $data['InvoiceAddress']['CountryID'] = $data['InvoiceAddress']['Country'];
        }
        if (!array_key_exists('CountryID', $data['ShippingAddress'])) {
            $data['ShippingAddress']['CountryID'] = $data['ShippingAddress']['Country'];
        }

        $checkout = $this->getController()->getCheckout();
        /* @var $checkout \SilverCart\Checkout\Checkout */
        $checkout->addData($data);
        $checkout->getCurrentStep()->complete();
        $checkout->getCurrentStep()->redirectToNextStep();
    }
    
    /**
     * Returns thether to use a minimum age to order
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.01.2014
     */
    public function UseMinimumAgeToOrder() {
        return Config::UseMinimumAgeToOrder();
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
    
}
