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
 * @subpackage Customer
 */

/**
 * abstract for a customers address
 * As a customer might want to get an order delivered to a third person, the address has a FirstName and Surname.
 *
 * @package Silvercart
 * @subpackage Customer
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 22.10.2010
 */
class SilvercartAddress extends DataObject {

    public static $singular_name = 'address';
    public static $plural_name = 'addresses';
    public static $db = array(
        'Salutation' => 'Enum("Herr,Frau","Herr")',
        'FirstName' => 'VarChar(50)',
        'Surname' => 'VarChar(50)',
        'Addition' => 'VarChar(255)',
        'Street' => 'VarChar(255)',
        'StreetNumber' => 'VarChar(15)',
        'Postcode' => 'VarChar',
        'City' => 'VarChar(100)',
        'PhoneAreaCode' => 'VarChar(10)',
        'Phone' => 'VarChar(50)'
    );
    public static $has_one = array(
        'Member' => 'Member',
        'SilvercartCountry' => 'SilvercartCountry'
    );
    public static $summary_fields = array(
        'Street' => 'Strasse',
        'City' => 'Stadt'
    );
    public static $field_labels = array(
        'Street' => 'Strasse',
        'StreetNumber' => 'Hausnummer',
        'Postcode' => 'PLZ',
        'City' => 'Ort',
        'PhoneAreaCode' => 'Vorwahl',
        'Phone' => 'Telefonnummer',
        'SilvercartCountry' => 'Land',
        'Addition' => 'Adresszusatz',
        'FirstName' => 'Vorname',
        'Surname' => 'Nachname'
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
        self::$summary_fields = array(
            'Street' => _t('SilvercartAddress.STREET'),
            'City' => _t('SilvercartAddress.CITY')
        );
        self::$field_labels = array(
            'Street' => _t('SilvercartAddress.STREET'),
            'StreetNumber' => _t('SilvercartAddress.STREETNUMBER'),
            'Postcode' => _t('SilvercartAddress.POSTCODE'),
            'City' => _t('SilvercartAddress.CITY'),
            'PhoneAreaCode' => _t('SilvercartAddress.PHONEAREACODE'),
            'Phone' => _t('SilvercartAddress.PHONE'),
            'SilvercartCountry' => _t('SilvercartCountry.SINGULARNAME'),
            'Addition' => _t('SilvercartAddress.ADDITION'),
            'FirstName' => _t('SilvercartAddress.FIRSTNAME'),
            'Surname' => _t('SilvercartAddress.SURNAME')
        );
        parent::__construct($record, $isSingleton);
    }

}
