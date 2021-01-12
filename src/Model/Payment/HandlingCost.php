<?php

namespace SilverCart\Model\Payment;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\FormFields\FieldGroup;
use SilverCart\Model\Order\ShoppingCart;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Product\Tax;
use SilverCart\Model\Shipment\Zone;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBMoney;

/**
 * Class for processing transaction costs etc.
 *
 * @package SilverCart
 * @subpackage Model_Payment
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property DBMoney $amount        Amount
 * @property string  $handlingcosts Handling costs string
 * 
 * @method Tax           Tax()           Returns the related Tax.
 * @method PaymentMethod PaymentMethod() Returns the related PaymentMethod.
 * @method Zone          Zone()          Returns the related Zone.
 */
class HandlingCost extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = [
        'amount' => \SilverCart\ORM\FieldType\DBMoney::class,
    ];
    /**
     * Casting.
     *
     * @var array
     */
    private static $casting = [
        'handlingcosts' => 'Text'
    ];
    /**
     * Has-one relationships.
     *
     * @var array
     */
    private static $has_one = [
        'Tax'           => Tax::class,
        'PaymentMethod' => PaymentMethod::class,
        'Zone'          => Zone::class,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartHandlingCost';

    /**
     * Sets the field labels.
     *
     * @param bool $includerelations set to true to include the DataObjects relations
     * 
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'amount' => _t(HandlingCost::class . '.AMOUNT', 'amount'),
            'Tax'    => Tax::singleton()->singular_name(),
            'Zone'   => Zone::singleton()->singular_name(),
        ]);
    }

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this); 
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     */
    public function summaryFields() : array
    {
        $summaryFields = [
            'handlingcosts' => $this->fieldLabel('amount'),
            'Tax.Rate'      => $this->fieldLabel('Tax'),
            'Zone.Title'    => $this->fieldLabel('Zone'),
        ];
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            Tax::presetDropdownWithDefault($fields->dataFieldByName('TaxID'), $this);
            $fieldGroup = FieldGroup::create('handlingCostGroup', '', $fields);
            $fieldGroup->push(        $fields->dataFieldByName('amount'));
            $fieldGroup->pushAndBreak($fields->dataFieldByName('TaxID'));
            $fields->addFieldToTab('Root.Main', $fieldGroup);
        });
        return parent::getCMSFields();
    }

    /**
     * Returns the prices amount
     *
     * @return float
     */
    public function getPriceAmount() : float
    {
        $price = (float) $this->amount->getAmount();
        if (Config::PriceType() === Config::PRICE_TYPE_NET) {
            $price = $price - $this->getTaxAmount();
        }
        return $price;
    }

    /**
     * Returns the tax rate for this fee.
     * 
     * @return float
     */
    public function getTaxRate() : float
    {
        $taxRate = ShoppingCart::get_most_valuable_tax_rate();
        if ($taxRate === false) {
            $taxRate = $this->Tax()->getTaxRate();
        }
        return (float) $taxRate;
    }

    /**
     * returns the tax amount included in $this
     *
     * @return float
     */
    public function getTaxAmount() : float
    {
        $taxRate = $this->amount->getAmount() - ($this->amount->getAmount() / (100 + $this->getTaxRate()) * 100);
        return (float) $taxRate;
    }

    /**
     * Returns the Price formatted by locale.
     *
     * @return string
     */
    public function PriceFormatted() : string
    {
        return DBMoney::create()
                ->setAmount($this->getPriceAmount())
                ->setCurrency($this->price->getCurrency())
                ->Nice();
    }

    /**
     * Returns the handling costs for display in tables.
     *
     * @return string|null
     */
    public function handlingcosts() : ?string
    {
        return $this->amount->Nice();
    }
}