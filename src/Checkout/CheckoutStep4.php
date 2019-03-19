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
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\SS_List;
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
class CheckoutStep4 extends CheckoutStep
{
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
     * Custom checkout step processor.
     * Will be called for invisible steps.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.03.2019
     */
    public function process() {
        if (!$this->IsVisible()) {
            $paymentMethod = $this->getAllowedPaymentMethods()->first();
            if ($paymentMethod instanceof PaymentMethod) {
                $this->getCheckout()->addDataValue('PaymentMethod', $paymentMethod->ID);
            }
        }
    }
    
    /**
     * Returns the CheckoutChoosePaymentMethodForm.
     * 
     * @return CheckoutChoosePaymentMethodForm
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function CheckoutChoosePaymentMethodForm() : ?CheckoutChoosePaymentMethodForm
    {
        $form = null;
        $paymentMethodID = $this->getController()->getRequest()->postVar('PaymentMethod');
        if (is_numeric($paymentMethodID)) {
            $paymentMethod = PaymentMethod::get()->byID($paymentMethodID);
            if ($paymentMethod instanceof PaymentMethod
             && $paymentMethod->exists()
            ) {
                $form = $paymentMethod->CheckoutChoosePaymentMethodForm();
            }
        }
        return $form;
    }
    
    /**
     * Returns the allowed payment methods.
     *
     * @return ArrayList
     */
    public function getAllowedPaymentMethods() : ArrayList
    {
        if (is_null($this->allowedPaymentMethods)) {
            $allowedPaymentMethods = ArrayList::create();
            $shippingAddressData   = (array) $this->getCheckout()->getDataValue('ShippingAddress');
            if (array_key_exists('CountryID', $shippingAddressData)
             && Customer::currentUser() instanceof Member
             && is_numeric($shippingAddressData['CountryID'])
            ) {
                $shippingCountry = Country::get()->byID($shippingAddressData['CountryID']);
                if ($shippingCountry instanceof Country) {
                    $allowedPaymentMethods = PaymentMethod::getAllowedPaymentMethodsFor($shippingCountry, Customer::currentUser()->getCart());
                    if (!($allowedPaymentMethods instanceof ArrayList)) {
                        $allowedPaymentMethods = ArrayList::create();
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
     * @param SS_List $allowedPaymentMethods Allowed payment method
     * 
     * @return CheckoutStep4
     */
    public function setAllowedPaymentMethods(SS_List $allowedPaymentMethods) : CheckoutStep4
    {
        $this->allowedPaymentMethods = $allowedPaymentMethods;
        return $this;
    }
    
    /**
     * Returns the active payment methods.
     *
     * @return SS_List
     */
    public function getActivePaymentMethods() : SS_List
    {
        if (is_null($this->activePaymentMethods)) {
            $activePaymentMethods  = PaymentMethod::getActivePaymentMethods();
            if (!($activePaymentMethods instanceof DataList)) {
                $activePaymentMethods = ArrayList::create();
            }
            $this->setActivePaymentMethods($activePaymentMethods);
        }
        return $this->activePaymentMethods;
    }

    /**
     * Sets the active payment methods.
     *
     * @param SS_List $activePaymentMethods Active payment method
     * 
     * @return CheckoutStep4
     */
    public function setActivePaymentMethods(SS_List $activePaymentMethods) : CheckoutStep4
    {
        $this->activePaymentMethods = $activePaymentMethods;
        return $this;
    }
    
    /**
     * Returns whether this step is visible.
     * 
     * @return bool
     */
    public function IsVisible() : bool
    {
        $isVisible = parent::IsVisible();
        if ($this->SkipPaymentStep()) {
            $isVisible = false;
        }
        return $isVisible;
    }

    /**
     * Returns whether to skip this step or not.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.11.2017
     */
    public function SkipPaymentStep() : bool
    {
        if (is_null($this->skipPaymentStep)) {
            $this->skipPaymentStep = false;
            if ((Config::SkipPaymentStepIfUnique()
              && $this->getAllowedPaymentMethods()->count() == 1)
             || (Config::SkipPaymentStepIfUnique()
              && $this->getActivePaymentMethods()->count() == 1)
            ) {
                $this->skipPaymentStep = true;
            }
        }
        return $this->skipPaymentStep;
    }
    
    /**
     * Returns the rendered step summary.
     * 
     * @return DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.03.2019
     */
    public function StepSummary() : DBHTMLText
    {
        return $this->customise([
            'InvoiceAddress'  => $this->getController()->getInvoiceAddress(),
            'ShippingAddress' => $this->getController()->getShippingAddress(),
        ])->renderWith(self::class . '_Summary');
    }
}