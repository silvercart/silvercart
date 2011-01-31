<?php
/**
 * abstract for a shipping zone; makes it easier to calculate shipping rates
 * Every carrier might have it´s own zones. That´s why zones:countries is n:m
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 23.10.2010
 * @license none
 */
class Zone extends DataObject {

    /**
     * Singular name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $singular_name = "zone";

    /**
     * Plural name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $plural_name = "zones";

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
        'Title' => 'VarChar'
    );

    /**
     * Has-one relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $has_one = array(
        'carrier' => 'Carrier'
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
        'shippingFees' => 'ShippingFee'
    );

    /**
     * Many-many relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $many_many = array(
        'countries' => 'Country'
    );

    /**
     * Belongs-many-many relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $belongs_many_many = array(
        'shippingMethods' => 'ShippingMethod'
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
        'Title'         => 'Name',
        'carrier.Title' => 'Frachtführer'
    );

    /**
     * Default database records.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        $standardZone = DataObject::get_one(
            'Zone'
        );

        if (!standardZone) {
            $obj        = new Zone();
            $obj->Title = 'EU';
            $obj->write();
        }
    }

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
        $tabParam = "Root."._t('Zone.COUNTRIES', 'countries');
        $fields->addFieldToTab($tabParam, $countriesTable);
        return $fields;
    }
}
