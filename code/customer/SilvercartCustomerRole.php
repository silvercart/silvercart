<?php

/**
 * Decorates the Member class for additional customer functionality
 * 
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 18.10.2010
 * @license BSD
 */
class SilvercartCustomerRole extends DataObjectDecorator {

    /**
     * defines relations, attributes and some settings this class.
     *
     * @return array for denfining and configuring the class via the framework
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.10.2010
     */
    public function extraStatics() {
        return array(
            'db' => array(
                'Salutation' => "Enum('Herr,Frau', 'Herr')",
                'SubscribedToNewsletter' => 'Boolean',
                'HasAcceptedTermsAndConditions' => 'Boolean',
                'HasAcceptedRevocationInstruction' => 'Boolean',
                'ConfirmationDate' => 'SS_DateTime',
                'ConfirmationHash' => 'VarChar(100)',
                'OptInStatus' => 'Boolean',
                'Birthday' => 'Date'
            ),
            'has_one' => array(
                'customerCategory' => 'SilvercartCustomerCategory',
                'shoppingCart' => 'SilvercartShoppingCart',
                'invoiceAddress' => 'SilvercartInvoiceAddress',
                'shippingAddress' => 'SilvercartShippingAddress'
            ),
            'has_many' => array(
                'addresses' => 'SilvercartAddress',
                'orders' => 'SilvercartOrder'
            ),
            'summary_fields' => array(
                'FirstName',
                'Surname',
                'ClassName'
            ),
            'api_access' => array(
                'view' => array(
                    'Email'
                )
            ),
            'searchable_fields' => array(
                'FirstName'
            )
        );
    }

    /**
     * Function similar to Member::currentUser(); Determins if we deal with a registered customer
     *
     * @return Member Customer-Object or false
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.10.2010
     */
    public static function currentRegisteredCustomer() {
        $id = Member::currentUserID();
        if ($id) {
            $member = DataObject::get_by_id("Member", $id);
            $memberClass = $member->ClassName;
            if (($memberClass == "SilvercartBusinessCustomer") OR ($memberClass == "SilvercartRegularCustomer")) {
                return $member;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Returns customers shipping address by attribute isDefaultForShipping
     *
     * @return Address Wird Address nicht gefunden, wird bool false zurückgegeben
     * @author Roland Lehmann
     */
    public function getDefaultShippingAddress() {
        if ($customer = Member::currentUser()) {
            $filter = sprintf("\"ownerID\" = '%s' AND \"isDefaultForShipping\" = '1'", $customer->ID);
            $shippingAddress = DataObject::get_one('SilvercartAddress', $filter);
            return $shippingAddress;
        }
    }

    /**
     * Transfer a shopping cart from a cutomer to another user and kill the victim.
     *
     * @param Member $victim Member that must be deleted
     * 
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.10.2010
     */
    public function lootShoppingCartAndKillVictim(Member $victim) {
        $filter = sprintf("\"ID\" = '%s'", $this->owner->shoppingCartID);
        $customersOldCart = DataObject::get_one('SilvercartShoppingCart', $filter);
        if ($victim->shoppingCartID) {
            $this->owner->shoppingCartID = $victim->shoppingCartID;
            $this->owner->write();
        }
        $customersOldCart->delete();
        $victim->delete();
    }

    /**
     * Get the customers shopping cart or create one if it doesn´t exist yet. "Get me a cart, I don´t care how!"
     * 
     * @return <type> DataObject ShoppingCart
     * @author Roland Lehmann
     */
    public function getCart() {

        $cartID = $this->owner->shoppingCartID;
        if ($cartID != null) { //If a user has no shopping cart yet calling his shoppingCartID will return NULL.
            return $this->owner->shoppingCart();
        } else {
            $cart = new SilvercartShoppingCart();
            $cart->write();
            $this->owner->shoppingCartID = $cart->ID;
            $this->owner->write();
            return $this->owner->shoppingCart();
        }
    }

    /**
     * standard hook
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.10.2010
     * @return void
     */
    public function onAfterWrite() {
        parent::onAfterWrite();
    }

    /**
     * defines which attributes of an object that can be accessed via api
     * 
     * @return SearchContext ???
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 3.11.2010
     */
    public static function getRestfulSearchContext() {
        $fields = new FieldSet(
                        array(
                            new TextField(
                                    'Email'
                            )
                        )
        );

        $filters = array(
            'Email' => new ExactMatchFilter('Email')
        );

        return new SearchContext(
                'Member',
                $fields,
                $filters
        );
    }
}