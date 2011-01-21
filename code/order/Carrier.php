<?php

/**
 * abstract for a shipping carrier
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 06.11.2010
 * @license BSD
 */
class Carrier extends DataObject {

    public static $singular_name = "Frachtführer";
    public static $plural_name = "Frachtführer";
    public static $db = array(
        'Title' => 'VarChar(25)',
        'FullTitle' => 'VarChar(60)'
    );
    public static $has_many = array(
        'shippingMethods' => 'ShippingMethod',
        'zones' => 'Zone'
    );
    public static $field_labels = array(
        'Title' => 'Name',
        'FullTitle' => 'voller Name'
    );

    /**
     * default instances will be created if no instance exists at all
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 7.11.2010
     * @return void
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();
        if (!DataObject::get('Carrier')) {
            $carrier = new Carrier();
            $carrier->Title = 'DHL';
            $carrier->FullTitle = 'DHL International GmbH';
            $carrier->write();

            /**
             * the carrier DHL has at least one shipping method
             */
            if (!DataObject::get('ShippingMethod')) {
                $shippingMethod = new ShippingMethod();
                $shippingMethod->Title = 'Paket';
                $shippingMethod->write();
                $shippingMethod->carrierID = $carrier->ID;
                $shippingMethod->write();
            }
        }
    }

}

