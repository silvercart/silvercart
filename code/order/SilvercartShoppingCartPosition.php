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
 * abstract for shopping cart positions
 *
 * @package Silvercart
 * @subpackage Order
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 22.11.2010
 * @license see license file in modules root directory
 */
class SilvercartShoppingCartPosition extends DataObject {
    
    /**
     * attributes
     *
     * @var array
     */
    public static $db = array(
        'Quantity' => 'Decimal'
        
    );
    /**
     * n:m relations
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartProduct'      => 'SilvercartProduct',
        'SilvercartShoppingCart' => 'SilvercartShoppingCart'
    );

    /**
     * List of different accessed prices
     *
     * @var array
     */
    protected $prices = array();
    
    /**
     * List of different accessed isQuantityIncrementableBy calls
     *
     * @var array
     */
    protected $isQuantityIncrementableByList = array();
    
    /**
     * plugged in title
     *
     * @var string
     */
    protected $pluggedInTitle = null;
    
    /**
     * List of already initialized positions
     *
     * @var array
     */
    public static $initializedPositions = array();

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
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.11.2012
     */
    public function __construct($record = null, $isSingleton = false) {
        parent::__construct($record, $isSingleton);
        if ($this->ID > 0 &&
            !array_key_exists($this->ID, self::$initializedPositions)) {
            // Check if the installation is complete. If it's not complete we
            // can't access the SilvercartConfig data object (out of database)
            // because it's not build yet
            if (SilvercartTools::isInstallationCompleted()) {
                $this->adjustQuantityToStockQuantity();
            }

            self::$initializedPositions[$this->ID] = true;
        }
    }

