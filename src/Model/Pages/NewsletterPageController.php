<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Forms\NewsletterForm;
use SilverCart\Model\ShopEmail;
use SilverCart\Model\Newsletter\AnonymousNewsletterRecipient;
use SilverCart\Model\Pages\MetaNavigationHolderController;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;

/**
 * NewsletterPage Controller class.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class NewsletterPageController extends MetaNavigationHolderController {

    /**
     * List of allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = array(
        'NewsletterForm',
        'optin',
        'thanks',
    );
    
    /**
     * Opt-In message.
     *
     * @var string
     */
    protected $optInMessage = '';

    /**
     * Returns the NewsletterForm.
     *
     * @return NewsletterForm
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.11.2017
     */
    public function NewsletterForm() {
        $form = new NewsletterForm($this);
        return $form;
    }
    
    /**
     * Opt-In action.
     * 
     * @param HTTPRequest $request HTTP request
     * 
     * @return string
     */
    public function optin(HTTPRequest $request) {
        $newsletterPage      = $this->data();
        $statusMessage       = $newsletterPage->ConfirmationFailureMessage;
        $isAnonymousCustomer = true;
        $optInHash           = $request->getVar('h');

        if (!is_null($optInHash)) {
            $recipient = AnonymousNewsletterRecipient::getByHash($optInHash);

            if (!($recipient instanceof AnonymousNewsletterRecipient) ||
                !$recipient->exists()) {
                $recipient = Member::get()->filter('NewsletterConfirmationHash', $optInHash)->first();
                if ($recipient instanceof Member &&
                    $recipient->exists()) {
                    $isAnonymousCustomer = false;
                }
            }

            if ($recipient instanceof DataObject &&
                $recipient->exists()) {
                if ($recipient->NewsletterOptInStatus) {
                    $statusMessage = $newsletterPage->AlreadyConfirmedMessage;
                } else {
                    if ($isAnonymousCustomer) {
                        AnonymousNewsletterRecipient::doConfirmationByHash($optInHash);
                    } else {
                        $recipient->NewsletterOptInStatus  = true;
                        $recipient->SubscribedToNewsletter = true;
                        $recipient->write();
                    }
                    $statusMessage = $newsletterPage->ConfirmationSuccessMessage;

                    $this->sendConfirmationMail(
                        $recipient->Salutation,
                        $recipient->FirstName,
                        $recipient->Surname,
                        $recipient->Email
                    );
                }
            }
        }

        $this->setOptInMessage(Tools::string2html($statusMessage));
        return $this->render();
    }
    
    /**
     * Returns the opt-in message.
     * 
     * @return string
     */
    public function getOptInMessage() {
        return $this->optInMessage;
    }

    /**
     * Sets the opt-in message.
     * 
     * @param string $optInMessage Opt-In message
     * 
     * @return void
     */
    public function setOptInMessage($optInMessage) {
        $this->optInMessage = $optInMessage;
    }

    /**
     * Send confirmation mail to customer
     *
     * @param string $salutation Salutation
     * @param string $firstname  Firstname
     * @param string $surname    Surname
     * @param string $email      Email
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.10.2010
     */
    public function sendConfirmationMail($salutation, $firstname, $surname, $email) {
        ShopEmail::send(
            'NewsletterOptInConfirmation',
            $email,
            array(
                'Salutation' => $salutation,
                'FirstName'  => $firstname,
                'Surname'    => $surname,
                'Email'      => $email
            )
        );
    }
    
}