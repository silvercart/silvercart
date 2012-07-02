<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * @subpackage Widgets
 */

/**
 * Provides the shoppingcart as a widget.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 26.05.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
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
     * Returns the title of this widget.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Title() {
        return _t('SilvercartShoppingcartWidget.TITLE');
    }
    
    /**
     * Returns the title of this widget for display in the WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function CMSTitle() {
        return _t('SilvercartShoppingcartWidget.CMSTITLE');
    }
    
    /**
     * Returns the description of what this template does for display in the
     * WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Description() {
        return _t('SilvercartShoppingcartWidget.DESCRIPTION');
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
     * field label method
     * 
     * @param bool $includerelations include relations
     * 
     * @return array 
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'ShowOnlyWhenFilled'    => _t('SilvercartShoppingcartWidget.SHOWONLYWHENFILLED', 'Show only when filled'),
                )
        );
        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * add checkbox option to the widget
     * 
     * @return FieldSet
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->push(new CheckboxField('ShowOnlyWhenFilled', $this->fieldLabel('ShowOnlyWhenFilled')));
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
