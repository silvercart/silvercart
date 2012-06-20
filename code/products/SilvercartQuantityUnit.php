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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        //multilingual fields, in fact just the title
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguage(true));
        foreach ($languageFields as $languageField) {
            $fields->addFieldToTab('Root.Main', $languageField);
        }
        return $fields;
    }

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
                    'TableIndicator'    => '',
                    'Title'             => $this->fieldLabel('Title'),
                    'Abbreviation'      => $this->fieldLabel('Abbreviation'),
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
                    'Title'                             => _t('SilvercartQuantityUnit.NAME'),
                    'Abbreviation'                      => _t('SilvercartQuantityUnit.ABBREVIATION'),
                    'SilvercartQuantityUnitLanguages'   => _t('SilvercartQuantityUnitLanguage.PLURALNAME'),
                )
        );
    }
}
