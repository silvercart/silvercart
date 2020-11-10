<?php

namespace SilverCart\Extensions\Model\CookieConsent;

use Broarm\CookieConsent\CookieConsent;
use Broarm\CookieConsent\Model\CookieGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

/**
 * Broarm CookieConsent extension for SilverCart ExternalResource.
 * 
 * @package SilverCart
 * @subpackage Extensions\Model\CookieConsent
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 04.11.2020
 * @copyright 2020 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property \SilverCart\Model\CookieConsent\ExternalResource $owner Owner
 */
class BroarmExternalResourceExtension extends DataExtension
{
    /**
     * Has one relations.
     *
     * @var array
     */
    private static $has_one = [
        'CookieGroup' => CookieGroup::class,
    ];
    
    /**
     * Returns whether the given $member can require this record.
     * If no $member is given, the currently logged in user will be used instead.
     * 
     * @return bool|null
     */
    public function canRequire() : ?bool
    {
        $can         = null;
        $cookieGroup = $this->owner->CookieGroup();
        /* @var $cookieGroup CookieGroup */
        if ($cookieGroup->exists()
         && !CookieConsent::check($cookieGroup->ConfigName)
        ) {
            $can = false;
        }
        return $can;
    }
    
    /**
     * Updates the CMS fields.
     * 
     * @param FieldList &$fields Fields to update
     * 
     * @return void
     */
    public function updateCMSFields(FieldList $fields) : void
    {
        $fields->dataFieldByName('CookieGroupID')->setDescription($this->owner->fieldLabel('CookieGroupDesc'));
    }
    
    /**
     * Updates the summary fields.
     * 
     * @param array &$fields Fields to update
     */
    public function updateSummaryFields(&$fields) : void
    {
        $fields['CookieGroup.Title'] = $this->owner->fieldLabel('CookieGroup');
    }
}