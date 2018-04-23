<?php

namespace SilverCart\Model\Shipment;

use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Payment\HandlingCost;
use SilverCart\Model\Shipment\Carrier;
use SilverCart\Model\Shipment\ShippingFee;
use SilverCart\Model\Shipment\ShippingMethod;
use SilverCart\Model\Shipment\ZoneTranslation;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\Filters\ExactMatchFilter;
use SilverStripe\ORM\Filters\PartialMatchFilter;

/**
 * abstract for a shipping zone; makes it easier to calculate shipping rates.
 * Every carrier might have it´s own zones. That´s why zones:countries is n:m.
 *
 * @package SilverCart
 * @subpackage Model_Shipment
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Zone extends DataObject {
    
    /**
     * Has-many relationship.
     *
     * @var array
     */
    private static $has_many = array(
        'ShippingFees'     => ShippingFee::class,
        'ZoneTranslations' => ZoneTranslation::class,
        'HandlingCosts'    => HandlingCost::class,
    );
    /**
     * Many-many relationships.
     *
     * @var array
     */
    private static $many_many = array(
        'Countries'       => Country::class,
        'Carriers'        => Carrier::class,
    );
    /**
     * Belongs-many-many relationships.
     *
     * @var array
     */
    private static $belongs_many_many = array(
        'ShippingMethods' => ShippingMethod::class,
    );
    
    /**
     * Virtual database columns.
     *
     * @var array
     */
    private static $casting = array(
        'AttributedCountries'       => 'Varchar(255)',
        'AttributedShippingMethods' => 'Varchar(255)',
        'CarriersAsString'          => 'Text',
        'Title'                     => 'Text',
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartZone';
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
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
                )
        );
        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function getCMSFields() {
        $fields = DataObjectExtension::getCMSFields($this);
        if ($this->isInDB()) {
            $useAllCountries = new CheckboxField('UseAllCountries', $this->fieldLabel('UseAllCountries'));
            $fields->addFieldToTab('Root.Main', $useAllCountries);
        }
        return $fields;
    }
    
    /**
     * Returns the translated singular name of the object.
     * 
     * @return string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.06.2012
     */
    public function singular_name() {
        return Tools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object.
     * 
     * @return string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.06.2012
     */
    public function plural_name() {
        return Tools::plural_name_for($this);
    }
    
    /**
     * retirieves title from related language class depending on the set locale
     *
     * @return string
     */
    public function getTitle() {
        return $this->getTranslationFieldValue('Title');
    }
    
    /**
     * Searchable fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012
     */
    public function searchableFields() {
        $searchableFields = array(
            'ZoneTranslations.Title' => array(
                'title' => $this->fieldLabel('Title'),
                'filter' => PartialMatchFilter::class,
            ),
            'Carriers.ID' => array(
                'title' => $this->fieldLabel('Carriers'),
                'filter' => ExactMatchFilter::class,
            ),
            'Countries.ID' => array(
                'title' => $this->fieldLabel('Countries'),
                'filter' => ExactMatchFilter::class,
            ),
            'ShippingMethods.ID' => array(
                'title' => $this->fieldLabel('ShippingMethods'),
                'filter' => ExactMatchFilter::class,
            )
        );
        $this->extend('updateSearchableFields', $searchableFields);
        return $searchableFields;
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2011
     */
    public function summaryFields() {
        $summaryFields = array(
            'Title'                     => $this->fieldLabel('Title'),
            'CarriersAsString'          => $this->fieldLabel('Carriers'),
            'AttributedCountries'       => $this->fieldLabel('AttributedCountries'),
            'AttributedShippingMethods' => $this->fieldLabel('AttributedShippingMethods'),
        );
        
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Processing hook before writing the DataObject
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2012 
     */
    protected function onBeforeWrite() {
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
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function AttributedCountries() {
        return Tools::AttributedDataObject($this->Countries());
    }

    /**
     * Returns the attributed shipping methods as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function AttributedShippingMethods() {
        return Tools::AttributedDataObject($this->ShippingMethods());
    }
    
    /**
     * Returns the carriers as a comma separated string
     *
     * @return string
     */
    public function getCarriersAsString() {
        $carriersAsString = '---';
        $carriersAsArray  = $this->Carriers()->map()->toArray();
        if (count($carriersAsArray) > 0 &&
            is_array($carriersAsArray)) {
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
    public static function getZonesFor($countryID) {
        $zoneTable    = Tools::get_table_name(Zone::class);
        $countryTable = Tools::get_table_name(Country::class);
        return self::get()
            ->leftJoin(
                $zoneTable . '_Countries',
                'SZSC.' . $zoneTable . 'ID = ' . $zoneTable . '.ID',
                'SZSC'
            )
            ->filter(
                array(
                    $countryTable . 'ID' => $countryID,
                )
            );
    }
    
    /**
     * Returns whether this zone is related to all active countries
     *
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.06.2012
     */
    public function hasAllCountries() {
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