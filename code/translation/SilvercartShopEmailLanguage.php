<?php
/**
 * Copyright 2012 pixeltricks GmbH
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
 * @subpackage Translation
 */

/**
 * Translations for SilvercartShopEmail
 *
 * @package Silvercart
 * @subpackage Translation
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright pixeltricks GmbH
 * @since 27.04.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartShopEmailLanguage extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'Subject'       => 'Varchar(255)',
        'EmailText'     => 'Text',
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartShopEmail' => 'SilvercartShopEmail'
    );
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012
     */
    public function singular_name() {
        if (_t('SilvercartShopEmailLanguage.SINGULARNAME')) {
            return _t('SilvercartShopEmailLanguage.SINGULARNAME');
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012
     */
    public function plural_name() {
        if (_t('SilvercartShopEmailLanguage.PLURALNAME')) {
            return _t('SilvercartShopEmailLanguage.PLURALNAME');
        } else {
            return parent::plural_name();
        }

    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),             array(
                    'Subject'               => _t('SilvercartShopEmail.SUBJECT'),
                    'EmailText'             => _t('SilvercartShopEmail.EMAILTEXT'),
                    'SilvercartShopEmail'   => _t('SilvercartShopEmail.SINGULARNAME'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Summary fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.04.2012
     */
    public function summaryFields() {
        $summaryFields = array_merge(
                parent::summaryFields(),
                array(
                    'Subject'   => $this->fieldLabel('Subject'),
                )
        );

        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
}

