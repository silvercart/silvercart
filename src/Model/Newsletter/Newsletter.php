<?php

namespace SilverCart\Model\Newsletter;

use SilverCart\Dev\Tools;
use SilverCart\Model\Newsletter\AnonymousNewsletterRecipient;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\ShopEmail;
use SilverStripe\Control\Director;
use SilverStripe\Core\Convert;
use SilverStripe\Security\Member;
use SilverStripe\Forms\FormField;

/**
 * Bundles newsletter related functionality.
 *
 * @package SilverCart
 * @subpackage Model_Newsletter
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Newsletter {
    
    use \SilverStripe\Core\Extensible;
    use \SilverStripe\Core\Injector\Injectable;

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function fieldLabels() {
        $fieldLabels = [
            'OptInNotFinished'   => _t(Newsletter::class . '.OPTIN_NOT_FINISHED_MESSAGE', 'You\'ll be on the newsletter recipients list after clicking on the link in the opt-in mail we sent you.'),
            'YouAreSubscribed'   => _t(Newsletter::class . '.SUBSCRIBED', 'You are subscribed to the newsletter'),
            'YouAreUnsubscribed' => _t(Newsletter::class . '.UNSUBSCRIBED', 'You are not subscribed to the newsletter'),
        ];

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Get a human-readable label for a single field,
     * see {@link fieldLabels()} for more details.
     *
     * @uses fieldLabels()
     * @uses FormField::name_to_label()
     *
     * @param string $name Name of the field
     * 
     * @return string Label of the field
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function fieldLabel($name) {
        $labels = $this->fieldLabels();
        return (isset($labels[$name])) ? $labels[$name] : FormField::name_to_label($name);
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
    public static function subscribeRegisteredCustomer($member, $useDoubleOptIn = true) {
        $subscribed = false;
        
        if ($member instanceof Member) {
            if (!$useDoubleOptIn) {
                $member->NewsletterOptInStatus = true;
            }
            
            if ($member->NewsletterOptInStatus) {
                // Opt-in is done, so subscribe to newsletter
                $member->SubscribedToNewsletter = true;
            } else {
                // Opt-in has to be done first
                $confirmationHash = self::createConfirmationHash($member->Salutation, $member->FirstName, $member->Surname, $member->Email);
                $member->setField('NewsletterConfirmationHash', Convert::raw2sql($confirmationHash));
                self::sendOptInEmailTo($member->Salutation, $member->FirstName, $member->Surname, $member->Email, $confirmationHash, $member->Locale);
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
    public static function subscribeAnonymousCustomer($salutation, $firstName, $surName, $email, $useDoubleOptIn = true) {
        $doOptIn = true;
        
        if (!$useDoubleOptIn ||
            (AnonymousNewsletterRecipient::doesExist($email) &&
             AnonymousNewsletterRecipient::isOptInDoneFor($email))) {
            
            $doOptIn = false;
        }
        
        if ($doOptIn) {
            $confirmationHash = self::createConfirmationHash($salutation, $firstName, $surName, $email);
            AnonymousNewsletterRecipient::add($salutation, $firstName, $surName, $email, false, Convert::raw2sql($confirmationHash));
            self::sendOptInEmailTo($salutation, $firstName, $surName, $email, $confirmationHash);
        } else {
            AnonymousNewsletterRecipient::add($salutation, $firstName, $surName, $email, true);
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
        AnonymousNewsletterRecipient::removeByEmailAddress($email);
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
        $regularCustomer = Member::get()->filter('Email', $email)->first();
        
        if ($regularCustomer &&
            $regularCustomer->isValidCustomer()) {
            
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
        $anonymousRecipient = AnonymousNewsletterRecipient::getByEmailAddress($email);
        
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
     * @param string $locale           Locale
     * 
     * @return void
     */
    public static function sendOptInEmailTo(string $salutation, string $firstName, string $surName, string $email, string $confirmationHash, string $locale = null) : void
    {
        ShopEmail::send(
                'NewsletterOptIn',
                $email,
                [
                    'Salutation'        => $salutation,
                    'FirstName'         => $firstName,
                    'Surname'           => $surName,
                    'Email'             => $email,
                    'ConfirmationLink'  => Director::absoluteURL(Tools::PageByIdentifierCode(Page::IDENTIFIER_NEWSLETTER_PAGE)->Link('optin')).'?h='.urlencode($confirmationHash)
                ],
                [],
                $locale
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
            time() .
            rand() .
            $salutation .
            $email .
            $firstName .
            $surName
        );
        
        return $confirmationHash;
    }
}