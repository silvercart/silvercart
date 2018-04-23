<?php

namespace SilverCart\Model\Product;

use SilverCart\Dev\Tools;
use SilverCart\Model\Product\QuantityUnitTranslation;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataObject;

/**
 * Abstract for QuantityUnit.
 *
 * @package SilverCart
 * @subpackage Model_Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class QuantityUnit extends DataObject {

    /**
     * Attributes
     *
     * @var array
     */
    private static $db = array(
        'numberOfDecimalPlaces' => 'Int'
    );

    /**
     * cast field types to other SS data types
     *
     * @var array
     */
    private static $casting = array(
        'Title'          => 'Text',
        'Abbreviation'   => 'Text'
    );
    
    /**
     * 1:n relations
     *
     * @var array
     */
    private static $has_many = array(
        'QuantityUnitTranslations' => QuantityUnitTranslation::class,
    );

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
     * Returns the translated singular name of the object.
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function singular_name() {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function plural_name() {
        return Tools::plural_name_for($this);
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @since 29.03.2011
     */
    public function summaryFields() {
        return array_merge(
            parent::summaryFields(),
            array(
                'Title'             => $this->fieldLabel('Title'),
                'Abbreviation'      => $this->fieldLabel('Abbreviation'),
            )
        );
    }

    /**
     * Field labels for display in tables.
     * 
     * @param bool $includerelations config option
     *
     * @return array
     *
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @since 29.03.2011
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
            parent::fieldLabels(),
            array(
                'Title'                      => _t(QuantityUnit::class . '.NAME', 'Name'),
                'Abbreviation'               => _t(QuantityUnit::class . '.ABBREVIATION', 'Abbreviation'),
                'QuantityUnitTranslations'   => QuantityUnitTranslation::singleton()->plural_name(),
                'numberOfDecimalPlaces'      => _t(QuantityUnit::class . '.NUMBER_OF_DECIMAL_PLACES', 'Number of decimal places'),
                'ExplanationToDecimalPlaces' => _t(QuantityUnit::class . '.EXPLANATION_TO_DECIMAL_PLACES', 'Leave empty or set to 0 to use no decimal places. This setting is used e.g. for add to cart forms.'),
            )
        );
    }
}