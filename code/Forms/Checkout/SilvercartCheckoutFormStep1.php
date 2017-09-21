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
 * form step for customers shipping/billing address
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 08.04.2011
 * @license see license file in modules root directory
 */
class SilvercartCheckoutFormStep1 extends CustomHtmlFormStep {

    /**
     * A list of custom output to add to the content area.
     *
     * @var array
     */
    public static $customOutput = array();
    
    /**
     * Set this to false to hide the step in navigation
     *
     * @var bool
     */
    public static $show_in_step_navigation = true;

    /**
     * Don't cache this form.
     *
     * @var bool
     */
    protected $excludeFromCache = true;
    
    /**
     * Returns the Cache Key for the current step
     * 
     * @return string
     */
    public function getCacheKeyExtension() {
        return $this->Controller()->getCacheKey();
    }

    /**
     * init
     *
     * @param Controller $controller  the controller object
     * @param array      $params      additional parameters
     * @param array      $preferences array with preferences
     * @param bool       $barebone    is the form initialized completely?
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.11.2014
     */
    public function __construct($controller, $params = null, $preferences = null, $barebone = false) {

        parent::__construct($controller, $params, $preferences, $barebone);

        if (!$barebone) {
            /*
             * redirect a user if his cart is empty and no order exists
             */
            $checkoutData = $this->controller->getCombinedStepData();
            if (!SilvercartCustomer::currentUser() ||
                (!SilvercartCustomer::currentUser()->getCart()->isFilled() &&
                 !array_key_exists('orderId', $checkoutData))) {

                $frontPage = SilvercartPage_Controller::PageByIdentifierCode();
                $this->getController()->redirect($frontPage->RelativeLink());
            }

            $this->registerCustomHtmlForm('SilvercartCheckoutFormStep1LoginForm', new SilvercartCheckoutFormStep1LoginForm($this->controller));
            $this->registerCustomHtmlForm('SilvercartCheckoutFormStep1NewCustomerForm', new SilvercartCheckoutFormStep1NewCustomerForm($this->controller));
        }
    }

    /**
     * Logged in users get directed to the next step immediately.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Seabstian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2011
     */
    public function isConditionForDisplayFulfilled() {
        $isConditionForDisplayFulfilled = false;
        if (!SilvercartCustomer::currentRegisteredCustomer()) {
            $isConditionForDisplayFulfilled = true;
        }
        return $isConditionForDisplayFulfilled;
    }

    /**
     * Here we set some preferences.
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.03.2014
     */
    public function preferences() {
        $this->preferences['stepIsVisible']             = self::$show_in_step_navigation;
        $this->preferences['stepTitle']                 = _t('SilvercartCheckoutFormStep1.TITLE');
        $this->preferences['submitButtonTitle']         = _t('SilvercartCheckoutFormStep.FORWARD');
        $this->preferences['fillInRequestValues']       = true;
        $this->preferences['isConditionalStep']         = true;
        $this->preferences['loadModules']               = false;
        $this->preferences['createShoppingcartForms']   = false;
        $this->preferences['doJsValidationScrolling']   = false;

        parent::preferences();
    }

    /**
     * executed if there are no valdation errors on submit
     * Form data is saved in session
     *
     * @param SS_HTTPRequest $data     contains the frameworks form data
     * @param Form           $form     not used
     * @param array          $formData contains the modules form data
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.04.2011
     */
    public function submitSuccess($data, $form, $formData) {
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
        $this->extend('updateCustomOutput');

        $output = '';

        if (count(self::$customOutput) > 0) {
            $output = implode("\n", self::$customOutput);
        }

        return $output;
    }
}

