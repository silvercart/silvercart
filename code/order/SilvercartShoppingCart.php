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
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 21.01.2011
     */
    public static $registeredModules = array();
    /**
     * Singular-Beschreibung zur Darstellung im Backend.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $singular_name = "cart";
    /**
     * Plural-Beschreibung zur Darstellung im Backend.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $plural_name = "carts";
    /**
     * 1:n relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $has_many = array(
        'SilvercartShoppingCartPositions' => 'SilvercartShoppingCartPosition'
    );
    /**
     * defines n:m relations
     *
     * @var array configure relations
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.12.10
     */
    public static $many_many = array(
        'SilvercartProducts' => 'SilvercartProduct'
    );
    /**
     * Indicates wether the registered modules should be loaded.
     *
     * @var boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.04.2011
     */
    public static $loadModules = true;
    /**
     * Indicates wether the registered modules should be loaded.
     *
     * @var boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.04.2011
     */
    public static $createForms = true;
    /**
     * Contains the ID of the payment method the customer has chosen.
     *
     * @var Int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.02.2011
     */
    protected $paymentMethodID;
    /**
     * Contains the ID of the shipping method the customer has chosen.
     *
     * @var Int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.02.2011
     */
    protected $shippingMethodID;

    /**
     * default constructor
     *
     * @param array $record      array of field values
     * @param bool  $isSingleton true if this is a singleton() object
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        parent::__construct($record, $isSingleton);
        if (array_key_exists('url', $_REQUEST)) {
            if (stripos($_REQUEST['url'], '/dev/build') !== false) {
                return;
            }
        }

        // Initialize shopping cart position object, so that it can inject
        // its forms into the controller.
        if (!self::$loadModules) {
            SilvercartShoppingCartPosition::setCreateForms(false);
        }
        $this->SilvercartShoppingCartPositions();

        $this->SilvercartShippingMethodID = 0;
        $this->SilvercartPaymentMethodID = 0;

        // Check if unit test are performed: The call to Member:currentUserID()
        // would fail
        //  
        // Check if the installation is complete. If it's not complete we
        // can't call the method "Member::currentUser()", since it tries to
        // get the decorated fields from SilvercartCustomerRole that are not
        // yet created in the database
        if (!SapphireTest::is_running_test() && 
            SilvercartConfig::isInstallationCompleted() &&
            Member::currentUserID() &&
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
        $error = true;
        if ($formData['productID'] && $formData['productQuantity']) {
            $member = Member::currentUser();
            if ($member == false) {
                $member = new SilvercartAnonymousCustomer();
                $member->write();
                // Add customer to intermediate group
                $customerGroup = DataObject::get_one(
                                'Group', "`Code` = 'anonymous'"
                );
                if ($customerGroup) {
                    $member->Groups()->add($customerGroup);
                }
                $member->logIn(true);
            }

            if ($member) {
                $cart = $member->getCart();
                if ($cart) {
                    $product = DataObject::get_by_id('SilvercartProduct', $formData['productID'], 'Created');
                    if ($product) {
                        $quantity = (int) $formData['productQuantity'];
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
     * @param array $excludeShoppingCartPositions Positions that shall not be counted
     *
     * @return string a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.02.2011
     */
    public function getTaxableAmountGrossWithFees($excludeShoppingCartPositions = false) {
        $member = Member::currentUser();
        $shippingMethod = DataObject::get_by_id('SilvercartShippingMethod', $this->SilvercartShippingMethodID);
        $paymentMethod = DataObject::get_by_id('SilvercartPaymentMethod', $this->SilvercartPaymentMethodID);
        $amountTotal = $this->getTaxableAmountGrossWithoutFees(null, $excludeShoppingCartPositions)->getAmount();

        if ($shippingMethod) {
            $shippingFee = $shippingMethod->getShippingFee();

            if ($shippingFee) {
                $shippingFeeAmount = $shippingFee->Price->getAmount();
                $amountTotal = $shippingFeeAmount + $amountTotal;
            }
        }

        if ($paymentMethod) {
            $paymentFee = $paymentMethod->SilvercartHandlingCost();

            if ($paymentFee) {
                $paymentFeeAmount = $paymentFee->amount->getAmount();
                $amountTotal = $paymentFeeAmount + $amountTotal;
            }
        }

        $amountTotalObj = new Money;
        $amountTotalObj->setAmount($amountTotal);
        $amountTotalObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $amountTotalObj;
    }
    
    /**
     * Returns the price of the cart positions, including taxes.
     *
     * @param array $excludeModules              An array of registered modules that shall not
     *                                           be taken into account.
     * @param array $excludeShoppingCartPosition Positions that shall not be counted
     *
     * @return Money a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.02.2011
     */
    public function getTaxableAmountGrossWithoutFees($excludeModules = array(), $excludeShoppingCartPosition = false) {
        $amount = 0;

        $registeredModules = $this->callMethodOnRegisteredModules(
                        'ShoppingCartPositions', array(
                    $this,
                    Member::currentUser(),
                    true,
                    $excludeShoppingCartPosition,
                    false
                        ), $excludeModules
        );

        // products
        foreach ($this->SilvercartShoppingCartPositions() as $position) {
            $amount += (float) $position->SilvercartProduct()->Price->getAmount() * $position->Quantity;
        }

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
     * Returns the total amount of all taxes.
     *
     * @return Money a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.04.2011
     */
    public function getTaxTotal() {
        $taxTotal = 0;
        $taxRates = $this->getTaxRatesWithoutFees();
        
        foreach ($taxRates as $taxRate) {
            $taxTotal += $taxRate->AmountRaw;
        }
        
        $taxTotalObj = new Money;
        $taxTotalObj->setAmount($taxTotal);
        $taxTotalObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $taxTotalObj;
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
                        'ShoppingCartPositions', array(
                    $this,
                    Member::currentUser(),
                    false,
                    $excludeShoppingCartPosition
                        ), $excludeModules
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
        $handlingCostPayment = 0;
        $paymentMethodObj = DataObject::get_by_id(
                        'SilvercartPaymentMethod', $this->SilvercartPaymentMethodID
        );

        if ($paymentMethodObj) {
            $handlingCostPaymentObj = $paymentMethodObj->getHandlingCost();
        } else {
            $handlingCostPaymentObj = new Money();
            $handlingCostPaymentObj->setAmount(0);
            $handlingCostPaymentObj->setCurrency(SilvercartConfig::DefaultCurrency());
        }

        return $handlingCostPaymentObj;
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
        $selectedShippingMethod = DataObject::get_by_id(
                        'SilvercartShippingMethod', $this->SilvercartShippingMethodID
        );

        if ($selectedShippingMethod) {
            $handlingCostShipmentObj = $selectedShippingMethod->getShippingFee()->Price;
        } else {
            $handlingCostShipmentObj = new Money();
            $handlingCostShipmentObj->setAmount($handlingCostShipment);
            $handlingCostShipmentObj->setCurrency(SilvercartConfig::DefaultCurrency());
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
        $selectedShippingMethod = DataObject::get_by_id(
                        'SilvercartShippingMethod', $this->SilvercartShippingMethodID
        );

        if ($selectedShippingMethod) {
            $title = $selectedShippingMethod->SilvercartCarrier()->Title . "-" . $selectedShippingMethod->Title;
        }

        return $title;
    }

    /**
     * Returns the payment method object.
     *
     * @return PaymentMethod
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 26.1.2011
     */
    public function getPayment() {
        $paymentMethodObj = DataObject::get_by_id(
                        'SilvercartPaymentMethod', $this->SilvercartPaymentMethodID
        );

        return $paymentMethodObj;
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
     * @param array $excludeModules               An array of registered modules that shall not
     *                                            be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted
     * 
     * @return string a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.02.2011
     */
    public function getAmountTotal($excludeModules = array(), $excludeShoppingCartPositions = false) {
        $amount  = $this->getTaxableAmountGrossWithFees($excludeShoppingCartPositions)->getAmount();
        $amount += $this->getNonTaxableAmount($excludeModules, $excludeShoppingCartPositions)->getAmount();

        $amountObj = new Money;
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $amountObj;
    }

    /**
     * Returns the end sum of the cart without fees (taxable positions +
     * nontaxable positions).
     *
     * @param array $excludeModules               An array of registered modules that shall not
     *                                            be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted
     * 
     * @return string a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 12.05.2011
     */
    public function getAmountTotalWithoutFees($excludeModules = array(), $excludeShoppingCartPositions = false) {
        $amount  = $this->getTaxableAmountGrossWithoutFees($excludeShoppingCartPositions)->getAmount();
        $amount += $this->getNonTaxableAmount($excludeModules, $excludeShoppingCartPositions)->getAmount();

        $amountObj = new Money;
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $amountObj;
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
        $positions = $this->SilvercartShoppingCartPositions();
        $taxes = new DataObjectSet;
        $registeredModules = $this->callMethodOnRegisteredModules(
                        'ShoppingCartPositions', array(
                    Member::currentUser()->SilvercartShoppingCart(),
                    Member::currentUser(),
                    true
                        )
        );

        // products
        foreach ($positions as $position) {
            $taxRate = $position->SilvercartProduct()->getTaxRate();

            if (!$taxes->find('Rate', $taxRate)) {
                $taxes->push(
                        new DataObject(
                                array(
                                    'Rate' => $taxRate,
                                    'AmountRaw' => 0.0,
                                )
                        )
                );
            }
            $taxSection = $taxes->find('Rate', $taxRate);
            $taxSection->AmountRaw += $position->SilvercartProduct()->getTaxAmount() * $position->Quantity;
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
                                        'AmountRaw' => 0.0,
                                    )
                            )
                    );
                }
                $taxSection = $taxes->find('Rate', $taxRate);
                $taxSection->AmountRaw += $modulePosition->TaxAmount;
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
     * with fee taxes.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.02.2011
     */
    public function getTaxRatesWithFees() {
        $taxes = $this->getTaxRatesWithoutFees();
        $shippingMethod = DataObject::get_by_id('SilvercartShippingMethod', $this->SilvercartShippingMethodID);
        $paymentMethod = DataObject::get_by_id('SilvercartPaymentMethod', $this->SilvercartPaymentMethodID);

        if ($shippingMethod) {
            $shippingFee = $shippingMethod->getShippingFee();

            if ($shippingFee) {
                if ($shippingFee->SilvercartTax()) {
                    $taxRate = $shippingFee->SilvercartTax()->getTaxRate();

                    if ($taxRate &&
                            !$taxes->find('Rate', $taxRate)) {

                        $taxes->push(
                                new DataObject(
                                        array(
                                            'Rate' => $taxRate,
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
            $paymentFee = $paymentMethod->SilvercartHandlingCost();

            if ($paymentFee) {
                if ($paymentFee->SilvercartTax()) {
                    $taxRate = $paymentFee->SilvercartTax()->getTaxRate();

                    if ($taxRate &&
                            !$taxes->find('Rate', $taxRate)) {

                        $taxes->push(
                                new DataObject(
                                        array(
                                            'Rate' => $taxRate,
                                            'AmountRaw' => 0.0,
                                        )
                                )
                        );
                    }
                    $taxSection = $taxes->find('Rate', $taxRate);
                    $taxSection->AmountRaw += $paymentFee->getTaxAmount();
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
     * @param array  $excludeShoppingCartPositions Positions that shall not be counted
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
     * determin weather a cart is filled or empty; usefull for template conditional
     *
     * @return bool
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 17.02.2011
     */
    public function isFilled() {
        if ($this->SilvercartShoppingCartPositions()->Count() > 0) {
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
        if (SilvercartConfig::isEnabledStockManagement() && !SilvercartConfig::isStockManagementOverbookable()) {
            $positions = $this->SilvercartShoppingCartPositions();
            if ($positions) {
                foreach ($positions as $position) {
                    $position->adjustQuantityToStockQuantity();
                }
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
