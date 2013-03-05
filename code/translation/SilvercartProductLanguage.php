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
 * Translations for a product
 *
 * @package Silvercart
 * @subpackage Translation
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProductLanguage extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'Title'             => 'VarChar(255)',
        'ShortDescription'  => 'Text',
        'LongDescription'   => 'HTMLText',
        'MetaDescription'   => 'VarChar(255)',
        'MetaTitle'         => 'VarChar(64)', //search engines use only 64 chars
        'MetaKeywords'      => 'VarChar'
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartProduct' => 'SilvercartProduct'
    );
    
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
     * Returns an array of field/relation names (db, has_one, has_many, 
     * many_many, belongs_many_many) to exclude from form scaffolding in
     * backend.
     * This is a performance friendly way to exclude fields.
     * 
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 27.02.2013
     */
    public function excludeFromScaffolding() {
        $excludeFromScaffolding = array(
            'SilvercartProduct'
        );
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldSet the fields for the backend
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 27.02.2013
 */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this);
        
        return $fields;
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2012 pixeltricks GmbH
     * @since 05.01.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'Title'             => _t('SilvercartProduct.COLUMN_TITLE'),
                    'ShortDescription'  => _t('SilvercartProduct.SHORTDESCRIPTION'),
                    'LongDescription'   => _t('SilvercartProduct.DESCRIPTION'),
                    'MetaDescription'   => _t('SilvercartProduct.METADESCRIPTION'),
                    'MetaKeywords'      => _t('SilvercartProduct.METAKEYWORDS'),
                    'MetaTitle'         => _t('SilvercartProduct.METATITLE')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * columns for table overview
     *
     * @return array $summaryFields 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'Title'               => $this->fieldLabel('Title'),
        );
        
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Sets the cache relevant fields.
     * 
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.02.2013
     */
    public function getCacheRelevantFields() {
        $cacheRelevantFields = array(
            'Title',
            'ShortDescription',
            'LongDescription',
            'MetaDescription',
            'MetaTitle',
            'MetaKeywords',
        );
        $this->extend('updateCacheRelevantFields', $cacheRelevantFields);
        return $cacheRelevantFields;
    }
    
}

