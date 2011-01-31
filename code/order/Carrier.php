<?php
/**
 * abstract for a shipping carrier
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 06.11.2010
 * @license none
 */
class Carrier extends DataObject {

    /**
     * Singular name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $singular_name = "Frachtführer";

    /**
     * Plural name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $plural_name = "Frachtführer";

    /**
     * Attributes.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $db = array(
        'Title'             => 'VarChar(25)',
        'FullTitle'         => 'VarChar(60)'
    );

    /**
     * Has-many relationship.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $has_many = array(
        'shippingMethods'   => 'ShippingMethod',
        'zones'             => 'Zone'
    );

    /**
     * Column labels for display in tables.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $field_labels = array(
        'Title'             => 'Name',
        'FullTitle'         => 'voller Name'
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

            // the carrier DHL has at least one shipping method
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