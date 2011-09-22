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
        'ISO3' => 'VarChar',
        'ISON' => 'Int',
        'FIPS' => 'VarChar',
        'Continent' => 'VarChar',
        'Currency' => 'VarChar',
        'Active' => 'Boolean',
    );
    /**
     * Default values
     *
     * @var array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.03.2011
     * @copyright 2011 pixeltricks GmbH
     */
    public static $defaults = array(
        'Active' => false,
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
        'Continent',
        'Currency',
        'AttributedZones',
        'AttributedPaymentMethods',
        'ActivityText',
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
        'AttributedPaymentMethods' => 'Varchar(255)',
        'ActivityText' => 'VarChar'
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
        if (_t('SilvercartCountry.SINGULARNAME')) {
            return _t('SilvercartCountry.SINGULARNAME');
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
        if (_t('SilvercartCountry.PLURALNAME')) {
            return _t('SilvercartCountry.PLURALNAME');
        } else {
            return parent::plural_name();
        }   
    }

    /**
     * i18n for field labels
     *
     * @param bool $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Seabstian Diel <sdiel@pixeltricks.de>
     * @since 27.02.2011
     * @copyright 2010 pixeltricks GmbH
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Title'                     => _t('SilvercartCountry.SINGULARNAME'),
                'ISO2'                      => _t('SilvercartCountry.ISO2', 'ISO Alpha2'),
                'ISO3'                      => _t('SilvercartCountry.ISO3', 'ISO Alpha3'),
                'ISON'                      => _t('SilvercartCountry.ISON', 'ISO numeric'),
                'FIPS'                      => _t('SilvercartCountry.FIPS', 'FIPS code'),
                'Continent'                 => _t('SilvercartCountry.CONTINENT', 'Continent'),
                'Currency'                  => _t('SilvercartCountry.CURRENCY', 'Currency'),
                'Active'                    => _t('SilvercartCountry.ACTIVE', 'Active'),
                'AttributedZones'           => _t('SilvercartCountry.ATTRIBUTED_ZONES', 'attributed zones'),
                'AttributedPaymentMethods'  => _t('SilvercartCountry.ATTRIBUTED_PAYMENTMETHOD', 'attributed payment method'),
                'ActivityText'              => _t('SilvercartCountry.ACTIVE', 'Active'),
            )
        );
    }

    /**
     * Searchable fields of SIlvercartCountry.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.03.2011
     */
    public function  searchableFields() {
        return array(
            'Title' => array(
                'title'     => _t('SilvercartCountry.SINGULARNAME'),
                'filter'    => 'PartialMatchFilter',
            ),
            'ISO2' => array(
                'title'     => _t('SilvercartCountry.ISO2', 'ISO Alpha2'),
                'filter'    => 'PartialMatchFilter',
            ),
            'ISO3' => array(
                'title'     => _t('SilvercartCountry.ISO3', 'ISO Alpha3'),
                'filter'    => 'PartialMatchFilter',
            ),
            'ISON' => array(
                'title'     => _t('SilvercartCountry.ISON', 'ISO numeric'),
                'filter'    => 'PartialMatchFilter',
            ),
            'FIPS' => array(
                'title'     => _t('SilvercartCountry.FIPS', 'FIPS code'),
                'filter'    => 'PartialMatchFilter',
            ),
            'Continent' => array(
                'title'     => _t('SilvercartCountry.CONTINENT', 'Continent'),
                'filter'    => 'PartialMatchFilter',
            ),
            'Currency' => array(
                'title'     => _t('SilvercartCountry.CURRENCY', 'Currency'),
                'filter'    => 'PartialMatchFilter',
            ),
            'SilvercartZones.ID' => array(
                'title'     => _t('SilvercartCountry.ATTRIBUTED_ZONES'),
                'filter'    => 'PartialMatchFilter',
            ),
            'SilvercartPaymentMethods.ID' => array(
                'title'     => _t('SilvercartCountry.ATTRIBUTED_PAYMENTMETHOD'),
                'filter'    => 'PartialMatchFilter',
            )
        );
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
     * Returns the text label for a countries activity.
     *
     * @return string
     */
    public function getActivityText() {
        if ($this->Active) {
            return _t('Silvercart.YES');
        }
        return _t('Silvercart.NO');
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
