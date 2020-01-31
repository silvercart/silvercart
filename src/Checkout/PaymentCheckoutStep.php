<?php

namespace SilverCart\Checkout;

use SilverCart\Dev\Tools;
use SilverCart\Model\Payment\PaymentMethod;
use SilverStripe\Control\Director;
use SilverStripe\Security\Security;

/**
 * PaymentCheckoutStep.
 * Trait to provide payment method related methods to a checkout step.
 *
 * @package SilverCart
 * @subpackage Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 12.04.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
trait PaymentCheckoutStep
{
    /**
     * Chosen payment method.
     *
     * @var \SilverCart\Model\Payment\PaymentMethod
     */
    protected $paymentMethod = null;

    /**
     * Returns the chosen payment method.
     * 
     * @return \SilverCart\Model\Payment\PaymentMethod
     */
    public function getPaymentMethod() : ?PaymentMethod
    {
        return $this->paymentMethod;
    }

    /**
     * Sets the chosen payment method.
     * 
     * @param \SilverCart\Model\Payment\PaymentMethod $paymentMethod Payment method
     * 
     * @return \SilverCart\Checkout\CheckoutStep
     */
    public function setPaymentMethod(PaymentMethod $paymentMethod) : CheckoutStep
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * Initializes the payment method by using the checkout data.
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return \SilverCart\Checkout\CheckoutStep
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.04.2018
     */
    public function initPaymentMethod($checkoutData = null) : CheckoutStep
    {
        if (is_null($checkoutData)) {
            $checkoutData = $this->getCheckout()->getData();
        }
        if (array_key_exists('PaymentMethod', $checkoutData)) {
            $customer        = Security::getCurrentUser();
            $controller      = $this->getController();
            $currentStep     = $controller->getCheckout()->getCurrentStep();
            $paymentMethodID = $checkoutData['PaymentMethod'];
            $paymentMethod   = PaymentMethod::get()->byID($paymentMethodID);
            /* @var $paymentMethod PaymentMethod */
            if ($paymentMethod instanceof PaymentMethod) {
                $paymentMethod->setController($controller);
                $paymentMethod->setCancelLink(Director::absoluteURL($controller->Link()) . 'step/4');
                $paymentMethod->setReturnLink(Director::absoluteURL($controller->Link()) . 'step/' . $currentStep->StepNumber());
                $paymentMethod->setCustomerDetailsByCheckoutData($checkoutData);
                $paymentMethod->setInvoiceAddressByCheckoutData($checkoutData);
                $paymentMethod->setShippingAddressByCheckoutData($checkoutData);
                $paymentMethod->setShoppingCart($customer->getCart());
                $this->setPaymentMethod($paymentMethod);
            }
        }
        return $this;
    }
    
    /**
     * Resets the payment specific progress information.
     * 
     * @return \SilverCart\Checkout\CheckoutStep
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.10.2018
     */
    public function resetPaymentProgress() : CheckoutStep
    {
        $this->getPaymentMethod()->resetProgress();
        return $this;
    }
}