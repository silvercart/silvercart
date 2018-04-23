<?php

namespace SilverCart\Model\Product;

use SilverCart\Dev\Tools;
use SilverCart\Model\Product\Manufacturer;
use SilverStripe\ORM\DataObject;

/**
 * translation class for Manufacturer.
 *
 * @package SilverCart
 * @subpackage Model_Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ManufacturerTranslation extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = array(
        'Description' => 'Text'
    );

    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_one = array(
        'Manufacturer' => Manufacturer::class,
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartManufacturerTranslation';

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
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.01.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'Description' => Manufacturer::singleton()->fieldLabel('Description'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
}