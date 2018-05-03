<?php

namespace SilverCart\Model\Customer;

use SilverCart\Admin\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\CountryTranslation;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Shipment\Zone;
use SilverCart\Model\Translation\TranslationTools;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\SS_List;
use SilverStripe\ORM\Filters\ExactMatchFilter;
use SilverStripe\ORM\Filters\PartialMatchFilter;

/**
 * Abstract for a country.
 *
 * @package SilverCart
 * @subpackage Model_Customer
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Country extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = array(
        'ISO2'                      => 'Varchar',
        'ISO3'                      => 'Varchar',
        'ISON'                      => 'Int',
        'FIPS'                      => 'Varchar',
        'Continent'                 => 'Varchar',
        'Currency'                  => 'Varchar',
        'Active'                    => 'Boolean',
        'freeOfShippingCostsFrom'   => \SilverCart\ORM\FieldType\DBMoney::class,
        'IsPrioritive'              => 'Boolean(0)',
        'DisplayPosition'           => 'Int',
        'IsNonTaxable'              => 'Boolean(0)',
    );
    /**
     * Default values
     *
     * @var array
     */
    private static $defaults = array(
        'Active' => false,
    );
    /**
     * Has-many relationship.
     *
     * @var array
     */
    private static $has_many = array(
        'CountryTranslations' => CountryTranslation::class,
    );
    /**
     * Many-many relationships.
     *
     * @var array
     */
    private static $many_many = array(
        'PaymentMethods' => PaymentMethod::class,
    );
    /**
     * Belongs-many-many relationships.
     *
     * @var array
     */
    private static $belongs_many_many = array(
        'Zones' => Zone::class,
    );
    /**
     * Virtual database columns.
     *
     * @var array
     */
    private static $casting = array(
        'AttributedZones'           => 'Varchar(255)',
        'AttributedPaymentMethods'  => 'Varchar(255)',
        'ActivityText'              => 'Varchar',
        'Title'                     => 'Text',
        'IsPrioritiveText'          => 'Varchar',
    );
    
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
     * list of prioritive countries
     *
     * @var array 
     */
    protected static $prioritiveCountries = array();
    
    /**
     * count of prioritive countries
     *
     * @var array
     */
    protected static $prioritiveCountryCount = array();
    
    /**
     * list of non prioritive countries
     *
     * @var array 
     */
    protected static $nonPrioritiveCountries = array();
    
    /**
     * count of non prioritive countries
     *
     * @var array
     */
    protected static $nonPrioritiveCountryCount = array();
    
    /**
     * dropdown map sorted by prioritive countries
     *
     * @var array
     */
    protected static $prioritiveDropdownMap = array();
    
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
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.06.2012
     */
    public function singular_name() {
        return Tools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.06.2012
     */
    public function plural_name() {
        return Tools::plural_name_for($this);
    }

    /**
     * i18n for field labels
     *
     * @param bool $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.07.2013
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
            parent::fieldLabels($includerelations),
            array(
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
            )
        );
    }

    /**
     * Searchable fields of SIlvercartCountry.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.04.2014
     */
    public function  searchableFields() {
        return array(
            'CountryTranslations.Title' => array(
                'title'  => $this->singular_name(),
                'filter' => PartialMatchFilter::class,
            ),
            'ISO2' => array(
                'title'     => $this->fieldLabel('ISO2'),
                'filter'    => PartialMatchFilter::class,
            ),
            'ISO3' => array(
                'title'     => $this->fieldLabel('ISO3'),
                'filter'    => PartialMatchFilter::class,
            ),
            'ISON' => array(
                'title'     => $this->fieldLabel('ISON'),
                'filter'    => PartialMatchFilter::class,
            ),
            'FIPS' => array(
                'title'     => $this->fieldLabel('FIPS'),
                'filter'    => PartialMatchFilter::class,
            ),
            'Continent' => array(
                'title'     => $this->fieldLabel('Continent'),
                'filter'    => PartialMatchFilter::class,
            ),
            'Currency' => array(
                'title'     => $this->fieldLabel('Currency'),
                'filter'    => PartialMatchFilter::class,
            ),
            'PaymentMethods.ID' => array(
                'title'     => $this->fieldLabel('PaymentMethods'),
                'filter'    => PartialMatchFilter::class,
            ),
            'Zones.ID' => array(
                'title'     => $this->fieldLabel('Zones'),
                'filter'    => PartialMatchFilter::class,
            ),
            'Active' => array(
                'title'     => $this->fieldLabel('Active'),
                'filter'    => ExactMatchFilter::class,
            ),
            'IsPrioritive' => array(
                'title'     => $this->fieldLabel('IsPrioritiveShort'),
                'filter'    => ExactMatchFilter::class,
            ),
            'IsNonTaxable' => array(
                'title'     => $this->fieldLabel('IsNonTaxable'),
                'filter'    => ExactMatchFilter::class,
            ),
        );
    }
    
    /**
     * Returns freeOfShippingCostsFrom in a nice format
     *
     * @return string
     */
    public function getFreeOfShippingCostsFromNice() {
        return $this->freeOfShippingCostsFrom->Nice();
    }

        /**
     * Summary fields
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012 
     */
    public function summaryFields() {
        $summaryFields = array_merge(
                parent::summaryFields(),
                array(
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
                )
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
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 10.02.2013
     */
    public function excludeFromScaffolding() {
        $excludeFromScaffolding = array(
            'Zones',
            'Locale'
        );
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
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
        
        $paymentMethodsTable = $fields->dataFieldByName('PaymentMethods');
        $paymentMethodsTable->setConfig(GridFieldConfig_RelationEditor::create());
        
        $languageFields = TranslationTools::prepare_cms_fields($this->getTranslationClassName());
        foreach ($languageFields as $languageField) {
            $fields->insertBefore($languageField, 'ISO2');
        }
        
        $displayPositionMap = array(
            '0' => Tools::field_label('PleaseChoose'),
        );
        for ($x = 1; $x <= self::getPrioritiveCountryCount(false) + 1; $x++) {
            $displayPositionMap[$x] = $x;
        }
        $displayPositionField = new DropdownField('DisplayPosition', $this->fieldLabel('DisplayPosition'), $displayPositionMap);
        $fields->insertAfter($displayPositionField, 'IsPrioritive');

        return $fields;
    }
    
    /**
     * Hook before writing th object
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.12.2012
     */
    public function onBeforeWrite() {
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
    public function getActivityText() {
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
    public function getIsPrioritiveText() {
        if ($this->IsPrioritive) {
            return Tools::field_label('Yes');
        }
        return Tools::field_label('No');
    }

    /**
     * Returns the attributed zones as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function AttributedZones() {
        return Tools::AttributedDataObject($this->Zones());
    }

    /**
     * Returns the attributed payment methods as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function AttributedPaymentMethods() {
        return Tools::AttributedDataObject($this->PaymentMethods());
    }
    
    /**
     * Returns the title
     *
     * @return string $title i18n title
     */
    public function getTitle() {
        return $this->getTranslationFieldValue('Title');
    }
    
    /**
     * Returns all active countries
     * 
     * @return SS_List
     */
    public static function get_active() {
        if (!self::$activeCountries) {
            $activeCountries = Country::get()->filter("Active", 1);
            if (!$activeCountries->exists()) {
                $activeCountries = new ArrayList();
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
     * @return SS_List
     */
    public static function getPrioritiveCountries($onlyActive = true) {
        $key            = 0;
        $addToFilter    = array();
        if ($onlyActive) {
            $key            = 1;
            $addToFilter    = array("Active" => 1);
        }
        if (!array_key_exists($key, self::$prioritiveCountries)) {
            $filter = array_merge(array("IsPrioritive" => 1), $addToFilter);
            $prioritiveCountries = Country::get()
                                    ->filter($filter)
                                    ->sort(array("DisplayPosition" => "ASC", "Title" => "ASC"));
            self::$prioritiveCountries[$key] = $prioritiveCountries;
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
    public static function getPrioritiveCountryCount($onlyActive = true) {
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
     * @return ArrayList
     */
    public static function getNonPrioritiveCountries($onlyActive = true) {
        $key            = 0;
        $addToFilter    = array();
        if ($onlyActive) {
            $key            = 1;
            $addToFilter    = array("Active" => 1);
        }
        if (!array_key_exists($key, self::$nonPrioritiveCountries)) {
            $filter = array_merge(array("IsPrioritive" => 0), $addToFilter);
            $nonPrioritiveCountries = Country::get()
                                        ->filter($filter)
                                        ->sort(array("Title" => "ASC"));
            self::$nonPrioritiveCountries[$key] = $nonPrioritiveCountries;
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
    public static function getNonPrioritiveCountryCount($onlyActive = true) {
        $key = 0;
        if ($onlyActive) {
            $key = 1;
        }
        if (!array_key_exists($key, self::$nonPrioritiveCountryCount)) {
            $nonPrioritiveCountryCount  = 0;
            $nonPrioritiveCountries     = self::getNonPrioritiveCountries($onlyActive);
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
    public static function getPrioritiveDropdownMap($onlyActive = true, $emptyString = null) {
        $key = 0;
        if ($onlyActive) {
            $key = 1;
        }
        if (!is_null($emptyString)) {
            $key .= md5($emptyString);
        }
        if (!array_key_exists($key, self::$prioritiveDropdownMap)) {
            $dropdownMap = array();
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
                if ((is_null($emptyString) && count($dropdownMap) > 0) ||
                    (!is_null($emptyString) && count($dropdownMap) > 1)) {
                    $dropdownMap[' '] = '------------------------';
                }
                $nonPrioritiveCountries = self::getNonPrioritiveCountries($onlyActive);
                foreach ($nonPrioritiveCountries->map()->toArray() as $id => $title) {
                    $dropdownMap[$id] = $title;
                }
            }
            if (empty($dropdownMap) &&
                Tools::isBackendEnvironment()) {
                $allCountries = Country::get();
                $dropdownMap = $allCountries->map()->toArray();
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
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.05.2018
     */
    public static function create_translations($existingLocale, $targetLocale = null) {
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
                if (!($translation instanceof CountryTranslation) ||
                    !$translation->exists()) {

                    $translation = new CountryTranslation();
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
