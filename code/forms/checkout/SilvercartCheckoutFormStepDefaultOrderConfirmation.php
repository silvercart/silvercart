<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 */

/**
 * Shows order confirmation
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 * @author Roland Lehmann <rlehmann@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>,
 *         Sascha Koehler <skoehler@pixeltricks.de>
 * @since 14.05.2013
 * @copyright pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartCheckoutFormStepDefaultOrderConfirmation extends CustomHtmlFormStep {

    /**
     * A list of custom output to add to the content area.
     *
     * @var array
     */
    public static $customOutput = array();

    /**
     * Don't cache this form.
     *
     * @var bool
     */
    protected $excludeFromCache = true;
    
    /**
     * Order.
     *
     * @var SilvercartOrder
     */
    protected $order = null;
    
    /**
     * Payment method chosen for checkout.
     *
     * @var SilvercartPaymentMethod
     */
    protected $paymentMethod = null;

    /**
     * Here we set some preferences.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 31.03.2011
     */
    public function preferences() {
        $this->preferences['stepIsVisible']                    = false;
        $this->preferences['stepTitle']                        = _t('SilvercartCheckoutFormStepDefaultOrderConfirmation.TITLE', 'Order Confirmation');
        $this->preferences['ShowCustomHtmlFormStepNavigation'] = false;
        $this->preferences['createShoppingcartForms']          = false;
        $this->preferences['doJsValidationScrolling']          = false;

        parent::preferences();
    }

    /**
     * Renders the default order confirmation template.
     * Can be called in the last step template of a payment module.
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.2011
     */
    public function defaultOrderConfirmation() {
        return $this->renderWith('SilvercartCheckoutFormStepDefaultOrderConfirmation');
    }

    /**
     * Add a custom output snippet.
     *
     * @param string $output the output to add
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since  2013-05-06
     */
    public static function addCustomOutput($output) {
        self::$customOutput[] = $output;
    }

    /**
     * Returns the combined custom output snippets as string.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since  2013-05-06
     */
    public function CustomOutput() {
        $this->extend('updateCustomOutput');

        $output = '';

        if (count(self::$customOutput) > 0) {
            $output = implode("\n", self::$customOutput);
        }

        return $output;
    }
    
    /**
     * Returns the order.
     * 
     * @return SilvercartOrder
     */
    public function getOrder() {
        if (is_null($this->order)) {
            $this->order = self::init_order($this->getController());
        }
        return $this->order;
    }
    
    /**
     * Returns the payment method chosen for checkout.
     * 
     * @return SilvercartPaymentMethod
     */
    public function getPaymentMethod() {
        if (is_null($this->paymentMethod)) {
            $this->paymentMethod = SilvercartCheckoutFormStepPaymentInit::init_payment_method($this->getController());
        }
        return $this->paymentMethod;
    }
    
    /**
     * Initializes the order.
     * 
     * @param Controller $controller Controller
     * 
     * @return \SilvercartOrder
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2018
     */
    public static function init_order($controller) {
        $order        = null;
        $checkoutData = $controller->getCombinedStepData();
        if (array_key_exists('orderId', $checkoutData)) {
            $order = SilvercartOrder::get()->byID($checkoutData['orderId']);
        }
        return $order;
    }
}
