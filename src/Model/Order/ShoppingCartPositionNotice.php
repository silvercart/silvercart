<?php

namespace SilverCart\Model\Order;

use SilverCart\Dev\Tools;
use SilverCart\Model\Order\ShoppingCartPosition;
use SilverStripe\ORM\FieldType\DBHTMLText;

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
        $notices = [
            self::NOTICE_CODE_ADJUSTED  => [
                'text' => ShoppingCartPosition::singleton()->fieldLabel('QuantityAdjusted'),
                'type' => 'hint'
            ],
            self::NOTICE_CODE_DELETED => [
                'text' => ShoppingCartPosition::singleton()->fieldLabel('PositionDeleted'),
                'type' => 'hint'
            ],
            self::NOTICE_CODE_REMAINING => [
                'text' => ShoppingCartPosition::singleton()->fieldLabel('RemainingQuantityAdded'),
                'type' => 'hint'
            ],
            self::NOTICE_CODE_MAX_QUANTITY_REACHED => [
                'text' => ShoppingCartPosition::singleton()->fieldLabel('MaxQuantityReached'),
                'type' => 'hint'
            ],
        ];
        if (array_key_exists($code, $notices)) {
            $text = $notices[$code]['text'];
        }
        return $text;   
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
     * deletes only one specific position notice.
     * 
     * @param integer $positionID the positions id
     * @param string  $code       the code to identify the message 
     * 
     * @return bool Was the notice unset?
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 7.8.2011
     */
    public static function unsetNotice($positionID, $code) : bool
    {
        $notices = Tools::Session()->get("position".$positionID);
        if (array_key_exists('codes', $notices)) {
            unset ($notices[$code]);
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