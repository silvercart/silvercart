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
     * Constructor. We localize the static variables here.
     *
     * @param array|null $record      This will be null for a new database record.
     *                                  Alternatively, you can pass an array of
     *                                  field values.  Normally this contructor is only used by the internal systems that get objects from the database.
     * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.  Singletons
     *                                  don't have their defaults set.
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 24.01.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        self::$field_labels = array(
            'Title' => _t('ArticleCategoryPage.COLUMN_TITLE'),
            'carrier.Title' => _t('Carrier.SINGULARNAME'),
            'AttributedCountries' => _t('Zone.ATTRIBUTED_COUNTRIES', 'attributed countries'),
            'AttributedShippingMethods' => _t('Zone.ATTRIBUTED_SHIPPINGMETHODS', 'attributed shipping methods')
        );
        self::$searchable_fields = array(
            'Title',
            'carrier.ID' => array(
                'title' => _t('Carrier.SINGULARNAME')
            ),
            'countries.ID' => array(
                'title' => _t('Zone.FOR_COUNTRIES', 'for countries')
            ),
            'shippingMethods.ID' => array(
                'title' => _t('Zone.ATTRIBUTED_SHIPPINGMETHODS')
            )
        );
        parent::__construct($record, $isSingleton);
    }

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
        'Title',
        'carrier.Title',
        'AttributedCountries',
        'AttributedShippingMethods'
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
        'Title' => 'Name',
        'carrier.Title' => 'Frachtführer',
        'AttributedCountries' => 'Für Länder',
        'AttributedShippingMethods' => 'Zugeordnete Versandarten'
    );
    /**
     * Virtual database columns.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $casting = array(
        'AttributedCountries' => 'Varchar(255)',
        'AttributedShippingMethods' => 'Varchar(255)'
    );
    /**
     * List of searchable fields for the model admin
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $searchable_fields = array(
        'Title',
        'carrier.ID' => array(
            'title' => 'Frachtführer'
        ),
        'countries.ID' => array(
            'title' => 'Für Länder'
        ),
        'shippingMethods.ID' => array(
            'title' => 'Zugeordnete Versandarten'
        )
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

        if (!$standardZone) {
            $obj = new Zone();
            $obj->Title = 'EU';
            $obj->write();

            $domestic = new Zone();
            $domestic->Title = _t('Zone.DOMESTIC', 'domestic');
            $domestic->write();

            //relate country to zones
$standardCountry = DataObject::get_one(
                        'Country'
        );

        if (!$standardCountry) {
            $standardCountry = new Country();
            $standardCountry->Title = 'Deutschland';
            $standardCountry->ISO2 = 'de';
            $standardCountry->ISO3 = 'deu';
            $standardCountry->write();
        }
        $domestic->countries()->add($standardCountry);

        }

    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldSet the fields for the backend
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 7.11.10
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('countries');
        $countriesTable = new ManyManyComplexTableField(
                        $this,
                        'countries',
                        'Country',
                        array('Title' => _t('Country.SINGULARNAME')),
                        'getCMSFields_forPopup'
        );
        $tabParam = "Root." . _t('Zone.COUNTRIES', 'countries');
        $fields->addFieldToTab($tabParam, $countriesTable);
        return $fields;
    }

    /**
     * Returns the attributed countries as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function AttributedCountries() {
        $attributedCountriesStr = '';
        $attributedCountries = array();
        $maxLength = 150;

        foreach ($this->countries() as $country) {
            $attributedCountries[] = $country->Title;
        }

        if (!empty($attributedCountries)) {
            $attributedCountriesStr = implode(', ', $attributedCountries);

            if (strlen($attributedCountriesStr) > $maxLength) {
                $attributedCountriesStr = substr($attributedCountriesStr, 0, $maxLength) . '...';
            }
        }

        return $attributedCountriesStr;
    }

    /**
     * Returns the attributed shipping methods as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function AttributedShippingMethods() {
        $attributedShippingMethodsStr = '';
        $attributedShippingMethods = array();
        $maxLength = 150;

        foreach ($this->shippingMethods() as $shippingMethod) {
            $attributedShippingMethods[] = $shippingMethod->Title;
        }

        if (!empty($attributedShippingMethods)) {
            $attributedShippingMethodsStr = implode(', ', $attributedShippingMethods);

            if (strlen($attributedShippingMethodsStr) > $maxLength) {
                $attributedShippingMethodsStr = substr($attributedShippingMethodsStr, 0, $maxLength) . '...';
            }
        }

        return $attributedShippingMethodsStr;
    }

}
