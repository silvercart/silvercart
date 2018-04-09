<?php
/**
 * Copyright 2018 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Security
 */

/**
 * Replaces MemberLoginForm through Injector.
 * Adds a simple i18n/Translatable support for the forgotPassword action.
 *
 * @package Silvercart
 * @subpackage Security
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2018 pixeltricks GmbH
 * @since 09.04.2018
 * @license see license file in modules root directory
 */
class SilvercartMemberLoginForm extends MemberLoginForm {
    
    /**
     * Modifies the forgotPassword password action and returns the actions.
     * 
     * @return FieldList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.04.2018
     */
    public function Actions() {
        $actions = parent::Actions();
        
        $forgotPasswordAction = $actions->fieldByName('forgotPassword');
        if ($forgotPasswordAction instanceof LiteralField) {
            $forgotPasswordAction->setContent('<p id="ForgotPassword"><a href="' . Security::lost_password_url() . '?locale=' . i18n::get_locale() . '">'
                . _t('Member.BUTTONLOSTPASSWORD', "I've lost my password") . '</a></p>');
        }
        
        return $actions;
    }
    
}
