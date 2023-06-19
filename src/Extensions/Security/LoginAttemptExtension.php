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
     * Casted attribute
     * 
     * @var array
     */
    private static array $casting = [
        'IPCity'         => 'Varchar',
        'IPCityAdmin'    => 'Varchar',
        'IPCountry'      => 'Varchar',
        'IPCountryAdmin' => 'Varchar',
    ];
    
    /**
     * Updates the summary fields.
     * 
     * @param array &$fields Fields to update
     * 
     * @return void
     */
    public function updateFieldLabels(&$labels) : void
    {
        $labels = array_merge($labels, [
            'IPCity'         => _t(self::class . '.City', 'City'),
            'IPCityAdmin'    => _t(self::class . '.City', 'City'),
            'IPCountry'      => _t(self::class . '.Country', 'Country'),
            'IPCountryAdmin' => _t(self::class . '.Country', 'Country'),
        ]);
    }
    
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
            'Created'        => Tools::field_label('DATE'),
            'Status'         => $this->owner->fieldLabel('Status'),
            'IP'             => $this->owner->fieldLabel('IP'),
            'IPCityAdmin'    => $this->owner->fieldLabel('IPCity'),
            'IPCountryAdmin' => $this->owner->fieldLabel('IPCountry'),
        ];
    }
    
    /**
     * Returns the city name matching to the IP.
     * 
     * @return string
     */
    public function getIPCityAdmin() : string
    {
        $city = $this->IPCity();
        if (empty($city)) {
            $city = _t('SilverCart.NotDetectable', 'Not detectable');
        }
        return $city;
    }
    
    /**
     * Returns the city name matching to the IP.
     * 
     * @return string
     */
    public function getIPCity() : string
    {
        return $this->IPCity();
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
    public function getIPCountryAdmin() : string
    {
        $country = $this->IPCountry();
        if (empty($country)) {
            $country = _t('SilverCart.NotDetectable', 'Not detectable');
        }
        return $country;
    }
    
    /**
     * Returns the country name matching to the IP.
     * 
     * @return string
     */
    public function getIPCountry() : string
    {
        return $this->IPCountry();
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