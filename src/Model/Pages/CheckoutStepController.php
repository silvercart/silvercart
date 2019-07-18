<?php

namespace SilverCart\Model\Pages;

use SilverCart\Admin\Model\Config;
use SilverCart\Checkout\Checkout;
use SilverCart\Checkout\CheckoutStep3;
use SilverCart\Checkout\CheckoutStep4;
use SilverCart\Dev\Tools;
use SilverCart\Forms\AddAddressForm;
use SilverCart\Forms\EditAddressForm;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Order\ShoppingCart;
use SilverCart\Model\Pages\AddressHolderController;
use SilverCart\Model\Payment\PaymentMethod;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\HiddenField;

/**
 * Checkout step page controller.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CheckoutStepController extends \PageController {
    
    /**
     * Allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = [
        'addNewAddress',
        'editAddress',
        'deleteAddress',
        'step',
        'thanks',
        'welcome',
        'AddAddressForm',
        'EditAddressForm',
    ];
    /**
     * Allowed thanks actions.
     *
     * @var array
     */
    private static $allowed_thanks_actions = [];

    /**
     * Checkout.
     *
     * @var Checkout
     */
    protected $checkout = null;
    
    /**
     * Initializes the controller.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2018
     */
    protected function init() {
        if (Config::EnableSSL()) {
            Director::forceSSL();
        }
        parent::init();
        if (Tools::is_cms_preview()) {
            return;
        }
        $checkout = $this->getCheckout();
        if ($checkout->canAccess()) {
            $stepList = $checkout->getStepList();
            $stepName = $checkout->getCurrentStepName();
            if (!$checkout->stepExists($stepName)) {
                $checkout->setCurrentStepName($stepList[0]);
                $checkout->saveInSession();
            }
            $currentStepNumber = array_search($stepName, $stepList) + 1;
            $action            = $this->getRequest()->param('Action');
            if (empty($action)
             && !$this->redirectedTo()
            ) {
                $this->redirect($this->Link('step/' . $currentStepNumber));
            }
        } elseif ($this->getRequest()->param('Action') != 'thanks'
               && !in_array($this->getRequest()->param('Action'), $this->config()->allowed_thanks_actions)
               && !$this->redirectedTo()
        ) {
            $cartPage = Tools::PageByIdentifierCode('SilvercartCartPage');
            $this->redirect($cartPage->Link());
        }
    }
    
    /**
     * Adds the current checkout step actions to the allowed actions.
     * 
     * @param string $limitToClass Class name to limit actions to
     * 
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.11.2017
     */
    public function allowedActions($limitToClass = null) {
        $currentStep    = $this->getCheckout()->getCurrentStep();
        $allowedActions = parent::allowedActions($limitToClass);
        if (is_null($limitToClass) ||
            $limitToClass == get_class($this)) {
            if (!is_array($allowedActions)) {
                $allowedActions = array();
            }
            $stepActions = $currentStep->allowedActions();
            if (is_array($stepActions)) {
                if (array_key_exists('*', $stepActions)) {
                    throw new InvalidArgumentException("Invalid allowed_action '*'");
                }

                // convert all keys and values to lowercase to
                // allow for easier comparison, unless it is a permission code
                $stepActions = array_change_key_case($stepActions, CASE_LOWER);

                foreach ($stepActions as $key => $value) {
                    if (is_numeric($key)) {
                        $stepActions[$key] = strtolower($value);
                    }
                }
            }
            $allowedActions = array_merge(
                    $allowedActions,
                    $stepActions
            );
        }
        return $allowedActions;
    }
    
    /**
     * Returns whether this controller or the current checkout step has the given action.
     * 
     * @param string $action Action
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.11.2017
     */
    public function hasAction($action) {
        $hasAction = parent::hasAction($action);
        if (!$hasAction) {
            $hasAction = $this->getCheckout()->getCurrentStep()->hasAction($action);
        }
        return $hasAction;
    }
    
    /**
     * Controller's default action handler.  It will call the method named in "$Action", if that method
     * exists. If "$Action" isn't given, it will use "index" as a default.
     *
     * @param HTTPRequest $request Request
     * @param string      $action  Action
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText | \SilverStripe\Control\HTTPResponse
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.11.2017
     */
    protected function handleAction($request, $action) {
        $currentStep = $this->getCheckout()->getCurrentStep();
        if ($currentStep->hasAction($action)) {
            return $currentStep->$action($request);
        }
        return parent::handleAction($request, $action);
    }
    
    /**
     * Action to handle the checkout step.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.11.2017
     */
    public function step(HTTPRequest $request) {
        $stepNumber = (int) $request->param('ID') - 1;
        $checkout   = $this->getCheckout();
        $stepList   = $checkout->getStepList();
        if (!array_key_exists($stepNumber, $stepList)) {
            if (!$this->redirectedTo()) {
                $this->redirect($this->Link('step/1'));
            }
            return;
        }
        $currentStepName = $stepList[$stepNumber];
        $currentStep     = new $currentStepName($this);
        if ($currentStep->canAccess()) {
            $checkout->resetCurrentStep($currentStepName);
            $checkout->setCurrentStep($currentStep);
            $checkout->saveInSession();
            $checkout->initStep();
            if ($currentStep->IsVisible()) {
                return $this->render();
            } else {
                $nextStepIndex = $stepNumber + 2;
                $currentStep->process();
                $currentStep->complete();
                if ($checkout->nextStepExists()) {
                    if (!$this->redirectedTo()) {
                        $this->redirect($this->Link('step/' . $nextStepIndex));
                    }
                } else {
                    return $this->render();
                }
            }
        } else {
            $previousStepIndex = $stepNumber;
            if (!$this->redirectedTo()) {
                $this->redirect($this->Link('step/' . $previousStepIndex));
            }
        }
    }
    
    /**
     * Action to show the order thanks page after placing an order.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.09.2018
     */
    public function thanks(HTTPRequest $request) {
        $checkout     = $this->getCheckout();
        $checkoutData = $checkout->getFinalizedData();
        if (!empty($checkoutData)
         && array_key_exists('Order', $checkoutData)
        ) {
            $paymentMethod = PaymentMethod::get()->byID($checkoutData['PaymentMethod']);
            $order         = Order::get()->byID($checkoutData['Order']);
            /* @var $paymentMethod PaymentMethod */
            /* @var $order Order */
            $this->extend('onBeforeRenderThanks', $order, $paymentMethod, $checkoutData);
            return $this->customise([
                'PaymentConfirmationText' => $paymentMethod->processConfirmationText($order, $checkoutData),
                'CustomersOrder'          => $order,
            ])->render();
        }
        if (!$this->redirectedTo()) {
            $this->redirect($this->Link());
        }
    }
    
    /**
     * Action to delete an address. Checks, whether the given address is related
     * to the logged in customer and deletes it.
     *
     * @param HTTPRequest $request The given request
     * @param string      $context specifies the context from the action to adjust redirect behaviour
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.07.2011
     */
    public function deleteAddress(HTTPRequest $request, $context = 'CheckoutStep') {
        $silvercartAddressHolder = new AddressHolderController();
        $silvercartAddressHolder->deleteAddress($request, $context);
        $this->redirectBack();
    }
    
    /**
     * Returns the AddAddressForm.
     * 
     * @return AddAddressForm
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.2018
     */
    public function AddAddressForm() {
        $form = new AddAddressForm($this);
        $form->Fields()->push(new HiddenField('redirect', '', $this->Link()));
        return $form;
    }
    
    /**
     * Returns the EditAddressForm.
     * 
     * @return EditAddressForm
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.2018
     */
    public function EditAddressForm() {
        $addressID = $this->getRequest()->postVar('AddressID');
        if (is_null($addressID)) {
            $addressID = $this->getRequest()->param('ID');
        }
        if (!is_numeric($addressID)) {
            return;
        }
        $address = Address::get()->byID($addressID);
        if (!($address instanceof Address) ||
            !$address->exists()) {
            return;
        }
        $form = new EditAddressForm($address, $this);
        $form->Fields()->push(new HiddenField('redirect', '', $this->Link()));
        return $form;
    }

    /**
     * Returns the checkout.
     * 
     * @return Checkout
     */
    public function getCheckout() {
        if (is_null($this->checkout)) {
            $this->checkout = Checkout::create_from_session($this);
        }
        if (get_class($this->checkout->getController()) != get_class($this)) {
            $this->checkout->setController($this);
        }
        return $this->checkout;
    }
    
    /**
     * Returns the invoice address set in checkout
     *
     * @return Address 
     */
    public function getInvoiceAddress() {
        return $this->getAddress('InvoiceAddress');
    }
    
    /**
     * Returns the shipping address set in checkout
     *
     * @return Address
     */
    public function getShippingAddress() {
        return $this->getAddress('ShippingAddress');
    }
    
    /**
     * Returns the shipping or invoice address set in checkout
     *
     * @param string $type The type to use
     * 
     * @return \SilverCart\Model\Customer\Address
     */
    public function getAddress($type) {
        $address  = false;
        $checkout = $this->getCheckout();
        /* @var $checkout \SilverCart\Checkout\Checkout */
        $addressData = $checkout->getDataValue($type);
        if (is_array($addressData) &&
            !empty($addressData)) {
            if (!array_key_exists('CountryID', $addressData) &&
                array_key_exists('Country', $addressData)) {
                $addressData['CountryID'] = $addressData['Country'];
            }
            $address = new Address($addressData);
            $address->CountryID = $addressData['CountryID'];
        }
        return $address;
    }
    
    /**
     * Checks whether the current step is the payment step
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.11.2017
     */
    public function currentStepIsPaymentStep() {
        return $this->getCheckout()->getCurrentStep() instanceof CheckoutStep4;
    }
    
    /**
     * Returns the address step number.
     * 
     * @return int
     */
    public function getAddressStepNumber() {
        $stepNumber = 2;
        return $stepNumber;
    }
    
    /**
     * Returns the shippment step number.
     * 
     * @return int
     */
    public function getShipmentStepNumber() {
        $stepNumber = 3;
        return $stepNumber;
    }
    
    /**
     * Returns the payment step number.
     * 
     * @return int
     */
    public function getPaymentStepNumber() {
        $stepNumber = 4;
        return $stepNumber;
    }
    
    /**
     * Returns the payment step number.
     * 
     * @return int
     */
    public function getLastStepNumber() {
        $stepNumber = 5;
        return $stepNumber;
    }
    
    /**
     * Returns the address step number.
     * 
     * @return int
     */
    public function getAddressStepLink() {
        return $this->Link('step/' . $this->getAddressStepNumber());
    }
    
    /**
     * Returns the shippment step number.
     * 
     * @return int
     */
    public function getShipmentStepLink() {
        return $this->Link('step/' . $this->getShipmentStepNumber());
    }
    
    /**
     * Returns the payment step number.
     * 
     * @return int
     */
    public function getPaymentStepLink() {
        return $this->Link('step/' . $this->getPaymentStepNumber());
    }
    
    /**
     * Returns the payment step number.
     * 
     * @return int
     */
    public function getLastStepLink() {
        return $this->Link('step/' . $this->getLastStepNumber());
    }
    
    /**
     * Returns whether to skip payment step or not.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.11.2017
     */
    public function SkipPaymentStep() {
        $paymentStep = new CheckoutStep4($this);
        return $paymentStep->SkipPaymentStep();
    }
    
    /**
     * Returns whether to skip shipping step or not.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.11.2017
     */
    public function SkipShippingStep() {
        $shippingStep = new CheckoutStep3($this);
        return $shippingStep->SkipShippingStep();
    }

    /**
     * Indicates wether ui elements for removing items and altering their
     * quantity should be shown in the shopping cart templates.
     *
     * During the checkout process the user may not be able to alter the
     * shopping cart.
     *
     * @return boolean false
     */
    public function getEditableShoppingCart() {
        return false;
    }
    
    /**
     * Returns whether the current customer can checkout or not.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.2018
     */
    public function canCheckout() {
        $canCheckout = true;
        $customer    = Customer::currentUser();
        if ($customer instanceof \SilverStripe\Security\Member &&
            $customer->exists()) {
            $cart = $customer->getCart();
            if (Config::UseMinimumOrderValue() &&
                is_object(Config::MinimumOrderValue()) &&
                Config::MinimumOrderValue()->getAmount() > $cart->getAmountTotalWithoutFees()->getAmount()) {
                $canCheckout = false;
            }
        } else {
            $canCheckout = false;
        }
        $this->extend('canCheckout', $canCheckout, $customer);
        return $canCheckout;
    }
    
    /**
     * Returns a checkout error message.
     * 
     * @return string
     */
    public function getCheckoutErrorMessage() {
        $errorMessage = '';
        $customer     = Customer::currentUser();
        if ($customer instanceof \SilverStripe\Security\Member &&
            $customer->exists()) {
            $cart = $customer->getCart();
            if (Config::UseMinimumOrderValue() &&
                is_object(Config::MinimumOrderValue()) &&
                Config::MinimumOrderValue()->getAmount() > $cart->getAmountTotalWithoutFees()->getAmount()) {
                $errorMessage = _t(ShoppingCart::class . '.ERROR_MINIMUMORDERVALUE_NOT_REACHED',
                        'The minimum order value is {amount}',
                        [
                            'amount' => Config::MinimumOrderValue()->Nice(),
                        ]
                );
            }
        }
        return $errorMessage;
    }
    
}