<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Forms_FormFields
 */

/**
 * A formfield for editing Money values
 *
 * @package Silvercart
 * @subpackage Forms_FormFields
 * @copyright 2013 pixeltricks GmbH
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.05.2012
 * @license see license file in modules root directory 
 */
class SilvercartMoneyField extends MoneyField {

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
    public function Field($properties = array()) {
        Requirements::css('silvercart/css/backend/SilvercartMain.css');
        $this->fieldAmount->setTitle($this->Title());
        $this->fieldAmount->addExtraClass('silvercartmoney');
        return $this->fieldAmount->Field() . " " . $this->fieldCurrency->Value() . $this->fieldCurrency->FieldHolder();
    }

    /**
     * Returns the currency field
     * 
     * @param string $name Name of field
     * 
     * @return FormField
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.05.2012
     */
    public function FieldCurrency($name) {
        $field = new HiddenField(
                "{$name}[Currency]",
                _t('MoneyField.FIELDLABELCURRENCY', 'Currency')
        );
        return $field;
    }

    /**
     * Set the Money value
     *
     * @param mixed $val Value to set
     * 
     * @return void
     */
    public function setValue($val) {
        $defaultCurrency = SilvercartConfig::DefaultCurrency();
        
        if (is_array($val)) {
            if (empty($val['Currency'])) {
                $val['Currency'] = $defaultCurrency;
            }
            if (!empty($val['Amount'])) {
                $val['Amount'] = $this->prepareAmount($val['Amount']);
            }
        } elseif ($val instanceof Money) {
            if ($val->getCurrency() != $defaultCurrency) {
                $val->setCurrency($defaultCurrency);
            }
            if ($val->getAmount() != '') {
                $val->setAmount($this->prepareAmount($val->getAmount()));
            }
        }
        parent::setValue($val);
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
     * @since 22.05.2012
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
        }
        return $amount;
    }
    
}