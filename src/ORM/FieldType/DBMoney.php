<?php

namespace SilverCart\ORM\FieldType;

use NumberFormatter;
use SilverCart\Admin\Model\Config;
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
class DBMoney extends \SilverStripe\ORM\FieldType\DBMoney
{
    /**
     * Similiar to {@link DataObject::$db},
     * holds an array of composite field names.
     * Don't include the fields "main name",
     * it will be prefixed in {@link requireField()}.
     * 
     * @param array
     */
    private static $composite_db = [
        "Currency" => "Varchar(3)",
        "Amount"   => 'Decimal(19,4)'
    ];
    
    /**
     * Returns the amount.
     * 
     * @return float
     */
    public function getAmount()
    {
        $amount = parent::getAmount();
        $this->extend('updateAmount', $amount);
        return $amount;
    }
    
    /**
     * Returns the currency.
     * 
     * @return string
     */
    public function getCurrency()
    {
        $currency = parent::getCurrency();
        if (empty($currency)) {
            $currency = Config::DefaultCurrency();
        }
        $this->extend('updateCurrency', $currency);
        return $currency;
    }

    /**
     * Returns the amount formatted.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.10.2018
     */
    public function NiceAmount()
    {
        if (!$this->exists()) {
            return null;
        }
        $amount    = $this->getAmount();
        $locale    = $this->getLocale();
        $formatter = NumberFormatter::create($locale, NumberFormatter::DECIMAL);
        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 2);
        return $formatter->format($amount);
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
    public function scaffoldFormField($title = null, $params = null)
    {
        return MoneyField::create($this->getName(), $title)
            ->setLocale($this->getLocale());
    }
}