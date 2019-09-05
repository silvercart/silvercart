<?php

namespace SilverCart\Forms\FormFields;

use NumberFormatter;
use SilverCart\Admin\Model\Config;
use SilverStripe\Forms\HiddenField;
use SilverStripe\ORM\FieldType\DBMoney;
use SilverStripe\View\Requirements;
use SilverStripe\Forms\MoneyField as SilverStripeMoneyField;

/** 
 * A formfield for editing Money values.
 *
 * @package SilverCart
 * @subpackage Forms_FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class MoneyField extends SilverStripeMoneyField
{
    /**
     * Determines whether the currency is a readonly property.
     *
     * @var bool
     */
    protected $currencyIsReadonly = true;
    
    /**
     * Builds a new currency field based on the allowed currencies configured.
     *
     * @return FormField
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.10.2017
     */
    protected function buildCurrencyField() {
        if ($this->getCurrencyIsReadonly()) {
            $field = HiddenField::create("{$this->getName()}[Currency]");
            $field->setValue(Config::DefaultCurrency());
            $this->fieldCurrency = $field;
        } else {
            $field = parent::buildCurrencyField();
        }
        return $field;
    }

    /**
     * Returns the field markup
     * 
     * @param array $properties not in use, just declared to be compatible with parent
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.05.2012
     */
    public function Field($properties = []) {
        if ($this->getCurrencyIsReadonly()) {
            Requirements::css('silvercart/silvercart:client/admin/css/LeftAndMainExtension.css');
            $this->fieldAmount->setTitle($this->Title());
            $this->fieldAmount->addExtraClass('silvercartmoney');
        }
        return parent::Field($properties);
    }

    /**
     * Set the Money value
     *
     * @param mixed $val Value to set
     * 
     * @return void
     */
    public function setValue($value, $data = null) {
        $defaultCurrency = Config::DefaultCurrency();

        if (is_array($value)) {
            if (empty($value['Currency'])) {
                $value['Currency'] = $defaultCurrency;
            }
        } elseif ($value instanceof DBMoney) {
            if ($value->getCurrency() != $defaultCurrency &&
                $this->getCurrencyIsReadonly()) {
                $value->setCurrency($defaultCurrency);
            }
        }
        return parent::setValue($value, $data);
    }
    
    /**
     * Returns the fields id (html)
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.05.2012
     */
    public function id() {
        return $this->fieldAmount->id();
    }
    
    /**
     * Prepares the given amount to save in db.
     * Makes som format corrections in case of wrong format
     *
     * @param string $amount Amount to check
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.12.2013
     */
    public function prepareAmount($amount) {
        if (strpos($amount, ',') !== false) {
            if (strpos($amount, '.') !== false &&
                strpos($amount, '.') > strpos($amount, ',')) {
                // amount has the format 1,234.56
                $amount = str_replace(',', '', $amount);
            } elseif (strpos($amount, '.') !== false &&
                      strpos($amount, '.') < strpos($amount, ',')) {
                // amount has the format 1.234,56
                $amount = str_replace('.', '', $amount);
                $amount = str_replace(',', '.', $amount);
            } elseif (strpos($amount, '.') === false) {
                // amount has the format 1234,56
                $amount = str_replace(',', '.', $amount);
            }
        } elseif (!is_numeric($amount)) {
            $amount = 0;
        }
        return (float) $amount;
    }
    
    /**
     * Returns whether the currency is a readonly property.
     * 
     * @return bool
     */
    public function getCurrencyIsReadonly() {
        return $this->currencyIsReadonly;
    }

    /**
     * Sets whether the currency is a readonly property.
     * 
     * @param bool $currencyIsReadonly Currency is readonly?
     * 
     * @return MoneyField
     */
    public function setCurrencyIsReadonly(bool $currencyIsReadonly) : MoneyField
    {
        $this->currencyIsReadonly = $currencyIsReadonly;
        $this->buildCurrencyField();
        return $this;
    }
    
    /**
     * Returns the currency symbol.
     * 
     * @return string
     */
    public function CurrencySymbol() : string
    {
        return $this->getDBMoney()->getFormatter()->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
    }
}