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
 * @subpackage Customer
 */

/**
 * Decorates the Member class for additional customer functionality
 *
 * @package Silvercart
 * @subpackage Customer
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 18.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
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
                'SilvercartCustomerCategory' => 'SilvercartCustomerCategory',
                'SilvercartShoppingCart' => 'SilvercartShoppingCart',
                'SilvercartInvoiceAddress' => 'SilvercartInvoiceAddress',
                'SilvercartShippingAddress' => 'SilvercartShippingAddress'
            ),
            'has_many' => array(
                'SilvercartAddress' => 'SilvercartAddress',
                'SilvercartOrder' => 'SilvercartOrder'
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
            ),
            'field_labels' => array(
                'Salutation'                        => _t('SilvercartCustomerRole.SALUTATION', 'salutation'),
                'SubscribedToNewsletter'            => _t('SilvercartCustomerRole.SUBSCRIBEDTONEWSLETTER', 'subscribed to newsletter'),
                'HasAcceptedTermsAndConditions'     => _t('SilvercartCustomerRole.HASACCEPTEDTERMSANDCONDITIONS', 'has accepted terms and conditions'),
                'HasAcceptedRevocationInstruction'  => _t('SilvercartCustomerRole.HASACCEPTEDREVOCATIONINSTRUCTION', 'has accepted revocation instruction'),
                'ConfirmationDate'                  => _t('SilvercartCustomerRole.CONFIRMATIONDATE', 'confirmation date'),
                'ConfirmationHash'                  => _t('SilvercartCustomerRole.CONFIRMATIONHASH', 'confirmation code'),
                'OptInStatus'                       => _t('SilvercartCustomerRole.OPTINSTATUS', 'opt-in status'),
                'Birthday'                          => _t('SilvercartCustomerRole.BIRTHDAY', 'birthday'),
                'ClassName'                         => _t('SilvercartCustomerRole.TYPE', 'type'),
                'SilvercartCustomerCategory'        => _t('SilvercartCustomerCategory.SINGULARNAME', 'customer category'),
                'SilvercartShoppingCart'            => _t('SilvercartShoppingCart.SINGULARNAME', 'shopping cart'),
                'SilvercartInvoiceAddress'          => _t('SilvercartInvoiceAddress.SINGULARNAME', 'invoice address'),
                'SilvercartShippingAddress'         => _t('SilvercartShippingAddress.SINGULARNAME', 'shipping address'),
                'SilvercartAddress'                 => _t('SilvercartAddress.PLURALNAME', 'addresses'),
                'SilvercartOrder'                   => _t('SilvercartOrder.PLURALNAME', 'orders'),
            ),
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
            $filter = sprintf("\"MemberID\" = '%s' AND \"isDefaultForShipping\" = '1'", $customer->ID);
            $shippingAddress = DataObject::get_one('SilvercartAddress', $filter);
            return $shippingAddress;
        }
    }

    /**
     * Get the customers shopping cart or create one if it doesn´t exist yet. "Get me a cart, I don´t care how!"
     * 
     * @return SilvercartShoppingCart
     *
     * @author Roland Lehmann
     */
    public function getCart() {
        if (is_null($this->owner->SilvercartShoppingCartID)) {
            $cart = new SilvercartShoppingCart();
            $cart->write();
            $this->owner->SilvercartShoppingCartID = $cart->ID;
            $this->owner->write();
        }
        return $this->owner->SilvercartShoppingCart();
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