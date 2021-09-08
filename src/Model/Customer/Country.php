<?php

namespace SilverCart\Model\Customer;

use SilverCart\Admin\Forms\AlertWarningField;
use SilverCart\Admin\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\CountryTranslation;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Shipment\Zone;
use SilverCart\ORM\FieldType\DBMoney;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\SS_List;
use SilverStripe\ORM\Filters\ExactMatchFilter;
use SilverStripe\ORM\Filters\PartialMatchFilter;

/**
 * Abstract for a country.
 *
 * @package SilverCart
 * @subpackage Model\Customer
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string  $ISO2                    ISO2
 * @property string  $ISO3                    ISO3
 * @property int     $ISON                    ISON
 * @property string  $FIPS                    FIPS
 * @property string  $Continent               Continent
 * @property string  $Currency                Currency
 * @property bool    $Active                  Active
 * @property DBMoney $freeOfShippingCostsFrom Amount where free shipping costs start from
 * @property bool    $IsPrioritive            Is Prioritive?
 * @property int     $DisplayPosition         Display position
 * @property bool    $IsNonTaxable            Is non taxable?
 * 
 * @property string  $AttributedZones          Attributed Zones (comma separated list)
 * @property string  $AttributedPaymentMethods Attributed PaymentMethods (comma separated list)
 * @property string  $ActivityText             Activity Text
 * @property string  $Title                    Title
 * @property string  $IsPrioritiveText         Is Prioritive Text
 * 
 * @method \SilverStripe\ORM\HasManyList  CountryTranslations() Returns the related translations.
 * @method \SilverStripe\ORM\ManyManyList PaymentMethods()      Returns the related PaymentMethods.
 * @method \SilverStripe\ORM\ManyManyList Zones()               Returns the related Zones.
 */
