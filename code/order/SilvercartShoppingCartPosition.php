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
        'Quantity' => 'Int'
        
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
        
        $this->adjustQuantityToStockQuantity();
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
     * Returns the title of the shopping cart position.
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.01.2012
     */
    public function getTitle() {
        $pluginTitle = SilvercartPlugin::call($this, 'overwriteGetTitle', null, false, '');
        
        if ($pluginTitle !== '') {
            return $pluginTitle;
        }

        return $this->SilvercartProduct()->Title;
    }

    /**
     * price sum of this position
     *
     * @param boolean $forSingleProduct Indicates wether the price for the total
     *                                  quantity of products should be returned
     *                                  or for one product only.
     * 
     * @return Money the price sum
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 22.10.2010
     */
    public function getPrice($forSingleProduct = false) {
        $pluginPriceObj = SilvercartPlugin::call($this, 'overwriteGetPrice', array($forSingleProduct), false, 'DataObject');
        
        if ($pluginPriceObj !== false) {
            return $pluginPriceObj;
        }
        
        $product = $this->SilvercartProduct();
        $price = 0;

        if ($product && $product->getPrice()->getAmount()) {
            if ($forSingleProduct) {
                $price = $product->getPrice()->getAmount();
            } else {
                $price = $product->getPrice()->getAmount() * $this->Quantity;
            }
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
        if (SilvercartConfig::EnableStockManagement()) {
            if ($this->SilvercartProduct()->isStockQuantityOverbookable()) {
                return true;
            }
            if ($this->SilvercartProduct()->StockQuantity >= ($this->Quantity + $quantity)) {
                return true;
            }
            return false;
        }
        return true;
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
     * returns the tax amount included in $this
     *
     * @param boolean $forSingleProduct Indicates wether the price for the total
     *                                  quantity of products should be returned
     *                                  or for one product only.
     * 
     * @return float
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 25.11.2010
     */
    public function getTaxAmount($forSingleProduct = false) {        
        if (Member::currentUser()->showPricesGross()) {
            $taxRate = $this->getPrice($forSingleProduct)->getAmount() - ($this->getPrice($forSingleProduct)->getAmount() / (100 + $this->SilvercartProduct()->getTaxRate()) * 100); 
        } else {
            $taxRate = $this->getPrice($forSingleProduct)->getAmount() * ($this->SilvercartProduct()->getTaxRate() / 100);
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.7.2011
     */
    public function adjustQuantityToStockQuantity() {
        if (array_key_exists('url', $_REQUEST)) {
            //must not be executed on a dev/build and dev/tests because a SilvercartConfig instance does not exist
            if (strpos($_REQUEST['url'], 'dev/build') === false && strpos($_REQUEST['url'], 'dev/tests') === false) {
                if (SilvercartConfig::EnableStockManagement() && !$this->SilvercartProduct()->isStockQuantityOverbookable()) {
                    if ($this->Quantity > $this->SilvercartProduct()->StockQuantity) {
                        $this->Quantity = $this->SilvercartProduct()->StockQuantity;
                        $this->write();
                        SilvercartShoppingCartPositionNotice::setNotice($this->ID, "adjusted");
                    }
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 17.11.2011
     */
    public function onBeforeDelete() {
        parent::onBeforeDelete();
        
        $this->extend('updateOnBeforeDelete');
    }
    
    /**
     * We make this method extendable here.
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 17.11.2011
     */
    public function onAfterWrite() {
        parent::onAfterWrite();
        
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
}
