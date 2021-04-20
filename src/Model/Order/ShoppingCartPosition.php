<?php

namespace SilverCart\Model\Order;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\DecrementPositionQuantityForm;
use SilverCart\Forms\IncrementPositionQuantityForm;
use SilverCart\Forms\RemovePositionForm;
use SilverCart\Model\Order\ShoppingCart;
use SilverCart\Model\Order\ShoppingCartPositionNotice;
use SilverCart\Model\Product\Product;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBDecimal;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\FieldType\DBMoney;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

/**
 * abstract for shopping cart positions.
 *
 * @package SilverCart
 * @subpackage Model_Order
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property float $Quantity       Quantity
 * @property int   $ProductID      Product ID
 * @property int   $ShoppingCartID ShoppingCart ID
 * 
 * @method Product      Product()      Returns the related Product.
 * @method ShoppingCart ShoppingCart() Returns the related ShoppingCart.
 */
class ShoppingCartPosition extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * attributes
     *
     * @var array
     */
    private static $db = [
        'Quantity' => DBDecimal::class,
    ];
    /**
     * n:m relations
     *
     * @var array
     */
    private static $has_one = [
        'Product'      => Product::class,
        'ShoppingCart' => ShoppingCart::class,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartShoppingCartPosition';
    /**
     * Summary fields
     *
     * @var array
     */
    private static $summary_fields = [
        'Quantity',
        'Product.ProductNumberShop',
        'Product.Title',
    ];
    /**
     * List of different accessed prices
     *
     * @var array
     */
    protected $prices = [];
    /**
     * List of different accessed isQuantityIncrementableBy calls
     *
     * @var array
     */
    protected $isQuantityIncrementableByList = [];
    /**
     * plugged in title
     *
     * @var string
     */
    protected $pluggedInTitle = null;

    /**
     * Returns the translated singular name of the object.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object.
     *
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this);
    }

    /**
     * Indicates wether the current user can delete this object.
     * 
     * @param Member $member Member to check permission for.
     *
     * @return bool
     */
    public function canDelete($member = null) : bool
    {
        $canDelete = false;
        if (is_null($member)) {
            $member = Security::getCurrentUser();
        }
        if ($member instanceof Member
         && $member->ID === $this->ShoppingCart()->Member()->ID
         && !is_null($this->ShoppingCart()->Member()->ID)
        ) {
            $canDelete = true;
        }
        $results = $this->extend('canDelete', $member);
        if ($results
         && is_array($results)
        ) {
            if(!min($results)) {
                $canDelete = false;
            }
        }
        return $canDelete;
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
            'MaxQuantityReached'        => _t(ShoppingCartPosition::class . '.MAX_QUANTITY_REACHED_MESSAGE', 'The maximum quantity of products for this position has been reached.'),
            'Product.ProductNumberShop' => _t(Product::class . '.PRODUCTNUMBER', 'Product number'),
            'Product.Title'             => _t(Product::class . '.SINGULARNAME', 'Product'),
            'Quantity'                  => _t(Product::class . '.QUANTITY', 'Quantity'),
            'QuantityAdded'             => _t(ShoppingCartPosition::class . '.QUANTITY_ADDED_MESSAGE', 'The product(s) were added to your cart.'),
            'QuantityAdjusted'          => _t(ShoppingCartPosition::class . '.QUANTITY_ADJUSTED_MESSAGE', 'The quantity of this position was adjusted to the currently available stock quantity.'),
            'PositionDeleted'           => _t(ShoppingCartPosition::class . '.PositionDeletedMessage', 'One or more positions were deleted from your shoppping cart because they are no more available.'),
            'RemainingQuantityAdded'    => _t(ShoppingCartPosition::class . '.REMAINING_QUANTITY_ADDED_MESSAGE', 'We do NOT have enough products in stock. We just added the remaining quantity to your cart.'),
        ]);
    }

    /**
     * Returns the title of the shopping cart position.
     * 
     * @return string
     */
    public function getTitle() : string
    {
        if (is_null($this->pluggedInTitle)) {
            $title = $this->Product()->Title;
            $this->extend('updateTitle', $title);
            $this->pluggedInTitle = $title;
        }
        return (string) $this->pluggedInTitle;
    }

    /**
     * Returns the title of the shopping cart position to display in a widget.
     * 
     * @return string
     */
    public function getTitleForWidget() : string
    {
        $titleForWidget = $this->getTitle();
        if (strlen($titleForWidget) > 60) {
            $titleForWidget = substr($titleForWidget, 0, 57) . '...';
        }
        return $titleForWidget;
    }
    
    /**
     * Alias for self::ShoppingCart().
     * 
     * @return ShoppingCart
     */
    public function getCart() : ShoppingCart
    {
        return $this->ShoppingCart();
    }

    /**
     * Returns additional tile information provided by plugins
     * 
     * @return DBHTMLText
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2018
     */
    public function addToTitle() : DBHTMLText
    {
        $addToTitle = '';
        $this->extend('addToTitle', $addToTitle);
        return Tools::string2html($addToTitle);
    }

    /**
     * Returns additional tile information provided by plugins
     * 
     * @return DBHTMLText
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2018
     */
    public function addToTitleForWidget() : DBHTMLText
    {
        $addToTitleForWidget = '';
        $this->extend('addToTitleForWidget', $addToTitleForWidget);
        if (empty($addToTitleForWidget)) {
            $addToTitleForWidget = $this->addToTitle();
        }
        return Tools::string2html($addToTitleForWidget);
    }
    
    /**
     * Clears the price cache.
     * 
     * @return ShoppingCartPosition
     */
    public function clearPriceCache() : ShoppingCartPosition
    {
        $this->prices = [];
        return $this;
    }

    /**
     * price sum of this position
     *
     * @param bool   $forSingleProduct Indicates wether the price for the total
     *                                  quantity of products should be returned
     *                                  or for one product only.
     * @param string $priceType        'gross' or 'net'. If undefined it'll be automatically chosen.
     * 
     * @return DBMoney
     */
    public function getPrice(bool $forSingleProduct = false, string $priceType = null) : DBMoney
    {
        $this->extend('onBeforeUpdatePrice', $forSingleProduct, $priceType);
        $priceKey = (string) $forSingleProduct . '-' . (string) $priceType;
        if (!array_key_exists($priceKey, $this->prices)) {
            $overwrittenPrice = null;
            $this->extend('overwriteGetPrice', $overwrittenPrice, $forSingleProduct, $priceType);
            if (!is_null($overwrittenPrice)) {
                return $overwrittenPrice;
            }
            $product = $this->Product();
            $price   = 0;
            if ($product instanceof Product
             && $product->getPrice($priceType)->getAmount()
            ) {
                if ($forSingleProduct) {
                    $price = $product->getPrice($priceType)->getAmount();
                } else {
                    $price = $product->getPrice($priceType)->getAmount() * $this->Quantity;
                }
            }
            $priceObj = DBMoney::create();
            $priceObj->setAmount($price);
            $priceObj->setCurrency(Config::DefaultCurrency());
            $this->extend('updatePrice', $priceObj, $forSingleProduct);
            $this->extend('onAfterUpdatePrice', $priceObj, $forSingleProduct);
            $this->prices[$priceKey] = $priceObj;
        }
        return $this->prices[$priceKey];
    }

    /**
     * Returns the formatted (Nice) summed price.
     *
     * @return DBHTMLText
     */
    public function getPriceNice() : DBHTMLText
    {
        $priceNice = '';
        $price     = $this->getPrice();
        if ($price) {
            $priceNice = DBHTMLText::create()->setValue($price->Nice());
        }
        $this->extend('updatePriceNice', $priceNice);
        return $priceNice;
    }

    /**
     * Returns the formatted (Nice) single price.
     *
     * @return DBHTMLText
     */
    public function getSinglePriceNice() : DBHTMLText
    {
        $priceNice = '';
        $price     = $this->getPrice(true);
        if ($price) {
            $priceNice = DBHTMLText::create()->setValue($price->Nice());
        }
        $this->extend('updateSinglePriceNice', $priceNice);
        return $priceNice;
    }

    /**
     * Returns the single price.
     *
     * @return DBMoney
     */
    public function getSinglePrice() : DBMoney
    {
        $price = $this->getPrice(true);
        $this->extend('updateSinglePrice', $price);
        return $price;
    }

    /**
     * Returns the shop product number
     *
     * @return string
     */
    public function getProductNumberShop() : string
    {
        $productNumber = (string) $this->Product()->ProductNumberShop;
        $this->extend('overwriteGetProductNumberShop', $productNumber);
        $this->extend('updateProductNumberShop', $productNumber);
        return (string) $productNumber;
    }

    /**
     * Returns the form for incrementing the amount of this position.
     *
     * @return IncrementPositionQuantityForm
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2017
     */
    public function IncrementPositionQuantityForm() : IncrementPositionQuantityForm
    {
        return IncrementPositionQuantityForm::create($this, Controller::curr());
    }

    /**
     * Returns the form for decrementing the amount of this position.
     *
     * @return DecrementPositionQuantityForm
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2017
     */
    public function DecrementPositionQuantityForm() : DecrementPositionQuantityForm
    {
        return DecrementPositionQuantityForm::create($this, Controller::curr());
    }

    /**
     * Returns the form for removing this position.
     *
     * @return RemovePositionForm
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2017
     */
    public function RemovePositionForm() : RemovePositionForm
    {
        return RemovePositionForm::create($this, Controller::curr());
    }

    /**
     * Returns the form for incrementing the amount of this position.
     *
     * @return IncrementPositionQuantityForm
     */
    public function getIncrementPositionQuantityForm() : IncrementPositionQuantityForm
    {
        return $this->IncrementPositionQuantityForm();
    }

    /**
     * Returns the form for decrementing the amount of this position.
     *
     * @return DecrementPositionQuantityForm
     */
    public function getDecrementPositionQuantityForm() : DecrementPositionQuantityForm
    {
        return $this->DecrementPositionQuantityForm();
    }

    /**
     * Returns the form for removing this position.
     *
     * @return RemovePositionForm
     */
    public function getRemovePositionForm() : RemovePositionForm
    {
        return $this->RemovePositionForm();
    }
    
    /**
     * Find out if the demanded quantity is in stock when stock management is enabled.
     * If stock management is disabled true will be returned.
     * 
     * @param integer $quantity The quantity of products
     * @param Product $product  Optional context product
     * 
     * @return bool Can this position be incremented
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.04.2018
     */
    public function isQuantityIncrementableBy($quantity = 1, Product $product = null) : bool
    {
        if (!array_key_exists((int) $quantity, $this->isQuantityIncrementableByList)) {
            if (is_null($product)) {
                $product = $this->Product();
            }
            $isQuantityIncrementableBy = true;

            if (Config::EnableStockManagement()) {
                $isQuantityIncrementableBy = false;
                if ($product->isStockQuantityOverbookable()) {
                    $isQuantityIncrementableBy = true;
                } elseif ($product->StockQuantity >= ($this->Quantity + $quantity)) {
                    $isQuantityIncrementableBy = true;
                }
            }
            $this->extend('overwriteIsQuantityIncrementableBy', $isQuantityIncrementableBy, $quantity, $product);
            $this->extend('updateIsQuantityIncrementableBy', $isQuantityIncrementableBy, $quantity, $product);
            $this->isQuantityIncrementableByList[$quantity] = $isQuantityIncrementableBy;
        }
        return $this->isQuantityIncrementableByList[$quantity];
    }
    
    /**
     * returns a string with notices. Notices are seperated by <br />
     * 
     * @return DBHTMLText
     */
    public function getShoppingCartPositionNotices() : DBHTMLText
    {
        $this->extend('onBeforeGetShoppingCartPositionNotices');
        return ShoppingCartPositionNotice::getNotices($this->ID);
    }
    
    /**
     * Returns a list of notices.
     * 
     * @return ArrayList
     */
    public function getShoppingCartPositionNoticesList() : ArrayList
    {
        $this->extend('onBeforeGetShoppingCartPositionNoticesList');
        return ShoppingCartPositionNotice::getNoticesList($this->ID);
    }

    /**
     * Returns the legally required description for shopping cart positions.
     *
     * @return string
     */
    public function getCartDescription() : string
    {
        if (!Config::useProductDescriptionFieldForCart()) {
            $description = '';
        } else {
            if (Config::productDescriptionFieldForCart() == 'LongDescription') {
                $description = $this->Product()->LongDescription;
            } else {
                $description = $this->Product()->ShortDescription;
            }
        }

        return (string) $description;
    }

    /**
     * Returns the quantity according to the Product quantity type setting.
     *
     * @return float|int
     */
    public function getTypeSafeQuantity()
    {
       $quantity = $this->Quantity;

        if ($this->Product()->QuantityUnit()->numberOfDecimalPlaces == 0) {
            $quantity = (int) $quantity;
        }

        return $quantity;
    }
    
    /**
     * returns the tax amount included in $this
     *
     * @param boolean $forSingleProduct Indicates wether the price for the total
     *                                  quantity of products should be returned
     *                                  or for one product only.
     * 
     * @return float
     */
    public function getTaxAmount($forSingleProduct = false) : float
    {
        if (Config::PriceType() === Config::PRICE_TYPE_GROSS) {
            $taxRate = $this->getPrice($forSingleProduct)->getAmount() -
                       ($this->getPrice($forSingleProduct)->getAmount() /
                        (100 + $this->Product()->getTaxRate()) * 100); 
        } else {
            $taxRate = $this->getPrice($forSingleProduct)->getAmount() *
                       ($this->Product()->getTaxRate() / 100);
        }
        return $taxRate;
    }

    /**
     * Decrement the positions quantity if it is higher than the stock quantity.
     * If this position has a quantity of 5 but the products stock quantity is
     * only 3 the positions quantity would be set to 3.
     * This happens only if the product is not overbookable.
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.11.2012
     */
    public function adjustQuantityToStockQuantity() : void
    {
        if (!Tools::isIsolatedEnvironment()) {
            if (Config::EnableStockManagement()
             && !$this->Product()->isStockQuantityOverbookable()
            ) {
                if ($this->Quantity > $this->Product()->StockQuantity) {
                    $this->Quantity = $this->Product()->StockQuantity;
                    $this->write();
                    if ($this->exists()) {
                        $noticeCode = ShoppingCartPositionNotice::NOTICE_CODE_ADJUSTED;
                    } else {
                        $noticeCode = ShoppingCartPositionNotice::NOTICE_CODE_DELETED;
                    }
                    ShoppingCartPositionNotice::setNotice($this->ID, $noticeCode);
                }
            }
        }
    }
    
    /**
     * Is a notice set in the session?
     * 
     * @return bool
     */
    public function hasNotice() : bool
    {
        $this->extend('onBeforeHasNotice');
        return ShoppingCartPositionNotice::hasNotices($this->ID);
    }
    
    /**
     * We make this method extendable here.
     *
     * @return void
     */
    public function onAfterDelete() : void
    {
        parent::onAfterDelete();
        $this->extend('updateOnAfterDelete');
    }
    
    /**
     * We make this method extendable here.
     *
     * @return void
     */
    public function onBeforeDelete() : void
    {
        parent::onBeforeDelete();
        $this->getCart()->LastEdited = $this->LastEdited;
        $this->getCart()->write();
        $this->extend('updateOnBeforeDelete');
    }
    
    /**
     * We make this method extendable here.
     *
     * @return void
     */
    public function onAfterWrite() : void
    {
        parent::onAfterWrite();
        $this->getCart()->LastEdited = $this->LastEdited;
        $this->getCart()->write();
        $this->extend('updateOnAfterWrite');
        if ($this->Quantity <= 0) {
            $this->delete();
        }
    }
    
    /**
     * We make this method extendable here.
     *
     * @return void
     */
    public function onBeforeWrite() : void
    {
        parent::onBeforeWrite();
        $this->extend('updateOnBeforeWrite');
    }

    /**
     * This method gets called when the shopping cart of a customer gets
     * transferred to a new cart (e.g. during the registration process).
     *
     * @param ShoppingCartPosition $newShoppingCartPosition The new cart position
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 14.03.2012
     */
    public function transferToNewPosition($newShoppingCartPosition) : void
    {
        $this->extend('updateTransferToNewPosition', $newShoppingCartPosition);
    }
}