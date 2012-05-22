<?php
/**
 * Copyright 2012 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage FormFields
 */

/**
 * A formfield for editing Money values
 *
 * @package Silvercart
 * @subpackage FormFields
 * @copyright pixeltricks GmbH
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.05.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License 
 */
class SilvercartMoneyField extends MoneyField {

    /**
     * Returns the field markup
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.05.2012
     */
    public function Field() {
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