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
     * Has-many relationship.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartShippingMethods'   => 'SilvercartShippingMethod',
        'SilvercartCarrierLanguages'  => 'SilvercartCarrierLanguage',
    );
    /**
     * Many to many relations
     * 
     * @var array
     */
    public static $belongs_many_many = array(
        'SilvercartZones'   => 'SilvercartZone',
    );
    /**
     * Virtual database fields.
     *
     * @var array
     */
    public static $casting = array(
        'AttributedZones'           => 'Varchar(255)',
        'AttributedShippingMethods' => 'Varchar(255)',
        'Title'                     => 'VarChar(25)',
        'FullTitle'                 => 'VarChar(60)',
    );
    
    /**
     * retirieves title from related language class depending on the set locale
     *
     * @return string 
     */
    public function getTitle() {
        return $this->getLanguageFieldValue('Title');
    }
    
    /**
     * retirieves title from related language class depending on the set locale
     *
     * @return string 
     */
    public function getFullTitle() {
        return $this->getLanguageFieldValue('FullTitle');
    }
    
    /**
     * Defines the form fields for the search in ModelAdmin
     * 
     * @return array seach fields definition
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2012
     */
    public function searchableFields() {
        $searchableFields = array(
            'SilvercartCarrierLanguages.Title' => array(
                'title' => $this->fieldLabel('Title'),
                'filter' => 'PartialMatchFilter'
            ),
            'SilvercartZones.ID' => array(
                'title' => $this->fieldLabel('SilvercartZones'),
                'filter' => 'ExactMatchFilter'
            ),
            'SilvercartShippingMethods.ID' => array(
                'title' => $this->fieldLabel('SilvercartShippingMethods'),
                'filter' => 'ExactMatchFilter'
            )
        );
        $this->extend('updateSearchableFields', $searchableFields);
        return $searchableFields;
    }

    /**
     * CMS fields
     *
     * @param array $params Params to use
     *
     * @return FieldList
     */
    public function getCMSFields($params = null) {
        $fields = parent::getCMSFields($params);
//        if ($this->ID) {
//            $zonesTable = new GridField("carrierszones", _t("SilvercartZone.PLURALNAME"), $this->SilvercartZones());
//            $fields->findOrMakeTab('Root.SilvercartZones', $this->fieldLabel('SilvercartZones'));
//            $fields->addFieldToTab("Root.SilvercartZones", $zonesTable);
//        }
        
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguageClassName());
        foreach ($languageFields as $languageField) {
            $fields->addFieldToTab('Root.Main', $languageField);
        }
        return $fields;
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
                    'AttributedZones'            => _t('SilvercartCountry.ATTRIBUTED_ZONES'),
                    'AttributedShippingMethods'  => _t('SilvercartCarrier.ATTRIBUTED_SHIPPINGMETHODS'),
                    'SilvercartShippingMethods'  => _t('SilvercartShippingMethod.PLURALNAME', 'zones'),
                    'SilvercartZones'            => _t('SilvercartZone.PLURALNAME', 'zones'),
                    'SilvercartCarrierLanguages' => _t('SilvercartCarrierLanguage.PLURALNAME')
                )
        );
    }
    
    /**
     * Sets the summary fields.
     *
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2012
     */
    public function summaryFields() {
        return array_merge(
            parent::summaryFields(),
            array(
                'Title'                     => $this->fieldLabel('Title'),
                'AttributedZones'           => $this->fieldLabel('AttributedZones'),
                'AttributedShippingMethods' => $this->fieldLabel('AttributedShippingMethods'),
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
     * @since 13.07.2012
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
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
     * Returns the attributed shipping methods as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function AttributedShippingMethods() {
        return SilvercartTools::AttributedDataObject($this->SilvercartShippingMethods());
    }
    
    /**
     * Returns all allowed shipping methods for the this carrier
     *
     * @return SilvercartShippingMethod 
     */
    public function getAllowedSilvercartShippingMethods() {
        return SilvercartShippingMethod::getAllowedShippingMethodsForOverview($this);
    }
    
}
