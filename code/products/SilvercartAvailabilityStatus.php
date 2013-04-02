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
 * @subpackage Products
 */

/**
 * abstract for an availibility status
 *
 * @package Silvercart
 * @subpackage Products
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 18.03.2011
 * @license see license file in modules root directory
 */
class SilvercartAvailabilityStatus extends DataObject {

    /**
     * attributes
     *
     * @var array
     */
    public static $db = array(
        'Code' => 'VarChar',
    );
    
    /**
     * field casting
     *
     * @var array
     */
    public static $casting = array(
        'Title'          => 'Text',
        'AdditionalText' => 'Text'
    );
    
    /**
     * 1:n relationships.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartAvailabilityStatusLanguages' => 'SilvercartAvailabilityStatusLanguage'
    );

    /**
     * Returns the translated singular name of the object.
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this);
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 10.03.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Code'                                  => _t('SilvercartOrderStatus.CODE'),
                'Title'                                 => _t('SilvercartAvailabilityStatus.SINGULARNAME'),
                'AdditionalText'                        => _t('SilvercartAvailabilityStatus.ADDITIONALTEXT'),
                'SilvercartAvailabilityStatusLanguages' => _t('SilvercartAvailabilityStatusLanguage.SINGULARNAME')
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
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
        $fields = SilvercartDataObject::getCMSFields($this, 'Code', false);
        return $fields;
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.03.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'Title' => $this->fieldLabel('Title'),
            'Code'  => $this->fieldLabel('Code'),
        );

        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Defines the form fields for the search in ModelAdmin
     * 
     * @return array seach fields definition
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012
     */
    public function searchableFields() {
        $searchableFields = array(
            'SilvercartAvailabilityStatusLanguages.Title' => array(
                'title' => $this->fieldLabel('Title'),
                'filter' => 'PartialMatchFilter'
            ),
            'Code' => array(
                'title' => $this->fieldLabel('Code'),
                'filter' => 'PartialMatchFilter'
            ),
        );
        $this->extend('updateSearchableFields', $searchableFields);
        return $searchableFields;
    }
    
    /**
     * getter for the pseudo attribute title
     *
     * @return string the title in the corresponding frontend language 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 11.01.2012
     */
    public function getTitle() {
        return $this->getLanguageFieldValue('Title');
    }

    /**
     * getter for the pseudo attribute AdditionalText
     *
     * @return string the title in the corresponding frontend language
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.12.2012
     */
    public function getAdditionalText() {
        return $this->getLanguageFieldValue('AdditionalText');
    }
}