<?php
/**
 * Copyright 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Customer
 */

/**
 * This is a datastore for recipients of the newsletter that are not
 * registered or logged in as Silvercart customers.
 *
 * @package Silvercart
 * @subpackage Customer
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 22.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartAnonymousNewsletterRecipient extends DataObject {

    /**
     * Attributes.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.03.2011
     */
    public static $db = array(
        'Salutation'                        => 'Varchar(20)',
        'FirstName'                         => 'VarChar(50)',
        'Surname'                           => 'VarChar(50)',
        'Email'                             => 'VarChar(100)',
        'NewsletterOptInStatus'             => 'Boolean(0)',
        'NewsletterOptInConfirmationHash'   => 'VarChar(100)'
    );

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
     * @copyright 2011 pixeltricks GmbH
     * @since 22.03.2011
     */
    public static function add($Salutation, $FirstName, $Surname, $Email, $optInStatus = false, $confirmationHash = '') {
        if (!empty($Salutation) &&
            !empty($FirstName) &&
            !empty($Surname) &&
            !empty($Email)) {

            $anonymousRecipient = new SilvercartAnonymousNewsletterRecipient();
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
     * @return void
     *
     * @param string $confirmationHash The hash to operate on
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
     * @copyright 2011 pixeltricks GmbH
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
     * @return boolean
     *
     * @param string $emailAddress The email address to check for
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
     * @return boolean
     *
     * @param string $emailAddress The email address to check for
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
     * Returns an SilvercartAnonymousNewsletterRecipient object with the given
     * email address.
     *
     * @return mixed SilvercartAnonymousNewsletterRecipient|boolean false
     *
     * @param string $emailAddress The email address to get the object for
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public static function getByEmailAddress($emailAddress) {
        $recipient  = DataObject::get_one(
            'SilvercartAnonymousNewsletterRecipient',
            sprintf(
                "Email = '%s'",
                $emailAddress
            )
        );
        
        return $recipient;
    }
    
    /**
     * Returns an SilvercartAnonymousNewsletterRecipient object with the given
     * email address.
     *
     * @return mixed SilvercartAnonymousNewsletterRecipient|boolean false
     *
     * @param string $emailAddress The email address to get the object for
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public static function getByHash($confirmationHash) {
        $recipient  = DataObject::get_one(
            'SilvercartAnonymousNewsletterRecipient',
            sprintf(
                "NewsletterOptInConfirmationHash = '%s'",
                $confirmationHash
            )
        );
        
        return $recipient;
    }
}