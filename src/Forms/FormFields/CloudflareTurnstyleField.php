<?php

namespace SilverCart\Forms\FormFields;

use ReCaptcha\ReCaptcha;
use ReCaptcha\RequestMethod\Post as ReCaptchaPost;
use SilverStripe\Forms\Validator;
use function _t;

/**
 * A Cloudflare Turnstyle field
 *
 * @package SilverCart
 * @subpackage Forms\FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 20.04.2023
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CloudflareTurnstyleField extends GoogleRecaptchaField
{
    /**
     * Private Cloudflare Turnstyle secret key.
     *
     * @var string
     */
    private static $secret_key = '';
    /**
     * Cloudflare Turnstyle site key.
     *
     * @var string
     */
    private static $site_key = '';
    /**
     * Cloudflare Turnstyle site verify url.
     *
     * @var string
     */
    private static $siteverify_url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    /**
     * Private Cloudflare Turnstyle theme (light/dark/auto).
     *
     * @var string
     */
    private static $theme = 'light';
    
    /**
     * Returns the private Cloudflare Turnstyle secret.
     * 
     * @return string
     */
    public static function SecretKey() : string
    {
        return (string) self::config()->secret_key;
    }

    /**
     * Sets the private Cloudflare Turnstyle secret.
     * 
     * @param string $secret_key Private Cloudflare Turnstyle secret
     * 
     * @return void
     */
    public static function setSecretKey(string $secret_key) : void
    {
        self::config()->update('secret_key', $secret_key);
    }
    
    /**
     * Returns the private Cloudflare Turnstyle secret.
     * 
     * @return string
     */
    public function getSecretKey() : string
    {
        return self::SecretKey();
    }
    
    /**
     * Returns the Cloudflare Turnstyle site_key.
     * 
     * @return string
     */
    public static function SiteKey() : string
    {
        return (string) self::config()->site_key;
    }

    /**
     * Sets the Cloudflare Turnstyle site_key.
     * 
     * @param string $site_key Private Cloudflare Turnstyle site_key
     * 
     * @return void
     */
    public static function setSiteKey(string $site_key) : void
    {
        self::config()->update('site_key', $site_key);
    }
    
    /**
     * Returns the Cloudflare Turnstyle site_key.
     * 
     * @return string
     */
    public function getSiteKey() : string
    {
        return self::SiteKey();
    }
    
    /**
     * Returns the Cloudflare Turnstyle siteverify_url.
     * 
     * @return string
     */
    public static function SiteverifyURL() : string
    {
        return (string) self::config()->siteverify_url;
    }
    
    /**
     * Returns the private Cloudflare Turnstyle theme.
     * 
     * @return string
     */
    public static function Theme() : string
    {
        return (string) self::config()->theme;
    }
    
    /**
     * Returns whether Google Cloudflare Turnstyle is enabled or not.
     * 
     * @return bool
     */
    public static function isEnabled() : bool
    {
        return !empty(self::config()->secret_key)
            && !empty(self::config()->site_key);
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
        $recaptcha          = new ReCaptcha(self::SecretKey(), new ReCaptchaPost(self::SiteverifyURL()));
        $resp               = $recaptcha->verify($gRecaptchaResponse, $remoteIp);
        return $resp->isSuccess();
    }
}