<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * Definition for the condition of a product.
 *
 * @package Silvercart
 * @subpackage Products
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 09.08.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartProductCondition extends DataObject {
    
    /**
     * n:m relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.08.2011
     */
    public static $has_many = array(
        'SilvercartProducts'                  => 'SilvercartProduct',
        'SilvercartProductConditionLanguages' => 'SilvercartProductConditionLanguage'
    );
    
    public static $casting = array(
        'Title'             => 'VarChar(255)',
        'TableIndicator'    => 'Text'
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
     * retirieves title from related language class depending on the set locale
     *
     * @return string 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 12.01.2012
     */
    public function getTitle() {
        $title = '';
        if ($this->getLanguage()) {
            $title = $this->getLanguage()->Title;
        }
        return $title;
    }

    /**
     * define the CMS ields
     *
     * @param array $params Parameters for scaffolding
     *
     * @return FieldSet 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function getCMSFields($params = null) {
        $fields = parent::getCMSFields($params);
        //multilingual fields, in fact just the title
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguage(true));
        foreach ($languageFields as $languageField) {
            $fields->addFieldToTab('Root.Main', $languageField);
        }
        return $fields;
    }
    
    /**
     * Returns a string with HTML Code for a selector box that lets the user
     * choose a product condition.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.08.2011
     */
    public static function getDropdownFieldOptionSet() {
        $productConditionMap    = array();
        $productConditions      = DataObject::get(
            'SilvercartProductCondition'
        );
        
        if ($productConditions) {
            $productConditionMap = $productConditions->map('ID', 'Title');
        }
        
        return $productConditionMap;
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
     * @since 09.08.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Title'                                 => _t('SilvercartProductCondition.TITLE'),
                'SilvercartProducts'                    => _t('SilvercartProduct.PLURALNAME'),
                'SilvercartProductConditionLanguages'   => _t('SilvercartProductConditionLanguage.PLURALNAME')
            )
        );
        
        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
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
            'SilvercartProductConditionLanguages.Title' => array(
                'title' => $this->fieldLabel('Title'),
                'filter' => 'PartialMatchFilter'
            ),
        );
        $this->extend('updateSearchableFields', $searchableFields);
        return $searchableFields;
    }
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 10.03.2011
     */
    public function summaryFields() {
        $summaryFields = array(
            'TableIndicator'    => '',
            'Title'             => $this->fieldLabel('Title'),
        );
        
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
}