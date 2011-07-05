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
 * @subpackage Order
 */

/**
 * abstract for a single position of an order
 * they are not changeable after creation and serve as a history
 *
 * @package Silvercart
 * @subpackage Order
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 22.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartOrderPosition extends DataObject {

    /**
     * attributes
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $db = array(
        'Price' => 'Money',
        'PriceTotal' => 'Money',
        'Tax' => 'Float',
        'TaxTotal' => 'Float',
        'TaxRate' => 'Float',
        'ProductDescription' => 'Text',
        'Quantity' => 'Int',
        'Title' => 'VarChar',
        'ProductNumber' => 'VarChar',
    );
    /**
     * 1:n relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $has_one = array(
        'SilvercartOrder' => 'SilvercartOrder',
        'SilvercartProduct' => 'SilvercartProduct'
    );
    public static $casting = array(
        'PriceNice' => 'VarChar(255)',
        'PriceTotalNice' => 'VarChar(255)',
    );
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011
     */
    public function singular_name() {
        if (_t('SilvercartOrderPosition.SINGULARNAME')) {
            return _t('SilvercartOrderPosition.SINGULARNAME');
        } else {
            return parent::singular_name();
        } 
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011 
     */
    public function plural_name() {
        if (_t('SilvercartOrderPosition.PLURALNAME')) {
            return _t('SilvercartOrderPosition.PLURALNAME');
        } else {
            return parent::plural_name();
        }   
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2011
     */
    public function summaryFields() {
        $summaryFields = array(
            'ProductNumber'         => _t('SilvercartProduct.PRODUCTNUMBER'),
            'Title'                 => _t('SilvercartPage.PRODUCTNAME'),
            'ProductDescription'    => _t('SilvercartProduct.DESCRIPTION'),
            'PriceNice'             => _t('SilvercartProduct.PRICE'),
            'Quantity'              => _t('SilvercartProduct.QUANTITY'),
        );
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }

    /**
     * returns the orders total amount as string incl. currency.
     *
     * @return string
     */
    public function getPriceNice() {
        return str_replace('.', ',', number_format($this->PriceAmount, 2)) . ' ' . $this->PriceCurrency;
    }

    /**
     * returns the orders total amount as string incl. currency.
     *
     * @return string
     */
    public function getPriceTotalNice() {
        return str_replace('.', ',', number_format($this->PriceTotalAmount, 2)) . ' ' . $this->PriceTotalCurrency;
    }

    /**
     * Returns true if this position has a quantity of more than 1.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 13.04.2011
     */
    public function MoreThanOneProduct() {
        $moreThanOneProduct = false;

        if ($this->Quantity > 1) {
            $moreThanOneProduct = true;
        }

        return $moreThanOneProduct;
    }
}
