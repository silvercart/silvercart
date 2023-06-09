<?php

namespace SilverCart\Model\Shipment;

use SilverCart\Admin\Forms\GridField\GridFieldAddExistingAutocompleter as SilverCartGridFieldAddExistingAutocompleter;
use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Payment\HandlingCost;
use SilverCart\Model\Shipment\Carrier;
use SilverCart\Model\Shipment\ShippingFee;
use SilverCart\Model\Shipment\ShippingMethod;
use SilverCart\Model\Shipment\ZoneTranslation;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\Filters\ExactMatchFilter;
use SilverStripe\ORM\Filters\PartialMatchFilter;

/**
 * abstract for a shipping zone; makes it easier to calculate shipping rates.
 * Every carrier might have it´s own zones. That´s why zones:countries is n:m.
 *
 * @package SilverCart
 * @subpackage Model\Shipment
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Zone extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * Has-many relationship.
     *
     * @var array
     */
    private static array $has_many = [
        'ShippingFees'     => ShippingFee::class,
        'ZoneTranslations' => ZoneTranslation::class,
        'HandlingCosts'    => HandlingCost::class,
    ];
    /**
     * Many-many relationships.
     *
     * @var array
     */
    private static array $many_many = [
        'Countries' => Country::class,
        'Carriers'  => Carrier::class,
    ];
    /**
     * Belongs-many-many relationships.
     *
     * @var array
     */
    private static array $belongs_many_many = [
        'ShippingMethods' => ShippingMethod::class . '.Zones',
    ];
    /**
     * Virtual database columns.
     *
     * @var array
     */
    private static array $casting = [
        'AttributedCountries'       => 'Varchar(255)',
        'AttributedShippingMethods' => 'Varchar(255)',
        'CarriersAsString'          => 'Text',
        'Title'                     => 'Text',
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static string $table_name = 'SilvercartZone';
    /**
     * Insert translation CMS fields.
     * 
     * @var bool
     */
    private static bool $insert_translation_cms_fields = true;


    /**
     * Field labels for display in tables.
     *
     * @param bool $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'Title'                     => Page::singleton()->fieldLabel('Title'),
            'Carriers'                  => Carrier::singleton()->plural_name(),
            'AttributedCountries'       => _t(Zone::class . '.ATTRIBUTED_COUNTRIES', 'attributed countries'),
            'AttributedShippingMethods' => _t(Zone::class . '.ATTRIBUTED_SHIPPINGMETHODS', 'attributed shipping methods'),
            'ShippingFees'              => ShippingFee::singleton()->plural_name(),
            'ShippingMethods'           => ShippingMethod::singleton()->plural_name(),
            'Countries'                 => _t(Country::class . '.PLURALNAME', 'Countries'),
            'UseAllCountries'           => _t(Zone::class . '.USE_ALL_COUNTRIES', 'Relate all countries after saving'),
            'ZoneTranslations'          => ZoneTranslation::singleton()->plural_name(),
            'HandlingCosts'             => HandlingCost::singleton()->plural_name(),
        ]);
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            if ($this->isInDB()) {
                $useAllCountries = CheckboxField::create('UseAllCountries', $this->fieldLabel('UseAllCountries'));
                $fields->addFieldToTab('Root.Main', $useAllCountries);
                $replaceAddExistingAutocompleter = ['Countries', 'Carriers', 'ShippingMethods'];
                foreach ($replaceAddExistingAutocompleter as $fieldName) {
                    $grid       = $fields->dataFieldByName($fieldName);
                    $gridConfig = $grid->getConfig();
                    $gridConfig->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
                    $gridConfig->addComponent(new SilverCartGridFieldAddExistingAutocompleter('buttons-before-right'));
                }
            }
        });
        return parent::getCMSFields();
    }
    
    /**
     * Returns the translated singular name of the object.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this);
    }
    
    /**
     * retirieves title from related language class depending on the set locale
     *
     * @return string
     */
    public function getTitle() : string
    {
        return (string) $this->getTranslationFieldValue('Title');
    }
    
    /**
     * Searchable fields
     *
     * @return array
     */
    public function searchableFields() : array
    {
        $searchableFields = [
            'ZoneTranslations.Title' => [
                'title' => $this->fieldLabel('Title'),
                'filter' => PartialMatchFilter::class,
            ],
            'Carriers.ID' => [
                'title' => $this->fieldLabel('Carriers'),
                'filter' => ExactMatchFilter::class,
            ],
            'Countries.ID' => [
                'title' => $this->fieldLabel('Countries'),
                'filter' => ExactMatchFilter::class,
            ],
            'ShippingMethods.ID' => [
                'title' => $this->fieldLabel('ShippingMethods'),
                'filter' => ExactMatchFilter::class,
            ],
        ];
        $this->extend('updateSearchableFields', $searchableFields);
        return $searchableFields;
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     */
    public function summaryFields() : array
    {
        $summaryFields = [
            'Title'                     => $this->fieldLabel('Title'),
            'CarriersAsString'          => $this->fieldLabel('Carriers'),
            'AttributedCountries'       => $this->fieldLabel('AttributedCountries'),
            'AttributedShippingMethods' => $this->fieldLabel('AttributedShippingMethods'),
        ];
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Processing hook before writing the DataObject
     * 
     * @return void
     */
    protected function onBeforeWrite() : void
    {
        parent::onBeforeWrite();
        if (array_key_exists('UseAllCountries', $_POST)) {
            $countries = Country::get();
            foreach ($countries as $country) {
                $this->Countries()->add($country);
            }
        }
    }

    /**
     * Returns the attributed countries as string (limited to 150 chars).
     *
     * @return string
     */
    public function AttributedCountries()  : string
    {
        return (string) Tools::AttributedDataObject($this->Countries());
    }

    /**
     * Returns the attributed shipping methods as string (limited to 150 chars).
     *
     * @return string
     */
    public function AttributedShippingMethods()  : string
    {
        return (string) Tools::AttributedDataObject($this->ShippingMethods());
    }
    
    /**
     * Returns the carriers as a comma separated string
     *
     * @return string
     */
    public function getCarriersAsString() : string
    {
        $carriersAsString = '---';
        $carriersAsArray  = $this->Carriers()->map()->toArray();
        if (count($carriersAsArray) > 0
         && is_array($carriersAsArray)
        ) {
            $carriersAsString = implode(',', $carriersAsArray);
        }
        return $carriersAsString;
    }
    
    /**
     * Returns all zones for the given country ID
     *
     * @param int $countryID ID of the country to get zones for
     * 
     * @return DataList
     */
    public static function getZonesFor(int $countryID) : DataList
    {
        $zoneTable    = self::config()->table_name;
        $countryTable = Country::config()->table_name;
        return self::get()
            ->leftJoin(
                "{$zoneTable}_Countries",
                "SZSC.{$zoneTable}ID = {$zoneTable}.ID",
                'SZSC'
            )
            ->filter([
                $countryTable . 'ID' => $countryID,
            ]);
    }
    
    /**
     * Returns whether this zone is related to all active countries
     *
     * @return bool
     */
    public function hasAllCountries() : bool
    {
        /* @var $countries ArrayList */
        $countries          = $this->Countries();
        $availableCountries = Country::get()->filter("Active", 1);
        $hasAllCountries    = true;
        foreach ($availableCountries as $availableCountry) {
            if (!$countries->find('ID', $availableCountry->ID)) {
                $hasAllCountries = false;
                break;
            }
        }
        return $hasAllCountries;
    }
}