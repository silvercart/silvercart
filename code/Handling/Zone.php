<?php
/**
 * abstract for a shipping zone; makes it easier to calculate shipping rates
 * Every carrier might have it´s own zones. That´s why zones:countries is n:m
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license BSD
 * @copyright Pixeltricks GmbH
 */
class Zone extends DataObject {
    public static $singular_name = "Zone";
    public static $plural_name = "Zonen";
    public static $db = array(
        'Title' => 'VarChar'
    );
    public static $has_one = array(
        'carrier' => 'Carrier'
    );
    public static $has_many = array(
        'shippingFees' => 'ShippingFee'
    );
    public static $many_many = array(
        'countries' => 'Country'
    );
    public static $belongs_many_many = array(
        'shippingMethods' => 'ShippingMethod'
    );
    public static $summary_fields = array(
        'Title' => 'Name',
        'carrier.Title' => 'Frachtführer'
    );

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldSet the fields for the backend
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 7.11.10
     */
    public function  getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('countries');
        $countriesTable = new ManyManyComplexTableField(
                $this,
                'countries',
                'Country',
                array('Title' => 'Land'),
                'getCMSFields_forPopup'
                );
        $fields->addFieldToTab('Root.Laender', $countriesTable);
        return $fields;
    }
}
