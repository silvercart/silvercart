<?php

namespace SilverCart\ORM\FieldType;

use SilverCart\Forms\FormFields\MoneyField;

/**
 * This is an extended Money Field to modify scaffolding and add some functions.
 *
 * @package SilverCart
 * @subpackage ORM_FieldType
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 10.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DBMoney extends \SilverStripe\ORM\FieldType\DBMoney {

    /**
     * Similiar to {@link DataObject::$db},
     * holds an array of composite field names.
     * Don't include the fields "main name",
     * it will be prefixed in {@link requireField()}.
     * 
     * @param array
     */
    private static $composite_db = array(
        "Currency" => "Varchar(3)",
        "Amount" => 'Decimal(19,4)'
    );
    
    /**
     * Returns the amount.
     * 
     * @return float
     */
    public function getAmount() {
        $amount = parent::getAmount();
        $this->extend('updateAmount', $amount);
        return $amount;
    }
    
    /**
     * Returns the currency.
     * 
     * @return string
     */
    public function getCurrency() {
        $currency = parent::getCurrency();
        $this->extend('updateCurrency', $currency);
        return $currency;
    }

    /**
     * Returns the amount formatted.
     *
     * @param array $options The options
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function NiceAmount($options = array()) {
        $options['display'] = Zend_Currency::NO_SYMBOL;
        return $this->Nice($options);
    }

    /**
     * Returns a CompositeField instance used as a default
     * for form scaffolding.
     *
     * Used by {@link SearchContext}, {@link ModelAdmin}, {@link DataObject::scaffoldFormFields()}
     * 
     * @param string $title  Optional. Localized title of the generated instance
     * @param array  $params Optional params.
     * 
     * @return FormField
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.10.2017
     */
    public function scaffoldFormField($title = null, $params = null) {
        return MoneyField::create($this->getName(), $title)
            ->setLocale($this->getLocale());
    }

}