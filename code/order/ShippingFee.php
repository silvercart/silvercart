<?php
/**
 * A carrier has many shipping fees.
 * They mainly depend on the freights weight.
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 06.11.2010
 * @license none
 */
class ShippingFee extends DataObject {

    /**
     * Singular name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    static $singular_name = "Versandtarif";

    /**
     * Plural name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    static $plural_name = "Versandtarife";

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
        'MaximumWeight' => 'Int',   //gramms
        'Price'         => 'Money'
    );

    /**
     * Has-one relationship.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $has_one = array(
        'zone'              => 'Zone',
        'shippingMethod'    => 'ShippingMethod'
    );

    /**
     * Has-many Relationship.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $has_many = array(
        'orders' => 'Order'
    );

    /**
     * Summaryfields for display in tables.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $summary_fields = array(
        'MaximumWeight'     => 'Maximalgewicht (g)',
        'PriceFormatted'    => 'Kosten',
        'zone.Title'        => 'Zone'
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
        'MaximumWeight'     => 'Maximalgewicht (g)',
        'Price'             => 'Kosten'
    );

    /**
     * Searchable fields in the model admin.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $searchable_fields = array(
        'MaximumWeight'
    );

    /**
     * Virtual database fields.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $casting = array(
        'PriceFormatted' => 'Varchar(20)'
    );

    /**
     * Customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldSet the fields for the backend
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 8.11.10
     */
    public function getCMSFields() {
       $fields = parent::getCMSFields();

       /**
        * only the carriers zones must be selectable
        */
       $fields->removeByName('zone');
       $filter  = sprintf("`carrierID` = %s", $this->shippingMethod()->carrier()->ID);
       $zones   = DataObject::get('Zone', $filter);
       if ($zones) {
           $fields->addFieldToTab(
               "Root.Main",
               new DropdownField('zoneID', 'Zone (nur die des Frachtführers verfügbar)',
                       $zones->toDropDownMap('ID', 'Title', "-- Zone wählen --")
                       )
               );
       }

       return $fields;
    }

    /**
     * Returns the Price formatted by locale.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function PriceFormatted() {
        return $this->Price->Nice();
    }
}

