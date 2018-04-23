<?php

namespace SilverCart\Model\Newsletter;

use SilverCart\Dev\Tools;
use SilverStripe\ORM\DataObject;

/**
 * This is a datastore for recipients of the newsletter that are not
 * registered or logged in as SilverCart customers.
 *
 * @package SilverCart
 * @subpackage Model_Newsletter
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class AnonymousNewsletterRecipient extends DataObject {

    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = array(
        'Salutation'                        => 'Varchar(20)',
        'FirstName'                         => 'Varchar(50)',
        'Surname'                           => 'Varchar(50)',
        'Email'                             => 'Varchar(100)',
        'NewsletterOptInStatus'             => 'Boolean(0)',
        'NewsletterOptInConfirmationHash'   => 'Varchar(100)'
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartAnonymousNewsletterRecipient';
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 17.07.2012
     */
    public function singular_name() {
        return Tools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 17.07.2012
     */
    public function plural_name() {
        return Tools::plural_name_for($this); 
    }

    /**
     * Add a recipient to the list.
     *
     * @param string $Salutation       The salutation to use
     * @param string $FirstName        The recipients' firstname
     * @param string $Surname          The recipients' surname
     * @param string $Email            The recipients' email address
     * @param string $optInStatus      Optional: the opt-in status, defaults to false
     * @param string $confirmationHash Optional: confirmation hash for this user, defaults to empty string
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.03.2011
     */
    public static function add($Salutation, $FirstName, $Surname, $Email, $optInStatus = false, $confirmationHash = '') {
        if (!empty($Salutation) &&
            !empty($FirstName) &&
            !empty($Surname) &&
            !empty($Email)) {

            $anonymousRecipient = new AnonymousNewsletterRecipient();
            $anonymousRecipient->setField('Salutation',                      $Salutation);
            $anonymousRecipient->setField('FirstName',                       $FirstName);
            $anonymousRecipient->setField('Surname',                         $Surname);
            $anonymousRecipient->setField('Email',                           $Email);
            $anonymousRecipient->setField('NewsletterOptInStatus',           $optInStatus);
            $anonymousRecipient->setField('NewsletterOptInConfirmationHash', $confirmationHash);
            $anonymousRecipient->write();
        }
    }

    /**
     * Sets the opt-in status to true for a recipient with the given hash.
     *
     * @param string $confirmationHash The hash to operate on
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public static function doConfirmationByHash($confirmationHash) {
        $recipient  = self::getByHash($confirmationHash);
        
        if ($recipient) {
            $recipient->setField('NewsletterOptInStatus', true);
            $recipient->write();
        }
    }
    
    /**
     * Remove a recipient from the list by his/her email address.
     *
     * @param string $emailAddress The email address to remove
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.03.2011
     */
    public static function removeByEmailAddress($emailAddress) {
        if (!empty($emailAddress)) {
            $anonymousRecipient = self::getByEmailAddress($emailAddress);
            
            if ($anonymousRecipient) {
                $anonymousRecipient->delete();
            }
        }
    }
    
    /**
     * Checks if a recipient with the given email address exists already.
     *
     * @param string $emailAddress The email address to check for
     *
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public static function doesExist($emailAddress) {
        $recipientExists = false;
        $recipient       = self::getByEmailAddress($emailAddress);
        
        if ($recipient) {
            $recipientExists = true;
        }
        
        return $recipientExists;
    }
    
    /**
     * Checks if the opt-in is done for the recipient with the given email
     * address.
     *
     * @param string $emailAddress The email address to check for
     *
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public static function isOptInDoneFor($emailAddress) {
        $optInIsDone = false;
        $recipient   = self::getByEmailAddress($emailAddress);
        
        if ($recipient &&
            $recipient->NewsletterOptInStatus) {
            
            $optInIsDone = true;
        }
        
        return $optInIsDone;
    }
    
    /**
     * Returns an AnonymousNewsletterRecipient object with the given
     * email address.
     *
     * @param string $emailAddress The email address to get the object for
     *
     * @return AnonymousNewsletterRecipient|boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.06.2014
     */
    public static function getByEmailAddress($emailAddress) {
        $recipient = AnonymousNewsletterRecipient::get()->filter('Email', $emailAddress)->first();
        
        return $recipient;
    }
    
    /**
     * Returns an AnonymousNewsletterRecipient object with the given
     * email address.
     *
     * @param string $confirmationHash ...
     *
     * @return AnonymousNewsletterRecipient|boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.06.2014
     */
    public static function getByHash($confirmationHash) {
        $recipient = AnonymousNewsletterRecipient::get()->filter('NewsletterOptInConfirmationHash', $confirmationHash)->first();
        
        return $recipient;
    }
}