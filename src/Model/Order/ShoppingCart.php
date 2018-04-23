<?php

namespace SilverCart\Model\Order;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\ShoppingCartPosition;
use SilverCart\Model\Payment\HandlingCost;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Product\Tax;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Shipment\ShippingMethod;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBMoney;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

/**
 * abstract for shopping cart.
 *
 * @package SilverCart
 * @subpackage Model_Order
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ShoppingCart extends DataObject {

    /**
     * Contains all registered modules that get called when the shoppingcart
     * is displayed.
     *
     * @var array
     */
    public static $registeredModules = array();

    /**
     * 1:n relations
     *
     * @var array
     */
    private static $has_many = array(
        'ShoppingCartPositions' => ShoppingCartPosition::class,
    );
    
    /**
     * defines n:m relations
     *
     * @var array configure relations
     */
    private static $many_many = array(
        'Products' => Product::class,
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartShoppingCart';

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
     * @var ArrayList
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
     * Set of registered modules.
     *
     * @var ArrayList
     */
    protected $registeredModulesSet = null;

    /**
     * default constructor
     *
     * @param array $record      array of field values
     * @param bool  $isSingleton true if this is a singleton() object
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function __construct($record = null, $isSingleton = false) {
        parent::__construct($record, $isSingleton);
        if ($this->ID > 0) {
            if (!Tools::isIsolatedEnvironment() &&
                !Tools::isBackendEnvironment()) {

                if (!self::$cartCleaningFinished && 
                    !self::$cartCleaningInProgress) {
                    self::$cartCleaningInProgress = true;

                    $this->cleanUp();
                }

                $this->ShippingMethodID = 0;
                $this->PaymentMethodID = 0;

                $currentUser = Security::getCurrentUser();
                if ($currentUser instanceof Member &&
                    self::$loadModules) {

                    $this->callMethodOnRegisteredModules(
                        'performShoppingCartConditionsCheck',
                        array(
                            $this,
                            $currentUser
                        )
                    );

                    $this->callMethodOnRegisteredModules(
                        'ShoppingCartInit',
                        array($this)
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
        $positionTable = Tools::get_table_name(ShoppingCartPosition::class);
        $positionIds   = DB::query(
            sprintf(
                "SELECT ID FROM %s WHERE
                    %s.ShoppingCartID = %d AND
                    ProductID = 0",
                $positionTable,
                $positionTable,
                $this->ID
            )
        );

        if ($positionIds) {
            foreach ($positionIds as $positionId) {
                $position = ShoppingCartPosition::get()->byID($positionId);
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
                'Products'              => Product::singleton()->plural_name(),
                'ShoppingCartPositions' => ShoppingCartPosition::singleton()->plural_name(),
            )
        );
        $this->extend('updateFieldLabels', $fieldLabels);

        return $fieldLabels;
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.07.2013
     */
    public function ChargesAndDiscountsForProducts($priceType = false) {
        $cacheHash = md5($priceType);
        $cacheKey = 'ChargesAndDiscountsForProducts_'.$cacheHash;

        if (array_key_exists($cacheKey, $this->cacheHashes)) {
            return $this->cacheHashes[$cacheKey];
        }

        $paymentMethod = $this->getPaymentMethod();

        if ($paymentMethod) {
            $handlingCostPayment = $paymentMethod->getChargesAndDiscountsForProducts($this, $priceType);
            
            if ($handlingCostPayment === false) {
                return false;
            } else {
                $tax = $this->getMostValuableTaxRate();

                $chargesAndDiscounts = new DataObject(
                    array(
                        'Name'                          => $paymentMethod->sumModificationLabel,
                        'sumModificationImpact'         => $paymentMethod->sumModificationImpact,
                        'sumModificationProductNumber'  => $paymentMethod->sumModificationProductNumber,
                        'PriceFormatted'                => $handlingCostPayment->Nice(),
                        'Price'                         => $handlingCostPayment,
                        'Tax'                           => $tax,
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
     * @return DataObject
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.07.2013
     */
    public function ChargesAndDiscountsForTotal($priceType = false) {
        $cacheHash = md5($priceType);
        $cacheKey = 'ChargesAndDiscountsForTotal_'.$cacheHash;

        if (array_key_exists($cacheKey, $this->cacheHashes)) {
            return $this->cacheHashes[$cacheKey];
        }

        $paymentMethod = $this->getPaymentMethod();

        if ($paymentMethod instanceof PaymentMethod &&
            $paymentMethod->exists()) {
            $handlingCostPayment = $paymentMethod->getChargesAndDiscountsForTotal($this, $priceType);
            
            if ($handlingCostPayment === false) {
                return false;
            } else {
                $tax   = $this->getMostValuableTaxRate($this->getTaxRatesWithFees());
                $handlingCostPaymentRounded = $handlingCostPayment;
                $handlingCostPaymentRounded->setAmount(
                    round($handlingCostPayment->getAmount(), 2)
                );
                $chargesAndDiscounts = new DataObject(
                    array(
                        'Name'                          => $paymentMethod->sumModificationLabel,
                        'sumModificationImpact'         => $paymentMethod->sumModificationImpact,
                        'sumModificationProductNumber'  => $paymentMethod->sumModificationProductNumber,
                        'PriceFormatted'                => $handlingCostPayment->Nice(),
                        'Price'                         => $handlingCostPayment,
                        'Tax'                           => $tax,
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.11.2014
     */
    public static function addProduct($formData) {
        $error  = true;
        $member = Customer::currentUser();
        
        if (!$member) {
            $member = Customer::createAnonymousCustomer();
        }

        $overwriteAddProduct = false;
        $member->getCart()->extend('overwriteAddProduct', $overwriteAddProduct, $formData);
        
        if ($overwriteAddProduct) {
            $error = false;
        } elseif ($formData['productID'] && $formData['productQuantity']) {
            $cart = $member->getCart();
            if ($cart instanceof ShoppingCart &&
                $cart->exists()) {
                $product = Product::get()->byID($formData['productID']);
                if ($product instanceof Product &&
                    $product->exists()) {
                    $quantity = (float) str_replace(',', '.', $formData['productQuantity']);

                    if ($quantity > 0) {
                        $product->addToCart($cart->ID, $quantity);
                        $error = false;
                    }
                }
            }
        }

        return !$error;
    }

    /**
     * Removes a product out of the cart.
     *
     * @param array $data Data to use to identify the position.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public static function removeProduct($data) {
        $error  = true;
        $member = Customer::currentUser();
        
        if (!$member) {
            $member = Customer::createAnonymousCustomer();
        }
        
        $overwriteRemoveProduct = false;
        $member->getCart()->extend('overwriteRemoveProduct', $overwriteRemoveProduct, $data);
        
        if ($overwriteRemoveProduct) {
            $error = false;
        } elseif ($member instanceof Member) {
            $cart       = $member->getCart();
            $position   = $cart->ShoppingCartPositions()->filter('ProductID', $data['productID'])->first();
            if ($position instanceof ShoppingCartPosition) {
                $position->delete();
                $error = false;
            }
        }

        return !$error;
    }


    /**
     * Returns one or more plugged in rows for the shopping carts editable table
     * as a ArrayList
     * 
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.04.2018
     */
    public function addToEditableShoppingCartTable() {
        $addToCartTable = new ArrayList();
        $this->extend('addToEditableShoppingCartTable', $addToCartTable);
        return $addToCartTable;
    }

    /**
     * empties cart
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.11.2010
     */
    public function delete() {
        $positions = $this->ShoppingCartPositions();

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
        $positions = $this->ShoppingCartPositions();
        $quantity = 0;

        foreach ($positions as $position) {
            if ($productId === null ||
                    $position->Product()->ID === $productId) {

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
        if (Config::PriceType() == 'gross') {
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
     * @return DBMoney
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
        
        $amountTotalObj = new DBMoney;
        $amountTotalObj->setAmount($amountTotal);
        $amountTotalObj->setCurrency(Config::DefaultCurrency());

        return $amountTotalObj;
    }

    /**
     * Returns the NET price of the cart positions + fees, including taxes.
     *
     * @param array   $excludeShoppingCartPositions Positions that shall not be counted;
     *                                              can contain the ID or the className of the position
     * @param boolean $excludeCharges               Indicates wether charges and discounts should be calculated
     *
     * @return DBMoney
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

        $amountTotalObj = new DBMoney;
        $amountTotalObj->setAmount($amountTotal);
        $amountTotalObj->setCurrency(Config::DefaultCurrency());

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
     * @return DBMoney a price amount
     */
    public function getTaxableAmountWithoutFees($excludeModules = array(), $excludeShoppingCartPosition = false, $excludeCharges = false) {
        if (Config::PriceType() == 'gross') {
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
     * @return DBMoney
     */
    public function getTaxableAmountGrossWithoutFees($excludeModules = array(), $excludeShoppingCartPosition = false, $excludeCharges = false) {
        $amount = $this->getTaxableAmountGrossWithoutFeesAndCharges($excludeModules, $excludeShoppingCartPosition)->getAmount();
        
        // Handling costs for payment and shipment
        if (!$excludeCharges &&
             $this->ChargesAndDiscountsForProducts()) {
            
            $amount += $this->ChargesAndDiscountsForProducts()->Price->getAmount();
        }
        
        $amountObj = new DBMoney;
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(Config::DefaultCurrency());

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
     * @return DBMoney
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

        $amountObj = new DBMoney;
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(Config::DefaultCurrency());

        return $amountObj;
    }
    
    /**
     * Returns the price of the cart positions without modules.
     *
     * The price type is automatically determined by the ShoppinCartPosition.
     *
     * @return DBMoney
     */
    public function getTaxableAmountWithoutModules() {
        if (Config::PriceType() == 'gross') {
            $taxableAmountWithoutModules = $this->getTaxableAmountGrossWithoutModules();
        } else {
            $taxableAmountWithoutModules = $this->getTaxableAmountNetWithoutModules();
        }
        return $taxableAmountWithoutModules;
    }
    
    /**
     * Returns the GROSS price of the cart positions without modules.
     *
     * The price type is automatically determined by the ShoppinCartPosition.
     *
     * @return DBMoney
     */
    public function getTaxableAmountGrossWithoutModules() {
        $amountObj = new DBMoney();
        $amount    = 0;

        $modulePositions = $this->getTaxableShoppingcartPositions(array(), array(), false);
        foreach ($modulePositions as $modulePosition) {
            $amount += (float) $modulePosition->getPrice(false, 'gross')->getAmount();
        }

        $amountObj->setAmount($amount);
        $amountObj->setCurrency(Config::DefaultCurrency());

        return $amountObj;
    }
    
    /**
     * Returns the NET price of the cart positions without modules.
     *
     * The price type is automatically determined by the ShoppinCartPosition.
     *
     * @return DBMoney
     */
    public function getTaxableAmountNetWithoutModules() {
        $amountObj = new DBMoney();
        $amount    = 0;

        $modulePositions = $this->getTaxableShoppingcartPositions(array(), array(), false);
        foreach ($modulePositions as $modulePosition) {
            $amount += (float) $modulePosition->getPrice(false, 'net')->getAmount();
        }

        $amountObj->setAmount($amount);
        $amountObj->setCurrency(Config::DefaultCurrency());

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
     * @return ArrayList
     */
    public function getTaxableShoppingcartPositions($excludeModules = array(), $excludeShoppingCartPosition = false, $includeModules = true) {
        $cartPositions = new ArrayList();

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

        foreach ($this->ShoppingCartPositions() as $position) {
            $cartPositions->push($position);
        }

        if ($includeModules) {
            $registeredModules = $this->callMethodOnRegisteredModules(
                'ShoppingCartPositions',
                array(
                    $this,
                    Customer::currentUser(),
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
     * @return DBMoney
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

        $amountObj = new DBMoney();
        $amountObj->setCurrency(Config::DefaultCurrency());
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
     * Returns the price of the cart positions, including taxes.
     *
     * @param array $excludeShoppingCartPosition Positions that shall not be counted;
     *                                           can contain the ID or the className of the position
     * 
     * @return DBMoney
     */
    public function getTaxableAmountGrossWithoutFeesAndChargesAndModules($excludeShoppingCartPosition = false) {
        $excludeModules = self::$registeredModules;
        return $this->getTaxableAmountGrossWithoutFeesAndCharges($excludeModules, $excludeShoppingCartPosition);
    }

    /**
     * Returns the price of the cart positions.
     *
     * @param array $excludeModules              An array of registered modules that shall not
     *                                           be taken into account.
     * @param array $excludeShoppingCartPosition Positions that shall not be counted;
     *                                           can contain the ID or the className of the position
     * 
     * @return DBMoney
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

        $amountObj = new DBMoney();
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
     * Returns the price of the cart positions.
     *
     * @param array $excludeShoppingCartPosition Positions that shall not be counted;
     *                                           can contain the ID or the className of the position
     * 
     * @return DBMoney
     */
    public function getTaxableAmountNetWithoutFeesAndChargesAndModules($excludeShoppingCartPosition = false) {
        $excludeModules = self::$registeredModules;
        return $this->getTaxableAmountNetWithoutFeesAndCharges($excludeModules, $excludeShoppingCartPosition);
    }

    /**
     * Returns the total amount of all taxes.
     *
     * @param boolean $excludeCharges Indicates wether to exlude charges and discounts
     *
     * @return DBMoney
     */
    public function getTaxTotal($excludeCharges = false) {
        $cacheKey = (int) $excludeCharges;
        if (!array_key_exists($cacheKey, $this->taxTotalList)) {
            $taxRates = $this->getTaxRatesWithFees();

            if (!$excludeCharges &&
                 $this->HasChargesAndDiscountsForTotal()) {

                foreach ($this->ChargesAndDiscountsForTotal() as $charge) {
                    if ($charge->Tax === false) {
                        continue;
                    }

                    $taxRate = $taxRates->filter('Rate', $charge->Tax->Rate)->first();

                    if ($taxRate instanceof DataObject) {
                        $amount = $charge->Price->getAmount();

                        if (Config::PriceType() == 'gross') {
                            $rateAmount = $amount - ($amount / (100 + $charge->Tax->Rate) * 100);
                        } else {
                            $rateAmount = ($amount / 100 * (100 + $charge->Tax->Rate)) - $amount;
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
     * @return DBMoney
     */
    public function getNonTaxableAmount($excludeModules = array(), $excludeShoppingCartPosition = false) {
        $amount = 0;
        $registeredModules = $this->callMethodOnRegisteredModules(
            'ShoppingCartPositions',
            array(
                $this,
                Customer::currentUser(),
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

        $amountObj = new DBMoney;
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(Config::DefaultCurrency());

        return $amountObj;
    }

    /**
     * Returns the handling costs for the chosen payment method.
     *
     * @return DBMoney
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.01.2013
     */
    public function HandlingCostPayment() {
        $paymentMethod = $this->getPaymentMethod();

        if ($paymentMethod instanceof PaymentMethod &&
            $paymentMethod->exists()) {
            $handlingCostPayment = $paymentMethod->getHandlingCost();
        } else {
            $paymentDefaultCost = new DBMoney();
            $paymentDefaultCost->setAmount(0);
            $paymentDefaultCost->setCurrency(Config::DefaultCurrency());

            $handlingCostPayment = new HandlingCost();
            $handlingCostPayment->amount = $paymentDefaultCost;
        }

        return $handlingCostPayment->amount;
    }

    /**
     * Returns the handling costs for the chosen shipping method.
     *
     * @return DBMoney
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2013
     */
    public function HandlingCostShipment() {
        $handlingCostShipment = 0;
        $selectedShippingMethod = $this->getShippingMethod();

        if ($selectedShippingMethod) {
            $handlingCostShipmentObj = $selectedShippingMethod->getShippingFee()->getCalculatedPrice();
        } else {
            $handlingCostShipmentObj = new DBMoney();
            $handlingCostShipmentObj->setAmount($handlingCostShipment);
            $handlingCostShipmentObj->setCurrency(Config::DefaultCurrency());
        }

        return $handlingCostShipmentObj;
    }
    
    /**
     * Returns whether there are handling costs for payment.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.04.2014
     */
    public function hasHandlingCostPayment() {
        $has = false;
        if ($this->HandlingCostPayment()->getAmount() > 0) {
            $has = true;
        }
        return $has;
    }
    
    /**
     * Returns whether there are handling costs for shipment.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.04.2014
     */
    public function hasHandlingCostShipment() {
        $has = false;
        if ($this->HandlingCostShipment()->getAmount() > 0) {
            $has = true;
        }
        return $has;
    }

    /**
     * Returns the shipping method title.
     *
     * @return string
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 26.1.2011
     */
    public function CarrierAndShippingMethodTitle() {
        $title = '';
        $selectedShippingMethod = $this->getShippingMethod();

        if ($selectedShippingMethod) {
            $title = $selectedShippingMethod->Carrier()->Title . ' - ' . $selectedShippingMethod->Title;
        }

        return $title;
    }

    /**
     * Returns the payment method object.
     *
     * @return PaymentMethod
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
     * @return ShippingMethod
     */
    public function getShippingMethod() {
        $shippingMethod = null;
        if (is_numeric($this->ShippingMethodID)) {
            $shippingMethod = ShippingMethod::get()->byID($this->ShippingMethodID);
        }
        return $shippingMethod;
    }
    
    /**
     * Returns the payment method
     *
     * @return PaymentMethod
     */
    public function getPaymentMethod() {
        $paymentMethod = null;
        if (is_numeric($this->PaymentMethodID)) {
            $paymentMethod = PaymentMethod::get()->byID($this->PaymentMethodID);
        }
        return $paymentMethod;
    }

    /**
     * Returns the minimum order value.
     *
     * @return mixed Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.06.2011
     */
    public function MinimumOrderValue() {
        $minimumOrderValue = new DBMoney();

        if (Config::UseMinimumOrderValue() &&
            Config::MinimumOrderValue()) {

            $minimumOrderValue->setAmount(Config::MinimumOrderValue()->getAmount());
            $minimumOrderValue->setCurrency(Config::MinimumOrderValue()->getCurrency());
        }

        return $minimumOrderValue->Nice();
    }

    /**
     * Indicates wether the minimum order value is reached.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.06.2011
     */
    public function IsMinimumOrderValueReached() {
        if (Config::UseMinimumOrderValue() &&
            Config::MinimumOrderValue() &&
            Config::MinimumOrderValue()->getAmount() > $this->getAmountTotalWithoutFees()->getAmount()) {

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
        $positions = $this->ShoppingCartPositions();
        if ($positions) {
            $isCheckoutable = true;
            foreach ($positions as $position) {
                if ($position->Quantity > $position->Product()->StockQuantity) {
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
     * Returns the delivery time as string.
     * 
     * @param int  $shippingMethodID   ID of the shipping method to use for delivery
     * @param bool $forceDisplayInDays Force displaying the delivery time in days
     * 
     * @return string
     */
    public function getDeliveryTime($shippingMethodID = 0, $forceDisplayInDays = false) {
        $deliveryDaysMin  = 0;
        $deliveryDaysMax  = 0;
        $deliveryDaysText = '';
        if ($shippingMethodID != 0) {
            $shippingMethod = ShippingMethod::get()->byID($shippingMethodID);
        } else {
            $shippingMethod = $this->getShippingMethod();
        }
        if ($shippingMethod instanceof ShippingMethod &&
            $shippingMethod->exists()) {
            
            $deliveryDaysMin = (int) $shippingMethod->getShippingFee()->DeliveryTimeMin;
            $deliveryDaysMax = (int) $shippingMethod->getShippingFee()->DeliveryTimeMax;
            if ($deliveryDaysMin == 0) {
                $deliveryDaysMin = $shippingMethod->DeliveryTimeMin;
            }
            if ($deliveryDaysMax == 0) {
                $deliveryDaysMax = $shippingMethod->DeliveryTimeMax;
            }
        }
        
        $productDeliveryDaysMin = 0;
        $productDeliveryDaysMax = 0;
        foreach ($this->ShoppingCartPositions() as $position) {
            $min = $position->Product()->getPurchaseMinDurationDays();
            $max = $position->Product()->getPurchaseMaxDurationDays();
            if ($min > $productDeliveryDaysMin) {
                $productDeliveryDaysMin = $min;
            }
            if ($max > $productDeliveryDaysMax) {
                $productDeliveryDaysMax = $max;
            }
        }
        
        if ($productDeliveryDaysMin > $deliveryDaysMin) {
            $deliveryDaysMin = $productDeliveryDaysMin;
        }
        if ($productDeliveryDaysMax > $deliveryDaysMax) {
            $deliveryDaysMax = $productDeliveryDaysMax;
        }
        if ($deliveryDaysMax < $productDeliveryDaysMin) {
            $deliveryDaysMax = 0;
        }
        
        $deliveryTime = ShippingMethod::get_delivery_time($deliveryDaysMin, $deliveryDaysMax, $deliveryDaysText, $forceDisplayInDays);
        $this->extend('updateDeliveryTime', $deliveryTime);
        return $deliveryTime;
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
        if (Config::PriceType() == 'gross') {
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
     * @return DBMoney
     */
    public function getAmountTotalGross($excludeModules = array(), $excludeShoppingCartPositions = false, $excludeCharges = false) {
        $amount  = $this->getTaxableAmountGrossWithFees($excludeShoppingCartPositions)->getAmount();
        $amount += $this->getNonTaxableAmount($excludeModules, $excludeShoppingCartPositions)->getAmount();
        
        // Handling costs for payment and shipment
        if (!$excludeCharges &&
             $this->HasChargesAndDiscountsForTotal()) {
            
            $amount += $this->ChargesAndDiscountsForTotal('gross')->Price->getAmount();
        }
        
        $amountObj = new DBMoney;
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(Config::DefaultCurrency());

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
     * @return DBMoney
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
     * @return DBMoney
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

        $amountObj = new DBMoney;
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(Config::DefaultCurrency());

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
     * @return DBMoney
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
        
        $amountObj = new DBMoney;
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(Config::DefaultCurrency());

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
     * @return DBMoney
     */
    public function getAmountTotalWithoutFees($excludeModules = array(), $excludeShoppingCartPositions = false, $excludeCharges = false) {
        if (Config::Pricetype() == 'gross') {
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
     * @return DBMoney
     */
    public function getAmountTotalGrossWithoutFees($excludeModules = array(), $excludeShoppingCartPositions = false, $excludeCharges = false) {
        $amount  = $this->getTaxableAmountGrossWithoutFees($excludeModules, $excludeShoppingCartPositions, $excludeCharges)->getAmount();
        $amount += $this->getNonTaxableAmount($excludeModules, $excludeShoppingCartPositions)->getAmount();

        if (round($amount, 2) === 0.00) {
            $amount = round($amount, 2);
        }       

        $amountObj = new DBMoney;
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(Config::DefaultCurrency());

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
     * @return DBMoney
     */
    public function getAmountTotalNetWithoutFees($excludeModules = array(), $excludeShoppingCartPositions = false, $excludeCharges = false) {
        $amount  = $this->getTaxableAmountNetWithoutFees($excludeModules, $excludeShoppingCartPositions, $excludeCharges)->getAmount();
        $amount += $this->getNonTaxableAmount($excludeModules, $excludeShoppingCartPositions)->getAmount();

        if (round($amount, 2) === 0.00) {
            $amount = round($amount, 2);
        }

        $amountObj = new DBMoney;
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(Config::DefaultCurrency());

        return $amountObj;
    }
    
    /**
     * Returns the tax rates for shipping and payment fees.
     * 
     * @return ArrayList
     */
    public function getTaxRatesForFees() {
        $taxes          = new ArrayList();
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
            $taxObj = new DBMoney;
            $taxObj->setAmount($tax->AmountRaw);
            $taxObj->setCurrency(Config::DefaultCurrency());

            $tax->Amount = $taxObj;
        }

        return $taxes;
    }

    /**
     * Returns tax amounts included in the shoppingcart separated by tax rates
     * with fee taxes.
     *
     * @return ArrayList
     */
    public function getTaxRatesWithFees() {
        if (is_null($this->taxRatesWithFees)) {
            $taxes          = $this->getTaxRatesWithoutFees();
            $shippingMethod = $this->getShippingMethod();
            $paymentMethod  = $this->getPaymentMethod();

            if ($shippingMethod) {
                $shippingFee = $shippingMethod->getShippingFee();

                if ($shippingFee) {
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

            if ($paymentMethod) {
                $paymentFee = $paymentMethod->getHandlingCost();

                if ($paymentFee instanceof HandlingCost) {
                    if ($paymentFee->Tax()) {
                        $taxRate = $paymentFee->Tax()->getTaxRate();

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
                $taxObj = new DBMoney;
                $taxObj->setAmount(round($tax->AmountRaw, 2));
                $taxObj->setCurrency(Config::DefaultCurrency());

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
     * @return ArrayList
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

                if (Config::PriceType() == 'gross') {
                    $taxSection->AmountRaw += $chargeAndDiscountAmount - ($chargeAndDiscountAmount / (100 + $taxSection->Rate) * 100);
                } else {
                    $taxSection->AmountRaw += ($chargeAndDiscountAmount / 100 * (100 + $taxSection->Rate)) - $chargeAndDiscountAmount;
                }
            }
        }

        foreach ($taxes as $tax) {
            $taxObj = new DBMoney;
            $taxObj->setAmount($tax->AmountRaw);
            $taxObj->setCurrency(Config::DefaultCurrency());

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
     * @return ArrayList
     */
    public function getTaxRatesWithoutFeesAndCharges($excludeModules = array(), $excludeShoppingCartPosition = false) {
        $positions          = $this->ShoppingCartPositions();
        $taxes              = new ArrayList();
        $registeredModules  = $this->callMethodOnRegisteredModules(
            'ShoppingCartPositions',
            array(
                Customer::currentUser()->getCart(),
                Customer::currentUser(),
                true
            ),
            $excludeModules,
            $excludeShoppingCartPosition
        );

        // products
        foreach ($positions as $position) {
            $taxRate            = $position->Product()->getTaxRate();
            $originalTaxRate    = $position->Product()->getTaxRate(true);

            if (!$taxes->find('Rate', $taxRate)) {
                $taxes->push(
                    new DataObject(
                        array(
                            'Rate'          => $taxRate,
                            'OriginalRate'  => $originalTaxRate,
                            'AmountRaw'     => (float) 0.0,
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
                                'Rate'          => $taxRate,
                                'OriginalRate'  => $taxRate,
                                'AmountRaw'     => (float) 0.0,
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
            $taxObj = new DBMoney;
            $taxObj->setAmount($tax->AmountRaw);
            $taxObj->setCurrency(Config::DefaultCurrency());

            $tax->Amount = $taxObj;
        }

        return $taxes;
    }

    /**
     * Returns the most valuable tax rate for the current cart.
     *
     * @param array $taxes The tax rates array (associative)
     * 
     * @return int
     */
    public static function get_most_valuable_tax_rate($taxes = null) {
        $rate = false;
        if (Customer::currentUser() &&
            Customer::currentUser()->ShoppingCartID > 0) {
            $shoppingCart = Customer::currentUser()->getCart();
            $taxRate = $shoppingCart->getMostValuableTaxRate($taxes);
            if ($taxRate) {
                $rate = $taxRate->Rate;
            }
        }
        return $rate;
    }

    /**
     * Returns the Tax object with the highest tax value for the
     * given taxes.
     *
     * @param array $taxes The tax rates array (associative)
     *
     * @return Tax
     */
    public function getMostValuableTaxRate($taxes = null) {
        if (is_null($taxes)) {
            $taxes = $this->getTaxRatesWithoutFeesAndCharges('Voucher');
        }
        $highestTaxValue                = 0;
        $mostValuableTaxRate            = null;
        $originalMostValuableTaxRate    = null;

        foreach ($taxes as $tax) {
            if ($tax->AmountRaw >= $highestTaxValue) {
                $highestTaxValue                = $tax->AmountRaw;
                $mostValuableTaxRate            = $tax->Rate;
                $originalMostValuableTaxRate    = $tax->OriginalRate;
            }
        }

        if (!is_null($originalMostValuableTaxRate)) {
            $silvercartTax = Tax::get()->filter('Rate', $originalMostValuableTaxRate)->first();
            
            if ($silvercartTax) {
                if ($originalMostValuableTaxRate != $mostValuableTaxRate) {
                    $silvercartTax->Rate = $mostValuableTaxRate;
                    $silvercartTax->setI18nTitle($mostValuableTaxRate . '%');
                }
                return $silvercartTax;
            }
        }

        return false;
    }

    /**
     * calculate the carts total weight
     * needed to determin the ShippingFee
     *
     * @return integer|boolean the cart´s weight in gramm
     */
    public function getWeightTotal() {
        $positions = $this->ShoppingCartPositions();
        $totalWeight = (int) 0;
        if ($positions) {
            foreach ($positions as $position) {
                $totalWeight += $position->Product()->Weight * $position->Quantity;
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
     */
    public function getShowFees() {
        $showFees = false;

        if ($this->ShippingMethodID > 0 &&
            $this->PaymentMethodID > 0) {

            $showFees = true;
        }

        return $showFees;
    }

    /**
     * Indicates wether the fees for shipping and payment should be shown.
     *
     * @return boolean
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
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.10.2010
     */
    public function onBeforeDelete() {
        parent::onBeforeDelete();
        $shoppingCartPositions = ShoppingCartPosition::get()->filter('ShoppingCartID', $this->ID);
        
        if ($shoppingCartPositions->exists()) {
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
     * @return DataList
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.04.2014
     */
    public function registeredModules() {
        if (is_null($this->registeredModulesSet)) {
            $customer = Customer::currentUser();
            $modules = array();
            $registeredModules = self::$registeredModules;
            $hookMethods = array(
                'NonTaxableShoppingCartPositions',
                'TaxableShoppingCartPositions',
                'IncludedInTotalShoppingCartPositions',
                'ShoppingCartActions',
                'ShoppingCartTotal',
                'CustomShoppingCartPositions',
            );

            foreach ($registeredModules as $registeredModule) {
                $registeredModuleObjPlain = new $registeredModule();
                $registeredModuleObj      = false;

                if ($registeredModuleObjPlain->hasMethod('loadObjectForShoppingCart')) {
                    $registeredModuleObj = $registeredModuleObjPlain->loadObjectForShoppingCart($this);
                }

                if (!$registeredModuleObj) {
                    $registeredModuleObj = $registeredModuleObjPlain;
                }

                if ($registeredModuleObj) {
                    $hooks = array();
                    foreach ($hookMethods as $hookMethod) {
                        if ($registeredModuleObj->hasMethod($hookMethod)) {
                            $hooks[$hookMethod] = $registeredModuleObj->$hookMethod($this, $customer);
                        }
                    }
                    $modules[] = $hooks;
                }
            }

            $this->registeredModulesSet = new ArrayList($modules);
        }

        return $this->registeredModulesSet;
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
     *      'ModuleName' => DataList (ModulePositions)
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
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
     */
    public function setShippingMethodID($shippingMethodId) {
        $this->ShippingMethodID = $shippingMethodId;
    }

    /**
     * Set the ID of the payment method the customer has chosen.
     *
     * @param Int $paymentMethodId The ID of the payment method object.
     *
     * @return void
     */
    public function setPaymentMethodID($paymentMethodId) {
        $this->PaymentMethodID = $paymentMethodId;
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
        $positionTable = Tools::get_table_name(ShoppingCartPosition::class);
        $records = DB::query(
            sprintf(
                "SELECT COUNT(Pos.ID) AS NumberOfPositions FROM %s Pos WHERE Pos.ShoppingCartID = %d",
                $positionTable,
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
        $positions = $this->ShoppingCartPositions();
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
        $positions = $this->ShoppingCartPositions();
        if ($positions) {
            foreach ($positions as $position) {
                $position->resetMessageTokens();
            }
        }
    }
    
}
