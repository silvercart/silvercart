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
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'ISO2'                      => 'VarChar',
        'ISO3'                      => 'VarChar',
        'ISON'                      => 'Int',
        'FIPS'                      => 'VarChar',
        'Continent'                 => 'VarChar',
        'Currency'                  => 'VarChar',
        'Active'                    => 'Boolean',
        'freeOfShippingCostsFrom'   => 'Money',
    );
    /**
     * Default values
     *
     * @var array
     */
    public static $defaults = array(
        'Active' => false,
    );
    /**
     * Has-many relationship.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartCountryLanguages' => 'SilvercartCountryLanguage'
    );
    /**
     * Many-many relationships.
     *
     * @var array
     */
    public static $many_many = array(
        'SilvercartPaymentMethods' => 'SilvercartPaymentMethod'
    );
    /**
     * Belongs-many-many relationships.
     *
     * @var array
     */
    public static $belongs_many_many = array(
        'SilvercartZones' => 'SilvercartZone'
    );
    /**
     * Virtual database columns.
     *
     * @var array
     */
    public static $casting = array(
        'AttributedZones'           => 'Varchar(255)',
        'AttributedPaymentMethods'  => 'Varchar(255)',
        'ActivityText'              => 'VarChar',
        'Title'                     => 'Text',
    );
    
     /**
     * Default sort order and direction
     *
     * @var string
     */
    public static $default_sort = "SilvercartCountry.Active DESC, SilvercartCountryLanguage.Title ASC";


        /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.06.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.06.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this);
    }

    /**
     * i18n for field labels
     *
     * @param bool $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.06.2012
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Title'                         => _t('SilvercartCountry.SINGULARNAME'),
                'ISO2'                          => _t('SilvercartCountry.ISO2', 'ISO Alpha2'),
                'ISO3'                          => _t('SilvercartCountry.ISO3', 'ISO Alpha3'),
                'ISON'                          => _t('SilvercartCountry.ISON', 'ISO numeric'),
                'FIPS'                          => _t('SilvercartCountry.FIPS', 'FIPS code'),
                'Continent'                     => _t('SilvercartCountry.CONTINENT', 'Continent'),
                'Currency'                      => _t('SilvercartCountry.CURRENCY', 'Currency'),
                'Active'                        => _t('SilvercartCountry.ACTIVE', 'Active'),
                'AttributedZones'               => _t('SilvercartCountry.ATTRIBUTED_ZONES', 'attributed zones'),
                'AttributedPaymentMethods'      => _t('SilvercartCountry.ATTRIBUTED_PAYMENTMETHOD', 'attributed payment method'),
                'ActivityText'                  => _t('SilvercartCountry.ACTIVE', 'Active'),
                'SilvercartCountryLanguages'    => _t('SilvercartCountryLanguage.PLURALNAME'),
                'SilvercartZones'               => _t('SilvercartZone.PLURALNAME'),
                'SilvercartPaymentMethods'      => _t('SilvercartPaymentMethod.PLURALNAME'),
                'SilvercartZones'               => _t('SilvercartZone.PLURALNAME'),
                'freeOfShippingCostsFrom'       => _t('SilvercartCountry.FREEOFSHIPPINGCOSTSFROM'),
            )
        );
    }

    /**
     * Searchable fields of SIlvercartCountry.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.06.2012
     */
    public function  searchableFields() {
        return array(
            'SilvercartCountryLanguages.Title' => array(
                'title'     => $this->singular_name(),
                'filter'    => 'PartialMatchFilter',
            ),
            'ISO2' => array(
                'title'     => $this->fieldLabel('ISO2'),
                'filter'    => 'PartialMatchFilter',
            ),
            'ISO3' => array(
                'title'     => $this->fieldLabel('ISO3'),
                'filter'    => 'PartialMatchFilter',
            ),
            'ISON' => array(
                'title'     => $this->fieldLabel('ISON'),
                'filter'    => 'PartialMatchFilter',
            ),
            'FIPS' => array(
                'title'     => $this->fieldLabel('FIPS'),
                'filter'    => 'PartialMatchFilter',
            ),
            'Continent' => array(
                'title'     => $this->fieldLabel('Continent'),
                'filter'    => 'PartialMatchFilter',
            ),
            'Currency' => array(
                'title'     => $this->fieldLabel('Currency'),
                'filter'    => 'PartialMatchFilter',
            ),
            'SilvercartZones.ID' => array(
                'title'     => $this->fieldLabel('SilvercartZones'),
                'filter'    => 'PartialMatchFilter',
            ),
            'SilvercartPaymentMethods.ID' => array(
                'title'     => $this->fieldLabel('SilvercartPaymentMethods'),
                'filter'    => 'PartialMatchFilter',
            ),
            'Active' => array(
                'title'     => $this->fieldLabel('Active'),
                'filter'    => 'ExactMatchFilter',
            )
        );
    }
    
    /**
     * Returns freeOfShippingCostsFrom in a nice format
     *
     * @return string
     */
    public function getFreeOfShippingCostsFromNice() {
        return $this->freeOfShippingCostsFrom->Nice();
    }

        /**
     * Summary fields
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012 
     */
    public function summaryFields() {
        $summaryFields = array_merge(
                parent::summaryFields(),
                array(
                    'Title'                             => $this->fieldLabel('Title'),
                    'ISO2'                              => $this->fieldLabel('ISO2'),
                    'ISO3'                              => $this->fieldLabel('ISO3'),
                    'Continent'                         => $this->fieldLabel('Continent'),
                    'Currency'                          => $this->fieldLabel('Currency'),
                    'AttributedZones'                   => $this->fieldLabel('AttributedZones'),
                    'AttributedPaymentMethods'          => $this->fieldLabel('AttributedPaymentMethods'),
                    'ActivityText'                      => $this->fieldLabel('ActivityText'),
                    'getFreeOfShippingCostsFromNice'    => $this->fieldLabel('freeOfShippingCostsFrom'),
                )
        );
        
        $this->extend('updateSummary', $summaryFields);
        return $summaryFields;
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields(
                array(
                    'fieldClasses' => array(
                        'freeOfShippingCostsFrom'   => 'SilvercartMoneyField',
                    ),
                )
        );
        $fields->removeByName('SilvercartPaymentMethods');
        $fields->removeByName('SilvercartZones');
        $fields->removeByName('Locale');//Field comes from Translatable

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
        
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguageClassName());
        foreach ($languageFields as $languageField) {
            $fields->insertBefore($languageField, 'ISO2');
        }

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
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function AttributedZones() {
        return SilvercartTools::AttributedDataObject($this->SilvercartZones());
    }

    /**
     * Returns the attributed payment methods as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function AttributedPaymentMethods() {
        return SilvercartTools::AttributedDataObject($this->SilvercartPaymentMethods());
    }
    
    /**
     * Returns the title
     *
     * @return string $title i18n title
     */
    public function getTitle() {
        return $this->getLanguageFieldValue('Title');
    }
}
