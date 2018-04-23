<?php

namespace SilverCart\Checkout;

use SilverCart\Admin\Model\Config;
use SilverCart\Checkout\CheckoutStep;
use SilverCart\Forms\Checkout\CheckoutChooseShippingMethodForm;
use SilverCart\Model\Shipment\ShippingMethod;

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
class CheckoutStep3 extends CheckoutStep {
    
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
     * Returns the CheckoutChooseShippingMethodForm.
     * 
     * @return CheckoutChooseShippingMethodForm
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function CheckoutChooseShippingMethodForm() {
        $form = new CheckoutChooseShippingMethodForm($this->getController());
        return $form;
    }

    /**
     * Returns whether to skip this step or not.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.03.2013
     */
    public function SkipShippingStep() {
        if (is_null($this->skipShippingStep)) {
            $shippingMethods = ShippingMethod::getAllowedShippingMethods(null, $this->getController()->getShippingAddress());
            if (Config::SkipShippingStepIfUnique() &&
                $shippingMethods->count() == 1) {
                $this->skipShippingStep = true;
            } else {
                $this->skipShippingStep = false;
            }
        }
        return $this->skipShippingStep;
    }
    
}
