<?php

/**
 * Contains a couple of static methods for shopping cart related notices for the
 * customer. They are manages via the session.
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 07.08.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartShoppingCartPositionNotice extends SilvercartNotice {

    /**
     * Holds an array with possible notices that are selected with a $code
     * A notice can have a type: hint, error, warning
     * 
     * @param string $code
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
            )
        );
        if (array_key_exists($code, $notices)) {
            return $notices[$code]['text'];
        }
        return false;   
    }
    
    /**
     * adds a notice to a position
     * 
     * @return void
     * 
     * @param integer $positionID object id of the position
     * @param string  $code       message identifier 
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
     * @return boolean Was the notice unset?
     * 
     * @param integer $positionID the positions id
     * @param string  $code       the code to identify the message 
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
     * @return void
     * 
     * @param integer $positionID the positions id
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 7.8.2011
     */
    public static function unsetNotices($positionID) {
        Session::clear("position".$positionID);
        Session::save();
    }
}

