<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Base
 */

/**
 * Bundles newsletter related functionality.
 *
 * @package Silvercart
 * @subpackage Base
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 25.08.2011
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartNewsletter extends DataObject {
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
    }
    
    /**
     * Adds a member to the newsletter subscribers list.
     *
     * @param Member $member The member to subscribe 
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public static function subscribeRegisteredCustomer($member) {
        $subscribed = false;
        
        if ($member instanceof Member) {
            
            if ($member->NewsletterOptInStatus) {
                // Opt-in is done, so subscribe to newsletter
                $member->SubscribedToNewsletter = true;
            } else {
                // Opt-in has to be done first
                $confirmationHash = self::createConfirmationHash($member->Salutation, $member->FirstName, $member->Surname, $member->Email);
                $member->setField('NewsletterConfirmationHash', Convert::raw2sql($confirmationHash));
                self::sendOptInEmailTo($member->Salutation, $member->FirstName, $member->Surname, $member->Email, $confirmationHash);
            }
            $member->write();
            $subscribed = true;
        }
        
        return $subscribed;
    }
 
    /**
     * Removes a member from the newsletter subscribers list.
     *
     * @param Member $member The member to unsubscribe 
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public static function unSubscribeRegisteredCustomer($member) {
        $unSubscribed = false;
        
        if ($member instanceof Member) {
            $member->SubscribedToNewsletter     = false;
            $member->NewsletterConfirmationHash = '';
            $member->NewsletterOptInStatus      = false;
            $member->write();
            $unSubscribed = true;
        }
        
        return $unSubscribed;
    }
    
    /**
     * Adds an anonymous customer to the newsletter subscribers list.
     *
     * @param string $salutation The salutation to use
     * @param string $firstName  The first name to use
     * @param string $surName    The last name to use
     * @param string $email      The email address to use
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public static function subscribeAnonymousCustomer($salutation, $firstName, $surName, $email) {
        $doOptIn = true;
        
        if (SilvercartAnonymousNewsletterRecipient::doesExist($email) &&
            SilvercartAnonymousNewsletterRecipient::isOptInDoneFor($email)) {
            
            $doOptIn = false;
        }
        
        if ($doOptIn) {
            $confirmationHash = self::createConfirmationHash($salutation, $firstName, $surName, $email);
            SilvercartAnonymousNewsletterRecipient::add($salutation, $firstName, $surName, $email, false, Convert::raw2sql($confirmationHash));
            self::sendOptInEmailTo($salutation, $firstName, $surName, $email, $confirmationHash);
        } else {
            SilvercartAnonymousNewsletterRecipient::add($salutation, $firstName, $surName, $email, true);
        }
    }
    
    /**
     * Removes an anonymous customer from the newsletter subscribers list.
     *
     * @param string $email The email address whose entry should be removed from
     *                      the recipient list.
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public static function unSubscribeAnonymousCustomer($email) {
        SilvercartAnonymousNewsletterRecipient::removeByEmailAddress($email);
    }
    
    /**
     * Checks if the given email address is allocated by a registered
     * regular customer.
     * 
     * @param string $email The email address to check
     * 
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public static function isEmailAllocatedByRegularCustomer($email) {
        $emailIsAllocated = false;
        $regularCustomer  = DataObject::get_one(
            'Member',
            sprintf(
                "Email = '%s'",
                $email
            )
        );
        
        if ( $regularCustomer &&
            ($regularCustomer->Groups()->find('Code', 'b2b') ||
             $regularCustomer->Groups()->find('Code', 'b2c'))
            ) {
            
            $emailIsAllocated = true;
        }
        
        return $emailIsAllocated;
    }
    
    /**
     * Checks if the given email address is allocated by an anonymous
     * newsletter subscriber.
     *
     * @param string $email The email address to check
     * 
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public static function isEmailAllocatedByAnonymousRecipient($email) {
        $emailIsAllocated   = false;
        $anonymousRecipient = SilvercartAnonymousNewsletterRecipient::getByEmailAddress($email);
        
        if ($anonymousRecipient) {
            $emailIsAllocated = true;
        }
        
        return $emailIsAllocated;
    }
    
    /**
     * Sends an email with opt-in link to the given address.
     *
     * @param string $salutation       The salutation to use
     * @param string $firstName        The first name to use
     * @param string $surName          The last name to use
     * @param string $email            The email address to use
     * @param string $confirmationHash The hash value to use for identification
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public static function sendOptInEmailTo($salutation, $firstName, $surName, $email, $confirmationHash) {
        SilvercartShopEmail::send(
            'NewsletterOptIn',
            $email,
            array(
                'Salutation'        => $salutation,
                'FirstName'         => $firstName,
                'Surname'           => $surName,
                'Email'             => $email,
                'ConfirmationLink'  => Director::absoluteURL(SilvercartPage_Controller::PageByIdentifierCode("SilvercartNewsletterOptInConfirmationPage")->Link()).'?h='.urlencode($confirmationHash)
            )
        );
    }
    
    /**
     * Creates a hash from the given parameters and returns it.
     *
     * @param string $salutation The salutation to use
     * @param string $firstName  The first name to use
     * @param string $surName    The last name to use
     * @param string $email      The email address to use
     * 
     * @return string The created hash
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public static function createConfirmationHash($salutation, $firstName, $surName, $email) {
        $confirmationHash = md5(
            mktime() .
            rand() .
            $salutation .
            $email .
            $firstName .
            $surName
        );
        
        return $confirmationHash;
    }
}