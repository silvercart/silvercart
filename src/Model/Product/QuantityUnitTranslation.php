<?php

namespace SilverCart\Model\Product;

use SilverCart\Dev\Tools;
use SilverCart\Model\Product\QuantityUnit;
use SilverStripe\ORM\DataObject;

/**
 * translation for multilingual quantity unit fields.
 *
 * @package SilverCart
 * @subpackage Model_Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $Title        Title
 * @property string $Abbreviation Abbreviation
 * 
 * @method QuantityUnit QuantityUnit() Returns the related QuantityUnit.
 */
class QuantityUnitTranslation extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * attributes
     *
     * @var array
     */
    private static $db = [
        'Title'        => 'Varchar(50)',
        'Abbreviation' => 'Varchar(5)'
    ];
    /**
     * 1:n relations
     *
     * @var array
     */
    private static $has_one = [
        'QuantityUnit' => QuantityUnit::class,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartQuantityUnitTranslation';
    
    /**
     * Returns the translated singular name.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this); 
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'Title'        => QuantityUnit::singleton()->fieldLabel('Title'),
            'Abbreviation' => QuantityUnit::singleton()->fieldLabel('Abbreviation'),
        ]);
    }
}