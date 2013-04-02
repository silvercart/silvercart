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
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 03.01.2011
 * @license see license file in modules root directory
 */
class SilvercartCheckoutFormStepDefaultOrderConfirmation extends CustomHtmlFormStep {

    /**
     * Don't cache this form.
     *
     * @var bool
     */
    protected $excludeFromCache = true;

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
}
