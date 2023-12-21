<?php

namespace SilverCart\Security;

use SilverCart\Dev\Tools;
use SilverCart\Extensions\Security\LoginAttemptExtension;
use SilverCart\ORM\ExtensibleDataObject;
use SilverStripe\ORM\DataObject;

/**
 * Record all lost password attempts with unknown email addresses.
 * 
 * @package SilverCart
 * @subpackage Security
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 17.10.2023
 * @copyright 2023 pixeltricks GmbH
 * @license see license file in modules root directory
 *
 * @property string $Email          Email address used for request password attempt.
 * @property string $IP             IP address of user attempting to request password
 * @property string $IPCity         IP City
 * @property string $IPCountry      IP Country
 * @property string $Status         Status (success or failure)
 * @property string $IPCityAdmin    IP City Admin
 * @property string $IPCountryAdmin IP Country Admin
 */
class LostPasswordAttempt extends DataObject
{
    use ExtensibleDataObject;
    /**
     * Success status
     */
    const SUCCESS = 'Success';
    /**
     * Failure status
     */
    const FAILURE = 'Failure';
    /**
     * DB attributes.
     * 
     * @var string[]
     */
    private static array $db = [
        'Email'     => 'Varchar(255)',
        'IP'        => 'Varchar(255)',
        'IPCity'    => 'Varchar',
        'IPCountry' => 'Varchar',
        'Status'    => "Enum('Success,Failure')",
    ];
    /**
     * Casted attribute
     * 
     * @var array
     */
    private static array $casting = [
        'IPCityAdmin'    => 'Varchar',
        'IPCountryAdmin' => 'Varchar',
    ];
    /**
     * Casted attribute
     * 
     * @var array
     */
    private static array $summary_fields = [
        'Created',
        'Status',
        'Email',
        'IP',
        'IPCityAdmin',
        'IPCountryAdmin',
    ];
    /**
     * DB table name
     * 
     * @var string
     */
    private static $table_name = "LostPasswordAttempt";
    /**
     * Default sort
     * 
     * @var string[]
     */
    private static $default_sort = "Created DESC";
    
    /**
     * Returns the summary fields.
     * 
     * @param bool $includerelations Include relations?
     * 
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'IPCity'         => _t(LoginAttemptExtension::class . '.City', 'City'),
            'IPCityAdmin'    => _t(LoginAttemptExtension::class . '.City', 'City'),
            'IPCountry'      => _t(LoginAttemptExtension::class . '.Country', 'Country'),
            'IPCountryAdmin' => _t(LoginAttemptExtension::class . '.Country', 'Country'),
        ]);
    }

    /**
     * On before write.
     * 
     * @return void
     */
    public function onBeforeWrite() : void
    {
        parent::onBeforeWrite();
        if (empty($this->IPCity)) {
            $this->IPCity = Tools::IPCity($this->IP);
        }
        if (empty($this->IPCountry)) {
            $this->IPCountry = Tools::IPCountry($this->IP);
        }
    }
    
    /**
     * Returns the city name matching to the IP.
     * 
     * @return string
     */
    public function getIPCityAdmin() : string
    {
        $city = $this->IPCity;
        if (empty($city)) {
            $city = _t('SilverCart.NotDetectable', 'Not detectable');
        }
        return $city;
    }
    
    /**
     * Returns the country name matching to the IP.
     * 
     * @return string
     */
    public function getIPCountryAdmin() : string
    {
        $country = $this->IPCountry;
        if (empty($country)) {
            $country = _t('SilverCart.NotDetectable', 'Not detectable');
        }
        return $country;
    }
}