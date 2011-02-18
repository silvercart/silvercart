<?php

/**
 * abstract for a customer that did not log in himself;
 * His cart is stored in the session.
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 23.10.2010
 * @license BSD
 */
class SilvercartAnonymousCustomer extends Member {


    /**
     * distinguish customer classes
     * determin if a customer is an anonymous customer
     *
     * @return Object an instance of AnonymousCustomer of false
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     */
    public static function currentAnonymousCustomer() {
        $id = Member::currentUserID();
        if ($id) {
            $member = DataObject::get_by_id("Member", $id);
            $memberClass = $member->ClassName;
            if (($memberClass == "SilvercartAnonymousCustomer")) {
                return $member;
            }
        } else {
            return false;
        }
    }
}
