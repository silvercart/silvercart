<?php

/**
 * A carrier has many shipping fees.
 * They mainly depend on the freights weight.
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 06.11.2010
 * @license BSD
 */
class ShippingFee extends DataObject {
    static $singular_name = "Versandtarif";
    static $plural_name = "Versandtarife";
    public static $db = array(
        'MaximumWeight' => 'Int', //gramms
        'Price' => 'Money'
    );
    public static $has_one = array(
        'zone' => 'Zone',
        'shippingMethod' => 'ShippingMethod'
    );
    public static $has_many = array(
        'orders' => 'Order'
    );
    public static $summary_fields = array(
        'MaximumWeight' => 'Maximalgewicht (g)',
        'Price' => 'Kosten',
        'zone.Title' => 'Zone'
    );
    public static $field_labels = array(
        'MaximumWeight' => 'Maximalgewicht (g)',
        'Price' => 'Kosten'
    );
    public static $searchable_fields = array(
        'MaximumWeight'
    );

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldSet the fields for the backend
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 8.11.10
     */
    public function  getCMSFields() {
       $fields = parent::getCMSFields();

       /**
        * only the carriers zones must be selectable
        */
       $fields->removeByName('zone');
       $filter = sprintf("`carrierID` = %s", $this->shippingMethod()->carrier()->ID);
       $zones = DataObject::get('Zone', $filter);
       if ($zones) {
           $fields->addFieldToTab(
                   "Root.Main",
                   new DropdownField('zoneID', _t('ShippingFee.ZONE_WITH_DESCRIPTION', 'zone (only carrier\'s zones available)'),
                           $zones->toDropDownMap('ID', 'Title', _t('ShippingFee.EMPTYSTRING_CHOOSEZONE', '--choose zone--'))
                           )
                   );
       }

       return $fields;
    }
}

