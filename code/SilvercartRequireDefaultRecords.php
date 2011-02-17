<?php

/**
 * collects all default records to avoid redundant code when it comes to relations
 * you do not need to search for other default records, they are all here
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 16.02.2011
 * @license BSD
 */
class SilvercartRequireDefaultRecords extends DataObject {

    /**
     * create default records
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.02.2011
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        // Create an own group for SilvercartAnonymousCustomer. The group is identified by "Code", so its name can be changed via backend.
        $anonymousGroup = DataObject::get_one('Group', "`Code` = 'anonymous'");
        if (!$anonymousGroup) {
            $anonymousGroup = new Group();
            $anonymousGroup->Title = _t('SilvercartAnonymousCustomer.ANONYMOUSCUSTOMER', 'anonymous customer');
            $anonymousGroup->Code = "anonymous";
            $anonymousGroup->write();
        }

        // Create an own group for this class. The group is identified by "Code", so its name can be changed via backend.
        if (!DataObject::get_one('Group', "`Code` = 'b2b'")) {
            $B2Bgroup = new Group();
            $B2Bgroup->Title = _t('SilvercartBusinessCustomer.BUSINESSCUSTOMER', 'business customer');
            $B2Bgroup->Code = "b2b";
            $B2Bgroup->write();
        }

        //create a carrier and an associated zone
        if (!DataObject::get('SilvercartCarrier')) {
            $carrier = new SilvercartCarrier();
            $carrier->Title = 'DHL';
            $carrier->FullTitle = 'DHL International GmbH';
            $carrier->write();

            //relate carrier to zones
            $domestic = DataObject::get_one("SilvercartZone", sprintf("`Title` = '%s'", _t('SilvercartZone.DOMESTIC', 'domestic')));
            if (!$domestic) {
                $domestic = new SilvercartZone();
                $domestic->Title = _t('SilvercartZone.DOMESTIC', 'domestic');
            }
            $domestic->SilvercartCarrierID = $carrier->ID;
            $domestic->write();

            $eu = DataObject::get_one("SilvercartZone", "`Title` = 'EU'");
            if (!$eu) {
                $eu = new SilvercartZone();
                $eu->Title = 'EU';
            }
            $eu->SilvercartCarrierID = $carrier->ID;
            $eu->write();

            /**
             * @todo create countries and relate them to zones
             */


            // relate ShippingMethod to Carrier (if exists)
            $shippingMethod = DataObject::get_one("SilvercartShippingMethod", "`Title` = 'Paket'");
            if ($shippingMethod) {
                $shippingMethod->SilvercartCarrierID = $carrier->ID;
                $shippingMethod->write();
            }
        }
    }

}

