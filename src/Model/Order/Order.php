<?php

namespace SilverCart\Model\Order;

use SilverCart\Admin\Forms\TableField;
use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\FormFields\FieldGroup;
use SilverCart\Model\ShopEmail;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\NumberRange;
use SilverCart\Model\Order\OrderInvoiceAddress;
use SilverCart\Model\Order\OrderLog;
use SilverCart\Model\Order\OrderPosition;
use SilverCart\Model\Order\OrderShippingAddress;
use SilverCart\Model\Order\OrderStatus;
use SilverCart\Model\Order\ShoppingCartPosition;
use SilverCart\Model\Pages\AddressHolder;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Payment\HandlingCost;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Shipment\ShippingFee;
use SilverCart\Model\Shipment\ShippingMethod;
use SilverCart\ORM\DataObjectExtension;
use SilverCart\ORM\Filters\DateRangeSearchFilter;
use SilverCart\ORM\Filters\ExactMatchBooleanMultiFilter;
use SilverCart\ORM\Search\SearchContext;
use SilverCart\View\Printer\Printer;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBMoney;
use SilverStripe\ORM\Filters\ExactMatchFilter;
use SilverStripe\ORM\Filters\PartialMatchFilter;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Security\Security;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\ViewableData;

/**
 * abstract for an order.
 *
 * @package SilverCart
 * @subpackage Model_Order
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Order extends DataObject implements PermissionProvider {

    /**
     * attributes
     *
     * @var array
     */
    private static $db = array(
        'AmountTotal'                       => \SilverCart\ORM\FieldType\DBMoney::class, // value of all products
        'PriceType'                         => 'Varchar(24)',
        'HandlingCostPayment'               => \SilverCart\ORM\FieldType\DBMoney::class,
        'HandlingCostShipment'              => \SilverCart\ORM\FieldType\DBMoney::class,
        'TaxRatePayment'                    => 'Int',
        'TaxRateShipment'                   => 'Int',
        'TaxAmountPayment'                  => 'Float',
        'TaxAmountShipment'                 => 'Float',
        'Note'                              => 'Text',
        'WeightTotal'                       => 'Float', //unit is gramm
        'CustomersEmail'                    => 'Varchar(60)',
        'OrderNumber'                       => 'Varchar(128)',
        'HasAcceptedTermsAndConditions'     => 'Boolean(0)',
        'HasAcceptedRevocationInstruction'  => 'Boolean(0)',
        'IsSeen'                            => 'Boolean(0)',
        'TrackingCode'                      => 'Varchar(64)',
        'TrackingLink'                      => 'Text',
        'PaymentReferenceID'                => 'Text',
        'PaymentReferenceMessage'           => 'Text',
        'PaymentReferenceData'              => 'Text',
    );

    /**
     * 1:1 relations
     *
     * @var array
     */
    private static $has_one = array(
        'ShippingAddress' => OrderShippingAddress::class,
        'InvoiceAddress'  => OrderInvoiceAddress::class,
        'PaymentMethod'   => PaymentMethod::class,
        'ShippingMethod'  => ShippingMethod::class,
        'OrderStatus'     => OrderStatus::class,
        'Member'          => Member::class,
        'ShippingFee'     => ShippingFee::class,
    );

    /**
     * 1:n relations
     *
     * @var array
     */
    private static $has_many = array(
        'OrderPositions'  => OrderPosition::class,
        'OrderLogs'       => OrderLog::class,
    );

    /**
     * Casting.
     *
     * @var array
     */
    private static $casting = array(
        'Created'                       => 'Date',
        'CreatedNice'                   => 'Varchar',
        'ShippingAddressSummary'        => 'Text',
        'ShippingAddressSummaryHtml'    => 'HtmlText',
        'ShippingAddressTable'          => 'HtmlText',
        'InvoiceAddressSummary'         => 'Text',
        'InvoiceAddressSummaryHtml'     => 'HtmlText',
        'InvoiceAddressTable'           => 'HtmlText',
        'AmountTotalNice'               => 'Varchar',
        'PriceTypeText'                 => 'Varchar(24)',
    );
    
        /**
     * Default sort direction in tables.
     *
     * @var string
     */
    private static $default_sort = "Created DESC";

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartOrder';
    
    /**
     * register extensions
     *
     * @var array
     */
    private static $extensions = [
        Versioned::class . '.versioned',
    ];

    /**
     * Grant API access on this item.
     *
     * @var bool
     *
     * @since 2013-03-13
     */
    private static $api_access = true;

    /**
     * Prevents multiple handling of order status change.
     *
     * @var bool
     */
    protected $didHandleOrderStatusChange = false;
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.07.2012
     */
    public function singular_name() {
        return Tools::singular_name_for($this); 
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.07.2012
     */
    public function plural_name() {
        return Tools::plural_name_for($this); 
    }

    /**
     * Set permissions.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.07.2012
     */
    public function providePermissions() {
        return array(
            'SILVERCART_ORDER_VIEW'   => $this->fieldLabel('SILVERCART_ORDER_VIEW'),
            'SILVERCART_ORDER_EDIT'   => $this->fieldLabel('SILVERCART_ORDER_EDIT'),
            'SILVERCART_ORDER_DELETE' => $this->fieldLabel('SILVERCART_ORDER_DELETE'),
        );
    }

    /**
     * Indicates wether the current user can view this object.
     * 
     * @param Member $member declated to be compatible with parent
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.20118
     */
    public function canView($member = null) {
        $canView = false;
        if (is_null($member)) {
            $member = Security::getCurrentUser();
        }
        if (($member instanceof Member &&
             $member->ID == $this->MemberID &&
             !is_null($this->MemberID)) ||
            Permission::checkMember($member, 'SILVERCART_ORDER_VIEW')) {
            $canView = true;
        }
        return $canView;
    }
    
    /**
     * Order should not be created via backend
     * 
     * @param Member $member Member to check permission for
     *
     * @return false 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 10.02.2012
     */
    public function canCreate($member = null, $context = array()) {
        return false;
    }

    /**
     * Indicates wether the current user can edit this object.
     * 
     * @param Member $member declated to be compatible with parent
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.20118
     */
    public function canEdit($member = null) {
        return Permission::checkMember($member, 'SILVERCART_ORDER_EDIT');
    }

    /**
     * Indicates wether the current user can delete this object.
     * 
     * @param Member $member declated to be compatible with parent
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.20118
     */
    public function canDelete($member = null) {
        return Permission::checkMember($member, 'SILVERCART_ORDER_DELETE');
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.04.2013
     */
    public function summaryFields() {
        $summaryFields = array(
            'CreatedNice'                => $this->fieldLabel('Created'),
            'OrderNumber'                => $this->fieldLabel('OrderNumberShort'),
            'Member.CustomerNumber'      => $this->Member()->fieldLabel('CustomerNumberShort'),
            'ShippingAddressSummaryHtml' => $this->fieldLabel('ShippingAddress'),
            'InvoiceAddressSummaryHtml'  => $this->fieldLabel('InvoiceAddress'),
            'AmountTotalNice'            => $this->fieldLabel('AmountTotal'),
            'PaymentMethod.Title'        => $this->fieldLabel('PaymentMethod'),
            'OrderStatus.Title'          => $this->fieldLabel('OrderStatus'),
            'ShippingMethod.Title'       => $this->fieldLabel('ShippingMethod'),
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.20118
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'ID'                               => _t(Order::class . '.ORDER_ID', 'Ordernumber'),
                'Created'                          => Page::singleton()->fieldLabel('OrderDate'),
                'OrderNumber'                      => _t(Order::class . '.ORDERNUMBER', 'ordernumber'),
                'OrderNumberShort'                 => _t(Order::class . '.OrderNumberShort', 'Orderno.'),
                'ShippingFee'                      => _t(Order::class . '.SHIPPINGRATE', 'shipping costs'),
                'Note'                             => _t(Order::class . '.NOTE', 'Note'),
                'YourNote'                         => _t(Order::class . '.YOUR_REMARK', 'Your note'),
                'Member'                           => _t(Order::class . '.CUSTOMER', 'customer'),
                'Customer'                         => _t(Order::class . '.CUSTOMER', 'customer'),
                'CustomerData'                     => _t(Order::class . '.CUSTOMERDATA', 'Customer Data'),
                'MemberCustomerNumber'             => Member::singleton()->fieldLabel('CustomerNumber'),
                'MemberEmail'                      => Member::singleton()->fieldLabel('Email'),
                'Email'                            => Address::singleton()->fieldLabel('Email'),
                'ShippingAddress'                  => Address::singleton()->fieldLabel('ShippingAddress'),
                'ShippingAddressFirstName'         => Address::singleton()->fieldLabel('FirstName'),
                'ShippingAddressSurname'           => Address::singleton()->fieldLabel('Surname'),
                'ShippingAddressCountry'           => Country::singleton()->singular_name(),
                'InvoiceAddress'                   => Address::singleton()->fieldLabel('InvoiceAddress'),
                'OrderStatus'                      => _t(Order::class . '.STATUS', 'order status'),
                'AmountTotal'                      => _t(Order::class . '.AMOUNTTOTAL', 'Amount total'),
                'PriceType'                        => _t(Order::class . '.PRICETYPE', 'Price-Display-Type'),
                'HandlingCost'                     => _t(Order::class . '.HandlingCost', 'Handling cost'),
                'HandlingCostPayment'              => _t(Order::class . '.HANDLINGCOSTPAYMENT', 'Payment handling costs'),
                'HandlingCostShipment'             => _t(Order::class . '.HANDLINGCOSTSHIPMENT', 'Shipping handling costs'),
                'TaxRatePayment'                   => _t(Order::class . '.TAXRATEPAYMENT', 'Payment tax rate'),
                'TaxRateShipment'                  => _t(Order::class . '.TAXRATESHIPMENT', 'Shipping tax rate'),
                'TaxAmountPayment'                 => _t(Order::class . '.TAXAMOUNTPAYMENT', 'Pamyent tax amount'),
                'TaxAmountShipment'                => _t(Order::class . '.TAXAMOUNTSHIPMENT', 'Shipping tax amountt'),
                'WeightTotal'                      => _t(Order::class . '.WEIGHTTOTAL', 'Total weight'),
                'CustomersEmail'                   => _t(Order::class . '.CUSTOMERSEMAIL', 'Customers email address'),
                'PaymentMethod'                    => PaymentMethod::singleton()->singular_name(),
                'ShippingMethod'                   => ShippingMethod::singleton()->singular_name(),
                'HasAcceptedTermsAndConditions'    => _t(Order::class . '.HASACCEPTEDTERMSANDCONDITIONS', 'Has accepted terms and conditions'),
                'HasAcceptedRevocationInstruction' => _t(Order::class . '.HASACCEPTEDREVOCATIONINSTRUCTION', 'Has accepted revocation instruction'),
                'OrderPositions'                   => OrderPosition::singleton()->plural_name(),
                'OrderPositionsProductNumber'      => Product::singleton()->fieldLabel('ProductNumberShop'),
                'OrderPositionData'                => _t(Order::class . '.ORDERPOSITIONDATA', 'Position Data'),
                'OrderPositionQuantity'            => _t(Order::class . '.ORDERPOSITIONQUANTITY', 'Position Quantity'),
                'OrderPositionIsLimit'             => _t(Order::class . '.ORDERPOSITIONISLIMIT', 'Order may not have other positions'),
                'SearchResultsLimit'               => _t(Order::class . '.SEARCHRESULTSLIMIT', 'Limit'),
                'BasicData'                        => _t(Order::class . '.BASICDATA', 'Basics'),
                'MiscData'                         => _t(Order::class . '.MISCDATA', 'Others'),
                'ShippingAddressTab'               => _t(AddressHolder::class . '.SHIPPINGADDRESS_TAB', 'Shippingaddress'),
                'InvoiceAddressTab'                => _t(AddressHolder::class . '.INVOICEADDRESS_TAB', 'Invoiceaddress'),
                'Print'                            => _t(Order::class . '.PRINT', 'Print order'),
                'PrintPreview'                     => _t(Order::class . '.PRINT_PREVIEW', 'Print preview'),
                'EmptyString'                      => Tools::field_label('PleaseChoose'),
                'ChangeOrderStatus'                => _t(Order::class . '.BATCH_CHANGEORDERSTATUS', 'Change order status to...'),
                'IsSeen'                           => _t(Order::class . '.IS_SEEN', 'Seen'),
                'OrderLogs'                        => OrderLog::singleton()->plural_name(),
                'ValueOfGoods'                     => Page::singleton()->fieldLabel('ValueOfGoods'),
                'Tracking'                         => _t(Order::class . '.Tracking', 'Tracking'),
                'TrackingCode'                     => _t(Order::class . '.TrackingCode', 'Tracking Code'),
                'TrackingLink'                     => _t(Order::class . '.TrackingLink', 'Tracking Link'),
                'TrackingLinkLabel'                => _t(Order::class . '.TrackingLinkLabel', 'Reveal where my shipment currently is'),
                'PaymentReferenceID'               => _t(Order::class . '.PaymentReferenceID', 'Payment Provider Reference Number'),
                'PaymentReferenceMessage'          => _t(Order::class . '.PaymentReferenceMessage', 'Payment Provider Reference Message'),
                'PaymentReferenceData'             => _t(Order::class . '.PaymentReferenceData', 'Payment Provider Reference Data'),
                'DateFormat'                       => Tools::field_label('DateFormat'),
                'PaymentMethodTitle'               => _t(Order::class . '.PAYMENTMETHODTITLE', 'Payment method'),
                'OrderAmount'                      => _t(Order::class . '.ORDER_VALUE', 'Orderamount'),
                'SILVERCART_ORDER_VIEW'            => _t(Order::class . '.SILVERCART_ORDER_VIEW', 'View order'),
                'SILVERCART_ORDER_EDIT'            => _t(Order::class . '.SILVERCART_ORDER_EDIT', 'Edit order'),
                'SILVERCART_ORDER_DELETE'          => _t(Order::class . '.SILVERCART_ORDER_DELETE', 'Delete order'),
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.20118
     */
    public function searchableFields() {
        $address = Address::singleton();
        $searchableFields = array(
            'Created' => array(
                'title'     => $this->fieldLabel('Created'),
                'filter'    => DateRangeSearchFilter::class,
                'field'     => TextField::class,
            ),
            'OrderNumber' => array(
                'title'     => $this->fieldLabel('OrderNumber'),
                'filter'    => PartialMatchFilter::class,
            ),
            'IsSeen' => array(
                'title'     => $this->fieldLabel('IsSeen'),
                'filter'    => ExactMatchFilter::class,
            ),
            'OrderStatusID' => array(
                'title'     => $this->fieldLabel('OrderStatus'),
                'filter'    => ExactMatchBooleanMultiFilter::class,
                'field'     => \SilverCart\Admin\Forms\MultiDropdownField::class,
            ),
            'PaymentMethodID' => array(
                'title'     => $this->fieldLabel('PaymentMethod'),
                'filter'    => ExactMatchFilter::class,
            ),
            'ShippingMethodID' => array(
                'title'     => $this->fieldLabel('ShippingMethod'),
                'filter'    => ExactMatchFilter::class,
            ),
            'Member.CustomerNumber' => array(
                'title'     => $this->fieldLabel('MemberCustomerNumber'),
                'filter'    => PartialMatchFilter::class,
            ),
            'Member.Email' => array(
                'title'     => $this->fieldLabel('MemberEmail'),
                'filter'    => PartialMatchFilter::class,
            ),
            'ShippingAddress.FirstName' => array(
                'title'     => $this->fieldLabel('ShippingAddressFirstName'),
                'filter'    => PartialMatchFilter::class,
            ),
            'ShippingAddress.Surname' => array(
                'title'     => $this->fieldLabel('ShippingAddressSurname'),
                'filter'    => PartialMatchFilter::class,
            ),
            'ShippingAddress.Street' => array(
                'title'     => $address->fieldLabel('Street'),
                'filter'    => PartialMatchFilter::class,
            ),
            'ShippingAddress.StreetNumber' => array(
                'title'     => $address->fieldLabel('StreetNumber'),
                'filter'    => PartialMatchFilter::class,
            ),
            'ShippingAddress.Postcode' => array(
                'title'     => $address->fieldLabel('Postcode'),
                'filter'    => PartialMatchFilter::class,
            ),
            'ShippingAddress.City' => array(
                'title'     => $address->fieldLabel('City'),
                'filter'    => PartialMatchFilter::class,
            ),
            'ShippingAddress.CountryID' => array(
                'title'     => $this->fieldLabel('ShippingAddressCountry'),
                'filter'    => ExactMatchFilter::class,
            ),
            'OrderPositions.ProductNumber' => array(
                'title'     => $this->fieldLabel('OrderPositionsProductNumber'),
                'filter'    => PartialMatchFilter::class,
            ),
        );
        $this->extend('updateSearchableFields', $searchableFields);

        return $searchableFields;
    }
    
    /**
     * Returns the Title.
     * 
     * @return string
     */
    public function getTitle() {
        $title = $this->fieldLabel('OrderNumber') . ': ' . $this->OrderNumber . ' | ' . $this->fieldLabel('Created') . ': ' . date($this->fieldLabel('DateFormat'), strtotime($this->Created)) . ' | ' . $this->fieldLabel('AmountTotal') . ': ' . $this->AmountTotal->Nice();
        $this->extend('updateTitle', $title);
        return $title;
    }

    /**
     * Set the default search context for this field
     * 
     * @return SearchContext
     */
    public function getDefaultSearchContext() {
        return new SearchContext(
            static::class,
            $this->scaffoldSearchFields(),
            $this->defaultSearchFilters()
        );
    }
    
    /**
     * Determine which properties on the DataObject are
     * searchable, and map them to their default {@link FormField}
     * representations. Used for scaffolding a searchform for {@link ModelAdmin}.
     *
     * Some additional logic is included for switching field labels, based on
     * how generic or specific the field type is.
     *
     * Used by {@link SearchContext}.
     *
     * @param array $_params
     *   'fieldClasses': Associative array of field names as keys and FormField classes as values
     *   'restrictFields': Numeric array of a field name whitelist
     * @return FieldList
     */
    public function scaffoldSearchFields($_params = null) {
        $fields = parent::scaffoldSearchFields($_params);
        
        $orderStatusField = $fields->dataFieldByName('OrderStatusID');
        /* @var $orderStatusField \SilverCart\Admin\Forms\MultiDropdownField */
        $orderStatusField->setSource(OrderStatus::get()->map()->toArray());
        $orderStatusField->setEmptyString(Tools::field_label('PleaseChoose'));
        
        $order                 = Order::singleton();
        $basicLabelField       = new HeaderField(  'BasicLabelField',       $order->fieldLabel('BasicData'));
        $customerLabelField    = new HeaderField(  'CustomerLabelField',    $order->fieldLabel('CustomerData'));
        $positionLabelField    = new HeaderField(  'PositionLabelField',    $order->fieldLabel('OrderPositionData'));
        $miscLabelField        = new HeaderField(  'MiscLabelField',        $order->fieldLabel('MiscData'));
        $positionQuantityField = new TextField(    'OrderPositionQuantity', $order->fieldLabel('OrderPositionQuantity'));
        $positionIsLimitField  = new CheckboxField('OrderPositionIsLimit',  $order->fieldLabel('OrderPositionIsLimit'));
        $limitField            = new TextField(    'SearchResultsLimit',    $order->fieldLabel('SearchResultsLimit'));
        
        $fields->insertBefore($basicLabelField,                   'OrderNumber');
        $fields->insertAfter($fields->dataFieldByName('Created'), 'OrderNumber');
        $fields->insertBefore($customerLabelField,                'Member__CustomerNumber');
        $fields->insertBefore($positionLabelField,                'OrderPositions__ProductNumber');
        $fields->insertAfter($positionQuantityField,              'OrderPositions__ProductNumber');
        $fields->insertAfter($positionIsLimitField,               'OrderPositionQuantity');
        $fields->insertAfter($miscLabelField,                     'OrderPositionIsLimit');
        $fields->insertAfter($limitField,                         'MiscLabelField');
        
        $fields->dataFieldByName('PaymentMethodID')->setEmptyString(           Tools::field_label('PleaseChoose'));
        $fields->dataFieldByName('ShippingMethodID')->setEmptyString(          Tools::field_label('PleaseChoose'));
        //fields->dataFieldByName('ShippingAddress__CountryID')->setEmptyString(Tools::field_label('PleaseChoose'));
        
        return $fields;
    }
    
    /**
     * Returns the orders tracking code.
     * Tracking code is extendable by decorator.
     * 
     * @return string
     */
    public function getTrackingCode() {
        $trackingCode = $this->getField('TrackingCode');
        $this->extend('updateTrackingCode', $trackingCode);
        return $trackingCode;
    }
    
    /**
     * Returns the orders tracking link.
     * Tracking link is extendable by decorator.
     * 
     * @return string
     */
    public function getTrackingLink() {
        $trackingLink = $this->getField('TrackingLink');
        $this->extend('updateTrackingLink', $trackingLink);
        return $trackingLink;
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
     * @param bool $disableUpdate Disable update by decorator?
     *
     * @return string
     */
    public function getShippingAddressSummary($disableUpdate = false) {
        $shippingAddressSummary = '';
        if (!empty($this->ShippingAddress()->Company)) {
            $shippingAddressSummary .= $this->ShippingAddress()->Company . PHP_EOL;
        }
        $shippingAddressSummary .= $this->ShippingAddress()->FullName . PHP_EOL;
        if ($this->ShippingAddress()->IsPackstation) {
            $shippingAddressSummary .= $this->ShippingAddress()->PostNumber . PHP_EOL;
            $shippingAddressSummary .= $this->ShippingAddress()->Packstation . PHP_EOL;
        } else {
            $shippingAddressSummary .= $this->ShippingAddress()->Addition == '' ? '' : $this->ShippingAddress()->Addition . PHP_EOL;
            $shippingAddressSummary .= $this->ShippingAddress()->Street . ' ' . $this->ShippingAddress()->StreetNumber . PHP_EOL;
        }
        $shippingAddressSummary .= strtoupper($this->ShippingAddress()->Country()->ISO2) . '-' . $this->ShippingAddress()->Postcode . ' ' . $this->ShippingAddress()->City . PHP_EOL;
        if (!empty($this->ShippingAddress()->TaxIdNumber)) {
            $shippingAddressSummary .= $this->ShippingAddress()->TaxIdNumber . PHP_EOL;
        }
        if (!$disableUpdate) {
            $this->extend('updateShippingAddressSummary', $shippingAddressSummary);
        }
        return $shippingAddressSummary;
    }

    /**
     * return the orders shipping address as complete HTML string.
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getShippingAddressSummaryHtml() {
        return Tools::string2html(str_replace(PHP_EOL, '<br/>', $this->ShippingAddressSummary));
    }

    /**
     * Returns the shipping address rendered with a HTML table
     * 
     * @return type
     */
    public function getShippingAddressTable() {
        return $this->ShippingAddress()->renderWith('SilverCart/Email/Includes/AddressData');
    }
    
    /**
     * Returns whether the invoice address equals the shipping address.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.02.2014
     */
    public function InvoiceAddressEqualsShippingAddress() {
        $isEqual = $this->InvoiceAddress()->isEqual($this->ShippingAddress());
        return $isEqual;
    }

    /**
     * return the orders invoice address as complete string.
     * 
     * @param bool $disableUpdate Disable update by decorator?
     *
     * @return string
     */
    public function getInvoiceAddressSummary($disableUpdate = false) {
        $invoiceAddressSummary = '';
        if (!empty($this->InvoiceAddress()->Company)) {
            $invoiceAddressSummary .= $this->InvoiceAddress()->Company . PHP_EOL;
        }
        $invoiceAddressSummary .= $this->InvoiceAddress()->FullName . PHP_EOL;
        if ($this->InvoiceAddress()->IsPackstation) {
            $invoiceAddressSummary .= $this->InvoiceAddress()->PostNumber . PHP_EOL;
            $invoiceAddressSummary .= $this->InvoiceAddress()->Packstation . PHP_EOL;
        } else {
            $invoiceAddressSummary .= $this->InvoiceAddress()->Addition == '' ? '' : $this->InvoiceAddress()->Addition . PHP_EOL;
            $invoiceAddressSummary .= $this->InvoiceAddress()->Street . ' ' . $this->InvoiceAddress()->StreetNumber . PHP_EOL;
        }
        $invoiceAddressSummary .= strtoupper($this->InvoiceAddress()->Country()->ISO2) . '-' . $this->InvoiceAddress()->Postcode . ' ' . $this->InvoiceAddress()->City . PHP_EOL;
        if (!empty($this->InvoiceAddress()->TaxIdNumber)) {
            $invoiceAddressSummary .= $this->InvoiceAddress()->TaxIdNumber . PHP_EOL;
        }
        if (!$disableUpdate) {
            $this->extend('updateInvoiceAddressSummary', $invoiceAddressSummary);
        }
        return $invoiceAddressSummary;
    }

    /**
     * return the orders invoice address as complete HTML string.
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getInvoiceAddressSummaryHtml() {
        return Tools::string2html(str_replace(PHP_EOL, '<br/>', $this->InvoiceAddressSummary));
    }
    
    /**
     * Returns the invoice address rendered with a HTML table
     * 
     * @return type
     */
    public function getInvoiceAddressTable() {
        return $this->InvoiceAddress()->renderWith('SilverCart/Email/Includes/AddressData');
    }

    /**
     * Returns a limited number of order positions.
     * 
     * @param int $numberOfPositions The number of positions to get.
     *
     * @return DataList
     */
    public function getLimitedOrderPositions($numberOfPositions = 2) {
        return $this->OrderPositions()->limit($numberOfPositions);
    }

    /**
     * Returns a limited number of order positions.
     * 
     * @param int $numberOfPositions The number of positions to check for.
     *
     * @return ArrayList
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.01.2012
     */
    public function hasMoreOrderPositionsThan($numberOfPositions = 2) {
        $hasMorePositions = false;

        if ($this->OrderPositions()->count() > $numberOfPositions) {
            $hasMorePositions = true;
        }

        return $hasMorePositions;
    }
    
    /**
     * Returns an array of field/relation names (db, has_one, has_many, 
     * many_many, belongs_many_many) to exclude from form scaffolding in
     * backend.
     * This is a performance friendly way to exclude fields.
     * 
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 05.03.2013
     */
    public function excludeFromScaffolding() {
        $excludeFromScaffolding = array(
            'Version',
            'IsSeen',
            'ShippingAddress',
            'InvoiceAddress',
            'ShippingFee',
            'Member'
        );
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }

    /**
     * customize backend fields
     *
     * @return FieldList the form fields for the backend
     */
    public function getCMSFields() {
        $this->markAsSeen();
        $fields = DataObjectExtension::getCMSFields($this);
        
        //add the shipping/invloice address fields as own tab
        $address = Address::singleton();
        $fields->findOrMakeTab('Root.ShippingAddressTab', $this->fieldLabel('ShippingAddressTab'));
        $fields->findOrMakeTab('Root.InvoiceAddressTab',  $this->fieldLabel('InvoiceAddressTab'));
        
        $fields->addFieldToTab('Root.ShippingAddressTab', new LiteralField('sa__Preview',           '<p>' . Convert::raw2xml($this->getShippingAddressSummary(true)) . '</p>'));
        $fields->addFieldToTab('Root.ShippingAddressTab', new TextField('sa__TaxIdNumber',          $address->fieldLabel('TaxIdNumber'),        $this->ShippingAddress()->TaxIdNumber));
        $fields->addFieldToTab('Root.ShippingAddressTab', new TextField('sa__Company',              $address->fieldLabel('Company'),            $this->ShippingAddress()->Company));
        $fields->addFieldToTab('Root.ShippingAddressTab', new TextField('sa__FirstName',            $address->fieldLabel('FirstName'),          $this->ShippingAddress()->FirstName));
        $fields->addFieldToTab('Root.ShippingAddressTab', new TextField('sa__Surname',              $address->fieldLabel('Surname'),            $this->ShippingAddress()->Surname));
        $fields->addFieldToTab('Root.ShippingAddressTab', new TextField('sa__Addition',             $address->fieldLabel('Addition'),           $this->ShippingAddress()->Addition));
        $fields->addFieldToTab('Root.ShippingAddressTab', new TextField('sa__Street',               $address->fieldLabel('Street'),             $this->ShippingAddress()->Street));
        $fields->addFieldToTab('Root.ShippingAddressTab', new TextField('sa__StreetNumber',         $address->fieldLabel('StreetNumber'),       $this->ShippingAddress()->StreetNumber));
        $fields->addFieldToTab('Root.ShippingAddressTab', new CheckboxField('sa__IsPackstation',    $address->fieldLabel('IsPackstation'),      $this->ShippingAddress()->IsPackstation));
        $fields->addFieldToTab('Root.ShippingAddressTab', new TextField('sa__PostNumber',           $address->fieldLabel('PostNumber'),         $this->ShippingAddress()->PostNumber));
        $fields->addFieldToTab('Root.ShippingAddressTab', new TextField('sa__Packstation',          $address->fieldLabel('PackstationPlain'),   $this->ShippingAddress()->Packstation));
        $fields->addFieldToTab('Root.ShippingAddressTab', new TextField('sa__Postcode',             $address->fieldLabel('Postcode'),           $this->ShippingAddress()->Postcode));
        $fields->addFieldToTab('Root.ShippingAddressTab', new TextField('sa__City',                 $address->fieldLabel('City'),               $this->ShippingAddress()->City));
        $fields->addFieldToTab('Root.ShippingAddressTab', new DropdownField('sa__Country',          $address->fieldLabel('Country'),            Country::get_active()->map()->toArray(), $this->ShippingAddress()->Country()->ID));
        $fields->addFieldToTab('Root.ShippingAddressTab', new TextField('sa__PhoneAreaCode',        $address->fieldLabel('PhoneAreaCode'),      $this->ShippingAddress()->PhoneAreaCode));
        $fields->addFieldToTab('Root.ShippingAddressTab', new TextField('sa__Phone',                $address->fieldLabel('Phone'),              $this->ShippingAddress()->Phone));
            
        $fields->addFieldToTab('Root.InvoiceAddressTab', new LiteralField('ia__Preview',            '<p>' . Convert::raw2xml($this->getInvoiceAddressSummary(true)) . '</p>'));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new TextField('ia__TaxIdNumber',           $address->fieldLabel('TaxIdNumber'),        $this->InvoiceAddress()->TaxIdNumber));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new TextField('ia__Company',               $address->fieldLabel('Company'),            $this->InvoiceAddress()->Company));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new TextField('ia__FirstName',             $address->fieldLabel('FirstName'),          $this->InvoiceAddress()->FirstName));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new TextField('ia__Surname',               $address->fieldLabel('Surname'),            $this->InvoiceAddress()->Surname));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new TextField('ia__Addition',              $address->fieldLabel('Addition'),           $this->InvoiceAddress()->Addition));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new TextField('ia__Street',                $address->fieldLabel('Street'),             $this->InvoiceAddress()->Street));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new TextField('ia__StreetNumber',          $address->fieldLabel('StreetNumber'),       $this->InvoiceAddress()->StreetNumber));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new CheckboxField('ia__IsPackstation',     $address->fieldLabel('IsPackstation'),      $this->InvoiceAddress()->IsPackstation));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new TextField('ia__PostNumber',            $address->fieldLabel('PostNumber'),         $this->InvoiceAddress()->PostNumber));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new TextField('ia__Packstation',           $address->fieldLabel('PackstationPlain'),   $this->InvoiceAddress()->Packstation));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new TextField('ia__Postcode',              $address->fieldLabel('Postcode'),           $this->InvoiceAddress()->Postcode));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new TextField('ia__City',                  $address->fieldLabel('City'),               $this->InvoiceAddress()->City));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new DropdownField('ia__Country',           $address->fieldLabel('Country'),            Country::get_active()->map()->toArray(), $this->InvoiceAddress()->Country()->ID));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new TextField('ia__PhoneAreaCode',         $address->fieldLabel('PhoneAreaCode'),      $this->InvoiceAddress()->PhoneAreaCode));
        $fields->addFieldToTab('Root.InvoiceAddressTab', new TextField('ia__Phone',                 $address->fieldLabel('Phone'),              $this->InvoiceAddress()->Phone));
        
        //add print preview
        $fields->findOrMakeTab('Root.PrintPreviewTab',    $this->fieldLabel('PrintPreview'));
        $printPreviewField = new LiteralField(
                'PrintPreviewField',
                sprintf(
                    '<iframe width="100%%" height="100%%" border="0" src="%s" class="print-preview"></iframe>',
                    Printer::getPrintInlineURL($this)
                )
        );
        $fields->addFieldToTab('Root.PrintPreviewTab', $printPreviewField);
        
        if (!empty($this->PaymentReferenceID)) {
            $paymentReferenceIDField = new TextField('PaymentReferenceID_Readonly', $this->fieldLabel('PaymentReferenceID'), $this->PaymentReferenceID);
            $paymentReferenceIDField->setReadonly(true);
            $fields->insertAfter($paymentReferenceIDField, 'PaymentReferenceID');
        }
        if (empty($this->PaymentReferenceID)) {
            $fields->removeByName('PaymentReferenceMessage');
        } else {
            $fields->dataFieldByName('PaymentReferenceMessage')->setReadonly(true);
        }
        $fields->removeByName('PaymentReferenceID');
        $fields->removeByName('PaymentReferenceData');
        
        return $fields;
    }
    
    /**
     * Returns the quick access fields to display in GridField
     * 
     * @return FieldList
     */
    public function getQuickAccessFields() {
        $quickAccessFields = new FieldList();
        
        $threeColField = '<div class="multi-col-field"><strong>%s</strong><span>%s</span><span>%s</span></div>';
        $twoColField   = '<div class="multi-col-field"><strong>%s</strong><span></span><span>%s</span></div>';
        
        $orderNumberField   = new TextField('OrderNumber__' . $this->ID, $this->fieldLabel('OrderNumber'), $this->OrderNumber);
        $orderStatusField   = new TextField('OrderStatus__' . $this->ID, $this->fieldLabel('OrderStatus'), $this->OrderStatus()->Title);
        $orderPositionTable = new TableField(
                'OrderPositions__' . $this->ID,
                $this->fieldLabel('OrderPositions'),
                $this->OrderPositions()
        );
        $shippingField    = new LiteralField('ShippingMethod__' . $this->ID, sprintf($threeColField, $this->fieldLabel('ShippingMethod'), $this->ShippingMethod()->TitleWithCarrier, $this->HandlingCostShipmentNice));
        $paymentField     = new LiteralField('PaymentMethod__' . $this->ID,  sprintf($threeColField, $this->fieldLabel('PaymentMethod'),  $this->PaymentMethod()->Title,             $this->HandlingCostPaymentNice));
        $amountTotalField = new LiteralField('AmountTotal__' . $this->ID,    sprintf($twoColField,   $this->fieldLabel('AmountTotal'),    $this->AmountTotalNice));
        
        $orderNumberField->setReadonly(true);
        $orderStatusField->setReadonly(true);
        
        $mainGroup = new FieldGroup('MainGroup');
        $mainGroup->push($orderNumberField);
        $mainGroup->push($orderStatusField);
        
        $quickAccessFields->push($mainGroup);
        $quickAccessFields->push($orderPositionTable);
        $quickAccessFields->push($shippingField);
        $quickAccessFields->push($paymentField);
        $quickAccessFields->push($amountTotalField);
        
        $this->extend('updateQuickAccessFields', $quickAccessFields);
        
        return $quickAccessFields;
    }
    
    /**
     * Creates an invoice address for an order from customers data.
     *
     * @param array $addressData Address data from checkout
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.04.2018
     */
    public function createInvoiceAddress($addressData = array()) {
        $this->extend('onBeforeCreateInvoiceAddress', $addressData, $this);
        $orderInvoiceAddress    = $this->createAddress($addressData, new OrderInvoiceAddress());
        $this->InvoiceAddressID = $orderInvoiceAddress->ID;
        $this->write();
        $this->extend('onAfterCreateInvoiceAddress', $orderInvoiceAddress, $this);
    }

    /**
     * Creates a shipping address for an order from customers data.
     *
     * @param array $addressData Address data from checkout
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.04.2018
     */
    public function createShippingAddress($addressData = array()) {
        $this->extend('onBeforeCreateShippingAddress', $addressData, $this);
        $orderShippingAddress    = $this->createAddress($addressData, new OrderShippingAddress());
        $this->ShippingAddressID = $orderShippingAddress->ID;
        $this->write();
        $this->extend('onAfterCreateShippingAddress', $orderShippingAddress, $this);
    }

    /**
     * Creates an address for an order from customers data.
     *
     * @param array $addressData Address data from checkout
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.04.2018
     */
    public function createAddress($addressData, $address) {
        if (empty($addressData)) {
            $member      = Customer::currentUser();
            $addressData = $member->ShippingAddress()->toMap();
        }
        $forbiddenFields = ['ID', 'ClassName', 'RecordClassName', 'LastEdited', 'Created', 'MemberID'];
        $this->extend('updateCreateAddressForbiddenFields', $forbiddenFields);
        foreach ($forbiddenFields as $field) {
            if (array_key_exists($field, $addressData)) {
                unset($addressData[$field]);
            }
        }
        $this->extend('updateCreateAddress', $addressData);
        $address->castedUpdate($addressData);
        $address->write();
        return $address;
    }

    /**
     * creates an order from the cart
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.20118
     */
    public function createFromShoppingCart() {
        $member = Customer::currentUser();
        if ($member instanceof Member) {
            $shoppingCart = $member->getCart();
            $shoppingCart->setPaymentMethodID($this->PaymentMethodID);
            $shoppingCart->setShippingMethodID($this->ShippingMethodID);
            $this->MemberID = $member->ID;

            $overwriteCreateFromShoppingCart = false;
            $this->extend('overwriteCreateFromShoppingCart', $overwriteCreateFromShoppingCart, $shoppingCart);
            if ($overwriteCreateFromShoppingCart) {
                return true;
            }
            
            $this->extend('onBeforeCreateFromShoppingCart', $shoppingCart);

            // VAT tax for shipping and payment fees
            $shippingMethod = ShippingMethod::get()->byID($this->ShippingMethodID);
            if ($shippingMethod instanceof ShippingMethod &&
                $shippingMethod->exists()) {
                $shippingFee = $shippingMethod->getShippingFee();
                if ($shippingFee instanceof ShippingFee &&
                    $shippingFee->exists() &&
                    $shippingFee->Tax()->exists()) {
                    $this->TaxRateShipment   = $shippingFee->getTaxRate();
                    $this->TaxAmountShipment = $shippingFee->getTaxAmount();
                }
            }

            $paymentMethod = PaymentMethod::get()->byID($this->PaymentMethodID);
            if ($paymentMethod instanceof PaymentMethod &&
                $paymentMethod->exists()) {
                $paymentFee = $paymentMethod->getHandlingCost();

                if ($paymentFee instanceof HandlingCost &&
                    $paymentFee->exists()) {
                    if ($paymentFee->Tax()->exists()) {
                        $this->TaxRatePayment   = $paymentFee->Tax()->getTaxRate();
                        $this->TaxAmountPayment = $paymentFee->getTaxAmount();
                    }
                    $this->HandlingCostPayment->setAmount($paymentFee->amount->getAmount());
                    $this->HandlingCostPayment->setCurrency($paymentFee->amount->getCurrency());
                }
            }

            // amount of all positions + handling fee of the payment method + shipping fee
            $totalAmount = $member->getCart()->getAmountTotal()->getAmount();

            $this->AmountTotal->setAmount(
                $totalAmount
            );
            $this->AmountTotal->setCurrency(Config::DefaultCurrency());

            $this->PriceType = $member->getPriceType();

            // adjust orders standard status
            $orderStatus = OrderStatus::get()->filter('Code', $paymentMethod->getDefaultOrderStatus())->first();
            if ($orderStatus) {
                $this->OrderStatusID = $orderStatus->ID;
            }

            // write order to have an id
            $this->write();
            
            $this->extend('onAfterCreateFromShoppingCart', $shoppingCart);
        }
    }

    /**
     * convert cart positions in order positions
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.11.2014
     */
    public function convertShoppingCartPositionsToOrderPositions() {
        if ($this->extend('updateConvertShoppingCartPositionsToOrderPositions')) {
            return true;
        }
        
        $member = Customer::currentUser();
        if ($member instanceof Member) {
            $shoppingCart = $member->getCart();
            $shoppingCart->setPaymentMethodID($this->PaymentMethodID);
            $shoppingCart->setShippingMethodID($this->ShippingMethodID);
            $shoppingCartPositions = ShoppingCartPosition::get()->filter('ShoppingCartID', $member->ShoppingCartID);

            if ($shoppingCartPositions->exists()) {
                foreach ($shoppingCartPositions as $shoppingCartPosition) {
                    $product = $shoppingCartPosition->Product();

                    if ($product) {
                        $orderPosition = new OrderPosition();
                        $orderPosition->objectCreated = true;
                        $orderPosition->Price->setAmount($shoppingCartPosition->getPrice(true)->getAmount());
                        $orderPosition->Price->setCurrency($shoppingCartPosition->getPrice(true)->getCurrency());
                        $orderPosition->PriceTotal->setAmount($shoppingCartPosition->getPrice()->getAmount());
                        $orderPosition->PriceTotal->setCurrency($shoppingCartPosition->getPrice()->getCurrency());
                        $orderPosition->Tax                     = $shoppingCartPosition->getTaxAmount(true);
                        $orderPosition->TaxTotal                = $shoppingCartPosition->getTaxAmount();
                        $orderPosition->TaxRate                 = $product->getTaxRate();
                        $orderPosition->ProductDescription      = $product->LongDescription;
                        $orderPosition->Quantity                = $shoppingCartPosition->Quantity;
                        $orderPosition->numberOfDecimalPlaces   = $product->QuantityUnit()->numberOfDecimalPlaces;
                        $orderPosition->ProductNumber           = $shoppingCartPosition->getProductNumberShop();
                        $orderPosition->Title                   = $product->Title;
                        $orderPosition->OrderID                 = $this->ID;
                        $orderPosition->IsNonTaxable            = $member->doesNotHaveToPayTaxes();
                        $orderPosition->ProductID               = $product->ID;
                        $orderPosition->log                     = false;
                        $this->extend('onBeforeConvertSingleShoppingCartPositionToOrderPosition', $shoppingCartPosition, $orderPosition);
                        $orderPosition->write();

                        // Call hook method on product if available
                        if ($product->hasMethod('ShoppingCartConvert')) {
                            $product->ShoppingCartConvert($this, $orderPosition);
                        }
                        // decrement stock quantity of the product
                        if (Config::EnableStockManagement()) {
                            $product->decrementStockQuantity($shoppingCartPosition->Quantity);
                        }

                        $this->extend('onAfterConvertSingleShoppingCartPositionToOrderPosition', $shoppingCartPosition, $orderPosition);

                        $orderPosition->write();
                        unset($orderPosition);
                    }
                }

                // Get taxable positions from registered modules
                $registeredModules = $member->getCart()->callMethodOnRegisteredModules(
                    'ShoppingCartPositions',
                    array(
                        $member->getCart(),
                        $member,
                        true
                    )
                );

                foreach ($registeredModules as $moduleName => $moduleOutput) {
                    foreach ($moduleOutput as $modulePosition) {
                        $orderPosition = new OrderPosition();
                        if ($this->IsPriceTypeGross()) {
                            if ($modulePosition->Price instanceof DBMoney) {
                                $price = $modulePosition->Price->getAmount();
                            } else {
                                $price = $modulePosition->Price;
                            }
                            $orderPosition->Price->setAmount($price);
                        } else {
                            if ($modulePosition->Price instanceof DBMoney) {
                                $price = $modulePosition->PriceNet->getAmount();
                            } else {
                                $price = $modulePosition->PriceNet;
                            }
                            $orderPosition->Price->setAmount($price);
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
                        if ($modulePosition->isChargeOrDiscount) {
                            $orderPosition->isChargeOrDiscount                  = true;
                            $orderPosition->chargeOrDiscountModificationImpact  = $modulePosition->chargeOrDiscountModificationImpact;
                        }
                        $orderPosition->OrderID = $this->ID;
                        $orderPosition->write();
                        unset($orderPosition);
                    }
                }

                // Get charges and discounts for product values
                if ($shoppingCart->HasChargesAndDiscountsForProducts()) {
                    $chargesAndDiscountsForProducts = $shoppingCart->ChargesAndDiscountsForProducts();

                    foreach ($chargesAndDiscountsForProducts as $chargeAndDiscountForProduct) {
                        $orderPosition = new OrderPosition();
                        $orderPosition->Price->setAmount($chargeAndDiscountForProduct->Price->getAmount());
                        $orderPosition->Price->setCurrency($chargeAndDiscountForProduct->Price->getCurrency());
                        $orderPosition->PriceTotal->setAmount($chargeAndDiscountForProduct->Price->getAmount());
                        $orderPosition->PriceTotal->setCurrency($chargeAndDiscountForProduct->Price->getCurrency());
                        $orderPosition->isChargeOrDiscount = true;
                        $orderPosition->chargeOrDiscountModificationImpact = $chargeAndDiscountForProduct->sumModificationImpact;
                        $orderPosition->Tax                 = $chargeAndDiscountForProduct->Tax->Title;

                        if ($this->IsPriceTypeGross()) {
                            $orderPosition->TaxTotal = $chargeAndDiscountForProduct->Price->getAmount() - ($chargeAndDiscountForProduct->Price->getAmount() / (100 + $chargeAndDiscountForProduct->Tax->Rate) * 100);
                        } else {
                            $orderPosition->TaxTotal = ($chargeAndDiscountForProduct->Price->getAmount() / 100 * (100 + $chargeAndDiscountForProduct->Tax->Rate)) - $chargeAndDiscountForProduct->Price->getAmount();
                        }

                        $orderPosition->TaxRate             = $chargeAndDiscountForProduct->Tax->Rate;
                        $orderPosition->ProductDescription  = $chargeAndDiscountForProduct->Name;
                        $orderPosition->Quantity            = 1;
                        $orderPosition->ProductNumber       = $chargeAndDiscountForProduct->sumModificationProductNumber;
                        $orderPosition->Title               = $chargeAndDiscountForProduct->Name;
                        $orderPosition->OrderID             = $this->ID;
                        $orderPosition->write();
                        unset($orderPosition);
                    }
                }

                // Get nontaxable positions from registered modules
                $registeredModulesNonTaxablePositions = $member->getCart()->callMethodOnRegisteredModules(
                    'ShoppingCartPositions',
                    array(
                        $member->getCart(),
                        $member,
                        false
                    )
                );

                foreach ($registeredModulesNonTaxablePositions as $moduleName => $moduleOutput) {
                    foreach ($moduleOutput as $modulePosition) {
                        $orderPosition = new OrderPosition();
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
                        $orderPosition->OrderID             = $this->ID;
                        $orderPosition->write();
                        unset($orderPosition);
                    }
                }

                // Get charges and discounts for shopping cart total
                if ($shoppingCart->HasChargesAndDiscountsForTotal()) {
                    $chargesAndDiscountsForTotal = $shoppingCart->ChargesAndDiscountsForTotal();

                    foreach ($chargesAndDiscountsForTotal as $chargeAndDiscountForTotal) {
                        $orderPosition = new OrderPosition();
                        $orderPosition->Price->setAmount($chargeAndDiscountForTotal->Price->getAmount());
                        $orderPosition->Price->setCurrency($chargeAndDiscountForTotal->Price->getCurrency());
                        $orderPosition->PriceTotal->setAmount($chargeAndDiscountForTotal->Price->getAmount());
                        $orderPosition->PriceTotal->setCurrency($chargeAndDiscountForTotal->Price->getCurrency());
                        $orderPosition->isChargeOrDiscount = true;
                        $orderPosition->chargeOrDiscountModificationImpact = $chargeAndDiscountForTotal->sumModificationImpact;
                        $orderPosition->Tax                 = $chargeAndDiscountForTotal->Tax->Title;
                        if ($this->IsPriceTypeGross()) {
                            $orderPosition->TaxTotal = $chargeAndDiscountForTotal->Price->getAmount() - ($chargeAndDiscountForTotal->Price->getAmount() / (100 + $chargeAndDiscountForTotal->Tax->Rate) * 100);
                        } else {
                            $orderPosition->TaxTotal = ($chargeAndDiscountForTotal->Price->getAmount() / 100 * (100 + $chargeAndDiscountForTotal->Tax->Rate)) - $chargeAndDiscountForTotal->Price->getAmount();
                        }
                        $orderPosition->TaxRate             = $chargeAndDiscountForTotal->Tax->Rate;
                        $orderPosition->ProductDescription  = $chargeAndDiscountForTotal->Name;
                        $orderPosition->Quantity            = 1;
                        $orderPosition->ProductNumber       = $chargeAndDiscountForTotal->sumModificationProductNumber;
                        $orderPosition->Title               = $chargeAndDiscountForTotal->Name;
                        $orderPosition->OrderID             = $this->ID;
                        $orderPosition->write();
                        unset($orderPosition);
                    }
                }

                // Convert positions of registered modules
                $member->getCart()->callMethodOnRegisteredModules(
                    'ShoppingCartConvert',
                    array(
                        Customer::currentUser()->getCart(),
                        Customer::currentUser(),
                        $this
                    )
                );
                
                $this->extend('onAfterConvertShoppingCartPositionsToOrderPositions', $shoppingCart);

                // Delete the shoppingcart positions
                foreach ($shoppingCartPositions as $shoppingCartPosition) {
                    $shoppingCartPosition->delete();
                }
            
                $this->write();
            }
        }
    }

    /**
     * set payment method for $this
     *
     * @param int $paymentMethodID id of payment method
     *
     * @return void
     */
    public function setPaymentMethod($paymentMethodID) {
        $paymentMethod = PaymentMethod::get()->byID($paymentMethodID);

        if ($paymentMethod) {
            $this->PaymentMethodID = $paymentMethod->ID;
            $this->HandlingCostPayment->setAmount($paymentMethod->getHandlingCost()->amount->getAmount());
            $this->HandlingCostPayment->setCurrency(Config::DefaultCurrency());
        }
    }

    /**
     * set status of $this
     *
     * @param OrderStatus $orderStatus the order status object
     *
     * @return bool
     */
    public function setOrderStatus($orderStatus) {
        $orderStatusSet = false;

        if ($orderStatus && $orderStatus->exists()) {
            $this->OrderStatusID = $orderStatus->ID;
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
     */
    public function setOrderStatusByID($orderStatusID) {
        $orderStatusSet = false;

        if (OrderStatus::get()->byID($orderStatusID)->exists()) {
            $this->OrderStatusID = $orderStatusID;
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
     * @return void
     */
    public function setNote($note) {
        $this->setField('Note', $note);
    }

    /**
     * Returns the formatted note.
     *
     * @return string
     */
    public function getFormattedNote() {
        $note = str_replace(
            '\r\n',
            '<br />',
            $this->Note
        );

        return $note;
    }

    /**
     * save the carts weight
     *
     * @return void
     */
    public function setWeight() {
        $member = Customer::currentUser();
        if ($member instanceof Member &&
            $member->getCart()->getWeightTotal()) {
            $this->WeightTotal = $member->getCart()->getWeightTotal();
        }
    }

    /**
     * set the total price for this order
     *
     * @return void
     */
    public function setAmountTotal() {
        $member = Customer::currentUser();

        if ($member && $member->getCart()) {
            $this->AmountTotal = $member->getCart()->getAmountTotal();
        }
    }

    /**
     * set the email for this order
     *
     * @param string $email the email address of the customer
     *
     * @return void
     */
    public function setCustomerEmail($email = null) {
        $member = Customer::currentUser();
        if ($member instanceof Member &&
            $member->Email) {
            $email = $member->Email;
        }
        $this->CustomersEmail = $email;
    }
    
    /**
     * Set the status of the revocation instructions checkbox field.
     *
     * @param boolean $status The status of the field
     * 
     * @return void
     */
    public function setHasAcceptedRevocationInstruction($status) {
        if ($status == 1) {
            $status = true;
        }
        
        $this->HasAcceptedRevocationInstruction = $status;
    }
    
    /**
     * Set the status of the terms and conditions checkbox field.
     *
     * @param boolean $status The status of the field
     * 
     * @return void
     */
    public function setHasAcceptedTermsAndConditions($status) {
        if ($status == 1) {
            $status = true;
        }
        
        $this->HasAcceptedTermsAndConditions = $status;
    }

    /**
     * The shipping method is a relation + an attribte of the order
     *
     * @param int $shippingMethodID the ID of the shipping method
     *
     * @return void
     */
    public function setShippingMethod($shippingMethodID) {
        $selectedShippingMethod = ShippingMethod::get()->byID($shippingMethodID);

        if ($selectedShippingMethod instanceof ShippingMethod &&
            $selectedShippingMethod->getShippingFee() instanceof ShippingFee) {
            $this->ShippingMethodID    = $selectedShippingMethod->ID;
            $this->ShippingFeeID       = $selectedShippingMethod->getShippingFee()->ID;
            $this->HandlingCostShipment->setAmount($selectedShippingMethod->getShippingFee()->getPriceAmount());
            $this->HandlingCostShipment->setCurrency(Config::DefaultCurrency());
        }
    }

    /**
     * returns tax included in $this
     *
     * @return float
     */
    public function getTax() {
        $tax = 0.0;

        foreach ($this->OrderPositions() as $orderPosition) {
            $tax += $orderPosition->TaxTotal;
        }

        $taxObj = new DBMoney('Tax');
        $taxObj->setAmount($tax);
        $taxObj->setCurrency(Config::DefaultCurrency());

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
     * Returns the Order Positions as a string.
     * 
     * @param bool $asHtmlString    Set to true to use HTML inside the string.
     * @param bool $withAmountTotal Set to true add the orders total amount.
     * 
     * @return string
     */
    public function getPositionsAsString($asHtmlString = false, $withAmountTotal = false) {
        if ($asHtmlString) {
            $seperator = '<br/>';
        } else {
            $seperator = PHP_EOL;
        }
        $positionsStrings = array();
        foreach ($this->OrderPositions() as $position) {
            $positionsString = $position->getTypeSafeQuantity() . 'x #' . $position->ProductNumber . ' "' . $position->Title . '" ' . $position->getPriceTotalNice();
            $positionsStrings[] = $positionsString;
        }
        $positionsAsString = implode($seperator . '------------------------' . $seperator, $positionsStrings);
        if ($withAmountTotal) {
            $shipmentAndPayment = new DBMoney();
            $shipmentAndPayment->setAmount($this->HandlingCostPayment->getAmount() + $this->HandlingCostShipment->getAmount());
            $shipmentAndPayment->setCurrency($this->HandlingCostPayment->getCurrency());
            
            $positionsAsString .= $seperator . '------------------------' . $seperator;
            $positionsAsString .= $this->fieldLabel('HandlingCost') . ': ' . $shipmentAndPayment->Nice() . $seperator;
            $positionsAsString .= '________________________' . $seperator . $seperator;
            $positionsAsString .= $this->fieldLabel('AmountTotal') . ': ' . $this->AmountTotal->Nice();
        }
        return $positionsAsString;
    }
    
    /**
     * Returns the gross amount of all order positions.
     * 
     * @return DBMoney
     */
    public function getPositionsPriceGross() {
        $positionsPriceGross = $this->AmountTotal->getAmount() - ($this->HandlingCostShipment->getAmount() + $this->HandlingCostPayment->getAmount());

        $positionsPriceGrossObj = new DBMoney();
        $positionsPriceGrossObj->setAmount($positionsPriceGross);
        $positionsPriceGrossObj->setCurrency(Config::DefaultCurrency());
        
        return $positionsPriceGrossObj;
    }

    /**
     * Returns the net amount of all order positions.
     *
     * @return DBMoney
     */
    public function getPositionsPriceNet() {
        $priceNet = $this->getPositionsPriceGross()->getAmount() - $this->getTax(true,true,true)->getAmount();

        $priceNetObj = new DBMoney();
        $priceNetObj->setAmount($priceNet);
        $priceNetObj->setCurrency(Config::DefaultCurrency());
        
        return $priceNetObj;
    }

    /**
     * Returns the gross amount of the order.
     *
     * @return DBMoney
     */
    public function getPriceGross() {
        return $this->AmountTotal;
    }
    
    /**
     * Returns all order positions without a tax value.
     * 
     * @return ArrayList
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.12.2011
     */
    public function OrderPositionsWithoutTax() {
        $orderPositions = new ArrayList();
        
        foreach ($this->OrderPositions() as $orderPosition) {
            if (!$orderPosition->isChargeOrDiscount &&
                 $orderPosition->TaxRate == 0) {
                
                $orderPositions->push($orderPosition);
            }
        }
        
        return $orderPositions;
    }

    /**
     * Returns all OrderPositions that are included in the total
     * price.
     *
     * @return mixed ArrayList
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.03.2013
     */
    public function OrderIncludedInTotalPositions() {
        $positions = new ArrayList();

        foreach ($this->OrderPositions() as $orderPosition) {
            if ($orderPosition->isIncludedInTotal) {
                $positions->push($orderPosition);
            }
        }

        return $positions;
    }

    /**
     * Returns all regular order positions.
     *
     * @return ArrayList
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>,
     *         Ramon Kupper <rkupper@pixeltricks.de>
     * @since 16.11.2013
     */
    public function OrderListPositions() {
        $orderPositions = new ArrayList();
        
        foreach ($this->OrderPositions() as $orderPosition) {
            if (!$orderPosition->isChargeOrDiscount) {
                
                $orderPositions->push($orderPosition);
            }
        }
        
        return $orderPositions;
    }
    
    /**
     * Returns all order positions that contain charges and discounts for the 
     * shopping cart value.
     *
     * @return ArrayList
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.12.2011
     */
    public function OrderChargePositionsTotal() {
        $chargePositions = new ArrayList();
        
        foreach ($this->OrderPositions() as $orderPosition) {
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
     * @return ArrayList
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.12.2011
     */
    public function OrderChargePositionsProduct() {
        $chargePositions = new ArrayList();
        
        foreach ($this->OrderPositions() as $orderPosition) {
            if ($orderPosition->isChargeOrDiscount &&
                $orderPosition->chargeOrDiscountModificationImpact == 'productValue') {
                
                $chargePositions->push($orderPosition);
            }
        }
        
        return $chargePositions;
    }

    /**
     * returns the orders taxable amount without fees as string incl. currency.
     *
     * @param boolean $includeChargesForProducts Indicates wether to include charges and
     *                                           discounts for products
     * @param boolean $includeChargesForTotal    Indicates wether to include charges and
     *                                           discounts for the shopping cart total
     *
     * @return string
     */
    public function getTaxableAmountWithoutFeesNice($includeChargesForProducts = false, $includeChargesForTotal = false) {
        $taxableAmountWithoutFees = $this->getTaxableAmountWithoutFees($includeChargesForProducts, $includeChargesForTotal);
        return str_replace('.', ',', number_format($taxableAmountWithoutFees->Amount->getAmount(), 2)) . ' ' . $this->AmountTotal->getCurrency();
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
     * @return DBMoney
     */
    public function getTaxableAmountWithoutFees($includeChargesForProducts = false, $includeChargesForTotal = false) {
        $taxableAmountWithoutFees = null;
        if ($this->IsPriceTypeGross()) {
            $taxableAmountWithoutFees = $this->getTaxableAmountGrossWithoutFees($includeChargesForProducts, $includeChargesForTotal);
        } else {
            $taxableAmountWithoutFees = $this->getTaxableAmountNetWithoutFees($includeChargesForProducts, $includeChargesForTotal);
        }
        return $taxableAmountWithoutFees;
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
     * @return DBMoney
     */
    public function getTaxableAmountGrossWithoutFees($includeChargesForProducts = false, $includeChargesForTotal = false) {
        $priceGross = new DBMoney();
        $priceGross->setAmount(0);
        $priceGross->setCurrency(Config::DefaultCurrency());
        
        if ($includeChargesForTotal == 'false') {
            $includeChargesForTotal = false;
        }
        if ($includeChargesForProducts == 'false') {
            $includeChargesForProducts = false;
        }
        
        foreach ($this->OrderPositions() as $position) {
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
            
            if ($position->TaxRate > 0 ||
                $position->IsNonTaxable) {
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
     * @return DBMoney
     */
    public function getTaxableAmountNetWithoutFees($includeChargesForProducts = false, $includeChargesForTotal = false) {
        $priceNet = new DBMoney();
        $priceNet->setAmount(0);
        $priceNet->setCurrency(Config::DefaultCurrency());
        
        if ($includeChargesForTotal == 'false') {
            $includeChargesForTotal = false;
        }
        if ($includeChargesForProducts == 'false') {
            $includeChargesForProducts = false;
        }
        
        foreach ($this->OrderPositions() as $position) {
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
            
            if ($position->TaxRate > 0 ||
                $position->IsNonTaxable) {
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
     * returns the orders taxable amount with fees as string incl. currency.
     *
     * @param boolean $includeChargesForProducts Indicates wether to include charges and
     *                                           discounts for products
     * @param boolean $includeChargesForTotal    Indicates wether to include charges and
     *                                           discounts for the shopping cart total
     *
     * @return string
     */
    public function getTaxableAmountWithFeesNice($includeChargesForProducts = false, $includeChargesForTotal = false) {
        $taxableAmountWithFees = $this->getTaxableAmountWithFees($includeChargesForProducts, $includeChargesForTotal);
        return str_replace('.', ',', number_format($taxableAmountWithFees->Amount->getAmount(), 2)) . ' ' . $this->AmountTotal->getCurrency();
    }

    /**
     * Returns the order value of all positions with a tax rate > 0 without any
     * charges.
     *
     * @param boolean $includeChargesForProducts Indicates wether to include charges and
     *                                           discounts for products
     * @param boolean $includeChargesForTotal    Indicates wether to include charges and
     *                                           discounts for the shopping cart total
     * 
     * @return DBMoney
     */
    public function getTaxableAmountWithFees($includeChargesForProducts = false, $includeChargesForTotal = false) {
        $taxableAmountWithFees = 0;
        if ($this->IsPriceTypeGross()) {
            $taxableAmountWithFees = $this->getTaxableAmountGrossWithFees($includeChargesForProducts, $includeChargesForTotal);
        } else {
            $taxableAmountWithFees = $this->getTaxableAmountNetWithFees($includeChargesForProducts, $includeChargesForTotal);
        }
        return $taxableAmountWithFees;
    }

    /**
     * Returns the order value of all positions with a tax rate > 0 without any
     * charges.
     *
     * @param boolean $includeChargesForProducts Indicates wether to include charges and
     *                                           discounts for products
     * @param boolean $includeChargesForTotal    Indicates wether to include charges and
     *                                           discounts for the shopping cart total
     * 
     * @return DBMoney
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
     * charges.
     *
     * @param boolean $includeChargesForProducts Indicates wether to include charges and
     *                                           discounts for products
     * @param boolean $includeChargesForTotal    Indicates wether to include charges and
     *                                           discounts for the shopping cart total
     * 
     * @return DBMoney
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
     * @return ArrayList
     */
    public function getTaxRatesWithoutFees($includeChargesForProducts = false, $includeChargesForTotal = false) {
        if ($includeChargesForTotal === 'false') {
            $includeChargesForTotal = false;
        }
        if ($includeChargesForProducts === 'false') {
            $includeChargesForProducts = false;
        }
        
        $taxes = new ArrayList();
        
        foreach ($this->OrderPositions() as $orderPosition) {
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
            if ($taxRate == '') {
                $taxRate = 0;
            }
            if ($taxRate >= 0 &&
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
            $taxObj = new DBMoney();
            $taxObj->setAmount($tax->AmountRaw);
            $taxObj->setCurrency(Config::DefaultCurrency());

            $tax->Amount = $taxObj;
        }
        
        return $taxes;
    }

    /**
     * Returns the total amount of all taxes.
     *
     * @param boolean $excludeCharges Indicates wether to exlude charges and discounts
     *
     * @return ArrayList
     */
    public function getTaxTotal($excludeCharges = false) {
        $taxRates = $this->getTaxRatesWithFees(true, false);

        if (!$excludeCharges &&
             $this->HasChargePositionsForTotal()) {

            foreach ($this->OrderChargePositionsTotal() as $charge) {
                $taxRate = $taxRates->find('Rate', $charge->TaxRate);

                if ($taxRate) {
                    $taxRateAmount   = $taxRate->Amount->getAmount();
                    $chargeTaxAmount = $charge->TaxTotal;
                    $taxRate->Amount->setAmount($taxRateAmount + $chargeTaxAmount);

                    if (round($taxRate->Amount->getAmount(), 2) === -0.00) {
                        $taxRate->Amount->setAmount(0);
                    }
                }
            }
        }

        $this->extend('updateTaxTotal', $taxRates);

        return $taxRates;
    }
    
    /**
     * Returns the tax total amount
     * 
     * @param bool $excludeCharges Exclude charges?
     * 
     * @return float
     */
    public function getTaxTotalAmount($excludeCharges = false) {
        $amount   = 0;
        $taxRates = $this->getTaxTotal($excludeCharges);
        foreach ($taxRates as $taxRate) {
            $amount += $taxRate->Amount->getAmount();
        }
        return round($amount, 2);
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
     * @return ArrayList
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
        $taxRateShipment = $this->TaxRateShipment;
        if ($taxRateShipment == '') {
            $taxRateShipment = 0;
        }
        if ($taxRateShipment >= 0 &&
            !$taxes->find('Rate', $taxRateShipment)) {

            $taxes->push(
                new DataObject(
                    array(
                        'Rate'      => $taxRateShipment,
                        'AmountRaw' => 0.0,
                    )
                )
            );
        }
        $taxSectionShipment = $taxes->find('Rate', $taxRateShipment);
        $taxSectionShipment->AmountRaw += $this->TaxAmountShipment;

        // Payment cost tax
        $taxRatePayment = $this->TaxRatePayment;
        if ($taxRatePayment == '') {
            $taxRatePayment = 0;
        }
        if ($taxRatePayment >= 0 &&
            !$taxes->find('Rate', $taxRatePayment)) {

            $taxes->push(
                new DataObject(
                    array(
                        'Rate'      => $taxRatePayment,
                        'AmountRaw' => 0.0,
                    )
                )
            );
        }
        $taxSectionPayment = $taxes->find('Rate', $taxRatePayment);
        $taxSectionPayment->AmountRaw += $this->TaxAmountPayment;

        foreach ($taxes as $tax) {
            $taxObj = new DBMoney;
            $taxObj->setAmount($tax->AmountRaw);
            $taxObj->setCurrency(Config::DefaultCurrency());

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
     */
    public function getQuantity($productId = null) {
        $positions = $this->OrderPositions();
        $quantity = 0;

        foreach ($positions as $position) {
            if ($productId === null ||
                    $position->Product()->ID === $productId) {

                $quantity += $position->Quantity;
            }
        }

        return $quantity;
    }
    
    /**
     * Returns plugin output.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.04.2018
     */
    public function OrderDetailInformation() {
        $orderDetailInformation = '';
        $this->extend('updateOrderDetailInformation', $orderDetailInformation);
        return $orderDetailInformation;
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
            $template = $viewableData->customise($this)->renderWith('SilverCart/Model/Pages/Includes/OrderDetailsGross');
        } else {
            $template = $viewableData->customise($this)->renderWith('SilverCart/Model/Pages/Includes/OrderDetailsNet');
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

        foreach ($this->OrderPositions() as $orderPosition) {
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

        foreach ($this->OrderPositions() as $orderPosition) {
            if ($orderPosition->isChargeOrDiscount &&
                $orderPosition->chargeOrDiscountModificationImpact == 'totalValue') {

                $hasChargePositionsForTotal = true;
            }
        }
        
        return $hasChargePositionsForTotal;
    }

    /**
     * Indicates wether there are positions that are included in the total
     * price.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.02.2013
     */
    public function HasIncludedInTotalPositions() {
        if ($this->OrderIncludedInTotalPositions()) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Returns the i18n text for the price type
     *
     * @return string
     */
    public function getPriceTypeText() {
        return _t(Customer::class . '.PRICETYPE_' . strtoupper($this->PriceType), $this->PriceType);
    }

    /**
     * Indicates wether this order is gross calculated or not.
     * 
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.04.2018
     */
    public function IsPriceTypeGross() {
        $isPriceTypeGross = false;

        if ($this->PriceType == 'gross') {
            $isPriceTypeGross = true;
        }

        $this->extend('updateIsPriceTypeGross', $isPriceTypeGross);

        return $isPriceTypeGross;
    }

    /**
     * Indicates wether this order is net calculated or not.
     * 
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.04.2018
     */
    public function IsPriceTypeNet() {
        $isPriceTypeNet = false;

        if ($this->PriceType == 'net') {
            $isPriceTypeNet = true;
        }

        $this->extend('updateIsPriceTypeNet', $isPriceTypeNet);

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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.04.2018
     */
    public function Log($context, $text) {
        Tools::Log($context, $text, 'order');
    }

    /**
     * Send a confirmation mail with order details to the customer $member
     *
     * @return \SilverCart\Model\Order\Order
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 12.04.2018
     */
    public function sendConfirmationMail() {
        $this->extend('onBeforeConfirmationMail');
        $params = [
            'OrderConfirmation' => [
                'Template'      => 'OrderConfirmation',
                'Recipient'     => $this->CustomersEmail,
                'Variables'     => [
                    'FirstName'  => $this->InvoiceAddress()->FirstName,
                    'Surname'    => $this->InvoiceAddress()->Surname,
                    'Salutation' => $this->InvoiceAddress()->getSalutationText(),
                    'Order'      => $this
                ],
                'Attachments'   => null,
            ],
            'OrderNotification' => [
                'Template'      => 'OrderNotification',
                'Recipient'     => Config::DefaultMailOrderNotificationRecipient(),
                'Variables'     => [
                    'FirstName'  => $this->InvoiceAddress()->FirstName,
                    'Surname'    => $this->InvoiceAddress()->Surname,
                    'Salutation' => $this->InvoiceAddress()->getSalutationText(),
                    'Order'      => $this
                ],
                'Attachments'   => null,
            ],
        ];
                
        $this->extend('updateConfirmationMail', $params);
        
        ShopEmail::send(
            $params['OrderConfirmation']['Template'],
            $params['OrderConfirmation']['Recipient'],
            $params['OrderConfirmation']['Variables'],
            $params['OrderConfirmation']['Attachments']
        );
        ShopEmail::send(
            $params['OrderNotification']['Template'],
            $params['OrderNotification']['Recipient'],
            $params['OrderNotification']['Variables'],
            $params['OrderNotification']['Attachments']
        );
        $this->extend('onAfterConfirmationMail', $params);
        return $this;
    }

    /**
     * Set a new/reserved ordernumber before writing and send attributed
     * ShopEmails.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.01.2014
     */
    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        
        if (empty ($this->OrderNumber)) {
            $this->OrderNumber = NumberRange::useReservedNumberByIdentifier('OrderNumber');
        }
        if (!$this->didHandleOrderStatusChange &&
            $this->ID > 0 && $this->isChanged('OrderStatusID')) {
            $this->didHandleOrderStatusChange = true;
            $this->extend('onBeforeOrderStatusChange');
            if (method_exists($this->PaymentMethod(), 'handleOrderStatusChange')) {
                $this->PaymentMethod()->handleOrderStatusChange($this);
            }
            $newOrderStatus = OrderStatus::get()->byID($this->OrderStatusID);
            
            if ($newOrderStatus) {
                if ($this->AmountTotalAmount > 0) {
                    $this->AmountTotal->setAmount($this->AmountTotalAmount);
                    $this->AmountTotal->setCurrency($this->AmountTotalCurrency);
                }
                
                $newOrderStatus->sendMailFor($this);
            }
            $orderStatusID = 0;
            if (array_key_exists('OrderStatusID', $this->original)) {
                $orderStatusID = $this->original['OrderStatusID'];
            }
            OrderLog::addChangedLog($this, OrderStatus::class, $orderStatusID, $this->OrderStatusID);
        }
        if (array_key_exists('sa__FirstName', $_POST) &&
            $this->ShippingAddress()->ID > 0) {
            foreach ($_POST as $paramName => $paramValue) {
                if (strpos($paramName, 'sa__') === 0) {
                    $addressParamName = str_replace('sa__', '', $paramName);
                    $this->ShippingAddress()->{$addressParamName} = $paramValue;
                }
            }
            $this->ShippingAddress()->write();
        }
        if (array_key_exists('ia__FirstName', $_POST) &&
            $this->InvoiceAddress()->ID > 0) {
            foreach ($_POST as $paramName => $paramValue) {
                if (strpos($paramName, 'ia__') === 0) {
                    $addressParamName = str_replace('ia__', '', $paramName);
                    $this->InvoiceAddress()->{$addressParamName} = $paramValue;
                }
            }
            $this->InvoiceAddress()->write();
        }
        $this->extend('updateOnBeforeWrite');
    }

    /**
     * hook triggered after write
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.01.2014
     */
    protected function onAfterWrite() {
        parent::onAfterWrite();

        $this->extend('updateOnAfterWrite');
        $this->didHandleOrderStatusChange = false;
    }

    /**
     * Returns an order by the given PaymentReferenceID.
     * 
     * @param string $paymentReferenceID Payment reference ID
     * 
     * @return Order
     */
    public static function get_by_payment_reference_id($paymentReferenceID) {
        return Order::get()->filter('PaymentReferenceID', $paymentReferenceID)->first();
    }

    /**
     * Returns an order by the given Order ID and Member.
     * If no Member is given, the current logged in Member will be used as fallback.
     * 
     * @param int    $orderID  Order ID
     * @param Member $customer Customer
     * 
     * @return Order
     */
    public static function get_by_customer($orderID, Member $customer = null) {
        $customerID = null;
        if ($customer instanceof Member &&
            $customer->exists()) {
            $customerID = $customer->ID;
        }
        return self::get_by_customer_id($orderID, $customerID);
    }

    /**
     * Returns an order by the given Order and Member ID.
     * If no Member ID is given, the current logged in Member's ID will be used as fallback.
     * 
     * @param int $orderID    Order ID
     * @param int $customerID Customer ID
     * 
     * @return Order
     */
    public static function get_by_customer_id($orderID, $customerID = null) {
        if (is_null($customerID)) {
            $customer = Security::getCurrentUser();
            if ($customer instanceof Member) {
                $customerID = $customer->ID;
            }
        }
        return Order::get()->filter(array(
            'ID'       => $orderID,
            'MemberID' => $customerID,
        ))->first();
    }
    
    /**
     * Calculates the total amount of positions and handling cost.
     * 
     * @return float
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.2018
     */
    public function calculateAmountTotal() {
        $amountTotal = 0;

        foreach ($this->OrderPositions() as $orderPosition) {
            $amountTotal += $orderPosition->PriceTotal->getAmount();
        }
        
        $amountTotal += $this->HandlingCostShipment->getAmount();
        $amountTotal += $this->HandlingCostPayment->getAmount();
        
        if ($this->IsPriceTypeNet()) {
            $amountTotal += $this->getTaxTotalAmount();
        }
        
        return $amountTotal;
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

        foreach ($this->OrderPositions() as $orderPosition) {
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
     * @return ShippingMethod
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.04.2012
     */
    public function ShippingMethod() {
        $shippingMethod = null;
        if ($this->getComponent('ShippingMethod')) {
            $shippingMethod = $this->getComponent('ShippingMethod');
            $shippingMethod->setShippingAddress($this->ShippingAddress());
        }
        return $shippingMethod;
    }

    /**
     * returns the orders total amount as string incl. currency.
     *
     * @return string
     * 
     * @deprecated Use property AmountTotal instead
     */
    public function getAmountTotalNice() {
        return $this->AmountTotal->Nice();
    }

    /**
     * returns the orders total amount as string incl. currency.
     *
     * @return string
     */
    public function getHandlingCostShipmentNice() {
        return str_replace('.', ',', number_format($this->HandlingCostShipmentAmount, 2)) . ' ' . $this->HandlingCostShipmentCurrency;
    }

    /**
     * returns the orders total amount as string incl. currency.
     *
     * @return string
     */
    public function getHandlingCostPaymentNice() {
        return str_replace('.', ',', number_format($this->HandlingCostPaymentAmount, 2)) . ' ' . $this->HandlingCostPaymentCurrency;
    }
    
    /**
     * Marks the order as seen
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function markAsSeen() {
        if (!$this->IsSeen) {
            $this->IsSeen = true;
            $this->write();
            OrderLog::addMarkedAsSeenLog($this, Order::class);
        }
    }
    
    /**
     * Marks the order as not seen
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function markAsNotSeen() {
        if ($this->IsSeen) {
            $this->IsSeen = false;
            $this->write();
            OrderLog::addMarkedAsNotSeenLog($this, Order::class);
        }
    }
    
}
