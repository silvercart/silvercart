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
     * create default groups on build
     * these groups should be identified via attribute Code, which cannot be changed via backend
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 22.10.2010
     * @return void
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        /**
         * Create a Group for $this
         */
        if (!DataObject::get_one('Group', "\"Code\" = 'b2c'")) {
            $group = new Group();
            $group->Title = _t('SilvercartRegularCustomer.REGULARCUSTOMER', 'regular customer');
            $group->Code = "b2c";
            $group->write();
        }

        /*
         * Create a OptIn Group for $this
         */
        if (!DataObject::get_one('Group', "\"Code\" = 'b2c-optin'")) {
            $group = new Group();
            $group->Title = _t('SilvercartRegularCustomer.REGULARCUSTOMER', 'regular customer')." OptIn";
            $group->Code = "b2c-optin";
            $group->write();
        }
    }

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