    /**
     * Registers all CustomHtmlForms for this object.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.12.2012
     */
    public function registerCustomHtmlForms() {
        $controller     = Controller::curr();

        if ($controller->hasMethod('getRegisteredCustomHtmlForm')) {
            $positionForms  = array(
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
                                'positionID' => $this->ID,
                                'BlID'       => $controller->ID
                            )
                        )
                    );
                }
            }
        }

        $this->extend('updateCreateForms');
    }

    /**
     * Returns the translated singular name of the object.
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.06.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.06.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this);
    }

    /**
     * Returns the title of the shopping cart position.
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.02.2018
     */
    public function getTitle() {
        $title = $this->SilvercartProduct()->Title;
        $this->extend('updateTitle', $title);
        
        if (is_null($this->pluggedInTitle)) {
            $pluginTitle = SilvercartPlugin::call($this, 'overwriteGetTitle', null, false, '');
            if ($pluginTitle != '') {
                Deprecation::notice('4.0', 'SilvercartPlugin::call overwriteGetTitle is deprecated. Please use a DataExtension with the method "updateTitle(&$title)" instead.');
            } else {
                $pluginTitle = $title;
            }
            $title = $this->pluggedInTitle = $pluginTitle;
        }
        
        return $title;
    }

    /**
     * Returns the title of the shopping cart position to display in a widget.
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.07.2012
     */
    public function getTitleForWidget() {
        $titleForWidget = $this->getTitle();
        if (strlen($titleForWidget) > 60) {
            $titleForWidget = substr($titleForWidget, 0, 57) . '...';
        }
        return $titleForWidget;
    }
    
    /**
     * Alias for self::SilvercartShoppingCart().
     * 
     * @return SilvercartShoppingCart
     */
    public function getCart() {
        return $this->SilvercartShoppingCart();
    }

    /**
     * Returns additional tile information provided by plugins
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.04.2012
     */
    public function addToTitle() {
        $addToTitle = SilvercartPlugin::call($this, 'addToTitle', null, false, '');
        return $addToTitle;
    }

    /**
     * Returns additional tile information provided by plugins
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.07.2012
     */
    public function addToTitleForWidget() {
        $addToTitleForWidget = SilvercartPlugin::call($this, 'addToTitleForWidget', null, false, '');
        if (empty($addToTitleForWidget)) {
            $addToTitleForWidget = $this->addToTitle();
        }
        return $addToTitleForWidget;
    }

    /**
     * price sum of this position
     *
     * @param boolean $forSingleProduct Indicates wether the price for the total
     *                                  quantity of products should be returned
     *                                  or for one product only.
     * @param boolean $priceType        'gross' or 'net'. If undefined it'll be automatically chosen.
     * 
     * @return Money the price sum
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 19.11.2014
     */
    public function getPrice($forSingleProduct = false, $priceType = false) {
        $priceKey = (string) $forSingleProduct . '-' . (string) $priceType;

        if (!array_key_exists($priceKey, $this->prices)) {
            $pluginPriceObj = SilvercartPlugin::call($this, 'overwriteGetPrice', array($forSingleProduct), false, 'DataObject');

            if ($pluginPriceObj !== false) {
                return $pluginPriceObj;
            }

            $product = $this->SilvercartProduct();
            $price   = 0;

            if ($product && $product->getPrice($priceType)->getAmount()) {
                if ($forSingleProduct) {
                    $price = $product->getPrice($priceType)->getAmount();
                } else {
                    $price = $product->getPrice($priceType)->getAmount() * $this->Quantity;
                }
            }

            $priceObj = Money::create();
            $priceObj->setAmount($price);
            $priceObj->setCurrency(SilvercartConfig::DefaultCurrency());
            $this->extend('updatePrice', $priceObj);
            $this->prices[$priceKey] = $priceObj;
        }

        return $this->prices[$priceKey];
    }

    /**
     * Returns the formatted (Nice) summed price.
     *
     * @return string
     */
    public function getPriceNice() {
        $priceNice = '';
        $price     = $this->getPrice();

        if ($price) {
            $priceNice = $price->Nice();
        }
        $this->extend('updatePriceNice', $priceNice);

        return $priceNice;
    }

    /**
     * Returns the formatted (Nice) single price.
     *
     * @return string
     */
    public function getSinglePriceNice() {
        $priceNice = '';
        $price     = $this->getPrice(true);

        if ($price) {
            $priceNice = $price->Nice();
        }
        $this->extend('updateSinglePriceNice', $priceNice);

        return $priceNice;
    }

    /**
     * Returns the shop product number
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2012-12-18
     */
    public function getProductNumberShop() {
        $pluginObj = SilvercartPlugin::call($this, 'overwriteGetProductNumberShop');

        if (!empty($pluginObj)) {
            return $pluginObj;
        }

        return $this->SilvercartProduct()->ProductNumberShop;
    }

    /**
     * Returns the form for incrementing the amount of this position.
     *
     * @return string
     */
    public function getIncrementPositionQuantityForm() {
        return Controller::curr()->InsertCustomHtmlForm('SilvercartIncrementPositionQuantityForm' . $this->ID);
    }

    /**
     * Returns the form for decrementing the amount of this position.
     *
     * @return string
     */
    public function getDecrementPositionQuantityForm() {
        return Controller::curr()->InsertCustomHtmlForm('SilvercartDecrementPositionQuantityForm' . $this->ID);
    }

    /**
     * Returns the form for removing this position.
     *
     * @return string
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.06.2012
     */
    public function isQuantityIncrementableBy($quantity = 1) {
        if (!array_key_exists((int) $quantity, $this->isQuantityIncrementableByList)) {
            $isQuantityIncrementableBy  = true;
            $pluginResult               = SilvercartPlugin::call($this, 'overwriteIsQuantityIncrementableBy', $quantity, false, 'DataObject');

            if (is_null($pluginResult) &&
                SilvercartConfig::EnableStockManagement()) {
                $isQuantityIncrementableBy = false;
                if ($this->SilvercartProduct()->isStockQuantityOverbookable()) {
                    $isQuantityIncrementableBy = true;
                } elseif ($this->SilvercartProduct()->StockQuantity >= ($this->Quantity + $quantity)) {
                    $isQuantityIncrementableBy = true;
                }
            } elseif (!is_null($pluginResult) &&
                      is_bool($pluginResult)) {
                $isQuantityIncrementableBy = $pluginResult;
            }
            $this->isQuantityIncrementableByList[$quantity] = $isQuantityIncrementableBy;
        }
        return $this->isQuantityIncrementableByList[$quantity];
    }
    
    /**
     * returns a string with notices. Notices are seperated by <br />
     * 
     * @return string|false
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 8.8.2011
     */
    public function getShoppingCartPositionNotices() {
        $notices = Session::get("position".$this->ID);
        if (array_key_exists('codes', $notices)) {
            $text = "";
            foreach ($notices['codes'] as $code) {
                $text .= SilvercartShoppingCartPositionNotice::getNoticeText($code) . "<br />";
            }
            SilvercartShoppingCartPositionNotice::unsetNotices($this->ID);
            return $text;
        }
        return false;
    }

    /**
     * Returns the legally required description for shopping cart positions.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 31.07.2012
     */
    public function getCartDescription() {
        if (!SilvercartConfig::useProductDescriptionFieldForCart()) {
            $description = '';
        } else {
            if (SilvercartConfig::productDescriptionFieldForCart() == 'LongDescription') {
                $description = $this->SilvercartProduct()->LongDescription;
            } else {
                $description = $this->SilvercartProduct()->ShortDescription;
            }
        }

        return $description;
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

        if ($this->SilvercartProduct()->SilvercartQuantityUnit()->numberOfDecimalPlaces == 0) {
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
    public function getTaxAmount($forSingleProduct = false) {
        if (SilvercartConfig::PriceType() == 'gross') {
            $taxRate = $this->getPrice($forSingleProduct)->getAmount() -
                       ($this->getPrice($forSingleProduct)->getAmount() /
                        (100 + $this->SilvercartProduct()->getTaxRate()) * 100); 
        } else {
            $taxRate = $this->getPrice($forSingleProduct)->getAmount() *
                       ($this->SilvercartProduct()->getTaxRate() / 100);
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
    public function adjustQuantityToStockQuantity() {
        if (!SilvercartTools::isIsolatedEnvironment()) {
            if (SilvercartConfig::EnableStockManagement() && !$this->SilvercartProduct()->isStockQuantityOverbookable()) {
                if ($this->Quantity > $this->SilvercartProduct()->StockQuantity) {
                    $this->Quantity = $this->SilvercartProduct()->StockQuantity;
                    $this->write();
                    SilvercartShoppingCartPositionNotice::setNotice($this->ID, "adjusted");
                }
            }
        }
    }
    
    /**
     * Is a notice set in the session?
     * 
     * @return bool 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.7.2011
     */
    public function hasNotice() {
        if (Session::get("position".$this->ID)) {
            return true;
        }
        return false;
    }
    
    /**
     * We make this method extendable here.
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 17.11.2011
     */
    public function onAfterDelete() {
        parent::onAfterDelete();
        
        $this->extend('updateOnAfterDelete');
    }
    
    /**
     * We make this method extendable here.
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.06.2014
     */
    public function onBeforeDelete() {
        parent::onBeforeDelete();
        
        $this->getCart()->LastEdited = $this->LastEdited;
        $this->getCart()->write();
        
        $this->extend('updateOnBeforeDelete');
    }
    
    /**
     * We make this method extendable here.
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.06.2014
     */
    public function onAfterWrite() {
        parent::onAfterWrite();
        
        $this->getCart()->LastEdited = $this->LastEdited;
        $this->getCart()->write();
        
        $this->extend('updateOnAfterWrite');
    }
    
    /**
     * We make this method extendable here.
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 17.11.2011
     */
    public function onBeforeWrite() {
        parent::onBeforeWrite();
        
        $this->extend('updateOnBeforeWrite');
    }

    /**
     * This method gets called when the shopping cart of a customer gets
     * transferred to a new cart (e.g. during the registration process).
     *
     * @param SilvercartShoppingCartPosition $newShoppingCartPosition The new cart position
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 14.03.2012
     */
    public function transferToNewPosition($newShoppingCartPosition) {
        $this->extend('updateTransferToNewPosition', $newShoppingCartPosition);
    }
}
