<?php

namespace SilverCart\Checkout;

use SilverCart\Admin\Model\Config;
use SilverCart\Checkout\CheckoutStep;
use SilverCart\Forms\Checkout\CheckoutChooseShippingMethodForm;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Shipment\ShippingMethod;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\Member;

/**
 * Checkout step 3.
 * Checkout step to choose the shipping method.
 *
 * @package SilverCart
 * @subpackage Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 16.11.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CheckoutStep3 extends CheckoutStep
{
    /**
     * List of allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = [
        'CheckoutChooseShippingMethodForm',
    ];
    /**
     * Determines whether to skip this step or not.
     *
     * @var bool
     */
    protected $skipShippingStep = null;
    
    /**
     * Custom checkout step processor.
     * Will be called for invisible steps.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.03.2019
     */
    public function process() : void
    {
        if (!$this->IsVisible()) {
            $shippingMethod = ShippingMethod::getAllowedShippingMethods(null, $this->getController()->getShippingAddress())->first();
            if ($shippingMethod instanceof ShippingMethod) {
                $this->getCheckout()->addDataValue('ShippingMethod', $shippingMethod->ID);
            }
        }
    }
    
    /**
     * Returns the CheckoutChooseShippingMethodForm.
     * 
     * @return CheckoutChooseShippingMethodForm
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function CheckoutChooseShippingMethodForm() : CheckoutChooseShippingMethodForm
    {
        return CheckoutChooseShippingMethodForm::create($this->getController());
    }
    
    /**
     * Returns whether this step is visible.
     * 
     * @return bool
     */
    public function IsVisible() : bool
    {
        $isVisible = parent::IsVisible();
        if ($this->SkipShippingStep()) {
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
     * @since 11.03.2013
     */
    public function SkipShippingStep() : bool
    {
        if (is_null($this->skipShippingStep)) {
            $shippingMethods = ShippingMethod::getAllowedShippingMethods(null, $this->getShippingAddress());
            if (Config::SkipShippingStepIfUnique()
             && $shippingMethods->count() == 1
            ) {
                $this->skipShippingStep = true;
            } else {
                $this->skipShippingStep = false;
            }
        }
        return $this->skipShippingStep;
    }
    
    /**
     * Returns the context shipping address.
     * 
     * @return Address|null
     */
    public function getShippingAddress() : ?Address
    {
        $shippingAddress = false;
        $ctrl            = $this->getController();
        if ($ctrl->hasMethod('getShippingAddress')) {
            $shippingAddress = $ctrl->getShippingAddress();
        }
        if ($shippingAddress === false) {
            $shippingAddress = null;
            $customer        = Customer::currentRegisteredCustomer();
            if ($customer instanceof Member) {
                $countryMap = $customer->Addresses()->map('ID', 'CountryID')->toArray();
                $uniqueMap  = array_unique($countryMap);
                if (count($uniqueMap) > 1) {
                    $countryID = $customer->ShippingAddress()->CountryID;
                } else {
                    $countryID = array_pop($uniqueMap);
                }
                $shippingAddress = Address::create(['CountryID' => $countryID]);
            }
        }
        return $shippingAddress;
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