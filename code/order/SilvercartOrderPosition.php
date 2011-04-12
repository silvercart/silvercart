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
     * Constructor. We localize the static variables here.
     *
     * @param array|null $record      This will be null for a new database record.
     *                                  Alternatively, you can pass an array of
     *                                  field values.  Normally this contructor is only used by the internal systems that get objects from the database.
     * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.  Singletons
     *                                  don't have their defaults set.
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        self::$singular_name = _t('SilvercartOrderPosition.SINGULARNAME');
        self::$plural_name = _t('SilvercartOrderPosition.PLURALNAME');
        parent::__construct($record, $isSingleton);
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
        return array(
            'ProductNumber'         => _t('SilvercartProduct.PRODUCTNUMBER'),
            'Title'                 => _t('SilvercartPage.PRODUCTNAME'),
            'ProductDescription'    => _t('SilvercartProduct.DESCRIPTION'),
            'PriceNice'             => _t('SilvercartProduct.PRICE'),
            'Quantity'              => _t('SilvercartProduct.QUANTITY'),
        );
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

}
