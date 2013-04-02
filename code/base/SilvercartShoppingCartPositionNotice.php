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
 * @subpackage Base
 */

/**
 * Contains a couple of static methods for shopping cart related notices for the
 * customer. They are manages via the session.
 *
 * @package Silvercart
 * @subpackage Base
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 07.08.2011
 * @license see license file in modules root directory
 */
class SilvercartShoppingCartPositionNotice extends SilvercartNotice {

    /**
     * Holds an array with possible notices that are selected with a $code
     * A notice can have a type: hint, error, warning
     * 
     * @param string $code Code to identify the notice text
     * 
     * @return array|false the translated notice and a notice type
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 7.8.2011
     */
    public static function getNoticeText($code) {
        $notices = array(
            'adjusted'  => array(
                'text' => _t('SilvercartShoppingCartPosition.QUANTITY_ADJUSTED_MESSAGE'),
                'type' => 'hint'
                ),
            'remaining' => array(
                'text' => _t('SilvercartShoppingCartPosition.REMAINING_QUANTITY_ADDED_MESSAGE'),
                'type' => 'hint'
            ),
            'maxQuantityReached' => array(
                'text' => _t('SilvercartShoppingCartPosition.MAX_QUANTITY_REACHED_MESSAGE'),
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
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 7.8.2011
     */
    public static function setNotice($positionID, $code) {
        $notice = array(
            'codes'       => array($code)
        );
        //merge existing notices for the position
        if (Session::get("position".$positionID)) {
            $existingPositionCodes = Session::get("position".$positionID);
            $codes = array_merge($existingPositionCodes['codes'], $notice['codes']);
            $notice['codes'] = array_unique($codes);
        }
        Session::set("position".$positionID, $notice);
        Session::save();
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
        $notices = Session::get("position".$positionID);
        if (array_key_exists('codes', $notices)) {
            unset ($notices[$code]);
            Session::set("position".$positionID, $notices);
            Session::save();
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
        Session::clear("position".$positionID);
        Session::save();
    }
}

