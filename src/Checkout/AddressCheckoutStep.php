<?php

namespace SilverCart\Checkout;

use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Customer;

/**
 * AddressCheckoutStep.
 * Trait to provide address related methods to a checkout step.
 *
 * @package SilverCart
 * @subpackage Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 12.04.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
trait AddressCheckoutStep
{
    /**
     * Invoice address.
     *
     * @var \SilverCart\Model\Customer\Address
     */
    protected $invoiceAddress = null;
    /**
     * Shipping address.
     *
     * @var \SilverCart\Model\Customer\Address
     */
    protected $shippingAddress = null;
    /**
     * Determines whether the invoice address is also used as shipping address.
     *
     * @var bool
     */
    protected $invoiceAddressIsShippingAddress = false;
    
    /**
     * Returns the invoice address.
     * 
     * @return \SilverCart\Model\Customer\Address
     */
    public function getInvoiceAddress() : ?Address
    {
        return $this->invoiceAddress;
    }

    /**
     * Returns the shipping address.
     * 
     * @return \SilverCart\Model\Customer\Address
     */
    public function getShippingAddress() : ?Address
    {
        return $this->shippingAddress;
    }

    /**
     * Returns whether the invoice address is also used as shipping address.
     * 
     * @return bool
     */
    public function getInvoiceAddressIsShippingAddress() : bool
    {
        return $this->invoiceAddressIsShippingAddress;
    }

    /**
     * Sets the invoice address.
     * 
     * @param \SilverCart\Model\Customer\Address $invoiceAddress Invoice address
     * 
     * @return \SilverCart\Checkout\CheckoutStep
     */
    public function setInvoiceAddress(Address $invoiceAddress) : CheckoutStep
    {
        $this->invoiceAddress = $invoiceAddress;
        return $this;
    }

    /**
     * Sets the shipping address.
     * 
     * @param \SilverCart\Model\Customer\Address $shippingAddress Shipping address
     * 
     * @return \SilverCart\Checkout\CheckoutStep
     */
    public function setShippingAddress(Address $shippingAddress) : CheckoutStep
    {
        $this->shippingAddress = $shippingAddress;
        return $this;
    }

    /**
     * Sets whether the invoice address is also used as shipping address.
     * 
     * @param bool $invoiceAddressIsShippingAddress Invoice address is shipping address?
     * 
     * @return \SilverCart\Checkout\CheckoutStep
     */
    public function setInvoiceAddressIsShippingAddress(bool $invoiceAddressIsShippingAddress) : CheckoutStep
    {
        $this->invoiceAddressIsShippingAddress = $invoiceAddressIsShippingAddress;
        return $this;
    }
    
    /**
     * Initializes the address data.
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return \SilverCart\Checkout\CheckoutStep
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.04.2018
     */
    public function initAddressData(array $checkoutData = null) : CheckoutStep
    {
        if (is_null($checkoutData)) {
            $checkoutData = $this->getCheckout()->getData();
        }
        if (array_key_exists('InvoiceAddressAsShippingAddress', $checkoutData)) {
            $invoiceAddressIsShippingAddress = $checkoutData['InvoiceAddressAsShippingAddress'] == '1';

            $this->setInvoiceAddress($this->initAddress(Address::TYPE_INVOICE, $checkoutData));
            $this->setShippingAddress($this->initAddress(Address::TYPE_SHIPPING, $checkoutData));
            $this->setInvoiceAddressIsShippingAddress($invoiceAddressIsShippingAddress);

            if ($invoiceAddressIsShippingAddress) {
                $this->getInvoiceAddress()->setIsCheckoutShippingAddress(true);
                $this->getShippingAddress()->setIsCheckoutInvoiceAddress(true);
            }
        }
        return $this;
    }
    
    /**
     * Initializes a single address (either invoice or shipping).
     * 
     * @param string $type         Address type (@see \SilverCart\Model\Customer\Address)
     * @param array  $checkoutData Checkout data
     * 
     * @return \SilverCart\Model\Customer\Address
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.04.2018
     */
    public function initAddress(string $type = Address::TYPE_INVOICE, array $checkoutData = null) : ?Address
    {
        if (is_null($checkoutData)) {
            $checkoutData = $this->getCheckout()->getData();
        }
        $customer   = Customer::currentUser();
        $address    = null;
        $addressKey = "{$type}Address";
        
        if (array_key_exists($addressKey, $checkoutData)
         && is_array($checkoutData[$addressKey])
        ) {
            $addressData = $checkoutData[$addressKey];
            $addressID   = array_key_exists('ID', $addressData) ? $addressData['ID'] : 0;
            $address     = $customer->Addresses()->byID($addressID);
            if (!($address instanceof Address)
             || !$address->exists()
            ) {
                $address = Address::create($addressData);
                if ($type == Address::TYPE_INVOICE) {
                    $address->setIsAnonymousInvoiceAddress(true);
                } else {
                    $address->setIsAnonymousShippingAddress(true);
                }
            }
            if ($type == Address::TYPE_INVOICE) {
                $address->setIsCheckoutInvoiceAddress(true);
            } else {
                $address->setIsCheckoutShippingAddress(true);
            }
        }
        return $address;
    }
}