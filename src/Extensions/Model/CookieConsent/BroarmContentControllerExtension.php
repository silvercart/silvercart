<?php

namespace SilverCart\Extensions\Model\CookieConsent;

use SilverStripe\Core\Extension;

/**
 * Broarm CookieConsent extension for SilverStripe ContentController.
 * 
 * @package SilverCart
 * @subpackage Extensions\Model\CookieConsent
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 04.11.2020
 * @copyright 2020 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property \SilverStripe\CMS\Controllers\ContentController $owner Owner
 */
class BroarmContentControllerExtension extends Extension
{
    /**
     * Updates whether the cookie consent modal should be prompted or not.
     * 
     * @param bool &$prompt Prompt cookie consent modal?
     * 
     * @return void
     */
    public function updatePromptCookieConsent(bool &$prompt) : void
    {
        $doNotPromptCookieConsent = $this->owner->getRequest()->getVar('dnpcs') === '1';
        if ($doNotPromptCookieConsent) {
            $prompt = false;
        }
    }
}