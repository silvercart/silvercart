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
 * abstract for shopping cart positions
 *
 * @package Silvercart
 * @subpackage Order
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 22.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartShoppingCartPosition extends DataObject {

    /**
     * Singular-Beschreibung zur Darstellung im Backend.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $singular_name = "cart position";
    /**
     * Plural-Beschreibung zur Darstellung im Backend.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $plural_name = "cart positions";
    /**
     * attributes
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $db = array(
        'Quantity'                          => 'Int',
        'ShowQuantityAdjustedMessage'       => 'Boolean(0)',
        'ShowRemainingQuantityAddedMessage' => 'Boolean(0)'
        
    );
    /**
     * n:m relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $has_one = array(
        'SilvercartProduct'      => 'SilvercartProduct',
        'SilvercartShoppingCart' => 'SilvercartShoppingCart'
    );
    
    /**
     * Indicates wether the forms should be loaded.
     *
     * @var boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 27.04.2011
     */
    public static $doCreateForms = true;
    
    /**
     * Sets wether the form objects should be created.
     *
     * @param boolean $doCreateForms Indicates wether the forms should be loaded.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 27.04.2011
     */
    public static function setCreateForms($doCreateForms = true) {
        self::$doCreateForms = $doCreateForms;
    }

    /**
     * Registers the edit-forms for this position.
     *
     * @param array|null $record      This will be null for a new database record.  Alternatively, you can pass an array of
     *                                  field values.  Normally this contructor is only used by the internal systems that get objects from the database.
     * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.  Singletons
     *                                  don't have their defaults set.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 11.02.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        parent::__construct($record, $isSingleton);

        $controller = Controller::curr();

        if ($controller->hasMethod('getRegisteredCustomHtmlForm') &&
            self::$doCreateForms) {
            $positionForms = array(
                'SilvercartIncrementPositionQuantityForm',
                'SilvercartDecrementPositionQuantityForm',
                'SilvercartRemovePositionForm'
            );

            foreach ($positionForms as $positionForm) {
                if (!$controller->getRegisteredCustomHtmlForm($positionForm . $this->ID)) {
                    $controller->registerCustomHtmlForm(
                            $positionForm . $this->ID,
                            new $positionForm(
                                    $controller,
                                    array(
                                        'positionID' => $this->ID
                                    )
                            )
                    );
                }
            }
        }
    }

    /**
     * price sum of this position
     *
     * @return Money the price sum
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 22.10.2010
     */
    public function getPrice() {
        $product = $this->SilvercartProduct();
        $price = 0;

        if ($product && $product->Price->getAmount()) {
            $price = $product->Price->getAmount() * $this->Quantity;
        }

        $priceObj = new Money();
        $priceObj->setAmount($price);
        $priceObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $priceObj;
    }

    /**
     * Returns the form for incrementing the amount of this position.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 11.02.2011
     */
    public function getIncrementPositionQuantityForm() {
        return Controller::curr()->InsertCustomHtmlForm('SilvercartIncrementPositionQuantityForm' . $this->ID);
    }

    /**
     * Returns the form for decrementing the amount of this position.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 11.02.2011
     */
    public function getDecrementPositionQuantityForm() {
        return Controller::curr()->InsertCustomHtmlForm('SilvercartDecrementPositionQuantityForm' . $this->ID);
    }

    /**
     * Returns the form for removing this position.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 11.02.2011
     */
    public function getRemovePositionForm() {
        return Controller::curr()->InsertCustomHtmlForm('SilvercartRemovePositionForm' . $this->ID);
    }
    
    /**
     * Find out if the demanded quantity is in stock when stock management is enabled.
     * If stock management is disabled true will be returned.
     * 
     * @param integer $quantity The quantity of products
     * 
     * @return bool Can this position be incremented
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.7.2011 
     */
    public function isQuantityIncrementableBy($quantity = 1) {
        if (SilvercartConfig::isEnabledStockManagement() && !$this->SilvercartProduct()->isStockQuantityOverbookable()) {
            if ($this->SilvercartProduct()->StockQuantity >= ($this->Quantity + $quantity)) {
                return true;
            }
            return false;
        }
        return true;
    }
    
    /**
     * Has a positions quantity become unavailable?
     * 
     * @return bool Is the set quantity of $this unavailable?
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.7.2011
     */
    public function showQuantityUnavailableText() {
        if (SilvercartConfig::isEnabledStockManagement()
            && !$this->SilvercartProduct()->isStockQuantityOverbookable()
            && $this->SilvercartProduct()->StockQuantity > $this->Quantity) {
            return false;
        }
        return true;
    }
    
    /**
     * Returns messages determined by tokens of $this.
     * The messages are i18n.
     * 
     * @return string the messages determined by tokens
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.7.2011
     */
    public function getShoppingCartPositionMessage() {
        $text = "";
        if ($this->ShowQuantityAdjustedMessage) {
            $text .= _t('SilvercartShoppingCartPosition.QUANTITY_ADJUSTED_MESSAGE') . "<br />";
        }
        if ($this->ShowRemainingQuantityAddedMessage) {
            $text .= _t('SilvercartShoppingCartPosition.REMAINING_QUANTITY_ADDED_MESSAGE') . "<br />";
        }
        
        return $text;
    }
    
    /**
     * Decrement the positions quantity if it is higher than the stock quantity.
     * If this position has a quantity of 5 but the products stock quantity is
     * only 3 the positions quantity would be set to 3.
     * This happens only if the product is not overbookable.
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.7.2011
     */
    public function adjustQuantityToStockQuantity() {
        if ($this->Quantity > $this->SilvercartProduct()->StockQuantity) {
            $this->Quantity = $this->SilvercartProduct()->StockQuantity;
            $this->ShowQuantityAdjustedMessage = true;
            $this->write();
        }
    }
    
    /**
     * Feedback message for customer if he tries to put a larger quantity of a
     * product in his cart than there is availible in stock.
     * This does only make sense if stock management is activated.
     * 
     * @return mixed string|bool Message or false
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.7.2011
     */
    public function getQuantityUnavailableText() {
        $text = "";
        $title = $this->SilvercartProduct()->Title;
        $stockQuantity = $this->SilvercartProduct()->StockQuantity;
        $text = sprintf(_t('SilvercartCartPage.QUANTITY_INAVAILABLE','Only %1$s piece(s) of "%2$s" are available.'), $stockQuantity, $title);
        return $text;
    }
    
    /**
     * A position may have feedback messages to the customer which are determined
     * by tokens.
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.7.2011
     */
    public function resetMessageTokens() {
        $this->ShowQuantityAdjustedMessage = false;
        $this->ShowRemainingQuantityAddedMessage = false;
        $this->write();
    }

}