class Country extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = [
        'ISO2'                      => 'Varchar',
        'ISO3'                      => 'Varchar',
        'ISON'                      => 'Int',
        'FIPS'                      => 'Varchar',
        'Continent'                 => 'Varchar',
        'Currency'                  => 'Varchar',
        'Active'                    => 'Boolean',
        'freeOfShippingCostsFrom'   => DBMoney::class,
        'IsPrioritive'              => 'Boolean(0)',
        'DisplayPosition'           => 'Int',
        'IsNonTaxable'              => 'Boolean(0)',
    ];
    /**
     * Default values
     *
     * @var array
     */
    private static $defaults = [
        'Active' => false,
    ];
    /**
     * Has-many relationship.
     *
     * @var array
     */
    private static $has_many = [
        'CountryTranslations' => CountryTranslation::class,
    ];
    /**
     * Many-many relationships.
     *
     * @var array
     */
    private static $many_many = [
        'PaymentMethods' => PaymentMethod::class,
    ];
    /**
     * Belongs-many-many relationships.
     *
     * @var array
     */
    private static $belongs_many_many = [
        'Zones' => Zone::class,
    ];
    /**
     * Virtual database columns.
     *
     * @var array
     */
    private static $casting = [
        'AttributedZones'           => 'Varchar(255)',
        'AttributedPaymentMethods'  => 'Varchar(255)',
        'ActivityText'              => 'Varchar',
        'Title'                     => 'Text',
        'IsPrioritiveText'          => 'Varchar',
    ];
    /**
     * Default sort order and direction
     *
     * @var string
     */
    private static $default_sort = "SilvercartCountry.Active DESC, SilvercartCountry.IsPrioritive DESC, SilvercartCountryTranslation.Title ASC";
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartCountry';
    /**
     * Determines to insert the translation CMS fields by TranslatableDataObjectExtension.
     * 
     * @var bool
     */
    private static $insert_translation_cms_fields = true;
    /**
     * Determines to insert the translation CMS fields before this field.
     * 
     * @var string
     */
    private static $insert_translation_cms_fields_before = 'ISO2';
    /**
     * list of prioritive countries
     *
     * @var array 
     */
    protected static $prioritiveCountries = [];
    /**
     * count of prioritive countries
     *
     * @var array
     */
    protected static $prioritiveCountryCount = [];
    /**
     * list of non prioritive countries
     *
     * @var array 
     */
    protected static $nonPrioritiveCountries = [];
    /**
     * count of non prioritive countries
     *
     * @var array
     */
    protected static $nonPrioritiveCountryCount = [];
    /**
     * dropdown map sorted by prioritive countries
     *
     * @var array
     */
    protected static $prioritiveDropdownMap = [];
    /**
     * A DataList of all active countries or en empty ArrayList
     * 
     * @var SS_List
     */
    protected static $activeCountries = null;

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this);
    }

    /**
     * i18n for field labels
     *
     * @param bool $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'Title'                     => $this->singular_name(),
            'ISO2'                      => _t(Country::class . '.ISO2', 'ISO Alpha2'),
            'ISO3'                      => _t(Country::class . '.ISO3', 'ISO Alpha3'),
            'ISON'                      => _t(Country::class . '.ISON', 'ISO numeric'),
            'FIPS'                      => _t(Country::class . '.FIPS', 'FIPS code'),
            'Continent'                 => _t(Country::class . '.CONTINENT', 'Continent'),
            'Currency'                  => _t(Country::class . '.CURRENCY', 'Currency'),
            'Active'                    => _t(Country::class . '.ACTIVE', 'Active'),
            'AttributedZones'           => _t(Country::class . '.ATTRIBUTED_ZONES', 'attributed zones'),
            'AttributedPaymentMethods'  => _t(Country::class . '.ATTRIBUTED_PAYMENTMETHOD', 'attributed payment method'),
            'ActivityText'              => _t(Country::class . '.ACTIVE', 'Active'),
            'freeOfShippingCostsFrom'   => _t(Country::class . '.FREEOFSHIPPINGCOSTSFROM', 'Free of shipping costs from'),
            'IsPrioritive'              => _t(Country::class . '.ISPRIORITIVE', 'Show country prioritive at the top of dropdown lists?'),
            'IsPrioritiveShort'         => _t(Country::class . '.ISPRIORITIVE_SHORT', 'Prioritive'),
            'DisplayPosition'           => _t(Country::class . '.DISPLAYPOSITION', 'Display position (if prioritive)'),
            'IsNonTaxable'              => _t(Country::class . '.IsNonTaxable', 'Non-taxable'),
            'CountryTranslations.Title' => _t(Country::class . '.TITLE', 'Name'),
            'CountryTranslations'       => CountryTranslation::singleton()->plural_name(),
            'Zones'                     => Zone::singleton()->plural_name(),
            'PaymentMethods'            => PaymentMethod::singleton()->plural_name(),
            'PaymentMethods.ID'         => PaymentMethod::singleton()->plural_name(),
        ]);
    }

    /**
     * Searchable fields of SilvercartCountry.
     *
     * @return array
     */
    public function  searchableFields() : array
    {
        return [
            'CountryTranslations.Title' => [
                'title'  => $this->singular_name(),
                'filter' => PartialMatchFilter::class,
            ],
            'ISO2' => [
                'title'     => $this->fieldLabel('ISO2'),
                'filter'    => PartialMatchFilter::class,
            ],
            'ISO3' => [
                'title'     => $this->fieldLabel('ISO3'),
                'filter'    => PartialMatchFilter::class,
            ],
            'ISON' => [
                'title'     => $this->fieldLabel('ISON'),
                'filter'    => PartialMatchFilter::class,
            ],
            'FIPS' => [
                'title'     => $this->fieldLabel('FIPS'),
                'filter'    => PartialMatchFilter::class,
            ],
            'Continent' => [
                'title'     => $this->fieldLabel('Continent'),
                'filter'    => PartialMatchFilter::class,
            ],
            'Currency' => [
                'title'     => $this->fieldLabel('Currency'),
                'filter'    => PartialMatchFilter::class,
            ],
            'PaymentMethods.ID' => [
                'title'     => $this->fieldLabel('PaymentMethods'),
                'filter'    => PartialMatchFilter::class,
            ],
            'Zones.ID' => [
                'title'     => $this->fieldLabel('Zones'),
                'filter'    => PartialMatchFilter::class,
            ],
            'Active' => [
                'title'     => $this->fieldLabel('Active'),
                'filter'    => ExactMatchFilter::class,
            ],
            'IsPrioritive' => [
                'title'     => $this->fieldLabel('IsPrioritiveShort'),
                'filter'    => ExactMatchFilter::class,
            ],
            'IsNonTaxable' => [
                'title'     => $this->fieldLabel('IsNonTaxable'),
                'filter'    => ExactMatchFilter::class,
            ],
        ];
    }
    
    /**
     * Returns freeOfShippingCostsFrom in a nice format
     *
     * @return string
     */
    public function getFreeOfShippingCostsFromNice() : string
    {
        return (string) $this->freeOfShippingCostsFrom->Nice();
    }

    /**
     * Summary fields
     *
     * @return array
     */
    public function summaryFields() : array
    {
        $summaryFields = array_merge(
                parent::summaryFields(),
                [
                    'Title'                             => $this->fieldLabel('Title'),
                    'ISO2'                              => $this->fieldLabel('ISO2'),
                    'ISO3'                              => $this->fieldLabel('ISO3'),
                    'Continent'                         => $this->fieldLabel('Continent'),
                    'Currency'                          => $this->fieldLabel('Currency'),
                    'AttributedZones'                   => $this->fieldLabel('AttributedZones'),
                    'AttributedPaymentMethods'          => $this->fieldLabel('AttributedPaymentMethods'),
                    'ActivityText'                      => $this->fieldLabel('ActivityText'),
                    'getFreeOfShippingCostsFromNice'    => $this->fieldLabel('freeOfShippingCostsFrom'),
                    'IsPrioritiveText'                  => $this->fieldLabel('IsPrioritiveShort'),
                    'IsNonTaxable'                      => $this->fieldLabel('IsNonTaxable'),
                ]
        );
        $this->extend('updateSummary', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Returns an array of field/relation names (db, has_one, has_many, 
     * many_many, belongs_many_many) to exclude from form scaffolding in
     * backend.
     * This is a performance friendly way to exclude fields.
     * 
     * @return array
     */
    public function excludeFromScaffolding() : array
    {
        $excludeFromScaffolding = [
            'Zones',
            'Locale'
        ];
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }

    /**
     * Returns the CMS fields.
     *
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            if ($this->exists()) {
                $paymentMethodsTable = $fields->dataFieldByName('PaymentMethods');
                $paymentMethodsTable->setConfig(GridFieldConfig_RelationEditor::create());
            } else {
                $content = _t(self::class . '.AlertWarningCreationContent', 'Do you really want to create a new country? If you want to assign one of the {count} existing countries instead, use the "Link Existing" function (upper right corner of the table).', ['count' => self::get()->count()]);
                $title   = _t(self::class . '.AlertWarningCreationTitle', 'Caution');
                $creationWarningField = AlertWarningField::create('CreationWarning', $content, "{$title}:");
                $fields->insertBefore($creationWarningField, 'Title');
            }
            $displayPositionMap = [
                '0' => Tools::field_label('PleaseChoose'),
            ];
            for ($x = 1; $x <= self::getPrioritiveCountryCount(false) + 1; $x++) {
                $displayPositionMap[$x] = $x;
            }
            $displayPositionField = DropdownField::create('DisplayPosition', $this->fieldLabel('DisplayPosition'), $displayPositionMap);
            $fields->insertAfter($displayPositionField, 'IsPrioritive');
        });
        return parent::getCMSFields();
    }
    
    /**
     * Hook before writing th object
     * 
     * @return void
     */
    public function onBeforeWrite() : void
    {
        parent::onBeforeWrite();
        if (!$this->IsPrioritive) {
            $this->DisplayPosition = 0;
        } elseif ($this->DisplayPosition == 0) {
            $this->DisplayPosition = 1000;
        }
    }

    /**
     * Returns the text label for a countries activity.
     *
     * @return string
     */
    public function getActivityText() : string
    {
        if ($this->Active) {
            return Tools::field_label('Yes');
        }
        return Tools::field_label('No');
    }

    /**
     * Returns the text label for a countries priority.
     *
     * @return string
     */
    public function getIsPrioritiveText() : string
    {
        if ($this->IsPrioritive) {
            return Tools::field_label('Yes');
        }
        return Tools::field_label('No');
    }

    /**
     * Returns the attributed zones as string (limited to 150 chars).
     *
     * @return string
     */
    public function AttributedZones() : string
    {
        return Tools::AttributedDataObject($this->Zones());
    }

    /**
     * Returns the attributed payment methods as string (limited to 150 chars).
     *
     * @return string
     */
    public function AttributedPaymentMethods() : string
    {
        return Tools::AttributedDataObject($this->PaymentMethods());
    }
    
    /**
     * Returns the title
     *
     * @return string
     */
    public function getTitle() : string
    {
        return (string) $this->getTranslationFieldValue('Title');
    }
    
    /**
     * Returns all active countries
     * 
     * @return SS_List
     */
    public static function get_active() : SS_List
    {
        if (!self::$activeCountries) {
            $activeCountries = Country::get()->filter("Active", 1);
            if (!$activeCountries->exists()) {
                $activeCountries = ArrayList::create();
            }
            self::$activeCountries = $activeCountries;
        }
        return self::$activeCountries;
    }
    
    /**
     * Returns a list of prioritive countries
     * 
     * @param bool $onlyActive Search only for active coutries?
     * 
     * @return DataList
     */
    public static function getPrioritiveCountries(bool $onlyActive = true) : DataList
    {
        $key            = 0;
        $addToFilter    = [];
        if ($onlyActive) {
            $key         = 1;
            $addToFilter = ['Active' => 1];
        }
        if (!array_key_exists($key, self::$prioritiveCountries)) {
            $filter = array_merge(['IsPrioritive' => 1], $addToFilter);
            self::$prioritiveCountries[$key] = Country::get()
                                    ->filter($filter)
                                    ->sort(['DisplayPosition' => 'ASC', 'Title' => 'ASC']);
        }
        return self::$prioritiveCountries[$key];
    }
    
    /**
     * Returns the count of prioritive countries
     * 
     * @param bool $onlyActive Search only for active coutries?
     * 
     * @return int
     */
    public static function getPrioritiveCountryCount(bool $onlyActive = true) : int
    {
        $key = 0;
        if ($onlyActive) {
            $key = 1;
        }
        if (!array_key_exists($key, self::$prioritiveCountryCount)) {
            $prioritiveCountryCount = 0;
            $prioritiveCountries    = self::getPrioritiveCountries($onlyActive);
            if ($prioritiveCountries instanceof SS_List) {
                $prioritiveCountryCount = (int) $prioritiveCountries->count();
            }
            self::$prioritiveCountryCount[$key] = $prioritiveCountryCount;
        }
        return self::$prioritiveCountryCount[$key];
    }
    
    /**
     * Returns a list of non prioritive countries
     * 
     * @param bool $onlyActive Search only for active coutries?
     * 
     * @return DataList
     */
    public static function getNonPrioritiveCountries(bool $onlyActive = true) : DataList
    {
        $key         = 0;
        $addToFilter = [];
        if ($onlyActive) {
            $key         = 1;
            $addToFilter = ['Active' => 1];
        }
        if (!array_key_exists($key, self::$nonPrioritiveCountries)) {
            $filter = array_merge(['IsPrioritive' => 0], $addToFilter);
            self::$nonPrioritiveCountries[$key] = Country::get()
                                        ->filter($filter)
                                        ->sort(['Title' => 'ASC']);
        }
        return self::$nonPrioritiveCountries[$key];
    }
    
    /**
     * Returns the count of non prioritive countries
     * 
     * @param bool $onlyActive Search only for active coutries?
     * 
     * @return int
     */
    public static function getNonPrioritiveCountryCount(bool $onlyActive = true) : int
    {
        $key = 0;
        if ($onlyActive) {
            $key = 1;
        }
        if (!array_key_exists($key, self::$nonPrioritiveCountryCount)) {
            $nonPrioritiveCountryCount = 0;
            $nonPrioritiveCountries    = self::getNonPrioritiveCountries($onlyActive);
            if ($nonPrioritiveCountries instanceof SS_List) {
                $nonPrioritiveCountryCount = (int) $nonPrioritiveCountries->count();
            }
            self::$nonPrioritiveCountryCount[$key] = $nonPrioritiveCountryCount;
        }
        return self::$nonPrioritiveCountryCount[$key];
    }
    
    /**
     * Returns a dropdown map sorted by prioritive countries
     * 
     * @param bool   $onlyActive  Search only for active coutries?
     * @param string $emptyString String to show for empty value
     * 
     * @return array
     */
    public static function getPrioritiveDropdownMap(bool $onlyActive = true, string $emptyString = null) : array
    {
        $key = 0;
        if ($onlyActive) {
            $key = 1;
        }
        if (!is_null($emptyString)) {
            $key .= md5($emptyString);
        }
        if (!array_key_exists($key, self::$prioritiveDropdownMap)) {
            $dropdownMap = [];
            if (!is_null($emptyString)) {
                $dropdownMap[''] = $emptyString;
            }
            if (self::getPrioritiveCountryCount() > 0) {
                $prioritiveCountries    = self::getPrioritiveCountries($onlyActive);
                foreach ($prioritiveCountries->map()->toArray() as $id => $title) {
                    $dropdownMap[$id] = $title;
                }
            }
            if (self::getNonPrioritiveCountryCount() > 0) {
                if ((is_null($emptyString)
                  && count($dropdownMap) > 0)
                 || (!is_null($emptyString)
                  && count($dropdownMap) > 1)
                ) {
                    $dropdownMap[' '] = '------------------------';
                }
                $nonPrioritiveCountries = self::getNonPrioritiveCountries($onlyActive);
                foreach ($nonPrioritiveCountries->map()->toArray() as $id => $title) {
                    $dropdownMap[$id] = $title;
                }
            }
            if (empty($dropdownMap)
             && Tools::isBackendEnvironment()
            ) {
                $allCountries = Country::get();
                $dropdownMap  = $allCountries->map()->toArray();
            }
            self::$prioritiveDropdownMap[$key] = $dropdownMap;
        }
        return self::$prioritiveDropdownMap[$key];
    }
    
    /**
     * Creates the default translations for the given $targetLocale dependent on
     * the given $existingLocale.
     * If $targetLocale is not given, Tools::current_locale() will be used as
     * $targetLocale.
     * 
     * @param string $existingLocale Existing locale (e.g. en_US)
     * @param string $targetLocale   Target locale (e.g. de_DE)
     * 
     * @return void
     */
    public static function create_translations(string $existingLocale, string $targetLocale = null) : void
    {
        if (is_null($targetLocale)) {
            $targetLocale = Tools::current_locale();
        }
        if ($targetLocale != $existingLocale) {
            $originalLocale = Tools::current_locale();
            Tools::set_current_locale($existingLocale);
            $countries = Country::get();
            foreach ($countries as $country) {
                $translation = CountryTranslation::get()->filter([
                    'CountryID' => $country->ID,
                    'Locale'    => $targetLocale,
                ])->first();
                if (!($translation instanceof CountryTranslation)
                 || !$translation->exists()
                ) {
                    $translation = CountryTranslation::create();
                    $translation->Locale    = $targetLocale;
                    $translation->CountryID = $country->ID;
                    $translation->Title     = _t(Country::class . ".TITLE_" . $country->ISO2, $country->Title);
                    $translation->write();
                }
            }
            Tools::set_current_locale($originalLocale);
        }
    }
}