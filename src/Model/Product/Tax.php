<?php

namespace SilverCart\Model\Product;

use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\NumberRange;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\TaxTranslation;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;

/**
 * Defines Taxrates.
 *
 * @package SilverCart
 * @subpackage Model_Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Tax extends DataObject {

    /**
     * attributes
     *
     * @var array
     */
    private static $db = array(
        'Rate'          => 'Float',
        'Identifier'    => 'Varchar(30)',
        'IsDefault'     => 'Boolean',
    );

    /**
     * n:m relations
     *
     * @var array
     */
    private static $has_many = array(
        'Products'        => Product::class,
        'TaxTranslations' => TaxTranslation::class,
    );

    /**
     * List of searchable fields for the model admin
     *
     * @var array
     */
    private static $searchable_fields = array(
        'Rate'
    );
    
    /**
     * cast fields to other SS data types
     *
     * @var array
     */
    private static $casting = array(
        'Title'             => 'Text',
        'IsDefaultString'   => 'Text',
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartTax';
    
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
                        'Title'           => _t(Tax::class . '.LABEL', 'label'),
                        'Rate'            => _t(Tax::class . '.RATE_IN_PERCENT', 'Rate in %'),
                        'Products'        => _t(Product::class . '.PLURALNAME', 'Products'),
                        'Identifier'      => _t(NumberRange::class . '.IDENTIFIER', 'Identifier'),
                        'IsDefault'       => _t(Tax::class . '.ISDEFAULT', 'Is default'),
                        'TaxTranslations' => TaxTranslation::singleton()->plural_name(),
                        'Yes'             => Tools::field_label('Yes'),
                        'No'              => Tools::field_label('No'),
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
        return Tools::singular_name_for($this);
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
        return Tools::plural_name_for($this);
    }
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = DataObjectExtension::getCMSFields($this);
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
            $this->i18nTitle = $this->getTranslationFieldValue('Title');
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
        parent::setField('Title', $title);
    }
    
    /**
     * Sets the i18n title only. Won't be saved.
     * 
     * @param string $title Title to set
     * 
     * @return void
     */
    public function setI18nTitle($title) {
        $this->i18nTitle = $title;
    }

    /**
     * Casting to get the IsDefault state as a readable string
     *
     * @return string
     */
    public function getIsDefaultString() {
        $IsDefaultString = $this->fieldLabel('No');
        if ($this->IsDefault) {
            $IsDefaultString = $this->fieldLabel('Yes');
        }
        return $IsDefaultString;
    }
    
    /**
     * determine the tax rate. This method can be extended via DataExtension
     * to implement project specific behavior.
     * 
     * @param bool $ignoreTaxExemption Determines whether to ignore tax exemption or not.
     *
     * @return float
     */
    public function getTaxRate($ignoreTaxExemption = false) {
        $overwritten = $this->extend('getTaxRate');
        if (empty ($overwritten)) {
            
            $member = Customer::currentUser();
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
        if (is_null($rate)) {
            $rate = 0;
        }
        return $rate;
    }
    
    /**
     * Returns the default tax rate
     * 
     * @return Tax
     */
    public static function getDefault() {
        $defaultTaxRate = Tax::get()->filter('IsDefault', '1')->first();
        return $defaultTaxRate;
    }
    
    /**
     * Presets the given dropdown field with the default tax rate
     * 
     * @param DropdownField $dropdownField Dropdown field to manipulate
     * @param DataObject    $object        Tax rate related object
     * @param string        $relationName  Tax rate relation name
     * 
     * @return Tax
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.10.2012
     */
    public static function presetDropdownWithDefault($dropdownField, $object, $relationName = 'Tax') {
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