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
class SilvercartOrder extends DataObject {

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
        'AmountTotal'                   => 'Money', // value of all products
        'AmountGrossTotal'              => 'Money', // value of all products + transaction fee
        'HandlingCostPayment'           => 'Money',
        'HandlingCostShipment'          => 'Money',
        'TaxRatePayment'                => 'Int',
        'TaxRateShipment'               => 'Int',
        'TaxAmountPayment'              => 'Float',
        'TaxAmountShipment'             => 'Float',
        'Note'                          => 'Text',
        'WeightTotal'                   => 'Int', //unit is gramm
        'CarrierAndShippingMethodTitle' => 'VarChar(100)',
        'PaymentMethodTitle'            => 'VarChar(100)',
        'CustomersEmail'                => 'VarChar(60)',
        'OrderNumber'                   => 'VarChar(128)',
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
        'SilvercartOrderPositions' => 'SilvercartOrderPosition'
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
        'AmountGrossTotalNice'      => 'VarChar',
    );

    /**
     * Searchable fields.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 28.03.2011
     */
    public static $searchable_fields = array(
        'Created',
        'Member.FirstName',
        'Member.Surame',
        'SilvercartOrderStatus.ID'
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
     * Constructor. We localize the static variables here.
     *
     * @param array|null $record      This will be null for a new database record.
     *                                  Alternatively, you can pass an array of
     *                                  field values.  Normally this contructor is only used by the internal systems that get objects from the database.
     * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.  Singletons
     *                                  don't have their defaults set.
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        self::$singular_name    = _t('SilvercartOrder.SINGULARNAME', 'order');
        self::$plural_name      = _t('SilvercartOrder.PLURALNAME', 'orders');
        parent::__construct($record, $isSingleton);
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
            'CreatedNice'                   => _t('SilvercartPage.ORDER_DATE'),
            'OrderNumber'                   => _t('SilvercartOrder.ORDERNUMBER', 'ordernumber'),
            'ShippingAddressSummary'        => _t('SilvercartShippingAddress.SINGULARNAME'),
            'InvoiceAddressSummary'         => _t('SilvercartInvoiceAddress.SINGULARNAME'),
            'AmountGrossTotalNice'          => _t('SilvercartOrder.ORDER_VALUE', 'order value'),
            'SilvercartOrderStatus.Title'   => _t('SilvercartOrderStatus.SINGULARNAME', 'order status')
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
                'ID'                            => _t('SilvercartOrder.ORDER_ID'),
                'Created'                       => _t('SilvercartPage.ORDER_DATE'),
                'SilvercartShippingFee'         => _t('SilvercartOrder.SHIPPINGRATE', 'shipping costs'),
                'Note'                          => _t('SilvercartPage.REMARKS'),
                'Member'                        => _t('SilvercartOrder.CUSTOMER', 'customer'),
                'SilvercartShippingAddress'     => _t('SilvercartShippingAddress.SINGULARNAME'),
                'SilvercartInvoiceAddress'      => _t('SilvercartInvoiceAddress.SINGULARNAME'),
                'SilvercartOrderStatus'         => _t('SilvercartOrder.STATUS', 'order status'),
                'AmountTotal'                   => _t('SilvercartOrder.AMOUNTTOTAL'),
                'AmountGrossTotal'              => _t('SilvercartOrder.AMOUNTGROSSTOTAL'),
                'HandlingCostPayment'           => _t('SilvercartOrder.HANDLINGCOSTPAYMENT'),
                'HandlingCostShipment'          => _t('SilvercartOrder.HANDLINGCOSTSHIPMENT'),
                'TaxRatePayment'                => _t('SilvercartOrder.TAXRATEPAYMENT'),
                'TaxRateShipment'               => _t('SilvercartOrder.TAXRATESHIPMENT'),
                'TaxAmountPayment'              => _t('SilvercartOrder.TAXAMOUNTPAYMENT'),
                'TaxAmountShipment'             => _t('SilvercartOrder.TAXAMOUNTSHIPMENT'),
                'Note'                          => _t('SilvercartOrder.NOTE'),
                'WeightTotal'                   => _t('SilvercartOrder.WEIGHTTOTAL'),
                'CarrierAndShippingMethodTitle' => _t('SilvercartOrder.CARRIERANDSHIPPINGMETHODTITLE'),
                'PaymentMethodTitle'            => _t('SilvercartOrder.PAYMENTMETHODTITLE'),
                'CustomersEmail'                => _t('SilvercartOrder.CUSTOMERSEMAIL')
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
                'filter'    => 'PartialMatchFilter'
            ),
            'Member.FirstName' => array(
                'title'     => _t('SilvercartAddress.FIRSTNAME'),
                'filter'    => 'PartialMatchFilter'
            ),
            'Member.Surname' => array(
                'title'     => _t('SilvercartAddress.SURNAME'),
                'filter'    => 'PartialMatchFilter'
            ),
            'SilvercartOrderStatus.Title' => array(
                'title'     => _t('SilvercartOrder.STATUS', 'order status'),
                'filter'    => 'ExactMatchFilter'
            )
        );
        $this->extend('updateSearchableFields', $searchableFields);

        return $searchableFields;
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
     * returns the orders total amount as string incl. currency.
     *
     * @return string
     */
    public function getAmountGrossTotalNice() {
        return str_replace('.', ',', number_format($this->AmountTotalAmount, 2)) . ' ' . $this->AmountTotalCurrency;
    }

        /**
     * customize backend fields
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 1.11.2010
     * @return FieldSet the form fields for the backend
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('Versandadresse');
        $fields->removeByName('Rechnungsadresse');
        $fields->removeByName('Versionen');
        $fields->removeByName('Silvercart Products');

        $fields->fieldByName('Root.SilvercartOrderPositions')->Title = _t('SilvercartOrderPosition.PLURALNAME');

        $fields->addFieldToTab('Root.Versandadresse', new ReadonlyField('sa_Name', 'Name', $this->SilvercartShippingAddress()->FirstName . ' ' . $this->SilvercartShippingAddress()->Surname));
        $fields->addFieldToTab('Root.Versandadresse', new ReadonlyField('sa_Street', 'Straße', $this->SilvercartShippingAddress()->Street . ' ' . $this->SilvercartShippingAddress()->StreetNumber));
        $fields->addFieldToTab('Root.Versandadresse', new ReadonlyField('sa_Addition', 'Zusatz', $this->SilvercartShippingAddress()->Addition));
        $fields->addFieldToTab('Root.Versandadresse', new ReadonlyField('sa_City', 'PLZ/Ort', strtoupper($this->SilvercartShippingAddress()->SilvercartCountry()->ISO2) . '-' . $this->SilvercartShippingAddress()->Postcode . ' ' . $this->SilvercartShippingAddress()->City));
        $fields->addFieldToTab('Root.Versandadresse', new ReadonlyField('sa_Phone', 'Telefon', $this->SilvercartShippingAddress()->PhoneAreaCode . '/' . $this->SilvercartShippingAddress()->Phone));

        $fields->addFieldToTab('Root.Rechnungsadresse', new ReadonlyField('ia_Name', 'Name', $this->SilvercartInvoiceAddress()->FirstName . ' ' . $this->SilvercartInvoiceAddress()->Surname));
        $fields->addFieldToTab('Root.Rechnungsadresse', new ReadonlyField('ia_Street', 'Straße', $this->SilvercartInvoiceAddress()->Street . ' ' . $this->SilvercartInvoiceAddress()->StreetNumber));
        $fields->addFieldToTab('Root.Rechnungsadresse', new ReadonlyField('ia_Addition', 'Zusatz', $this->SilvercartInvoiceAddress()->Addition));
        $fields->addFieldToTab('Root.Rechnungsadresse', new ReadonlyField('ia_City', 'PLZ/Ort', strtoupper($this->SilvercartInvoiceAddress()->SilvercartCountry()->ISO2) . '-' . $this->SilvercartInvoiceAddress()->Postcode . ' ' . $this->SilvercartInvoiceAddress()->City));
        $fields->addFieldToTab('Root.Rechnungsadresse', new ReadonlyField('ia_Phone', 'Telefon', $this->SilvercartInvoiceAddress()->PhoneAreaCode . '/' . $this->SilvercartInvoiceAddress()->Phone));

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
    public function createInvoiceAddress($registrationData = "") {
        $member = Member::currentUser();
        $orderInvoiceAddress = new SilvercartOrderInvoiceAddress();
        if ($member->ClassName != "SilvercartAnonymousCustomer") {//for registered users

            /*
             * a member might not have an invoice address Surname/FirstName
             */
            $orderInvoiceAddress->Salutation = $member->SilvercartInvoiceAddress()->Salutation;
            $orderInvoiceAddress->Surname = $member->SilvercartInvoiceAddress()->Surname;
            $orderInvoiceAddress->FirstName = $member->SilvercartInvoiceAddress()->FirstName;
            $orderInvoiceAddress->Street = $member->SilvercartInvoiceAddress()->Street;
            $orderInvoiceAddress->StreetNumber = $member->SilvercartInvoiceAddress()->StreetNumber;
            $orderInvoiceAddress->Postcode = $member->SilvercartInvoiceAddress()->Postcode;
            $orderInvoiceAddress->City = $member->SilvercartInvoiceAddress()->City;
            $orderInvoiceAddress->PhoneAreaCode = $member->SilvercartInvoiceAddress()->PhoneAreaCode;
            $orderInvoiceAddress->Phone = $member->SilvercartInvoiceAddress()->Phone;
            $orderInvoiceAddress->SilvercartCountryID = $member->SilvercartInvoiceAddress()->SilvercartCountryID;
            $orderInvoiceAddress->write();
            $this->SilvercartInvoiceAddressID = $orderInvoiceAddress->ID;
        } else { //for anonymous customers
            $orderInvoiceAddress->castedUpdate($registrationData);
            $orderInvoiceAddress->SilvercartCountryID = $registrationData['CountryID'];
            $orderInvoiceAddress->write();
            $this->SilvercartInvoiceAddressID = $orderInvoiceAddress->ID;
        }
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
    public function createShippingAddress($registrationData = "") {
        $member = Member::currentUser();
        $orderShippingAddress = new SilvercartOrderShippingAddress();
        if ($member->ClassName != "SilvercartAnonymousCustomer") {// for registered customers

            /*
             * get Surname and FirstName from the address, if not available from the member
             */
            $orderShippingAddress->Salutation = $member->SilvercartShippingAddress()->Salutation;
            $orderShippingAddress->Surname = $member->SilvercartShippingAddress()->Surname;
            $orderShippingAddress->FirstName = $member->SilvercartShippingAddress()->FirstName;
            $orderShippingAddress->Street = $member->SilvercartShippingAddress()->Street;
            $orderShippingAddress->StreetNumber = $member->SilvercartShippingAddress()->StreetNumber;
            $orderShippingAddress->Postcode = $member->SilvercartShippingAddress()->Postcode;
            $orderShippingAddress->City = $member->SilvercartShippingAddress()->City;
            $orderShippingAddress->PhoneAreaCode = $member->SilvercartShippingAddress()->PhoneAreaCode;
            $orderShippingAddress->Phone = $member->SilvercartShippingAddress()->Phone;
            $orderShippingAddress->SilvercartCountryID = $member->SilvercartShippingAddress()->SilvercartCountryID;
            $orderShippingAddress->write(); //write here to have an object ID
            $this->SilvercartShippingAddressID = $orderShippingAddress->ID;
        } else { //for anonymous customers
            $orderShippingAddress->castedUpdate($registrationData);
            $orderShippingAddress->SilvercartCountryID = $registrationData['CountryID'];
            $orderShippingAddress->write(); //write here to have an object ID
            $this->SilvercartShippingAddressID = $orderShippingAddress->ID;
        }
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
        $member = Member::currentUser();
        $this->MemberID = $member->ID;

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

        // price sum of all positions
        $this->AmountTotal->setAmount($member->SilvercartShoppingCart()->getAmountTotal()->getAmount());
        $this->AmountTotal->setCurrency(SilvercartConfig::DefaultCurrency());

        // amount of all positions + handling fee of the payment method + shipping fee
        $totalAmount = 
            $this->HandlingCostPayment->getAmount() +
            $this->HandlingCostShipment->getAmount() +
            $member->SilvercartShoppingCart()->getAmountTotal()->getAmount();

        $this->AmountTotal->setAmount(
            $totalAmount
        );
        $this->AmountGrossTotal->setCurrency('EUR');

        // adjust orders standard status
        $paymentObj = DataObject::get_by_id(
            'SilvercartPaymentMethod',
            $this->SilvercartPaymentMethodID
        );
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

        // Convert shopping cart positions
        $this->convertShoppingCartPositionsToOrderPositions();
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
    protected function convertShoppingCartPositionsToOrderPositions() {
        $member = Member::currentUser();
        $filter = sprintf("`SilvercartShoppingCartID` = '%s'", $member->SilvercartShoppingCartID);
        $shoppingCartPositions = DataObject::get('SilvercartShoppingCartPosition', $filter);

        if ($shoppingCartPositions) {
            foreach ($shoppingCartPositions as $shoppingCartPosition) {
                $product = $shoppingCartPosition->SilvercartProduct();

                if ($product) {
                    $orderPosition = new SilvercartOrderPosition();
                    $orderPosition->Price->setAmount($product->Price->getAmount());
                    $orderPosition->Price->setCurrency($product->Price->getCurrency());
                    $orderPosition->PriceTotal->setAmount($product->Price->getAmount() * $shoppingCartPosition->Quantity);
                    $orderPosition->PriceTotal->setCurrency($product->Price->getCurrency());
                    $orderPosition->Tax                 = $product->getTaxAmount();
                    $orderPosition->TaxTotal            = $product->getTaxAmount() * $shoppingCartPosition->Quantity;
                    $orderPosition->TaxRate             = $product->getTaxRate();
                    $orderPosition->ProductDescription  = $product->LongDescription;
                    $orderPosition->Quantity            = $shoppingCartPosition->Quantity;
                    $orderPosition->ProductNumber       = $product->ProductNumberShop;
                    $orderPosition->Title               = $product->Title;
                    $orderPosition->SilvercartOrderID   = $this->ID;
                    $orderPosition->SilvercartProductID = $product->ID;

                    // Call hook method on product if available
                    if ($product->hasMethod('ShoppingCartConvert')) {
                        $product->ShoppingCartConvert($this, $orderPosition);
                    }

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
                    $orderPosition->Price->setAmount($modulePosition->Price);
                    $orderPosition->Price->setCurrency($modulePosition->Currency);
                    $orderPosition->PriceTotal->setAmount($modulePosition->PriceTotal);
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
                    $orderPosition->Price->setAmount($modulePosition->Price);
                    $orderPosition->Price->setCurrency($modulePosition->Currency);
                    $orderPosition->PriceTotal->setAmount($modulePosition->PriceTotal);
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
            $this->PaymentMethodTitle = $paymentMethodObj->Name;
            $this->HandlingCostPayment->setAmount($paymentMethodObj->getHandlingCost()->getAmount());
            $this->HandlingCostPayment->setCurrency(SilvercartConfig::DefaultCurrency());
        }
    }

    /**
     * set status of $this
     *
     * @param OrderStatus $orderStatus the order status object
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
            $this->CarrierAndShippingMethodTitle = $selectedShippingMethod->SilvercartCarrier()->Title . "-" . $selectedShippingMethod->Title;
            $this->SilvercartShippingFeeID       = $selectedShippingMethod->getShippingFee()->ID;
            $this->HandlingCostShipment->setAmount($selectedShippingMethod->getShippingFee()->Price->getAmount());
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
     * returns carts net value including all editional costs
     *
     * @return Money amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function getAmountNet() {
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 05.01.2011
     */
    public function getAmountGross() {
        return $this->AmountGrossTotal;
    }

    /**
     * returns bills currency
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 06.01.2011
     */
    public function getCurrency() {
        return $this->AmountGrossTotal->getCurrency();
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
        $priceNet = $this->AmountTotal->getAmount() - $this->Tax->getAmount();
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
     * Returns the order value of all positions with a tax rate > 0 without any
     * fees.
     *
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public function getTaxableAmountGross() {
        $priceGross = new Money();
        $priceGross->setAmount(0);
        $priceGross->setCurrency(SilvercartConfig::DefaultCurrency());

        foreach ($this->SilvercartOrderPositions() as $position) {
            if ($position->TaxRate > 0) {
                $priceGross->setAmount(
                    $priceGross->getAmount() + $position->PriceTotal->getAmount()
                );
            }
        }

        return $priceGross;
    }

    /**
     * Returns the order value of all positions with a tax rate > 0 including
     * all fees.
     *
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public function getTaxableAmountGrossWithFees() {
        $priceGross = new Money();
        $priceGross->setAmount(0);
        $priceGross->setCurrency(SilvercartConfig::DefaultCurrency());

        foreach ($this->SilvercartOrderPositions() as $position) {
            if ($position->TaxRate > 0) {
                $priceGross->setAmount(
                    $priceGross->getAmount() + $position->PriceTotal->getAmount()
                );
            }
        }

        $priceGross->setAmount(
            $priceGross->getAmount() +
            $this->HandlingCostPayment->getAmount()
        );

        $priceGross->setAmount(
            $priceGross->getAmount() +
            $this->HandlingCostShipment->getAmount()
        );

        return $priceGross;
    }

    /**
     * Returns the sum of tax amounts grouped by tax rates for the products
     * of the order.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 03.02.2011
     */
    public function getTaxRatesWithoutFees() {
        $taxes = new DataObjectSet;

        foreach ($this->SilvercartOrderPositions() as $orderPosition) {
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
     * Returns the sum of tax amounts grouped by tax rates for the products
     * and all fees of the order.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 03.02.2011
     */
    public function getTaxRatesWithFees() {
        $taxes = $this->getTaxRatesWithoutFees();

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
     * @return float
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.11.2010
     * @return void
     */
    public function sendConfirmationMail() {
        SilvercartShopEmail::send(
            'MailOrderConfirmation',
            $this->CustomersEmail,
            array(
                'FirstName'         => $this->SilvercartInvoiceAddress()->FirstName,
                'Surname'           => $this->SilvercartInvoiceAddress()->Surname,
                'Salutation'        => $this->SilvercartInvoiceAddress()->Salutation,
                'SilvercartOrder'   => $this
            )
        );
        SilvercartShopEmail::send(
            'MailOrderNotification',
            Email::getAdminEmail(),
            array(
                'FirstName'         => $this->SilvercartInvoiceAddress()->FirstName,
                'Surname'           => $this->SilvercartInvoiceAddress()->Surname,
                'Salutation'        => $this->SilvercartInvoiceAddress()->Salutation,
                'SilvercartOrder'   => $this
            )
        );
    }

    /**
     * Set a new/reserved ordernumber before writing
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.04.2011
     */
    protected function  onBeforeWrite() {
        parent::onBeforeWrite();
        if (empty ($this->OrderNumber)) {
            $this->OrderNumber = SilvercartNumberRange::useReservedNumberByIdentifier('OrderNumber');
        }
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
        if ($this->AmountGrossTotal->hasAmount() === false) {
            $price = $this->AmountTotal->getAmount() + $this->HandlingCostShipment->getAmount();
            $this->AmountGrossTotal->setAmount($price);
            $this->AmountGrossTotal->setCurrency(SilvercartConfig::DefaultCurrency());
            $this->write();
        }
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
        $orderStatusSet = DataObject::get('SilvercartOrderStatus');
        $searchForm     = parent::SearchForm();

        if ($orderStatusSet) {
            $searchForm->Fields()->removeByName('SilvercartOrderStatus__Title');

            $dropdownField = new DropdownField(
                'SilvercartOrderStatus__Title',
                _t('SilvercartOrder.STATUS', 'order status'),
                $orderStatusSet->map(
                    'ID',
                    'Title',
                    _t('SilvercartOrderSearchForm.PLEASECHOOSE')
                )
            );

            $searchForm->Fields()->insertAfter($dropdownField, 'Member__Surname');
        }

        $this->extend('updateSearchForm', $searchForm);
        
        return $searchForm;
    }

    /**
     * Extend the getResultsTable method in DataObjectDecorators.
     *
     * @param array $searchCriteria The search criteria
     *
     * @return TableListField
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 28.03.2011
     */
    public function getResultsTable($searchCriteria) {
        $tableField = parent::getResultsTable($searchCriteria);

        $this->extend('getResultsTable', $tableField, $searchCriteria);
        
        return $tableField;
    }
}
