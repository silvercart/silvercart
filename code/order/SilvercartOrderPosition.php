<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Order
 */

/**
 * The SilvercartOrderPosition object.
 *
 * @package Silvercart
 * @subpackage Order
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 22.11.2010
 * @license see license file in modules root directory
 */
class SilvercartOrderPosition extends DataObject {

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
    public static $db = array(
        'Price'                              => 'Money',
        'PriceTotal'                         => 'Money',
        'isChargeOrDiscount'                 => 'Boolean(0)',
        'isIncludedInTotal'                  => 'Boolean(0)',
        'chargeOrDiscountModificationImpact' => "enum('none,productValue,totalValue','none')",
        'Tax'                                => 'Float',
        'TaxTotal'                           => 'Float',
        'TaxRate'                            => 'Float',
        'ProductDescription'                 => 'Text',
        'Quantity'                           => 'Decimal',
        'Title'                              => 'VarChar(255)',
        'ProductNumber'                      => 'VarChar',
        'numberOfDecimalPlaces'              => 'Int',
        'IsNonTaxable'                       => 'Boolean(0)',
    );

    /**
     * 1:n relations
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartOrder'   => 'SilvercartOrder',
        'SilvercartProduct' => 'SilvercartProduct',
    );

    /**
     * casted attributes
     *
     * @var array
     */
    public static $casting = array(
        'PriceNice'         => 'VarChar(255)',
        'PriceTotalNice'    => 'VarChar(255)',
        'FullTitle'         => 'HtmlText',
    );

