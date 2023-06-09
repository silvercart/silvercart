<?php

namespace SilverCart\Extensions\Security\MemberAuthenticator;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Extension;
use SilverStripe\Security\LoginAttempt;
use SilverStripe\Security\MemberAuthenticator\MemberAuthenticator;

/**
 * Extesnions for the SilverStripe MemberAuthenticator
 * 
 * @package SilverCart
 * @subpackage SubPackage
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.06.2023
 * @copyright 2023 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property MemberAuthenticator $owner Owner
 */
class MemberAuthenticatorExtension extends Extension
{
    /**
     * Updates the LoginAttempt.
     * 
     * @param LoginAttempt $attempt Attempt to update
     * @param array        $data    Context data
     * @param HTTPRequest  $request Context HTTP request
     * 
     * @return void
     */
    public function updateLoginAttempt(LoginAttempt $attempt, array $data, HTTPRequest $request) : void
    {
        $ip = $attempt->IP;
        if (array_key_exists('HTTP_X_REAL_IP', $_SERVER)) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } elseif (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if ($ip !== $attempt->IP) {
            $attempt->IP = $ip;
        }
    }
}