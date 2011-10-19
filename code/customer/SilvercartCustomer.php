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
 * @subpackage Customer
 */

/**
 * Contains additional datafields for SilverCart customers and corresponding
 * methods.
 *
 * @package Silvercart
 * @subpacke Customer
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 10.10.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartCustomer extends DataObjectDecorator {
    
    /**
     * Extends the database fields and relations of the decorated class.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.10.2011
     */
    public function extraStatics() {
        return array(
            'db' => array(
                'taxIdNumber'                       => 'VarChar(30)',
                'Salutation'                        => "Enum('Herr,Frau', 'Herr')",
                'NewsletterOptInStatus'             => 'Boolean(0)',
                'NewsletterConfirmationHash'        => 'VarChar(50)',
                'SubscribedToNewsletter'            => 'Boolean(0)',
                'HasAcceptedTermsAndConditions'     => 'Boolean(0)',
                'HasAcceptedRevocationInstruction'  => 'Boolean(0)',
                'Birthday'                          => 'Date',
                'CustomerNumber'                    => 'VarChar(128)',
            ),
            'has_one' => array(
                'SilvercartShoppingCart'        => 'SilvercartShoppingCart',
                'SilvercartInvoiceAddress'      => 'SilvercartAddress',
                'SilvercartShippingAddress'     => 'SilvercartAddress',
                'SilvercartCustomerConfig'      => 'SilvercartCustomerConfig'
            ),
            'has_many' => array(
                'SilvercartAddresses'   => 'SilvercartAddress',
                'SilvercartOrder'       => 'SilvercartOrder'
            ),
            'belongs_many_many' => array(
                'SilvercartPaymentMethods' => 'SilvercartPaymentMethod'
            ),
            'api_access' => array(
                'view' => array(
                    'Email'
                )
            ),
            'field_labels' => array(
                'Salutation'                        => _t('SilvercartCustomer.SALUTATION', 'salutation'),
                'SubscribedToNewsletter'            => _t('SilvercartCustomer.SUBSCRIBEDTONEWSLETTER', 'subscribed to newsletter'),
                'HasAcceptedTermsAndConditions'     => _t('SilvercartCustomer.HASACCEPTEDTERMSANDCONDITIONS', 'has accepted terms and conditions'),
                'HasAcceptedRevocationInstruction'  => _t('SilvercartCustomer.HASACCEPTEDREVOCATIONINSTRUCTION', 'has accepted revocation instruction'),
                'Birthday'                          => _t('SilvercartCustomer.BIRTHDAY', 'birthday'),
                'ClassName'                         => _t('SilvercartCustomer.TYPE', 'type'),
                'CustomerNumber'                    => _t('SilvercartCustomer.CUSTOMERNUMBER', 'Customernumber'),
                'SilvercartShoppingCart'            => _t('SilvercartShoppingCart.SINGULARNAME', 'shopping cart'),
                'SilvercartInvoiceAddress'          => _t('SilvercartInvoiceAddress.SINGULARNAME', 'invoice address'),
                'SilvercartShippingAddress'         => _t('SilvercartShippingAddress.SINGULARNAME', 'shipping address'),
                'SilvercartAddress'                 => _t('SilvercartAddress.PLURALNAME', 'addresses'),
                'SilvercartOrder'                   => _t('SilvercartOrder.PLURALNAME', 'orders'),
            ),
            'casting' => array(
                'GroupNames' => 'Text',
            ),
        );
    }
    
    // ------------------------------------------------------------------------
    // Extension methods
    // ------------------------------------------------------------------------
    
    /**
     * manipulate the cms fields of the decorated class
     *
     * @param FieldSet &$fields the field set of cms fields
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.3.2011
     */
    public function updateCMSFields(FieldSet &$fields) {
        parent::updateCMSFields($fields);
        
        $fields->removeByName('Salutation');
        $values = array(
            'Herr' => _t('SilvercartAddress.MISTER'),
            'Frau' => _t('SilvercartAddress.MISSES')
        );
        $salutationDropdown = new DropdownField('Salutation', _t('SilvercartCustomer.SALUTATION'), $values);
        $fields->insertBefore($salutationDropdown, 'FirstName');
    }
    
    /**
     * Defines additional searchable fields.
     *
     * @param array &$fields The searchable fields from the decorated object
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.10.2011
     */
    public function updateSearchableFields(&$fields) {
        $fields['CustomerNumber'] = array(
            'title'     => _t('SilvercartCustomer.CUSTOMERNUMBER'),
            'filter'    => 'PartialMatchFilter'
        );
        $fields['FirstName'] = array(
            'title'     => _t('SilvercartCustomer.FIRSTNAME'),
            'filter'    => 'PartialMatchFilter'
        );
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
            'CustomerNumber'    => _t('SilvercartCustomer.CUSTOMERNUMBER', 'Customernumber'),
            'FirstName'         => _t('Member.FIRSTNAME'),
            'Surname'           => _t('Member.SURNAME'),
            'GroupNames'        => _t('SilvercartCustomer.TYPE', 'type'),
        );
    }
    
    // ------------------------------------------------------------------------
    // Casting methods
    // ------------------------------------------------------------------------
    
    /**
     * Returns the related groups as comma separated list.
     *
     * @return string
     */
    public function getGroupNames() {
        $groupNamesAsString = '';
        $groupNamesMap = $this->owner->Groups()->map();
        $groupNamesAsString = implode(', ', $groupNamesMap);
        return $groupNamesAsString;
    }
    
    // ------------------------------------------------------------------------
    // Regular methods
    // ------------------------------------------------------------------------
    
    /**
     * Creates an anonymous customer if there's no currentMember object.
     *
     * @return Member
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public static function createAnonymousCustomer() {
        $member = Member::currentUser();
        
        if (!$member) {
            $member = new Member();
            $member->write();
            
            // Add customer to intermediate group
            $customerGroup = DataObject::get_one(
                'Group', "`Code` = 'anonymous'"
            );
            
            if ($customerGroup) {
                $member->Groups()->add($customerGroup);
            }
            
            $member->logIn(true);
        }
        
        return $member;
    }
    
    /**
     * Returns the Member object if the current Member is an anonymous
     * customer.
     * If the user is not logged in or the Member is not anonymous boolean
     * false will be returned.
     *
     * @return mixed Member|boolean false
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.10.2011
     */
    public static function currentAnonymousCustomer() {
        $isAnonymousCustomer = false;
        $member              = Member::currentUser();
        
        if ($member) {
            if ($member->Groups()->find('Code', 'anonymous')) {
                $isAnonymousCustomer = true;
            }
        }
        
        if ($isAnonymousCustomer) {
            return $member;
        }
        
        return false;
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
        $member                 = Member::currentUser();
        $isRegisteredCustomer   = false;
        
        if ($member &&
            $member->Groups()) {
            
            if ($member->Groups()->find('Code', 'b2c') ||
                $member->Groups()->find('Code', 'b2b') ||
                $member->Groups()->find('Code', 'administrators')) {
                
                $isRegisteredCustomer = true;
            }
        }
        
        if ($isRegisteredCustomer) {
            return $member;
        }
        
        return false;
    }
    
    /**
     * Get the customers shopping cart or create one if it doesn't exist yet.
     * 
     * @return SilvercartShoppingCart
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.10.2010
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
     * Defines which attributes of an object can be accessed via api
     * 
     * @return SearchContext
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.11.2010
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
    
    /**
     * Returns the translated salutation.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.10.2011
     */
    public function getTranslatedSalutation() {
        $salutation = '';
        
        switch ($this->owner->Salutation) {
            case 'Frau':
                $salutation = _t('SilvercartAddress.MISSES');
                break;
            case 'Herr':
                $salutation = _t('SilvercartAddress.MISTER');
                break;
        }
            
        return $salutation;
    }
    
    /**
     * Get the customer's configuration object or create one if it doesn't
     * exist yet.
     *
     * @return SilvercartCustomerConfig
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public function getSilvercartCustomerConfig() {
        if (!$this->owner->SilvercartCustomerConfigID ||
            !DataObject::get_by_id('SilvercartCustomerConfig', $this->owner->SilvercartCustomerConfigID)) {
            
            $silvercartCustomerConfig                   = new SilvercartCustomerConfig();
            $silvercartCustomerConfig->MemberID         = $this->owner->ID;
            $silvercartCustomerConfig->productsPerPage  = SilvercartConfig::getProductsPerPageDefault();
            $silvercartCustomerConfig->write();
            
            $this->owner->SilvercartCustomerConfigID = $silvercartCustomerConfig->ID;
            $this->owner->write();
        }
        
        return $this->owner->SilvercartCustomerConfig();
    }
    
    /**
     * Indicates wether the customer has finished the newsletter opt-in or not.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.10.2011
     */
    public function hasFinishedNewsletterOptIn() {
        $hasFinishedNewsletterOptIn = false;
        
        if ($this->owner->NewsletterOptInStatus) {
            $hasFinishedNewsletterOptIn = true;
        }
        
        return $hasFinishedNewsletterOptIn;
    }
    
    /**
     * Indicates wether the customer has defined only one address to be both
     * invoice and shipping address.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.10.2011
     */
    public function hasOnlyOneStandardAddress() {
        $hasOnlyOneStandardAddress = false;
        
        if ($this->owner->SilvercartInvoiceAddressID == $this->owner->SilvercartShippingAddressID &&
            $this->owner->SilvercartInvoiceAddressID > 0) {
            $hasOnlyOneStandardAddress = true;
        }
        
        return $hasOnlyOneStandardAddress;
    }
    
     /**
     * used to determine weather something should be shown on a template or not
     *
     * @return bool
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.3.2011
     */
    public function showPricesGross() {
        $pricetype = SilvercartConfig::Pricetype();
        
        if ($pricetype == "gross") {
            return true;
        } else {
            return false;
        }
    }
    
    // ------------------------------------------------------------------------
    // Hooks
    // ------------------------------------------------------------------------
    
    /**
     * If the user is not anonymous a customer number is attributed to the
     * Member if none is yet given.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.10.2011
     */
    public function onBeforeWrite() {
        parent::onBeforeWrite();
        
        if (!self::currentAnonymousCustomer()) {
            if (empty($this->owner->CustomerNumber)) {
                $this->owner->CustomerNumber = SilvercartNumberRange::useReservedNumberByIdentifier('CustomerNumber');
            }
        }
    }
    
    /**
     * Attributes a shopping cart to the Member if none is attributed yet.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.10.2011
     */
    public function onAfterWrite() {
        parent::onAfterWrite();
        
        if ($this->owner->SilvercartShoppingCartID === null) {
            $cart = new SilvercartShoppingCart();
            $cart->write();
            $this->owner->SilvercartShoppingCartID = $cart->ID;
            $this->owner->write();
        }
    }
    
    /**
     * Delete the attributed shopping cart if existant.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.10.2011
     */
    public function onAfterDelete() {
        parent::onAfterDelete();
        
        if ($this->owner->SilvercartShoppingCartID !== null) {
            $cart = DataObject::get_by_id('SilvercartShoppingCart', $this->owner->SilvercartShoppingCartID);
            if ($cart) {
                $cart->delete();
            }
        }
    }
}