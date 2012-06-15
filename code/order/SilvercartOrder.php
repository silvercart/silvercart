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
 * @subpackage Order
 */

/**
 * abstract for an order
 *
 * @package Silvercart
 * @subpackage Order
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 22.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartOrder extends DataObject implements PermissionProvider {

    /**
     * Set permissions.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.02.2012
     */
    public function providePermissions() {
        return array(
            'SILVERCART_ORDER_VIEW'   => _t('SilvercartOrder.SILVERCART_ORDER_VIEW'),
            'SILVERCART_ORDER_EDIT'   => _t('SilvercartOrder.SILVERCART_ORDER_EDIT'),
            'SILVERCART_ORDER_DELETE' => _t('SilvercartOrder.SILVERCART_ORDER_DELETE')
        );
    }

    /**
     * Indicates wether the current user can view this object.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.02.2012
     */
    public function CanView() {
        $canView = false;
        if (Member::currentUserID() == $this->MemberID ||
            Permission::check('SILVERCART_ORDER_VIEW')) {
            $canView = true;
        }
        return $canView;
    }

    /**
     * Indicates wether the current user can edit this object.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.02.2012
     */
    public function CanEdit() {
        return Permission::check('SILVERCART_ORDER_EDIT');
    }

    /**
     * Indicates wether the current user can delete this object.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.02.2012
     */
    public function CanDelete() {
        return Permission::check('SILVERCART_ORDER_DELETE');
    }

    /**
     * attributes
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $db = array(
        'AmountTotal'                       => 'Money', // value of all products
        'AmountGrossTotal'                  => 'Money', // value of all products + transaction fee
        'HandlingCostPayment'               => 'Money',
        'HandlingCostShipment'              => 'Money',
        'TaxRatePayment'                    => 'Int',
        'TaxRateShipment'                   => 'Int',
        'TaxAmountPayment'                  => 'Float',
        'TaxAmountShipment'                 => 'Float',
        'Note'                              => 'Text',
        'WeightTotal'                       => 'Int', //unit is gramm
        'CustomersEmail'                    => 'VarChar(60)',
        'OrderNumber'                       => 'VarChar(128)',
        'HasAcceptedTermsAndConditions'     => 'Boolean(0)',
        'HasAcceptedRevocationInstruction'  => 'Boolean(0)',
    );

    /**
     * 1:1 relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $has_one = array(
        'SilvercartShippingAddress' => 'SilvercartOrderShippingAddress',
        'SilvercartInvoiceAddress'  => 'SilvercartOrderInvoiceAddress',
        'SilvercartPaymentMethod'   => 'SilvercartPaymentMethod',
        'SilvercartShippingMethod'  => 'SilvercartShippingMethod',
        'SilvercartOrderStatus'     => 'SilvercartOrderStatus',
        'Member'                    => 'Member',
        'SilvercartShippingFee'     => 'SilvercartShippingFee'
    );

    /**
     * 1:n relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $has_many = array(
        'SilvercartOrderPositions'  => 'SilvercartOrderPosition'
    );

    /**
     * m:n relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public static $many_many = array(
        'SilvercartProducts' => 'SilvercartProduct'
    );

    /**
     * Casting.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public static $casting = array(
        'Created'                   => 'Date',
        'CreatedNice'               => 'VarChar',
        'ShippingAddressSummary'    => 'VarChar',
        'InvoiceAddressSummary'     => 'VarChar',
        'AmountTotalNice'           => 'VarChar',
    );

    /**
     * Default sort direction in tables.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public static $default_sort = "Created DESC";

    /**
     * register extensions
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public static $extensions = array(
        "Versioned('Live')",
    );
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011
     */
    public function singular_name() {
        if (_t('SilvercartOrder.SINGULARNAME')) {
            return _t('SilvercartOrder.SINGULARNAME');
        } else {
            return parent::singular_name();
        } 
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011 
     */
    public function plural_name() {
        if (_t('SilvercartOrder.PLURALNAME')) {
            return _t('SilvercartOrder.PLURALNAME');
        } else {
            return parent::plural_name();
        }   
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 10.03.2011
     */
    public function summaryFields() {
        $summaryFields = array(
            'CreatedNice'                       => $this->fieldLabel('Created'),
            'OrderNumber'                       => $this->fieldLabel('OrderNumber'),
            'ShippingAddressSummary'            => $this->fieldLabel('SilvercartShippingAddress'),
            'InvoiceAddressSummary'             => $this->fieldLabel('SilvercartInvoiceAddress'),
            'AmountTotalNice'                   => $this->fieldLabel('AmountTotal'),
            'SilvercartPaymentMethod.Title'     => $this->fieldLabel('SilvercartPaymentMethod'),
            'SilvercartOrderStatus.Title'       => $this->fieldLabel('SilvercartOrderStatus'),
            'SilvercartShippingMethod.Title'    => $this->fieldLabel('SilvercartShippingMethod'),
        );
        $this->extend('updateSummaryFields', $summaryFields);

        return $summaryFields;
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     * 
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 10.03.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'ID'                                => _t('SilvercartOrder.ORDER_ID'),
                'Created'                           => _t('SilvercartPage.ORDER_DATE'),
                'OrderNumber'                       => _t('SilvercartOrder.ORDERNUMBER', 'ordernumber'),
                'SilvercartShippingFee'             => _t('SilvercartOrder.SHIPPINGRATE', 'shipping costs'),
                'Note'                              => _t('SilvercartPage.REMARKS'),
                'Member'                            => _t('SilvercartOrder.CUSTOMER', 'customer'),
                'SilvercartShippingAddress'         => _t('SilvercartShippingAddress.SINGULARNAME'),
                'SilvercartInvoiceAddress'          => _t('SilvercartInvoiceAddress.SINGULARNAME'),
                'SilvercartOrderStatus'             => _t('SilvercartOrder.STATUS', 'order status'),
                'AmountTotal'                       => _t('SilvercartOrder.AMOUNTTOTAL'),
                'AmountGrossTotal'                  => _t('SilvercartOrder.AMOUNTGROSSTOTAL'),
                'HandlingCostPayment'               => _t('SilvercartOrder.HANDLINGCOSTPAYMENT'),
                'HandlingCostShipment'              => _t('SilvercartOrder.HANDLINGCOSTSHIPMENT'),
                'TaxRatePayment'                    => _t('SilvercartOrder.TAXRATEPAYMENT'),
                'TaxRateShipment'                   => _t('SilvercartOrder.TAXRATESHIPMENT'),
                'TaxAmountPayment'                  => _t('SilvercartOrder.TAXAMOUNTPAYMENT'),
                'TaxAmountShipment'                 => _t('SilvercartOrder.TAXAMOUNTSHIPMENT'),
                'Note'                              => _t('SilvercartOrder.NOTE'),
                'WeightTotal'                       => _t('SilvercartOrder.WEIGHTTOTAL'),
                'CustomersEmail'                    => _t('SilvercartOrder.CUSTOMERSEMAIL'),
                'SilvercartPaymentMethod'           => _t('SilvercartPaymentMethod.SINGULARNAME'),
                'SilvercartShippingMethod'          => _t('SilvercartShippingMethod.SINGULARNAME'),
                'HasAcceptedTermsAndConditions'     => _t('SilvercartOrder.HASACCEPTEDTERMSANDCONDITIONS'),
                'HasAcceptedRevocationInstruction'  => _t('SilvercartOrder.HASACCEPTEDREVOCATIONINSTRUCTION'),
                'SilvercartOrderPositions'          => _t('SilvercartOrderPosition.PLURALNAME'),
            )
        );
        $this->extend('updateFieldLabels', $fieldLabels);
        
        return $fieldLabels;
    }

    /**
     * Searchable fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 10.03.2011
     */
    public function searchableFields() {
        $searchableFields = array(
            'Created' => array(
                'title'     => _t('SilvercartPage.ORDER_DATE'),
                'filter'    => 'DateRangeSearchFilter'
            ),
            'OrderNumber' => array(
                'title'     => _t('SilvercartOrder.ORDERNUMBER'),
                'filter'    => 'PartialMatchFilter'
            ),
            'SilvercartShippingAddress.FirstName' => array(
                'title'     => _t('SilvercartAddress.FIRSTNAME'),
                'filter'    => 'PartialMatchFilter'
            ),
            'SilvercartShippingAddress.Surname' => array(
                'title'     => _t('SilvercartAddress.SURNAME'),
                'filter'    => 'PartialMatchFilter'
            ),
            'SilvercartOrderStatus.ID' => array(
                'title'     => _t('SilvercartOrder.STATUS', 'order status'),
                'filter'    => 'ExactMatchFilter'
            ),
            'SilvercartPaymentMethod.ID' => array(
                'title'     => _t('SilvercartPaymentMethod.SINGULARNAME', 'Payment method'),
                'filter'    => 'ExactMatchFilter'
            ),
        );
        $this->extend('updateSearchableFields', $searchableFields);

        return $searchableFields;
    }

    /**
     * Set the default search context for this field
     * 
     * @return return_value
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.02.2012
     */
    public function getDefaultSearchContext() {
        return new DateRangeSearchContext(
            $this->owner->class,
            $this->owner->scaffoldSearchFields(),
            $this->owner->defaultSearchFilters()
        );
    }

    /**
     * returns the orders creation date formated: dd.mm.yyyy hh:mm
     *
     * @return string
     */
    public function getCreatedNice() {
        return date('d.m.Y H:i', strtotime($this->Created)) . ' Uhr';
    }

    /**
     * return the orders shipping address as complete string.
     *
     * @return string
     */
    public function getShippingAddressSummary() {
        $shippingAddressSummary = '';
        $shippingAddressSummary .= $this->SilvercartShippingAddress()->FirstName . ' ' . $this->SilvercartShippingAddress()->Surname . "\n";
        $shippingAddressSummary .= $this->SilvercartShippingAddress()->Street . ' ' . $this->SilvercartShippingAddress()->StreetNumber . "\n";
        $shippingAddressSummary .= $this->SilvercartShippingAddress()->Addition == '' ? '' : $this->SilvercartShippingAddress()->Addition . "\n";
        $shippingAddressSummary .= strtoupper($this->SilvercartShippingAddress()->SilvercartCountry()->ISO2) . '-' . $this->SilvercartShippingAddress()->Postcode . ' ' . $this->SilvercartShippingAddress()->City . "\n";
        return $shippingAddressSummary;
    }

    /**
     * return the orders invoice address as complete string.
     *
     * @return string
     */
    public function getInvoiceAddressSummary() {
        $invoiceAddressSummary = '';
        $invoiceAddressSummary .= $this->SilvercartInvoiceAddress()->FirstName . ' ' . $this->SilvercartInvoiceAddress()->Surname . "\n";
        $invoiceAddressSummary .= $this->SilvercartInvoiceAddress()->Street . ' ' . $this->SilvercartInvoiceAddress()->StreetNumber . "\n";
        $invoiceAddressSummary .= $this->SilvercartInvoiceAddress()->Addition == '' ? '' : $this->SilvercartInvoiceAddress()->Addition . "\n";
        $invoiceAddressSummary .= strtoupper($this->SilvercartInvoiceAddress()->SilvercartCountry()->ISO2) . '-' . $this->SilvercartInvoiceAddress()->Postcode . ' ' . $this->SilvercartInvoiceAddress()->City . "\n";
        return $invoiceAddressSummary;
    }

    /**
     * Returns a limited number of order positions.
     * 
     * @param int $numberOfPositions The number of positions to get.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.01.2012
     */
    public function getLimitedSilvercartOrderPositions($numberOfPositions = 2) {
        return $this->SilvercartOrderPositions()->getRange(0, $numberOfPositions);
    }

    /**
     * Returns a limited number of order positions.
     * 
     * @param int $numberOfPositions The number of positions to check for.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.01.2012
     */
    public function hasMoreSilvercartOrderPositionsThan($numberOfPositions = 2) {
        $hasMorePositions = false;

        if ($this->SilvercartOrderPositions()->Count() > $numberOfPositions) {
            $hasMorePositions = true;
        }

        return $hasMorePositions;
    }

    /**
     * customize backend fields
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 1.11.2010
     * @return FieldSet the form fields for the backend
     */
    public function getCMSFields() {
        $ignoreFields = array(
            'AmountGrossTotal',
        );
        
        $restrictFields = array(
            'SilvercartOrderStatus',
            'SilvercartPaymentMethod',
        );
        foreach ($this->db() as $fieldName => $fieldType) {
            if (in_array($fieldName, $ignoreFields)) {
                continue;
            }
            $restrictFields[] = $fieldName;
        }
        
        $fields = parent::getCMSFields(
                array(
                    'restrictFields' => $restrictFields,
                    'includeRelations' => array(
                        'has_many'  => true,
                    ),
                    'fieldClasses' => array(
                        'AmountTotal'           => 'SilvercartMoneyField',
                        'AmountGrossTotal'      => 'SilvercartMoneyField',
                        'HandlingCostPayment'   => 'SilvercartMoneyField',
                        'HandlingCostShipment'  => 'SilvercartMoneyField',
                    ),
                )
        );
        
        /***********************************************************************
        * TAB SECTION
        **********************************************************************/
        $fields->findOrMakeTab('Root.ShippingAddressTab',   _t('SilvercartAddressHolder.SHIPPINGADDRESS_TAB'));
        $fields->findOrMakeTab('Root.InvoiceAddressTab',    _t('SilvercartAddressHolder.INVOICEADDRESS_TAB'));
        $fields->findOrMakeTab('Root.PrintPreviewTab',      _t('SilvercartOrder.PRINT_PREVIEW'));
        
        /***********************************************************************
        * SIMPLE MODIFICATION SECTION
        **********************************************************************/
        $fields->makeFieldReadonly('OrderNumber');
        $fields->makeFieldReadonly('HasAcceptedTermsAndConditions');
        $fields->makeFieldReadonly('HasAcceptedRevocationInstruction');

        /***********************************************************************
        * REMOVALSECTION
        **********************************************************************/
        $fields->removeByName('Version');
        $fields->removeByName('Versions');

        /***********************************************************************
        * ADDITION SECTION
        **********************************************************************/
        $shippingFees = DataObject::get('SilvercartShippingFee');
        $shippingFeesDropdown = new DropdownField(
                'SilvercartShippingFeeID',
                $this->fieldLabel('SilvercartShippingMethod'),
                array(),
                $this->SilvercartShippingFeeID,
                null,
                _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE')
        );
        if ($shippingFees) {
            $shippingFeesDropdown->setSource($shippingFees->toDropDownMap('ID', 'FeeWithCarrierAndShippingMethod'));
            $fields->addFieldToTab('Root.Main', $shippingFeesDropdown);
        }
        
        $printPreviewField = new LiteralField(
                'PrintPreviewField',
                sprintf(
                    '<iframe width="100%%" height="100%%" border="0" src="%s"></iframe>',
                    SilvercartPrint::getPrintInlineURL($this)
                )
        );
        $fields->addFieldToTab('Root.PrintPreviewTab', $printPreviewField);
        
        $fields->addFieldToTab('Root.Main', new HiddenField('Version'));
        
        $address = singleton('SilvercartAddress');
        $fields->addFieldToTab('Root.ShippingAddressTab', new ReadonlyField('sa_Name',      $address->fieldLabel('Name'),       $this->SilvercartShippingAddress()->FirstName . ' ' . $this->SilvercartShippingAddress()->Surname));
        $fields->addFieldToTab('Root.ShippingAddressTab', new ReadonlyField('sa_Street',    $address->fieldLabel('Street'),     $this->SilvercartShippingAddress()->Street . ' ' . $this->SilvercartShippingAddress()->StreetNumber));
        $fields->addFieldToTab('Root.ShippingAddressTab', new ReadonlyField('sa_Addition',  $address->fieldLabel('Addition'),   $this->SilvercartShippingAddress()->Addition));
        $fields->addFieldToTab('Root.ShippingAddressTab', new ReadonlyField('sa_City',      $address->fieldLabel('City'),       strtoupper($this->SilvercartShippingAddress()->SilvercartCountry()->ISO2) . '-' . $this->SilvercartShippingAddress()->Postcode . ' ' . $this->SilvercartShippingAddress()->City));
        $fields->addFieldToTab('Root.ShippingAddressTab', new ReadonlyField('sa_Phone',     $address->fieldLabel('Phone'),      $this->SilvercartShippingAddress()->PhoneAreaCode . '/' . $this->SilvercartShippingAddress()->Phone));

        $fields->addFieldToTab('Root.InvoiceAddressTab', new ReadonlyField('ia_Name',       $address->fieldLabel('Name'),       $this->SilvercartInvoiceAddress()->FirstName . ' ' . $this->SilvercartInvoiceAddress()->Surname));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new ReadonlyField('ia_Street',     $address->fieldLabel('Street'),     $this->SilvercartInvoiceAddress()->Street . ' ' . $this->SilvercartInvoiceAddress()->StreetNumber));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new ReadonlyField('ia_Addition',   $address->fieldLabel('Addition'),   $this->SilvercartInvoiceAddress()->Addition));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new ReadonlyField('ia_City',       $address->fieldLabel('City'),       strtoupper($this->SilvercartInvoiceAddress()->SilvercartCountry()->ISO2) . '-' . $this->SilvercartInvoiceAddress()->Postcode . ' ' . $this->SilvercartInvoiceAddress()->City));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new ReadonlyField('ia_Phone',      $address->fieldLabel('Phone'),      $this->SilvercartInvoiceAddress()->PhoneAreaCode . '/' . $this->SilvercartInvoiceAddress()->Phone));

        /***********************************************************************
        * REORDER SECTION
        **********************************************************************/
        $fields->insertBefore($fields->dataFieldByName('SilvercartOrderStatusID'), 'AmountTotal');
        
        $mainGroup = new SilvercartFieldGroup('MainGroup', '', $fields);
        $mainGroup->pushAndBreak(   $fields->dataFieldByName('OrderNumber'));
        $mainGroup->pushAndBreak(   $fields->dataFieldByName('CustomersEmail'));
        $mainGroup->breakAndPush(   $fields->dataFieldByName('AmountTotal'));
        $mainGroup->breakAndPush(   $fields->dataFieldByName('HandlingCostPayment'));
        $mainGroup->push(           $fields->dataFieldByName('TaxAmountPayment'));
        $mainGroup->push(           $fields->dataFieldByName('TaxRatePayment'));
        $mainGroup->pushAndBreak(   $fields->dataFieldByName('SilvercartPaymentMethodID'));
        $mainGroup->breakAndPush(   $fields->dataFieldByName('HandlingCostShipment'));
        $mainGroup->push(           $fields->dataFieldByName('TaxAmountShipment'));
        $mainGroup->push(           $fields->dataFieldByName('TaxRateShipment'));
        $mainGroup->pushAndBreak(   $fields->dataFieldByName('SilvercartShippingFeeID'));
        $mainGroup->pushAndBreak(   $fields->dataFieldByName('WeightTotal'));
        $mainGroup->breakAndPush(   $fields->dataFieldByName('HasAcceptedTermsAndConditions'));
        $mainGroup->pushAndBreak(   $fields->dataFieldByName('HasAcceptedRevocationInstruction'));
        $fields->insertAfter($mainGroup, 'SilvercartOrderStatusID');

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }
    
    /**
     * create a invoice address for an order from customers data
     *
     * @param array $registrationData checkout forms submit data; only needed for anonymous customers
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.11.2010
     * @return void
     */
    public function createInvoiceAddress($registrationData = array()) {
        $member              = Member::currentUser();
        $orderInvoiceAddress = new SilvercartOrderInvoiceAddress();
        
        if (empty($registrationData)) {
            $addressData = $member->SilvercartInvoiceAddress()->toMap();
            unset($addressData['ID']);
            unset($addressData['ClassName']);
            unset($addressData['RecordClassName']);
            $orderInvoiceAddress->castedUpdate($addressData);
        } else {
            $orderInvoiceAddress->castedUpdate($registrationData);
            $orderInvoiceAddress->SilvercartCountryID = $registrationData['CountryID'];
        }
        $orderInvoiceAddress->write();
        $this->SilvercartInvoiceAddressID = $orderInvoiceAddress->ID;
        
        $this->write();
    }

    /**
     * create a shipping address for an order from customers data
     * writes $this to the database
     *
     * @param array $registrationData checkout forms submit data; only needed for anonymous customers
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.11.2010
     * @return void
     */
    public function createShippingAddress($registrationData = array()) {
        $member               = Member::currentUser();
        $orderShippingAddress = new SilvercartOrderShippingAddress();
       
        if (empty($registrationData)) {
            $addressData = $member->SilvercartShippingAddress()->toMap();
            unset($addressData['ID']);
            unset($addressData['ClassName']);
            unset($addressData['RecordClassName']);
            $orderShippingAddress->castedUpdate($addressData);
        } else {
            $orderShippingAddress->castedUpdate($registrationData);
            $orderShippingAddress->SilvercartCountryID = $registrationData['CountryID'];
        }

        $orderShippingAddress->write(); //write here to have an object ID
        $this->SilvercartShippingAddressID = $orderShippingAddress->ID;
       
        $this->write();
    }

    /**
     * creates an order from the cart
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public function createFromShoppingCart() {
        $member                 = Member::currentUser();
        $silvercartShoppingCart = $member->SilvercartShoppingCart();
        $silvercartShoppingCart->setPaymentMethodID($this->SilvercartPaymentMethodID);
        $silvercartShoppingCart->setShippingMethodID($this->SilvercartShippingMethodID);
        $this->MemberID         = $member->ID;

        $paymentObj = DataObject::get_by_id(
            'SilvercartPaymentMethod',
            $this->SilvercartPaymentMethodID
        );
        
        // VAT tax for shipping and payment fees
        $shippingMethod = DataObject::get_by_id('SilvercartShippingMethod', $this->SilvercartShippingMethodID);
        if ($shippingMethod) {
            $shippingFee  = $shippingMethod->getShippingFee();

            if ($shippingFee) {
                if ($shippingFee->SilvercartTax()) {
                    $this->TaxRateShipment   = $shippingFee->SilvercartTax()->getTaxRate();
                    $this->TaxAmountShipment = $shippingFee->getTaxAmount();
                }
            }
        }

        $paymentMethod = DataObject::get_by_id('SilvercartPaymentMethod', $this->SilvercartPaymentMethodID);
        if ($paymentMethod) {
            $paymentFee = $paymentMethod->SilvercartHandlingCost();

            if ($paymentFee) {
                if ($paymentFee->SilvercartTax()) {
                    $this->TaxRatePayment   = $paymentFee->SilvercartTax()->getTaxRate();
                    $this->TaxAmountPayment = $paymentFee->getTaxAmount();
                }
            }
        }

        // amount of all positions + handling fee of the payment method + shipping fee
        $totalAmount = $member->SilvercartShoppingCart()->getAmountTotal()->getAmount();
        
        $this->AmountTotal->setAmount(
            $totalAmount
        );
        $this->AmountTotal->setCurrency(SilvercartConfig::DefaultCurrency());

        // adjust orders standard status
        $orderStatus = DataObject::get_one(
            'SilvercartOrderStatus',
            sprintf(
                "\"Code\" = '%s'",
                $paymentObj->getDefaultOrderStatus()
            )
        );
        if ($orderStatus) {
            $this->SilvercartOrderStatusID = $orderStatus->ID;
        }
        
        // write order to have an id
        $this->write();
        
        SilvercartPlugin::call($this, 'createFromShoppingCart', array($this, $silvercartShoppingCart));
    }

    /**
     * convert cart positions in order positions
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public function convertShoppingCartPositionsToOrderPositions() {
        if ($this->extend('updateConvertShoppingCartPositionsToOrderPositions')) {
            return true;
        }
        
        $member = Member::currentUser();
        $filter = sprintf("`SilvercartShoppingCartID` = '%s'", $member->SilvercartShoppingCartID);
        $silvercartShoppingCart = $member->SilvercartShoppingCart();
        $silvercartShoppingCart->setPaymentMethodID($this->SilvercartPaymentMethodID);
        $silvercartShoppingCart->setShippingMethodID($this->SilvercartShippingMethodID);
        $shoppingCartPositions = DataObject::get('SilvercartShoppingCartPosition', $filter);

        if ($shoppingCartPositions) {
            foreach ($shoppingCartPositions as $shoppingCartPosition) {
                $product = $shoppingCartPosition->SilvercartProduct();

                if ($product) {
                    $orderPosition = new SilvercartOrderPosition();
                    $orderPosition->objectCreated = true;
                    $orderPosition->Price->setAmount($shoppingCartPosition->getPrice(true)->getAmount());
                    $orderPosition->Price->setCurrency($shoppingCartPosition->getPrice(true)->getCurrency());
                    $orderPosition->PriceTotal->setAmount($shoppingCartPosition->getPrice()->getAmount());
                    $orderPosition->PriceTotal->setCurrency($shoppingCartPosition->getPrice()->getCurrency());
                    $orderPosition->Tax                 = $shoppingCartPosition->getTaxAmount(true);
                    $orderPosition->TaxTotal            = $shoppingCartPosition->getTaxAmount();
                    $orderPosition->TaxRate             = $product->getTaxRate();
                    $orderPosition->ProductDescription  = $product->LongDescription;
                    $orderPosition->Quantity            = $shoppingCartPosition->Quantity;
                    $orderPosition->ProductNumber       = $product->ProductNumberShop;
                    $orderPosition->Title               = $product->Title;
                    $orderPosition->SilvercartOrderID   = $this->ID;
                    $orderPosition->SilvercartProductID = $product->ID;
                    $orderPosition->log                 = false;
                    $orderPosition->write();

                    // Call hook method on product if available
                    if ($product->hasMethod('ShoppingCartConvert')) {
                        $product->ShoppingCartConvert($this, $orderPosition);
                    }
                    // decrement stock quantity of the product
                    if (SilvercartConfig::EnableStockManagement()) {
                        $product->decrementStockQuantity($shoppingCartPosition->Quantity);
                    }
                    
                    $result = SilvercartPlugin::call($this, 'convertShoppingCartPositionToOrderPosition', array($shoppingCartPosition, $orderPosition), true, array());
                    
                    if (!empty($result)) {
                        $orderPosition = $result[0];
                    }
                    
                    $orderPosition->write();
                    unset($orderPosition);
                }
            }

            // Get charges and discounts for product values
            if ($silvercartShoppingCart->HasChargesAndDiscountsForProducts()) {
                $chargesAndDiscountsForProducts = $silvercartShoppingCart->ChargesAndDiscountsForProducts();
                
                foreach ($chargesAndDiscountsForProducts as $chargeAndDiscountForProduct) {
                    $orderPosition = new SilvercartOrderPosition();
                    $orderPosition->Price->setAmount($chargeAndDiscountForProduct->Price->getAmount());
                    $orderPosition->Price->setCurrency($chargeAndDiscountForProduct->Price->getCurrency());
                    $orderPosition->PriceTotal->setAmount($chargeAndDiscountForProduct->Price->getAmount());
                    $orderPosition->PriceTotal->setCurrency($chargeAndDiscountForProduct->Price->getCurrency());
                    $orderPosition->isChargeOrDiscount = true;
                    $orderPosition->chargeOrDiscountModificationImpact = $chargeAndDiscountForProduct->sumModificationImpact;
                    $orderPosition->Tax                 = $chargeAndDiscountForProduct->SilvercartTax->Title;

                    if ($this->IsPriceTypeGross()) {
                        $orderPosition->TaxTotal = $chargeAndDiscountForProduct->Price->getAmount() - ($chargeAndDiscountForProduct->Price->getAmount() / (100 + $chargeAndDiscountForProduct->SilvercartTax->Rate) * 100);
                    } else {
                        $orderPosition->TaxTotal = ($chargeAndDiscountForProduct->Price->getAmount() / 100 * (100 + $chargeAndDiscountForProduct->SilvercartTax->Rate)) - $chargeAndDiscountForProduct->Price->getAmount();
                    }

                    $orderPosition->TaxRate             = $chargeAndDiscountForProduct->SilvercartTax->Rate;
                    $orderPosition->ProductDescription  = $chargeAndDiscountForProduct->Name;
                    $orderPosition->Quantity            = 1;
                    $orderPosition->ProductNumber       = '';
                    $orderPosition->Title               = $chargeAndDiscountForProduct->Name;
                    $orderPosition->SilvercartOrderID   = $this->ID;
                    $orderPosition->SilvercartProductID = $product->ID;
                    $orderPosition->write();
                    unset($orderPosition);
                }
            }
            
            // Get taxable positions from registered modules
            $registeredModules = $member->SilvercartShoppingCart()->callMethodOnRegisteredModules(
                'ShoppingCartPositions',
                array(
                    $member->SilvercartShoppingCart(),
                    $member,
                    true
                )
            );

            foreach ($registeredModules as $moduleName => $moduleOutput) {
                foreach ($moduleOutput as $modulePosition) {
                    $orderPosition = new SilvercartOrderPosition();
                    if ($this->IsPriceTypeGross()) {
                        $orderPosition->Price->setAmount($modulePosition->Price);
                    } else {
                        $orderPosition->Price->setAmount($modulePosition->PriceNet);
                    }
                    $orderPosition->Price->setCurrency($modulePosition->Currency);
                    if ($this->IsPriceTypeGross()) {
                        $orderPosition->PriceTotal->setAmount($modulePosition->PriceTotal);
                    } else {
                        $orderPosition->PriceTotal->setAmount($modulePosition->PriceNetTotal);
                    }
                    $orderPosition->PriceTotal->setCurrency($modulePosition->Currency);
                    $orderPosition->Tax                 = 0;
                    $orderPosition->TaxTotal            = $modulePosition->TaxAmount;
                    $orderPosition->TaxRate             = $modulePosition->TaxRate;
                    $orderPosition->ProductDescription  = $modulePosition->LongDescription;
                    $orderPosition->Quantity            = $modulePosition->Quantity;
                    $orderPosition->Title               = $modulePosition->Name;
                    $orderPosition->SilvercartOrderID   = $this->ID;
                    $orderPosition->write();
                    unset($orderPosition);
                }
            }

            // Get nontaxable positions from registered modules
            $registeredModules = $member->SilvercartShoppingCart()->callMethodOnRegisteredModules(
                'ShoppingCartPositions',
                array(
                    $member->SilvercartShoppingCart(),
                    $member,
                    false
                )
            );

            foreach ($registeredModules as $moduleName => $moduleOutput) {
                foreach ($moduleOutput as $modulePosition) {
                    $orderPosition = new SilvercartOrderPosition();
                    if ($this->IsPriceTypeGross()) {
                        $orderPosition->Price->setAmount($modulePosition->Price);
                    } else {
                        $orderPosition->Price->setAmount($modulePosition->PriceNet);
                    }
                    $orderPosition->Price->setCurrency($modulePosition->Currency);
                    if ($this->IsPriceTypeGross()) {
                        $orderPosition->PriceTotal->setAmount($modulePosition->PriceTotal);
                    } else {
                        $orderPosition->PriceTotal->setAmount($modulePosition->PriceNetTotal);
                    }
                    $orderPosition->PriceTotal->setCurrency($modulePosition->Currency);
                    $orderPosition->Tax                 = 0;
                    $orderPosition->TaxTotal            = $modulePosition->TaxAmount;
                    $orderPosition->TaxRate             = $modulePosition->TaxRate;
                    $orderPosition->ProductDescription  = $modulePosition->LongDescription;
                    $orderPosition->Quantity            = $modulePosition->Quantity;
                    $orderPosition->Title               = $modulePosition->Name;
                    $orderPosition->SilvercartOrderID   = $this->ID;
                    $orderPosition->write();
                    unset($orderPosition);
                }
            }
            
            // Get charges and discounts for shopping cart total
            if ($silvercartShoppingCart->HasChargesAndDiscountsForTotal()) {
                $chargesAndDiscountsForTotal = $silvercartShoppingCart->ChargesAndDiscountsForTotal();
                
                foreach ($chargesAndDiscountsForTotal as $chargeAndDiscountForTotal) {
                    $orderPosition = new SilvercartOrderPosition();
                    $orderPosition->Price->setAmount($chargeAndDiscountForTotal->Price->getAmount());
                    $orderPosition->Price->setCurrency($chargeAndDiscountForTotal->Price->getCurrency());
                    $orderPosition->PriceTotal->setAmount($chargeAndDiscountForTotal->Price->getAmount());
                    $orderPosition->PriceTotal->setCurrency($chargeAndDiscountForTotal->Price->getCurrency());
                    $orderPosition->isChargeOrDiscount = true;
                    $orderPosition->chargeOrDiscountModificationImpact = $chargeAndDiscountForTotal->sumModificationImpact;
                    $orderPosition->Tax                 = $chargeAndDiscountForTotal->SilvercartTax->Title;
                    if ($this->IsPriceTypeGross()) {
                        $orderPosition->TaxTotal = $chargeAndDiscountForTotal->Price->getAmount() - ($chargeAndDiscountForTotal->Price->getAmount() / (100 + $chargeAndDiscountForTotal->SilvercartTax->Rate) * 100);
                    } else {
                        $orderPosition->TaxTotal = ($chargeAndDiscountForTotal->Price->getAmount() / 100 * (100 + $chargeAndDiscountForTotal->SilvercartTax->Rate)) - $chargeAndDiscountForTotal->Price->getAmount();
                    }
                    $orderPosition->TaxRate             = $chargeAndDiscountForTotal->SilvercartTax->Rate;
                    $orderPosition->ProductDescription  = $chargeAndDiscountForTotal->Name;
                    $orderPosition->Quantity            = 1;
                    $orderPosition->ProductNumber       = '';
                    $orderPosition->Title               = $chargeAndDiscountForTotal->Name;
                    $orderPosition->SilvercartOrderID   = $this->ID;
                    $orderPosition->SilvercartProductID = $product->ID;
                    $orderPosition->write();
                    unset($orderPosition);
                }
            }

            // Convert positions of registered modules
            $member->currentUser()->SilvercartShoppingCart()->callMethodOnRegisteredModules(
                'ShoppingCartConvert',
                array(
                    Member::currentUser()->SilvercartShoppingCart(),
                    Member::currentUser()
                )
            );
            
            // Delete the shoppingcart positions
            foreach ($shoppingCartPositions as $shoppingCartPosition) {
                $shoppingCartPosition->delete();
            }
        }
    }

    /**
     * save order to db
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public function save() {
        $this->write();
    }

    /**
     * set payment method for $this
     *
     * @param int $paymentMethodID id of payment method
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 05.01.2011
     */
    public function setPaymentMethod($paymentMethodID) {
        $paymentMethodObj = DataObject::get_by_id(
                        'SilvercartPaymentMethod',
                        $paymentMethodID
        );

        if ($paymentMethodObj) {
            $this->SilvercartPaymentMethodID = $paymentMethodObj->ID;
            $this->HandlingCostPayment->setAmount($paymentMethodObj->getHandlingCost()->getAmount());
            $this->HandlingCostPayment->setCurrency(SilvercartConfig::DefaultCurrency());
        }
    }

    /**
     * set status of $this
     *
     * @param SilvercartOrderStatus $orderStatus the order status object
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public function setOrderStatus($orderStatus) {
        $orderStatusSet = false;

        if ($orderStatus && $orderStatus->exists()) {
            $this->SilvercartOrderStatusID = $orderStatus->ID;
            $this->write();
            $orderStatusSet = true;
        }

        return $orderStatusSet;
    }

    /**
     * set status of $this
     *
     * @param int $orderStatusID the order status ID
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.04.2011
     */
    public function setOrderStatusByID($orderStatusID) {
        $orderStatusSet = false;

        if (DataObject::get_by_id('SilvercartOrderStatus', $orderStatusID)) {
            $this->SilvercartOrderStatusID = $orderStatusID;
            $this->write();
            $orderStatusSet = true;
        }

        return $orderStatusSet;
    }

    /**
     * Save the note from the form if there is one
     *
     * @param string $note the customers notice
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.12.10
     * @return void
     */
    public function setNote($note) {
        $this->setField('Note', $note);
    }

    /**
     * getter
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     *
     */
    public function getFormattedNote() {
        $note = $this->Note;
        $note = str_replace(
            '\r\n',
            '<br />',
            $note
        );

        return $note;
    }

    /**
     * save the cart´s weight
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.12.10
     * @return void
     */
    public function setWeight() {
        $member = Member::currentUser();
        if ($member->SilvercartShoppingCart()->getWeightTotal()) {
            $this->WeightTotal = $member->SilvercartShoppingCart()->getWeightTotal();
        }
    }

    /**
     * set the total price for this order
     *
     * @return void
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.12.10
     */
    public function setAmountTotal() {
        $member = Member::currentUser();

        if ($member && $member->SilvercartShoppingCart()) {
            $this->AmountTotal = $member->SilvercartShoppingCart()->getAmountTotal();
        }
    }

    /**
     * set the email for this order
     *
     * @param string $email the email address of the customer
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.12.10
     * @return void
     */
    public function setCustomerEmail($email = null) {
        $member = Member::currentUser();
        if ($member->Email) { //for registered customers
            $email = $member->Email;
        } else { // for anonymous customers
            $email = $email;
        }
        $this->CustomersEmail = $email;
    }
    
    /**
     * Set the status of the revocation instructions checkbox field.
     *
     * @param boolean $status The status of the field
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.10.2011
     */
    public function setHasAcceptedRevocationInstruction($status) {
        if ($status == 1) {
            $status = true;
        }
        
        $this->setField('HasAcceptedRevocationInstruction', $status);
    }
    
    /**
     * Set the status of the terms and conditions checkbox field.
     *
     * @param boolean $status The status of the field
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.10.2011
     */
    public function setHasAcceptedTermsAndConditions($status) {
        if ($status == 1) {
            $status = true;
        }
        
        $this->setField('HasAcceptedTermsAndConditions', $status);
    }

    /**
     * The shipping method is a relation + an attribte of the order
     *
     * @param int $shippingMethodID the ID of the shipping method
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.12.10
     * @copyright 2010 pixeltricks GmbH
     * @return void
     */
    public function setShippingMethod($shippingMethodID) {
        $selectedShippingMethod = DataObject::get_by_id(
            'SilvercartShippingMethod',
            $shippingMethodID
        );

        if ($selectedShippingMethod) {
            $this->SilvercartShippingMethodID    = $selectedShippingMethod->ID;
            $this->SilvercartShippingFeeID       = $selectedShippingMethod->getShippingFee()->ID;
            $this->HandlingCostShipment->setAmount($selectedShippingMethod->getShippingFee()->getPriceAmount());
            $this->HandlingCostShipment->setCurrency(SilvercartConfig::DefaultCurrency());
        }
    }

    /**
     * returns tax included in $this
     *
     * @return float
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function getTax() {
        $tax = 0.0;

        foreach ($this->SilvercartOrderPositions() as $orderPosition) {
            $tax += $orderPosition->TaxTotal;
        }

        $taxObj = new Money('Tax');
        $taxObj->setAmount($tax);
        $taxObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $taxObj;
    }

    /**
     * returns bills currency
     * 
     * @return string
     */
    public function getCurrency() {
        return $this->AmountTotal->getCurrency();
    }

    /**
     * returns the cart's net amount
     *
     * @return Money money object
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function getPriceNet() {
        /*
        $priceNet = $this->AmountTotal->getAmount() - $this->Tax->getAmount();
        */
        $priceNet = $this->AmountTotal->getAmount() - $this->HandlingCostShipment->getAmount() - $this->HandlingCostPayment->getAmount() - $this->getTax(true,true,true)->getAmount();

        $priceNetObj = new Money();
        $priceNetObj->setAmount($priceNet);
        $priceNetObj->setCurrency(SilvercartConfig::DefaultCurrency());
        
        return $priceNetObj;
    }

    /**
     * returns the cart's gross amount
     *
     * @return Money money object
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function getPriceGross() {
        return $this->AmountTotal;
    }
    
    /**
     * Returns all order positions without a tax value.
     *
     * @return DataObjectSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.12.2011
     */
    public function SilvercartOrderPositionsWithoutTax() {
        $orderPositions = new DataObjectSet();
        
        foreach ($this->SilvercartOrderPositions() as $orderPosition) {
            if (!$orderPosition->isChargeOrDiscount &&
                 $orderPosition->TaxRate == 0) {
                
                $orderPositions->push($orderPosition);
            }
        }
        
        return $orderPositions;
    }
    
    /**
     * Returns all regular order positions.
     *
     * @return DataObjectSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.12.2011
     */
    public function SilvercartOrderListPositions() {
        $orderPositions = new DataObjectSet();
        
        foreach ($this->SilvercartOrderPositions() as $orderPosition) {
            if (!$orderPosition->isChargeOrDiscount &&
                 $orderPosition->TaxRate > 0) {
                
                $orderPositions->push($orderPosition);
            }
        }
        
        return $orderPositions;
    }
    
    /**
     * Returns all order positions that contain charges and discounts for the 
     * shopping cart value.
     *
     * @return DataObjectSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.12.2011
     */
    public function SilvercartOrderChargePositionsTotal() {
        $chargePositions = new DataObjectSet();
        
        foreach ($this->SilvercartOrderPositions() as $orderPosition) {
            if ($orderPosition->isChargeOrDiscount &&
                $orderPosition->chargeOrDiscountModificationImpact == 'totalValue') {
                
                $chargePositions->push($orderPosition);
            }
        }
        
        return $chargePositions;
    }
    
    /**
     * Returns all order positions that contain charges and discounts for
     * product values.
     *
     * @return DataObjectSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.12.2011
     */
    public function SilvercartOrderChargePositionsProduct() {
        $chargePositions = new DataObjectSet();
        
        foreach ($this->SilvercartOrderPositions() as $orderPosition) {
            if ($orderPosition->isChargeOrDiscount &&
                $orderPosition->chargeOrDiscountModificationImpact == 'productValue') {
                
                $chargePositions->push($orderPosition);
            }
        }
        
        return $chargePositions;
    }
    
    /**
     * Returns the order value of all positions with a tax rate > 0 without any
     * fees and charges.
     *
     * @param boolean $includeChargesForProducts Indicates wether to include charges and
     *                                           discounts for products
     * @param boolean $includeChargesForTotal    Indicates wether to include charges and
     *                                           discounts for the shopping cart total
     * 
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 16.12.2011
     */
    public function getTaxableAmountGrossWithoutFees($includeChargesForProducts = false, $includeChargesForTotal = false) {
        $priceGross = new Money();
        $priceGross->setAmount(0);
        $priceGross->setCurrency(SilvercartConfig::DefaultCurrency());
        
        if ($includeChargesForTotal == 'false') {
            $includeChargesForTotal = false;
        }
        if ($includeChargesForProducts == 'false') {
            $includeChargesForProducts = false;
        }
        
        foreach ($this->SilvercartOrderPositions() as $position) {
            if ((
                    !$includeChargesForProducts &&
                     $position->isChargeOrDiscount &&
                     $position->chargeOrDiscountModificationImpact == 'productValue'
                ) || (
                    !$includeChargesForTotal &&
                     $position->isChargeOrDiscount &&
                     $position->chargeOrDiscountModificationImpact == 'totalValue'
                )
               ) {
                continue;
            }
            
            if ($position->TaxRate > 0) {
                $priceGross->setAmount(
                    $priceGross->getAmount() + $position->PriceTotal->getAmount()
                );
            }
        }
        
        return new DataObject(
            array(
                'Amount' => $priceGross
            )
        );
    }

    /**
     * Returns the order value of all positions with a tax rate > 0 without any
     * fees and charges.
     *
     * @param boolean $includeChargesForProducts Indicates wether to include charges and
     *                                           discounts for products
     * @param boolean $includeChargesForTotal    Indicates wether to include charges and
     *                                           discounts for the shopping cart total
     * 
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 16.12.2011
     */
    public function getTaxableAmountNetWithoutFees($includeChargesForProducts = false, $includeChargesForTotal = false) {
        $priceNet = new Money();
        $priceNet->setAmount(0);
        $priceNet->setCurrency(SilvercartConfig::DefaultCurrency());
        
        if ($includeChargesForTotal == 'false') {
            $includeChargesForTotal = false;
        }
        if ($includeChargesForProducts == 'false') {
            $includeChargesForProducts = false;
        }
        
        foreach ($this->SilvercartOrderPositions() as $position) {
            if ((
                    !$includeChargesForProducts &&
                     $position->isChargeOrDiscount &&
                     $position->chargeOrDiscountModificationImpact == 'productValue'
                ) || (
                    !$includeChargesForTotal &&
                     $position->isChargeOrDiscount &&
                     $position->chargeOrDiscountModificationImpact == 'totalValue'
                )
               ) {
                continue;
            }
            
            if ($position->TaxRate > 0) {
                $priceNet->setAmount(
                    $priceNet->getAmount() + $position->PriceTotal->getAmount()
                );
            }
        }
        
        return new DataObject(
            array(
                'Amount' => $priceNet
            )
        );
    }
    
    /**
     * Returns the order value of all positions with a tax rate > 0 without any
     * fees and charges.
     *
     * @param boolean $includeChargesForProducts Indicates wether to include charges and
     *                                           discounts for products
     * @param boolean $includeChargesForTotal    Indicates wether to include charges and
     *                                           discounts for the shopping cart total
     * 
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 16.12.2011
     */
    public function getTaxableAmountGrossWithFees($includeChargesForProducts = false, $includeChargesForTotal = false) {
        if ($includeChargesForTotal == 'false') {
            $includeChargesForTotal = false;
        }
        if ($includeChargesForProducts == 'false') {
            $includeChargesForProducts = false;
        }
        
        $priceGross = $this->getTaxableAmountGrossWithoutFees($includeChargesForProducts, $includeChargesForTotal)->Amount;
        
        $priceGross->setAmount(
            $priceGross->getAmount() +
            $this->HandlingCostPayment->getAmount()
        );

        $priceGross->setAmount(
            $priceGross->getAmount() +
            $this->HandlingCostShipment->getAmount()
        );
        
        return new DataObject(
            array(
                'Amount' => $priceGross
            )
        );
    }
    
    /**
     * Returns the order value of all positions with a tax rate > 0 without any
     * fees and charges.
     *
     * @param boolean $includeChargesForProducts Indicates wether to include charges and
     *                                           discounts for products
     * @param boolean $includeChargesForTotal    Indicates wether to include charges and
     *                                           discounts for the shopping cart total
     * 
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 16.12.2011
     */
    public function getTaxableAmountNetWithFees($includeChargesForProducts = false, $includeChargesForTotal = false) {
        if ($includeChargesForTotal == 'false') {
            $includeChargesForTotal = false;
        }
        if ($includeChargesForProducts == 'false') {
            $includeChargesForProducts = false;
        }
        
        $priceGross = $this->getTaxableAmountNetWithoutFees($includeChargesForProducts, $includeChargesForTotal)->Amount;
        
        $priceGross->setAmount(
            $priceGross->getAmount() +
            $this->HandlingCostPayment->getAmount()
        );

        $priceGross->setAmount(
            $priceGross->getAmount() +
            $this->HandlingCostShipment->getAmount()
        );
        
        return new DataObject(
            array(
                'Amount' => $priceGross
            )
        );
    }

    /**
     * Returns the sum of tax amounts grouped by tax rates for the products
     * of the order.
     *
     * @param boolean $includeChargesForProducts Indicates wether to include charges and
     *                                           discounts for products
     * @param boolean $includeChargesForTotal    Indicates wether to include charges and
     *                                           discounts for the shopping cart total
     * 
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 16.12.2011
     */
    public function getTaxRatesWithoutFees($includeChargesForProducts = false, $includeChargesForTotal = false) {
        if ($includeChargesForTotal === 'false') {
            $includeChargesForTotal = false;
        }
        if ($includeChargesForProducts === 'false') {
            $includeChargesForProducts = false;
        }
        
        $taxes = new DataObjectSet;
        
        foreach ($this->SilvercartOrderPositions() as $orderPosition) {
            if ((
                    !$includeChargesForProducts &&
                     $orderPosition->isChargeOrDiscount &&
                     $orderPosition->chargeOrDiscountModificationImpact == 'productValue'
                ) || (
                    !$includeChargesForTotal &&
                     $orderPosition->isChargeOrDiscount &&
                     $orderPosition->chargeOrDiscountModificationImpact == 'totalValue'
                )
               ) {
                continue;
            }
            
            $taxRate = $orderPosition->TaxRate;

            if ($taxRate > 0 &&
                !$taxes->find('Rate', $taxRate)) {
                
                $taxes->push(
                    new DataObject(
                        array(
                            'Rate'      => $taxRate,
                            'AmountRaw' => 0.0,
                        )
                    )
                );
            }
            $taxSection = $taxes->find('Rate', $taxRate);
            $taxSection->AmountRaw += $orderPosition->TaxTotal;
        }

        foreach ($taxes as $tax) {
            $taxObj = new Money;
            $taxObj->setAmount($tax->AmountRaw);
            $taxObj->setCurrency(SilvercartConfig::DefaultCurrency());

            $tax->Amount = $taxObj;
        }
        
        return $taxes;
    }

    /**
     * Returns the total amount of all taxes.
     *
     * @param boolean $excludeCharges Indicates wether to exlude charges and discounts
     *
     * @return Money a price amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2012 pixeltricks GmbH
     * @since 26.01.2012
     */
    public function getTaxTotal($excludeCharges = false) {
        $taxRates = $this->getTaxRatesWithFees(true, false);

        if (!$excludeCharges &&
             $this->HasChargePositionsForTotal()) {

            foreach ($this->SilvercartOrderChargePositionsTotal() as $charge) {
                $taxRate = $taxRates->find('Rate', $charge->TaxRate);

                if ($taxRate) {
                    $taxRateAmount   = $taxRate->Amount->getAmount();
                    $chargeTaxAmount = $charge->TaxTotal;
                    $taxRate->Amount->setAmount($taxRateAmount + $chargeTaxAmount);

                    if (round($taxRate->Amount->getAmount(), 2) === -0.00) {
                        $taxRate->Amount->setAmount($taxRate->Amount->getAmount() * -1);
                    }
                }
            }
        }

        return $taxRates;
    }
    
    /**
     * Returns the sum of tax amounts grouped by tax rates for the products
     * of the order.
     *
     * @param boolean $includeChargesForProducts Indicates wether to include charges and
     *                                           discounts for products
     * @param boolean $includeChargesForTotal    Indicates wether to include charges and
     *                                           discounts for the shopping cart total
     * 
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 16.12.2011
     */
    public function getTaxRatesWithFees($includeChargesForProducts = false, $includeChargesForTotal = false) {
        if ($includeChargesForTotal === 'false') {
            $includeChargesForTotal = false;
        }
        if ($includeChargesForProducts === 'false') {
            $includeChargesForProducts = false;
        }
        
        $taxes = $this->getTaxRatesWithoutFees($includeChargesForProducts, $includeChargesForTotal);
        
        // Shipping cost tax
        $taxRate = $this->TaxRateShipment;
        if ($taxRate > 0 &&
            !$taxes->find('Rate', $taxRate)) {

            $taxes->push(
                new DataObject(
                    array(
                        'Rate'      => $taxRate,
                        'AmountRaw' => 0.0,
                    )
                )
            );
        }
        $taxSection = $taxes->find('Rate', $taxRate);
        $taxSection->AmountRaw += $this->TaxAmountShipment;

        // Payment cost tax
        $taxRate = $this->TaxRatePayment;
        if ($taxRate > 0 &&
            !$taxes->find('Rate', $taxRate)) {

            $taxes->push(
                new DataObject(
                    array(
                        'Rate'      => $taxRate,
                        'AmountRaw' => 0.0,
                    )
                )
            );
        }
        $taxSection = $taxes->find('Rate', $taxRate);
        $taxSection->AmountRaw += $this->TaxAmountPayment;

        foreach ($taxes as $tax) {
            $taxObj = new Money;
            $taxObj->setAmount($tax->AmountRaw);
            $taxObj->setCurrency(SilvercartConfig::DefaultCurrency());

            $tax->Amount = $taxObj;
        }
        
        return $taxes;
    }

    /**
     * returns quantity of all products of the order
     *
     * @param int $productId if set only product quantity of this product is returned
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.11.10
     */
    public function getQuantity($productId = null) {
        $positions = $this->SilvercartOrderPositions();
        $quantity = 0;

        foreach ($positions as $position) {
            if ($productId === null ||
                    $position->SilvercartProduct()->ID === $productId) {

                $quantity += $position->Quantity;
            }
        }

        return $quantity;
    }

    /**
     * returns handling fee for choosen payment method
     *
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 23.11.2010
     */
    public function getHandlingCostPayment() {
        $handlingCosts = 0.0;
        $paymentObj = DataObject::get_by_id(
            'SilvercartPaymentMethod',
            $this->SilvercartPaymentMethodID
        );

        // get handling fee
        if ($paymentObj) {
            $handlingCosts += $paymentObj->getHandlingCost()->getAmount();
        }
        $handlingCostsObj = new Money('paymentHandlingCosts');
        $handlingCostsObj->setAmount($handlingCosts);
        $handlingCostsObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $handlingCostsObj;
    }
    
    /**
     * Returns plugin output.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.09.2011
     */
    public function OrderDetailInformation() {
        return SilvercartPlugin::call($this, 'OrderDetailInformation', array($this));
    }

    /**
     * Returns the order positions, shipping method, payment method etc. as
     * HTML table.
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.01.2012
     */
    public function OrderDetailTable() {
        $viewableData = new ViewableData();
        $template     = '';

        if ($this->IsPriceTypeGross()) {
            $template = $viewableData->customise($this)->renderWith('SilvercartOrderDetailsGross');
        } else {
            $template = $viewableData->customise($this)->renderWith('SilvercartOrderDetailsNet');
        }

        return $template;
    }
    
    /**
     * Indicates wether there are positions that are charges or discounts for
     * the product value.
     *
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 19.12.2011
     */
    public function HasChargePositionsForProduct() {
        $hasChargePositionsForProduct = false;

        foreach ($this->SilvercartOrderPositions() as $orderPosition) {
            if ($orderPosition->isChargeOrDiscount &&
                $orderPosition->chargeOrDiscountModificationImpact == 'productValue') {

                $hasChargePositionsForProduct = true;
            }
        }
        
        return $hasChargePositionsForProduct;
    }
    
    /**
     * Indicates wether there are positions that are charges or discounts for
     * the product value.
     *
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 19.12.2011
     */
    public function HasChargePositionsForTotal() {
        $hasChargePositionsForTotal = false;

        foreach ($this->SilvercartOrderPositions() as $orderPosition) {
            if ($orderPosition->isChargeOrDiscount &&
                $orderPosition->chargeOrDiscountModificationImpact == 'totalValue') {

                $hasChargePositionsForTotal = true;
            }
        }
        
        return $hasChargePositionsForTotal;
    }
    
    /**
     * Returns the price type dependant on the related member
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.06.2012
     */
    public function PriceType() {
        $member     = $this->Member();
        $priceType  = SilvercartConfig::DefaultPriceType();
        if ($member) {
            foreach ($member->Groups() as $group) {
                if (!empty($group->Pricetype) &&
                    $group->Pricetype != '---') {
                    $priceType = $group->Pricetype;
                    break;
                }
            }
        }
        return $priceType;
    }

    /**
     * Indicates wether this order is gross calculated or not.
     * 
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.06.2012
     */
    public function IsPriceTypeGross() {
        $isPriceTypeGross = false;

        if ($this->PriceType() == 'gross') {
            $isPriceTypeGross = true;
        }

        $isPriceTypeGross = SilvercartPlugin::call(
            $this,
            'IsPriceTypeGross',
            array(
                $isPriceTypeGross
            )
        );

        return $isPriceTypeGross;
    }

    /**
     * Indicates wether this order is net calculated or not.
     * 
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.06.2012
     */
    public function IsPriceTypeNet() {
        $isPriceTypeNet = false;

        if ($this->PriceType() == 'net') {
            $isPriceTypeNet = true;
        }

        $isPriceTypeNet = SilvercartPlugin::call(
            $this,
            'IsPriceTypeNet',
            array(
                $isPriceTypeNet
            )
        );

        return $isPriceTypeNet;
    }

    /**
     * writes a log entry
     * 
     * @param string $context context for log entry
     * @param string $text    text for log entry
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 17.11.2010
     */
    public function Log($context, $text) {
        $path = Director::baseFolder() . '/silvercart/log/' . $this->ClassName . '.log';
        $text = sprintf(
            "%s - Method: '%s' - %s\n",
            date('Y-m-d H:i:s'),
            $context,
            $text
        );
        file_put_contents($path, $text, FILE_APPEND);
    }

    /**
     * send a confirmation mail with order details to the customer $member
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.08.2011
     * @return void
     */
    public function sendConfirmationMail() {
        $params = array(
            'MailOrderConfirmation' => array(
                'Template'      => 'MailOrderConfirmation',
                'Recipient'     => $this->CustomersEmail,
                'Variables'     => array(
                    'FirstName'         => $this->SilvercartInvoiceAddress()->FirstName,
                    'Surname'           => $this->SilvercartInvoiceAddress()->Surname,
                    'Salutation'        => $this->SilvercartInvoiceAddress()->getSalutationText(),
                    'SilvercartOrder'   => $this
                ),
                'Attachments'   => null,
            ),
            'MailOrderNotification' => array(
                'Template'      => 'MailOrderNotification',
                'Recipient'     => Email::getAdminEmail(),
                'Variables'     => array(
                    'FirstName'         => $this->SilvercartInvoiceAddress()->FirstName,
                    'Surname'           => $this->SilvercartInvoiceAddress()->Surname,
                    'Salutation'        => $this->SilvercartInvoiceAddress()->getSalutationText(),
                    'SilvercartOrder'   => $this
                ),
                'Attachments'   => null,
            ),
        );
                
        $result = $this->extend('updateConfirmationMail', $params);
        
        SilvercartShopEmail::send(
            $params['MailOrderConfirmation']['Template'],
            $params['MailOrderConfirmation']['Recipient'],
            $params['MailOrderConfirmation']['Variables'],
            $params['MailOrderConfirmation']['Attachments']
        );
        SilvercartShopEmail::send(
            $params['MailOrderNotification']['Template'],
            $params['MailOrderNotification']['Recipient'],
            $params['MailOrderNotification']['Variables'],
            $params['MailOrderNotification']['Attachments']
        );
        $this->extend('onAfterConfirmationMail');
    }

    /**
     * Set a new/reserved ordernumber before writing and send attributed
     * ShopEmails.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.04.2011
     */
    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        
        if (empty ($this->OrderNumber)) {
            $this->OrderNumber = SilvercartNumberRange::useReservedNumberByIdentifier('OrderNumber');
        }
        if ($this->ID > 0 && $this->isChanged('SilvercartOrderStatusID')) {
            if (method_exists($this->SilvercartPaymentMethod(), 'handleOrderStatusChange')) {
                $this->SilvercartPaymentMethod()->handleOrderStatusChange($this);
            }
            $newOrderStatus = DataObject::get_by_id('SilvercartOrderStatus', $this->SilvercartOrderStatusID);
            
            if ($newOrderStatus) {
                if ($this->AmountTotalAmount > 0) {
                    $this->AmountTotal->setAmount($this->AmountTotalAmount);
                    $this->AmountTotal->setCurrency($this->AmountTotalCurrency);
                }
                
                $newOrderStatus->sendMailFor($this);
            }
        }
        $this->extend('updateOnBeforeWrite');
    }

    /**
     * hook triggered after write
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 9.11.10
     * @return void
     */
    protected function onAfterWrite() {
        parent::onAfterWrite();

        $this->extend('updateOnAfterWrite');
    }

    /**
     * Recalculates the order totals for the attributed positions.
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.03.2012
     */
    public function recalculate() {
        $totalAmount = 0.0;

        foreach ($this->SilvercartOrderPositions() as $orderPosition) {
            $totalAmount += $orderPosition->PriceTotal->getAmount();
        }

        $this->AmountTotal->setAmount(
            $totalAmount
        );

        $this->write();
    }

    /**
     * Returns the shipping method of this order and injects the shipping address
     *
     * @return SilvercartShippingMethod
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.04.2012
     */
    public function SilvercartShippingMethod() {
        $silvercartShippingMethod = null;
        if ($this->getComponent('SilvercartShippingMethod')) {
            $silvercartShippingMethod = $this->getComponent('SilvercartShippingMethod');
            $silvercartShippingMethod->setShippingAddress($this->SilvercartShippingAddress());
        }
        return $silvercartShippingMethod;
    }

    /**
     * returns the orders total amount as string incl. currency.
     *
     * @return string
     * 
     * @deprecated
     */
    public function getAmountTotalNice() {
        return str_replace('.', ',', number_format($this->AmountTotalAmount, 2)) . ' ' . $this->AmountTotalCurrency;
    }

    /**
     * returns carts net value including all editional costs
     *
     * @return Money amount
     * 
     * @deprecated
     */
    public function getAmountNet() {
        user_error('SilvercartOrder::getAmountNet() is marked as deprecated!', E_USER_ERROR);
        $amountNet = $this->AmountGrossTotal->getAmount() - $this->Tax->getAmount();
        $amountNetObj = new Money();
        $amountNetObj->setAmount($amountNet);
        $amountNetObj->setCurrency(SilvercartConfig::DefaultCurrency());

        return $amountNetObj;
    }

    /**
     * returns carts gross value including all editional costs
     *
     * @return Money
     * 
     * @deprecated
     */
    public function getAmountGross() {
        user_error('SilvercartOrder::getAmountGross() is marked as deprecated!', E_USER_ERROR);
        return $this->AmountGrossTotal;
    }

    /**
     * returns the orders total amount as string incl. currency.
     *
     * @return string
     * 
     * @deprecated
     */
    public function getAmountGrossTotalNice() {
        user_error('SilvercartOrder::getAmountGrossTotalNice() is marked as deprecated!', E_USER_ERROR);
        return $this->getAmountTotalNice();
    }
}

