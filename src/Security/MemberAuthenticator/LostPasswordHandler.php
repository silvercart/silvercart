<?php

namespace SilverCart\Security\MemberAuthenticator;

use SilverCart\Dev\Tools;
use SilverCart\Model\ShopEmail;
use SilverCart\Security\LostPasswordAttempt;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Security\MemberAuthenticator\LostPasswordForm;
use SilverStripe\Security\MemberAuthenticator\LostPasswordHandler as SilverStripeLostPasswordHandler;
use SilverStripe\Security\Security;

/**
 * Alternative class for SilverStripe LostPasswordHandler.
 * Overwrites the email handling.
 * 
 * @package SilverCart
 * @subpackage Security\MemberAuthenticator
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 17.05.2019
 * @copyright 2019 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class LostPasswordHandler extends SilverStripeLostPasswordHandler
{
    /**
     * Forgot password form handler method.
     * Called when the user clicks on "I've lost my password".
     * Extensions can use the 'forgotPassword' method to veto executing
     * the logic, by returning FALSE. In this case, the user will be redirected back
     * to the form without further action. It is recommended to set a message
     * in the form detailing why the action was denied.
     *
     * @param array            $data Submitted data
     * @param LostPasswordForm $form Form
     * 
     * @return HTTPResponse
     */
    public function forgotPassword($data, $form)
    {
        // Run a first pass validation check on the data
        $dataValidation = $this->validateForgotPasswordData($data, $form);
        if ($dataValidation instanceof HTTPResponse) {
            return $dataValidation;
        }

        /** @var Member $member */
        $member = $this->getMemberFromData($data);

        // Allow vetoing forgot password requests
        $results = $this->extend('forgotPassword', $member);
        if ($results && is_array($results) && in_array(false, $results ?? [], true)) {
            return $this->redirectToLostPassword();
        }
        
        $attempt        = LostPasswordAttempt::create();
        $attempt->Email = $data['Email'];
        $attempt->IP    = $this->getRequest()->getIP();
        if ($member) {
            $token = $member->generateAutologinTokenAndStoreHash();

            $this->sendEmail($member, $token);
            $attempt->Status = LostPasswordAttempt::SUCCESS;
        } else {
            $attempt->Status = LostPasswordAttempt::FAILURE;
        }
        $attempt->write();

        return $this->redirectToSuccess($data);
    }

    /**
     * Send the email to the member that requested a reset link.
     * 
     * @param Member $member Member
     * @param string $token  Token
     * 
     * @return bool
     */
    protected function sendEmail($member, $token) : bool
    {
        $variables                      = $member->toMap();
        $variables['Member']            = $member;
        $variables['PasswordResetLink'] = Director::absoluteURL(Security::getPasswordResetLink($member, $token));
        $memberDbFields                 = (array) $member->config()->db;
        foreach ($memberDbFields as $dbFieldName => $dbFieldType) {
            if (!array_key_exists($dbFieldName, $variables)) {
                $variables[$dbFieldName] = $member->{$dbFieldName};
            }
        }
        $variables['SalutationText'] = Tools::getSalutationText($variables['Salutation']);
        $variables['InvoiceAddress'] = $member->InvoiceAddress();
        return ShopEmail::send(
                'ChangePassword',
                $member->Email,
                $variables,
                [],
                $member->Locale
        );
    }
}