<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @copyright 2013 pixeltricks GmbH
 * @since 24.11.2010
 * @license see license file in modules root directory
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
     * The i18n dependent title
     *
     * @var string
     */
    protected $i18nTitle = null;
    
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
     * @return FieldList the fields for the backend
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.02.2012
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this);
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
     */
    public function getTitle() {
        if (is_null($this->i18nTitle)) {
            $this->i18nTitle = $this->getLanguageFieldValue('Title');
        }
        return $this->i18nTitle;
    }
    
    /**
     * Sets the title
     * 
     * @param string $title Title to set
     * 
     * @return void
     */
    public function setTitle($title) {
        $this->i18nTitle = $title;
    }

    /**
     * Casting to get the IsDefault state as a readable string
     *
     * @return string
     */
    public function getIsDefaultString() {
        $IsDefaultString = _t('Silvercart.NO');
        if ($this->IsDefault) {
            $IsDefaultString = _t('Silvercart.YES');
        }
        return $IsDefaultString;
    }
    
    /**
     * determine the tax rate. This method can be extended via DataExtension
     * to implement project specific behavior.
     * 
     * @param bool $ignoreTaxExemption Determines whether to ignore tax exemption or not.
     *
     * @return float the tax rate in percent
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function getTaxRate($ignoreTaxExemption = false) {
        $overwritten = $this->extend('getTaxRate');
        if (empty ($overwritten)) {
            
            $member = SilvercartCustomer::currentUser();
            if (!$ignoreTaxExemption &&
                $member instanceof Member &&
                $member->doesNotHaveToPayTaxes()) {
                $rate = 0;
            } else {
                $rate = $this->Rate;
            }
        } else {
            $rate = $overwritten[0];
        }
        return $rate;
    }
    
    /**
     * Returns the default tax rate
     * 
     * @return SilvercartTax
     */
    public static function getDefault() {
        $defaultTaxRate = SilvercartTax::get()->filter('IsDefault', '1')->first();
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

/**
 * Translations for SilvercartTax
 *
 * @package Silvercart
 * @subpackage Translation
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 21.01.2012
 * @license see license file in modules root directory
 */
class SilvercartTaxLanguage extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'Title' => 'VarChar'
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartTax' => 'SilvercartTax'
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
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.01.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'SilvercartTax' => _t('SilvercartTax.SINGULARNAME'),
                    'Title'         => _t('SilvercartProduct.COLUMN_TITLE')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
}