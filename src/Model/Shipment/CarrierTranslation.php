<?php

namespace SilverCart\Model\Shipment;

use SilverCart\Dev\Tools;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Shipment\Carrier;
use SilverStripe\ORM\DataObject;

/**
 * Translations for Carrier.
 *
 * @package SilverCart
 * @subpackage Model_Shipment
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CarrierTranslation extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = array(
        'Title'     => 'Varchar(25)',
        'FullTitle' => 'Varchar(60)'
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_one = array(
        'Carrier' => Carrier::class,
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartCarrierTranslation';
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.09.2017
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.09.2017
     */
    public function plural_name() {
        return Tools::plural_name_for($this); 
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.09.2017
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'Title'     => Product::singleton()->fieldLabel('Title'),
                    'FullTitle' => Carrier::singleton()->fieldLabel('FullTitle'),
                    'Carrier'   => Carrier::singleton()->singular_name(),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
}