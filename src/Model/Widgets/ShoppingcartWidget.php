<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Widgets\Widget;
use SilverStripe\i18n\i18n;

/**
 * Provides the shoppingcart as a widget.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ShoppingcartWidget extends Widget {
    
    /**
     * attributes
     * 
     * @var array 
     */
    private static $db = array(
        'ShowOnlyWhenFilled' => 'Boolean(0)',
    );
    
    /**
     * Casted attributes
     * 
     * @var array 
     */
    private static $casting = array(
        'ShowWidget' => 'Boolean',
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartShoppingcartWidget';

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'ShowOnlyWhenFilled' => _t(ShoppingcartWidget::class . '.SHOWONLYWHENFILLED', 'Show this wiget only if the cart is filled.'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns the link to the checkout (for template rendering)
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2011
     */
    public function CheckOutLink() {
        return Tools::PageByIdentifierCode('SilvercartCheckoutStep')->Link();
    }
    
    /**
     * Returns the link to the cart (for template rendering)
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2011
     */
    public function CartLink() {
        return Tools::PageByIdentifierCode('SilvercartCartpage')->Link();
    }
        
    /**
     * Creates the cache key for this widget.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.11.2014
     */
    public function WidgetCacheKey() {
        $key    = i18n::get_locale() . '_' . $this->LastEdited . '_';
        $member = Customer::currentUser();
        
        if ($member) {
            $cart = $member->getCart();
            
            if ($cart) {
                $key .= $cart->LastEdited . '_' . $cart->ID;
            }
        }
        
        return $key;
    }
    
    /**
     * returns true if ShowOnlyWhenFilled is unchecked and neither a shopping cart exists or is filled in
     * 
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Patrick Schneider <pschneider@pixeltricks.de>
     * @since 15.11.2014
     */
    public function getShowWidget() {
        $showWidget = true;
        $member = Customer::currentUser();
        if ($this->ShowOnlyWhenFilled &&
            (!($member instanceof Member) ||
             $member->ShoppingCartID == 0 ||
             !$member->getCart()->isFilled())) {
            $showWidget = false;
        }
        return $showWidget;
    }
}