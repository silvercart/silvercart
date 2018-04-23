<?php

namespace SilverCart\Checkout;

use ReflectionClass;
use SilverCart\Checkout\Checkout;
use SilverStripe\View\ViewableData;
use SilverStripe\Control\Controller;
use SilverStripe\View\SSViewer;

/**
 * Checkout step.
 *
 * @package SilverCart
 * @subpackage Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 16.11.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CheckoutStep extends ViewableData {
    
    /**
     * List of allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = [];

    /**
     * Is this step visible?
     * (default: true)
     *
     * @var bool
     */
    private static $is_visible = true;

    /**
     * A list of custom output to add to the content area.
     *
     * @var array
     */
    protected static $customOutput = array();

    /**
     * Checkout.
     *
     * @var Checkout
     */
    protected $checkout = null;
    
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
    public function __construct(Controller $controller) {
        parent::__construct();
        $this->setController($controller);
    }
    
    /**
     * Optional method to initialize a checkout step.
     * Executed by CheckoutStepController. Also calls extension methods onBeforeInit and onAfterInit.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.04.2018
     */
    public function doInit() {
        $this->extend('onBeforeInit');
        $this->init();
        $this->extend('onAfterInit');
    }
    
    /**
     * Optional method to initialize a checkout step.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.04.2018
     */
    public function init() {
        
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
        
    }
    
    /**
     * Marks this step as completed.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.11.2017
     */
    public function complete() {
        $checkout = $this->getCheckout();
        $checkout->addCompletedStep($this);
        $nextStep = $checkout->getStepByIndex($this->getStepIndex() + 1);
        if ($nextStep instanceof CheckoutStep) {
            $checkout->setCurrentStepName(get_class($nextStep));
        }
        $checkout->saveInSession();
    }

    /**
     * Returs the ordered list of preferred templates for rendering this form
     * If the template isn't set, then default to the
     * form class name e.g "Form".
     *
     * @return array
     */
    public function getTemplates() {
        return SSViewer::get_templates_by_class(static::class, '', __CLASS__);
    }

    /**
     * Return a rendered version of this step.
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function forTemplate() {
        return $this->renderWith($this->getTemplates());
    }

    /**
     * Get a array of allowed actions defined on this step,
     * any parent classes or extensions.
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function allowedActions() {
        return $this->config()->get('allowed_actions');
    }

    /**
     * Get a array of allowed actions defined on this step,
     * any parent classes or extensions.
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function hasAction($action) {
        $hasAction = false;
        $actions   = $this->allowedActions();
        if (in_array($action, $actions)) {
            $hasAction = true;
        }
        return $hasAction;
    }
    
    /**
     * Returns whether this step is visible.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function IsVisible() {
        return $this->config()->get('is_visible');
    }
    
    /**
     * Returns whether this step is the current step.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function IsCurrentStep() {
        return $this->getCheckout()->isCurrentStep($this);
    }
    
    /**
     * Returns whether this step is completed.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function IsCompleted() {
        return $this->getCheckout()->isCompletedStep($this);
    }
    
    /**
     * Returns whether the previous step is completed.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function IsPreviousStepCompleted() {
        $completed = false;
        $checkout  = $this->getCheckout();
        $stepList  = $checkout->getStepList();
        $index     = array_search(get_class($this), $stepList);
        if (is_int($index)) {
            $previousIndex    = $index - 1;
            $previousStepName = $stepList[$previousIndex];
            if ($checkout->isCompletedStep($previousStepName)) {
                $completed = true;
            }
        }
        return $completed;
    }
    
    /**
     * Returns this step's number.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function StepNumber() {
        return $this->getStepIndex() + 1;
    }
    
    /**
     * Returns this step's index.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function getStepIndex() {
        $stepNumber = array_search(get_class($this), $this->getCheckout()->getStepList());
        return $stepNumber;
    }
    
    /**
     * Returns this step's number respecting its visibility.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function VisibleStepNumber() {
        $visibleStepNumber = 0;
        if ($this->IsVisible()) {
            $checkout = $this->getCheckout();
            $number   = 0;
            if ($checkout->ShowCartInCheckoutNavigation()) {
                $number++;
            }
            foreach ($checkout->CheckoutSteps() as $step) {
                if ($step->IsVisible()) {
                    $number++;
                }
                if (get_class($this) == get_class($step)) {
                    $visibleStepNumber = $number;
                }
            }
        }
        return $visibleStepNumber;
    }
    
    /**
     * Returns the step title.
     * 
     * @return string
     */
    public function StepTitle() {
        $reflection = new ReflectionClass(static::class);
        $fallback   = $reflection->getShortName();
        return _t(static::class . '.StepTitle', $fallback);
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
        return $this->checkout;
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

    /**
     * Add a custom output snippet.
     *
     * @param string $output the output to add
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.06.2017
     */
    public static function addCustomOutput($output) {
        self::$customOutput[] = $output;
    }

    /**
     * Returns the combined custom output snippets as string.
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.06.2017
     */
    public function CustomOutput() {
        $this->extend('updateCustomOutput', self::$customOutput);

        $output = '';

        if (count(self::$customOutput) > 0) {
            $output = implode("\n", self::$customOutput);
        }

        return $output;
    }
    
    /**
     * Returns whether this step can be accessed.
     * 
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.11.2017
     */
    public function canAccess() {
        $canAccess = false;
        $stepIndex = $this->getStepIndex();
        if ($stepIndex == 0) {
            $canAccess = true;
        } else {
            $checkout = $this->getCheckout();
            $stepList = $checkout->getStepList();
            $index    = array_search(get_class($this), $stepList);
            if (is_int($index)) {
                $previousIndex    = $index - 1;
                $previousStepName = $stepList[$previousIndex];
                if ($checkout->isCompletedStep($previousStepName)) {
                    $canAccess = true;
                }
            }
        }
        return $canAccess;
    }
    
    /**
     * Removes all following steps out of completed step list.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.11.2017
     */
    public function resetNextSteps() {
        $checkout       = $this->getCheckout();
        $allSteps       = $checkout->getStepList();
        $completedSteps = $checkout->getCompletedSteps();
        $resetStep      = false;
        foreach ($completedSteps as $index => $completedStep) {
            if ($allSteps[$index] == $completedStep) {
                $resetStep = true;
                continue;
            }
            if ($resetStep) {
                unset($completedSteps[$index]);
            }
        }
        $checkout->setCompletedSteps($completedSteps);
        $checkout->saveInSession();
    }
    
    /**
     * Redirects to the next step.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function redirectToNextStep() {
        $this->getController()->redirect($this->getController()->Link('step/' . ($this->StepNumber() + 1)));
    }
    
}