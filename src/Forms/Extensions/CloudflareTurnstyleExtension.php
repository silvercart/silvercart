<?php

namespace SilverCart\Forms\Extensions;

use SilverCart\Forms\CustomForm;
use SilverCart\Forms\FormFields\CloudflareTurnstyleField;
use SilverStripe\Core\Extension;
use function _t;

/**
 * Cloudflare Turnstyle extension for CustomForms.
 * 
 * @package SilverCart
 * @subpackage Forms\Extensions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 20.04.2023
 * @copyright 2023 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property CustomForm $owner Owner
 */
class CloudflareTurnstyleExtension extends Extension
{
    /**
     * Determines whether to use the default field integration using the 
     * updateCustomFields() method.
     * 
     * @var bool
     */
    private static bool $cloudflare_use_default_field = true;
    /**
     * Private Cloudflare Turnstyle secret key.
     *
     * @var string
     */
    private static string $cloudflare_secret_key = '';
    /**
     * Cloudflare Turnstyle site key.
     *
     * @var string
     */
    private static string $cloudflare_site_key = '';
    /**
     * Private Cloudflare Turnstyle theme (light/dark/auto).
     *
     * @var string
     */
    private static string $cloudflare_theme = '';
    
    /**
     * Updates the given $fields.
     * 
     * @param array &$fields Fields to update
     * 
     * @return void
     */
    public function updateCustomFields(array &$fields) : void
    {
        if ($this->EnableCloudflareTurnstyle()) {
            foreach ($fields as $field) {
                if ($field instanceof CloudflareTurnstyleField) {
                    return;
                }
            }
            $fields = array_merge($fields, $this->getCloudflareTurnstyleFields());
        }
    }
    
    /**
     * Adds the rendered Cloudflare Turnstyle field if necessary.
     * 
     * @param string &$renderedFields Rendered field (HTML) string
     * 
     * @return void
     */
    public function renderUpdatedCustomFields(string &$renderedFields) : void
    {
        if (!$this->owner->config()->cloudflare_use_default_field) {
            return;
        }
        if ($this->EnableCloudflareTurnstyle()) {
            $renderedFields .= $this->owner->Fields()->dataFieldByName('CloudflareTurnstyle')->forTemplate();
        }
    }
    
    /**
     * Overwrites the submission if the validation fails.
     * 
     * @param array      $data         Data
     * @param CustomForm $form         Form
     * @param bool       &$overwritten Overwritten
     * 
     * @return void
     */
    public function overwriteDoSubmit(array $data, CustomForm $form, bool &$overwritten) : void
    {
        if ($this->EnableCloudflareTurnstyle()) {
            $verified = CloudflareTurnstyleField::verifyRequest();
            if (!$verified) {
                $this->owner->setErrorMessage(_t(CloudflareTurnstyleField::class . '.Verify', 'Please verify that you are not a robot.'));
                $this->owner->setSessionData($this->owner->getData());
                $overwritten = true;
                return;
            }
        }
    }
    
    /**
     * Returns the Cloudflare Turnstyle related form fields.
     * 
     * @return array
     */
    protected function getCloudflareTurnstyleFields() : array
    {
        $fields = [];
        if ($this->EnableCloudflareTurnstyle()) {
            $field    = CloudflareTurnstyleField::create('CloudflareTurnstyle', $this->owner->fieldLabel('CloudflareTurnstyle'));
            $fields[] = $field;
            /* @var $field CloudflareTurnstyleField */
            if (!empty($this->owner->config()->cloudflare_site_key)) {
                $field->setSiteKey($this->owner->config()->cloudflare_site_key);
            }
            if (!empty($this->owner->config()->cloudflare_secret_key)) {
                $field->setSecretKey($this->owner->config()->cloudflare_secret_key);
            }
            if (!empty($this->owner->config()->cloudflare_theme)) {
                $field->setTheme($this->owner->config()->cloudflare_theme);
            }
        }
        return $fields;
    }
    
    /**
     * Returns whether Cloudflare Turnstyle is enabled or not.
     * 
     * @return bool
     */
    public function EnableCloudflareTurnstyle() : bool
    {
        return CloudflareTurnstyleField::isEnabled();
    }
}