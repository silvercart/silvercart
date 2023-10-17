<?php

namespace SilverCart\Security;

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
 * @property string $Email  Email address used for request password attempt.
 * @property string $IP     IP address of user attempting to request password
 * @property string $Status Status (success or failure)
 */
class LostPasswordAttempt extends DataObject
{
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
        'Email'  => 'Varchar(255)',
        'IP'     => 'Varchar(255)',
        'Status' => "Enum('Success,Failure')",
    ];
    /**
     * DB table name
     * 
     * @var string
     */
    private static $table_name = "LostPasswordAttempt";
}