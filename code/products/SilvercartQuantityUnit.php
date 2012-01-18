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
 * Abstract for SilvercartQuantityUnit
 *
 * @package Silvercart
 * @subpackage Products
 * @author Ramon Kupper <rkupper@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 25.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartQuantityUnit extends DataObject {
    
    public static $casting = array(
        'Title'          => 'Text',
        'Abbreviation'   => 'Text',
        'TableIndicator' => 'Text'
    );
    
    public static $has_many = array(
        'SilvercartQuantityUnitLanguages' => 'SilvercartQuantityUnitLanguage'
    );
    
    /**
     * getter for the quantity units title
     *
     * @return string the title in the corresponding front end language 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 11.01.2012
     */
    public function getTitle() {
        $title = '';
        if ($this->getLanguage()) {
            $title = $this->getLanguage()->Title;
        }
        return $title;
    }
    
    /**
     * getter for the quantity units title
     *
     * @return string the title in the corresponding front end language 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 11.01.2012
     */
    public function getAbbreviation() {
        $title = '';
        if ($this->getLanguage()) {
            $title = $this->getLanguage()->Abbreviation;
        }
        return $title;
    }
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldSet the fields for the backend
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 17.01.2012
 */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        //multilingual fields, in fact just the title
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguage());
        foreach ($languageFields as $languageField) {
            $fields->addFieldToTab('Root.Main', $languageField);
        }
        return $fields;
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
        if (_t('SilvercartQuantityUnit.SINGULARNAME')) {
            return _t('SilvercartQuantityUnit.SINGULARNAME');
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
        if (_t('SilvercartQuantityUnit.PLURALNAME')) {
            return _t('SilvercartQuantityUnit.PLURALNAME');
        } else {
            return parent::plural_name();
        }   
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 29.03.2011
     */
    public function summaryFields() {
        return array_merge(
                parent::summaryFields(),
                array(
                    'TableIndicator' => '',
                    'Title' => _t('SilvercartQuantityUnit.NAME'),
                    'Abbreviation' => _t('SilvercartQuantityUnit.ABBREVIATION')
                )
        );
    }

    /**
     * Field labels for display in tables.
     *
     * @return array
     *
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 29.03.2011
     */
    public function fieldLabels() {
        return array_merge(
                parent::fieldLabels(),
                array(
                    'Title' => _t('SilvercartQuantityUnit.NAME'),
                    'Abbreviation' => _t('SilvercartQuantityUnit.ABBREVIATION'),
                    'SilvercartQuantityUnitLanguages' => _t('SilvercartQuantityUnitLanguage.PLURALNAME')
                )
        );
    }
}
