<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 * 
 * @package Silvercart
 * @subpackage Widgets
 */

/**
 * Provides the shoppingcart as a widget.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 26.05.2011
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartShoppingcartWidget extends SilvercartWidget {
    
    /**
     * attributes
     * 
     * @var array 
     */
    public static $db = array(
        'ShowOnlyWhenFilled'    => 'Boolean(0)',
    );

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
                    'ShowOnlyWhenFilled'    => _t('SilvercartShoppingcartWidget.SHOWONLYWHENFILLED'),
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
        return Controller::curr()->PageByIdentifierCodeLink('SilvercartCheckoutStep');
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
        return Controller::curr()->PageByIdentifierCodeLink('SilvercartCartpage');
    }
        
    /**
     * Creates the cache key for this widget.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>, Sascha Koehler <skoehler@pixeltricks.de>
     * @since 02.07.2012
     */
    public function WidgetCacheKey() {
        $key    = i18n::get_locale().'_'.$this->LastEdited.'_';
        $member = Member::currentUser();
        
        if ($member) {
            $cart = $member->SilvercartShoppingCart();
            
            if ($cart) {
                $key .= $cart->LastEdited.'_'.$cart->ID;
            }
        }
        
        return $key;
    }
    
    /**
     * add checkbox option to the widget
     * 
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this);
        return $fields;
    }
    
    /**
     * returns true if ShowOnlyWhenFilled is unchecked and neither a shopping cart exists or is filled in
     * 
     * @return boolean 
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function ShowWidget() {
        $showWidget = true;
        $member = Member::currentUser();
        if ($this->ShowOnlyWhenFilled &&
            (!$member ||
             $member->SilvercartShoppingCartID == 0 ||
             !$member->SilvercartShoppingCart()->isFilled())) {
            $showWidget = false;
        }
        return $showWidget;
    }
}
