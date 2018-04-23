<?php

namespace SilverCart\Model\Order;

use SilverCart\Dev\Tools;
use SilverCart\Model\Order\ShoppingCartPosition;

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
class ShoppingCartPositionNotice {

    /**
     * Holds an array with possible notices that are selected with a $code
     * A notice can have a type: hint, error, warning
     * 
     * @param string $code Code to identify the notice text
     * 
     * @return array|false the translated notice and a notice type
     */
    public static function getNoticeText($code) {
        $notices = array(
            'adjusted'  => array(
                'text' => ShoppingCartPosition::singleton()->fieldLabel('QuantityAdjusted'),
                'type' => 'hint'
                ),
            'remaining' => array(
                'text' => ShoppingCartPosition::singleton()->fieldLabel('RemainingQuantityAdded'),
                'type' => 'hint'
            ),
            'maxQuantityReached' => array(
                'text' => ShoppingCartPosition::singleton()->fieldLabel('MaxQuantityReached'),
                'type' => 'hint'
            ),
        );
        if (array_key_exists($code, $notices)) {
            return $notices[$code]['text'];
        }
        return false;   
    }
    
    /**
     * adds a notice to a position
     * 
     * @param integer $positionID object id of the position
     * @param string  $code       message identifier 
     * 
     * @return void
     */
    public static function setNotice($positionID, $code) {
        $notice = array(
            'codes'       => array($code)
        );
        //merge existing notices for the position
        if (Tools::Session()->get("position".$positionID)) {
            $existingPositionCodes = Tools::Session()->get("position".$positionID);
            $codes = array_merge($existingPositionCodes['codes'], $notice['codes']);
            $notice['codes'] = array_unique($codes);
        }
        Tools::Session()->set("position".$positionID, $notice);
        Tools::saveSession();
    }
    
    /**
     * deletes only one specific position notice.
     * 
     * @param integer $positionID the positions id
     * @param string  $code       the code to identify the message 
     * 
     * @return boolean Was the notice unset?
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 7.8.2011
     */
    public static function unsetNotice($positionID, $code) {
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
    public static function unsetNotices($positionID) {
        Tools::Session()->clear("position".$positionID);
        Tools::saveSession();
    }
}

