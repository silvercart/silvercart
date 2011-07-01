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
     * Defines relations, attributes and settings for the decorated class.
     *
     * @return for defining and configuring the decorated class.
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.10.2010
     */
    public function extraStatics() {
        return array(
            'db' => array(
                'Salutation'                        => "Enum('Herr,Frau', 'Herr')",
                'SubscribedToNewsletter'            => 'Boolean',
                'HasAcceptedTermsAndConditions'     => 'Boolean',
                'HasAcceptedRevocationInstruction'  => 'Boolean',
                'ConfirmationDate'                  => 'SS_DateTime',
                'ConfirmationHash'                  => 'VarChar(100)',
                'ConfirmationBacklink'              => 'VarChar(255)',
                'ConfirmationBacklinkText'          => 'VarChar(255)',
                'OptInStatus'                       => 'Boolean',
                'OptInTempText'                     => 'Text',
                'Birthday'                          => 'Date',
                'CustomerNumber'                    => 'VarChar(128)',
            ),
            'has_one' => array(
                'SilvercartCustomerCategory'    => 'SilvercartCustomerCategory',
                'SilvercartShoppingCart'        => 'SilvercartShoppingCart',
                'SilvercartInvoiceAddress'      => 'SilvercartAddress',
                'SilvercartShippingAddress'     => 'SilvercartAddress'
            ),
            'has_many' => array(
                'SilvercartAddresses' => 'SilvercartAddress',
                'SilvercartOrder'   => 'SilvercartOrder'
            ),
            'belongs_many_many' => array(
                'SilvercartPaymentMethods' => 'SilvercartPaymentMethod'
            ),
            'summary_fields' => array(
                'CustomerNumber',
                'FirstName',
                'Surname',
                'ClassName',
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
                'CustomerNumber'                    => _t('SilvercartCustomerRole.CUSTOMERNUMBER', 'Customernumber'),
                'SilvercartCustomerCategory'        => _t('SilvercartCustomerCategory.SINGULARNAME', 'customer category'),
                'SilvercartShoppingCart'            => _t('SilvercartShoppingCart.SINGULARNAME', 'shopping cart'),
                'SilvercartInvoiceAddress'          => _t('SilvercartInvoiceAddress.SINGULARNAME', 'invoice address'),
                'SilvercartShippingAddress'         => _t('SilvercartShippingAddress.SINGULARNAME', 'shipping address'),
                'SilvercartAddress'                 => _t('SilvercartAddress.PLURALNAME', 'addresses'),
                'SilvercartOrder'                   => _t('SilvercartOrder.PLURALNAME', 'orders'),
            )
        );
    }

    /**
     * manipulate the cms fields of the decorated class
     *
     * @param FieldSet &$fields the field set of cms fields
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.3.2011
     * @return void
     */
    public function updateCMSFields(FieldSet &$fields) {
        parent::updateCMSFields($fields);
        //i18n for enum values of Salutation
        $fields->removeByName('Salutation');
        $values = array(
            'Herr' => _t('SilvercartAddress.MISTER'),
            'Frau' => _t('SilvercartAddress.MISSES')
        );
        $salutationDropdown = new DropdownField('Salutation', _t('SilvercartCustomerRole.SALUTATION'), $values);
        $fields->insertBefore($salutationDropdown, 'FirstName');
    }

    /**
     * overwrite the summary fields
     *
     * @param array &$fields db fields to summarize the DataObject
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.04.2011
     */
    public function updateSummaryFields(&$fields) {
        $fields = array(
            'CustomerNumber'    => _t('SilvercartCustomerRole.CUSTOMERNUMBER', 'Customernumber'),
            'FirstName'         => _t('Member.FIRSTNAME'),
            'Surname'           => _t('Member.SURNAME'),
            'ClassName'         => _t('SilvercartCustomerRole.TYPE', 'type'),
        );
    }

    /**
     * Function similar to Member::currentUser(); Determins if we deal with a
     * registered customer who has opted in. Returns the member object or
     * false.
     *
     * @return mixed Member|boolean(false)
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.10.2010
     */
    public static function currentRegisteredCustomer() {
        $member             = Member::currentUser();
        $isInCustomerGroup  = false;
        
        if ($member) {
            
            if ($member->Groups()->find('Code', 'b2c') ||
                $member->Groups()->find('Code', 'b2b')) {
                $isInCustomerGroup = true;
            }
            
            if (($member->ClassName == "SilvercartRegularCustomer" ||
                 $member->ClassName == 'SilvercartBusinessCustomer' ||
                 $isInCustomerGroup) &&
                $member->OptInStatus === '1') {

                return $member;
            }
        } else {
            return false;
        }
    }

    /**
     * Get the customers shopping cart or create one if it doesn¬¥t exist yet. "Get me a cart, I don¬¥t care how!"
     * 
     * @return SilvercartShoppingCart
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     */
    public function getCart() {
        if ($this->owner->SilvercartShoppingCartID == 0 ||
            !DataObject::get_by_id('SilvercartShoppingCart', $this->owner->SilvercartShoppingCartID)) {
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