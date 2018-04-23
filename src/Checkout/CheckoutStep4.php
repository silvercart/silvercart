<?php

namespace SilverCart\Checkout;

use SilverCart\Admin\Model\Config;
use SilverCart\Checkout\CheckoutStep;
use SilverCart\Forms\Checkout\CheckoutChoosePaymentMethodForm;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Payment\PaymentMethod;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\Security\Member;

/**
 * Checkout step 4.
 * Checkout step to choose the payment method.
 *
 * @package SilverCart
 * @subpackage Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 16.11.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CheckoutStep4 extends CheckoutStep {
    
    /**
     * List of allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = [
        'CheckoutChoosePaymentMethodForm',
    ];
    
    /**
     * List of allowed payment methods
     *
     * @var ArrayList 
     */
    protected $allowedPaymentMethods = null;
    
    /**
     * List of active payment methods
     *
     * @var DataList 
     */
    protected $activePaymentMethods = null;
    
    /**
     * Determines whether to skip this step or not.
     *
     * @var bool
     */
    protected $skipPaymentStep = null;
    
    /**
     * Returns the CheckoutChoosePaymentMethodForm.
     * 
     * @return CheckoutChoosePaymentMethodForm
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function CheckoutChoosePaymentMethodForm() {
        $form = null;
        $paymentMethodID = $this->getController()->getRequest()->postVar('PaymentMethod');
        if (is_numeric($paymentMethodID)) {
            $paymentMethod = PaymentMethod::get()->byID($paymentMethodID);
            if ($paymentMethod instanceof PaymentMethod &&
                $paymentMethod->exists()) {
                $form = $paymentMethod->CheckoutChoosePaymentMethodForm();
            }
        }
        return $form;
    }
    
    /**
     * Returns the allowed payment methods.
     *
     * @return ArrayList|Boolean
     */
    public function getAllowedPaymentMethods() {
        if (is_null($this->allowedPaymentMethods)) {
            $allowedPaymentMethods = new ArrayList();
            $shippingAddressData   = $this->getCheckout()->getDataValue('ShippingAddress');
            if (array_key_exists('CountryID', $shippingAddressData) &&
                Customer::currentUser() instanceof Member &&
                is_numeric($shippingAddressData['CountryID'])) {
                $shippingCountry = Country::get()->byID($shippingAddressData['CountryID']);
                if ($shippingCountry instanceof Country) {
                    $allowedPaymentMethods = PaymentMethod::getAllowedPaymentMethodsFor($shippingCountry, Customer::currentUser()->getCart());
                    if (!($allowedPaymentMethods instanceof ArrayList)) {
                        $allowedPaymentMethods = new ArrayList();
                    }
                }
            }
            $this->setAllowedPaymentMethods($allowedPaymentMethods);
        }
        return $this->allowedPaymentMethods;
    }

    /**
     * Sets the allowed payment methods.
     *
     * @param \SilverStripe\ORM\SS_List $allowedPaymentMethods Allowed payment method
     * 
     * @return void
     */
    public function setAllowedPaymentMethods($allowedPaymentMethods) {
        $this->allowedPaymentMethods = $allowedPaymentMethods;
    }
    
    /**
     * Returns the active payment methods.
     *
     * @return DataList|ArrayList
     */
    public function getActivePaymentMethods() {
        if (is_null($this->activePaymentMethods)) {
            $activePaymentMethods  = PaymentMethod::getActivePaymentMethods();
            if (!($activePaymentMethods instanceof DataList)) {
                $activePaymentMethods = new ArrayList();
            }
            $this->setActivePaymentMethods($activePaymentMethods);
        }
        return $this->activePaymentMethods;
    }

    /**
     * Sets the active payment methods.
     *
     * @param DataList $activePaymentMethods Active payment method
     * 
     * @return void
     */
    public function setActivePaymentMethods($activePaymentMethods) {
        $this->activePaymentMethods = $activePaymentMethods;
    }

    /**
     * Returns whether to skip this step or not.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.11.2017
     */
    public function SkipPaymentStep() {
        if (is_null($this->skipPaymentStep)) {
            $this->skipPaymentStep = false;
            if ((Config::SkipPaymentStepIfUnique() &&
                 $this->getAllowedPaymentMethods()->count() == 1) ||
                (Config::SkipPaymentStepIfUnique() &&
                 $this->getActivePaymentMethods()->count() == 1)) {
                
                $this->skipPaymentStep = true;
            }
        }
        return $this->skipPaymentStep;
    }
    
}
