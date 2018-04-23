<?php

namespace SilverCart\Checkout;

use SilverCart\Checkout\CheckoutStep;
use SilverCart\Forms\RegisterRegularCustomerForm;
use SilverCart\Forms\Checkout\CheckoutLoginForm;
use SilverCart\Forms\Checkout\CheckoutNewCustomerForm;
use SilverCart\Model\Customer\Customer;
use SilverStripe\Control\Controller;
use SilverStripe\Security\Member;

/**
 * Checkout step 1.
 * Provides the options to login or register a new account.
 *
 * @package SilverCart
 * @subpackage Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 16.11.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CheckoutStep1 extends CheckoutStep {
    
    /**
     * List of allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = [
        'CheckoutLoginForm',
        'CheckoutNewCustomerForm',
        'RegisterRegularCustomerForm',
    ];

    /**
     * Constructor.
     * 
     * @param Controller $controller Controller
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function __construct(Controller $controller) {
        parent::__construct($controller);
        $registeredCustomer = Customer::currentRegisteredCustomer();
        if ($registeredCustomer instanceof Member &&
            $registeredCustomer->exists()) {
            CheckoutStep1::config()->set('is_visible', false);
        }
    }
    
    /**
     * Custom checkout step processor.
     * Will be called for invisible steps.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.11.2017
     */
    public function process() {
        $registeredCustomer = Customer::currentRegisteredCustomer();
        if ($registeredCustomer instanceof Member &&
            $registeredCustomer->exists()) {
            
        }
    }

    /**
     * Returns the CheckoutLoginForm.
     * 
     * @return CheckoutLoginForm
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function CheckoutLoginForm() {
        $form = new CheckoutLoginForm($this->getController());
        return $form;
    }
    
    /**
     * Returns the CheckoutNewCustomerForm.
     * 
     * @return CheckoutNewCustomerForm
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function CheckoutNewCustomerForm() {
        $form = new CheckoutNewCustomerForm($this->getController());
        return $form;
    }
    
    /**
     * Returns the RegisterRegularCustomerForm.
     * 
     * @return RegisterRegularCustomerForm
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function RegisterRegularCustomerForm() {
        $form = new RegisterRegularCustomerForm($this->getController());
        $form->setBackLink($this->getController()->Link());
        return $form;
    }
    
    /**
     * Returns whether to show the RegisterRegularCustomerForm or not.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.11.2017
     */
    public function ShowRegistrationForm() {
        return $this->getCheckout()->getDataValue('ShowRegistrationForm') === true;
    }
    
}
