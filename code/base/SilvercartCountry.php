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
 * Abstract for a country
 *
 * @package Silvercart
 * @subpackage Base
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 20.10.2010
 */
class SilvercartCountry extends DataObject {

    /**
     * Singular name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $singular_name = "country";
    /**
     * Plural name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $plural_name = "countries";
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
        'Title' => 'VarChar',
        'ISO2' => 'VarChar',
        'ISO3' => 'VarChar'
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
        'SilvercartPaymentMethods' => 'SilvercartPaymentMethod'
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
        'SilvercartZones' => 'SilvercartZone'
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
        'ISO2',
        'ISO3',
        'AttributedZones',
        'AttributedPaymentMethods'
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
        'Title' => 'Land',
        'ISO2' => 'ISO2 Code',
        'ISO3' => 'ISO3 Code',
        'AttributedZones' => 'Zugeordnete Zonen',
        'AttributedPaymentMethods' => 'Zugeordnete Bezahlarten'
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
        'ISO2',
        'ISO3',
        'SilvercartZones.ID' => array(
            'title' => 'Zugeordnete Zonen'
        ),
        'SilvercartPaymentMethods.ID' => array(
            'title' => 'Zugeordnete Bezahlarten'
        )
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
        'AttributedZones' => 'Varchar(255)',
        'AttributedPaymentMethods' => 'Varchar(255)'
    );

    /**
     * Constructor
     *
     * @param array|null $record      This will be null for a new database record.  Alternatively, you can pass an array of
     *                                field values.  Normally this contructor is only used by the internal systems that get objects from the database.
     * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.  Singletons
     *                                don't have their defaults set.
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 2.2.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        self::$field_labels = array(
            'ISO2' => 'ISO2 Code',
            'ISO3' => 'ISO3 Code',
            'AttributedZones' => _t('Country.ATTRIBUTED_ZONES', 'attributed zones'),
            'AttributedPaymentMethods' => _t('Country.ATTRIBUTED_PAYMENTMETHOD', 'attributed payment method')
        );
        self::$searchable_fields = array(
            'Title',
            'ISO2',
            'ISO3',
            'SilvercartZones.ID' => array(
                'title' => _t('Country.ATTRIBUTED_ZONES')
            ),
            'SilvercartPaymentMethods.ID' => array(
                'title' => _t('Country.ATTRIBUTED_PAYMENTMETHOD')
            )
        );
        parent::__construct($record, $isSingleton);
    }

    /**
     * i18n for field labels
     *
     * @param <type> $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 27.02.2011
     * @copyright 2010 pixeltricks GmbH
     */
    public function fieldLabels($includerelations = true) {
        $fields = parent::fieldLabels($includerelations);
        $fields['Title'] = _t('SilvercartCountry.SINGULARNAME');
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     * 
     * @return FieldSet the fields for the backend
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 28.10.10
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('SilvercartPaymentMethods');
        $fields->removeByName('SilvercartZones');

        $paymentMethodsTable = new ManyManyComplexTableField(
                        $this,
                        'SilvercartPaymentMethods',
                        'SilvercartPaymentMethod',
                        null,
                        'getCmsFields_forPopup'
        );
        $paymentMethodsTable->setAddTitle(_t('SilvercartPaymentMethod.TITLE', 'payment method'));
        $tabParam = "Root." . _t('SilvercartPaymentMethod.TITLE');
        $fields->addFieldToTab($tabParam, $paymentMethodsTable);

        return $fields;
    }

    /**
     * Returns the attributed zones as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function AttributedZones() {
        $attributedZonesStr = '';
        $attributedZones = array();
        $maxLength = 150;

        foreach ($this->SilvercartZones() as $zone) {
            $attributedZones[] = $zone->Title;
        }

        if (!empty($attributedZones)) {
            $attributedZonesStr = implode(', ', $attributedZones);

            if (strlen($attributedZonesStr) > $maxLength) {
                $attributedZonesStr = substr($attributedZonesStr, 0, $maxLength) . '...';
            }
        }

        return $attributedZonesStr;
    }

    /**
     * Returns the attributed payment methods as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function AttributedPaymentMethods() {
        $attributedPaymentMethodsStr = '';
        $attributedPaymentMethods = array();
        $maxLength = 150;

        foreach ($this->SilvercartPaymentMethods() as $paymentMethod) {
            $attributedPaymentMethods[] = $paymentMethod->Name;
        }

        if (!empty($attributedPaymentMethods)) {
            $attributedPaymentMethodsStr = implode(', ', $attributedPaymentMethods);

            if (strlen($attributedPaymentMethodsStr) > $maxLength) {
                $attributedPaymentMethodsStr = substr($attributedPaymentMethodsStr, 0, $maxLength) . '...';
            }
        }

        return $attributedPaymentMethodsStr;
    }
}
