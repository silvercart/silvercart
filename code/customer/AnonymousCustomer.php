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
class AnonymousCustomer extends Member {

    /**
     * default instances related to $this
     *
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        // Create an own group for this class. The group is identified by "Code", so its name can be changed via backend.
        $group = DataObject::get_one('Group', "\"Code\" = 'anonymous'");
        
        if (!$group) {
            $group = new Group();
            $group->Title = "anonyme Kunden";
            $group->Code = "anonymous";
            $group->write();
        }
    }

    /**
     * distinguish customer classes
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
            if (($memberClass == "AnonymousCustomer")) {
                return $member;
            }
        } else {
            return false;
        }
    }
}
