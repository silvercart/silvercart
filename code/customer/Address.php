<?php

/**
 * abstract for a customers address
 * As a customer might want to get an order delivered to a third person, the address has a FirstName and Surname.
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license BSD
 * @since 22.10.2010
 */
class Address extends DataObject {

    public static $singular_name = 'address';
    public static $plural_name = 'addresses';
    public static $db = array(
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
        'owner' => 'Member',
        'country' => 'Country'
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
        'country' => 'Land',
        'Addition' => 'Adresszusatz',
        'FirstName' => 'Vorname',
        'Surname' => 'Nachname'
    );
}
