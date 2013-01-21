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
 * abstract for shopping cart
 *
 * @package Silvercart
 * @subpackage Order
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 22.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartShoppingCart extends DataObject {

    /**
     * Contains all registered modules that get called when the shoppingcart
     * is displayed.
     *
     * @var array
     */
    public static $registeredModules = array();

    /**
     * Singular-Beschreibung zur Darstellung im Backend.
     *
     * @var string
     */

    /**
     * 1:n relations
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartShoppingCartPositions' => 'SilvercartShoppingCartPosition'
    );
    
    /**
     * defines n:m relations
     *
     * @var array configure relations
     */
    public static $many_many = array(
        'SilvercartProducts' => 'SilvercartProduct'
    );

    /**
     * Indicates wether the registered modules should be loaded.
     *
     * @var boolean
     */
    public static $loadModules = true;

    /**
     * Indicates wether the registered modules should be loaded.
     *
     * @var boolean
     */
    public static $createForms = true;

    /**
     * Contains the ID of the payment method the customer has chosen.
     *
     * @var Int
     */
    protected $paymentMethodID;

    /**
     * Contains the ID of the shipping method the customer has chosen.
     *
     * @var Int
     */

    protected $shippingMethodID;

    /**
     * Contains the calculated charges and discounts for product values for
     * caching purposes.
     *
     * @var DataObject
     */
    protected $chargesAndDiscountsForProducts = null;
    
    /**
     * Contains the calculated charges and discounts for the shopping cart
     * total for caching purposes.
     *
     * @var DataObject
     */
    protected $chargesAndDiscountsForTotal = null;

    /**
     * Contains hashes for caching.
     * 
     * @var array
     */
    protected $cacheHashes = array();
    
    /**
     * List of already calculated tax amounts
     *
     * @var array
     */
    protected $taxTotalList = array();
    
    /**
     * List of already calculated tax rates with fees
     *
     * @var DataObjectSet
     */
    protected $taxRatesWithFees = null;


    /**
     * Marker to check whether the cart position cleaning is in progress or not.
     * This is used to prevent an endless recursion loop.
     *
     * @var bool
     */
    public static $cartCleaningInProgress = false;
    
    /**
     * Marker to check whether the cart position cleaning is finished or not.
     *
     * @var bool
     */
    public static $cartCleaningFinished = false;

    /**
     * default constructor
     *
     * @param array $record      array of field values
     * @param bool  $isSingleton true if this is a singleton() object
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.12.2012
     */
    public function __construct($record = null, $isSingleton = false) {
        parent::__construct($record, $isSingleton);
        if ($this->ID > 0) {
            if (!SilvercartTools::isIsolatedEnvironment() &&
                !SilvercartTools::isBackendEnvironment()) {
                // Initialize shopping cart position object, so that it can inject
                // its forms into the controller.
                if (self::$loadModules) {
                    foreach ($this->SilvercartShoppingCartPositions() as $position) {
                        $position->registerCustomHtmlForms();
                    }
                }

                if (!self::$cartCleaningFinished && 
                    !self::$cartCleaningInProgress) {
                    self::$cartCleaningInProgress = true;

                    $this->cleanUp();
                }

                $this->SilvercartShippingMethodID = 0;
                $this->SilvercartPaymentMethodID = 0;

                if (Member::currentUserID() &&
                    self::$loadModules) {

                    $this->callMethodOnRegisteredModules(
                        'performShoppingCartConditionsCheck',
                        array(
                            $this,
                            Member::currentUser()
                        )
                    );

                    $this->callMethodOnRegisteredModules(
                        'ShoppingCartInit'
                    );
                }
            }
        }
    }

    /**
     * Deletes all shopping cart positions without a product association.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.11.2012
     */
    protected function cleanUp() {
        $positionIds = DB::query(
            sprintf(
                "
                SELECT
                    ID
                FROM
                    SilvercartShoppingCartPosition
                WHERE
                    SilvercartShoppingCartPosition.SilvercartShoppingCartID = %d AND
                    SilvercartProductID = 0
                ",
                $this->ID
            )
        );

        if ($positionIds) {
            foreach ($positionIds as $positionId) {
                $position = DataObject::get_by_id('SilvercartShoppingCartPosition', $positionId);
                $position->delete();
            }
        }
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
     * Indicates wether the cart has charges and discounts for the product
     * values.
     *
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.12.2011
     */
    public function HasChargesAndDiscountsForProducts() {
        if ($this->ChargesAndDiscountsForProducts()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Indicates wether the cart has charges and discounts for the total
     * shopping cart value.
     *
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.12.2011
     */
    public function HasChargesAndDiscountsForTotal() {
        if ($this->ChargesAndDiscountsForTotal()) {
            return true;
        }
        
        return false;
    }

    /**
     * Returns true if the given value is higher than the number of positions
     * in the cart.
     *
     * @param int $positions The value to check
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2013-01-08
     */
    public function HasMorePositionsThan($positions = 0) {
        $numberOfPositions = (int) $this->getQuantity();

        return (int) $positions > $numberOfPositions;
    }

    /**
     * Returns true if the number of positions in the cart equals the given
     * value.
     *
     * @param int $positions The value to check
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2013-01-08
     */
    public function HasNumberOfPositions($positions = 0) {
        $numberOfPositions = (int) $this->getQuantity();

        return (int) $positions === $numberOfPositions;
    }
    
    /**
     * Returns the charges and discounts for product values.
     *
     * @param string $priceType 'gross' or 'net'
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 14.12.2011
     */
    public function ChargesAndDiscountsForProducts($priceType = false) {
        $cacheHash = md5($priceType);
        $cacheKey = 'ChargesAndDiscountsForProducts_'.$cacheHash;

        if (array_key_exists($cacheKey, $this->cacheHashes)) {
            return $this->cacheHashes[$cacheKey];
        }

        $paymentMethodObj = $this->getPaymentMethod();

        if ($paymentMethodObj) {
            $handlingCostPayment = $paymentMethodObj->getChargesAndDiscountsForProducts($this, $priceType);
            
            if ($handlingCostPayment === false) {
                return false;
            } else {
                $taxes          = $this->getTaxRatesWithoutFeesAndCharges('SilvercartVoucher');
                $silvercartTax  = $this->getMostValuableTaxRate($taxes);

                $chargesAndDiscounts = new DataObject(
                    array(
                        'Name'                  => $paymentMethodObj->sumModificationLabel,
                        'sumModificationImpact' => $paymentMethodObj->sumModificationImpact,
                        'PriceFormatted'        => $handlingCostPayment->Nice(),
                        'Price'                 => $handlingCostPayment,
                        'SilvercartTax'         => $silvercartTax
                    )
                );

                $this->chargesAndDiscountsForProducts = $chargesAndDiscounts;
                $this->cacheHashes[$cacheKey] = $this->chargesAndDiscountsForProducts;

                return $chargesAndDiscounts;
            }
        }

        return false;
    }
    
    /**
     * Returns the charges and discounts for the shopping cart total.
     *
     * @param string $priceType 'gross' or 'net'
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.12.2011
     */
    public function ChargesAndDiscountsForTotal($priceType = false) {
        $cacheHash = md5($priceType);
        $cacheKey = 'ChargesAndDiscountsForTotal_'.$cacheHash;

        if (array_key_exists($cacheKey, $this->cacheHashes)) {
            return $this->cacheHashes[$cacheKey];
        }

        $paymentMethodObj = $this->getPaymentMethod();

        if ($paymentMethodObj) {
            $handlingCostPayment = $paymentMethodObj->getChargesAndDiscountsForTotal($this, $priceType);
            
            if ($handlingCostPayment === false) {
                return false;
            } else {
                $taxes               = $this->getTaxRatesWithFees();
                $silvercartTax       = $this->getMostValuableTaxRate($taxes);
                $handlingCostPaymentRounded = $handlingCostPayment;
                $handlingCostPaymentRounded->setAmount(
                    round($handlingCostPayment->getAmount(), 2)
                );
                $chargesAndDiscounts = new DataObject(
                    array(
                        'Name'                  => $paymentMethodObj->sumModificationLabel,
                        'sumModificationImpact' => $paymentMethodObj->sumModificationImpact,
                        'PriceFormatted'        => $handlingCostPayment->Nice(),
                        'Price'                 => $handlingCostPayment,
                        'SilvercartTax'         => $silvercartTax
                    )
                );

                $this->chargesAndDiscountsForTotal = $chargesAndDiscounts;
                $this->cacheHashes[$cacheKey] = $this->chargesAndDiscountsForTotal;

                return $chargesAndDiscounts;
            }
        }

        return false;
    }

    /**
     * Set wether the registered modules should be loaded and handled.
     *
     * @param boolean $doLoad set wether to load the modules or not
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 27.04.2011
     */
    public static function setLoadShoppingCartModules($doLoad) {
        self::$loadModules = $doLoad;
    }

    /**
     * Set wether the shopping cart forms should be drawn.
     *
     * @param boolean $doCreate set wether to create the forms or not
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 27.04.2011
     */
    public static function setCreateShoppingCartForms($doCreate) {
        self::$createForms = $doCreate;
    }

    /**
     * adds a product to the cart
     *
     * @param array $formData the sended form data
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 21.12.2010
     */
    public static function addProduct($formData) {
        $error  = true;
        $member = Member::currentUser();
        
        if (!$member) {
            $member = SilvercartCustomer::createAnonymousCustomer();
        }
        
        $overwriteAddProduct = SilvercartPlugin::call($member->getCart(), 'overwriteAddProduct', array($formData), false, 'boolean');
        
        if ($overwriteAddProduct) {
            $error = false;
        } else {
            if ($formData['productID'] && $formData['productQuantity']) {
                $cart = $member->getCart();
                if ($cart) {
                    $product = DataObject::get_by_id('SilvercartProduct', $formData['productID'], 'Created');
                    if ($product) {
                        $formData['productQuantity'] = str_replace(',', '.', $formData['productQuantity']);
                        $quantity                    = (float) $formData['productQuantity'];

                        if ($quantity > 0) {
                            $product->addToCart($cart->ID, $quantity);
                            $error = false;
                        }
                    }
                }
            }
        }

        return !$error;
    }
    
    /**
     * Returns one or more plugged in rows for the shopping carts editable table
     * as a DataobjectSet
     * 
     * @return DataObjectSet
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.09.2012
     */
    public function addToEditableShoppingCartTable() {
        $addToCartTable = SilvercartPlugin::call($this, 'addToEditableShoppingCartTable', array(), false, 'DataObjectSet');
        return $addToCartTable;
    }

    /**
     * empties cart
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public function delete() {
        $positions = $this->SilvercartShoppingCartPositions();

        foreach ($positions as $position) {
            $position->delete();
        }
    }

    /**
     * returns quantity of all products in the cart
     *
     * @param int $productId if set only product quantity of this product is returned
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.11.10
     */
    public function getQuantity($productId = null) {
        $positions = $this->SilvercartShoppingCartPositions();
        $quantity = 0;

        foreach ($positions as $position) {
            if ($productId === null ||
                    $position->SilvercartProduct()->ID === $productId) {

                $quantity += $position->Quantity;
            }
        }

        return $quantity;
    }

    /**
     * Returns the price of the cart positions + fees, including taxes.
     *
     * @param array   $excludeShoppingCartPositions Positions that shall not be counted;
     *                                              can contain the ID or the className of the position
     * @param boolean $excludeCharges               Indicates wether charges and discounts should be calculated
     *
     * @return string a price amount
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function getTaxableAmountWithFees($excludeShoppingCartPositions = false, $excludeCharges = false) {
        if (SilvercartConfig::PriceType() == 'gross') {
            $taxableAmountWithFees = $this->getTaxableAmountGrossWithFees($excludeShoppingCartPositions = false, $excludeCharges = false);
        } else {
            $taxableAmountWithFees = $this->getTaxableAmountNetWithFees($excludeShoppingCartPositions = false, $excludeCharges = false);
        }
        return $taxableAmountWithFees;
    }

    /**
     * Returns the GROSS price of the cart positions + fees, including taxes.
     *
     * @param array   $excludeShoppingCartPositions Positions that shall not be counted;
     *                                              can contain the ID or the className of the position
     * @param boolean $excludeCharges               Indicates wether charges and discounts should be calculated
     *
     * @return string a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function getTaxableAmountGrossWithFees($excludeShoppingCartPositions = false, $excludeCharges = false) {
        $shippingMethod = $this->getShippingMethod();
        $paymentMethod  = $this->getPaymentMethod();
        $amountTotal    = $this->getTaxableAmountGrossWithoutFees(null, $excludeShoppingCartPositions, $excludeCharges)->getAmount();

        if ($shippingMethod) {
            $shippingFee = $shippingMethod->getShippingFee();

            if ($shippingFee !== false) {
                $shippingFeeAmount = $shippingFee->getPriceAmount();
                $amountTotal = $shippingFeeAmount + $amountTotal;
            }
        }

        if ($paymentMethod) {
            $paymentFee = $paymentMethod->getHandlingCost();

            if ($paymentFee !== false) {
                $paymentFeeAmount = $paymentFee->getPriceAmount();
                $amountTotal = $paymentFeeAmount + $amountTotal;
            }
        }
        
        $amountTotalObj = new Money;
        $amountTotalObj->setAmount($amountTotal);
        $amountTotalObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $amountTotalObj;
    }

    /**
     * Returns the NET price of the cart positions + fees, including taxes.
     *
     * @param array   $excludeShoppingCartPositions Positions that shall not be counted;
     *                                              can contain the ID or the className of the position
     * @param boolean $excludeCharges               Indicates wether charges and discounts should be calculated
     *
     * @return string a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function getTaxableAmountNetWithFees($excludeShoppingCartPositions = false, $excludeCharges = false) {
        $shippingMethod = $this->getShippingMethod();
        $paymentMethod  = $this->getPaymentMethod();
        $amountTotal    = round($this->getTaxableAmountNetWithoutFees(null, $excludeShoppingCartPositions, $excludeCharges)->getAmount(), 2);

        if ($shippingMethod) {
            $shippingFee = $shippingMethod->getShippingFee();

            if ($shippingFee !== false) {
                $shippingFeeAmount = $shippingFee->getPriceAmount();
                $amountTotal       = $shippingFeeAmount + $amountTotal;
            }
        }

        if ($paymentMethod) {
            $paymentFee = $paymentMethod->getHandlingCost();

            if ($paymentFee !== false) {
                $paymentFeeAmount = $paymentFee->getPriceAmount();
                $amountTotal      = $paymentFeeAmount + $amountTotal;
            }
        }

        $amountTotalObj = new Money;
        $amountTotalObj->setAmount($amountTotal);
        $amountTotalObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $amountTotalObj;
    }

    /**
     * Returns the price of the cart positions, including taxes, excluding fees.
     *
     * @param array   $excludeModules              An array of registered modules that shall not
     *                                             be taken into account.
     * @param array   $excludeShoppingCartPosition Positions that shall not be counted;
     *                                             can contain the ID or the className of the position
     * @param boolean $excludeCharges              Indicates wether charges and discounts should be calculated
     * 
     * @return Money a price amount
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function getTaxableAmountWithoutFees($excludeModules = array(), $excludeShoppingCartPosition = false, $excludeCharges = false) {
        if (SilvercartConfig::PriceType() == 'gross') {
            $taxableAmountWithoutFees = $this->getTaxableAmountGrossWithoutFees($excludeModules, $excludeShoppingCartPosition, $excludeCharges);
        } else {
            $taxableAmountWithoutFees = $this->getTaxableAmountNetWithoutFees($excludeModules, $excludeShoppingCartPosition, $excludeCharges);
        }
        return $taxableAmountWithoutFees;
    }

    /**
     * Returns the GROSS price of the cart positions, including taxes, excluding fees.
     *
     * @param array   $excludeModules              An array of registered modules that shall not
     *                                             be taken into account.
     * @param array   $excludeShoppingCartPosition Positions that shall not be counted;
     *                                             can contain the ID or the className of the position
     * @param boolean $excludeCharges              Indicates wether charges and discounts should be calculated
     * 
     * @return Money a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function getTaxableAmountGrossWithoutFees($excludeModules = array(), $excludeShoppingCartPosition = false, $excludeCharges = false) {
        $amount = $this->getTaxableAmountGrossWithoutFeesAndCharges($excludeModules, $excludeShoppingCartPosition)->getAmount();
        
        // Handling costs for payment and shipment
        if (!$excludeCharges &&
             $this->ChargesAndDiscountsForProducts()) {
            
            $amount += $this->ChargesAndDiscountsForProducts()->Price->getAmount();
        }
        
        $amountObj = new Money;
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $amountObj;
    }

    /**
     * Returns the NET price of the cart positions, including taxes, excluding fees.
     *
     * @param array   $excludeModules              An array of registered modules that shall not
     *                                             be taken into account.
     * @param array   $excludeShoppingCartPosition Positions that shall not be counted;
     *                                             can contain the ID or the className of the position
     * @param boolean $excludeCharges              Indicates wether charges and discounts should be calculated
     * 
     * @return Money a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function getTaxableAmountNetWithoutFees($excludeModules = array(), $excludeShoppingCartPosition = false, $excludeCharges = false) {
        $amount = $this->getTaxableAmountNetWithoutFeesAndCharges($excludeModules, $excludeShoppingCartPosition)->getAmount();
        
        // Handling costs for payment and shipment
        if (!$excludeCharges &&
             $this->ChargesAndDiscountsForProducts()) {
            
            $amount += $this->ChargesAndDiscountsForProducts()->Price->getAmount();
        }
        
        if (round($amount, 2) === -0.00) {
            $amount = 0;
        }

        $amountObj = new Money;
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $amountObj;
    }
    
    /**
     * Returns the price of the cart positions without modules.
     *
     * The price type is automatically determined by the
     * SilvercartShoppinCartPosition.
     *
     * @return Money a price amount
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function getTaxableAmountWithoutModules() {
        if (SilvercartConfig::PriceType() == 'gross') {
            $taxableAmountWithoutModules = $this->getTaxableAmountGrossWithoutModules();
        } else {
            $taxableAmountWithoutModules = $this->getTaxableAmountNetWithoutModules();
        }
        return $taxableAmountWithoutModules;
    }
    
    /**
     * Returns the GROSS price of the cart positions without modules.
     *
     * The price type is automatically determined by the
     * SilvercartShoppinCartPosition.
     *
     * @return Money a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function getTaxableAmountGrossWithoutModules() {
        $amountObj = new Money();
        $amount    = 0;

        $modulePositions = $this->getTaxableShoppingcartPositions(array(), array(), false);
        foreach ($modulePositions as $modulePosition) {
            $amount += (float) $modulePosition->getPrice(false, 'gross')->getAmount();
        }

        $amountObj->setAmount($amount);
        $amountObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $amountObj;
    }
    
    /**
     * Returns the NET price of the cart positions without modules.
     *
     * The price type is automatically determined by the
     * SilvercartShoppinCartPosition.
     *
     * @return Money a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function getTaxableAmountNetWithoutModules() {
        $amountObj = new Money();
        $amount    = 0;

        $modulePositions = $this->getTaxableShoppingcartPositions(array(), array(), false);
        foreach ($modulePositions as $modulePosition) {
            $amount += (float) $modulePosition->getPrice(false, 'net')->getAmount();
        }

        $amountObj->setAmount($amount);
        $amountObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $amountObj;
    }

    /**
     * Returns all taxable shopping cart positions.
     *
     * @param array $excludeModules              An array of registered modules that shall not
     *                                           be taken into account.
     * @param array $excludeShoppingCartPosition Positions that shall not be counted;
     *                                           can contain the ID or the className of the position
     * @param bool  $includeModules              Indicate whether to include modules or not
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 19.07.2012
     */
    public function getTaxableShoppingcartPositions($excludeModules = array(), $excludeShoppingCartPosition = false, $includeModules = true) {
        $cartPositions = new DataObjectSet();

        if (!is_array($excludeModules)) {
            $excludeModules = array($excludeModules);
        }
        if (!is_array($excludeShoppingCartPosition)) {
            $excludeShoppingCartPosition = array($excludeShoppingCartPosition);
        }

        $cacheHash = md5(
            implode(',', $excludeModules).
            implode(',', $excludeShoppingCartPosition).
            $includeModules
        );
        $cacheKey = 'ggetTaxableShoppingcartPositions_'.$cacheHash;

        if (array_key_exists($cacheKey, $this->cacheHashes)) {
            return $this->cacheHashes[$cacheKey];
        }

        foreach ($this->SilvercartShoppingCartPositions() as $position) {
            $cartPositions->push($position);
        }

        if ($includeModules) {
            $registeredModules = $this->callMethodOnRegisteredModules(
                'ShoppingCartPositions',
                array(
                    $this,
                    Member::currentUser(),
                    true,
                    $excludeShoppingCartPosition,
                    false
                ),
                $excludeModules,
                $excludeShoppingCartPosition
            );

            // Registered Modules
            if ($registeredModules) {
                foreach ($registeredModules as $moduleName => $modulePositions) {
                    foreach ($modulePositions as $modulePosition) {
                        $cartPositions->push($modulePosition);
                    }
                }
            }
        }

        $this->cacheHashes[$cacheKey] = $cartPositions;

        return $cartPositions;
    }

    /**
     * Returns the price of the cart positions, including taxes.
     *
     * @param array $excludeModules              An array of registered modules that shall not
     *                                           be taken into account.
     * @param array $excludeShoppingCartPosition Positions that shall not be counted;
     *                                           can contain the ID or the className of the position
     * 
     * @return Money a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 15.12.2011
     */
    public function getTaxableAmountGrossWithoutFeesAndCharges($excludeModules = array(), $excludeShoppingCartPosition = false) {
        if (!is_array($excludeModules)) {
            $excludeModules = array($excludeModules);
        }
        if (!is_array($excludeShoppingCartPosition)) {
            $excludeShoppingCartPosition = array($excludeShoppingCartPosition);
        }

        $cacheHash = md5(
            implode(',', $excludeModules).
            implode(',', $excludeShoppingCartPosition)
        );
        $cacheKey = 'getTaxableAmountGrossWithoutFeesAndCharges_'.$cacheHash;

        if (array_key_exists($cacheKey, $this->cacheHashes)) {
            return $this->cacheHashes[$cacheKey];
        }

        $amountObj = new Money();
        $amountObj->setCurrency(SilvercartConfig::DefaultCurrency());
        $amount    = 0;

        $modulePositions = $this->getTaxableShoppingcartPositions($excludeModules, $excludeShoppingCartPosition, true);
        foreach ($modulePositions as $modulePosition) {
            $amount += (float) $modulePosition->getPrice(false, 'gross')->getAmount();
        }

        $amountObj->setAmount($amount);

        $this->cacheHashes[$cacheKey] = $amountObj;
    
        return $amountObj;
    }

    /**
     * Returns the price of the cart positions.
     *
     * @param array $excludeModules              An array of registered modules that shall not
     *                                           be taken into account.
     * @param array $excludeShoppingCartPosition Positions that shall not be counted;
     *                                           can contain the ID or the className of the position
     * 
     * @return Money a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 15.12.2011
     */
    public function getTaxableAmountNetWithoutFeesAndCharges($excludeModules = array(), $excludeShoppingCartPosition = false) {
        if (!is_array($excludeModules)) {
            $excludeModules = array($excludeModules);
        }
        if (!is_array($excludeShoppingCartPosition)) {
            $excludeShoppingCartPosition = array($excludeShoppingCartPosition);
        }

        $cacheHash = md5(
            implode(',', $excludeModules).'_'.
            implode(',', $excludeShoppingCartPosition)
        );
        $cacheKey = 'getTaxableAmountNetWithoutFeesAndCharges_'.$cacheHash;

        if (array_key_exists($cacheKey, $this->cacheHashes)) {
            return $this->cacheHashes[$cacheKey];
        }

        $amountObj = new Money();
        $amount    = 0;

        $modulePositions = $this->getTaxableShoppingcartPositions($excludeModules, $excludeShoppingCartPosition, true);
        foreach ($modulePositions as $modulePosition) {
            $amount += (float) $modulePosition->getPrice(false, 'net')->getAmount();
        }

        $amountObj->setAmount($amount);

        $this->cacheHashes[$cacheKey] = $amountObj;
    
        return $amountObj;
    }

    /**
     * Returns the total amount of all taxes.
     *
     * @param boolean $excludeCharges Indicates wether to exlude charges and discounts
     *
     * @return Money a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.11.2012
     */
    public function getTaxTotal($excludeCharges = false) {
        $cacheKey = (int) $excludeCharges;
        if (!array_key_exists($cacheKey, $this->taxTotalList)) {
            $taxRates = $this->getTaxRatesWithFees();

            if (!$excludeCharges &&
                 $this->HasChargesAndDiscountsForTotal()) {

                foreach ($this->ChargesAndDiscountsForTotal() as $charge) {
                    if ($charge->SilvercartTax === false) {
                        continue;
                    }

                    $taxRate = $taxRates->find('Rate', $charge->SilvercartTax->Rate);

                    if ($taxRate) {
                        $amount = $charge->Price->getAmount();

                        if (SilvercartConfig::PriceType() == 'gross') {
                            $rateAmount = $amount - ($amount / (100 + $charge->SilvercartTax->Rate) * 100);
                        } else {
                            $rateAmount = ($amount / 100 * (100 + $charge->SilvercartTax->Rate)) - $amount;
                        }

                        $taxRate->AmountRaw += $rateAmount;

                        if (round($taxRate->AmountRaw, 2) === -0.00) {
                            $taxRate->AmountRaw = 0;
                        }

                        $taxRate->Amount->setAmount($taxRate->AmountRaw);
                    }
                }
            }

            $this->extend('updateTaxTotal', $taxRates);
            
            $this->taxTotalList[$cacheKey] = $taxRates;
        }

        return $this->taxTotalList[$cacheKey];
    }

    /**
     * Returns the non taxable amount of positions in the shopping cart.
     * Those can originate from registered modules only.
     *
     * @param array $excludeModules              An array of registered modules that shall not
     *                                           be taken into account.
     * @param array $excludeShoppingCartPosition Positions that shall not be counted
     *
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.02.2011
     */
    public function getNonTaxableAmount($excludeModules = array(), $excludeShoppingCartPosition = false) {
        $amount = 0;
        $registeredModules = $this->callMethodOnRegisteredModules(
            'ShoppingCartPositions',
            array(
                $this,
                Member::currentUser(),
                false,
                $excludeShoppingCartPosition
            ),
            $excludeModules,
            $excludeShoppingCartPosition
        );

        // Registered Modules
        foreach ($registeredModules as $moduleName => $modulePositions) {
            foreach ($modulePositions as $modulePosition) {
                $amount += (float) $modulePosition->PriceTotal;
            }
        }

        $amountObj = new Money;
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $amountObj;
    }

    /**
     * Returns the handling costs for the chosen payment method.
     *
     * @return Money
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 26.1.2011
     */
    public function HandlingCostPayment() {
        $paymentMethodObj = $this->getPaymentMethod();

        if ($paymentMethodObj) {
            $handlingCostPaymentObj = $paymentMethodObj->getHandlingCost();
        } else {
            $paymentDefaultCost = new Money();
            $paymentDefaultCost->setAmount(0);
            $paymentDefaultCost->setCurrency(SilvercartConfig::DefaultCurrency());

            $handlingCostPaymentObj = new SilvercartHandlingCost();
            $handlingCostPaymentObj->amount = $paymentDefaultCost;
        }

        if (SilvercartConfig::PriceType() == 'net') {
            $taxRate             = $this->getMostValuableTaxRate($this->getTaxRatesWithoutFeesAndCharges('SilvercartVoucher'));

            if ($handlingCostPaymentObj->getPriceAmount() > 0) {
                $handlingCostPayment = round(($handlingCostPaymentObj->getPriceAmount() / (100 + $taxRate->Rate) * 100), 2);
            } else {
                $handlingCostPayment = 0;
            }

            $handlingCostPaymentObj->setAmount($handlingCostPayment);
        }

        return $handlingCostPaymentObj->amount;
    }

    /**
     * Returns the handling costs for the chosen shipping method.
     *
     * @return Money
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 26.1.2011
     */
    public function HandlingCostShipment() {
        $handlingCostShipment = 0;
        $selectedShippingMethod = $this->getShippingMethod();

        if ($selectedShippingMethod) {
            $handlingCostShipmentObj = $selectedShippingMethod->getShippingFee()->getCalculatedPrice();
        } else {
            $handlingCostShipmentObj = new Money();
            $handlingCostShipmentObj->setAmount($handlingCostShipment);
            $handlingCostShipmentObj->setCurrency(SilvercartConfig::DefaultCurrency());
        }

        if (SilvercartConfig::PriceType() == 'net') {
            $taxRate              = $this->getMostValuableTaxRate($this->getTaxRatesWithoutFeesAndCharges('SilvercartVoucher'));
            $handlingCostShipment = round(($handlingCostShipmentObj->getAmount() / (100 + $taxRate->Rate) * 100), 2);

            $handlingCostShipmentObj->setAmount($handlingCostShipment);
        }

        return $handlingCostShipmentObj;
    }

    /**
     * Returns the shipping method title.
     *
     * @return string
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 26.1.2011
     */
    public function CarrierAndShippingMethodTitle() {
        $title = '';
        $selectedShippingMethod = $this->getShippingMethod();

        if ($selectedShippingMethod) {
            $title = $selectedShippingMethod->SilvercartCarrier()->Title . ' - ' . $selectedShippingMethod->Title;
        }

        return $title;
    }

    /**
     * Returns the payment method object.
     *
     * @return SilvercartPaymentMethod
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @deprecated
     */
    public function getPayment() {
        return $this->getPaymentMethod();
    }
    
    /**
     * Returns the shipping method
     *
     * @return SilvercartShippingMethod
     */
    public function getShippingMethod() {
        $shippingMethod = null;
        if (is_numeric($this->SilvercartShippingMethodID)) {
            $shippingMethod = DataObject::get_by_id('SilvercartShippingMethod', $this->SilvercartShippingMethodID);
        }
        return $shippingMethod;
    }
    
    /**
     * Returns the payment method
     *
     * @return SilvercartPaymentMethod
     */
    public function getPaymentMethod() {
        $paymentMethod = null;
        if (is_numeric($this->SilvercartPaymentMethodID)) {
            $paymentMethod = DataObject::get_by_id('SilvercartPaymentMethod', $this->SilvercartPaymentMethodID);
        }
        return $paymentMethod;
    }

    /**
     * Returns the minimum order value.
     *
     * @return mixed Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 09.06.2011
     */
    public function MinimumOrderValue() {
        $minimumOrderValue = new Money();

        if (SilvercartConfig::UseMinimumOrderValue() &&
            SilvercartConfig::MinimumOrderValue()) {

            $minimumOrderValue->setAmount(SilvercartConfig::MinimumOrderValue()->getAmount());
            $minimumOrderValue->setCurrency(SilvercartConfig::MinimumOrderValue()->getCurrency());
        }

        return $minimumOrderValue->Nice();
    }

    /**
     * Indicates wether the minimum order value is reached.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 09.06.2011
     */
    public function IsMinimumOrderValueReached() {
        if (SilvercartConfig::UseMinimumOrderValue() &&
            SilvercartConfig::MinimumOrderValue() &&
            SilvercartConfig::MinimumOrderValue()->getAmount() > $this->getAmountTotalWithoutFees()->getAmount()) {

            return false;
        }
        
        return true;
    }
    
    /**
     * In case stock management is enabled: Find out if all positions quantities
     * are still in stock
     * 
     * @return bool Can this cart be checkt out?
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.7.2011
     */
    public function isAvailableInStock() {
        $positions = $this->SilvercartShoppingCartPositions();
        if ($positions) {
            $isCheckoutable = true;
            foreach ($positions as $position) {
                if ($position->Quantity > $position->SilvercartProduct()->StockQuantity) {
                    $isCheckoutable = false;
                    break;
                }
            }
            return $isCheckoutable;
        } else {
            return false;
        }
    }

    /**
     * Returns the end sum of the cart (taxable positions + nontaxable
     * positions + fees).
     *
     * @param array   $excludeModules               An array of registered modules that shall not
     *                                              be taken into account.
     * @param array   $excludeShoppingCartPositions Positions that shall not be counted
     * @param boolean $excludeCharges               Indicates wether to exlude charges and discounts
     * 
     * @return Money a money object with the calculated amount and the default currency
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.09.2012
     */
    public function getAmountTotal($excludeModules = array(), $excludeShoppingCartPositions = false, $excludeCharges = false) {
        if (SilvercartConfig::PriceType() == 'gross') {
            $amountTotal = $this->getAmountTotalGross($excludeModules, $excludeShoppingCartPositions, $excludeCharges);
        } else {
            $amountTotal = $this->getAmountTotalNet($excludeModules, $excludeShoppingCartPositions, $excludeCharges);
        }
        if ($amountTotal->getAmount() <= 0) {
            $amountTotal->setAmount(0);
        }
        return $amountTotal;
    }

    /**
     * Returns the end sum of the cart (taxable positions + nontaxable
     * positions + fees).
     *
     * @param array   $excludeModules               An array of registered modules that shall not
     *                                              be taken into account.
     * @param array   $excludeShoppingCartPositions Positions that shall not be counted
     * @param boolean $excludeCharges               Indicates wether to exlude charges and discounts
     * 
     * @return Money a money object with the calculated amount and the default currency
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.02.2011
     */
    public function getAmountTotalGross($excludeModules = array(), $excludeShoppingCartPositions = false, $excludeCharges = false) {
        $amount  = $this->getTaxableAmountGrossWithFees($excludeShoppingCartPositions)->getAmount();
        $amount += $this->getNonTaxableAmount($excludeModules, $excludeShoppingCartPositions)->getAmount();
        
        // Handling costs for payment and shipment
        if (!$excludeCharges &&
             $this->HasChargesAndDiscountsForTotal()) {
            
            $amount += $this->ChargesAndDiscountsForTotal('gross')->Price->getAmount();
        }
        
        $amountObj = new Money;
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $amountObj;
    }

    /**
     * Returns the end sum of the cart (taxable positions + nontaxable
     * positions + fees) excluding vat.
     *
     * @param array   $excludeModules               An array of registered modules that shall not
     *                                              be taken into account.
     * @param array   $excludeShoppingCartPositions Positions that shall not be counted
     * @param boolean $excludeCharges               Indicates wether to exlude charges and discounts
     * 
     * @return Money a money object with the calculated amount and the default currency
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.02.2011
     */
    public function getAmountTotalNet($excludeModules = array(), $excludeShoppingCartPositions = false, $excludeCharges = false) {
        $amountObj = $this->getAmountTotalNetWithoutVat($excludeModules, $excludeShoppingCartPositions, $excludeCharges);
        $amount    = $amountObj->getAmount();

        foreach ($this->getTaxTotal($excludeCharges) as $tax) {
            $amount += $tax->Amount->getAmount();
        }

        $amountObj->setAmount($amount);

        return $amountObj;
    }

    /**
     * Returns the end sum of the cart (taxable positions + nontaxable
     * positions + fees) excluding vat.
     *
     * @param array   $excludeModules               An array of registered modules that shall not
     *                                              be taken into account.
     * @param array   $excludeShoppingCartPositions Positions that shall not be counted
     * @param boolean $excludeCharges               Indicates wether to exlude charges and discounts
     * 
     * @return string a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.02.2011
     */
    public function getAmountTotalNetWithoutVat($excludeModules = array(), $excludeShoppingCartPositions = false, $excludeCharges = false) {
        $amount  = $this->getTaxableAmountNetWithFees($excludeShoppingCartPositions)->getAmount();
        $amount += $this->getNonTaxableAmount($excludeModules, $excludeShoppingCartPositions)->getAmount();

        // Handling costs for payment and shipment
        if (!$excludeCharges &&
             $this->HasChargesAndDiscountsForTotal()) {
            
            $amount += $this->ChargesAndDiscountsForTotal('net')->Price->getAmount();
        }

        if (round($amount, 2) === 0.00) {
            $amount = round($amount, 2);
        }

        $amountObj = new Money;
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $amountObj;
    }

    /**
     * Returns the end sum of the cart (taxable positions + nontaxable
     * positions + fees) without any taxes.
     *
     * @param array   $excludeModules               An array of registered modules that shall not
     *                                              be taken into account.
     * @param array   $excludeShoppingCartPositions Positions that shall not be counted
     * @param boolean $excludeCharges               Indicates wether to exlude charges and discounts
     * 
     * @return string a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.02.2011
     */
    public function getAmountTotalWithoutTaxes($excludeModules = array(), $excludeShoppingCartPositions = false, $excludeCharges = false) {
        $amount  = $this->getTaxableAmountGrossWithFees($excludeShoppingCartPositions)->getAmount();
        $amount += $this->getNonTaxableAmount($excludeModules, $excludeShoppingCartPositions)->getAmount();
        
        // Handling costs for payment and shipment
        if (!$excludeCharges &&
             $this->ChargesAndDiscountsForTotal()) {
            
            $amount += $this->ChargesAndDiscountsForTotal()->Price->getAmount();
        }

        if (round($amount, 2) === 0.00) {
            $amount = round($amount, 2);
        }
        
        $amountObj = new Money;
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $amountObj;
    }
    
    /**
     * Returns the end sum of the cart without fees based on shop settings for net or gross price type
     * 
     * @param array   $excludeModules               An array of registered modules that shall not
     *                                              be taken into account.
     * @param array   $excludeShoppingCartPositions Positions that shall not be counted
     * @param boolean $excludeCharges               Indicates wether to exlude charges and discounts
     * 
     * @return Money money object with amount 
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 26.03.2012
     */
    public function getAmountTotalWithoutFees($excludeModules = array(), $excludeShoppingCartPositions = false, $excludeCharges = false) {
        if (SilvercartConfig::Pricetype() == 'gross') {
            $amountObj = $this->getAmountTotalGrossWithoutFees($excludeModules, $excludeShoppingCartPositions, $excludeCharges);                        
        } else {
            $amountObj = $this->getAmountTotalNetWithoutFees($excludeModules, $excludeShoppingCartPositions, $excludeCharges);
        }       
        return $amountObj;
    }

    /**
     * Returns the end sum of the cart without fees (taxable positions +
     * nontaxable positions).
     *
     * @param array   $excludeModules               An array of registered modules that shall not
     *                                              be taken into account.
     * @param array   $excludeShoppingCartPositions Positions that shall not be counted
     * @param boolean $excludeCharges               Indicates wether to exlude charges and discounts
     * 
     * @return string a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 12.05.2011
     */
    public function getAmountTotalGrossWithoutFees($excludeModules = array(), $excludeShoppingCartPositions = false, $excludeCharges = false) {
        $amount  = $this->getTaxableAmountGrossWithoutFees($excludeModules, $excludeShoppingCartPositions, $excludeCharges)->getAmount();
        $amount += $this->getNonTaxableAmount($excludeModules, $excludeShoppingCartPositions)->getAmount();

        if (round($amount, 2) === 0.00) {
            $amount = round($amount, 2);
        }       

        $amountObj = new Money;
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $amountObj;
    }

    /**
     * Returns the end sum of the cart without fees (taxable positions +
     * nontaxable positions).
     *
     * @param array   $excludeModules               An array of registered modules that shall not
     *                                              be taken into account.
     * @param array   $excludeShoppingCartPositions Positions that shall not be counted
     * @param boolean $excludeCharges               Indicates wether to exlude charges and discounts
     * 
     * @return string a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 12.05.2011
     */
    public function getAmountTotalNetWithoutFees($excludeModules = array(), $excludeShoppingCartPositions = false, $excludeCharges = false) {
        $amount  = $this->getTaxableAmountNetWithoutFees($excludeModules, $excludeShoppingCartPositions, $excludeCharges)->getAmount();
        $amount += $this->getNonTaxableAmount($excludeModules, $excludeShoppingCartPositions)->getAmount();

        if (round($amount, 2) === 0.00) {
            $amount = round($amount, 2);
        }

        $amountObj = new Money;
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $amountObj;
    }
    
    /**
     * Returns the tax rates for shipping and payment fees.
     * 
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.01.2012
     */
    public function getTaxRatesForFees() {
        $taxes          = new DataObjectSet;
        $taxAmount      = 0;
        $shippingMethod = $this->getShippingMethod();
        $paymentMethod  = $this->getPaymentMethod();

        if ($shippingMethod) {
            $shippingFee = $shippingMethod->getShippingFee();

            if ($shippingFee) {
                $taxAmount += $shippingFee->getTaxAmount();
            }
        }

        if ($paymentMethod) {
            $paymentFee = $paymentMethod->getHandlingCost();

            if ($paymentFee) {
                $taxAmount += $paymentFee->getTaxAmount();
            }
        }

        $taxRate = $this->getMostValuableTaxRate($this->getTaxRatesWithoutFeesAndCharges())->Rate;

        if (!$taxes->find('Rate', $taxRate)) {
            $taxes->push(
                new DataObject(
                    array(
                        'Rate'      => $taxRate,
                        'AmountRaw' => $taxAmount,
                    )
                )
            );
        }

        foreach ($taxes as $tax) {
            $taxObj = new Money;
            $taxObj->setAmount($tax->AmountRaw);
            $taxObj->setCurrency(SilvercartConfig::DefaultCurrency());

            $tax->Amount = $taxObj;
        }

        return $taxes;
    }

    /**
     * Returns tax amounts included in the shoppingcart separated by tax rates
     * with fee taxes.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.11.2012
     */
    public function getTaxRatesWithFees() {
        if (is_null($this->taxRatesWithFees)) {
            $taxes          = $this->getTaxRatesWithoutFees();
            $shippingMethod = $this->getShippingMethod();
            $paymentMethod  = $this->getPaymentMethod();

            if ($shippingMethod) {
                $shippingFee = $shippingMethod->getShippingFee();

                if ($shippingFee) {
                    if ($shippingFee->SilvercartTax()) {
                        $taxRate = $shippingFee->getTaxRate();

                        if ( $taxRate &&
                            !$taxes->find('Rate', $taxRate)) {

                            $taxes->push(
                                new DataObject(
                                    array(
                                        'Rate'      => $taxRate,
                                        'AmountRaw' => 0.0,
                                    )
                                )
                            );
                        }
                        $taxSection = $taxes->find('Rate', $taxRate);
                        $taxSection->AmountRaw += $shippingFee->getTaxAmount();
                    }
                }
            }

            if ($paymentMethod) {
                $paymentFee = $paymentMethod->getHandlingCost();

                if ($paymentFee) {
                    if ($paymentFee->SilvercartTax()) {
                        $taxRate = $paymentFee->SilvercartTax()->getTaxRate();

                        if ( $taxRate &&
                            !$taxes->find('Rate', $taxRate)) {

                            $taxes->push(
                                new DataObject(
                                    array(
                                        'Rate'      => $taxRate,
                                        'AmountRaw' => 0.0,
                                    )
                                )
                            );
                        }
                        $taxSection             = $taxes->find('Rate', $taxRate);
                        $taxSection->AmountRaw += $paymentFee->getTaxAmount();
                    }
                }
            }

            foreach ($taxes as $tax) {
                $taxObj = new Money;
                $taxObj->setAmount(round($tax->AmountRaw, 2));
                $taxObj->setCurrency(SilvercartConfig::DefaultCurrency());

                $tax->Amount = $taxObj;
            }
            $this->taxRatesWithFees = $taxes;
        }
        return $this->taxRatesWithFees;
    }
    
    /**
     * Returns tax amounts included in the shoppingcart separated by tax rates
     * without fee taxes.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 01.02.2011
     */
    public function getTaxRatesWithoutFees() {
        $taxes = $this->getTaxRatesWithoutFeesAndCharges();
        
        // Charges and disounts
        $chargesAndDiscounts = $this->ChargesAndDiscountsForProducts();
        
        if ($this->HasChargesAndDiscountsForProducts()) {
            $mostValuableTaxRate = $this->getMostValuableTaxRate($taxes);
            
            if ($mostValuableTaxRate) {
                $taxSection              = $taxes->find('Rate', $mostValuableTaxRate->Rate);
                $chargeAndDiscountAmount = $chargesAndDiscounts->Price->getAmount();

                if (SilvercartConfig::PriceType() == 'gross') {
                    $taxSection->AmountRaw += $chargeAndDiscountAmount - ($chargeAndDiscountAmount / (100 + $taxSection->Rate) * 100);
                } else {
                    $taxSection->AmountRaw += ($chargeAndDiscountAmount / 100 * (100 + $taxSection->Rate)) - $chargeAndDiscountAmount;
                }
            }
        }

        foreach ($taxes as $tax) {
            $taxObj = new Money;
            $taxObj->setAmount($tax->AmountRaw);
            $taxObj->setCurrency(SilvercartConfig::DefaultCurrency());

            $tax->Amount = $taxObj;
        }

        return $taxes;
    }
    
    /**
     * Returns tax amounts included in the shoppingcart separated by tax rates
     * without fee taxes.
     *
     * @param array $excludeModules              An array of registered modules that shall not
     *                                           be taken into account.
     * @param array $excludeShoppingCartPosition Positions that shall not be counted
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 01.02.2011
     */
    public function getTaxRatesWithoutFeesAndCharges($excludeModules = array(), $excludeShoppingCartPosition = false) {
        $positions          = $this->SilvercartShoppingCartPositions();
        $taxes              = new DataObjectSet;
        $registeredModules  = $this->callMethodOnRegisteredModules(
            'ShoppingCartPositions',
            array(
                Member::currentUser()->SilvercartShoppingCart(),
                Member::currentUser(),
                true
            ),
            $excludeModules,
            $excludeShoppingCartPosition
        );

        // products
        foreach ($positions as $position) {
            $taxRate = $position->SilvercartProduct()->getTaxRate();

            if (!$taxes->find('Rate', $taxRate)) {
                $taxes->push(
                    new DataObject(
                        array(
                            'Rate' => $taxRate,
                            'AmountRaw' => (float) 0.0,
                        )
                    )
                );
            }
            $taxSection = $taxes->find('Rate', $taxRate);
            $taxSection->AmountRaw += $position->getTaxAmount();
        }

        // Registered Modules
        foreach ($registeredModules as $moduleName => $moduleOutput) {
            foreach ($moduleOutput as $modulePosition) {
                $taxRate = $modulePosition->TaxRate;
                if (!$taxes->find('Rate', $taxRate)) {
                    $taxes->push(
                        new DataObject(
                            array(
                                'Rate' => $taxRate,
                                'AmountRaw' => (float) 0.0,
                            )
                        )
                    );
                }

                $taxSection = $taxes->find('Rate', $taxRate);
                $taxAmount = $modulePosition->TaxAmount;
                $taxSection->AmountRaw = round($taxSection->AmountRaw + $taxAmount, 4);
            }
        }

        foreach ($taxes as $tax) {
            $taxObj = new Money;
            $taxObj->setAmount($tax->AmountRaw);
            $taxObj->setCurrency(SilvercartConfig::DefaultCurrency());

            $tax->Amount = $taxObj;
        }

        return $taxes;
    }

    /**
     * Returns the SilvercartTax object with the highest tax value for the
     * given taxes.
     *
     * @param array $taxes The tax rates array (associative)
     *
     * @return SilvercartTax
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.12.2011
     */
    public function getMostValuableTaxRate($taxes) {
        $highestTaxValue        = 0;
        $mostValuableTaxRate    = null;

        foreach ($taxes as $tax) {
            if ($tax->AmountRaw > $highestTaxValue) {
                $mostValuableTaxRate = $tax->Rate;
            }
        }

        if ($mostValuableTaxRate) {
            $silvercartTax = DataObject::get_one(
                'SilvercartTax',
                sprintf(
                    "Rate = %f",
                    $mostValuableTaxRate
                )
            );
            
            if ($silvercartTax) {
                return $silvercartTax;
            }
        }

        return false;
    }

    /**
     * calculate the carts total weight
     * needed to determin the ShippingFee
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 7.11.2010
     * @return integer|boolean the cart´s weight in gramm
     */
    public function getWeightTotal() {
        $positions = $this->SilvercartShoppingCartPositions();
        $totalWeight = (int) 0;
        if ($positions) {
            foreach ($positions as $position) {
                $totalWeight += $position->SilvercartProduct()->Weight * $position->Quantity;
            }
            return $totalWeight;
        } else {
            return false;
        }
    }

    /**
     * Indicates wether the fees for shipping and payment should be shown.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.02.2011
     */
    public function getShowFees() {
        $showFees = false;

        if ($this->SilvercartShippingMethodID > 0 &&
            $this->SilvercartPaymentMethodID > 0) {

            $showFees = true;
        }

        return $showFees;
    }

    /**
     * Indicates wether the fees for shipping and payment should be shown.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.02.2011
     */
    public function getHasFeesOrChargesOrModules() {
        $hasAnything       = false;
        $registeredModules = $this->registeredModules();

        if ($this->getShowFees() ||
            $this->HasChargesAndDiscountsForProducts() ||
            $this->HasChargesAndDiscountsForTotal() ||
            $registeredModules->NonTaxableShoppingCartPositions) {

            $hasAnything = true;
        }

        return $hasAnything;
    }

    /**
     * deletes all shopping cart positions when cart is deleted
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.10.2010
     * @return void
     */
    public function onBeforeDelete() {
        parent::onBeforeDelete();

        $filter = sprintf("`SilvercartShoppingCartID` = '%s'", $this->ID);
        $shoppingCartPositions = DataObject::get('SilvercartShoppingCartPosition', $filter);

        if ($shoppingCartPositions) {
            foreach ($shoppingCartPositions as $obj) {
                $obj->delete();
            }
        }
    }

    /**
     * Register a module.
     * Registered modules will be called when the shoppingcart is displayed.
     *
     * @param string $module The module class name
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 21.01.2011
     */
    public static function registerModule($module) {
        array_push(
                self::$registeredModules, $module
        );
    }

    /**
     * Returns all registered modules.
     *
     * Every module contains two keys for further iteration inside templates:
     *      - ShoppingCartPositions
     *      - ShoppingCartActions
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 21.01.2011
     */
    public function registeredModules() {
        $customer = Member::currentUser();
        $modules = array();
        $registeredModules = self::$registeredModules;
        $hookMethods = array(
            'NonTaxableShoppingCartPositions',
            'TaxableShoppingCartPositions',
            'ShoppingCartActions',
            'ShoppingCartTotal',
        );

        foreach ($registeredModules as $registeredModule) {
            $registeredModuleObjPlain = new $registeredModule();

            if ($registeredModuleObjPlain->hasMethod('loadObjectForShoppingCart')) {
                $registeredModuleObj = $registeredModuleObjPlain->loadObjectForShoppingCart($this);
            }

            if (!$registeredModuleObj) {
                $registeredModuleObj = $registeredModuleObjPlain;
            }

            if ($registeredModuleObj) {
                foreach ($hookMethods as $hookMethod) {
                    if ($registeredModuleObj->hasMethod($hookMethod)) {
                        $modules[] = array(
                            $hookMethod => $registeredModuleObj->$hookMethod($this, $customer)
                        );
                    }
                }
            }
        }

        return new DataObjectSet($modules);
    }

    /**
     * Calls a method on all registered modules and returns its output.
     *
     * @param string $methodName                   The name of the method to call
     * @param array  $parameters                   Additional parameters for the method call
     * @param array  $excludeModules               An array of registered modules that shall not
     *                                             be taken into account.
     * @param array  $excludeShoppingCartPositions Positions that shall not be counted; can contain the ID or the className of the position
     *
     * @return array Associative array:
     *      'ModuleName' => DataObjectSet (ModulePositions)
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 24.01.2011
     */
    public function callMethodOnRegisteredModules($methodName, $parameters = array(), $excludeModules = array(), $excludeShoppingCartPositions = false) {
        $registeredModules = self::$registeredModules;
        $outputOfModules = array();
        
        if (!is_array($excludeModules)) {
            $excludeModules = array($excludeModules);
        }
        
        foreach ($registeredModules as $registeredModule) {

            // Skip excluded modules
            if (in_array($registeredModule, $excludeModules)) {
                continue;
            }

            $registeredModuleObjPlain = new $registeredModule();

            if ($registeredModuleObjPlain->hasMethod('loadObjectForShoppingCart')) {
                $registeredModuleObj = $registeredModuleObjPlain->loadObjectForShoppingCart($this);
            } else {
                $registeredModuleObj = $registeredModuleObjPlain;
            }

            if ($registeredModuleObj) {
                if ($registeredModuleObj->hasMethod($methodName)) {

                    if (!is_array($parameters)) {
                        $parameters = array($parameters);
                    }

                    $parameters['excludeShoppingCartPositions'] = $excludeShoppingCartPositions;

                    $outputOfModules[$registeredModule] = call_user_func_array(
                        array(
                            $registeredModuleObj,
                            $methodName
                        ), $parameters
                    );
                }
            }
        }

        return $outputOfModules;
    }

    /**
     * Set the ID of the shipping method the customer has chosen.
     *
     * @param Int $shippingMethodId The ID of the shipping method object.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.02.2011
     */
    public function setShippingMethodID($shippingMethodId) {
        $this->SilvercartShippingMethodID = $shippingMethodId;
    }

    /**
     * Set the ID of the payment method the customer has chosen.
     *
     * @param Int $paymentMethodId The ID of the payment method object.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.02.2011
     */
    public function setPaymentMethodID($paymentMethodId) {
        $this->SilvercartPaymentMethodID = $paymentMethodId;
    }

    /**
     * determine wether a cart is filled or empty; useful for template conditional
     *
     * @return bool
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 17.02.2011
     */
    public function isFilled() {
        $records = DB::query(
            sprintf(
                "
                SELECT
                    COUNT(Pos.ID) AS NumberOfPositions
                FROM
                    SilvercartShoppingCartPosition Pos
                WHERE
                    Pos.SilvercartShoppingCartID = %d
                ",
                $this->ID
            )
        );

        $record = $records->nextRecord();

        if ($record &&
            $record['NumberOfPositions'] > 0) {

            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Decrement all position quantities is they are larger than the related
     * products stock quantities.
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.7.2011
     */
    public function adjustPositionQuantitiesToStockQuantities() {
        $positions = $this->SilvercartShoppingCartPositions();
        if ($positions) {
            foreach ($positions as $position) {
                $position->adjustQuantityToStockQuantity();
            }
        }
    }
    
    /**
     * Reset all message tokens of the related cart positions.
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.7.2011
     */
    public function resetPositionMessages() {
        $positions = $this->SilvercartShoppingCartPositions();
        if ($positions) {
            foreach ($positions as $position) {
                $position->resetMessageTokens();
            }
        }
    }
    
}
