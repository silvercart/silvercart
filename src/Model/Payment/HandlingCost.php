<?php

namespace SilverCart\Model\Payment;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\FormFields\FieldGroup;
use SilverCart\Forms\FormFields\MoneyField;
use SilverCart\Model\Order\ShoppingCart;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Product\Tax;
use SilverCart\Model\Shipment\Zone;
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
 */
class HandlingCost extends DataObject {

    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = array(
        'amount' => \SilverCart\ORM\FieldType\DBMoney::class,
    );

    /**
     * Casting.
     *
     * @var array
     */
    private static $casting = array(
        'handlingcosts' => 'Text'
    );

    /**
     * Has-one relationships.
     *
     * @var array
     */
    private static $has_one = array(
        'Tax'           => Tax::class,
        'PaymentMethod' => PaymentMethod::class,
        'Zone'          => Zone::class,
    );

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
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.10.2012
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'amount' => _t(HandlingCost::class . '.AMOUNT', 'amount'),
                    'Tax'    => Tax::singleton()->singular_name(),
                    'Zone'   => Zone::singleton()->singular_name(),
                )
        );
    }

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
        return Tools::singular_name_for($this);
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
        return Tools::plural_name_for($this); 
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.10.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'handlingcosts' => $this->fieldLabel('amount'),
            'Tax.Rate'      => $this->fieldLabel('Tax'),
            'Zone.Title'    => $this->fieldLabel('Zone'),
        );
        $this->extend('updateSummaryFields', $summaryFields);

        return $summaryFields;
    }
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     * 
     * @param array $params configuration parameters
     *
     * @return FieldList
     */
    public function getCMSFields($params = null) {
        $fields = parent::getCMSFields(
                array_merge(
                        array(
                            'fieldClasses' => array(
                                'amount' => MoneyField::class,
                            ),
                        ),
                        (array)$params
                )
        );
        Tax::presetDropdownWithDefault($fields->dataFieldByName('TaxID'), $this);
        
        $fieldGroup = new FieldGroup('handlingCostGroup', '', $fields);
        $fieldGroup->push(          $fields->dataFieldByName('amount'));
        $fieldGroup->pushAndBreak(  $fields->dataFieldByName('TaxID'));
        $fields->addFieldToTab('Root.Main', $fieldGroup);
        
        return $fields;
    }

    /**
     * Returns the prices amount
     *
     * @return float
     */
    public function getPriceAmount() {
        $price = (float) $this->amount->getAmount();

        if (Config::PriceType() == 'net') {
            $price = $price - $this->getTaxAmount();
        }

        return $price;
    }

    /**
     * Returns the tax rate for this fee.
     * 
     * @return int
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2013
     */
    public function getTaxRate() {
        $taxRate = ShoppingCart::get_most_valuable_tax_rate();
        if ($taxRate === false) {
            $taxRate = $this->Tax()->getTaxRate();
        }
        return $taxRate;
    }

    /**
     * returns the tax amount included in $this
     *
     * @return float
     */
    public function getTaxAmount() {
        $taxRate = $this->amount->getAmount() - ($this->amount->getAmount() / (100 + $this->getTaxRate()) * 100);

        return $taxRate;
    }

    /**
     * Returns the Price formatted by locale.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 31.01.2011
     */
    public function PriceFormatted() {
        $priceObj = new DBMoney();
        $priceObj->setAmount($this->getPriceAmount());
        $priceObj->setCurrency($this->price->getCurrency());

        return $priceObj->Nice();
    }

    /**
     * Returns the handling costs for display in tables.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.03.2012
     */
    public function handlingcosts() {
        return $this->amount->Nice();
    }
}
