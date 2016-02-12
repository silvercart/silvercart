<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * represents a shopping cart. Every customer has one initially.
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 * @since 23.10.2010
 */
class SilvercartCartPage extends Page {
    
    /**
     * icon for site tree
     *
     * @var array
     */
    public static $icon = "silvercart/img/page_icons/cart";
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
    }

}

/**
 * related controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartCartPage_Controller extends Page_Controller {

    /**
     * Initialise the shopping cart.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.11.2014
     */
    public function init() {
        if (SilvercartCustomer::currentUser() &&
            SilvercartCustomer::currentUser()->SilvercartShoppingCartID > 0) {

            SilvercartCustomer::currentUser()->getCart();
        }
        parent::init();
        if (SilvercartCustomer::currentUser() &&
            SilvercartCustomer::currentUser()->getCart()->exists() &&
            SilvercartCustomer::currentUser()->getCart()->SilvercartShoppingCartPositions()->count() > 0 &&
            SilvercartConfig::RedirectToCheckoutWhenInCart()) {
            
            $this->redirect(SilvercartTools::PageByIdentifierCode('SilvercartCheckoutStep')->Link());
        }
    }

    /** Indicates wether ui elements for removing items and altering their
     * quantity should be shown in the shopping cart templates.
     *
     * @return boolean true
     */
    public function getEditableShoppingCart() {
        return true;
    }
    
    /**
     * Returns an instance of SilvercartCheckoutFormStep2 to represent a valid 
     * checkout context.
     * 
     * @return SilvercartCheckoutFormStep2
     */
    public function getCheckoutContext() {
        $checkoutStepPage = SilvercartTools::PageByIdentifierCode('SilvercartCheckoutStep');
        $checkoutStepPageController = ModelAsController::controller_for($checkoutStepPage);
        $checkoutStepPageController->handleRequest($this->getRequest());
        return new SilvercartCheckoutFormStep2($checkoutStepPageController);
    }

}
