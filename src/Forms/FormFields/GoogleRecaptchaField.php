<?php

namespace SilverCart\Forms\FormFields;

use ReCaptcha\ReCaptcha;
use SilverStripe\Forms\FormField;

/**
 * A Google reCAPTCHA field
 *
 * @package SilverCart
 * @subpackage Forms\FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 21.12.2016
 * @copyright 2019 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GoogleRecaptchaField extends FormField
{
    /**
     * Private reCAPTCHA secret.
     *
     * @var string
     */
    private static $recaptcha_secret = '';
    /**
     * reCAPTCHA site key.
     *
     * @var string
     */
    private static $recaptcha_site_key = '';
    
    /**
     * Returns the private reCAPTCHA secret.
     * 
     * @return string
     */
    public static function get_recaptcha_secret() : string
    {
        return self::config()->recaptcha_secret;
    }

    /**
     * Sets the private reCAPTCHA secret.
     * 
     * @param string $recaptcha_secret Private reCAPTCHA secret
     * 
     * @return void
     */
    public static function set_recaptcha_secret(string $recaptcha_secret) : void
    {
        self::config()->update('recaptcha_secret', $recaptcha_secret);
    }
    
    /**
     * Returns the private reCAPTCHA secret.
     * 
     * @return string
     */
    public function getRecaptchaSecret() : string
    {
        return self::get_recaptcha_secret();
    }
    
    /**
     * Returns the reCAPTCHA site_key.
     * 
     * @return string
     */
    public static function get_recaptcha_site_key() : string
    {
        return self::config()->recaptcha_site_key;
    }

    /**
     * Sets the reCAPTCHA site_key.
     * 
     * @param string $recaptcha_site_key Private reCAPTCHA site_key
     * 
     * @return void
     */
    public static function set_recaptcha_site_key(string $recaptcha_site_key) : void
    {
        self::config()->update('recaptcha_site_key', $recaptcha_site_key);
    }
    
    /**
     * Returns whether Google reCAPTCHA is enabled or not.
     * 
     * @return bool
     */
    public static function isEnabled() : bool
    {
        return !empty(self::config()->recaptcha_secret)
            && !empty(self::config()->recaptcha_site_key);
    }
    
    /**
     * Returns the reCAPTCHA site_key.
     * 
     * @return string
     */
    public function getRecaptchaSiteKey() : string
    {
        return self::get_recaptcha_site_key();
    }
    
    /**
     * Verifies the request.
     * 
     * @return bool
     */
    public static function verifyRequest() : bool
    {
        $gRecaptchaResponse = $_REQUEST['g-recaptcha-response'];
        $remoteIp           = $_SERVER['REMOTE_ADDR'];
        $recaptcha          = new ReCaptcha(self::get_recaptcha_secret());
        $resp               = $recaptcha->verify($gRecaptchaResponse, $remoteIp);
        return $resp->isSuccess();
    }

    /**
     * Validate by submitting to external service
     *
     * @param \SilverStripe\Forms\Validator $validator Validator
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.09.2019
     */
    public function validate($validator) : bool
    {
        $valid = self::verifyRequest();
        if (!$valid) {
            $validator->validationError(
                $this->getName(),
                _t(self::class . '.Verify', 'Please verify that you are not a robot.'),
                "validation",
                false
            );
        }
        return $valid;
    }
}