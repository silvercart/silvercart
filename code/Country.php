<?php

/**
 * Abstract for a country
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license BSD
 * @since 20.10.2010
 */
class Country extends DataObject {

    /**
     * Singular name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $singular_name = "country";
    /**
     * Plural name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $plural_name = "countries";
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
        'Title' => 'VarChar',
        'ISO2' => 'VarChar',
        'ISO3' => 'VarChar'
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
        'paymentMethods' => 'PaymentMethod'
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
        'zones' => 'Zone'
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
        'ISO2',
        'ISO3',
        'AttributedZones',
        'AttributedPaymentMethods'
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
        'Title' => 'Land',
        'ISO2' => 'ISO2 Code',
        'ISO3' => 'ISO3 Code',
        'AttributedZones' => 'Zugeordnete Zonen',
        'AttributedPaymentMethods' => 'Zugeordnete Bezahlarten'
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
        'ISO2',
        'ISO3',
        'zones.ID' => array(
            'title' => 'Zugeordnete Zonen'
        ),
        'paymentMethods.ID' => array(
            'title' => 'Zugeordnete Bezahlarten'
        )
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
        'AttributedZones' => 'Varchar(255)',
        'AttributedPaymentMethods' => 'Varchar(255)'
    );

    /**
     * Constructor
     *
     * @param array|null $record      This will be null for a new database record.  Alternatively, you can pass an array of
     *                                field values.  Normally this contructor is only used by the internal systems that get objects from the database.
     * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.  Singletons
     *                                don't have their defaults set.
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 2.2.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        self::$field_labels = array(
            'Title' => _t('Country.SINGULARNAME'),
            'ISO2' => 'ISO2 Code',
            'ISO3' => 'ISO3 Code',
            'AttributedZones' => _t('Country.ATTRIBUTED_ZONES', 'attributed zones'),
            'AttributedPaymentMethods' => _t('Country.ATTRIBUTED_PAYMENTMETHOD', 'attributed payment method')
        );
        self::$searchable_fields = array(
            'Title',
            'ISO2',
            'ISO3',
            'zones.ID' => array(
                'title' => _t('Country.ATTRIBUTED_ZONES')
            ),
            'paymentMethods.ID' => array(
                'title' => _t('Country.ATTRIBUTED_PAYMENTMETHOD')
            )
        );
        parent::__construct($record, $isSingleton);
    }

    /**
     * Default database records
     *
     * @return void
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 20.10.2010
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        $standardCountry = DataObject::get_one(
                        'Country'
        );

        if (!$standardCountry) {
            $obj = new Country();
            $obj->Title = 'Deutschland';
            $obj->ISO2 = 'de';
            $obj->ISO3 = 'deu';
            $obj->write();
        }
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     * 
     * @return FieldSet the fields for the backend
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 28.10.10
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('paymentMethods');
        $fields->removeByName('zones');

        $paymentMethodsTable = new ManyManyComplexTableField(
                        $this,
                        'paymentMethods',
                        'PaymentMethod',
                        null,
                        'getCmsFields_forPopup'
        );
        $paymentMethodsTable->setAddTitle(_t('PaymentMethod.TITLE', 'payment method'));
        $tabParam = "Root." . _t('PaymentMethod.TITLE');
        $fields->addFieldToTab($tabParam, $paymentMethodsTable);

        return $fields;
    }

    /**
     * Returns the attributed zones as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function AttributedZones() {
        $attributedZonesStr = '';
        $attributedZones = array();
        $maxLength = 150;

        foreach ($this->zones() as $zone) {
            $attributedZones[] = $zone->Title;
        }

        if (!empty($attributedZones)) {
            $attributedZonesStr = implode(', ', $attributedZones);

            if (strlen($attributedZonesStr) > $maxLength) {
                $attributedZonesStr = substr($attributedZonesStr, 0, $maxLength) . '...';
            }
        }

        return $attributedZonesStr;
    }

    /**
     * Returns the attributed payment methods as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function AttributedPaymentMethods() {
        $attributedPaymentMethodsStr = '';
        $attributedPaymentMethods = array();
        $maxLength = 150;

        foreach ($this->paymentMethods() as $paymentMethod) {
            $attributedPaymentMethods[] = $paymentMethod->Name;
        }

        if (!empty($attributedPaymentMethods)) {
            $attributedPaymentMethodsStr = implode(', ', $attributedPaymentMethods);

            if (strlen($attributedPaymentMethodsStr) > $maxLength) {
                $attributedPaymentMethodsStr = substr($attributedPaymentMethodsStr, 0, $maxLength) . '...';
            }
        }

        return $attributedPaymentMethodsStr;
    }

}
