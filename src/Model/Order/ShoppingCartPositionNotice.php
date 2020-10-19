<?php

namespace SilverCart\Model\Order;

use SilverCart\Dev\Tools;
use SilverCart\Model\Order\ShoppingCartPosition;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\ArrayData;

/**
 * Contains a couple of static methods for shopping cart related notices for the
 * customer. They are manages via the session.
 *
 * @package SilverCart
 * @subpackage Model_Order
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ShoppingCartPositionNotice
{
    const NOTICE_CODE_ADJUSTED             = 'adjusted';
    const NOTICE_CODE_DELETED              = 'deleted';
    const NOTICE_CODE_MAX_QUANTITY_REACHED = 'maxQuantityReached';
    const NOTICE_CODE_REMAINING            = 'remaining';
    const NOTICE_TYPE_DANGER               = 'danger';
    const NOTICE_TYPE_INFO                 = 'info';
    const NOTICE_TYPE_SUCCESS              = 'success';
    const NOTICE_TYPE_WARNING              = 'warning';
    const NOTICE_FA_EXCLAMATION_CIRCLE     = 'exclamation-circle';
    const NOTICE_FA_CHECK                  = 'check';
    const NOTICE_FA_INFO_CIRCLE            = 'info-circle';
    const SESSION_KEY_ADDITIONAL_NOTICES   = 'SilverCart.AdditionalShoppingCartPositionNotices';
    
    /**
     * Adds a key value pair of allowed notices.
     * 
     * @param string $code Code
     * @param string $text Text
     * @param string $icon Icon
     * 
     * @return void
     */
    public static function addAllowedNotice(string $code, string $text, string $type = self::NOTICE_TYPE_WARNING, string $icon = self::NOTICE_FA_EXCLAMATION_CIRCLE) : void
    {
        $notices = (array) Tools::Session()->get(self::SESSION_KEY_ADDITIONAL_NOTICES);
        Tools::Session()->set(self::SESSION_KEY_ADDITIONAL_NOTICES, array_merge($notices, [
            $code => [
                'text' => $text,
                'type' => $type,
                'icon' => $icon,
            ],
        ]));
        Tools::saveSession();
    }

    /**
     * Returns the allowed notices.
     * 
     * @return array
     */
    public static function getAllowedNotices() : array
    {
        return array_merge([
            self::NOTICE_CODE_ADJUSTED  => [
                'text' => ShoppingCartPosition::singleton()->fieldLabel('QuantityAdjusted'),
                'type' => self::NOTICE_TYPE_WARNING
            ],
            self::NOTICE_CODE_DELETED => [
                'text' => ShoppingCartPosition::singleton()->fieldLabel('PositionDeleted'),
                'type' => self::NOTICE_TYPE_WARNING
            ],
            self::NOTICE_CODE_REMAINING => [
                'text' => ShoppingCartPosition::singleton()->fieldLabel('RemainingQuantityAdded'),
                'type' => self::NOTICE_TYPE_WARNING
            ],
            self::NOTICE_CODE_MAX_QUANTITY_REACHED => [
                'text' => ShoppingCartPosition::singleton()->fieldLabel('MaxQuantityReached'),
                'type' => self::NOTICE_TYPE_WARNING
            ],
        ], (array) Tools::Session()->get(self::SESSION_KEY_ADDITIONAL_NOTICES));
    }

    /**
     * Holds an array with possible notices that are selected with a $code
     * A notice can have a type: hint, error, warning
     * 
     * @param string $code Code to identify the notice text
     * 
     * @return string
     */
    public static function getNoticeText(string $code) : string
    {
        $text    = '';
        $notices = self::getAllowedNotices();
        if (array_key_exists($code, $notices)) {
            $text = $notices[$code]['text'];
        }
        return $text;   
    }
    
    /**
     * Returns the notice type for the given $code.
     * 
     * @param string $code Code to get type for
     * 
     * @return string
     */
    public static function getNoticeType(string $code) : string
    {
        $type    = self::NOTICE_TYPE_WARNING;
        $notices = self::getAllowedNotices();
        if (array_key_exists($code, $notices)) {
            $type = $notices[$code]['type'];
        }
        return $type;   
    }
    
    /**
     * Returns the notice icon for the given $code.
     * 
     * @param string $code Code to get icon for
     * 
     * @return string
     */
    public static function getNoticeIcon(string $code) : string
    {
        $icon    = self::NOTICE_TYPE_WARNING;
        $notices = self::getAllowedNotices();
        if (array_key_exists($code, $notices)
         && is_array($notices[$code])
         && array_key_exists('icon', $notices[$code])
        ) {
            $icon = $notices[$code]['icon'];
        }
        return (string) $icon;   
    }
    
    /**
     * adds a notice to a position
     * 
     * @param integer $positionID object id of the position
     * @param string  $code       message identifier 
     * 
     * @return void
     */
    public static function setNotice(int $positionID, string $code) : void
    {
        $notice = [
            'codes' => [$code]
        ];
        //merge existing notices for the position
        if (Tools::Session()->get("position".$positionID)) {
            $existingPositionCodes = Tools::Session()->get("position".$positionID);
            $codes                 = array_merge($existingPositionCodes['codes'], $notice['codes']);
            $notice['codes']       = array_unique($codes);
        }
        Tools::Session()->set("position".$positionID, $notice);
        Tools::saveSession();
    }
    
    /**
     * Returns the notices for the given position ID.
     * 
     * @param int $positionID Position ID
     * 
     * @return DBHTMLText
     */
    public static function getNotices(int $positionID) : DBHTMLText
    {
        $text    = "";
        $notices = (array) Tools::Session()->get("position".$positionID);
        if (array_key_exists('codes', $notices)) {
            foreach ($notices['codes'] as $code) {
                $text .= ShoppingCartPositionNotice::getNoticeText($code) . "<br />";
            }
            ShoppingCartPositionNotice::unsetNotices($positionID);
        }
        return Tools::string2html($text);
    }
    
    /**
     * Returns a list of notices for the given position ID.
     * 
     * @param int $positionID Position ID
     * 
     * @return ArrayList
     */
    public static function getNoticesList(int $positionID) : ArrayList
    {
        $list = ArrayList::create();
        $notices = (array) Tools::Session()->get("position".$positionID);
        if (array_key_exists('codes', $notices)) {
            foreach ($notices['codes'] as $code) {
                $list->push(ArrayData::create([
                    'Notice' => DBHTMLText::create()->setValue(ShoppingCartPositionNotice::getNoticeText($code)),
                    'Type'   => self::getNoticeType($code),
                    'Icon'   => self::getNoticeIcon($code),
                ]));
            }
            ShoppingCartPositionNotice::unsetNotices($positionID);
        }
        return $list;
    }
    
    /**
     * Returns whether there are notices for the given position ID.
     * 
     * @param int $positionID Position ID
     * 
     * @return bool
     */
    public static function hasNotices(int $positionID) : bool
    {
        $notices = (array) Tools::Session()->get("position".$positionID);
        return !empty($notices);
    }
    
    /**
     * Deletes only one specific position notice.
     * 
     * @param int    $positionID The positions id
     * @param string $code       The code to identify the message 
     * 
     * @return bool
     */
    public static function unsetNotice(int $positionID, string $code) : bool
    {
        $notices = (array) Tools::Session()->get("position".$positionID);
        if (array_key_exists('codes', $notices)
         && in_array($code, $notices['codes'])
        ) {
            unset ($notices['codes'][array_search($code, $notices['codes'])]);
            if (empty($notices['codes'])) {
                $notices = [];
            }
            Tools::Session()->clear("position".$positionID);
            Tools::Session()->set("position".$positionID, $notices);
            Tools::saveSession();
            return true;
        }
        return false;
    }
    
    /**
     * deletes all notices of a position.
     * 
     * @param integer $positionID the positions id
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 7.8.2011
     */
    public static function unsetNotices($positionID) : void
    {
        Tools::Session()->clear("position".$positionID);
        Tools::saveSession();
    }
}