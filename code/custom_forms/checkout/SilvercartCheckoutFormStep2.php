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
 * form step for customers invoice/shipping address. Adds a form for LOGGED IN
 * or ANONYMOUS customers.
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 01.07.2011
 * @license see license file in modules root directory
 */
class SilvercartCheckoutFormStep2 extends CustomHtmlFormStep {
    
    /**
     * Returns the Cache Key for the current step
     * 
     * @return string
     */
    public function getCacheKeyExtension() {
        if (empty($this->cacheKeyExtension)) {
            $cacheKeyExtension  = $this->Controller()->getCacheKey();
            $member             = SilvercartCustomer::currentRegisteredCustomer();

            if ($member) {
                $numberOfAddresses = $member->SilvercartAddresses()->count();

                if ($numberOfAddresses > 0) {
                    $cacheKeyExtension .= md5('_'.$numberOfAddresses.'_'.
                                     $member->SilvercartAddresses()->max('LastEdited'));
                } else {
                    $cacheKeyExtension .= md5('_0');
                }
            }
            $this->cacheKeyExtension = $cacheKeyExtension;
        }

        return $this->cacheKeyExtension;
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.06.2014
     */
    public function __construct($controller, $params = null, $preferences = null, $barebone = false) {

        parent::__construct($controller, $params, $preferences, $barebone);

        if (!$barebone) {
            /*
             * redirect a user if his cart is empty and no order exists
             */
            $checkoutData = $this->controller->getCombinedStepData();
            if (!Member::currentUser() ||
                (!Member::currentUser()->getCart()->isFilled() &&
                 !array_key_exists('orderId', $checkoutData))) {

                $frontPage = SilvercartPage_Controller::PageByIdentifierCode();
                $this->getController()->redirect($frontPage->RelativeLink());
            }
            
            if ($this->isCustomerLoggedIn()) {
                $this->registerCustomHtmlForm('SilvercartCheckoutFormStep2Regular', new SilvercartCheckoutFormStep2Regular($this->controller));
                Session::set("redirect", $this->controller->Link());
                $this->registerCustomHtmlForm('SilvercartAddAddressForm', new SilvercartAddAddressForm($this->controller));
            } else {
                $this->registerCustomHtmlForm('SilvercartCheckoutFormStep2Anonymous', new SilvercartCheckoutFormStep2Anonymous($this->controller));
            }
        }
    }

    /**
     * Is customer logged in?
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2011
     */
    public function isCustomerLoggedIn() {
        $isLoggedIn = false;
        if (SilvercartCustomer::currentRegisteredCustomer()) {
            $isLoggedIn = true;
        }
        return $isLoggedIn;
    }

    /**
     * Here we set some preferences.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 31.03.2011
     */
    public function preferences() {
        $this->preferences['stepIsVisible']             = true;
        $this->preferences['stepTitle']                 = _t('SilvercartCheckoutFormStep2.TITLE', 'Addresses');
        $this->preferences['submitButtonTitle']         = _t('SilvercartCheckoutFormStep.FORWARD', 'Next');
        $this->preferences['fillInRequestValues']       = true;
        $this->preferences['loadShoppingcartModules']   = false;
        $this->preferences['createShoppingcartForms']   = false;
        $this->preferences['doJsValidationScrolling']   = false;

        parent::preferences();
    }
    
}

