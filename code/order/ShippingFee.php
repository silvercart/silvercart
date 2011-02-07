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
     * Has-one relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $has_one = array(
        'zone'              => 'Zone',
        'shippingMethod'    => 'ShippingMethod',
        'Tax'               => 'Tax'
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
        'zone.Title'                => 'Für Zone',
        'AttributedShippingMethods' => 'Zugeordnete Versandart',
        'MaximumWeight'             => 'Maximalgewicht (g)',
        'PriceFormatted'            => 'Kosten'
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
        'MaximumWeight'             => 'Maximalgewicht (g)',
        'Price'                     => 'Kosten',
        'zone.Title'                => 'Für Zone',
        'AttributedShippingMethods' => 'Zugeordnete Versandart'
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
        'MaximumWeight',
        'zone.ID' => array(
            'title' => 'Für Zone'
        ),
        'shippingMethod.ID' => array(
            'title' => 'Für Versandart'
        )
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
        'PriceFormatted'            => 'Varchar(20)',
        'AttributedShippingMethods' => 'Varchar(255)'
    );

    /**

    /**
     * Constructor. We localize the static variables here.
     *
     * @param array|null $record      This will be null for a new database record.
     *                                  Alternatively, you can pass an array of
     *                                  field values.  Normally this contructor is only used by the internal systems that get objects from the database.
     * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.  Singletons
     *                                  don't have their defaults set.
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        self::$summary_fields = array(
            'zone.Title' => _t('ShippingMethod.FOR_ZONES'),
            'AttributedShippingMethods' => _t('ShippingFee.ATTRIBUTED_SHIPPINGMETHOD', 'attributed shipping method'),
            'MaximumWeight' => _t('ShippingFee.MAXIMUM_WEIGHT', 'maximum weight (g)', null, 'Maximalgewicht (g)'),
            'PriceFormatted' => _t('ShippingFee.COSTS', 'costs')
        );
        self::$field_labels = array(
            'MaximumWeight' => _t('ShippingFee.MAXIMUM_WEIGHT'),
            'Price' => _t('ShippingFee.COSTS'),
            'zone.Title' => _t('ShippingMethod.FOR_ZONES'),
            'AttributedShippingMethods' => _t('ShippingFee.ATTRIBUTED_SHIPPINGMETHOD')
        );
        self::$searchable_fields = array(
            'MaximumWeight',
            'zone.ID' => array(
                'title' => _t('ShippingMethod.FOR_ZONES')
            ),
            'shippingMethod.ID' => array(
                'title' => _t('ShippingFee.FOR_SHIPPINGMETHOD', 'for shipping method', null, 'Für Versandart')
            )
        );
        parent::__construct($record, $isSingleton);
    }
    
    /**
     * Set a custom search context for fields like "greater than", "less than",
     * etc.
     *
     * @return SearchContext
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function getDefaultSearchContext() {
        $fields     = $this->scaffoldSearchFields();
        $filters    = array(
            'MaximumWeight'             => new LessThanFilter('MaximumWeight')
        );
        return new SearchContext(
            $this->class,
            $fields,
            $filters
        );
    }

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
                new DropdownField(
                    'zoneID',
                    _t('ShippingFee.ZONE_WITH_DESCRIPTION', 'zone (only carrier\'s zones available)'),
                   $zones->toDropDownMap('ID', 'Title', _t('ShippingFee.EMPTYSTRING_CHOOSEZONE', '--choose zone--'))
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
        $attributedShippingMethods    = array();
        $maxLength          = 150;

        foreach ($this->shippingMethod() as $shippingMethod) {
            $attributedShippingMethods[] = $shippingMethod->Title;
        }

        if (!empty($attributedShippingMethods)) {
            $attributedShippingMethodsStr = implode(', ', $attributedShippingMethods);

            if (strlen($attributedShippingMethodsStr) > $maxLength) {
                $attributedShippingMethodsStr = substr($attributedShippingMethodsStr, 0, $maxLength).'...';
            }
        }

        return $attributedShippingMethodsStr;
    }

    /**
     * returns the tax amount included in $this
     *
     * @return float
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.02.2011
     */
    public function getTaxAmount() {
        $taxRate = $this->Price->getAmount() - ($this->Price->getAmount() / (100 + $this->Tax()->Rate) * 100);

        return $taxRate;
    }
}

