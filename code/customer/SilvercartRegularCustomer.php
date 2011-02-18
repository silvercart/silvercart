<?php

/**
 * abstract for a regular customer
 * 
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license BSD
 * @since 22.10.2010
 */
class SilvercartRegularCustomer extends Member {

    /**
     * hook
     * every $this gets a shopping cart
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 22.10.2010
     * @return void
     */
    public function onAfterWrite() {
        parent::onAfterWrite();
        //create a cart for every user
        if ($this->SilvercartShoppingCartID == null) {
            $cart = new SilvercartShoppingCart();
            $cart->write();
            $this->SilvercartShoppingCartID = $cart->ID;
            $this->write();
        }
    }

    /**
     * hook
     * delete shopping cart too
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 22.10.2010
     * @return void
     */
    public function onAfterDelete() {
        parent::onAfterDelete();
        if ($this->SilvercartShoppingCartID) {
            $cart = DataObject::get_by_id('SilvercartShoppingCart', $this->SilvercartShoppingCartID);
            if ($cart) {
                $cart->delete();
            }
        }
        
    }

}
