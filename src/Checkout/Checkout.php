<?php

namespace SilverCart\Checkout;

use SilverCart\Admin\Model\Config;
use SilverCart\Checkout\CheckoutStep;
use SilverCart\Checkout\CheckoutStep1;
use SilverCart\Checkout\CheckoutStep2;
use SilverCart\Checkout\CheckoutStep3;
use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Pages\CartPageController;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ViewableData;
use SilverStripe\Control\Controller;
use SilverStripe\Security\Member;

/**
 * Checkout.
 *
 * @package SilverCart
 * @subpackage Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 16.11.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Checkout extends ViewableData {
    
    /**
     * Session key
     * 
     * @var string
     */
    const SESSION_KEY = 'SilverCart.CheckoutData';
    
    /**
     * Session key
     * 
     * @var string
     */
    const FINALIZED_SESSION_KEY = 'SilverCart.FinalizedCheckoutData';

    /**
     * Determines whether to show the shopping cart in checkout step navigation.
     *
     * @var bool
     */
    private static $show_cart_in_checkout_navigation = true;

    /**
     * Checkout from session.
     *
     * @var Checkout
     */
    private static $session_checkout = null;
    
    /**
     * Step list.
     *
     * @var array
     */
    protected $stepList = [];
    
    /**
     * List of completed steps.
     *
     * @var array
     */
    protected $completedSteps = [];

    /**
     * Checkout data.
     *
     * @var array
     */
    protected $data = [];
    
    /**
     * Current checkout step.
     *
     * @var CheckoutStep
     */
    protected $currentStep = null;
    
    /**
     * Current checkout step.
     *
     * @var string
     */
    protected $currentStepName = null;
    
    /**
     * Controller.
     *
     * @var Controller
     */
    protected $controller = null;

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
    public function __construct(Controller $controller = null) {
        parent::__construct();
        if (is_null($controller)) {
            $controller = Controller::curr();
        }
        $this->setController($controller);
    }
    
    /**
     * Returns the checkout step list.
     * 
     * @return array
     */
    public function getDefaultStepList() {
        $defaultStepList = [
            CheckoutStep1::class,
            CheckoutStep2::class,
            CheckoutStep3::class,
            CheckoutStep4::class,
            CheckoutStep5::class,
            CheckoutStep6::class,
        ];
        $this->extend('updateDefaultStepList', $defaultStepList);
        return $defaultStepList;
    }
    
    /**
     * Returns the checkout step list.
     * 
     * @return array
     */
    public function getStepList() {
        if (empty($this->stepList)) {
            $stepList = $this->getDefaultStepList();
            $this->stepList = $stepList;
        }
        $this->extend('updateStepList', $this->stepList);
        return $this->stepList;
    }
    
    /**
     * Sets the step list.
     * 
     * @param array $stepList Step list.
     * 
     * @return $this
     */
    public function setStepList($stepList) {
        $this->stepList = $stepList;
        return $this;
    }
    
    /**
     * Returns if the given step exists.
     * 
     * @param string $step Step name
     * 
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function stepExists($step) {
        $stepExists = false;
        $stepList   = $this->getStepList();
        if (in_array($step, $stepList)) {
            $stepExists = true;
        }
        return $stepExists;
    }
    
    /**
     * Returns whether there is a next step or not.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.04.2018
     */
    public function nextStepExists() {
        $currentStep = $this->getCurrentStep();
        $nextStep    = $this->getStepByIndex($currentStep->StepNumber());
        return !is_null($nextStep);
    }
    
    /**
     * Returns the checkout steps to use in template.
     * 
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function CheckoutSteps() {
        $stepList = $this->getStepList();
        $steps = new ArrayList();
        foreach ($stepList as $stepName) {
            $steps->push(new $stepName($this->getController()));
        }
        return $steps;
    }
    
    /**
     * Returns the current checkout step to use in template.
     * 
     * @return \SilverCart\Checkout\CheckoutStep
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function getCurrentStep() {
        return $this->CurrentStep();
    }

    /**
     * Returns the current checkout step to use in template.
     * 
     * @return \SilverCart\Checkout\CheckoutStep
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function CurrentStep() {
        if (is_null($this->currentStep)) {
            $currentStepName = $this->getCurrentStepName();
            $this->currentStep = new $currentStepName($this->getController());
        }
        return $this->currentStep;
    }
    
    /**
     * Resets the current step.
     * 
     * @param string $currentStepName Optional current step name
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.04.2018
     */
    public function resetCurrentStep($currentStepName = null) {
        $this->currentStep = null;
        if (!is_null($currentStepName)) {
            $this->setCurrentStepName($currentStepName);
        }
    }
    
    /**
     * Returns the step matching the given index to use in template.
     * 
     * @param int $index Index
     * 
     * @return \SilverCart\Checkout\CheckoutStep
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function getStepNameByIndex($index) {
        $stepName = null;
        $stepList = $this->getStepList();
        if (array_key_exists($index, $stepList)) {
            $stepName = $stepList[$index];
        }
        return $stepName;
    }
    
    /**
     * Returns the step matching the given index to use in template.
     * 
     * @param int $index Index
     * 
     * @return \SilverCart\Checkout\CheckoutStep
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function getStepByIndex($index) {
        $stepName = $this->getStepNameByIndex($index);
        $step     = null;
        if (!is_null($stepName)) {
            $step = new $stepName($this->getController());
        }
        return $step;
    }
    
    /**
     * Returns whether to show the cart in checkout step navigation or not.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function ShowCartInCheckoutNavigation() {
        return $this->config()->get('show_cart_in_checkout_navigation');
    }
    
    /**
     * Returns whether the current page controller is a CartPageController.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function CurrentPageIsCartPage() {
        return Controller::curr() instanceof CartPageController;
    }
    
    /**
     * Creates the checkout from session.
     * 
     * @return Checkout
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public static function create_from_session() {
        if (is_null(self::$session_checkout)) {
            self::$session_checkout = new Checkout();
            self::$session_checkout->initFromSession();
        }
        return self::$session_checkout;
    }
    
    /**
     * Clears the checkout data out of session.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public static function clear_session() {
        Tools::Session()->set(self::SESSION_KEY, null);
        Tools::saveSession();
    }
    
    /**
     * Returns whether the given or current logged in Member can access the checkout.
     * 
     * @param Member $member Member to check access for
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function canAccess(Member $member = null) {
        $canAccess = false;
        if (is_null($member)) {
            $member = Customer::currentUser();
        }
        if ($member instanceof Member &&
            $member->exists()) {
            $cart = $member->getCart();
            if ($cart->isFilled() &&
                !(Config::UseMinimumOrderValue() &&
                  Config::MinimumOrderValue()->getAmount() > $cart->getAmountTotalWithoutFees()->getAmount())) {
                $canAccess = true;
            }
        }
        return $canAccess;
    }

    /**
     * Initializes the checkout from session.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function initFromSession() {
        $currentStepList = Tools::Session()->get(self::SESSION_KEY . '.CurrentStepList');
        $completedSteps  = Tools::Session()->get(self::SESSION_KEY . '.CompletedSteps');
        $currentStepName = Tools::Session()->get(self::SESSION_KEY . '.CurrentStepName');
        $sessionData     = Tools::Session()->get(self::SESSION_KEY . '.Data');
        $this->extend('updateInitFromSession', $sessionData);
        if (!is_array($sessionData)) {
            $sessionData = [];
        }
        if (is_null($currentStepName)) {
            $currentStepName = $this->getStepByIndex(0);
        }
        $this->setStepList($currentStepList);
        $this->setCompletedSteps($completedSteps);
        $this->setCurrentStepName($currentStepName);
        $this->setData($sessionData);
    }
    
    /**
     * Saves the current checkout data in session.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function saveInSession() {
        Tools::Session()->set(self::SESSION_KEY . '.CurrentStepList', $this->getStepList());
        Tools::Session()->set(self::SESSION_KEY . '.CompletedSteps', $this->getCompletedSteps());
        Tools::Session()->set(self::SESSION_KEY . '.CurrentStepName', $this->getCurrentStepName());
        Tools::Session()->set(self::SESSION_KEY . '.Data', $this->getData());
        Tools::saveSession();
    }
    
    /**
     * Finalizes the checkout and moves its data to a different session store.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.04.2018
     */
    public function finalize() {
        Tools::Session()->set(self::FINALIZED_SESSION_KEY, Tools::Session()->get(self::SESSION_KEY));
        Tools::saveSession();
        static::clear_session();
        $this->clearData();
    }
    
    /**
     * Returns the finalized checkout data.
     * Contains the checkout data of the last finalized order placement if done within the current
     * session.
     * 
     * @return array
     */
    public function getFinalizedData() {
        return Tools::Session()->get(self::FINALIZED_SESSION_KEY . '.Data');
    }
    
    /**
     * Adds the given data to the checkout data.
     * 
     * @param array $data Data to add
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function addData($data) {
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }
    }
    
    /**
     * Adds the given data key value pair to the checkout data.
     * 
     * @param string $key   Checkout data key
     * @param string $value Checkout data value
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function addDataValue($key, $value) {
        $this->data[$key] = $value;
    }
    
    /**
     * Clears the checkout data.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function clearData() {
        $this->setData([]);
    }
    
    /**
     * Returns the checkout data.
     * 
     * @return array
     */
    public function getData() {
        return $this->data;
    }
    
    /**
     * Returns the value for the given data key out of the checkout data.
     * 
     * @param string $key Checkout data key
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function getDataValue($key) {
        $value = null;
        if (array_key_exists($key, $this->data)) {
            $value = $this->data[$key];
        }
        return $value;
    }
    
    /**
     * Sets the checkout data.
     * 
     * @param array $data Data to set
     * 
     * @return void
     */
    public function setData($data) {
        $this->data = $data;
    }
    
    /**
     * Returns the current step name.
     * 
     * @return string
     */
    public function getCurrentStepName() {
        return $this->currentStepName;
    }

    /**
     * Sets the current step name.
     * 
     * @param string $currentStepName Current step name
     * 
     * @return void
     */
    public function setCurrentStepName($currentStepName) {
        $this->currentStepName = $currentStepName;
    }

    /**
     * Sets the current step name.
     * 
     * @param CheckoutStep $step Current step name
     * 
     * @return void
     */
    public function isCurrentStep(CheckoutStep $step) {
        $isCurrentStep = false;
        if (get_class($step) == $this->getCurrentStepName()) {
            $isCurrentStep = true;
        }
        return $isCurrentStep;
    }
    
    /**
     * Returns the list of completed steps.
     * 
     * @return array
     */
    public function getCompletedSteps() {
        return $this->completedSteps;
    }
    
    /**
     * Sets the list of completed steps.
     * 
     * @param array $completedSteps List of completed steps
     * 
     * @return void
     */
    public function setCompletedSteps($completedSteps) {
        if (!is_array($completedSteps)) {
            $completedSteps = [];
        }
        $this->completedSteps = $completedSteps;
    }
    
    /**
     * Adds a completed step.
     * 
     * @param CheckoutStep $step Step to add
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function addCompletedStep(CheckoutStep $step) {
        $stepList  = $this->getStepList();
        $stepName  = get_class($step);
        $stepIndex = array_search($stepName, $stepList);
        $this->completedSteps[$stepIndex] = $stepName;
    }
    
    /**
     * Returns if the given step is completed.
     * 
     * @param CheckoutStep $step Step instance or name
     * 
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function isCompletedStep($step) {
        $stepName = $step;
        if ($stepName instanceof CheckoutStep) {
            $stepName = get_class($stepName);
        }
        $isCompleted = false;
        $stepList    = $this->getStepList();
        $stepIndex   = array_search($stepName, $stepList);
        if (array_key_exists($stepIndex, $this->completedSteps) &&
            $this->completedSteps[$stepIndex] == $stepName) {
            $isCompleted = true;
        }
        return $isCompleted;
    }
    
    /**
     * Redirects to the current step.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function redirectToCurrentStep() {
        $this->getController()->redirect($this->getController()->Link('step/' . $this->getCurrentStep()->StepNumber()));
    }
    
    /**
     * Returns the controller.
     * 
     * @return Controller
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * Sets the controller.
     * 
     * @param Controller $controller Controller
     * 
     * @return void
     */
    public function setController(Controller $controller) {
        $this->controller = $controller;
    }
    
    public function initStep() {
        $shoppingCart = Customer::currentUser()->getCart();
        /* @var $shoppingCart \SilverCart\Model\Order\ShoppingCart */
        $shoppingCart->setShippingMethodID($this->getDataValue('ShippingMethod'));
        $shoppingCart->setPaymentMethodID($this->getDataValue('PaymentMethod'));
        $this->getCurrentStep()->init();
    }
    
}