/**
 * Used to redefine some form fields in the search box.
 *
 * @package Silvercart
 * @subpackage Order
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 10.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartOrder_CollectionController extends ModelAdmin_CollectionController {
    
    /**
     * Replace the OrderStatus textfield with a dropdown field.
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 10.03.2011
     */
    public function SearchForm() {
        $orderStatusSet     = DataObject::get('SilvercartOrderStatus');
        $paymentMethodSet   = DataObject::get('SilvercartPaymentMethod');
        $searchForm         = parent::SearchForm();

        if ($orderStatusSet) {
            $searchForm->Fields()->removeByName('SilvercartOrderStatus__ID');

            $dropdownField = new DropdownField(
                'SilvercartOrderStatus__ID',
                _t('SilvercartOrder.STATUS', 'order status'),
                $orderStatusSet->map(
                    'ID',
                    'Title',
                    _t('SilvercartOrderSearchForm.PLEASECHOOSE')
                )
            );

            $searchForm->Fields()->push($dropdownField);
        }
        
        if ($paymentMethodSet) {
            $searchForm->Fields()->removeByName('SilvercartPaymentMethod__ID');

            $dropdownField = new DropdownField(
                'SilvercartPaymentMethod__ID',
                _t('SilvercartPaymentMethod.SINGULARNAME', 'Payment method'),
                $paymentMethodSet->map(
                    'ID',
                    'Title',
                    _t('SilvercartOrderSearchForm.PLEASECHOOSE')
                )
            );

            $searchForm->Fields()->insertAfter($dropdownField, 'SilvercartOrderStatus__ID');
        }

        $this->extend('updateSearchForm', $searchForm);
        
        return $searchForm;
    }
    
    /**
     * We modify the original search query here, so that the administrator can
     * search for the firstname and surname in both the invoice and shipping
     * address of the order with only one input field for each.
     * 
     * @param mixed $searchCriteria Search criteria
     *
     * @return SQLQuery
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.08.2011
     */
    public function getSearchQuery($searchCriteria) {
        $query = parent::getSearchQuery($searchCriteria);
        $query->leftJoin( 'SilvercartAddress', 'SilvercartInvoiceAddress.ID = SilvercartOrder.SilvercartInvoiceAddressID', 'SilvercartInvoiceAddress');
        $query->leftJoin( 'SilvercartAddress', 'SilvercartShippingAddress.ID = SilvercartOrder.SilvercartShippingAddressID', 'SilvercartShippingAddress');

        if (isset($query->from['SilvercartOrderShippingAddress'])) {
            unset($query->from['SilvercartOrderShippingAddress']);
        }
        
        if (isset($query->from['SilvercartAddress'])) {
            unset($query->from['SilvercartAddress']);
        }

        $whereIdx = 0;
        foreach ($query->where as $sqlWhere) {
            if (strpos($sqlWhere, '"SilvercartAddress"."FirstName"') !== false) {
                $searchStr = str_replace('"SilvercartAddress"."FirstName" LIKE ', '', $sqlWhere);
                
                $query->where[$whereIdx] = sprintf(
                    '"SilvercartInvoiceAddress"."FirstName" LIKE %s OR "SilvercartShippingAddress"."FirstName" LIKE %s',
                    $searchStr,
                    $searchStr
                );
            }
            if (strpos($sqlWhere, '"SilvercartAddress"."Surname"') !== false) {
                $searchStr = str_replace('"SilvercartAddress"."Surname" LIKE ', '', $sqlWhere);
                
                $query->where[$whereIdx] = sprintf(
                    '"SilvercartInvoiceAddress"."Surname" LIKE %s OR "SilvercartShippingAddress"."Surname" LIKE %s',
                    $searchStr,
                    $searchStr
                );
            }
            $whereIdx++;
        }

        $this->extend('updateGetSearchQuery', $searchCriteria, $query);

        return $query;
    }

    /**
     * Extend the getResultsTable method in DataObjectDecorators.
     *
     * @param array $searchCriteria The search criteria
     *
     * @return TableListField
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.2012
     */
    public function getResultsTable($searchCriteria) {
        $tableField = parent::getResultsTable($searchCriteria);
        $tableField->addPrintAction();
        $this->extend('getResultsTable', $tableField, $searchCriteria);
        return $tableField;
    }
    
    /**
     * Removes the button "create order" from the model admin
     * 
     * @return bool false
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.7.2011 
     */
    public function CreateForm() {
        return false;
    }
}

/**
 * The Silvercart Order RecordController.
 *
 * @package Silvercart
 * @subpackage Order
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 08.02.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartOrder_RecordController extends ModelAdmin_RecordController {
    
    /**
     * EditForm
     *
     * @return Form
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.2012
     */
    public function EditForm() {
        $form = parent::EditForm();
        $actions = $form->Actions();
        $actions->push($this->addFormAction("printDataObject", _t('SilvercartOrder.PRINT', 'Print order')));
        return $form;
    }
    
}
