<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Forms\NewsletterForm;
use SilverCart\Model\ShopEmail;
use SilverCart\Model\Newsletter\AnonymousNewsletterRecipient;
use SilverCart\Model\Pages\MetaNavigationHolderController;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;

/**
 * NewsletterPage Controller class.
 *
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class NewsletterPageController extends MetaNavigationHolderController
{
    /**
     * List of allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = [
        'NewsletterForm',
        'optin',
        'thanks',
    ];
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
     */
    public function NewsletterForm() : NewsletterForm
    {
        $form = NewsletterForm::create($this);
        return $form;
    }
    
    /**
     * Opt-In action.
     * 
     * @param HTTPRequest $request HTTP request
     * 
     * @return HTTPResponse
     */
    public function optin(HTTPRequest $request) : HTTPResponse
    {
        $newsletterPage      = $this->data();
        $statusMessage       = $newsletterPage->ConfirmationFailureMessage;
        $isAnonymousCustomer = true;
        $optInHash           = $request->getVar('h');
        if (!is_null($optInHash)) {
            $recipient = AnonymousNewsletterRecipient::getByHash($optInHash);
            if (!($recipient instanceof AnonymousNewsletterRecipient)
             || !$recipient->exists()
            ) {
                $recipient = Member::get()->filter('NewsletterConfirmationHash', $optInHash)->first();
                if ($recipient instanceof Member
                 && $recipient->exists()
                ) {
                    $isAnonymousCustomer = false;
                }
            }
            if ($recipient instanceof DataObject
             && $recipient->exists()
            ) {
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
                        $recipient->Email,
                        $recipient
                    );
                }
            }
        }
        $this->setOptInMessage(Tools::string2html($statusMessage));
        return HTTPResponse::create($this->render());
    }
    
    /**
     * Returns the opt-in message.
     * 
     * @return string
     */
    public function getOptInMessage() : string
    {
        return (string) $this->optInMessage;
    }

    /**
     * Sets the opt-in message.
     * 
     * @param string $optInMessage Opt-In message
     * 
     * @return void
     */
    public function setOptInMessage(string $optInMessage) : void
    {
        $this->optInMessage = $optInMessage;
    }

    /**
     * Send confirmation mail to customer
     *
     * @param string                              $salutation Salutation
     * @param string                              $firstname  Firstname
     * @param string                              $surname    Surname
     * @param string                              $email      Email
     * @param AnonymousNewsletterRecipient|Member $recipient  Recipient
     *
     * @return void
     */
    public function sendConfirmationMail(string $salutation, string $firstname, string $surname, string $email, AnonymousNewsletterRecipient|Member $recipient = null) : void
    {
        ShopEmail::send(
            'NewsletterOptInConfirmation',
            $email,
            [
                'Member'     => $recipient,
                'Salutation' => $salutation,
                'FirstName'  => $firstname,
                'Surname'    => $surname,
                'Email'      => $email
            ]
        );
    }
}