<?php

namespace SilverCart\Model\Product;

use SilverCart\Dev\Tools;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\QuantityUnitTranslation;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\HasManyList;

/**
 * Abstract for QuantityUnit.
 *
 * @package SilverCart
 * @subpackage Model_Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property int $numberOfDecimalPlaces Number of decimal places
 * 
 * @property string $Title        Title (current locale context)
 * @property string $Abbreviation Abbreviation (current locale context)
 * 
 * @method HasManyList Products()                 Returns a list of related Products.
 * @method HasManyList QuantityUnitTranslations() Returns a list of translations for this QuantityUnit.
 */
class QuantityUnit extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * Attributes
     *
     * @var array
     */
    private static $db = [
        'numberOfDecimalPlaces' => 'Int'
    ];
    /**
     * cast field types to other SS data types
     *
     * @var array
     */
    private static $casting = [
        'Title'          => 'Text',
        'Abbreviation'   => 'Text'
    ];
    /**
     * 1:n relations
     *
     * @var array
     */
    private static $has_many = [
        'Products'                 => Product::class,
        'QuantityUnitTranslations' => QuantityUnitTranslation::class,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartQuantityUnit';
    
    /**
     * getter for the quantity units title
     *
     * @return string
     */
    public function getTitle() {
        return $this->getTranslationFieldValue('Title');
    }
    
    /**
     * getter for the quantity units title
     *
     * @return string
     */
    public function getAbbreviation() {
        return $this->getTranslationFieldValue('Abbreviation');
    }
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = DataObjectExtension::getCMSFields($this);

        $fields->insertAfter(
            new LiteralField(
                'ExplanationToDecimalPlaces',
                '<p class="silvercart-formfield-label"><i>' . $this->fieldLabel('ExplanationToDecimalPlaces') . '</i></p>'
            ),
            'numberOfDecimalPlaces'
        );
        
        return $fields;
    }
    
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
     * Summaryfields for display in tables.
     *
     * @return array
     */
    public function summaryFields() : array
    {
        $summaryFields = [
            'Title'        => $this->fieldLabel('Title'),
            'Abbreviation' => $this->fieldLabel('Abbreviation'),
        ];
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
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
            'Title'                      => _t(QuantityUnit::class . '.NAME', 'Name'),
            'Abbreviation'               => _t(QuantityUnit::class . '.ABBREVIATION', 'Abbreviation'),
            'QuantityUnitTranslations'   => QuantityUnitTranslation::singleton()->plural_name(),
            'numberOfDecimalPlaces'      => _t(QuantityUnit::class . '.NUMBER_OF_DECIMAL_PLACES', 'Number of decimal places'),
            'ExplanationToDecimalPlaces' => _t(QuantityUnit::class . '.EXPLANATION_TO_DECIMAL_PLACES', 'Leave empty or set to 0 to use no decimal places. This setting is used e.g. for add to cart forms.'),
        ]);
    }
}