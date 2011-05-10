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
 * abstract for a shipping zone; makes it easier to calculate shipping rates
 * Every carrier might have it´s own zones. That´s why zones:countries is n:m
 *
 * @package Silvercart
 * @subpackage Base
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 23.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartZone extends DataObject {

    /**
     * Singular name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $singular_name = "zone";
    /**
     * Plural name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $plural_name = "zones";
    /**
     * Attributes.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $db = array(
        'Title' => 'VarChar'
    );
    /**
     * Has-one relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $has_one = array(
        'SilvercartCarrier' => 'SilvercartCarrier'
    );
    /**
     * Has-many relationship.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $has_many = array(
        'SilvercartShippingFees' => 'SilvercartShippingFee'
    );
    /**
     * Many-many relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $many_many = array(
        'SilvercartCountries' => 'SilvercartCountry'
    );
    /**
     * Belongs-many-many relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $belongs_many_many = array(
        'SilvercartShippingMethods' => 'SilvercartShippingMethod'
    );
    /**
     * Summaryfields for display in tables.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $summary_fields = array(
        'Title',
        'SilvercartCarrier.Title',
        'AttributedCountries',
        'AttributedShippingMethods'
    );
    /**
     * Column labels for display in tables.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $field_labels = array(
        'Title' => 'Name',
        'SilvercartCarrier.Title' => 'Frachtführer',
        'AttributedCountries' => 'Für Länder',
        'AttributedShippingMethods' => 'Zugeordnete Versandarten'
    );
    /**
     * Virtual database columns.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $casting = array(
        'AttributedCountries' => 'Varchar(255)',
        'AttributedShippingMethods' => 'Varchar(255)'
    );
    /**
     * List of searchable fields for the model admin
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $searchable_fields = array(
        'Title',
        'SilvercartCarrier.ID' => array(
            'title' => 'Frachtführer'
        ),
        'SilvercartCountries.ID' => array(
            'title' => 'Für Länder'
        ),
        'SilvercartShippingMethods.ID' => array(
            'title' => 'Zugeordnete Versandarten'
        )
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 24.01.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        self::$field_labels = array(
            'Title' => _t('SilvercartPage.TITLE', 'title'),
            'SilvercartCarrier' => _t('SilvercartCarrier.SINGULARNAME', 'carrier'),
            'SilvercartCarrier.Title' => _t('SilvercartCarrier.SINGULARNAME'),
            'AttributedCountries' => _t('SilvercartZone.ATTRIBUTED_COUNTRIES', 'attributed countries'),
            'AttributedShippingMethods' => _t('SilvercartZone.ATTRIBUTED_SHIPPINGMETHODS', 'attributed shipping methods')
        );
        self::$searchable_fields = array(
            'Title',
            'SilvercartCarrier.ID' => array(
                'title' => _t('SilvercartCarrier.SINGULARNAME')
            ),
            'SilvercartCountries.ID' => array(
                'title' => _t('SilvercartZone.FOR_COUNTRIES', 'for countries')
            ),
            'SilvercartShippingMethods.ID' => array(
                'title' => _t('SilvercartZone.ATTRIBUTED_SHIPPINGMETHODS')
            )
        );
        self::$singular_name = _t('SilvercartZone.SINGULARNAME', 'zone');
        self::$plural_name = _t('SilvercartZone.PLURALNAME', 'zones');
        parent::__construct($record, $isSingleton);
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldSet the fields for the backend
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 7.11.10
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('SilvercartCountries');
        $countriesTable = new ManyManyComplexTableField(
                        $this,
                        'SilvercartCountries',
                        'SilvercartCountry',
                        array('Title' => _t('SilvercartCountry.SINGULARNAME')),
                        'getCMSFields_forPopup',
                        null,
                        'Title'
        );
        $tabParam = "Root." . _t('SilvercartZone.COUNTRIES', 'countries');
        $fields->addFieldToTab($tabParam, $countriesTable);
        return $fields;
    }

    /**
     * Returns the attributed countries as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function AttributedCountries() {
        $attributedCountriesStr = '';
        $attributedCountries = array();
        $maxLength = 150;

        foreach ($this->SilvercartCountries() as $silvercartCountry) {
            $attributedCountries[] = $silvercartCountry->Title;
        }

        if (!empty($attributedCountries)) {
            $attributedCountriesStr = implode(', ', $attributedCountries);

            if (strlen($attributedCountriesStr) > $maxLength) {
                $attributedCountriesStr = substr($attributedCountriesStr, 0, $maxLength) . '...';
            }
        }

        return $attributedCountriesStr;
    }

    /**
     * Returns the attributed shipping methods as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function AttributedShippingMethods() {
        $attributedShippingMethodsStr = '';
        $attributedShippingMethods = array();
        $maxLength = 150;

        foreach ($this->SilvercartShippingMethods() as $silvercartShippingMethod) {
            $attributedShippingMethods[] = $silvercartShippingMethod->Title;
        }

        if (!empty($attributedShippingMethods)) {
            $attributedShippingMethodsStr = implode(', ', $attributedShippingMethods);

            if (strlen($attributedShippingMethodsStr) > $maxLength) {
                $attributedShippingMethodsStr = substr($attributedShippingMethodsStr, 0, $maxLength) . '...';
            }
        }

        return $attributedShippingMethodsStr;
    }

}