    /**
     * Grant API access on this item.
     *
     * @var bool
     *
     * @since 2013-03-14
     */
    public static $api_access = true;

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     * 
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.11.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'SilvercartProduct'                     => _t('SilvercartOrderPosition.SILVERCARTPRODUCT'),
                'Price'                                 => _t('SilvercartOrderPosition.PRICE'),
                'PriceTotal'                            => _t('SilvercartOrderPosition.PRICETOTAL'),
                'isChargeOrDiscount'                    => _t('SilvercartOrderPosition.ISCHARGEORDISCOUNT'),
                'Tax'                                   => _t('SilvercartOrderPosition.TAX'),
                'TaxTotal'                              => _t('SilvercartOrderPosition.TAXTOTAL'),
                'TaxRate'                               => _t('SilvercartOrderPosition.TAXRATE'),
                'ProductDescription'                    => _t('SilvercartOrderPosition.PRODUCTDESCRIPTION'),
                'Quantity'                              => _t('SilvercartOrderPosition.QUANTITY'),
                'Title'                                 => _t('SilvercartOrderPosition.TITLE'),
                'ProductNumber'                         => _t('SilvercartOrderPosition.PRODUCTNUMBER'),
                'chargeOrDiscountModificationImpact'    => _t('SilvercartOrderPosition.CHARGEORDISCOUNTMODIFICATIONIMPACT'),
                'numberOfDecimalPlaces'                 => _t('SilvercartQuantityUnit.NUMBER_OF_DECIMAL_PLACES'),
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
        return SilvercartTools::singular_name_for($this);
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
        return SilvercartTools::plural_name_for($this);  
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
    public function CanView($member = null) {
        return $this->SilvercartOrder()->CanView();
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
    public function CanEdit($member = null) {
        return $this->SilvercartOrder()->CanEdit();
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
    public function CanDelete($member = null) {
        return $this->SilvercartOrder()->CanDelete();
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
     * Returns the quantity according to the SilvercartProduct quantity type
     * setting.
     *
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.11.2012
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
     * @return string
     */
    public function getFullTitle() {
        $fullTitle = $this->Title . '<br/>' . $this->addToTitle();
        $htmlText  = new HTMLText();
        $htmlText->setValue($fullTitle);
        return $htmlText;
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
        $fields = SilvercartDataObject::getCMSFields($this);
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
            $fields->makeFieldReadonly('SilvercartOrderID');
            $fields->makeFieldReadonly('SilvercartProductID');
            $fields->removeByName('isIncludedInTotal');
            $fields->removeByName('numberOfDecimalPlaces');
            $fields->removeByName('IsNonTaxable');
        }
        return $fields;
    }
    
    /**
     * Return fields for popup.
     *
     * @return FieldSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.03.2012
     */
    public function getCMSFields_forPopup() {
        $fields  = new FieldList();
        $orderId = 0;

        $fields->push(
            new HiddenField(
                'SilvercartOrderID',
                '',
                $orderId
            )
        );
        $fields->push(
            new DropdownField(
                'SilvercartProductID',
                $this->fieldLabel('SilvercartProduct'),
                SilvercartProduct::get()->map('ID', 'Title')->toArray()
            )
        );
        $fields->push(
            new TextField(
                'Quantity',
                $this->fieldLabel('Quantity'),
                '1'
            )
        );

        $this->extend('updateGetCMSFields_forPopup', $fields);

        return $fields;
    }

    /**
     * If the attributed product gets changed we adjust all order position
     * fields accordingly.
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.03.2012
     */
    public function onBeforeWrite() {
        $changedFields = $this->getChangedFields();

        if (!$this->objectCreated &&
             array_key_exists('SilvercartOrderID', $changedFields)) {
            $this->saveNew($changedFields);
            $this->objectCreated = true;
        } else if (!$this->objectCreated) {
            $this->saveChanges($changedFields);
        }

        $this->extend('updateOnBeforeWrite', $changedFields, $price, $this->doRecalculate);

        parent::onBeforeWrite();
    }

    /**
     * Saves changes on an existing position.
     *
     * @param array $changedFields The changed fields
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.10.2012
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

        if (array_key_exists('SilvercartProductID', $changedFields)) {
            if (($changedFields['SilvercartProductID']['before']) !==
                ($changedFields['SilvercartProductID']['after'])) {

                $newProduct = DataObject::get_by_id(
                    'SilvercartProduct',
                    $changedFields['SilvercartProductID']['after']
                );

                if ($newProduct) {
                    $this->Price->setAmount($newProduct->getPrice()->getAmount());
                    $this->PriceTotal->setAmount($newProduct->getPrice()->getAmount() * $this->Quantity);
                    $this->setField('Tax', $newProduct->getTaxAmount());
                    $this->setField('TaxTotal', $newProduct->getTaxAmount() * $this->Quantity);
                    $this->setField('TaxRate', $newProduct->getTaxRate());
                    $this->setField('ProductDescription', $newProduct->LongDescription);
                    $this->setField('Title', $newProduct->Title);
                    $this->setField('ProductNumber', $newProduct->ProductNumberShop);
                    $this->doRecalculate = true;
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.03.2012
     */
    public function saveNew($changedFields) {
        if (array_key_exists('SilvercartProductID', $changedFields)) {
            $productId = $changedFields['SilvercartProductID']['after'];

            $silvercartProduct = DataObject::get_by_id('SilvercartProduct', $productId);

            if ($silvercartProduct) {
                if (array_key_exists('Quantity', $changedFields) &&
                    (int) $changedFields['Quantity']['after'] > 0) {

                    $quantity = (int) $changedFields['Quantity']['after'];
                } else {
                    $quantity = 1;
                }

                $this->Price->setAmount($silvercartProduct->getPrice()->getAmount());
                $this->Price->setCurrency($silvercartProduct->getPrice()->getCurrency());
                $this->PriceTotal->setAmount($silvercartProduct->getPrice()->getAmount() * $quantity);
                $this->PriceTotal->setCurrency($silvercartProduct->getPrice()->getCurrency());
                $this->setField('Quantity', $quantity);
                $this->setField('Tax', $silvercartProduct->getTaxAmount());
                $this->setField('TaxTotal', $silvercartProduct->getTaxAmount() * $quantity);
                $this->setField('TaxRate', $silvercartProduct->getTaxRate());
                $this->setField('ProductDescription', $silvercartProduct->LongDescription);
                $this->setField('Title', $silvercartProduct->Title);
                $this->setField('ProductNumber', $silvercartProduct->ProductNumberShop);
                $this->doRecalculate = true;
            }
            $this->extend('updateSaveNew', $changedFields, $price, $this->doRecalculate);
        }
    }

    /**
     * Recalculate the order if necessary.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.10.2012
     */
    public function onAfterWrite() {
        parent::onAfterWrite();

        if ($this->doRecalculate &&
            $this->SilvercartOrder()->ID != 0) {
            $this->SilvercartOrder()->recalculate();
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
     * @since 26.06.2012
     */
    public function addToTitle() {
        $addToTitle = SilvercartPlugin::call($this, 'addToTitle', null, false, '');

        return $addToTitle;
    }
}
