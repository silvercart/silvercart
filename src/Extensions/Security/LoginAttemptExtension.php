<?php

namespace SilverCart\Extensions\Security;

use SilverCart\Dev\Tools;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\LoginAttempt;

/**
 * Extension for SilverStripe LoginAttempt
 * 
 * @package SilverCart
 * @subpackage Extensions\Security
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 13.10.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property LoginAttempt $owner Owner
 */
class LoginAttemptExtension extends DataExtension
{
    /**
     * Updates the summary fields.
     * 
     * @param array &$fields Fields to update
     * 
     * @return void
     */
    public function updateSummaryFields(&$fields) : void
    {
        $fields = [
            'Created' => Tools::field_label('DATE'),
            'Status'  => $this->owner->fieldLabel('Status'),
            'IP'      => $this->owner->fieldLabel('IP'),
        ];
    }
    
    /**
     * Returns the city name matching to the IP.
     * 
     * @return string
     */
    public function IPCity() : string
    {
        return Tools::IPCity($this->owner->IP);
    }
    
    /**
     * Returns the country name matching to the IP.
     * 
     * @return string
     */
    public function IPCountry() : string
    {
        return Tools::IPCountry($this->owner->IP);
    }
    
    /**
     * Returns a IP based location string.
     * 
     * @return string
     */
    public function IPLocation() : string
    {
        return Tools::IPLocation($this->owner->IP);
    }
}