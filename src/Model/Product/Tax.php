<?php

namespace SilverCart\Model\Product;

use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\NumberRange;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\TaxTranslation;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\HasManyList;
use SilverStripe\Security\Member;

/**
 * Defines Taxrates.
 *
 * @package SilverCart
 * @subpackage Model\Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property float  $Rate       Rate
 * @property string $Identifier Identifier
 * @property bool   $IsDefault  Is Default?
 * 
 * @property string $Title Title (current locale context)
 * 
 * @method HasManyList Products()        Returns a list of related Products.
 * @method HasManyList TaxTranslations() Returns a list of translations for this Tax.
 */
class Tax extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * attributes
     *
     * @var array
     */
    private static $db = [
        'Rate'       => 'Float',
        'Identifier' => 'Varchar(30)',
        'IsDefault'  => 'Boolean',
    ];
    /**
     * n:m relations
     *
     * @var array
     */
    private static $has_many = [
        'Products'        => Product::class,
        'TaxTranslations' => TaxTranslation::class,
    ];
    /**
     * List of searchable fields for the model admin
     *
     * @var array
     */
    private static $searchable_fields = [
        'Rate'
    ];
    /**
     * cast fields to other SS data types
     *
     * @var array
     */
    private static $casting = [
        'Title'             => 'Text',
        'IsDefaultString'   => 'Text',
    ];
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
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'Title'           => _t(Tax::class . '.LABEL', 'label'),
            'Rate'            => _t(Tax::class . '.RATE_IN_PERCENT', 'Rate in %'),
            'Products'        => _t(Product::class . '.PLURALNAME', 'Products'),
            'Identifier'      => _t(NumberRange::class . '.IDENTIFIER', 'Identifier'),
            'IsDefault'       => _t(Tax::class . '.ISDEFAULT', 'Is default'),
            'TaxTranslations' => TaxTranslation::singleton()->plural_name(),
            'Yes'             => Tools::field_label('Yes'),
            'No'              => Tools::field_label('No'),
        ]);
    }
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     */
    public function summaryFields() : array
    {
        $summaryFields = [
            'Title'             => $this->fieldLabel('Title'),
            'Rate'              => $this->fieldLabel('Rate'),
            'IsDefaultString'   => $this->fieldLabel('IsDefault'),
        ];
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }

    /**
     * Returns the translated singular name of the object.
     *
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object.
     *
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this);
    }
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        return DataObjectExtension::getCMSFields($this);
    }
    
    /**
     * Handles the default tax rate before writing
     * 
     * @return void
     */
    protected function onBeforeWrite() : void
    {
        parent::onBeforeWrite();
        $defaultTaxRate = self::getDefault();
        if (!$defaultTaxRate) {
            $defaultTaxRate = $this;
            $this->IsDefault = true;
        } elseif ($this->IsDefault
               && $defaultTaxRate->ID != $this->ID
        ) {
            $defaultTaxRate->IsDefault = false;
            $defaultTaxRate->write();
        }
    }

    /**
     * retirieves title from related language class depending on the set locale
     *
     * @return string 
     */
    public function getTitle() : string
    {
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
    public function setTitle(string $title) : Tax
    {
        $this->i18nTitle = $title;
        parent::setField('Title', $title);
        return $this;
    }
    
    /**
     * Sets the i18n title only. Won't be saved.
     * 
     * @param string $title Title to set
     * 
     * @return void
     */
    public function setI18nTitle(string $title) : Tax
    {
        $this->i18nTitle = $title;
        return $this;
    }

    /**
     * Casting to get the IsDefault state as a readable string
     *
     * @return string
     */
    public function getIsDefaultString() : string
    {
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
    public function getTaxRate($ignoreTaxExemption = false) : float
    {
        $overwritten = $this->extend('getTaxRate');
        if (empty ($overwritten)) {
            $member = Customer::currentUser();
            if (!$ignoreTaxExemption
             && $member instanceof Member
             && $member->doesNotHaveToPayTaxes()
            ) {
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
     * @return Tax|null
     */
    public static function getDefault() : ?Tax
    {
        return Tax::get()->filter('IsDefault', '1')->first();
    }
    
    /**
     * Presets the given dropdown field with the default tax rate
     * 
     * @param DropdownField $dropdownField Dropdown field to manipulate
     * @param DataObject    $object        Tax rate related object
     * @param string        $relationName  Tax rate relation name
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.10.2012
     */
    public static function presetDropdownWithDefault($dropdownField, $object, $relationName = 'Tax') : void
    {
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