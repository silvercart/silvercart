<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
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
 * @subpackage Base
 */

/**
 * Class for processing transaction costs etc.
 *
 * @package Silvercart
 * @subpackage Base
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 09.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartHandlingCost extends DataObject {

    /**
     * Singular name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.02.2011
     */
    public static $singular_name = "handling cost";

    /**
     * Plural name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.02.2011
     */
    public static $plural_name = "handling costs";

    /**
     * Attributes.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.02.2011
     */
    public static $db = array(
        'amount' => 'Money'
    );

    /**
     * Casting.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.03.2012
     */
    public static $casting = array(
        'handlingcosts' => 'Text'
    );

    /**
     * Has-one relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.02.2011
     */
    public static $has_one = array(
        'SilvercartTax' => 'SilvercartTax'
    );

    /**
     * Summary fields for display in tables.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.02.2011
     */
    public static $summary_fields = array(
        'handlingcosts'
    );

    /**
     * Labels for the columns in tables.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.02.2011
     */
    public static $field_labels = array(
        'amount' => 'Betrag'
    );
    
    /**
     * Sets the field labels.
     *
     * @param bool $includerelations set to true to include the DataObjects relations
     * 
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                array(
                   'amount' => _t('SilvercartHandlingCost.AMOUNT', 'amount') 
                    )
                );
    }

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.03.2012
     */
    public function singular_name() {
        if (_t('SilvercartHandlingCost.SINGULARNAME')) {
            return _t('SilvercartHandlingCost.SINGULARNAME');
        } else {
            return parent::singular_name();
        }
    }

    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects plural name 
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.03.2012
     */
    public function plural_name() {
        if (_t('SilvercartHandlingCost.PLURALNAME')) {
            return _t('SilvercartHandlingCost.PLURALNAME');
        } else {
            return parent::plural_name();
        }
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.03.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'handlingcosts' => _t('SilvercartHandlingCost.AMOUNT'),
        );
        $this->extend('updateSummaryFields', $summaryFields);

        return $summaryFields;
    }

    /**
     * Returns the prices amount
     *
     * @return float
     */
    public function getPriceAmount() {
        $price = (float) $this->amount->getAmount();

        if (SilvercartConfig::PriceType() == 'net') {
            $price = $price - $this->getTaxAmount();
        }

        return $price;
    }

    /**
     * returns the tax amount included in $this
     *
     * @return float
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.02.2011
     */
    public function getTaxAmount() {
        $taxRate = $this->amount->getAmount() - ($this->amount->getAmount() / (100 + $this->SilvercartTax()->getTaxRate()) * 100);

        return $taxRate;
    }

    /**
     * Returns the Price formatted by locale.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function PriceFormatted() {
        $priceObj = new Money();
        $priceObj->setAmount($this->getPriceAmount());
        $priceObj->setCurrency($this->price->getCurrency());

        return $priceObj->Nice();
    }

    /**
     * Returns the handling costs for display in tables.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.03.2012
     */
    public function handlingcosts() {
        return $this->amount->Nice();
    }
}
