<?php

namespace SilverCart\Checkout;

use ReflectionClass;
use SilverCart\Checkout\Checkout;
use SilverStripe\View\ViewableData;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\FieldType\DBHTMLText;
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
class CheckoutStep extends ViewableData
{
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
    protected static $customOutput = [];
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
    public function __construct(Controller $controller)
    {
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
    public function doInit() : void
    {
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
    public function init() : void
    {
        
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
    public function process() : void
    {
        
    }
    
    /**
     * Marks this step as completed.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.11.2017
     */
    public function complete() : void
    {
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
    public function getTemplates() : array
    {
        return SSViewer::get_templates_by_class(static::class, '', __CLASS__);
    }

    /**
     * Return a rendered version of this step.
     *
     * @return DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function forTemplate() : DBHTMLText
    {
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
    public function allowedActions() : array
    {
        return $this->config()->get('allowed_actions');
    }

    /**
     * Returns whether the given $action exists on this checkout step.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function hasAction($action) : bool
    {
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
    public function IsVisible() : bool
    {
        return (bool) $this->config()->get('is_visible');
    }
    
    /**
     * Returns whether this step is the current step.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function IsCurrentStep() : bool
    {
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
    public function IsCompleted() : bool
    {
        return $this->getCheckout()->isCompletedStep($this);
    }
    
    /**
     * Returns whether the previous step is completed.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.09.2018
     */
    public function IsPreviousStepCompleted() : bool
    {
        $completed = false;
        $checkout  = $this->getCheckout();
        $stepList  = $checkout->getStepList();
        $index     = array_search(get_class($this), $stepList);
        if (is_int($index)) {
            $previousIndex = $index - 1;
            if (array_key_exists($previousIndex, $stepList)) {
                $previousStepName = $stepList[$previousIndex];
                if ($checkout->isCompletedStep($previousStepName)) {
                    $completed = true;
                }
            }
        }
        return $completed;
    }
    
    /**
     * Returns whether this step is accessible.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.09.2018
     */
    public function IsAccessible() : bool
    {
        return ($this->IsCurrentStep() && !$this->getCheckout()->CurrentPageIsCartPage()) || $this->IsCompleted() || $this->IsPreviousStepCompleted();
    }
    
    /**
     * Returns this step's number.
     * 
     * @return int
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function StepNumber() : int
    {
        return $this->getStepIndex() + 1;
    }
    
    /**
     * Returns this step's index.
     * 
     * @return int
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function getStepIndex() : int
    {
        $stepNumber = array_search(get_class($this), $this->getCheckout()->getStepList());
        return $stepNumber;
    }
    
    /**
     * Returns this step's number respecting its visibility.
     * 
     * @return int
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function VisibleStepNumber() : int
    {
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
    public function StepTitle() : string
    {
        $reflection = new ReflectionClass(static::class);
        $fallback   = $reflection->getShortName();
        return _t(static::class . '.StepTitle', $fallback);
    }

    /**
     * Returns the checkout.
     * 
     * @return Checkout
     */
    public function getCheckout() : Checkout
    {
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
    public function getController() : ?Controller
    {
        return $this->controller;
    }

    /**
     * Sets the controller.
     * 
     * @param Controller $controller Controller
     * 
     * @return CheckoutStep
     */
    public function setController(Controller $controller) : CheckoutStep
    {
        $this->controller = $controller;
        return $this;
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
    public static function addCustomOutput($output) : void
    {
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
    public function CustomOutput() : string
    {
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
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.11.2017
     */
    public function canAccess() : bool
    {
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
    public function resetNextSteps() : void
    {
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
    public function redirectToNextStep() : CheckoutStep
    {
        if (!$this->getController()->redirectedTo()) {
            $this->getController()->redirect($this->getController()->Link('step/' . ($this->StepNumber() + 1)));
        }
        return $this;
    }
}