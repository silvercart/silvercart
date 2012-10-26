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
 * Defines Taxrates.
 *
 * @package Silvercart
 * @subpackage Products
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 24.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartTax extends DataObject {

    /**
     * attributes
     *
     * @var array
     */
    public static $db = array(
        'Rate'          => 'Float',
        'Identifier'    => 'VarChar(30)',
        'IsDefault'     => 'Boolean',
    );

    /**
     * n:m relations
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartProducts'     => 'SilvercartProduct',
        'SilvercartTaxLanguages' => 'SilvercartTaxLanguage'
    );

    /**
     * List of searchable fields for the model admin
     *
     * @var array
     */
    public static $searchable_fields = array(
        'Rate'
    );
    
    /**
     * cast fields to other SS data types
     *
     * @var array
     */
    public static $casting = array(
        'Title'             => 'Text',
        'IsDefaultString'   => 'Text',
    );
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.10.2012
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                array(
                        'Title'                     => _t('SilvercartTax.LABEL'),
                        'Rate'                      => _t('SilvercartTax.RATE_IN_PERCENT'),
                        'SilvercartProducts'        => _t('SilvercartProduct.PLURALNAME'),
                        'Identifier'                => _t('SilvercartNumberRange.IDENTIFIER'),
                        'IsDefault'                 => _t('SilvercartTax.ISDEFAULT'),
                        'SilvercartTaxLanguages'    => _t('SilvercartTaxLanguage.PLURALNAME'),
                    )
                );
    }
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.10.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'Title'             => $this->fieldLabel('Title'),
            'Rate'              => $this->fieldLabel('Rate'),
            'IsDefaultString'   => $this->fieldLabel('IsDefault'),
        );
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
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
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldSet the fields for the backend
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguage(true));
        foreach ($languageFields as $languageField) {
            $fields->addFieldToTab('Root.Main', $languageField);
        }
        return $fields;
    }
    
    /**
     * Handles the default tax rate before writing
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.10.2012
     */
    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        $defaultTaxRate = self::getDefault();
        if (!$defaultTaxRate) {
            $defaultTaxRate = $this;
            $this->IsDefault = true;
        } elseif ($this->IsDefault &&
                  $defaultTaxRate->ID != $this->ID) {
            $defaultTaxRate->IsDefault = false;
            $defaultTaxRate->write();
        }
    }

    /**
     * retirieves title from related language class depending on the set locale
     *
     * @return string 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.01.2012
     */
    public function getTitle() {
        return $this->getLanguageFieldValue('Title');
    }
    
    /**
     * Casting to get the IsDefault state as a readable string
     *
     * @return string
     */
    public function getIsDefaultString() {
        $IsDefaultString = _t('Boolean.NO');
        if ($this->IsDefault) {
            $IsDefaultString = _t('Boolean.YES');
        }
        return $IsDefaultString;
    }
    
    /**
     * determine the tax rate. This method can be extended via DataObjectDecorator to implement own behaviour.
     *
     * @return float the tax rate in percent
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 17.3.2011
     */
    public function getTaxRate() {
        $overwritten = $this->extend('getTaxRate');
        if (empty ($overwritten)) {
            return $this->Rate;
        }
        return $overwritten[0];
    }
    
    /**
     * Returns the default tax rate
     * 
     * @return SilvercartTax
     */
    public static function getDefault() {
        $defaultTaxRate = DataObject::get_one('SilvercartTax', '"IsDefault" = 1');
        return $defaultTaxRate;
    }
    
    /**
     * Presets the given dropdown field with the default tax rate
     * 
     * @param DropdownField $dropdownField Dropdown field to manipulate
     * @param DataObject    $object        Tax rate related object
     * @param string        $relationName  Tax rate relation name
     * 
     * @return SilvercartTax
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.10.2012
     */
    public static function presetDropdownWithDefault($dropdownField, $object, $relationName = 'SilvercartTax') {
        $relationIDName = $relationName . 'ID';
        $dropdownField->setEmptyString('');
        $dropdownField->setHasEmptyDefault(false);
        if ($object->{$relationIDName} == 0) {
            $defaultTaxRate = self::getDefault();
            if ($defaultTaxRate) {
                $dropdownField->setValue($defaultTaxRate->ID);
            }
        }
    }

}
