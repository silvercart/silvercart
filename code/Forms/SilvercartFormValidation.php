<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Base
 */

/**
 * Provides callbacks for a form validation
 *
 * @package Silvercart
 * @subpackage Base
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 09.11.2012
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartFormValidation extends Object {
    /**
     * used as Form callback: Does the entered Email already exist?
     *
     * @param string $value           The email address to be checked
     * @param int    $allowedMemberID ID of a member to ignore this check for
     *
     * @return array to be rendered in the template
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Patrick Schneider <pschneider@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.02.2013
     */
    public static function doesEmailExistAlready($value, $allowedMemberID = null) { 
        $emailExistsAlready = false;

        $member = Member::get()->filter('Email', $value)->first();

        if ($member instanceof Member &&
            $member->ID != $allowedMemberID) {
            $emailExistsAlready = true;
        }

        return array(
            'success' => !$emailExistsAlready,
            'errorMessage' => _t('SilvercartPage.EMAIL_ALREADY_REGISTERED', 'This Email address is already registered')
        );
    }
}