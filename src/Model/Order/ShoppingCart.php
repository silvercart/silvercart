<?php

namespace SilverCart\Model\Order;

use SilverCart\Admin\Model\Config;
use SilverCart\Checkout\Checkout;
use SilverCart\Dev\DateTools;
use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\ShoppingCartPosition;
use SilverCart\Model\Order\ShoppingCartPositionNotice;
use SilverCart\Model\Payment\HandlingCost;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Product\Tax;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Shipment\ShippingFee;
use SilverCart\Model\Shipment\ShippingMethod;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\FieldType\DBMoney;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SilverStripe\View\ArrayData;
use SilverStripe\View\ViewableData;

/**
 * abstract for shopping cart.
 *
 * @package SilverCart
 * @subpackage Model_Order
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @var int $MemberID Member ID
 * 
 * @method Member                         Member()                Returns the related Member.
 * @method \SilverStripe\ORM\HasManyList  ShoppingCartPositions() Returns the related positions.
 * @method \SilverStripe\ORM\ManyManyList Products()              Returns the related products.
 */
class ShoppingCart extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    const SESSION_KEY = 'SilverCart.ShoppingCart';

    /**
     * Contains all registered modules that get called when the shoppingcart
     * is displayed.
     *
     * @var array
     */
    public static $registeredModules = [];
    /**
     * Module list to exclude from most valuable tax rate calculation.
     *
     * @var array
     */
    private static $exclude_modules_from_most_valuable_tax_rate = [];
    /**
     * 1:n relations
     *
     * @var array
     */
    private static $has_many = [
        'ShoppingCartPositions' => ShoppingCartPosition::class,
    ];
    /**
     * defines n:m relations
     *
     * @var array configure relations
     */
    private static $many_many = [
        'Products' => Product::class,
    ];
    /**
     * has one back relation
     *
     * @var array
     */
    private static $belongs_to = [
        'Member' => Member::class,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartShoppingCart';
    /**
     * Summary fields
     *
     * @var string[]
     */
    private static $summary_fields = [
        'AmountTotal',
        'ShoppingCartPositions.count',
    ];
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
     * Global cart context
     *
     * @var ShoppingCart
     */
    protected static $cart = null;
    /**
     * Determines whether to clear checkout after writing or not.
     *
     * @var bool
     */
    protected static $clear_checkout_after_write = true;
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
     * Shipping country context to show fees for.
     *
     * @var Int
     */
    protected $shippingCountry;
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
    protected $cacheHashes = [];
    /**
     * List of already calculated tax amounts
     *
     * @var array
     */
    protected $taxTotalList = [];
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
     * Delivery time data.
     *
     * @var ArrayData[]
     */
    protected $deliveryTimeData = [];
    
    /**
     * Returns the global shopping cart instance.
     * 
     * @return \SilverCart\Model\Order\ShoppingCart|null
     */
    public static function getCart() : ?ShoppingCart
    {
        return self::$cart;
    }

    /**
     * Sets whether to clear checkout after writing or not.
     * 
     * @param bool $clear Clear?
     * 
     * @return void
     */
    public static function setClearCheckoutAfterWrite(bool $clear) : void
    {
        self::$clear_checkout_after_write = $clear;
    }
    
    /**
     * Returns whether to clear checkout after writing or not.
     * 
     * @return bool
     */
    public static function getClearCheckoutAfterWrite() : bool
    {
        return self::$clear_checkout_after_write;
    }
    
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
    public function __construct($record = null, $isSingleton = false)
    {
        parent::__construct($record, $isSingleton);
        if ($this->ID > 0) {
            if (self::$cart === null) {
                self::$cart = $this;
            }
            if (!Tools::isIsolatedEnvironment()
             && !Tools::isBackendEnvironment()
            ) {
                if (!self::$cartCleaningFinished
                 && !self::$cartCleaningInProgress
                ) {
                    self::$cartCleaningInProgress = true;
                    $this->cleanUp();
                }
                $this->ShippingMethodID = 0;
                $this->PaymentMethodID  = 0;
                $currentUser            = Security::getCurrentUser();
                if ($currentUser instanceof Member
                 && self::$loadModules
                ) {
                    $this->callMethodOnRegisteredModules('performShoppingCartConditionsCheck', [
                        $this,
                        $currentUser,
                    ]);
                    $this->callMethodOnRegisteredModules('ShoppingCartInit', [$this]);
                }
            }
        }
    }

    /**
     * Deletes all shopping cart positions without a product association or with a
     * quantity of 0.
     *
     * @return void
     */
    protected function cleanUp() : void
    {
        $this->extend('onBeforeCleanUp');
        $positionTable = ShoppingCartPosition::config()->table_name;
        $positionIDs   = DB::query("SELECT ID FROM {$positionTable} WHERE {$positionTable}.ShoppingCartID = {$this->ID} AND (ProductID = 0 OR Quantity = 0)");
        if ($positionIDs->numRecords() > 0) {
            foreach ($positionIDs as $positionID) {
                $position = ShoppingCartPosition::get()->byID($positionID);
                $position->delete();
            }
        }
        $this->extend('onAfterCleanUp');
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
     * Field labels for display in tables.
     *
     * @param bool $includerelations A boolean value to indicate if the labels returned include relation fields
     * 
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'AmountTotal'           => _t(Order::class . '.AMOUNTTOTAL', 'Amount total'),
            'Products'              => Product::singleton()->plural_name(),
            'ShoppingCartPositions' => ShoppingCartPosition::singleton()->plural_name(),
        ]);
    }
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $fields->addFieldToTab('Root.Main', ReadonlyField::create('AmountTotal', $this->fieldLabel('AmountTotal'), $this->getAmountTotal()->Nice()));
            $fields->addFieldToTab('Root.Main', ReadonlyField::create('ShoppingCartPositionsCount', $this->fieldLabel('ShoppingCartPositions'), $this->ShoppingCartPositions()->count()));
            $positionsField = $fields->dataFieldByName('ShoppingCartPositions');
            if ($positionsField instanceof GridField) {
                $positionFieldConfig = $positionsField->getConfig();
                $positionFieldConfig->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
                $positionFieldConfig->removeComponentsByType(GridFieldAddNewButton::class);
                $deleteAction        = $positionFieldConfig->getComponentByType(GridFieldDeleteAction::class);
                /* @var $deleteAction GridFieldDeleteAction */
                $deleteAction->setRemoveRelation(false);
            }
            $productsField = $fields->dataFieldByName('Products');
            if ($productsField instanceof GridField) {
                $productsFieldConfig = $productsField->getConfig();
                $productsFieldConfig->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
                $productsFieldConfig->removeComponentsByType(GridFieldAddNewButton::class);
            }
        });
        return parent::getCMSFields();
    }

    /**
     * Clears the checkout session data after updating the shopping cart.
     *
     * @return void
     */
    protected function onAfterWrite() : void
    {
        parent::onAfterWrite();
        if (self::getClearCheckoutAfterWrite()) {
            Checkout::clear_session();
        }
    }

    /**
     * Indicates wether the cart has charges and discounts for the product
     * values.
     *
     * @return bool
     */
    public function HasChargesAndDiscountsForProducts() : bool
    {
        if ($this->ChargesAndDiscountsForProducts()) {
            return true;
        }
        return false;
    }
    
    /**
     * Indicates wether the cart has charges and discounts for the total
     * shopping cart value.
     *
     * @return bool
     */
    public function HasChargesAndDiscountsForTotal() : bool
    {
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
     * @return bool
     */
    public function HasMorePositionsThan(int $positions = 0) : bool
    {
        return (int) $positions > (int) $this->getQuantity();
    }

    /**
     * Returns true if the number of positions in the cart equals the given
     * value.
     *
     * @param int $positions The value to check
     *
     * @return bool
     */
    public function HasNumberOfPositions(int $positions = 0) : bool
    {
        return (int) $positions === (int) $this->getQuantity();
    }
    
    /**
     * Returns whether this shopping cart has products with different release 
     * dates.
     * 
     * @return bool
     */
    public function HasProductsWithDifferentReleaseDates() : bool
    {
        $hasDifferentReleaseDates = false;
        if ($this->ShoppingCartPositions()->count() > 1) {
            $releaseDates = [];
            foreach ($this->ShoppingCartPositions() as $position) {
                $product = $position->Product();
                if ($product->HasReleaseDate()) {
                    $releaseDates[] = date('Y-m-d', strtotime($product->ReleaseDate));
                } else {
                    $releaseDates[] = date('Y-m-d');
                }
                $hasDifferentReleaseDates = count(array_unique($releaseDates)) > 1;
            }
        }
        return $hasDifferentReleaseDates;
    }
    
    /**
     * Returns whether this shopping cart has at least one product with a release 
     * date.
     * 
     * @return bool
     */
    public function HasProductWithReleaseDates() : bool
    {
        $hasReleaseDate = false;
        if ($this->ShoppingCartPositions()->exists()) {
            foreach ($this->ShoppingCartPositions() as $position) {
                $product = $position->Product();
                if ($product->HasReleaseDate()) {
                    $hasReleaseDate = true;
                    break;
                }
            }
        }
        return $hasReleaseDate;
    }
    
    /**
     * Clears the price cache.
     * 
     * @return ShoppingCart
     */
    public function clearPriceCache() : ShoppingCart
    {
        $this->cacheHashes = [];
        return $this;
    }
    
    /**
     * Returns the charges and discounts for product values.
     *
     * @param string $priceType 'gross' or 'net'
     *
     * @return ArrayData
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.07.2013
     */
    public function ChargesAndDiscountsForProducts($priceType = false)
    {
        $cacheHash = md5($priceType);
        $cacheKey = 'ChargesAndDiscountsForProducts_'.$cacheHash;
        if (array_key_exists($cacheKey, $this->cacheHashes)) {
            return $this->cacheHashes[$cacheKey];
        }
        $paymentMethod = $this->getPaymentMethod();
        if ($paymentMethod instanceof PaymentMethod
         && $paymentMethod->exists()
        ) {
            $handlingCostPayment = $paymentMethod->getChargesAndDiscountsForProducts($this, $priceType);
            if ($handlingCostPayment->getAmount() == 0) {
                return false;
            } else {
                $chargesAndDiscounts = ArrayData::create([
                    'Name'                         => $paymentMethod->sumModificationLabel,
                    'sumModificationImpact'        => $paymentMethod->sumModificationImpact,
                    'sumModificationProductNumber' => $paymentMethod->sumModificationProductNumber,
                    'PriceFormatted'               => $handlingCostPayment->Nice(),
                    'Price'                        => $handlingCostPayment,
                    'Tax'                          => $this->getMostValuableTaxRate(),
                ]);
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
     * @return ArrayData
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.07.2013
     */
    public function ChargesAndDiscountsForTotal($priceType = false)
    {
        $cacheHash = md5($priceType);
        $cacheKey  = 'ChargesAndDiscountsForTotal_'.$cacheHash;
        if (array_key_exists($cacheKey, $this->cacheHashes)) {
            return $this->cacheHashes[$cacheKey];
        }
        $paymentMethod = $this->getPaymentMethod();
        if ($paymentMethod instanceof PaymentMethod
         && $paymentMethod->exists()
        ) {
            $handlingCostPayment = $paymentMethod->getChargesAndDiscountsForTotal($this, $priceType);
            if ($handlingCostPayment === false) {
                return false;
            } else {
                $tax   = $this->getMostValuableTaxRate($this->getTaxRatesWithFees());
                $handlingCostPaymentRounded = $handlingCostPayment;
                $handlingCostPaymentRounded->setAmount(
                    round($handlingCostPayment->getAmount(), 2)
                );
                $chargesAndDiscounts = ArrayData::create([
                    'Name'                         => $paymentMethod->sumModificationLabel,
                    'sumModificationImpact'        => $paymentMethod->sumModificationImpact,
                    'sumModificationProductNumber' => $paymentMethod->sumModificationProductNumber,
                    'PriceFormatted'               => $handlingCostPayment->Nice(),
                    'Price'                        => $handlingCostPayment,
                    'Tax'                          => $tax,
                ]);
                $this->chargesAndDiscountsForTotal = $chargesAndDiscounts;
                $this->cacheHashes[$cacheKey]      = $this->chargesAndDiscountsForTotal;
                return $chargesAndDiscounts;
            }
        }
        return false;
    }

    /**
     * Set wether the registered modules should be loaded and handled.
     *
     * @param bool $doLoad set wether to load the modules or not
     *
     * @return void
     */
    public static function setLoadShoppingCartModules(bool $doLoad) : void
    {
        self::$loadModules = $doLoad;
    }

    /**
     * Set wether the shopping cart forms should be drawn.
     *
     * @param bool $doCreate set wether to create the forms or not
     *
     * @return void
     */
    public static function setCreateShoppingCartForms(bool $doCreate) : void
    {
        self::$createForms = $doCreate;
    }

    /**
     * adds a product to the cart
     *
     * @param array $formData the sended form data
     *
     * @return ShoppingCartPosition
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.11.2014
     */
    public static function addProduct(array $formData) : ?ShoppingCartPosition
    {
        $position = null;
        $member   = Customer::currentUser();
        if (!$member) {
            $member = Customer::createAnonymousCustomer();
        }
        $overwriteAddProduct = false;
        $member->getCart()->extend('overwriteAddProduct', $overwriteAddProduct, $formData, $position);
        if (!$overwriteAddProduct
         && $formData['productID']
         && $formData['productQuantity']
        ) {
            $cart = $member->getCart();
            if ($cart instanceof ShoppingCart
             && $cart->exists()
            ) {
                $product = Product::get()->byID($formData['productID']);
                if ($product instanceof Product
                 && $product->exists()
                ) {
                    $quantity = (float) str_replace(',', '.', $formData['productQuantity']);
                    if ($quantity > 0) {
                        $position = $product->addToCart($cart->ID, $quantity);
                    }
                }
            }
        }
        return $position;
    }

    /**
     * Removes a product out of the cart.
     *
     * @param array                $data             Data to use to identify the position.
     * @param ShoppingCartPosition &$deletedPosition Optional pointer to store the deleted Position in
     *
     * @return bool
     */
    public static function removeProduct(array $data, ShoppingCartPosition &$deletedPosition = null) : bool
    {
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
            $product    = Product::get()->byID($data['productID']);
            if ($product instanceof Product) {
                $cartID = $cart->ID;
                $product->extend('updateRemoveFromCart', $cartID);
            }
            if ($position instanceof ShoppingCartPosition) {
                $deletedPosition = $position;
                $position->delete();
                $error = false;
                $member->getCart()->extend('onAfterRemoveFromCart', $data);
            }
        }
        return !$error;
    }


    /**
     * Returns one or more plugged in rows for the shopping carts editable table
     * as a ArrayList
     * 
     * @return ArrayList
     */
    public function addToEditableShoppingCartTable() : ArrayList
    {
        $addToCartTable = ArrayList::create();
        $this->extend('addToEditableShoppingCartTable', $addToCartTable);
        return $addToCartTable;
    }

    /**
     * empties cart
     *
     * @return void
     */
    public function delete() : void
    {
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
     * @return float
     */
    public function getQuantity(int $productId = null) : float
    {
        $positions = $this->ShoppingCartPositions();
        $quantity  = 0;
        foreach ($positions as $position) {
            if ($productId === null
             || $position->Product()->ID === $productId
            ) {
                $quantity += $position->Quantity;
            }
        }
        return $quantity;
    }

    /**
     * Returns the price of the cart positions + fees, including taxes.
     *
     * @param array $excludeShoppingCartPositions Positions that shall not be counted; can contain the ID or the className of the position
     * @param bool  $excludeCharges               Indicates wether charges and discounts should be calculated
     *
     * @return DBMoney
     */
    public function getTaxableAmountWithFees(array $excludeShoppingCartPositions = [], bool $excludeCharges = false) : DBMoney
    {
        if (Config::PriceType() === Config::PRICE_TYPE_GROSS) {
            $taxableAmountWithFees = $this->getTaxableAmountGrossWithFees($excludeShoppingCartPositions, $excludeCharges);
        } else {
            $taxableAmountWithFees = $this->getTaxableAmountNetWithFees($excludeShoppingCartPositions, $excludeCharges);
        }
        return $taxableAmountWithFees;
    }

    /**
     * Returns the GROSS price of the cart positions + fees, including taxes.
     *
     * @param array $excludeShoppingCartPositions Positions that shall not be counted; can contain the ID or the className of the position
     * @param bool  $excludeCharges               Indicates wether charges and discounts should be calculated
     *
     * @return DBMoney
     */
    public function getTaxableAmountGrossWithFees(array $excludeShoppingCartPositions = [], bool $excludeCharges = false) : DBMoney
    {
        $shippingMethod = $this->getShippingMethod();
        $paymentMethod  = $this->getPaymentMethod();
        $amountTotal    = $this->getTaxableAmountGrossWithoutFees([], $excludeShoppingCartPositions, $excludeCharges)->getAmount();
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
        return DBMoney::create()
                ->setAmount($amountTotal)
                ->setCurrency(Config::DefaultCurrency());
    }

    /**
     * Returns the NET price of the cart positions + fees, including taxes.
     *
     * @param array $excludeShoppingCartPositions Positions that shall not be counted; can contain the ID or the className of the position
     * @param bool  $excludeCharges               Indicates wether charges and discounts should be calculated
     *
     * @return DBMoney
     */
    public function getTaxableAmountNetWithFees(array $excludeShoppingCartPositions = [], bool $excludeCharges = false) : DBMoney
    {
        $shippingMethod = $this->getShippingMethod();
        $paymentMethod  = $this->getPaymentMethod();
        $amountTotal    = round($this->getTaxableAmountNetWithoutFees([], $excludeShoppingCartPositions, $excludeCharges)->getAmount(), 2);
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
        return DBMoney::create()
                ->setAmount($amountTotal)
                ->setCurrency(Config::DefaultCurrency());
    }

    /**
     * Returns the price of the cart positions, including taxes, excluding fees.
     *
     * @param array $excludeModules               An array of registered modules that shall not be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted; can contain the ID or the className of the position
     * @param bool  $excludeCharges               Indicates wether charges and discounts should be calculated
     * 
     * @return DBMoney
     */
    public function getTaxableAmountWithoutFees(array $excludeModules = [], array $excludeShoppingCartPositions = [], bool $excludeCharges = false) : DBMoney
    {
        if (Config::PriceType() === Config::PRICE_TYPE_GROSS) {
            $taxableAmountWithoutFees = $this->getTaxableAmountGrossWithoutFees($excludeModules, $excludeShoppingCartPositions, $excludeCharges);
        } else {
            $taxableAmountWithoutFees = $this->getTaxableAmountNetWithoutFees($excludeModules, $excludeShoppingCartPositions, $excludeCharges);
        }
        return $taxableAmountWithoutFees;
    }

    /**
     * Returns the GROSS price of the cart positions, including taxes, excluding fees.
     *
     * @param array $excludeModules               An array of registered modules that shall not be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted; can contain the ID or the className of the position
     * @param bool  $excludeCharges               Indicates wether charges and discounts should be calculated
     * 
     * @return DBMoney
     */
    public function getTaxableAmountGrossWithoutFees(array $excludeModules = [], array $excludeShoppingCartPositions = [], bool $excludeCharges = false) : DBMoney
    {
        $amount = $this->getTaxableAmountGrossWithoutFeesAndCharges($excludeModules, $excludeShoppingCartPositions)->getAmount();
        // Handling costs for payment and shipment
        if (!$excludeCharges
         && $this->ChargesAndDiscountsForProducts()
        ) {
            $amount += $this->ChargesAndDiscountsForProducts()->Price->getAmount();
        }
        return DBMoney::create()
                ->setAmount($amount)
                ->setCurrency(Config::DefaultCurrency());
    }

    /**
     * Returns the NET price of the cart positions, including taxes, excluding fees.
     *
     * @param array $excludeModules               An array of registered modules that shall not be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted; can contain the ID or the className of the position
     * @param bool  $excludeCharges               Indicates wether charges and discounts should be calculated
     * 
     * @return DBMoney
     */
    public function getTaxableAmountNetWithoutFees(array $excludeModules = [], array $excludeShoppingCartPositions = [], bool $excludeCharges = false) : DBMoney
    {
        $amount = $this->getTaxableAmountNetWithoutFeesAndCharges($excludeModules, $excludeShoppingCartPositions)->getAmount();
        // Handling costs for payment and shipment
        if (!$excludeCharges
         && $this->ChargesAndDiscountsForProducts()
        ) {
            $amount += $this->ChargesAndDiscountsForProducts()->Price->getAmount();
        }
        if (round($amount, 2) === -0.00) {
            $amount = 0;
        }
        return DBMoney::create()
                ->setAmount($amount)
                ->setCurrency(Config::DefaultCurrency());
    }
    
    /**
     * Returns the price of the cart positions without modules.
     *
     * The price type is automatically determined by the ShoppinCartPosition.
     *
     * @return DBMoney
     */
    public function getTaxableAmountWithoutModules() : DBMoney
    {
        if (Config::PriceType() === Config::PRICE_TYPE_GROSS) {
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
    public function getTaxableAmountGrossWithoutModules() : DBMoney
    {
        $amountObj       = DBMoney::create();
        $amount          = 0;
        $modulePositions = $this->getTaxableShoppingcartPositions([], [], false);
        foreach ($modulePositions as $modulePosition) {
            $amount += (float) $modulePosition->getPrice(false, Config::PRICE_TYPE_GROSS)->getAmount();
        }
        $this->extend('updateTaxableAmountGrossWithoutModules', $amount, $modulePositions);
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
    public function getTaxableAmountNetWithoutModules() : DBMoney
    {
        $amountObj       = DBMoney::create();
        $amount          = 0;
        $modulePositions = $this->getTaxableShoppingcartPositions([], [], false);
        foreach ($modulePositions as $modulePosition) {
            $amount += (float) $modulePosition->getPrice(false, Config::PRICE_TYPE_NET)->getAmount();
        }
        $this->extend('updateTaxableAmountNetWithoutModules', $amount, $modulePositions);
        $amountObj->setAmount($amount);
        $amountObj->setCurrency(Config::DefaultCurrency());
        return $amountObj;
    }

    /**
     * Returns all taxable shopping cart positions.
     *
     * @param array $excludeModules              An array of registered modules that shall not
     *                                           be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted;
     *                                           can contain the ID or the className of the position
     * @param bool  $includeModules              Indicate whether to include modules or not
     *
     * @return ArrayList
     */
    public function getTaxableShoppingcartPositions(array $excludeModules = [], array $excludeShoppingCartPositions = [], bool $includeModules = true) : ArrayList
    {
        $cartPositions = ArrayList::create();
        $cacheHash     = md5(
            implode(',', $excludeModules).
            implode(',', $excludeShoppingCartPositions).
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
                [
                    $this,
                    Customer::currentUser(),
                    true,
                    $excludeShoppingCartPositions,
                    false
                ],
                $excludeModules
            );
            if ($registeredModules) {
                foreach ($registeredModules as $modulePositions) {
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
     * Returns the price of the cart positions, including or excluding taxes 
     * depending on the current context.
     *
     * @param array $excludeModules               An array of registered modules that shall not be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted; can contain the ID or the className of the position
     * 
     * @return DBMoney
     */
    public function getTaxableAmountWithoutFeesAndCharges(array $excludeModules = [], array $excludeShoppingCartPositions = []) : DBMoney
    {
        if (Config::PriceType() === Config::PRICE_TYPE_GROSS) {
            $amount = $this->getTaxableAmountGrossWithoutFeesAndCharges($excludeModules, $excludeShoppingCartPositions);
        } else {
            $amount = $this->getTaxableAmountNetWithoutFeesAndCharges($excludeModules, $excludeShoppingCartPositions);
        }
        return $amount;
    }

    /**
     * Returns the price of the cart positions, including taxes.
     *
     * @param array $excludeModules               An array of registered modules that shall not be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted; can contain the ID or the className of the position
     * 
     * @return DBMoney
     */
    public function getTaxableAmountGrossWithoutFeesAndCharges(array $excludeModules = [], array $excludeShoppingCartPositions = []) : DBMoney
    {
        if (!is_array($excludeModules)) {
            $excludeModules = [$excludeModules];
        }
        if (!is_array($excludeShoppingCartPositions)) {
            $excludeShoppingCartPositions = [$excludeShoppingCartPositions];
        }
        $cacheHash = md5(
            implode(',', $excludeModules).
            implode(',', $excludeShoppingCartPositions)
        );
        $cacheKey = 'getTaxableAmountGrossWithoutFeesAndCharges_'.$cacheHash;
        if (array_key_exists($cacheKey, $this->cacheHashes)) {
            return $this->cacheHashes[$cacheKey];
        }
        $amount          = 0;
        $modulePositions = $this->getTaxableShoppingcartPositions($excludeModules, $excludeShoppingCartPositions, true);
        foreach ($modulePositions as $modulePosition) {
            $amount += (float) $modulePosition->getPrice(false, Config::PRICE_TYPE_GROSS)->getAmount();
        }
        $this->extend('updateTaxableAmountGrossWithoutFeesAndCharges', $amount, $modulePositions);
        $amountObj = DBMoney::create()
                ->setCurrency(Config::DefaultCurrency())
                ->setAmount($amount);
        $this->cacheHashes[$cacheKey] = $amountObj;
        return $amountObj;
    }

    /**
     * Returns the price of the cart positions, including taxes.
     *
     * @param array $excludeShoppingCartPositions Positions that shall not be counted; can contain the ID or the className of the position
     * 
     * @return DBMoney
     */
    public function getTaxableAmountGrossWithoutFeesAndChargesAndModules(array $excludeShoppingCartPositions = []) : DBMoney
    {
        return $this->getTaxableAmountGrossWithoutFeesAndCharges(self::$registeredModules, $excludeShoppingCartPositions);
    }

    /**
     * Returns the price of the cart positions.
     *
     * @param array $excludeModules               An array of registered modules that shall not be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted; can contain the ID or the className of the position
     * 
     * @return DBMoney
     */
    public function getTaxableAmountNetWithoutFeesAndCharges(array $excludeModules = [], array $excludeShoppingCartPositions = []) : DBMoney
    {
        $cacheHash = md5(
            implode(',', $excludeModules).'_'.
            implode(',', $excludeShoppingCartPositions)
        );
        $cacheKey = 'getTaxableAmountNetWithoutFeesAndCharges_'.$cacheHash;
        if (array_key_exists($cacheKey, $this->cacheHashes)) {
            return $this->cacheHashes[$cacheKey];
        }
        $amount          = 0;
        $modulePositions = $this->getTaxableShoppingcartPositions($excludeModules, $excludeShoppingCartPositions, true);
        foreach ($modulePositions as $modulePosition) {
            $amount += (float) $modulePosition->getPrice(false, Config::PRICE_TYPE_NET)->getAmount();
        }
        $this->extend('updateTaxableAmountNetWithoutFeesAndCharges', $amount, $modulePositions);
        $this->cacheHashes[$cacheKey] = DBMoney::create()
                ->setAmount($amount);
        return $this->cacheHashes[$cacheKey];
    }

    /**
     * Returns the price of the cart positions.
     *
     * @param array $excludeShoppingCartPositions Positions that shall not be counted; can contain the ID or the className of the position
     * 
     * @return DBMoney
     */
    public function getTaxableAmountNetWithoutFeesAndChargesAndModules(array $excludeShoppingCartPositions = []) : DBMoney
    {
        return $this->getTaxableAmountNetWithoutFeesAndCharges(self::$registeredModules, $excludeShoppingCartPositions);
    }

    /**
     * Returns the total amount of all taxes.
     *
     * @param bool $excludeCharges Indicates wether to exlude charges and discounts
     *
     * @return ArrayList
     */
    public function getTaxTotal(bool $excludeCharges = false) : ArrayList
    {
        $cacheKey = (int) $excludeCharges;
        if (!array_key_exists($cacheKey, $this->taxTotalList)) {
            $taxRates = $this->getTaxRatesWithFees();
            if (!$excludeCharges
             && $this->HasChargesAndDiscountsForTotal()
            ) {
                foreach ($this->ChargesAndDiscountsForTotal() as $charge) {
                    if ($charge->Tax === false) {
                        continue;
                    }
                    $taxRate = $taxRates->filter('Rate', $charge->Tax->Rate)->first();
                    if ($taxRate instanceof ViewableData) {
                        $amount = $charge->Price->getAmount();
                        if (Config::PriceType() === Config::PRICE_TYPE_GROSS) {
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
     * @param array $excludeModules               An array of registered modules that shall not be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted
     *
     * @return DBMoney
     */
    public function getNonTaxableAmount(array $excludeModules = [], array $excludeShoppingCartPositions = []) : DBMoney
    {
        $amount = 0;
        $registeredModules = $this->callMethodOnRegisteredModules(
            'ShoppingCartPositions',
            [
                $this,
                Customer::currentUser(),
                false,
                $excludeShoppingCartPositions
            ],
            $excludeModules
        );
        // Registered Modules
        foreach ($registeredModules as $modulePositions) {
            foreach ($modulePositions as $modulePosition) {
                $amount += (float) $modulePosition->PriceTotal;
            }
        }
        return DBMoney::create()
                ->setAmount($amount)
                ->setCurrency(Config::DefaultCurrency());
    }

    /**
     * Returns the handling costs for the chosen payment method.
     *
     * @return DBMoney
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.2019
     */
    public function HandlingCostPayment() : DBMoney
    {
        $paymentMethod = $this->getPaymentMethod();
        if ($paymentMethod instanceof PaymentMethod
         && $paymentMethod->exists()
         && $paymentMethod->getHandlingCost() instanceof HandlingCost
        ) {
            $handlingCostPayment = $paymentMethod->getHandlingCost();
        } else {
            $paymentDefaultCost = DBMoney::create();
            $paymentDefaultCost->setAmount(0);
            $paymentDefaultCost->setCurrency(Config::DefaultCurrency());
            $handlingCostPayment = HandlingCost::create();
            $handlingCostPayment->amount = $paymentDefaultCost;
        }
        return $handlingCostPayment->amount;
    }

    /**
     * Returns the handling costs for the chosen payment method excluding tax.
     *
     * @return DBMoney
     */
    public function HandlingCostPaymentNet() : DBMoney
    {
        $handlingCost    = $this->HandlingCostPayment();
        $handlingCostNet = DBMoney::create()
                ->setAmount($handlingCost->getAmount())
                ->setCurrency($handlingCost->getCurrency());
        if ($handlingCost->getAmount() > 0) {
            $handlingCostPayment = $this->getPaymentMethod()->getHandlingCost();
            $amountNet           = ($handlingCost->getAmount() / (100 + $handlingCostPayment->Tax->Rate)) * 100;
            $handlingCostNet->setAmount($amountNet);
        }
        return $handlingCostNet;
    }

    /**
     * Returns the handling costs for the chosen shipping method.
     *
     * @return DBMoney
     */
    public function HandlingCostShipment() : DBMoney
    {
        $handlingCostShipment   = 0;
        $selectedShippingMethod = $this->getShippingMethod();
        if ($selectedShippingMethod instanceof ShippingMethod
         && $selectedShippingMethod->exists()
         && $selectedShippingMethod->getShippingFee() instanceof ShippingFee
        ) {
            $handlingCostShipmentObj = $selectedShippingMethod->getShippingFee()->getCalculatedPrice();
        } else {
            $handlingCostShipmentObj = DBMoney::create();
            $handlingCostShipmentObj->setAmount($handlingCostShipment);
            $handlingCostShipmentObj->setCurrency(Config::DefaultCurrency());
        }
        return $handlingCostShipmentObj;
    }
    
    /**
     * Returns whether there are handling costs for payment.
     * 
     * @return bool
     */
    public function hasHandlingCostPayment() : bool
    {
        return $this->HandlingCostPayment()->getAmount() > 0;
    }
    
    /**
     * Returns whether there are handling costs for shipment.
     * 
     * @return bool
     */
    public function hasHandlingCostShipment() : bool
    {
        return $this->HandlingCostShipment()->getAmount() > 0;
    }

    /**
     * Returns the shipping method title.
     *
     * @return string
     */
    public function CarrierAndShippingMethodTitle() : string
    {
        $title                  = '';
        $selectedShippingMethod = $this->getShippingMethod();
        if ($selectedShippingMethod instanceof ShippingMethod) {
            $title = "{$selectedShippingMethod->Carrier()->Title} - {$selectedShippingMethod->Title}";
        }
        return $title;
    }

    /**
     * Returns the payment method object.
     *
     * @return PaymentMethod
     * 
     * @deprecated
     */
    public function getPayment() {
        return $this->getPaymentMethod();
    }
    
    /**
     * Returns the shipping method
     *
     * @return ShippingMethod|null
     */
    public function getShippingMethod() : ?ShippingMethod
    {
        $shippingMethod = null;
        if (is_numeric($this->ShippingMethodID)) {
            $shippingMethod = ShippingMethod::get()->byID($this->ShippingMethodID);
        }
        return $shippingMethod;
    }
    
    /**
     * Returns the cheapest shipping method for the current cart context.
     * 
     * @param Country $country Optional country context.
     * 
     * @return ShippingMethod|null
     */
    public function getCheapestShippingMethod(Country $country = null) : ?ShippingMethod
    {
        $this->setShippingCountry($country);
        $address = Address::create();
        $address->CountryID = $this->getShippingCountry()->ID;
        $allowed         = ShippingMethod::getAllowedShippingMethods(null, $address);
        $cheapest        = null;
        $weight          = $this->getWeightTotal();
        $excludeIsPickup = false;
        if ($allowed->filter('isPickup', true)->count() < $allowed->count()) {
            $excludeIsPickup = true;
        }
        /* @var $cheapest ShippingMethod */
        foreach ($allowed as $shippingMethod) {
            /* @var $shippingMethod ShippingMethod */
            if ($shippingMethod->isPickup
             && $excludeIsPickup
            ) {
                continue;
            }
            $shippingFee = $shippingMethod->getShippingFee($weight);
            /* @var $shippingFee ShippingMethod */
            if (is_null($cheapest)
             || $cheapest->getShippingFee($weight)->getPriceAmount() > $shippingFee->getPriceAmount()
            ) {
                $cheapest = $shippingMethod;
            }
        }
        return $cheapest;
    }
    
    /**
     * Returns a DropdownField to choose a shipping country.
     * 
     * @return DropdownField
     */
    public function getShippingCountryDropdown() : DropdownField
    {
        return DropdownField::create('ShippingCountryID', 'Shipping Country', Country::getPrioritiveDropdownMap(), $this->getShippingCountry()->ID);
    }
    
    /**
     * Sets the shipping country.
     * If no country is given, the HTTP POST request will be checked for a 
     * transmitted country ID.
     * 
     * @param Country $country Country
     * 
     * @return $this
     */
    public function setShippingCountry(Country $country = null) : ShoppingCart
    {
        $this->shippingCountry = $country;
        Customer::setCurrentShippingCountry($country);
        return $this;
    }
    
    /**
     * Returns the shipping country context.
     * 
     * @return Country|null
     */
    public function getShippingCountry() : ?Country
    {
        return Customer::currentShippingCountry();
    }
    
    /**
     * Returns the payment method
     *
     * @return PaymentMethod|null
     */
    public function getPaymentMethod() : ?PaymentMethod
    {
        $paymentMethod = null;
        if (is_numeric($this->PaymentMethodID)) {
            $paymentMethod = PaymentMethod::get()->byID($this->PaymentMethodID);
        }
        return $paymentMethod;
    }

    /**
     * Returns the minimum order value.
     *
     * @return string
     */
    public function MinimumOrderValue() : string
    {
        $minimumOrderValue = DBMoney::create();
        if (Config::UseMinimumOrderValue()
         && Config::MinimumOrderValue()
        ) {
            $minimumOrderValue->setAmount(Config::MinimumOrderValue()->getAmount());
            $minimumOrderValue->setCurrency(Config::MinimumOrderValue()->getCurrency());
        }
        return $minimumOrderValue->Nice();
    }

    /**
     * Indicates wether the minimum order value is reached.
     *
     * @return bool
     */
    public function IsMinimumOrderValueReached() : bool
    {
        $is = true;
        if (Config::UseMinimumOrderValue()
         && Config::MinimumOrderValue()
         && Config::MinimumOrderValue()->getAmount() > $this->getAmountTotalWithoutFees()->getAmount()
        ) {
            $is = false;
        }
        return $is;
    }
    
    /**
     * In case stock management is enabled: Find out if all positions quantities
     * are still in stock
     * 
     * @return bool
     */
    public function isAvailableInStock() : bool
    {
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
     * @return ArrayData
     */
    public function getDeliveryTimeData(int $shippingMethodID = 0, bool $forceDisplayInDays = false) : ArrayData
    {
        $key = "{$shippingMethodID}-" . ($forceDisplayInDays ? 1 : 0);
        if (!array_key_exists($key, $this->deliveryTimeData)) {
            $deliveryDaysMin  = 0;
            $deliveryDaysMax  = 0;
            $deliveryDaysText = '';
            if ($shippingMethodID != 0) {
                $shippingMethod = ShippingMethod::get()->byID($shippingMethodID);
            } else {
                $shippingMethod = $this->getShippingMethod();
            }
            if ($shippingMethod instanceof ShippingMethod
             && $shippingMethod->exists()
             && $shippingMethod->getShippingFee() instanceof ShippingFee
             && $shippingMethod->getShippingFee()->exists()
            ) {
                $deliveryDaysMin  = (int) $shippingMethod->getShippingFee()->DeliveryTimeMin;
                $deliveryDaysMax  = (int) $shippingMethod->getShippingFee()->DeliveryTimeMax;
                $deliveryDaysText = $shippingMethod->getShippingFee()->DeliveryTimeText;
                if ($deliveryDaysMin == 0) {
                    $deliveryDaysMin = $shippingMethod->DeliveryTimeMin;
                }
                if ($deliveryDaysMax == 0) {
                    $deliveryDaysMax = $shippingMethod->DeliveryTimeMax;
                }
                if (empty($deliveryDaysText)) {
                    $deliveryDaysText = $shippingMethod->DeliveryTimeText;
                }
            } elseif ($shippingMethod instanceof ShippingMethod
                   && $shippingMethod->exists()
            ) {
                $deliveryDaysMin  = $shippingMethod->DeliveryTimeMin;
                $deliveryDaysMax  = $shippingMethod->DeliveryTimeMax;
                $deliveryDaysText = $shippingMethod->DeliveryTimeText;
            }
            $productDeliveryDaysMin = 0;
            $productDeliveryDaysMax = 0;
            foreach ($this->ShoppingCartPositions() as $position) {
                /* @var $position ShoppingCartPosition */
                /* @var $product Product */
                $product = $position->Product();
                if ($product->StockQuantity < $position->Quantity) {
                    $min = $product->getPurchaseMinDurationDays();
                    $max = $product->getPurchaseMaxDurationDays();
                    if ($min > $productDeliveryDaysMin) {
                        $productDeliveryDaysMin = $min;
                    }
                    if ($max > $productDeliveryDaysMax) {
                        $productDeliveryDaysMax = $max;
                    }
                }
                if ($product->HasReleaseDate()) {
                    $businessDays = DateTools::getBusinessDaysUntil($product->ReleaseDate);
                    if ($businessDays > $productDeliveryDaysMin) {
                        $productDeliveryDaysMin = $businessDays;
                    }
                    if ($businessDays > $productDeliveryDaysMax) {
                        $productDeliveryDaysMax = $businessDays;
                    }
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
            $this->deliveryTimeData[$key] = ArrayData::create([
                'Min'  => $deliveryDaysMin,
                'Max'  => $deliveryDaysMax,
                'Text' => $deliveryDaysText,
            ]);
            $this->extend('updateDeliveryTimeData', $this->deliveryTimeData[$key]);
        }
        return $this->deliveryTimeData[$key];
    }
    
    /**
     * Returns the minimum delivery time as date string YYYY-MM-DD.
     * 
     * @param int  $shippingMethodID   ID of the shipping method to use for delivery
     * @param bool $forceDisplayInDays Force displaying the delivery time in days
     * 
     * @return DBHTMLText
     */
    public function getDeliveryTime(int $shippingMethodID = 0, bool $forceDisplayInDays = false) : DBHTMLText
    {
        $deliveryTimeData = $this->getDeliveryTimeData($shippingMethodID, $forceDisplayInDays);
        $deliveryTime     = ShippingMethod::get_delivery_time(
                $deliveryTimeData->Min,
                $deliveryTimeData->Max,
                $deliveryTimeData->Text,
                $forceDisplayInDays
        );
        $this->extend('updateDeliveryTime', $deliveryTime);
        return DBHTMLText::create()->setValue($deliveryTime);
    }
    
    /**
     * Returns the minimum delivery time as date string YYYY-MM-DD.
     * 
     * @param int  $shippingMethodID   ID of the shipping method to use for delivery
     * @param bool $forceDisplayInDays Force displaying the delivery time in days
     * 
     * @return string
     */
    public function getDeliveryTimeMin(int $shippingMethodID = 0, bool $forceDisplayInDays = false) : string
    {
        $deliveryTimeData = $this->getDeliveryTimeData($shippingMethodID, $forceDisplayInDays);
        $deliveryTimeMin  = date('Y-m-d', time() + (DateTools::addOffDaysToBusinessDays($deliveryTimeData->Min, ShippingMethod::config()->include_saturdays_in_delivery_time)*60*60*24));
        return $deliveryTimeMin;
    }
    
    /**
     * Returns the maximum delivery time as date string YYYY-MM-DD.
     * 
     * @param int  $shippingMethodID   ID of the shipping method to use for delivery
     * @param bool $forceDisplayInDays Force displaying the delivery time in days
     * 
     * @return string
     */
    public function getDeliveryTimeMax(int $shippingMethodID = 0, bool $forceDisplayInDays = false) : string
    {
        $deliveryTimeData = $this->getDeliveryTimeData($shippingMethodID, $forceDisplayInDays);
        $deliveryTimeMax  = date('Y-m-d', time() + (DateTools::addOffDaysToBusinessDays($deliveryTimeData->Max, ShippingMethod::config()->include_saturdays_in_delivery_time)*60*60*24));
        return $deliveryTimeMax;
    }

    /**
     * Returns the end sum of the cart (taxable positions + nontaxable
     * positions + fees).
     *
     * @param array $excludeModules               An array of registered modules that shall not be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted
     * @param bool  $excludeCharges               Indicates wether to exlude charges and discounts
     * 
     * @return DBMoney
     */
    public function getAmountTotal(array $excludeModules = [], array $excludeShoppingCartPositions = [], $excludeCharges = false) : DBMoney
    {
        $this->extend('onBeforeGetAmountTotal');
        if (Config::PriceType() === Config::PRICE_TYPE_GROSS) {
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
     * @param array $excludeModules               An array of registered modules that shall not be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted
     * @param bool  $excludeCharges               Indicates wether to exlude charges and discounts
     * 
     * @return DBMoney
     */
    public function getAmountTotalGross(array $excludeModules = [], array $excludeShoppingCartPositions = [], $excludeCharges = false) : DBMoney
    {
        $amount  = $this->getTaxableAmountGrossWithFees($excludeShoppingCartPositions)->getAmount();
        $amount += $this->getNonTaxableAmount($excludeModules, $excludeShoppingCartPositions)->getAmount();
        // Handling costs for payment and shipment
        if (!$excludeCharges
         && $this->HasChargesAndDiscountsForTotal()
        ) {
            $amount += $this->ChargesAndDiscountsForTotal(Config::PRICE_TYPE_GROSS)->Price->getAmount();
        }
        return DBMoney::create()
                ->setAmount($amount)
                ->setCurrency(Config::DefaultCurrency());
    }

    /**
     * Returns the end sum of the cart (taxable positions + nontaxable
     * positions + fees) excluding vat.
     *
     * @param array $excludeModules               An array of registered modules that shall not be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted
     * @param bool  $excludeCharges               Indicates wether to exlude charges and discounts
     * 
     * @return DBMoney
     */
    public function getAmountTotalNet(array $excludeModules = [], array $excludeShoppingCartPositions = [], $excludeCharges = false) : DBMoney
    {
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
     * @param array $excludeModules               An array of registered modules that shall not be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted
     * @param bool  $excludeCharges               Indicates wether to exlude charges and discounts
     * 
     * @return DBMoney
     */
    public function getAmountTotalNetWithoutVat(array $excludeModules = [], array $excludeShoppingCartPositions = [], $excludeCharges = false) : DBMoney
    {
        $amount  = $this->getTaxableAmountNetWithFees($excludeShoppingCartPositions)->getAmount();
        $amount += $this->getNonTaxableAmount($excludeModules, $excludeShoppingCartPositions)->getAmount();
        // Handling costs for payment and shipment
        if (!$excludeCharges
         && $this->HasChargesAndDiscountsForTotal()
        ) {
            $amount += $this->ChargesAndDiscountsForTotal(Config::PRICE_TYPE_NET)->Price->getAmount();
        }
        if (round($amount, 2) === 0.00) {
            $amount = round($amount, 2);
        }
        return DBMoney::create()
                ->setAmount($amount)
                ->setCurrency(Config::DefaultCurrency());
    }

    /**
     * Returns the end sum of the cart (taxable positions + nontaxable
     * positions + fees) without any taxes.
     *
     * @param array $excludeModules               An array of registered modules that shall not be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted
     * @param bool  $excludeCharges               Indicates wether to exlude charges and discounts
     * 
     * @return DBMoney
     */
    public function getAmountTotalWithoutTaxes(array $excludeModules = [], array $excludeShoppingCartPositions = [], $excludeCharges = false) : DBMoney
    {
        $amount  = $this->getTaxableAmountGrossWithFees($excludeShoppingCartPositions)->getAmount();
        $amount += $this->getNonTaxableAmount($excludeModules, $excludeShoppingCartPositions)->getAmount();
        // Handling costs for payment and shipment
        if (!$excludeCharges
         && $this->ChargesAndDiscountsForTotal()
        ) {
            $amount += $this->ChargesAndDiscountsForTotal()->Price->getAmount();
        }
        if (round($amount, 2) == 0.00) {
            $amount = round($amount, 2);
        }
        return DBMoney::create()
                ->setAmount($amount)
                ->setCurrency(Config::DefaultCurrency());
    }
    
    /**
     * Returns the end sum of the cart without fees based on shop settings for net or gross price type
     * 
     * @param array $excludeModules               An array of registered modules that shall not be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted
     * @param bool  $excludeCharges               Indicates wether to exlude charges and discounts
     * 
     * @return DBMoney
     */
    public function getAmountTotalWithoutFees(array $excludeModules = [], array $excludeShoppingCartPositions = [], $excludeCharges = false) : DBMoney
    {
        if (Config::Pricetype() === Config::PRICE_TYPE_GROSS) {
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
     * @param array $excludeModules               An array of registered modules that shall not be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted
     * @param bool  $excludeCharges               Indicates wether to exlude charges and discounts
     * 
     * @return DBMoney
     */
    public function getAmountTotalGrossWithoutFees(array $excludeModules = [], array $excludeShoppingCartPositions = [], $excludeCharges = false) : DBMoney
    {
        $amount  = $this->getTaxableAmountGrossWithoutFees($excludeModules, $excludeShoppingCartPositions, $excludeCharges)->getAmount();
        $amount += $this->getNonTaxableAmount($excludeModules, $excludeShoppingCartPositions)->getAmount();
        if (round($amount, 2) == 0.00) {
            $amount = round($amount, 2);
        }
        return DBMoney::create()
                ->setAmount($amount)
                ->setCurrency(Config::DefaultCurrency());
    }

    /**
     * Returns the end sum of the cart without fees (taxable positions +
     * nontaxable positions).
     *
     * @param array $excludeModules               An array of registered modules that shall not be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted
     * @param bool  $excludeCharges               Indicates wether to exlude charges and discounts
     * 
     * @return DBMoney
     */
    public function getAmountTotalNetWithoutFees(array $excludeModules = [], array $excludeShoppingCartPositions = [], $excludeCharges = false) : DBMoney
    {
        $amount  = $this->getTaxableAmountNetWithoutFees($excludeModules, $excludeShoppingCartPositions, $excludeCharges)->getAmount();
        $amount += $this->getNonTaxableAmount($excludeModules, $excludeShoppingCartPositions)->getAmount();
        if (round($amount, 2) == 0.00) {
            $amount = round($amount, 2);
        }
        return DBMoney::create()
                ->setAmount($amount)
                ->setCurrency(Config::DefaultCurrency());
    }
    
    /**
     * Returns the tax rates for shipping and payment fees.
     * 
     * @return ArrayList
     */
    public function getTaxRatesForFees() : ArrayList
    {
        $taxes          = ArrayList::create();
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
            $taxes->push(ArrayData::create([
                'Rate'      => $taxRate,
                'AmountRaw' => $taxAmount,
            ]));
        }
        foreach ($taxes as $tax) {
            $tax->Amount = DBMoney::create()
                    ->setAmount($tax->AmountRaw)
                    ->setCurrency(Config::DefaultCurrency());
        }
        return $taxes;
    }

    /**
     * Returns tax amounts included in the shoppingcart separated by tax rates
     * with fee taxes.
     *
     * @return ArrayList
     */
    public function getTaxRatesWithFees() : ArrayList
    {
        if (is_null($this->taxRatesWithFees)) {
            $taxes          = $this->getTaxRatesWithoutFees();
            $shippingMethod = $this->getShippingMethod();
            $paymentMethod  = $this->getPaymentMethod();
            if ($shippingMethod instanceof ShippingMethod) {
                $shippingFee = $shippingMethod->getShippingFee();
                if ($shippingFee instanceof ShippingFee) {
                    $taxRate = $shippingFee->getTaxRate();
                    if ($taxRate > 0
                     && !$taxes->find('Rate', $taxRate)
                    ) {
                        $taxes->push(ArrayData::create([
                            'Rate'         => $taxRate,
                            'AmountRaw'    => 0.0,
                            'OriginalRate' => $taxRate,
                        ]));
                    }
                    $taxSection = $taxes->find('Rate', $taxRate);
                    $taxSection->AmountRaw += $shippingFee->getTaxAmount();
                }
            }
            if ($paymentMethod instanceof PaymentMethod) {
                $paymentFee = $paymentMethod->getHandlingCost();
                if ($paymentFee instanceof HandlingCost
                 && $paymentFee->Tax()->exists()
                ) {
                    $taxRate = $paymentFee->Tax()->getTaxRate();
                    if ($taxRate > 0
                     && !$taxes->find('Rate', $taxRate)
                    ) {
                        $taxes->push(ArrayData::create([
                            'Rate'         => $taxRate,
                            'AmountRaw'    => 0.0,
                            'OriginalRate' => $taxRate,
                        ]));
                    }
                    $taxSection             = $taxes->find('Rate', $taxRate);
                    $taxSection->AmountRaw += $paymentFee->getTaxAmount();
                }
            }
            foreach ($taxes as $tax) {
                $tax->Amount = DBMoney::create()
                        ->setAmount(round($tax->AmountRaw, 2))
                        ->setCurrency(Config::DefaultCurrency());
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
    public function getTaxRatesWithoutFees() : ArrayList
    {
        $taxes = $this->getTaxRatesWithoutFeesAndCharges();
        // Charges and disounts
        $chargesAndDiscounts = $this->ChargesAndDiscountsForProducts();
        if ($this->HasChargesAndDiscountsForProducts()) {
            $mostValuableTaxRate = $this->getMostValuableTaxRate($taxes);
            if ($mostValuableTaxRate) {
                $taxSection              = $taxes->find('Rate', $mostValuableTaxRate->Rate);
                $chargeAndDiscountAmount = $chargesAndDiscounts->Price->getAmount();
                if (Config::PriceType() === Config::PRICE_TYPE_GROSS) {
                    $taxSection->AmountRaw += $chargeAndDiscountAmount - ($chargeAndDiscountAmount / (100 + $taxSection->Rate) * 100);
                } else {
                    $taxSection->AmountRaw += ($chargeAndDiscountAmount / 100 * (100 + $taxSection->Rate)) - $chargeAndDiscountAmount;
                }
            }
        }
        foreach ($taxes as $tax) {
            $tax->Amount = DBMoney::create()
                    ->setAmount($tax->AmountRaw)
                    ->setCurrency(Config::DefaultCurrency());
        }
        return $taxes;
    }
    
    /**
     * Returns tax amounts included in the shoppingcart separated by tax rates
     * without fee taxes.
     *
     * @param array $excludeModules               An array of registered modules that shall not be taken into account.
     * @param array $excludeShoppingCartPositions Positions that shall not be counted
     *
     * @return ArrayList
     */
    public function getTaxRatesWithoutFeesAndCharges(array $excludeModules = [], array $excludeShoppingCartPositions = []) : ArrayList
    {
        $positions         = $this->ShoppingCartPositions();
        $taxes             = ArrayList::create();
        $registeredModules = $this->callMethodOnRegisteredModules(
            'ShoppingCartPositions',
            [
                Customer::currentUser()->getCart(),
                Customer::currentUser(),
                true,
                $excludeShoppingCartPositions,
            ],
            $excludeModules
        );
        // products
        $this->extend('overwritePositionTaxRates', $taxes, $positions);
        foreach ($positions as $position) {
            $taxRate         = $position->Product()->getTaxRate();
            $originalTaxRate = $position->Product()->getTaxRate(true);
            if (!$taxes->find('Rate', $taxRate)) {
                $taxes->push(ArrayData::create([
                        'Rate'         => $taxRate,
                        'OriginalRate' => $originalTaxRate,
                        'AmountRaw'    => (float) 0.0,
                ]));
            }
            $taxSection = $taxes->find('Rate', $taxRate);
            $taxSection->AmountRaw += $position->getTaxAmount();
        }
        // Registered Modules
        foreach ($registeredModules as $moduleOutput) {
            foreach ($moduleOutput as $modulePosition) {
                $taxRate = $modulePosition->TaxRate;
                if (!$taxes->find('Rate', $taxRate)) {
                    $taxes->push(ArrayData::create([
                        'Rate'         => $taxRate,
                        'OriginalRate' => $taxRate,
                        'AmountRaw'    => (float) 0.0,
                    ]));
                }
                $taxSection = $taxes->find('Rate', $taxRate);
                $taxAmount = $modulePosition->TaxAmount;
                $taxSection->AmountRaw = round($taxSection->AmountRaw + $taxAmount, 4);
            }
        }
        foreach ($taxes as $tax) {
            $tax->Amount = DBMoney::create()
                    ->setAmount($tax->AmountRaw)
                    ->setCurrency(Config::DefaultCurrency());
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
    public static function get_most_valuable_tax_rate($taxes = null)
    {
        $rate = false;
        if (Customer::currentUser()
         && Customer::currentUser()->ShoppingCartID > 0
        ) {
            $shoppingCart = Customer::currentUser()->getCart();
            $taxRate      = $shoppingCart->getMostValuableTaxRate($taxes);
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
    public function getMostValuableTaxRate($taxes = null)
    {
        if (is_null($taxes)) {
            $taxes = $this->getTaxRatesWithoutFeesAndCharges($this->config()->exclude_modules_from_most_valuable_tax_rate);
        }
        $highestTaxValue             = 0;
        $mostValuableTaxRate         = null;
        $originalMostValuableTaxRate = null;
        foreach ($taxes as $tax) {
            if ($tax->AmountRaw >= $highestTaxValue) {
                $highestTaxValue             = $tax->AmountRaw;
                $mostValuableTaxRate         = $tax->Rate;
                $originalMostValuableTaxRate = $tax->OriginalRate;
            }
        }
        if (!is_null($originalMostValuableTaxRate)) {
            $silvercartTax = Tax::get()->filter('Rate', $originalMostValuableTaxRate)->first();
            if ($silvercartTax instanceof Tax) {
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
     * @return int|bool
     */
    public function getWeightTotal()
    {
        $positions   = $this->ShoppingCartPositions();
        $totalWeight = 0;
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
     * @return bool
     */
    public function getShowFees() : bool
    {
        $showFees = false;
        if ($this->ShippingMethodID > 0
         && $this->PaymentMethodID > 0
        ) {
            $showFees = true;
        }
        return $showFees;
    }

    /**
     * Indicates wether the fees for shipping and payment should be shown.
     *
     * @return bool
     */
    public function getHasFeesOrChargesOrModules() : bool
    {
        $hasAnything       = false;
        $registeredModules = $this->registeredModules();
        if ($this->getShowFees()
         || $this->HasChargesAndDiscountsForProducts()
         || $this->HasChargesAndDiscountsForTotal()
         || $registeredModules->NonTaxableShoppingCartPositions
        ) {
            $hasAnything = true;
        }
        return $hasAnything;
    }

    /**
     * deletes all shopping cart positions when cart is deleted
     *
     * @return void
     */
    public function onBeforeDelete() : void
    {
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
     */
    public static function registerModule(string $module) : void
    {
        array_push(self::$registeredModules, $module);
    }

    /**
     * Returns all registered modules.
     *
     * Every module contains two keys for further iteration inside templates:
     *      - ShoppingCartPositions
     *      - ShoppingCartActions
     *
     * @return ArrayList
     */
    public function registeredModules() : ArrayList
    {
        if (is_null($this->registeredModulesSet)) {
            $customer          = Customer::currentUser();
            $modules           = [];
            $registeredModules = self::$registeredModules;
            $hookMethods       = [
                'NonTaxableShoppingCartPositions',
                'TaxableShoppingCartPositions',
                'IncludedInTotalShoppingCartPositions',
                'ShoppingCartActions',
                'ShoppingCartTotal',
                'CustomShoppingCartPositions',
            ];
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
                    $hooks = [];
                    foreach ($hookMethods as $hookMethod) {
                        if ($registeredModuleObj->hasMethod($hookMethod)) {
                            $hooks[$hookMethod] = $registeredModuleObj->$hookMethod($this, $customer);
                        }
                    }
                    $modules[] = $hooks;
                }
            }
            $this->registeredModulesSet = ArrayList::create($modules);
        }
        return $this->registeredModulesSet;
    }

    /**
     * Calls a method on all registered modules and returns its output.
     *
     * @param string $methodName                   The name of the method to call
     * @param array  $parameters                   Additional parameters for the method call
     * @param array  $excludeModules               An array of registered modules that shall not be taken into account.
     * @param array  $excludeShoppingCartPositions Positions that shall not be counted; can contain the ID or the className of the position
     *
     * @return array
     */
    public function callMethodOnRegisteredModules(string $methodName, array $parameters = [], array $excludeModules = [], array $excludeShoppingCartPositions = []) : array
    {
        $registeredModules = self::$registeredModules;
        $outputOfModules   = [];
        foreach ($registeredModules as $registeredModule) {
            // Skip excluded modules
            if (in_array($registeredModule, $excludeModules)) {
                continue;
            }
            $singleton = singleton($registeredModule);
            $objects   = ArrayList::create();
            if ($singleton->hasMethod('loadObjectsForShoppingCart')) {
                $objects = $singleton->loadObjectsForShoppingCart($this);
            } elseif ($singleton->hasMethod('loadObjectForShoppingCart')) {
                $object = $singleton->loadObjectForShoppingCart($this);
                if (is_object($object)) {
                    $objects->push($object);
                }
            } else {
                $objects->push($singleton);
            }
            if ($objects->exists()) {
                foreach ($objects as $object) {
                    if ($object->hasMethod($methodName)) {
                        if (!empty($excludeShoppingCartPositions)) {
                            $parameters['excludeShoppingCartPositions'] = $excludeShoppingCartPositions;
                        }
                        $outputOfModules[$registeredModule] = call_user_func_array([
                            $object,
                            $methodName
                        ], $parameters);
                    }
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
    public function setShippingMethodID(int $shippingMethodId) : void
    {
        $this->ShippingMethodID = $shippingMethodId;
    }

    /**
     * Set the ID of the payment method the customer has chosen.
     *
     * @param Int $paymentMethodId The ID of the payment method object.
     *
     * @return void
     */
    public function setPaymentMethodID(int $paymentMethodId) : void
    {
        $this->PaymentMethodID = $paymentMethodId;
    }

    /**
     * determine wether a cart is filled or empty; useful for template conditional
     *
     * @return bool
     */
    public function isFilled() : bool
    {
        $positionTable = Tools::get_table_name(ShoppingCartPosition::class);
        $records       = DB::query("SELECT COUNT(Pos.ID) AS NumberOfPositions FROM {$positionTable} Pos WHERE Pos.ShoppingCartID = {$this->ID}");
        $record        = $records->nextRecord();
        if ($record
         && $record['NumberOfPositions'] > 0
        ) {
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
     */
    public function adjustPositionQuantitiesToStockQuantities() : void
    {
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
     */
    public function resetPositionMessages() : void
    {
        $positions = $this->ShoppingCartPositions();
        if ($positions) {
            foreach ($positions as $position) {
                $position->resetMessageTokens();
            }
        }
    }
    
    /**
     * returns a string with notices. Notices are seperated by <br />
     * 
     * @return DBHTMLText
     */
    public function getShoppingCartPositionNotices() : DBHTMLText
    {
        return ShoppingCartPositionNotice::getNotices(0);
    }
    
    /**
     * Returns a list with notices.
     * 
     * @return ArrayList
     */
    public function getShoppingCartPositionNoticesList() : ArrayList
    {
        return ShoppingCartPositionNotice::getNoticesList(0);
    }
    
    /**
     * Is a notice set in the session?
     * 
     * @return bool
     */
    public function hasNotice() : bool
    {
        return ShoppingCartPositionNotice::hasNotices(0);
    }
}
