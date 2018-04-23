<?php

namespace SilverCart\Model\Order;

use SilverCart\Dev\Tools;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\QuantityUnit;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\ORM\DataObject;

/**
 * The OrderPosition object.
 *
 * @package SilverCart
 * @subpackage Model_Order
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class OrderPosition extends DataObject {

    /**
     * Indicates whether changes and creations of order positions should
     * be logged or not.
     *
     * @var boolean
     */
    public $log = true;

    /**
     * Indicates whether the order should be recalculated in method
     * "onAfterWrite".
     *
     * @var boolean
     */
    protected $doRecalculate = false;

    /**
     * Indicates whether the position has been created. Used in onBeforeWrite.
     *
     * @var boolean
     */
    public $objectCreated = false;

    /**
     * Indicates whether the position has been deleted. Used in onBeforeDelete.
     *
     * @var boolean
     */
    public $objectDeleted = false;

    /**
     * attributes
     *
     * @var array
     */
    private static $db = array(
        'Price'                              => \SilverCart\ORM\FieldType\DBMoney::class,
        'PriceTotal'                         => \SilverCart\ORM\FieldType\DBMoney::class,
        'isChargeOrDiscount'                 => 'Boolean(0)',
        'isIncludedInTotal'                  => 'Boolean(0)',
        'chargeOrDiscountModificationImpact' => "Enum('none,productValue,totalValue','none')",
        'Tax'                                => 'Float',
        'TaxTotal'                           => 'Float',
        'TaxRate'                            => 'Float',
        'ProductDescription'                 => 'Text',
        'Quantity'                           => 'Decimal',
        'Title'                              => 'Varchar(255)',
        'ProductNumber'                      => 'Varchar',
        'numberOfDecimalPlaces'              => 'Int',
        'IsNonTaxable'                       => 'Boolean(0)',
    );

    /**
     * 1:n relations
     *
     * @var array
     */
    private static $has_one = array(
        'Order'   => Order::class,
        'Product' => Product::class,
    );

    /**
     * casted attributes
     *
     * @var array
     */
    private static $casting = array(
        'PriceNice'         => 'Varchar(255)',
        'PriceTotalNice'    => 'Varchar(255)',
        'FullTitle'         => 'HtmlText',
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartOrderPosition';

    /**
     * Grant API access on this item.
     *
     * @var bool
     *
     * @since 2013-03-14
     */
    private static $api_access = true;

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     * 
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.10.2017
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Product'                               => Product::singleton()->singular_name(),
                'Price'                                 => _t(OrderPosition::class . '.PRICE', 'Price'),
                'PriceTotal'                            => _t(OrderPosition::class . '.PRICETOTAL', 'Price total'),
                'isChargeOrDiscount'                    => _t(OrderPosition::class . '.ISCHARGEORDISCOUNT', 'Is charge or discount'),
                'Tax'                                   => _t(OrderPosition::class . '.TAX', 'Vat'),
                'TaxTotal'                              => _t(OrderPosition::class . '.TAXTOTAL', 'Vat total'),
                'TaxRate'                               => _t(OrderPosition::class . '.TAXRATE', 'Vat rate'),
                'ProductDescription'                    => _t(OrderPosition::class . '.PRODUCTDESCRIPTION', 'Description'),
                'Quantity'                              => _t(OrderPosition::class . '.QUANTITY', 'Quantity'),
                'Title'                                 => _t(OrderPosition::class . '.TITLE', 'Title'),
                'ProductNumber'                         => _t(OrderPosition::class . '.PRODUCTNUMBER', 'Product no.'),
                'chargeOrDiscountModificationImpact'    => _t(OrderPosition::class . '.CHARGEORDISCOUNTMODIFICATIONIMPACT', 'Charge/Discount Type'),
                'numberOfDecimalPlaces'                 => QuantityUnit::singleton()->fieldLabel('numberOfDecimalPlaces'),
            )
        );
        $this->extend('updateFieldLabels', $fieldLabels);

        return $fieldLabels;
    }

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.06.2012
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.06.2012
     */
    public function plural_name() {
        return Tools::plural_name_for($this);  
    }

    /**
     * Indicates wether the current user can view this object.
     * 
     * @param Member $member current member
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function canView($member = null) {
        return $this->Order()->canView($member);
    }

    /**
     * Indicates wether the current user can edit this object.
     * 
     * @param Member $member current member
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function canEdit($member = null) {
        return $this->Order()->canEdit($member);
    }

    /**
     * Indicates wether the current user can delete this object.
     * 
     * @param Member $member current member
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function canDelete($member = null) {
        return $this->Order()->canDelete($member);
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.06.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'ProductNumber'         => $this->fieldLabel('ProductNumber'),
            'FullTitle'             => $this->fieldLabel('Title'),
            'PriceNice'             => $this->fieldLabel('Price'),
            'TaxRate'               => $this->fieldLabel('TaxRate'),
            'Quantity'              => $this->fieldLabel('Quantity'),
            'PriceTotalNice'        => $this->fieldLabel('PriceTotal'),
        );
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }

    /**
     * returns the orders total amount as string incl. currency.
     *
     * @return string
     */
    public function getPriceNice() {
        return str_replace('.', ',', number_format($this->PriceAmount, 2)) . ' ' . $this->PriceCurrency;
    }
    
    /**
     * returns the orders total amount as string incl. currency.
     *
     * @return string
     */
    public function getPriceTotalNice() {
        return str_replace('.', ',', number_format($this->PriceTotalAmount, 2)) . ' ' . $this->PriceTotalCurrency;
    }

    /**
     * Returns the quantity according to the Product quantity type
     * setting.
     *
     * @return mixed
     */
    public function getTypeSafeQuantity() {
        $quantity = $this->Quantity;

        if ($this->numberOfDecimalPlaces == 0) {
            $quantity = (int) $quantity;
        }

        return $quantity;
    }

    /**
     * returns the order positions Title with extensions
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getFullTitle() {
        $fullTitle = $this->Title . '<br/>' . $this->addToTitle();
        return Tools::string2html($fullTitle);
    }

    /**
     * Returns true if this position has a quantity of more than 1.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.04.2011
     */
    public function MoreThanOneProduct() {
        $moreThanOneProduct = false;

        if ($this->Quantity > 1) {
            $moreThanOneProduct = true;
        }

        return $moreThanOneProduct;
    }

    /**
     * Customize scaffolding fields for the backend
     *
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = DataObjectExtension::getCMSFields($this);
        if ($this->exists()) {
            $fields->makeFieldReadonly('Price');
            $fields->makeFieldReadonly('PriceTotal');
            $fields->makeFieldReadonly('Tax');
            $fields->makeFieldReadonly('TaxTotal');
            $fields->makeFieldReadonly('TaxRate');
            $fields->makeFieldReadonly('Quantity');
            $fields->makeFieldReadonly('ProductDescription');
            $fields->makeFieldReadonly('Title');
            $fields->makeFieldReadonly('ProductNumber');
            $fields->makeFieldReadonly('isChargeOrDiscount');
            $fields->makeFieldReadonly('chargeOrDiscountModificationImpact');
            $fields->makeFieldReadonly('OrderID');
            $fields->makeFieldReadonly('ProductID');
            $fields->removeByName('isIncludedInTotal');
            $fields->removeByName('numberOfDecimalPlaces');
            $fields->removeByName('IsNonTaxable');
        }
        return $fields;
    }

    /**
     * If the attributed product gets changed we adjust all order position
     * fields accordingly.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.09.2017
     */
    public function onBeforeWrite() {
        $changedFields = $this->getChangedFields();

        if (!$this->objectCreated &&
             array_key_exists('OrderID', $changedFields)) {
            $this->saveNew($changedFields);
            $this->objectCreated = true;
        } else if (!$this->objectCreated) {
            $this->saveChanges($changedFields);
        }

        $this->extend('updateOnBeforeWrite', $changedFields, $this->doRecalculate);

        parent::onBeforeWrite();
    }

    /**
     * Saves changes on an existing position.
     *
     * @param array $changedFields The changed fields
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.09.2017
     */
    public function saveChanges($changedFields) {
        $price = $this->Price->getAmount();

        if (array_key_exists('Price', $changedFields)) {
            if (($changedFields['Price']['before']) !==
                ($changedFields['Price']['after'])) {

                $newPrice = $changedFields['Price']['after'];
                $this->Price->setAmount($newPrice->getAmount());
                $this->PriceTotal->setAmount($newPrice->getAmount() * $this->Quantity);
                $price = $newPrice->getAmount();
                $this->doRecalculate = true;
            }
        }

        if (array_key_exists('Quantity', $changedFields)) {
            if (($changedFields['Quantity']['before']) !==
                ($changedFields['Quantity']['after'])) {

                $this->PriceTotal->setAmount($price * $changedFields['Quantity']['after']);
                $this->doRecalculate = true;
            }
        }

        if (array_key_exists('ProductID', $changedFields)) {
            if (($changedFields['ProductID']['before']) !==
                ($changedFields['ProductID']['after'])) {

                $newProduct = Product::get()->byID($changedFields['ProductID']['after']);

                if ($newProduct instanceof Product &&
                    $newProduct->exists()) {
                    $this->Price->setAmount($newProduct->getPrice()->getAmount());
                    $this->PriceTotal->setAmount($newProduct->getPrice()->getAmount() * $this->Quantity);
                    $this->Tax                = $newProduct->getTaxAmount();
                    $this->TaxTotal           = $newProduct->getTaxAmount() * $this->Quantity;
                    $this->TaxRate            = $newProduct->getTaxRate();
                    $this->ProductDescription = $newProduct->LongDescription;
                    $this->Title              = $newProduct->Title;
                    $this->ProductNumber      = $newProduct->ProductNumberShop;
                    $this->doRecalculate      = true;
                }
            }
        }
        $this->extend('updateSaveChanges', $changedFields, $price, $this->doRecalculate);
    }

    /**
     * Saves changes for a new position.
     *
     * @param array $changedFields The changed fields
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.09.2017
     */
    public function saveNew($changedFields) {
        if (array_key_exists('ProductID', $changedFields)) {
            $productId = $changedFields['ProductID']['after'];

            $product = Product::get()->byID($productId);

            if ($product) {
                if (array_key_exists('Quantity', $changedFields) &&
                    (int) $changedFields['Quantity']['after'] > 0) {

                    $quantity = (int) $changedFields['Quantity']['after'];
                } else {
                    $quantity = 1;
                }

                $this->Price->setAmount($product->getPrice()->getAmount());
                $this->Price->setCurrency($product->getPrice()->getCurrency());
                $this->PriceTotal->setAmount($product->getPrice()->getAmount() * $quantity);
                $this->PriceTotal->setCurrency($product->getPrice()->getCurrency());
                $this->Quantity           = $quantity;
                $this->Tax                = $product->getTaxAmount();
                $this->TaxTotal           = $product->getTaxAmount() * $quantity;
                $this->TaxRate            = $product->getTaxRate();
                $this->ProductDescription = $product->LongDescription;
                $this->Title              = $product->Title;
                $this->ProductNumber      = $product->ProductNumberShop;
                $this->doRecalculate      = true;
            }
            $this->extend('updateSaveNew', $changedFields, $this->doRecalculate);
        }
    }

    /**
     * Recalculate the order if necessary.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.09.2017
     */
    public function onAfterWrite() {
        parent::onAfterWrite();

        if ($this->doRecalculate &&
            $this->Order()->ID != 0) {
            $this->Order()->recalculate();
            $this->doRecalculate = false;
        }
    }

    /**
     * Make onAfterDelete extendable.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.03.2012
     */
    public function onAfterDelete() {
        $this->extend('updateOnAfterDelete');

        parent::onAfterDelete();
    }

    /**
     * Make onBeforeDelete extendable.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.03.2012
     */
    public function onBeforeDelete() {
        if (!$this->objectDeleted) {
            $this->extend('updateOnBeforeDelete');
            $this->objectDeleted = true;
        }

        parent::onBeforeDelete();
    }

    /**
     * Returns additional tile information provided by plugins
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.04.2018
     */
    public function addToTitle() {
        $addToTitle = '';
        $this->extend('addToTitle', $addToTitle);
        return $addToTitle;
    }
}
