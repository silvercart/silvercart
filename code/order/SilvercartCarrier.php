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
 * abstract for a shipping carrier
 *
 * @package Silvercart
 * @subpackage Order
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 06.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCarrier extends DataObject {

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
        'Title'             => 'VarChar(25)',
        'FullTitle'         => 'VarChar(60)'
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
        'SilvercartShippingMethods'   => 'SilvercartShippingMethod',
        'SilvercartZones'             => 'SilvercartZone'
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
        'Title'                     => 'Name',
        'AttributedZones'           => 'Zugeordnete Zonen',
        'AttributedShippingMethods' => 'Zugeordnete Versandarten'
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
        'Title'                     => 'Name',
        'FullTitle'                 => 'voller Name',
        'AttributedZones'           => 'Zugeordnete Zonen',
        'AttributedShippingMethods' => 'Zugeordnete Versandarten'
    );

    /**
     * Virtual database fields.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $casting = array(
        'AttributedZones'           => 'Varchar(255)',
        'AttributedShippingMethods' => 'Varchar(255)'
    );
    
    /**
     * Defines the form fields for the search in ModelAdmin
     * 
     * @return array seach fields definition
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011
     */
    public function searchableFields() {
        $seachableFields = array(
            'Title' => array(
                'title' => _t('SilvercartCarrier.SINGULARNAME'),
                'filter' => 'PartialMatchFilter'
            ),
            'SilvercartZones.ID' => array(
                'title' => _t('SilvercartCountry.ATTRIBUTED_ZONES'),
                'filter' => 'ExactMatchFilter'
            ),
            'SilvercartShippingMethods.ID' => array(
                'title' => _t('SilvercartCarrier.ATTRIBUTED_SHIPPINGMETHODS'),
                'filter' => 'ExactMatchFilter'
            )
        );
        $this->extend('updateSearchableFields', $searchableFields);
        return $seachableFields;
    }

    /**
     * Returns the objects field labels
     * 
     * @param bool $includerelations configuration setting
     * 
     * @return array the translated field labels 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'Title'                     => _t('SilvercartProduct.COLUMN_TITLE'),
                    'FullTitle'                 => _t('SilvercartCarrier.FULL_NAME', 'full name'),
                    'AttributedZones'           => _t('SilvercartCountry.ATTRIBUTED_ZONES'),
                    'AttributedShippingMethods' => _t('SilvercartCarrier.ATTRIBUTED_SHIPPINGMETHODS'),
                    'SilvercartShippingMethods' => _t('SilvercartShippingMethod.PLURALNAME', 'zones'),
                    'SilvercartZones'           => _t('SilvercartZone.PLURALNAME', 'zones') 
                )
        );
    }
    
    /**
     * Sets the summary fields.
     *
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011
     */
    public function summaryFields() {
        return array_merge(
            parent::summaryFields(),
            array(
                'Title'                     => _t('SilvercartProduct.COLUMN_TITLE'),
                'AttributedZones'           => _t('SilvercartCountry.ATTRIBUTED_ZONES'),
                'AttributedShippingMethods' => _t('SilvercartCarrier.ATTRIBUTED_SHIPPINGMETHODS', 'attributed shipping methods')
            )
        );
    }
    
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
        if (_t('SilvercartCarrier.SINGULARNAME')) {
            return _t('SilvercartCarrier.SINGULARNAME');
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
        if (_t('SilvercartCarrier.PLURALNAME')) {
            return _t('SilvercartCarrier.PLURALNAME');
        } else {
            return parent::plural_name();
        }   
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
        $attributedZones    = array();
        $maxLength          = 150;

        foreach ($this->SilvercartZones() as $zone) {
            $attributedZones[] = $zone->Title;
        }

        if (!empty($attributedZones)) {
            $attributedZonesStr = implode(', ', $attributedZones);

            if (strlen($attributedZonesStr) > $maxLength) {
                $attributedZonesStr = substr($attributedZonesStr, 0, $maxLength).'...';
            }
        }

        return $attributedZonesStr;
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
        $attributedShippingMethods    = array();
        $maxLength          = 150;

        foreach ($this->SilvercartShippingMethods() as $shippingMethod) {
            $attributedShippingMethods[] = $shippingMethod->Title;
        }

        if (!empty($attributedShippingMethods)) {
            $attributedShippingMethodsStr = implode(', ', $attributedShippingMethods);

            if (strlen($attributedShippingMethodsStr) > $maxLength) {
                $attributedShippingMethodsStr = substr($attributedShippingMethodsStr, 0, $maxLength).'...';
            }
        }

        return $attributedShippingMethodsStr;
    }
}
