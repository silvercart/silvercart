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
 * @subpackage Order
 */

/**
 * The SilvercartOrderPosition object.
 *
 * @package Silvercart
 * @subpackage Order
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 22.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
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
     * @var booleanhler <skoehler@pixeltricks.de>
     * @since 21.03.2012
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
        'chargeOrDiscountModificationImpact' => "enum('none,productValue,totalValue','none')",
        'Tax'                                => 'Float',
        'TaxTotal'                           => 'Float',
        'TaxRate'                            => 'Float',
        'ProductDescription'                 => 'Text',
        'Quantity'                           => 'Int',
        'Title'                              => 'VarChar(255)',
        'ProductNumber'                      => 'VarChar',
    );

    /**
     * 1:n relations
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartOrder' => 'SilvercartOrder',
        'SilvercartProduct' => 'SilvercartProduct'
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
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     * 
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.03.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'SilvercartProduct'  => _t('SilvercartOrderPosition.SILVERCARTPRODUCT'),
                'Price'              => _t('SilvercartOrderPosition.PRICE'),
                'PriceTotal'         => _t('SilvercartOrderPosition.PRICETOTAL'),
                'isChargeOrDiscount' => _t('SilvercartOrderPosition.ISCHARGEORDISCOUNT'),
                'Tax'                => _t('SilvercartOrderPosition.TAX'),
                'TaxTotal'           => _t('SilvercartOrderPosition.TAXTOTAL'),
                'TaxRate'            => _t('SilvercartOrderPosition.TAXRATE'),
                'ProductDescription' => _t('SilvercartOrderPosition.PRODUCTDESCRIPTION'),
                'Quantity'           => _t('SilvercartOrderPosition.QUANTITY'),
                'Title'              => _t('SilvercartOrderPosition.TITLE'),
                'ProductNumber'      => _t('SilvercartOrderPosition.PRODUCTNUMBER')
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
     * returns the order positions Title with extensions
     *
     * @return string
     */
    public function getFullTitle() {
        return $this->Title . '<br/>' . $this->addToTitle();
    }

    /**
     * Returns true if this position has a quantity of more than 1.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
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
     * @return FieldSet the form fields for the backend
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.03.2012
     */
    public function getCMSFields() {
        if ($this->ID === 0) {
            return $this->getCMSFields_forPopup();
        }

        $fields = parent::getCMSFields();

        $fields->removeByName('isChargeOrDiscount');
        $fields->removeByName('chargeOrDiscountModificationImpact');
        $fields->removeByName('Tax');
        $fields->removeByName('TaxTotal');
        $fields->removeByName('TaxRate');
        $fields->removeByName('SilvercartOrderID');
        $fields->removeByName('SilvercartProductGroupID');

        $productDescriptionField = $fields->dataFieldByName('ProductDescription');
        if ($productDescriptionField) {
            $productDescriptionField->setReadonly(true);
        }

        $productNumberField = $fields->dataFieldByName('ProductNumber');
        if ($productNumberField) {
            $productNumberField->setReadonly(true);
            $productNumberField->disabled = true;
        }

        $titleField = $fields->dataFieldByName('Title');
        if ($titleField) {
            $titleField->setReadonly(true);
            $titleField->disabled = true;
        }

        $priceTotalField = $fields->dataFieldByName('PriceTotal');
        if ($priceTotalField) {
            $priceTotalField->setReadonly(true);
            $priceTotalField->disabled = true;
        }

        if ($this->SilvercartOrder()->ID > 0) {
            $link = sprintf(
                "javascript:jQuery('#ModelAdminPanel').fn('loadForm', '%sadmin/silvercart-orders/SilvercartOrder/%d/edit',function() {openTab('Root_SilvercartOrderPositions');});",
                SilvercartTools::getBaseURLSegment(),
                $this->SilvercartOrder()->ID
            );
            $backToOrderLinkField = new LiteralField(
                'BackToOrderLinkField',
                '<p><a href="'.$link.'" class="action">Zur Bestellung</a></p>'
            );

            $fields->insertBefore($backToOrderLinkField, 'Root');
        }

        $this->extend('updateCMSFields', $fields);

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
        $fields  = new FieldSet();
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
                SilvercartProduct::get()->map('ID', 'Title')
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.03.2012
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
                    $this->setField('ProductNumber', $newProduct->ProductNumber);
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.03.2012
     */
    public function onAfterWrite() {
        parent::onAfterWrite();

        if ($this->doRecalculate) {
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
