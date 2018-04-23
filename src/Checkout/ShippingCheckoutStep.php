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
trait ShippingCheckoutStep {
    
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
    public function getShippingMethod() {
        return $this->shippingMethod;
    }

    /**
     * Sets the chosen shipping method.
     * 
     * @param \SilverCart\Model\Shipment\ShippingMethod $shippingMethod Payment method
     * 
     * @return \SilverCart\Checkout\ShippingCheckoutStep
     */
    public function setShippingMethod(ShippingMethod $shippingMethod) {
        $this->shippingMethod = $shippingMethod;
        return $this;
    }

    /**
     * Initializes the shipping method by using the checkout data.
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return \SilverCart\Checkout\ShippingCheckoutStep
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.04.2018
     */
    public function initShippingMethod($checkoutData = null) {
        if (is_null($checkoutData)) {
            $checkoutData = $this->getCheckout()->getData();
        }
        $shippingMethodID = $checkoutData['ShippingMethod'];
        $shippingMethod   = ShippingMethod::get()->byID($shippingMethodID);
        $this->setShippingMethod($shippingMethod);
        return $this;
    }
    
}
