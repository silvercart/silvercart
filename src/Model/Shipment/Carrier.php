<?php

namespace SilverCart\Model\Shipment;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Shipment\ShippingMethod;
use SilverCart\Model\Shipment\CarrierTranslation;
use SilverCart\Model\Shipment\Zone;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\Filters\ExactMatchFilter;
use SilverStripe\ORM\Filters\PartialMatchFilter;

/**
 * abstract for a shipping carrier.
 *
 * @package SilverCart
 * @subpackage Model_Shipment
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Carrier extends DataObject {

    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = array(
        'priority' => 'Int'
    );

    /**
     * Has-many relationship.
     *
     * @var array
     */
    private static $has_many = array(
        'ShippingMethods'     => ShippingMethod::class,
        'CarrierTranslations' => CarrierTranslation::class,
    );

    /**
     * Many to many relations
     * 
     * @var array
     */
    private static $belongs_many_many = array(
        'Zones' => Zone::class,
    );

    /**
     * Virtual database fields.
     *
     * @var array
     */
    private static $casting = array(
        'AttributedZones'           => 'Varchar(255)',
        'AttributedShippingMethods' => 'Varchar(255)',
        'Title'                     => 'Varchar(25)',
        'FullTitle'                 => 'Varchar(60)',
    );

    /**
     * Default sort field and direction
     *
     * @var string
     */
    private static $default_sort = "priority DESC";

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartCarrier';
    
    /**
     * retirieves title from related language class depending on the set locale
     *
     * @return string 
     */
    public function getTitle() {
        return $this->getTranslationFieldValue('Title');
    }
    
    /**
     * retirieves title from related language class depending on the set locale
     *
     * @return string 
     */
    public function getFullTitle() {
        return $this->getTranslationFieldValue('FullTitle');
    }
    
    /**
     * Defines the form fields for the search in ModelAdmin
     * 
     * @return array seach fields definition
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2012
     */
    public function searchableFields() {
        $searchableFields = array(
            'CarrierTranslations.Title' => array(
                'title' => $this->fieldLabel('Title'),
                'filter' => PartialMatchFilter::class,
            ),
            'Zones.ID' => array(
                'title' => $this->fieldLabel('Zones'),
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
     * CMS fields
     *
     * @param array $params Params to use
     *
     * @return FieldList
     */
    public function getCMSFields($params = null) {
        $fields = DataObjectExtension::getCMSFields($this);
        return $fields;
    }

    /**
     * Returns the objects field labels
     * 
     * @param bool $includerelations configuration setting
     * 
     * @return array the translated field labels 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'AttributedZones'           => _t(Country::class . '.ATTRIBUTED_ZONES', 'Attributed zones'),
                    'AttributedShippingMethods' => _t(Carrier::class . '.ATTRIBUTED_SHIPPINGMETHODS', 'Attributed shipping methods'),
                    'ShippingMethods'           => ShippingMethod::singleton()->plural_name(),
                    'Zones'                     => Zone::singleton()->plural_name(),
                    'CarrierTranslations'       => CarrierTranslation::singleton()->plural_name(),
                    'Title'                     => Page::singleton()->fieldLabel('Title'),
                    'priority'                  => Tools::field_label('Priority'),
                    'FullTitle'                 => _t(Carrier::class . '.FULL_NAME', 'Full name'),
                )
        );
    }
    
    /**
     * Sets the summary fields.
     *
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.02.2013
     */
    public function summaryFields() {
        $summaryFields = array(
            'Title'                     => $this->fieldLabel('Title'),
            'AttributedZones'           => $this->fieldLabel('AttributedZones'),
            'AttributedShippingMethods' => $this->fieldLabel('AttributedShippingMethods'),
            'priority'                  => $this->fieldLabel('priority'),
        );
        
        $this->extend('updateSummaryFields', $summaryFields);
        
        return $summaryFields;
    }
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return Tools::plural_name_for($this); 
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
     * Returns all allowed shipping methods for the this carrier
     *
     * @return ShippingMethod 
     */
    public function getAllowedShippingMethods() {
        return ShippingMethod::getAllowedShippingMethodsForOverview($this);
    }
    
}