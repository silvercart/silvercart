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
class Checkout extends ViewableData
{
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
     * Determines whether to allow an anonymous checkout (without customer 
     * registration).
     *
     * @var bool
     */
    private static $allow_anonymous_checkout = true;
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
    public function __construct(Controller $controller = null)
    {
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
    public function getDefaultStepList() : array
    {
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
    public function getStepList() : array
    {
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
    public function setStepList(array $stepList = null) : Checkout
    {
        $this->stepList = (array) $stepList;
        return $this;
    }
    
    /**
     * Returns if the given step exists.
     * 
     * @param string $step Step name
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function stepExists(string $step) : bool
    {
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
    public function nextStepExists() :bool
    {
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
    public function CheckoutSteps() : ArrayList
    {
        $stepList = $this->getStepList();
        $steps    = ArrayList::create();
        foreach ($stepList as $stepName) {
            $steps->push(new $stepName($this->getController()));
        }
        return $steps;
    }
    
    /**
     * Returns the visible checkout steps to use in template.
     * 
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2018
     */
    public function VisibleCheckoutSteps() : ArrayList
    {
        $allSteps     = $this->CheckoutSteps();
        $visibleSteps = ArrayList::create();
        foreach ($allSteps as $step) {
            if ($step->IsVisible()) {
                $visibleSteps->push($step);
            }
        }
        return $visibleSteps;
    }
    
    /**
     * Returns the count of visible checkout steps including the shopping cart.
     * 
     * @return int
     */
    public function getVisibleCheckoutStepWithCartCount() : int
    {
        return $this->VisibleCheckoutSteps()->count() + 1;
    }
    
    /**
     * Returns the current step progress as a percent value (e.g. step 3 of 5
     * results in 60[%]).
     * If $isCartPage is set to true the current step number will be set to 1.
     * 
     * @param bool $isCartPage Is the current page the cart page?
     * 
     * @return float
     */
    public function getStepProgressPercentage(bool $isCartPage = false) : float
    {
        $currrentStepNumber = 1;
        if (!$isCartPage) {
            $currrentStepNumber = $this->getCurrentStep()->VisibleStepNumber();
        }
        $totalStepCount = $this->getVisibleCheckoutStepWithCartCount();
        $percentage     = $currrentStepNumber / ($totalStepCount / 100);
        return $percentage;
    }
    
    /**
     * Returns the current checkout step to use in template.
     * 
     * @return CheckoutStep
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function getCurrentStep() : CheckoutStep
    {
        return $this->CurrentStep();
    }

    /**
     * Returns the current checkout step to use in template.
     * 
     * @return CheckoutStep
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function CurrentStep() : CheckoutStep
    {
        if (is_null($this->currentStep)) {
            $currentStepName   = $this->getCurrentStepName();
            $this->currentStep = new $currentStepName($this->getController());
        }
        return $this->currentStep;
    }
    
    /**
     * Returns the current checkout step to use in template.
     * 
     * @param CheckoutStep $currentStep Current step
     * 
     * @return $this
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function setCurrentStep(CheckoutStep $currentStep) : Checkout
    {
        $this->currentStep = $currentStep;
        return $this;
    }
    
    /**
     * Resets the current step.
     * 
     * @param string $currentStepName Optional current step name
     * 
     * @return Checkout
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.04.2018
     */
    public function resetCurrentStep(string $currentStepName = null) : Checkout
    {
        $this->currentStep = null;
        if (!is_null($currentStepName)) {
            $this->setCurrentStepName($currentStepName);
        }
        return $this;
    }
    
    /**
     * Returns the step matching the given index to use in template.
     * 
     * @param int $index Index
     * 
     * @return string|null
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function getStepNameByIndex(int $index) : ?string
    {
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
     * @return CheckoutStep|null
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function getStepByIndex($index) : ?CheckoutStep
    {
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
    public function ShowCartInCheckoutNavigation() : bool
    {
        return $this->config()->show_cart_in_checkout_navigation;
    }
    
    /**
     * Returns whether to allow an anonymous checkout or not.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.07.2019
     */
    public function AllowAnonymousCheckout() : bool
    {
        return $this->config()->allow_anonymous_checkout;
    }
    
    /**
     * Returns whether the current page controller is a CartPageController.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function CurrentPageIsCartPage() : bool
    {
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
    public static function create_from_session() : Checkout
    {
        if (is_null(self::$session_checkout)) {
            self::$session_checkout = Checkout::create();
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
     * @since 15.10.2018
     */
    public static function clear_session() : void
    {
        $sessionData = Tools::Session()->get(self::SESSION_KEY);
        if (!is_null($sessionData)) {
            Tools::Session()->set(self::SESSION_KEY, null);
            Tools::saveSession();
        }
    }
    
    /**
     * Returns whether the given or current logged in Member can access the checkout.
     * 
     * @param Member $member Member to check access for
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function canAccess(Member $member = null) : bool
    {
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
     * @return Checkout
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function initFromSession() : Checkout
    {
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
        return $this;
    }
    
    /**
     * Saves the current checkout data in session.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function saveInSession() : Checkout
    {
        Tools::Session()->set(self::SESSION_KEY . '.CurrentStepList', $this->getStepList());
        Tools::Session()->set(self::SESSION_KEY . '.CompletedSteps', $this->getCompletedSteps());
        Tools::Session()->set(self::SESSION_KEY . '.CurrentStepName', $this->getCurrentStepName());
        Tools::Session()->set(self::SESSION_KEY . '.Data', $this->getData());
        Tools::saveSession();
        return $this;
    }
    
    /**
     * Finalizes the checkout and moves its data to a different session store.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.04.2018
     */
    public function finalize() : Checkout
    {
        Tools::Session()->set(self::FINALIZED_SESSION_KEY, Tools::Session()->get(self::SESSION_KEY));
        Tools::saveSession();
        static::clear_session();
        $this->clearData();
        return $this;
    }
    
    /**
     * Returns the finalized checkout data.
     * Contains the checkout data of the last finalized order placement if done within the current
     * session.
     * 
     * @return array
     */
    public function getFinalizedData() : ?array
    {
        return Tools::Session()->get(self::FINALIZED_SESSION_KEY . '.Data');
    }
    
    /**
     * Adds the given data to the checkout data.
     * 
     * @param array $data Data to add
     * 
     * @return Checkout
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function addData($data) : Checkout
    {
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }
        return $this;
    }
    
    /**
     * Adds the given data key value pair to the checkout data.
     * 
     * @param string $key   Checkout data key
     * @param string $value Checkout data value
     * 
     * @return Checkout
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function addDataValue($key, $value) : Checkout
    {
        $this->data[$key] = $value;
        return $this;
    }
    
    /**
     * Clears the checkout data.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function clearData() : Checkout
    {
        $this->setData([]);
        return $this;
    }
    
    /**
     * Returns the checkout data.
     * 
     * @return array
     */
    public function getData() : array
    {
        return $this->data;
    }
    
    /**
     * Returns the value for the given data key out of the checkout data.
     * 
     * @param string $key Checkout data key
     * 
     * @return mixed
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function getDataValue($key)
    {
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
     * @return Checkout
     */
    public function setData(array $data = null) : Checkout
    {
        $this->data = $data;
        return $this;
    }
    
    /**
     * Returns the current step name.
     * 
     * @return string
     */
    public function getCurrentStepName() : string
    {
        return $this->currentStepName;
    }

    /**
     * Sets the current step name.
     * 
     * @param string $currentStepName Current step name
     * 
     * @return void
     */
    public function setCurrentStepName(string $currentStepName) : Checkout
    {
        if ($this->currentStepName !== $currentStepName) {
            $this->currentStep     = null;
            $this->currentStepName = $currentStepName;
        }
        return $this;
    }

    /**
     * Sets the current step name.
     * 
     * @param CheckoutStep $step Current step name
     * 
     * @return bool
     */
    public function isCurrentStep(CheckoutStep $step) : bool
    {
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
    public function getCompletedSteps() : array
    {
        return $this->completedSteps;
    }
    
    /**
     * Sets the list of completed steps.
     * 
     * @param array $completedSteps List of completed steps
     * 
     * @return Checkout
     */
    public function setCompletedSteps(array $completedSteps = null) : Checkout
    {
        if (!is_array($completedSteps)) {
            $completedSteps = [];
        }
        $this->completedSteps = (array) $completedSteps;
        return $this;
    }
    
    /**
     * Adds a completed step.
     * 
     * @param CheckoutStep $step Step to add
     * 
     * @return Checkout
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function addCompletedStep(CheckoutStep $step) : Checkout
    {
        $stepList  = $this->getStepList();
        $stepName  = get_class($step);
        $stepIndex = array_search($stepName, $stepList);
        $this->completedSteps[$stepIndex] = $stepName;
        return $this;
    }
    
    /**
     * Returns if the given step is completed.
     * 
     * @param CheckoutStep $step Step instance or name
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function isCompletedStep($step) : bool
    {
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
     * @return Checkout
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function redirectToCurrentStep() : Checkout
    {
        if (!$this->getController()->redirectedTo()) {
            $this->getController()->redirect($this->getController()->Link('step/' . $this->getCurrentStep()->StepNumber()));
        }
        return $this;
    }
    
    /**
     * Returns the controller.
     * 
     * @return Controller
     */
    public function getController() : Controller
    {
        return $this->controller;
    }

    /**
     * Sets the controller.
     * 
     * @param Controller $controller Controller
     * 
     * @return void
     */
    public function setController(Controller $controller) : Checkout
    {
        $this->controller = $controller;
        return $this;
    }
    
    /**
     * Initializes the current checkout step.
     * 
     * @return \SilverCart\Checkout\Checkout
     */
    public function initStep() : Checkout
    {
        $shoppingCart = Customer::currentUser()->getCart();
        /* @var $shoppingCart \SilverCart\Model\Order\ShoppingCart */
        $shoppingCart->setShippingMethodID((int) $this->getDataValue('ShippingMethod'));
        $shoppingCart->setPaymentMethodID((int) $this->getDataValue('PaymentMethod'));
        $this->getCurrentStep()->init();
        return $this;
    }
}