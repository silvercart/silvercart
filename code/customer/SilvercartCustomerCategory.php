<?php
/*
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
 */

/**
 * abstract for destinguishing customers that may have special prices
 *
 * @package Silvercart
 * @subpackage Customer
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright Pixeltricks GmbH
 * @since 23.10.2010
 */
class SilvercartCustomerCategory extends DataObject {
    public static $singular_name = "customer category";
    public static $plural_name = "customer categories";
    public static $db = array(
        'Title' => 'VarChar'
    );
    public static $has_many = array(
        'prices' => 'SilvercartPrice',
        'customers' => 'Member'
    );
    public static $summary_fields = array(
        "Title" => 'Name'
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
    public function  __construct($record = null, $isSingleton = false) {
        self::$summary_fields = array(
        "Title" => _t('SilvercartProduct.COLUMN_TITLE')
    );
        parent::__construct($record, $isSingleton);
    }
}
