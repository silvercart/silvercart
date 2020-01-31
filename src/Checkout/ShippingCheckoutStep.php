<?php

namespace SilverCart\Checkout;

use SilverCart\Model\Shipment\ShippingMethod;

/**
 * ShippingCheckoutStep.
 * Trait to provide shipping method related methods to a checkout step.
 *
 * @package SilverCart
 * @subpackage Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 12.04.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
trait ShippingCheckoutStep
{
    /**
     * Chosen shipping method.
     *
     * @var \SilverCart\Model\Shipment\ShippingMethod
     */
    protected $shippingMethod = null;

    /**
     * Returns the chosen shipping method.
     * 
     * @return \SilverCart\Model\Shipment\ShippingMethod
     */
    public function getShippingMethod() : ?ShippingMethod
    {
        return $this->shippingMethod;
    }

    /**
     * Sets the chosen shipping method.
     * 
     * @param \SilverCart\Model\Shipment\ShippingMethod $shippingMethod Payment method
     * 
     * @return \SilverCart\Checkout\CheckoutStep
     */
    public function setShippingMethod(ShippingMethod $shippingMethod) : CheckoutStep
    {
        $this->shippingMethod = $shippingMethod;
        return $this;
    }

    /**
     * Initializes the shipping method by using the checkout data.
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return \SilverCart\Checkout\CheckoutStep
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.04.2018
     */
    public function initShippingMethod($checkoutData = null) : CheckoutStep
    {
        if (is_null($checkoutData)) {
            $checkoutData = $this->getCheckout()->getData();
        }
        if (array_key_exists('ShippingMethod', $checkoutData)) {
            $shippingMethodID = $checkoutData['ShippingMethod'];
            $shippingMethod   = ShippingMethod::get()->byID($shippingMethodID);
            /* @var $shippingMethod ShippingMethod */
            if ($shippingMethod instanceof ShippingMethod) {
                $this->setShippingMethod($shippingMethod);
            }
        }
        return $this;
    }
}