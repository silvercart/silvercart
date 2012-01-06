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
 * @package SilverCart
 * @subpackage translation
 */

/**
 * Translations for a product
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProductLanguage extends DataObject implements SilvercartLanguageInterface {
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2012
     */
    public function singular_name() {
        if (_t('SilvercartProductLanguage.SINGULARNAME')) {
            return _t('SilvercartProductLanguage.SINGULARNAME');
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
     * @since 04.01.2012
     */
    public function plural_name() {
        if (_t('SilvercartProductLanguage.PLURALNAME')) {
            return _t('SilvercartProductLanguage.PLURALNAME');
        } else {
            return parent::plural_name();
        }

    }
    
    public static $db = array(
        'Locale'            => 'DBLocale',
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
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 03.01.2012
     */
    public static $has_one = array(
        'SilvercartProduct' => 'SilvercartProduct'
    );
    
    public static $casting = array(
        'NativeNameForLocale' => 'Text'
    );
    
    /**
     * must return true
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2012
     */
    public function canTranslate() {
        return true;
    }

    /**
     * Converts Locale field to a dropdown and removes dropdown for product relation
     *
     * @return FieldSet 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2012
     */
    public function getCMSFields_forPopup() {
        return SilvercartLanguageHelper::prepareCMSFields_forPopup($this);
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
            'NativeNameForLocale' => _t('SilvercartConfig.TRANSLATION'),
            'Title'               => _t('SilvercartProduct.COLUMN_TITLE')
        );
        
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
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
                parent::fieldLabels($includerelations),             array(
            'Locale'           => _t('SilvercartProductLanguage.LOCALE'),
            'Title'            => _t('SilvercartProduct.TITLE'),
            'ShortDescription' => _t('SilvercartProduct.SHORTDESCRIPTION'),
            'LongDescription'  => _t('SilvercartProduct.DESCRIPTION'),
            'MetaDescription'  => _t('SilvercartProduct.METADESCRIPTION'),
            'MetaKeywords'     => _t('SilvercartProduct.METAKEYWORDS'),
            'MetaTitle'        => _t('SilvercartProduct.METATITLE')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * return the locale as native name
     *
     * @return string native name for the locale 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 06.01.2012
     */
    public function NativeNameForLocale() {
        $locale = new DBLocale('test');
        $locale->setValue($this->Locale);
        return $locale->getNativeName();
    }
}

