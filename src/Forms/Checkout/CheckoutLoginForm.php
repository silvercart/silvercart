<?php

namespace SilverCart\Forms\Checkout;

use SilverCart\Forms\LoginForm;

/**
 * Login form to use in checkout.
 *
 * @package SilverCart
 * @subpackage Forms_Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 25.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CheckoutLoginForm extends LoginForm {
    
    /**
     * Returns the form actions.
     * 
     * @return FieldList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function Actions() {
        $actions = parent::Actions();
        $actions->dataFieldByName('action_dologin')->setTitle($this->fieldLabel('SubmitButtonTitle'));
        return $actions;
    }
    
}
