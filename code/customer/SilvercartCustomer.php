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
 * @subpackage Customer
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 10.10.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartCustomer extends DataObjectDecorator {
    
    /**
     * Comma separated string of related group names
     *
     * @var string 
     */
    protected $groupNames = null;
    
    /**
     * List of related group IDs
     *
     * @var array
     */
    protected $groupIDs = null;
    
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
                'SilvercartShoppingCart'            => 'SilvercartShoppingCart',
                'SilvercartInvoiceAddress'          => 'SilvercartAddress',
                'SilvercartShippingAddress'         => 'SilvercartAddress',
                'SilvercartCustomerConfig'          => 'SilvercartCustomerConfig',
                'SilvercartShippingAddressInUse'    => 'SilvercartAddress',
            ),
            'has_many' => array(
                'SilvercartAddresses'   => 'SilvercartAddress',
                'SilvercartOrder'       => 'SilvercartOrder'
            ),
            'belongs_many_many' => array(
                'SilvercartPaymentMethods' => 'SilvercartPaymentMethod'
            ),
            'api_access' => true,
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
        $salutationDropdown = new DropdownField('Salutation', $this->owner->fieldLabel('Salutation'), $values);
        $fields->insertBefore($salutationDropdown, 'FirstName');
    }
    
    /**
     * Updates the CMS fields to use BEFORE the scaffolding is called.
     *
     * @param array &$restrictFields Restrict fields 
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.06.2012
     */
    public function updateRestrictCMSFields(&$restrictFields) {
        foreach ($this->owner->db() as $fieldName => $fieldType) {
            $restrictFields[] = $fieldName;
        }
    }

    /**
     * manipulate the field labels of the decorated class
     *
     * @param array &$labels The labels of cms fields
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.03.2012
     */
    public function updateFieldLabels(&$labels) {
        $labels = array_merge(
                $labels,
                array(
                    'Salutation'                        => _t('SilvercartCustomer.SALUTATION', 'salutation'),
                    'SubscribedToNewsletter'            => _t('SilvercartCustomer.SUBSCRIBEDTONEWSLETTER', 'subscribed to newsletter'),
                    'HasAcceptedTermsAndConditions'     => _t('SilvercartCustomer.HASACCEPTEDTERMSANDCONDITIONS', 'has accepted terms and conditions'),
                    'HasAcceptedRevocationInstruction'  => _t('SilvercartCustomer.HASACCEPTEDREVOCATIONINSTRUCTION', 'has accepted revocation instruction'),
                    'Birthday'                          => _t('SilvercartCustomer.BIRTHDAY', 'birthday'),
                    'ClassName'                         => _t('SilvercartCustomer.TYPE', 'type'),
                    'CustomerNumber'                    => _t('SilvercartCustomer.CUSTOMERNUMBER', 'Customernumber'),
                    'CustomerNumberShort'               => _t('SilvercartCustomer.CUSTOMERNUMBER_SHORT'),
                    'EmailAddress'                      => _t('SilvercartPage.EMAIL_ADDRESS'),
                    'FullName'                          => _t('SilvercartCustomer.FULL_NAME'),
                    'SilvercartShoppingCart'            => _t('SilvercartShoppingCart.SINGULARNAME', 'shopping cart'),
                    'SilvercartInvoiceAddress'          => _t('SilvercartInvoiceAddress.SINGULARNAME', 'invoice address'),
                    'SilvercartShippingAddress'         => _t('SilvercartShippingAddress.SINGULARNAME', 'shipping address'),
                    'SilvercartAddresses'               => _t('SilvercartAddress.PLURALNAME', 'addresses'),
                    'SilvercartOrder'                   => _t('SilvercartOrder.PLURALNAME', 'orders'),
                    'SilvercartPaymentMethods'          => _t('SilvercartPaymentMethod.PLURALNAME'),
                    'GroupNames'                        => _t('SilvercartCustomer.TYPE', 'type'),
                    'SilvercartAddressCountry'          => _t('SilvercartCountry.SINGULARNAME'),
                    
                    'BasicData'                         => _t('SilvercartCustomer.BASIC_DATA'),
                    'AddressData'                       => _t('SilvercartCustomer.ADDRESS_DATA'),
                    'InvoiceData'                       => _t('SilvercartCustomer.INVOICE_DATA'),
                    'ShippingData'                      => _t('SilvercartCustomer.SHIPPING_DATA'),
                )
        );
    }
    
    /**
     * Defines additional searchable fields.
     *
     * @param array &$fields The searchable fields from the decorated object
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.09.2012
     */
    public function updateSearchableFields(&$fields) {
        $address = singleton('SilvercartAddress');
        
        $fields = array_merge(
                $fields,
                array(
                    'CustomerNumber' => array(
                        'title'     => $this->owner->fieldLabel('CustomerNumber'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'FirstName' => array(
                        'title'     => $this->owner->fieldLabel('FirstName'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'Groups.ID' => array(
                        'title'     => $this->owner->fieldLabel('GroupNames'),
                        'filter'    => 'ExactMatchFilter',
                    ),
                    'SubscribedToNewsletter' => array(
                        'title'     => $this->owner->fieldLabel('SubscribedToNewsletter'),
                        'filter'    => 'ExactMatchFilter',
                    ),
                    
                    'SilvercartAddresses.FirstName' => array(
                        'title'     => $address->fieldLabel('FirstName'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'SilvercartAddresses.Surname' => array(
                        'title'     => $address->fieldLabel('Surname'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'SilvercartAddresses.Street' => array(
                        'title'     => $address->fieldLabel('Street'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'SilvercartAddresses.StreetNumber' => array(
                        'title'     => $address->fieldLabel('StreetNumber'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'SilvercartAddresses.Postcode' => array(
                        'title'     => $address->fieldLabel('Postcode'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'SilvercartAddresses.City' => array(
                        'title'     => $address->fieldLabel('City'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'SilvercartAddresses.SilvercartCountry.ID' => array(
                        'title'     => $address->fieldLabel('SilvercartCountry'),
                        'filter'    => 'ExactMatchFilter',
                    ),
                    
                    'SilvercartInvoiceAddress.FirstName' => array(
                        'title'     => $address->fieldLabel('FirstName'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'SilvercartInvoiceAddress.Surname' => array(
                        'title'     => $address->fieldLabel('Surname'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'SilvercartInvoiceAddress.Street' => array(
                        'title'     => $address->fieldLabel('Street'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'SilvercartInvoiceAddress.StreetNumber' => array(
                        'title'     => $address->fieldLabel('StreetNumber'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'SilvercartInvoiceAddress.Postcode' => array(
                        'title'     => $address->fieldLabel('Postcode'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'SilvercartInvoiceAddress.City' => array(
                        'title'     => $address->fieldLabel('City'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'SilvercartInvoiceAddress.SilvercartCountry.ID' => array(
                        'title'     => $address->fieldLabel('SilvercartCountry'),
                        'filter'    => 'ExactMatchFilter',
                    ),
                    
                    'SilvercartShippingAddress.FirstName' => array(
                        'title'     => $address->fieldLabel('FirstName'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'SilvercartShippingAddress.Surname' => array(
                        'title'     => $address->fieldLabel('Surname'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'SilvercartShippingAddress.Street' => array(
                        'title'     => $address->fieldLabel('Street'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'SilvercartShippingAddress.StreetNumber' => array(
                        'title'     => $address->fieldLabel('StreetNumber'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'SilvercartShippingAddress.Postcode' => array(
                        'title'     => $address->fieldLabel('Postcode'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'SilvercartShippingAddress.City' => array(
                        'title'     => $address->fieldLabel('City'),
                        'filter'    => 'PartialMatchFilter',
                    ),
                    'SilvercartShippingAddress.SilvercartCountry.ID' => array(
                        'title'     => $address->fieldLabel('SilvercartCountry'),
                        'filter'    => 'ExactMatchFilter',
                    ),
                )
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
        if (Controller::curr()->class != 'SecurityAdmin') {
            $fields = array(
                'CustomerNumber'            => $this->owner->fieldLabel('CustomerNumber'),
                'Email'                     => $this->owner->fieldLabel('Email'),
                'ShippingAddressSummary'    => $this->owner->fieldLabel('SilvercartShippingAddress'),
                'InvoiceAddressSummary'     => $this->owner->fieldLabel('SilvercartInvoiceAddress'),
                'GroupNames'                => $this->owner->fieldLabel('GroupNames'),
            );
        } else {
            $fields = array_merge(
                    array(
                        'CustomerNumber'            => $this->owner->fieldLabel('CustomerNumber'),
                        'GroupNames'                => $this->owner->fieldLabel('GroupNames'),
                    ),
                    $fields
            );
        }
    }

    /**
     * return the orders shipping address as complete string.
     *
     * @return string
     */
    public function getShippingAddressSummary() {
        $shippingAddressSummary = '';
        $shippingAddressSummary .= $this->owner->SilvercartShippingAddress()->FirstName . ' ' . $this->owner->SilvercartShippingAddress()->Surname . "<br/>" . PHP_EOL;
        $shippingAddressSummary .= $this->owner->SilvercartShippingAddress()->Street . ' ' . $this->owner->SilvercartShippingAddress()->StreetNumber . "<br/>" . PHP_EOL;
        $shippingAddressSummary .= $this->owner->SilvercartShippingAddress()->Addition == '' ? '' : $this->owner->SilvercartShippingAddress()->Addition . "<br/>" . PHP_EOL;
        $shippingAddressSummary .= strtoupper($this->owner->SilvercartShippingAddress()->SilvercartCountry()->ISO2) . '-' . $this->owner->SilvercartShippingAddress()->Postcode . ' ' . $this->owner->SilvercartShippingAddress()->City . "<br/>" . PHP_EOL;
        return $shippingAddressSummary;
    }

    /**
     * return the orders invoice address as complete string.
     *
     * @return string
     */
    public function getInvoiceAddressSummary() {
        $invoiceAddressSummary = '';
        $invoiceAddressSummary .= $this->owner->SilvercartInvoiceAddress()->FirstName . ' ' . $this->owner->SilvercartInvoiceAddress()->Surname . "<br/>" . PHP_EOL;
        $invoiceAddressSummary .= $this->owner->SilvercartInvoiceAddress()->Street . ' ' . $this->owner->SilvercartInvoiceAddress()->StreetNumber . "<br/>" . PHP_EOL;
        $invoiceAddressSummary .= $this->owner->SilvercartInvoiceAddress()->Addition == '' ? '' : $this->owner->SilvercartInvoiceAddress()->Addition . "<br/>" . PHP_EOL;
        $invoiceAddressSummary .= strtoupper($this->owner->SilvercartInvoiceAddress()->SilvercartCountry()->ISO2) . '-' . $this->owner->SilvercartInvoiceAddress()->Postcode . ' ' . $this->owner->SilvercartInvoiceAddress()->City . "<br/>" . PHP_EOL;
        return $invoiceAddressSummary;
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
        if (is_null($this->groupNames)) {
            $groupNamesMap      = $this->owner->Groups()->map();
            $groupNamesAsString = implode(', ', $groupNamesMap);
            $this->groupNames   = $groupNamesAsString;
        }
        return $this->groupNames;
    }
    
    /**
     * Returns the related groups as comma separated list.
     *
     * @return string
     */
    public function getGroupIDs() {
        if (is_null($this->groupIDs)) {
            $this->groupIDs = $this->owner->Groups()->map('ID','ID');;
        }
        return $this->groupIDs;
    }
    
    // ------------------------------------------------------------------------
    // Regular methods
    // ------------------------------------------------------------------------
    
    /**
     * Returns whether the current customer is a registered one.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2013
     */
    public function isRegisteredCustomer() {
        $isRegisteredCustomer = false;
        if ($this->owner->Groups()->find('Code', 'b2c') ||
            $this->owner->Groups()->find('Code', 'b2b') ||
            $this->owner->Groups()->find('Code', 'administrators')) {

            $isRegisteredCustomer = true;
        }
        return $isRegisteredCustomer;
    }


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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.10.2010
     */
    public static function currentRegisteredCustomer() {
        $member                 = Member::currentUser();
        $isRegisteredCustomer   = false;
        
        if ($member &&
            $member->Groups()) {
            
            $isRegisteredCustomer = $member->isRegisteredCustomer();
        }
        
        if ($isRegisteredCustomer) {
            return $member;
        }
        
        return false;
    }
    
    /**
     * Returns a customers purchased products
     * 
     * @return DataObjectSet
     */
    public function getPurchasedProducts() {
        $orders             = $this->owner->SilvercartOrder();
        $purchasedProducts  = new DataObjectSet();
        
        foreach ($orders as $order) {
            $positions = $order->SilvercartOrderPositions();
            foreach ($positions as $position) {
                if (!$purchasedProducts->find('ID', $position->SilvercartProductID)) {
                    $purchasedProducts->push($position->SilvercartProduct());
                }
            }
        }
        
        return $purchasedProducts;
    }

    /**
     * Returns whether the given product is already purchased by customer or not
     * 
     * @param SilvercartProduct $product Product to check
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.08.2012
     */
    public function isPurchasedProduct(SilvercartProduct $product) {
        $isPurchasedProduct = false;
        $purchasedProducts  = $this->getPurchasedProducts();
        if ($purchasedProducts->find('ID', $product->ID)) {
            $isPurchasedProduct = true;
        }
        return $isPurchasedProduct;
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
     * Returns all customer groups of the current customer as a DataObjectSet.
     * If Member::currentUser() does not exist, the group for anonymous customers
     * will be returned. If no group for anonymous customers exists, null will 
     * be returned.
     * 
     * @return DataObjectSet
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.10.2011
     */
    public static function getCustomerGroups() {
        $customer = Member::currentUser();
        if ($customer) {
            $customerGroups = $customer->Groups();
        } else {
            $customerGroups = DataObject::get(
                'Group', "`Code` = 'anonymous'"
            );
        }
        return $customerGroups;
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
     * @param bool $ignoreTaxExemption Determines whether to ignore tax exemption or not.
     *
     * @return bool
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.07.2013
     */
    public function showPricesGross($ignoreTaxExemption = false) {
        $pricetype = SilvercartConfig::Pricetype();
        
        if (!$ignoreTaxExemption &&
            $this->doesNotHaveToPayTaxes()) {
            $pricetype = 'net';
        }
        
        if ($pricetype == "gross") {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Returns whether the customer has to pay tax or not.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.07.2013
     */
    public function doesNotHaveToPayTaxes() {
        $doesNotHaveToPayTaxes = false;
        if ($this->owner->SilvercartShippingAddress() instanceof SilvercartAddress &&
            $this->owner->SilvercartShippingAddress()->SilvercartCountry()->IsNonTaxable) {
            $doesNotHaveToPayTaxes = true;
        }
        return $doesNotHaveToPayTaxes;
    }


    /**
     * Returns the members price type
     *
     * @return string 
     */
    public function getPriceType() {
        $priceType = SilvercartConfig::DefaultPriceType();
        foreach ($this->owner->Groups() as $group) {
            if (!empty($group->Pricetype) &&
                $group->Pricetype != '---') {
                $priceType = $group->Pricetype;
                break;
            }
        }
        return $priceType;
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
        
        // check whether to add a member to an administrative group
        if (Member::currentUser() &&
            Member::currentUser()->isAdmin() &&
            array_key_exists('Groups', $_POST)) {
            $groups = explode(',', $_POST['Groups']);
            if (count($groups) > 0) {
                foreach ($groups as $group) {
                    if (!$this->owner->Groups()->find('ID', $group)) {
                        $groupToAdd = DataObject::get_by_id('Group', $group);
                        if ($groupToAdd) {
                            $groupToAdd->Members()->add($this->owner);
                        }
                    }
                }
                if ($this->owner->Groups()->Count() > count($groups)) {
                    foreach ($this->owner->Groups() as $group) {
                        if (!in_array($group->ID, $groups)) {
                            $group->Members()->remove($this->owner);
                        }
                    }
                }
            }
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

/**
 * Validator for Customers
 *
 * @package Silvercart
 * @subpackage Validator
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 05.04.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
 */
class SilvercartCustomer_Validator extends DataObjectDecorator {
    
    /**
     * Return TRUE if a method exists on this object
     *
     * @param string $method Method to check
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function hasMethod($method) {
        return method_exists($this, $method);
    }
    
    /**
     * validate form data
     *
     * @param array $data Data to validate
     * @param Form  $form Form
     * 
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function updatePHP($data, $form) {
        $valid = true;
        
        $groups = $data['Groups'];
        if (!empty($groups)) {
            $groupObjects   = DataObject::get(
                    'Group',
                    sprintf(
                            "`Group`.`ID` IN (%s)",
                            $groups
                    )
            );
            $pricetypes = array();
            foreach ($groupObjects as $group) {
                if (!empty($group->Pricetype) &&
                    $group->Pricetype != '---') {
                    $pricetypes[$group->Pricetype] = true;
                }
            }

            if (count($pricetypes) > 1) {
                $form->getValidator()->validationError(
                        'Groups',
                        _t('SilvercartCustomer.ERROR_MULTIPLE_PRICETYPES'),
                        'bad'
                );
                $valid = false;
            }
        }
        return $valid;
    }

}

/**
 * Extends standard ForgotPassword class to override some propertys
 *
 * @package Silvercart
 * @subpackage Customer
 * @author Patrick Schneider <pschneider@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
 * @since 19.09.2012
 * @copyright 2012 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License 
 */
class SilvercartCustomer_ForgotPasswordEmail extends Member_ForgotPasswordEmail {
    
    /**
     * changes from for Member_ForgotPasswordEmail to SilvercartConfig email sender
     * converts subject to ISO-8859-1
     * 
     * @return void
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 16.08.2012 
     */
    public function __construct() { 
        parent::__construct();
        $this->setSubject(iconv("UTF-8", "ISO-8859-1", $this->Subject())); // convert to iso because of some old mail clients
        $this->setFrom(SilvercartConfig::EmailSender());
    }
    
    /**
     * Uses DataObject baed, editable templates to send the email
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.09.2012
     */
    public function send() {
        $variables                      = $this->template_data->toMap();
        $variables['PasswordResetLink'] = Director::absoluteURL($this->template_data->PasswordResetLink);
        
        SilvercartShopEmail::send(
                'ForgotPasswordEmail',
                $this->To(),
                $variables
        );
    }
}