<?php

/**
 * abstract for a business customer which has own attributes.
 * They are treated differently when it comes to billing
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 23.10.2010
 * @license BSD
 */
class BusinessCustomer extends Member {

    public static $db = array(
        'UmsatzsteuerID' => 'VarChar'
    );

    /**
     * default instances related with $this
     *
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        // Create an own group for this class. The group is identified by "Code", so its name can be changed via backend.
        if (!DataObject::get_one('Group', "\"Code\" = 'b2b'")) {
            $group = new Group();
            $group->Title = _t('BusinessCustomer.BUSINESSCUSTOMER', 'business customer');
            $group->Code = "b2b";
            $group->write();
        }
    }
}
