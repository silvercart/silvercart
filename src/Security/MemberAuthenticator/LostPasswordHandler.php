<?php

namespace SilverCart\Security\MemberAuthenticator;

use SilverCart\Dev\Tools;
use SilverCart\Model\ShopEmail;
use SilverStripe\Control\Director;
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
     * Send the email to the member that requested a reset link.
     * 
     * @param Member $member Member
     * @param string $token  Token
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.05.2019
     */
    protected function sendEmail($member, $token) : bool
    {
        $variables                      = $member->toMap();
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