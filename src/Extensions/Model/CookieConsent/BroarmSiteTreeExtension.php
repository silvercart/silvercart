<?php

namespace SilverCart\Extensions\Model\CookieConsent;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

/**
 * Broarm CookieConsent extension for SilverStripe SiteTree.
 * 
 * @package SilverCart
 * @subpackage Extensions\Model\CookieConsent
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.03.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property \SilverStripe\CMS\Model\SiteTree $owner Owner
 */
class BroarmSiteTreeExtension extends DataExtension
{
    /**
     * DB attributes.
     * 
     * @var string[]
     */
    private static $db = [
        'DisablePromptCookieConsent' => 'Boolean',
    ];
    
    public function updateCMSFields(FieldList $fields) : void
    {
        $fields->addFieldToTab('Root.Main', CheckboxField::create('DisablePromptCookieConsent', $this->owner->fieldLabel('DisablePromptCookieConsent')));
    }
    
    /**
     * Updates the field labels.
     * 
     * @param array &$labels Labels to update
     * 
     * @return void
     */
    public function updateFieldLabels(&$labels) : void
    {
        $labels['DisablePromptCookieConsent'] = _t(self::class . '.DisablePromptCookieConsent', 'Disable cookie consent for this page');
    }
}