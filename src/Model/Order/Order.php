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
use SilverCart\Model\Pages\Page as SilverCartPage;
use SilverCart\Model\Payment\HandlingCost;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Payment\PaymentStatus;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\StockItemEntry;
use SilverCart\Model\Shipment\ShippingFee;
use SilverCart\Model\Shipment\ShippingMethod;
use SilverCart\ORM\DataObjectExtension;
use SilverCart\ORM\FieldType\DBMoney as SilverCartDBMoney;
use SilverCart\ORM\Filters\DateRangeSearchFilter;
use SilverCart\ORM\Filters\ExactMatchBooleanMultiFilter;
use SilverCart\ORM\Search\SearchContext;
use SilverCart\View\Printer\Printer;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_Base;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\FieldType\DBMoney;
use SilverStripe\ORM\Filters\ExactMatchFilter;
use SilverStripe\ORM\Filters\PartialMatchFilter;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Security\Security;
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
 * 
 * @property SilverCartDBMoney $AmountTotal                      Total Amount
 * @property string            $PriceType                        Price Type
 * @property SilverCartDBMoney $HandlingCostPayment              Handling Cost Payment
 * @property SilverCartDBMoney $HandlingCostShipment             Handling Cost Shipment
 * @property int               $TaxRatePayment                   Tax Rate Payment
 * @property int               $TaxRateShipment                  Tax Rate Shipment
 * @property float             $TaxAmountPayment                 Tax Amount Payment
 * @property float             $TaxAmountShipment                Tax Amount Shipment
 * @property string            $Note                             Note
 * @property float             $WeightTotal                      Weight Total
 * @property string            $WeightUnit                       Weight Unit
 * @property string            $CustomersEmail                   Customers Email
 * @property string            $OrderNumber                      Order Number
 * @property bool              $HasAcceptedTermsAndConditions    Has Accepted Terms And Conditions
 * @property bool              $HasAcceptedRevocationInstruction Has Accepted Revocation Instruction
 * @property bool              $IsSeen                           Is Seen Status
 * @property string            $TrackingCode                     Tracking Code
 * @property string            $TrackingLink                     Tracking Link
 * @property string            $PaymentReferenceID               Payment Reference ID
 * @property string            $PaymentReferenceMessage          Payment Reference Message
 * @property string            $PaymentReferenceData             Payment Reference Data
 * @property string            $ExpectedDeliveryMin              Minimum Expected Delivery Date
 * @property string            $ExpectedDeliveryMax              Maximum Expected Delivery Date
 * @property string            $PaymentDate                      Payment Date
 * @property string            $ShippingDate                     Shipping Date
 * 
 * @property int $ShippingAddressID The ID of the related ShippingAddress.
 * @property int $InvoiceAddressID  The ID of the related InvoiceAddress.
 * @property int $PaymentMethodID   The ID of the related PaymentMethod.
 * @property int $ShippingMethodID  The ID of the related ShippingMethod.
 * @property int $OrderStatusID     The ID of the related OrderStatus.
 * @property int $PaymentStatusID   The ID of the related PaymentStatus.
 * @property int $MemberID          The ID of the related Member.
 * @property int $ShippingFeeID     The ID of the related ShippingFee.
 * 
 * @method OrderShippingAddress ShippingAddress() Returns the related ShippingAddress.
 * @method OrderInvoiceAddress  InvoiceAddress()  Returns the related InvoiceAddress.
 * @method PaymentMethod        PaymentMethod()   Returns the related PaymentMethod.
 * @method OrderStatus          OrderStatus()     Returns the related OrderStatus.
 * @method PaymentStatus        PaymentStatus()   Returns the related PaymentStatus.
 * @method Member               Member()          Returns the related Member.
 * @method ShippingFee          ShippingFee()     Returns the related ShippingFee.
 * 
 * @method \SilverStripe\ORM\HasManyList OrderPositions() Returns the related OrderPositions.
 * @method \SilverStripe\ORM\HasManyList OrderLogs()      Returns the related OrderLogs.
 */
class Order extends DataObject implements PermissionProvider
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    const SESSION_KEY           = 'SilverCart.Order';
    const SESSION_KEY_EDIT_MODE = 'SilverCart.Order.EditMode';
    const ADMIN_MODE_EDIT       = 'edit';
    const ADMIN_MODE_VIEW       = 'view';
    const PERMISSION_EDIT       = 'SILVERCART_ORDER_EDIT';
    const PERMISSION_DELETE     = 'SILVERCART_ORDER_DELETE';
    const PERMISSION_VIEW       = 'SILVERCART_ORDER_VIEW';

    /**
     * attributes
     *
     * @var array
     */
    private static $db = [
        'AmountTotal'                       => SilverCartDBMoney::class, // value of all products
        'PriceType'                         => 'Varchar(24)',
        'HandlingCostPayment'               => SilverCartDBMoney::class,
        'HandlingCostShipment'              => SilverCartDBMoney::class,
        'TaxRatePayment'                    => 'Int',
        'TaxRateShipment'                   => 'Int',
        'TaxAmountPayment'                  => 'Float',
        'TaxAmountShipment'                 => 'Float',
        'Note'                              => 'Text',
        'WeightTotal'                       => 'Float', // default unit is gramm
        'WeightUnit'                        => Config::ENUMERATION_WEIGHT_UNIT,
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
        'ExpectedDeliveryMin'               => 'Date',
        'ExpectedDeliveryMax'               => 'Date',
        'PaymentDate'                       => 'Date',
        'ShippingDate'                      => 'Date',
    ];
    /**
     * 1:1 relations
     *
     * @var array
     */
    private static $has_one = [
        'ShippingAddress' => OrderShippingAddress::class,
        'InvoiceAddress'  => OrderInvoiceAddress::class,
        'PaymentMethod'   => PaymentMethod::class,
        'ShippingMethod'  => ShippingMethod::class,
        'OrderStatus'     => OrderStatus::class,
        'PaymentStatus'   => PaymentStatus::class,
        'Member'          => Member::class,
        'ShippingFee'     => ShippingFee::class,
    ];
    /**
     * 1:n relations
     *
     * @var array
     */
    private static $has_many = [
        'OrderPositions'  => OrderPosition::class,
        'OrderLogs'       => OrderLog::class,
    ];
    /**
     * Casting.
     *
     * @var array
     */
    private static $casting = [
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
    ];
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
     * Grant API access on this item.
     *
     * @var bool
     */
    private static $api_access = true;
    /**
     * Flag to determine whether the cancel is in progress.
     *
     * @var bool
     */
    protected $cancelInProgress = false;
    /**
     * Prevents multiple handling of order status change.
     *
     * @var bool
     */
    protected $didHandleOrderStatusChange = false;
    /**
     * Prevents multiple handling of payment status change.
     *
     * @var bool
     */
    protected $didHandlePaymentStatusChange = false;
    /**
     * Marker to check whether the CMS fields are called or not
     *
     * @var bool 
     */
    protected $getCMSFieldsIsCalled = false;
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this); 
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this); 
    }

    /**
     * Set permissions.
     *
     * @return array
     */
    public function providePermissions() : array
    {
        $permissions = [
            self::PERMISSION_VIEW   => [
                'name'     => $this->fieldLabel(self::PERMISSION_VIEW),
                'help'     => $this->fieldLabel(self::PERMISSION_VIEW . '_HELP'),
                'category' => $this->i18n_singular_name(),
                'sort'     => 10,
            ],
            self::PERMISSION_EDIT   => [
                'name'     => $this->fieldLabel(self::PERMISSION_EDIT),
                'help'     => $this->fieldLabel(self::PERMISSION_EDIT . '_HELP'),
                'category' => $this->i18n_singular_name(),
                'sort'     => 20,
            ],
            self::PERMISSION_DELETE => [
                'name'     => $this->fieldLabel(self::PERMISSION_DELETE),
                'help'     => $this->fieldLabel(self::PERMISSION_DELETE . '_HELP'),
                'category' => $this->i18n_singular_name(),
                'sort'     => 30,
            ],
        ];
        $this->extend('updateProvidePermissions', $permissions);
        return $permissions;
    }

    /**
     * Indicates wether the current user can view this object.
     * 
     * @param Member $member declated to be compatible with parent
     *
     * @return bool
     */
    public function canView($member = null) : bool
    {
        $canView = false;
        if (is_null($member)) {
            $member = Security::getCurrentUser();
        }
        if (($member instanceof Member
          && $member->ID == $this->MemberID
          && !is_null($this->MemberID))
         || Permission::checkMember($member, self::PERMISSION_VIEW)
        ) {
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
     */
    public function canCreate($member = null, $context = []) : bool
    {
        return false;
    }

    /**
     * Indicates wether the current user can edit this object.
     * 
     * @param Member $member declated to be compatible with parent
     *
     * @return bool
     */
    public function canEdit($member = null) : bool
    {
        return Permission::checkMember($member, self::PERMISSION_EDIT);
    }

    /**
     * Indicates wether the current user can delete this object.
     * 
     * @param Member $member declated to be compatible with parent
     *
     * @return bool
     */
    public function canDelete($member = null) : bool
    {
        return Permission::checkMember($member, self::PERMISSION_DELETE);
    }

    /**
     * Indicates wether the current user can delete this object.
     * 
     * @param Member $member declated to be compatible with parent
     *
     * @return bool
     */
    public function canCancel($member = null) : bool
    {
        $overwritten = null;
        $this->extend('overwriteCanCancel', $member, $overwritten);
        if ($overwritten !== null) {
            return (bool) $overwritten;
        }
        $can = $this->canEdit($member)
            && $this->OrderStatus()->Code !== OrderStatus::STATUS_CODE_CANCELED;
        $results = $this->extend('canCancel', $member);
        if ($results
         && is_array($results)
        ) {
            if(!min($results)) {
                $can = false;
            }
        }
        return $can;
    }
    
    /**
     * Returns the CSV export columns.
     * 
     * @return array
     */
    public function exportColumns() : array
    {
        $exportColumns = [];
        $this->owner->extend('updateExportColumns', $exportColumns);
        if (empty($exportColumns)) {
            $exportColumns = $this->summaryFields();
        }
        return $exportColumns;
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     */
    public function summaryFields() : array
    {
        $summaryFields = [
            'CreatedNice'                => $this->fieldLabel('Created'),
            'OrderNumber'                => $this->fieldLabel('OrderNumberShort'),
            'Member.CustomerNumber'      => $this->Member()->fieldLabel('CustomerNumberShort'),
            'ShippingAddressSummaryHtml' => $this->fieldLabel('ShippingAddress'),
            'InvoiceAddressSummaryHtml'  => $this->fieldLabel('InvoiceAddress'),
            'AmountTotalNice'            => $this->fieldLabel('AmountTotal'),
            'OrderStatus.Title'          => $this->fieldLabel('OrderStatus'),
            'PaymentStatus.Title'        => $this->fieldLabel('PaymentStatus'),
            'PaymentMethod.Title'        => $this->fieldLabel('PaymentMethod'),
            'ShippingMethod.Title'       => $this->fieldLabel('ShippingMethod'),
        ];
        $this->extend('updateSummaryFields', $summaryFields);

        return $summaryFields;
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     * 
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'ID'                               => _t(Order::class . '.ORDER_ID', 'Ordernumber'),
            'Created'                          => _t(SilverCartPage::class . '.ORDER_DATE', 'Order date'),
            'OrderNumber'                      => _t(Order::class . '.ORDERNUMBER', 'ordernumber'),
            'OrderNumberShort'                 => _t(Order::class . '.OrderNumberShort', 'Orderno.'),
            'ShippingFee'                      => _t(Order::class . '.SHIPPINGRATE', 'shipping costs'),
            'Note'                             => _t(Order::class . '.NOTE', 'Note'),
            'YourNote'                         => _t(Order::class . '.YOUR_REMARK', 'Your note'),
            'Member'                           => _t(Order::class . '.CUSTOMER', 'customer'),
            'Customer'                         => _t(Order::class . '.CUSTOMER', 'customer'),
            'CustomerData'                     => _t(Order::class . '.CUSTOMERDATA', 'Customer Data'),
            'MemberCustomerNumber'             => _t(Customer::class . '.CUSTOMERNUMBER', 'Customernumber'),
            'MemberEmail'                      => _t(Member::class . '.EMAIL', 'Email'),
            'Email'                            => _t(Address::class . '.EMAIL', 'Email address'),
            'ShippingAddress'                  => _t(Address::class . '.ShippingAddress', 'Shipping address'),
            'ShippingAddressFirstName'         => _t(Address::class . '.FIRSTNAME', 'Firstname'),
            'ShippingAddressSurname'           => _t(Address::class . '.SURNAME', 'Surname'),
            'ShippingAddressCountry'           => Country::singleton()->singular_name(),
            'ShippingAndInvoiceAddress'        => _t(SilverCartPage::class . '.SHIPPING_AND_BILLING', 'Shipping and invoice address'),
            'InvoiceAddress'                   => _t(Address::class . '.InvoiceAddress', 'Invoice address'),
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
            'OrderPositionsProductNumber'      => _t(Product::class . '.PRODUCTNUMBER', 'Item number'),
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
            'ChangePaymentStatus'              => _t(Order::class . '.BATCH_CHANGEPAYMENTSTATUS', 'Change payment status to...'),
            'IsSeen'                           => _t(Order::class . '.IS_SEEN', 'Seen'),
            'OrderLogs'                        => OrderLog::singleton()->plural_name(),
            'ValueOfGoods'                     => _t(SilverCartPage::class . '.VALUE_OF_GOODS', 'Value of goods'),
            'Tracking'                         => _t(Order::class . '.Tracking', 'Tracking'),
            'TrackingCode'                     => _t(Order::class . '.TrackingCode', 'Tracking Code'),
            'TrackingLink'                     => _t(Order::class . '.TrackingLink', 'Tracking Link'),
            'TrackingLinkLabel'                => _t(Order::class . '.TrackingLinkLabel', 'Reveal where my shipment currently is'),
            'PaymentReferenceID'               => _t(Order::class . '.PaymentReferenceID', 'Payment Provider Reference Number'),
            'PaymentReferenceMessage'          => _t(Order::class . '.PaymentReferenceMessage', 'Payment Provider Reference Message'),
            'PaymentReferenceData'             => _t(Order::class . '.PaymentReferenceData', 'Payment Provider Reference Data'),
            'ExpectedDelivery'                 => _t(Order::class . '.ExpectedDelivery', 'Expected Delivery'),
            'ExpectedDeliveryMax'              => _t(Order::class . '.ExpectedDeliveryMax', 'Maximum expected Delivery'),
            'ExpectedDeliveryMin'              => _t(Order::class . '.ExpectedDeliveryMin', 'Minimum expected Delivery'),
            'DateFormat'                       => Tools::field_label('DateFormat'),
            'PaymentMethodTitle'               => _t(Order::class . '.PAYMENTMETHODTITLE', 'Payment method'),
            'OrderAmount'                      => _t(Order::class . '.ORDER_VALUE', 'Orderamount'),
            'ResendOrderConfirmation'          => _t(Order::class . '.ResendOrderConfirmation', 'Resend order confirmation'),
            'ResendOrderConfirmationDesc'      => _t(Order::class . '.ResendOrderConfirmationDesc', 'Resends the order confirmation email to the customer'),
            self::PERMISSION_VIEW              => _t(Order::class . '.' . self::PERMISSION_VIEW, 'View order'),
            self::PERMISSION_VIEW . '_HELP'    => _t(Order::class . '.' . self::PERMISSION_VIEW . '_HELP', 'Allows an user to view any orders (not only owned ones!). Own orders can be viewed without this permission.'),
            self::PERMISSION_EDIT              => _t(Order::class . '.' . self::PERMISSION_EDIT, 'Edit order'),
            self::PERMISSION_EDIT . '_HELP'    => _t(Order::class . '.' . self::PERMISSION_EDIT . '_HELP', 'Allows an user to edit any orders (not only owned ones!).'),
            self::PERMISSION_DELETE            => _t(Order::class . '.' . self::PERMISSION_DELETE, 'Delete order'),
            self::PERMISSION_DELETE . '_HELP'  => _t(Order::class . '.' . self::PERMISSION_DELETE . '_HELP', 'Allows an user to delete any orders (not only owned ones!).'),
        ]);
    }
    
    /**
     * Searchable fields
     *
     * @return array
     */
    public function searchableFields() : array
    {
        $address          = Address::singleton();
        $searchableFields = [
            'Created' => [
                'title' => $this->fieldLabel('Created'),
                'filter' => DateRangeSearchFilter::class,
                'field' => TextField::class,
            ],
            'OrderNumber' => [
                'title' => $this->fieldLabel('OrderNumber'),
                'filter' => PartialMatchFilter::class,
            ],
            'IsSeen' => [
                'title'  => $this->fieldLabel('IsSeen'),
                'filter' => ExactMatchFilter::class,
            ],
            'OrderStatusID' => [
                'title'  => $this->fieldLabel('OrderStatus'),
                'filter' => ExactMatchBooleanMultiFilter::class,
                'field'  => \SilverCart\Admin\Forms\MultiDropdownField::class,
            ],
            'PaymentStatusID' => [
                'title'  => $this->fieldLabel('PaymentStatus'),
                'filter' => ExactMatchBooleanMultiFilter::class,
                'field'  => \SilverCart\Admin\Forms\MultiDropdownField::class,
            ],
            'PaymentMethodID'              => [
                'title'  => $this->fieldLabel('PaymentMethod'),
                'filter' => ExactMatchFilter::class,
            ],
            'ShippingMethodID'             => [
                'title'  => $this->fieldLabel('ShippingMethod'),
                'filter' => ExactMatchFilter::class,
            ],
            'Member.CustomerNumber'        => [
                'title'  => $this->fieldLabel('MemberCustomerNumber'),
                'filter' => PartialMatchFilter::class,
            ],
            'Member.Email'                 => [
                'title'  => $this->fieldLabel('MemberEmail'),
                'filter' => PartialMatchFilter::class,
            ],
            'ShippingAddress.FirstName'    => [
                'title'  => $this->fieldLabel('ShippingAddressFirstName'),
                'filter' => PartialMatchFilter::class,
            ],
            'ShippingAddress.Surname'      => [
                'title'  => $this->fieldLabel('ShippingAddressSurname'),
                'filter' => PartialMatchFilter::class,
            ],
            'ShippingAddress.Street'       => [
                'title'  => $address->fieldLabel('Street'),
                'filter' => PartialMatchFilter::class,
            ],
            'ShippingAddress.StreetNumber' => [
                'title'  => $address->fieldLabel('StreetNumber'),
                'filter' => PartialMatchFilter::class,
            ],
            'ShippingAddress.Postcode'     => [
                'title'  => $address->fieldLabel('Postcode'),
                'filter' => PartialMatchFilter::class,
            ],
            'ShippingAddress.City'         => [
                'title'  => $address->fieldLabel('City'),
                'filter' => PartialMatchFilter::class,
            ],
            'ShippingAddress.CountryID'    => [
                'title'  => $this->fieldLabel('ShippingAddressCountry'),
                'filter' => ExactMatchFilter::class,
            ],
            'OrderPositions.ProductNumber' => [
                'title'  => $this->fieldLabel('OrderPositionsProductNumber'),
                'filter' => PartialMatchFilter::class,
            ],
        ];
        $this->extend('updateSearchableFields', $searchableFields);

        return $searchableFields;
    }
    
    /**
     * Returns the Title.
     * 
     * @return string
     */
    public function getTitle()
    {
        $title = $this->fieldLabel('OrderNumber') . ': ' . $this->OrderNumber . ' | ' . $this->fieldLabel('Created') . ': ' . date($this->fieldLabel('DateFormat'), strtotime($this->Created)) . ' | ' . $this->fieldLabel('AmountTotal') . ': ' . $this->AmountTotal->Nice();
        $this->extend('updateTitle', $title);
        return $title;
    }
    
    /**
     * Returns the order number.
     * 
     * @return string
     */
    public function getOrderNumber() : string
    {
        $orderNumber = $this->getField('OrderNumber');
        $this->extend('updateOrderNumber', $orderNumber, $this->getCMSFieldsIsCalled);
        return (string) $orderNumber;
    }

    /**
     * Set the default search context for this field
     * 
     * @return SearchContext
     */
    public function getDefaultSearchContext()
    {
        return SearchContext::create(
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
    public function scaffoldSearchFields($_params = null)
    {
        $fields = parent::scaffoldSearchFields($_params);
        
        $fields->dataFieldByName('OrderStatusID')
                ->setSource(OrderStatus::get()->map()->toArray())
                ->setEmptyString(Tools::field_label('PleaseChoose'));
        $fields->dataFieldByName('PaymentStatusID')
                ->setSource(PaymentStatus::get()->map()->toArray())
                ->setEmptyString(Tools::field_label('PleaseChoose'));
        
        $order                 = Order::singleton();
        $basicLabelField       = HeaderField::create(  'BasicLabelField',       $order->fieldLabel('BasicData'));
        $customerLabelField    = HeaderField::create(  'CustomerLabelField',    $order->fieldLabel('CustomerData'));
        $positionLabelField    = HeaderField::create(  'PositionLabelField',    $order->fieldLabel('OrderPositionData'));
        $miscLabelField        = HeaderField::create(  'MiscLabelField',        $order->fieldLabel('MiscData'));
        $positionQuantityField = TextField::create(    'OrderPositionQuantity', $order->fieldLabel('OrderPositionQuantity'));
        $positionIsLimitField  = CheckboxField::create('OrderPositionIsLimit',  $order->fieldLabel('OrderPositionIsLimit'));
        $limitField            = TextField::create(    'SearchResultsLimit',    $order->fieldLabel('SearchResultsLimit'));
        
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
    public function getTrackingCode()
    {
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
    public function getTrackingLink()
    {
        $trackingLink = $this->getField('TrackingLink');
        $this->extend('updateTrackingLink', $trackingLink);
        return $trackingLink;
    }

    /**
     * returns the orders creation date formated: dd.mm.yyyy hh:mm
     *
     * @return string
     */
    public function getCreatedNice()
    {
        return Tools::getDateWithTimeNice($this->Created);
    }
    
    /**
     * Returns the date the current order status was changed.
     * 
     * @return DBDatetime|null
     */
    public function getOrderStatusChangedDate() : ?DBDatetime
    {
        $date = null;
        $log  = $this->OrderLogs()
                ->filter([
                    'Context'  => OrderStatus::class,
                    'TargetID' => $this->OrderStatus()->ID,
                ])
                ->sort('Created', 'DESC')
                ->first();
        if ($log instanceof OrderLog) {
            $date = $log->dbObject('Created');
        }
        return $date;
    }
    
    /**
     * Returns the date the current payment status was changed.
     * 
     * @return DBDatetime|null
     */
    public function getPaymentStatusChangedDate() : ?DBDatetime
    {
        $date = null;
        $log  = $this->OrderLogs()
                ->filter([
                    'Context'  => PaymentStatus::class,
                    'TargetID' => $this->PaymentStatus()->ID,
                ])
                ->sort('Created', 'DESC')
                ->first();
        if ($log instanceof OrderLog) {
            $date = $log->dbObject('Created');
        }
        return $date;
    }
    
    /**
     * Returns the expected delivery date (span).
     * 
     * @return string
     */
    public function getExpectedDelivery()
    {
        $expectedDelivery = $this->ExpectedDeliveryMax;
        if ($this->ExpectedDeliveryMin != $this->ExpectedDeliveryMax) {
            $expectedDelivery = $this->ExpectedDeliveryMin . ' - ' . $this->ExpectedDeliveryMax;
        }
        return $expectedDelivery;
    }
    
    /**
     * Returns the expected delivery date (span) in a nice format.
     * 
     * @return string
     */
    public function getExpectedDeliveryNice()
    {
        $expectedDelivery = date('d.m.Y', strtotime($this->ExpectedDeliveryMax));
        if ($this->ExpectedDeliveryMin != $this->ExpectedDeliveryMax) {
            $expectedDelivery = $this->ExpectedDeliveryMin . ' - ' . $this->ExpectedDeliveryMax;
            $expectedDelivery = date('d.m.Y', strtotime($this->ExpectedDeliveryMin)) . ' - ' . date('d.m.Y', strtotime($this->ExpectedDeliveryMax));
        }
        return $expectedDelivery;
    }

    /**
     * return the orders shipping address as complete string.
     * 
     * @param bool $disableUpdate Disable update by decorator?
     *
     * @return string
     */
    public function getShippingAddressSummary($disableUpdate = false)
    {
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
    public function getShippingAddressSummaryHtml()
    {
        return Tools::string2html(str_replace(PHP_EOL, '<br/>', $this->ShippingAddressSummary));
    }

    /**
     * Returns the shipping address rendered with a HTML table
     * 
     * @return type
     */
    public function getShippingAddressTable()
    {
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
    public function InvoiceAddressEqualsShippingAddress()
    {
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
    public function getInvoiceAddressSummary($disableUpdate = false)
    {
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
    public function getInvoiceAddressSummaryHtml()
    {
        return Tools::string2html(str_replace(PHP_EOL, '<br/>', $this->InvoiceAddressSummary));
    }
    
    /**
     * Returns the invoice address rendered with a HTML table
     * 
     * @return type
     */
    public function getInvoiceAddressTable()
    {
        return $this->InvoiceAddress()->renderWith('SilverCart/Email/Includes/AddressData');
    }

    /**
     * Returns a limited number of order positions.
     * 
     * @param int $numberOfPositions The number of positions to get.
     *
     * @return DataList
     */
    public function getLimitedOrderPositions($numberOfPositions = 2)
    {
        return $this->OrderPositions()->limit($numberOfPositions);
    }

    /**
     * Returns whether this order has more positions than $numberOfPositions.
     * 
     * @param int $numberOfPositions The number of positions to check for.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function hasMoreOrderPositionsThan($numberOfPositions = 2)
    {
        return $this->OrderPositions()->count() > $numberOfPositions;
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
    public function excludeFromScaffolding()
    {
        $excludeFromScaffolding = [
            'Version',
            'IsSeen',
            'ShippingAddress',
            'InvoiceAddress',
            'ShippingFee',
            'Member'
        ];
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }

    /**
     * customize backend fields
     *
     * @return FieldList the form fields for the backend
     */
    public function getCMSFields()
    {
        $this->getCMSFieldsIsCalled = true;
        $this->markAsSeen();
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            if ($this->isAdminModeEdit()) {
                $this->setCMSFieldsEdit($fields);
            } else {
                $this->setCMSFieldsView($fields);
            }

            //add print preview
            $tabPrint = $fields->findOrMakeTab('Root.PrintPreviewTab', $this->fieldLabel('PrintPreview'));
            /* @var $tabPrint \SilverStripe\Forms\Tab */
            $tabPrint->addExtraClass('h-100')
                    ->push(LiteralField::create(
                            'PrintPreviewField',
                            sprintf(
                                '<iframe width="100%%" height="100%%" border="0" src="%s" class="print-preview"></iframe>',
                                Printer::getPrintInlineURL($this)
                            )
            ));

            if (!empty($this->PaymentReferenceID)) {
                $fields->dataFieldByName('PaymentReferenceID')->setReadonly(true);
                $fields->dataFieldByName('PaymentReferenceMessage')->setReadonly(true);
            } else {
                $fields->removeByName('PaymentReferenceID');
                $fields->removeByName('PaymentReferenceMessage');
            }
            $fields->removeByName('PaymentReferenceData');
            $fields->removeByName('OrderLogs');
            $logField = GridField::create(
                    'OrderLogs',
                    $this->fieldLabel('OrderLogs'),
                    $this->OrderLogs(),
                    GridFieldConfig_Base::create()
            );
            $logField->getConfig()->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
            $fields->findOrMakeTab('Root.OrderLogs', $this->fieldLabel('OrderLogs'));
            $fields->addFieldToTab('Root.OrderLogs', $logField);
        });
        return DataObjectExtension::getCMSFields($this);
    }
    
    /**
     * Returns the CMS fields to view an order (readonly).
     * 
     * @param FieldList $fields Fields to update for view mode
     * 
     * @return $this
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function setCMSFieldsView($fields)
    {
        $fields->dataFieldByName('ShippingMethodID')->setSource(ShippingMethod::get()->map('ID', 'TitleWithCarrier')->toArray());
        
        $fields->insertBefore('AmountTotal', $handlingGroup = FieldGroup::create('Handling'));
        $fields->insertBefore('AmountTotal', $dateGroup = FieldGroup::create('Date'));
        $fields->insertBefore('AmountTotal', $trackingGroup = FieldGroup::create('Tracking'));
        $fields->insertBefore('AmountTotal', LiteralField::create('OrderPreview', $this->render()));
        
        $handlingGroup->push($fields->dataFieldByName('OrderStatusID'));
        $handlingGroup->push($fields->dataFieldByName('PaymentStatusID'));
        $handlingGroup->push($fields->dataFieldByName('ShippingMethodID'));
        $handlingGroup->push($fields->dataFieldByName('PaymentMethodID'));
        
        $dateGroup->push($fields->dataFieldByName('ExpectedDeliveryMin'));
        $dateGroup->push($fields->dataFieldByName('ExpectedDeliveryMax'));
        $dateGroup->push($fields->dataFieldByName('PaymentDate'));
        $dateGroup->push($fields->dataFieldByName('ShippingDate'));
        
        $trackingGroup->push($fields->dataFieldByName('TrackingCode'));
        $trackingGroup->push($fields->dataFieldByName('TrackingLink')->setRows(1));
        
        $fields->removeByName('AmountTotal');
        $fields->removeByName('PriceType');
        $fields->removeByName('HandlingCostPayment');
        $fields->removeByName('HandlingCostShipment');
        $fields->removeByName('TaxRatePayment');
        $fields->removeByName('TaxRateShipment');
        $fields->removeByName('TaxAmountPayment');
        $fields->removeByName('TaxAmountShipment');
        $fields->removeByName('Note');
        $fields->removeByName('WeightTotal');
        $fields->removeByName('CustomersEmail');
        $fields->removeByName('OrderNumber');
        $fields->removeByName('HasAcceptedTermsAndConditions');
        $fields->removeByName('HasAcceptedRevocationInstruction');
        $fields->removeByName('IsSeen');
        $fields->removeByName('OrderNumber');
        return $this;
    }
    
    /**
     * Returns the CMS fields to edit an order.
     * 
     * @param FieldList $fields Fields to update for edit mode
     * 
     * @return $this
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function setCMSFieldsEdit($fields)
    {
        //add the shipping/invloice address fields as own tab
        $address = Address::singleton();
        $fields->findOrMakeTab('Root.ShippingAddressTab', $this->fieldLabel('ShippingAddressTab'));
        $fields->findOrMakeTab('Root.InvoiceAddressTab',  $this->fieldLabel('InvoiceAddressTab'));

        $fields->addFieldToTab('Root.ShippingAddressTab', LiteralField::create('sa__Preview',           '<p>' . Convert::raw2xml($this->getShippingAddressSummary(true)) . '</p>'));
        $fields->addFieldToTab('Root.ShippingAddressTab', TextField::create('sa__TaxIdNumber',          $address->fieldLabel('TaxIdNumber'),        $this->ShippingAddress()->TaxIdNumber));
        $fields->addFieldToTab('Root.ShippingAddressTab', TextField::create('sa__Company',              $address->fieldLabel('Company'),            $this->ShippingAddress()->Company));
        $fields->addFieldToTab('Root.ShippingAddressTab', TextField::create('sa__FirstName',            $address->fieldLabel('FirstName'),          $this->ShippingAddress()->FirstName));
        $fields->addFieldToTab('Root.ShippingAddressTab', TextField::create('sa__Surname',              $address->fieldLabel('Surname'),            $this->ShippingAddress()->Surname));
        $fields->addFieldToTab('Root.ShippingAddressTab', TextField::create('sa__Addition',             $address->fieldLabel('Addition'),           $this->ShippingAddress()->Addition));
        $fields->addFieldToTab('Root.ShippingAddressTab', TextField::create('sa__Street',               $address->fieldLabel('Street'),             $this->ShippingAddress()->Street));
        $fields->addFieldToTab('Root.ShippingAddressTab', TextField::create('sa__StreetNumber',         $address->fieldLabel('StreetNumber'),       $this->ShippingAddress()->StreetNumber));
        $fields->addFieldToTab('Root.ShippingAddressTab', CheckboxField::create('sa__IsPackstation',    $address->fieldLabel('IsPackstation'),      $this->ShippingAddress()->IsPackstation));
        $fields->addFieldToTab('Root.ShippingAddressTab', TextField::create('sa__PostNumber',           $address->fieldLabel('PostNumber'),         $this->ShippingAddress()->PostNumber));
        $fields->addFieldToTab('Root.ShippingAddressTab', TextField::create('sa__Packstation',          $address->fieldLabel('PackstationPlain'),   $this->ShippingAddress()->Packstation));
        $fields->addFieldToTab('Root.ShippingAddressTab', TextField::create('sa__Postcode',             $address->fieldLabel('Postcode'),           $this->ShippingAddress()->Postcode));
        $fields->addFieldToTab('Root.ShippingAddressTab', TextField::create('sa__City',                 $address->fieldLabel('City'),               $this->ShippingAddress()->City));
        $fields->addFieldToTab('Root.ShippingAddressTab', DropdownField::create('sa__Country',          $address->fieldLabel('Country'),            Country::get_active()->map()->toArray(), $this->ShippingAddress()->Country()->ID));
        $fields->addFieldToTab('Root.ShippingAddressTab', TextField::create('sa__Phone',                $address->fieldLabel('Phone'),              $this->ShippingAddress()->Phone));

        $fields->addFieldToTab('Root.InvoiceAddressTab', LiteralField::create('ia__Preview',            '<p>' . Convert::raw2xml($this->getInvoiceAddressSummary(true)) . '</p>'));
        $fields->addFieldToTab('Root.InvoiceAddressTab', TextField::create('ia__TaxIdNumber',           $address->fieldLabel('TaxIdNumber'),        $this->InvoiceAddress()->TaxIdNumber));
        $fields->addFieldToTab('Root.InvoiceAddressTab', TextField::create('ia__Company',               $address->fieldLabel('Company'),            $this->InvoiceAddress()->Company));
        $fields->addFieldToTab('Root.InvoiceAddressTab', TextField::create('ia__FirstName',             $address->fieldLabel('FirstName'),          $this->InvoiceAddress()->FirstName));
        $fields->addFieldToTab('Root.InvoiceAddressTab', TextField::create('ia__Surname',               $address->fieldLabel('Surname'),            $this->InvoiceAddress()->Surname));
        $fields->addFieldToTab('Root.InvoiceAddressTab', TextField::create('ia__Addition',              $address->fieldLabel('Addition'),           $this->InvoiceAddress()->Addition));
        $fields->addFieldToTab('Root.InvoiceAddressTab', TextField::create('ia__Street',                $address->fieldLabel('Street'),             $this->InvoiceAddress()->Street));
        $fields->addFieldToTab('Root.InvoiceAddressTab', TextField::create('ia__StreetNumber',          $address->fieldLabel('StreetNumber'),       $this->InvoiceAddress()->StreetNumber));
        $fields->addFieldToTab('Root.InvoiceAddressTab', CheckboxField::create('ia__IsPackstation',     $address->fieldLabel('IsPackstation'),      $this->InvoiceAddress()->IsPackstation));
        $fields->addFieldToTab('Root.InvoiceAddressTab', TextField::create('ia__PostNumber',            $address->fieldLabel('PostNumber'),         $this->InvoiceAddress()->PostNumber));
        $fields->addFieldToTab('Root.InvoiceAddressTab', TextField::create('ia__Packstation',           $address->fieldLabel('PackstationPlain'),   $this->InvoiceAddress()->Packstation));
        $fields->addFieldToTab('Root.InvoiceAddressTab', TextField::create('ia__Postcode',              $address->fieldLabel('Postcode'),           $this->InvoiceAddress()->Postcode));
        $fields->addFieldToTab('Root.InvoiceAddressTab', TextField::create('ia__City',                  $address->fieldLabel('City'),               $this->InvoiceAddress()->City));
        $fields->addFieldToTab('Root.InvoiceAddressTab', DropdownField::create('ia__Country',           $address->fieldLabel('Country'),            Country::get_active()->map()->toArray(), $this->InvoiceAddress()->Country()->ID));
        $fields->addFieldToTab('Root.InvoiceAddressTab', TextField::create('ia__Phone',                 $address->fieldLabel('Phone'),              $this->InvoiceAddress()->Phone));
        return $this;
    }
    
    /**
     * Returns the CMS actions.
     * 
     * @return FieldList
     */
    public function getCMSActions() : FieldList
    {
        $this->beforeUpdateCMSActions(function(FieldList $actions) {
            if ($this->exists()) {
                if ($this->isAdminModeEdit()) {
                    $mode     = self::ADMIN_MODE_VIEW;
                    $btnTitle = _t(self::class . '.AdminModeView', 'View Order Data');
                    $btnIcon  = 'font-icon-eye';
                } else {
                    $mode     = self::ADMIN_MODE_EDIT;
                    $btnTitle = _t(self::class . '.AdminModeEdit', 'Edit Order Data');
                    $btnIcon  = 'font-icon-edit';
                }
                $actions->push(
                    FormAction::create("switcheditmodeto{$mode}", $btnTitle)
                        ->addExtraClass("btn-outline-info {$mode} {$btnIcon}")
                        ->setUseButtonTag(true)
                );
                $actions->push(
                    FormAction::create('resendorderconfirmation', $this->fieldLabel('ResendOrderConfirmation'))
                        ->addExtraClass("btn-outline-info font-icon-p-mail")
                        ->setUseButtonTag(true)
                );
            }
        });
        return parent::getCMSActions();
    }
    
    /**
     * Resends the confirmation email to the customer.
     * 
     * @param GridFieldDetailForm_ItemRequest $itemRequest GridField item request
     * @param array                           $data        Submitted data
     * @param Form                            $form        Form
     * 
     * @return void
     */
    public function resendorderconfirmation(GridFieldDetailForm_ItemRequest $itemRequest, array $data, Form $form)
    {
        $this->sendConfirmationMail(false);
        $message = _t(Order::class . '.ResendOrderConfirmationDone', 'Sent confirmation email to {email}', [
            'email' => $this->CustomersEmail,
        ]);
        $form->sessionMessage($message, \SilverStripe\ORM\ValidationResult::TYPE_GOOD, \SilverStripe\ORM\ValidationResult::CAST_HTML);
        return $itemRequest->edit(Controller::curr()->getRequest());
    }
    
    /**
     * Switched the admin mode to edit.
     * 
     * @param GridFieldDetailForm_ItemRequest $itemRequest GridField item request
     * @param array                           $data        Submitted data
     * @param Form                            $form        Form
     * 
     * @return ViewableData
     */
    public function switcheditmodetoedit(GridFieldDetailForm_ItemRequest $itemRequest, array $data, Form $form)
    {
        return $this->switcheditmode($itemRequest, self::ADMIN_MODE_EDIT);
    }
    
    /**
     * Switched the admin mode to view.
     * 
     * @param GridFieldDetailForm_ItemRequest $itemRequest GridField item request
     * @param array                           $data        Submitted data
     * @param Form                            $form        Form
     * 
     * @return ViewableData
     */
    public function switcheditmodetoview(GridFieldDetailForm_ItemRequest $itemRequest, array $data, Form $form)
    {
        return $this->switcheditmode($itemRequest, self::ADMIN_MODE_VIEW);
    }
    
    /**
     * Switches the admin mode to view or edit.
     * 
     * @param GridFieldDetailForm_ItemRequest $itemRequest GridField item request
     * @param string                          $mode        Admin mode
     * 
     * @return ViewableData
     */
    public function switcheditmode(GridFieldDetailForm_ItemRequest $itemRequest, string $mode = self::ADMIN_MODE_VIEW)
    {
        if (is_null($mode)) {
            $mode = self::ADMIN_MODE_VIEW;
        }
        Tools::Session()->set(self::SESSION_KEY_EDIT_MODE, $mode);
        Tools::saveSession();
        return $itemRequest->edit(Controller::curr()->getRequest());
    }
    
    /**
     * Returns whether the current admin mode is view or edit.
     * 
     * @return bool
     */
    public function isAdminModeEdit()
    {
        return Tools::Session()->get(self::SESSION_KEY_EDIT_MODE) === self::ADMIN_MODE_EDIT;
    }
    
    /**
     * Returns the quick access fields to display in GridField
     * 
     * @return FieldList
     */
    public function getQuickAccessFields()
    {
        $quickAccessFields = FieldList::create();
        
        $threeColField = '<div class="multi-col-field"><strong>%s</strong><span>%s</span><span>%s</span></div>';
        $twoColField   = '<div class="multi-col-field"><strong>%s</strong><span></span><span>%s</span></div>';
        
        $infoField = LiteralField::create('OrderInfo__' . $this->ID,
                "{$this->fieldLabel('OrderNumber')}: <strong>{$this->OrderNumber}</strong>"
                . " | {$this->fieldLabel('OrderStatus')}: <strong>{$this->OrderStatus()->Title}</strong>"
                . " | {$this->fieldLabel('PaymentStatus')}: <strong>{$this->PaymentStatus()->Title}</strong>"
        );
        $orderPositionTable = TableField::create(
                'OrderPositions__' . $this->ID,
                $this->fieldLabel('OrderPositions'),
                $this->OrderPositions()
        );
        $shippingField    = LiteralField::create('ShippingMethod__' . $this->ID, sprintf($threeColField, $this->fieldLabel('ShippingMethod'), $this->ShippingMethod()->TitleWithCarrier, $this->HandlingCostShipmentNice));
        $paymentField     = LiteralField::create('PaymentMethod__' . $this->ID,  sprintf($threeColField, $this->fieldLabel('PaymentMethod'),  $this->PaymentMethod()->Title,             $this->HandlingCostPaymentNice));
        $amountTotalField = LiteralField::create('AmountTotal__' . $this->ID,    sprintf($twoColField,   $this->fieldLabel('AmountTotal'),    $this->AmountTotalNice));
        
        $quickAccessFields->push($infoField);
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
    public function createInvoiceAddress($addressData = [])
    {
        $this->extend('onBeforeCreateInvoiceAddress', $addressData, $this);
        $orderInvoiceAddress    = $this->createAddress($addressData, OrderInvoiceAddress::create());
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
    public function createShippingAddress($addressData = [])
    {
        $this->extend('onBeforeCreateShippingAddress', $addressData, $this);
        $orderShippingAddress    = $this->createAddress($addressData, OrderShippingAddress::create());
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
    public function createAddress($addressData, $address)
    {
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
     * @param ShoppingCart $shoppingCart  Optional shopping cart context
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.20118
     */
    public function createFromShoppingCart(ShoppingCart $shoppingCart = null)
    {
        $member = Customer::currentUser();
        if ($member instanceof Member) {
            if ($shoppingCart === null) {
                $shoppingCart = $member->getCart();
            }
            $shoppingCart->setPaymentMethodID((int) $this->PaymentMethodID);
            $shoppingCart->setShippingMethodID((int) $this->ShippingMethodID);
            $this->MemberID                  = $member->ID;
            $this->ExpectedDeliveryMin       = $shoppingCart->getDeliveryTimeMin();
            $this->ExpectedDeliveryMax       = $shoppingCart->getDeliveryTimeMax();
            $overwriteCreateFromShoppingCart = false;
            $this->extend('overwriteCreateFromShoppingCart', $overwriteCreateFromShoppingCart, $shoppingCart);
            if ($overwriteCreateFromShoppingCart) {
                return true;
            }
            $this->extend('onBeforeCreateFromShoppingCart', $shoppingCart);
            // VAT tax for shipping and payment fees
            $shippingMethod = ShippingMethod::get()->byID($this->ShippingMethodID);
            if ($shippingMethod instanceof ShippingMethod
             && $shippingMethod->exists()
            ) {
                $shippingFee = $shippingMethod->getShippingFee();
                if ($shippingFee instanceof ShippingFee
                 && $shippingFee->exists()
                 && $shippingFee->Tax()->exists()
                ) {
                    $this->TaxRateShipment   = $shippingFee->getTaxRate();
                    $this->TaxAmountShipment = $shippingFee->getTaxAmount();
                }
            }
            $paymentMethod = PaymentMethod::get()->byID($this->PaymentMethodID);
            if ($paymentMethod instanceof PaymentMethod
             && $paymentMethod->exists()
            ) {
                $paymentFee = $paymentMethod->getHandlingCost();
                if ($paymentFee instanceof HandlingCost
                 && $paymentFee->exists()
                ) {
                    if ($paymentFee->Tax()->exists()) {
                        $this->TaxRatePayment   = $paymentFee->Tax()->getTaxRate();
                        $this->TaxAmountPayment = $paymentFee->getTaxAmount();
                    }
                    $this->HandlingCostPayment->setAmount($paymentFee->amount->getAmount());
                    $this->HandlingCostPayment->setCurrency($paymentFee->amount->getCurrency());
                }
            }
            // amount of all positions + handling fee of the payment method + shipping fee
            $totalAmount = $shoppingCart->getAmountTotal()->getAmount();
            $this->AmountTotal->setAmount($totalAmount);
            $this->AmountTotal->setCurrency(Config::DefaultCurrency());
            $this->PriceType = $member->getPriceType();
            // adjust orders standard status
            if ((int) $this->OrderStatusID === 0) {
                $orderStatus = OrderStatus::get_default();
                if ($orderStatus instanceof OrderStatus
                 && $orderStatus->exists()
                ) {
                    $this->OrderStatusID = $orderStatus->ID;
                }
            }
            $paymentStatus = $paymentMethod->PaymentStatus();
            if (!$paymentStatus->exists()) {
                $paymentStatus = PaymentStatus::get_default();
            }
            if ($paymentStatus instanceof PaymentStatus
             && $paymentStatus->exists()
            ) {
                $this->PaymentStatusID = $paymentStatus->ID;
            }
            $this->write();
            $this->extend('onAfterCreateFromShoppingCart', $shoppingCart);
        }
    }

    /**
     * Converts the given $shoppingCartPosition to an order position.
     * 
     * @param ShoppingCartPosition $shoppingCartPosition Shopping cart position
     *
     * @return OrderPosition|null
     */
    public function convertShoppingCartPositionToOrderPosition(ShoppingCartPosition $shoppingCartPosition, Member $customer) : ?OrderPosition
    {
        $orderPosition = null;
        $product       = $shoppingCartPosition->Product();
        if ($product->exists()) {
            $orderPosition = OrderPosition::create();
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
            $orderPosition->IsNonTaxable            = $customer->doesNotHaveToPayTaxes();
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
                $reason = "{$this->fieldLabel('OrderNumber')}: {$this->OrderNumber} [#{$this->ID}]";
                $product->decrementStockQuantity($shoppingCartPosition->Quantity, $reason, StockItemEntry::ORIGIN_CODE_ORDER_PLACED, $this);
            }
            $this->extend('onAfterConvertSingleShoppingCartPositionToOrderPosition', $shoppingCartPosition, $orderPosition);
            $orderPosition->write();
        }
        return $orderPosition;
    }
    
    /**
     * Adds charges and discounts for products.
     * 
     * @param \SilverCart\Model\Order\ShoppingCart $shoppingCart Shopping cart
     * 
     * @return void
     */
    public function addChargesAndDiscountsForProducts(ShoppingCart $shoppingCart) : void
    {
        // Get charges and discounts for product values
        if ($shoppingCart->HasChargesAndDiscountsForProducts()) {
            $chargesAndDiscountsForProducts = $shoppingCart->ChargesAndDiscountsForProducts();
            foreach ($chargesAndDiscountsForProducts as $chargeAndDiscountForProduct) {
                $orderPosition = OrderPosition::create();
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
                $orderPosition->Title               = (string) $chargeAndDiscountForProduct->Name;
                $orderPosition->OrderID             = $this->ID;
                $orderPosition->write();
                unset($orderPosition);
            }
        }
    }

    /**
     * convert cart positions in order positions
     * 
     * @param ShoppingCart $shoppingCart  Optional shopping cart context
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.11.2014
     */
    public function convertShoppingCartPositionsToOrderPositions(ShoppingCart $shoppingCart = null)
    {
        if ($this->extend('updateConvertShoppingCartPositionsToOrderPositions')) {
            return true;
        }
        
        $member = Customer::currentUser();
        if ($member instanceof Member) {
            if ($shoppingCart === null) {
                $shoppingCart = $member->getCart();
            }
            $shoppingCart->setPaymentMethodID((int) $this->PaymentMethodID);
            $shoppingCart->setShippingMethodID((int) $this->ShippingMethodID);
            //$shoppingCartPositions = ShoppingCartPosition::get()->filter('ShoppingCartID', $member->ShoppingCartID);
            $shoppingCartPositions = $shoppingCart->ShoppingCartPositions();

            if ($shoppingCartPositions->exists()) {
                foreach ($shoppingCartPositions as $shoppingCartPosition) {
                    $orderPosition = $this->convertShoppingCartPositionToOrderPosition($shoppingCartPosition, $member);
                    unset($orderPosition);
                }

                // Get taxable positions from registered modules
                $registeredModules = $shoppingCart->callMethodOnRegisteredModules(
                    'ShoppingCartPositions',
                    [
                        $shoppingCart,
                        $member,
                        true
                    ]
                );

                foreach ($registeredModules as $moduleName => $moduleOutput) {
                    foreach ($moduleOutput as $modulePosition) {
                        $orderPosition = OrderPosition::create();
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
                        $orderPosition->Title               = (string) $modulePosition->Name;
                        if ($modulePosition->isChargeOrDiscount) {
                            $orderPosition->isChargeOrDiscount                  = true;
                            $orderPosition->chargeOrDiscountModificationImpact  = $modulePosition->chargeOrDiscountModificationImpact;
                        }
                        $orderPosition->OrderID = $this->ID;
                        $this->extend('onBeforeConvertSingleModulePositionToOrderPosition', $modulePosition, $orderPosition, $moduleName);
                        $orderPosition->write();
                        $this->extend('onAfterConvertSingleModulePositionToOrderPosition', $modulePosition, $orderPosition, $moduleName);
                        unset($orderPosition);
                    }
                }

                $this->addChargesAndDiscountsForProducts($shoppingCart);

                // Get nontaxable positions from registered modules
                $registeredModulesNonTaxablePositions = $shoppingCart->callMethodOnRegisteredModules(
                    'ShoppingCartPositions',
                    [
                        $shoppingCart,
                        $member,
                        false
                    ]
                );

                foreach ($registeredModulesNonTaxablePositions as $moduleName => $moduleOutput) {
                    foreach ($moduleOutput as $modulePosition) {
                        $orderPosition = OrderPosition::create();
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
                        $orderPosition = OrderPosition::create();
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
                        $orderPosition->Title               = (string) $chargeAndDiscountForTotal->Name;
                        $orderPosition->OrderID             = $this->ID;
                        $orderPosition->write();
                        unset($orderPosition);
                    }
                }

                // Convert positions of registered modules
                $shoppingCart->callMethodOnRegisteredModules(
                    'ShoppingCartConvert',
                    [
                        $shoppingCart,
                        $member,
                        $this
                    ]
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
     * @return Order
     */
    public function setPaymentMethod(int $paymentMethodID) : Order
    {
        $paymentMethod = PaymentMethod::get()->byID($paymentMethodID);
        if ($paymentMethod instanceof PaymentMethod) {
            $this->PaymentMethodID = $paymentMethod->ID;
            $paymentFee            = $paymentMethod->getHandlingCost();
            if ($paymentFee instanceof HandlingCost
             && $paymentFee->exists()
            ) {
                $this->TaxRatePayment   = $paymentFee->Tax()->getTaxRate();
                $this->TaxAmountPayment = $paymentFee->getTaxAmount();
                $this->HandlingCostPayment->setAmount($paymentFee->amount->getAmount());
                $this->HandlingCostPayment->setCurrency($paymentFee->amount->getCurrency());
            }
        }
        return $this;
    }

    /**
     * set payment status of $this
     *
     * @param PaymentStatus $paymentStatus the payment status object
     *
     * @return bool
     */
    public function setPaymentStatus($paymentStatus)
    {
        $paymentStatusSet = false;
        if ($paymentStatus instanceof PaymentStatus
         && $paymentStatus->exists()
        ) {
            $this->PaymentStatusID = $paymentStatus->ID;
            $this->write();
            $paymentStatusSet = true;
        }
        return $paymentStatusSet;
    }

    /**
     * set payment status of $this
     *
     * @param int $paymentStatusID the order status ID
     *
     * @return bool
     */
    public function setPaymentStatusByID($paymentStatusID)
    {
        $paymentStatusSet = false;
        if (PaymentStatus::get()->byID($paymentStatusID)->exists()) {
            $this->PaymentStatusID = $paymentStatusID;
            $this->write();
            $paymentStatusSet = true;
        }
        return $paymentStatusSet;
    }
    
    /**
     * Sets the payment status by the given $paymentStatusID. If there is no 
     * existing payment status, the default payment status will be set.
     * 
     * @param int $paymentStatusID Payment status ID
     * 
     * @return \SilverCart\Model\Order\Order
     */
    public function setPaymentStatusByIDOrDefault(int $paymentStatusID = 0) : Order
    {
        $paymentStatus = PaymentStatus::get()->byID($paymentStatusID);
        if (!($paymentStatus instanceof PaymentStatus)
         || !$paymentStatus->exists()) {
            $paymentStatus = PaymentStatus::get_default();
        }
        if ($paymentStatus instanceof PaymentStatus
         && $paymentStatus->exists()
        ) {
            $this->PaymentStatusID = $paymentStatus->ID;
        }
        return $this;
    }
    
    /**
     * Returns whether this order has the payment status open.
     * 
     * @return bool
     */
    public function isPaymentStatusOpen() : bool
    {
        return $this->PaymentStatus()->Code === PaymentStatus::STATUS_CODE_OPEN;
    }

    /**
     * set status of $this
     *
     * @param OrderStatus $orderStatus the order status object
     *
     * @return bool
     */
    public function setOrderStatus($orderStatus)
    {
        $orderStatusSet = false;

        if ($orderStatus instanceof OrderStatus
         && $orderStatus->exists()
        ) {
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
    public function setOrderStatusByID($orderStatusID)
    {
        $orderStatusSet = false;

        if (OrderStatus::get()->byID($orderStatusID)->exists()) {
            $this->OrderStatusID = $orderStatusID;
            $this->write();
            $orderStatusSet = true;
        }

        return $orderStatusSet;
    }

    /**
     * Set status by the given $orderStatusCode.
     *
     * @param string $orderStatusCode The order status code
     *
     * @return bool
     */
    public function setOrderStatusByCode(string $orderStatusCode) : bool
    {
        $changed = false;
        $status  = OrderStatus::get_by_code($orderStatusCode);
        if ($status instanceof OrderStatus
         && $status->exists()
        ) {
            $this->OrderStatusID = $status->ID;
            $this->write();
            $changed = true;
        }
        return $changed;
    }
    
    /**
     * Sets the order status by the given $orderStatusID. If there is no existing 
     * order status, the default order status will be set.
     * 
     * @param int $orderStatusID Order status ID
     * 
     * @return \SilverCart\Model\Order\Order
     */
    public function setOrderStatusByIDOrDefault(int $orderStatusID = 0) : Order
    {
        $orderStatus = OrderStatus::get()->byID($orderStatusID);
        if (!($orderStatus instanceof OrderStatus)
         || !$orderStatus->exists()
        ) {
            $orderStatus = OrderStatus::get_default();
        }
        if ($orderStatus instanceof OrderStatus
         && $orderStatus->exists()
        ) {
            $this->OrderStatusID = $orderStatus->ID;
        }
        return $this;
    }

    /**
     * Save the note from the form if there is one
     *
     * @param string $note the customers notice
     *
     * @return $this
     */
    public function setNote($note)
    {
        $this->setField('Note', $note);
        return $this;
    }

    /**
     * Returns the formatted note.
     *
     * @return string
     */
    public function getFormattedNote()
    {
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
     * @return $this
     */
    public function setWeight()
    {
        $member = Customer::currentUser();
        if ($member instanceof Member
         && $member->getCart()->getWeightTotal()
        ) {
            $this->WeightTotal = $member->getCart()->getWeightTotal();
            $this->WeightUnit  = Config::getConfig()->WeightUnit;
        }
        return $this;
    }

    /**
     * set the total price for this order
     *
     * @return $this
     */
    public function setAmountTotal()
    {
        $member = Customer::currentUser();
        if ($member
         && $member->getCart()
        ) {
            $this->AmountTotal = $member->getCart()->getAmountTotal();
        }
        return $this;
    }

    /**
     * set the email for this order
     *
     * @param string $email the email address of the customer
     *
     * @return $this
     */
    public function setCustomerEmail($email = null)
    {
        $member = Customer::currentUser();
        if ($email === null
         && $member instanceof Member
         && $member->Email
        ) {
            $email = $member->Email;
        }
        $this->CustomersEmail = $email;
        return $this;
    }
    
    /**
     * Set the status of the revocation instructions checkbox field.
     *
     * @param boolean $status The status of the field
     * 
     * @return $this
     */
    public function setHasAcceptedRevocationInstruction($status)
    {
        $this->HasAcceptedRevocationInstruction = $status;
        return $this;
    }
    
    /**
     * Set the status of the terms and conditions checkbox field.
     *
     * @param boolean $status The status of the field
     * 
     * @return $this
     */
    public function setHasAcceptedTermsAndConditions($status)
    {
        $this->HasAcceptedTermsAndConditions = $status;
        return $this;
    }

    /**
     * The shipping method is a relation + an attribte of the order
     *
     * @param int $shippingMethodID the ID of the shipping method
     *
     * @return Order
     */
    public function setShippingMethod(int $shippingMethodID) : Order
    {
        $selectedShippingMethod = ShippingMethod::get()->byID($shippingMethodID);
        if ($selectedShippingMethod instanceof ShippingMethod
         && $selectedShippingMethod->getShippingFee() instanceof ShippingFee
        ) {
            $shippingFee = $selectedShippingMethod->getShippingFee();
            $this->ShippingMethodID    = $selectedShippingMethod->ID;
            $this->ShippingFeeID       = $shippingFee->ID;
            $this->TaxRateShipment     = $shippingFee->getTaxRate();
            $this->TaxAmountShipment   = $shippingFee->getTaxAmount();
            $this->HandlingCostShipment->setAmount($shippingFee->getPriceAmount());
            $this->HandlingCostShipment->setCurrency(Config::DefaultCurrency());
        }
        return $this;
    }

    /**
     * returns tax included in $this
     *
     * @return float
     */
    public function getTax()
    {
        $tax = 0.0;

        foreach ($this->OrderPositions() as $orderPosition) {
            $tax += $orderPosition->TaxTotal;
        }

        $taxObj = DBMoney::create('Tax');
        $taxObj->setAmount($tax);
        $taxObj->setCurrency(Config::DefaultCurrency());

        return $taxObj;
    }

    /**
     * returns bills currency
     * 
     * @return string
     */
    public function getCurrency()
    {
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
    public function getPositionsAsString($asHtmlString = false, $withAmountTotal = false)
    {
        if ($asHtmlString) {
            $seperator = '<br/>';
        } else {
            $seperator = PHP_EOL;
        }
        $positionsStrings = [];
        foreach ($this->OrderPositions() as $position) {
            $positionsString = $position->getTypeSafeQuantity() . 'x #' . $position->ProductNumber . ' "' . $position->Title . '" ' . $position->getPriceTotalNice();
            $positionsStrings[] = $positionsString;
        }
        $positionsAsString = implode($seperator . '------------------------' . $seperator, $positionsStrings);
        if ($withAmountTotal) {
            $shipmentAndPayment = DBMoney::create();
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
    public function getPositionsPriceGross()
    {
        $positionsPriceGross = $this->AmountTotal->getAmount() - ($this->HandlingCostShipment->getAmount() + $this->HandlingCostPayment->getAmount());

        $positionsPriceGrossObj = DBMoney::create();
        $positionsPriceGrossObj->setAmount($positionsPriceGross);
        $positionsPriceGrossObj->setCurrency(Config::DefaultCurrency());
        
        return $positionsPriceGrossObj;
    }

    /**
     * Returns the net amount of all order positions.
     *
     * @return DBMoney
     */
    public function getPositionsPriceNet()
    {
        $priceNet = $this->getPositionsPriceGross()->getAmount() - $this->getTax(true,true,true)->getAmount();

        $priceNetObj = DBMoney::create();
        $priceNetObj->setAmount($priceNet);
        $priceNetObj->setCurrency(Config::DefaultCurrency());
        
        return $priceNetObj;
    }

    /**
     * Returns the gross amount of the order.
     *
     * @return DBMoney
     */
    public function getPriceGross()
    {
        return $this->AmountTotal;
    }
    
    /**
     * Returns all order positions without a tax value.
     * 
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.09.2018
     */
    public function OrderPositionsWithoutTax()
    {
        $orderPositions = ArrayList::create();
        
        foreach ($this->OrderPositions() as $orderPosition) {
            if (!$orderPosition->isChargeOrDiscount
             && $orderPosition->TaxRate == 0
            ) {
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.09.2018
     */
    public function OrderIncludedInTotalPositions()
    {
        $positions = ArrayList::create();

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
    public function OrderListPositions()
    {
        $orderPositions = ArrayList::create();
        
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.09.2018
     */
    public function OrderChargePositionsTotal() {
        $chargePositions = ArrayList::create();
        
        foreach ($this->OrderPositions() as $orderPosition) {
            if ($orderPosition->isChargeOrDiscount
             && $orderPosition->chargeOrDiscountModificationImpact == 'totalValue'
            ) {
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.09.2018
     */
    public function OrderChargePositionsProduct()
    {
        $chargePositions = ArrayList::create();
        
        foreach ($this->OrderPositions() as $orderPosition) {
            if ($orderPosition->isChargeOrDiscount
             && $orderPosition->chargeOrDiscountModificationImpact == 'productValue'
            ) {
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
    public function getTaxableAmountWithoutFeesNice($includeChargesForProducts = false, $includeChargesForTotal = false)
    {
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
    public function getTaxableAmountWithoutFees($includeChargesForProducts = false, $includeChargesForTotal = false)
    {
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
    public function getTaxableAmountGrossWithoutFees($includeChargesForProducts = false, $includeChargesForTotal = false)
    {
        $priceGross = DBMoney::create();
        $priceGross->setAmount(0);
        $priceGross->setCurrency(Config::DefaultCurrency());
        
        if ($includeChargesForTotal == 'false') {
            $includeChargesForTotal = false;
        }
        if ($includeChargesForProducts == 'false') {
            $includeChargesForProducts = false;
        }
        
        foreach ($this->OrderPositions() as $position) {
            if ((!$includeChargesForProducts
              && $position->isChargeOrDiscount
              && $position->chargeOrDiscountModificationImpact == 'productValue')
             || (!$includeChargesForTotal
              && $position->isChargeOrDiscount
              && $position->chargeOrDiscountModificationImpact == 'totalValue')
            ) {
                continue;
            }
            
            if ($position->TaxRate > 0
             || $position->IsNonTaxable
            ) {
                $priceGross->setAmount($priceGross->getAmount() + $position->PriceTotal->getAmount());
            }
        }
        
        return DataObject::create(['Amount' => $priceGross]);
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
    public function getTaxableAmountNetWithoutFees($includeChargesForProducts = false, $includeChargesForTotal = false)
    {
        $priceNet = DBMoney::create();
        $priceNet->setAmount(0);
        $priceNet->setCurrency(Config::DefaultCurrency());
        
        if ($includeChargesForTotal == 'false') {
            $includeChargesForTotal = false;
        }
        if ($includeChargesForProducts == 'false') {
            $includeChargesForProducts = false;
        }
        
        foreach ($this->OrderPositions() as $position) {
            if ((!$includeChargesForProducts
              && $position->isChargeOrDiscount
              && $position->chargeOrDiscountModificationImpact == 'productValue')
             || (!$includeChargesForTotal
              && $position->isChargeOrDiscount
              && $position->chargeOrDiscountModificationImpact == 'totalValue')
            ) {
                continue;
            }
            
            if ($position->TaxRate > 0
             || $position->IsNonTaxable
            ) {
                $priceNet->setAmount($priceNet->getAmount() + $position->PriceTotal->getAmount());
            }
        }
        
        return DataObject::create(['Amount' => $priceNet]);
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
    public function getTaxableAmountWithFeesNice($includeChargesForProducts = false, $includeChargesForTotal = false)
    {
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
    public function getTaxableAmountWithFees($includeChargesForProducts = false, $includeChargesForTotal = false)
    {
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
    public function getTaxableAmountGrossWithFees($includeChargesForProducts = false, $includeChargesForTotal = false)
    {
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
        
        return DataObject::create(['Amount' => $priceGross]);
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
    public function getTaxableAmountNetWithFees($includeChargesForProducts = false, $includeChargesForTotal = false)
    {
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
        
        return DataObject::create(['Amount' => $priceGross]);
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
    public function getTaxRatesWithoutFees($includeChargesForProducts = false, $includeChargesForTotal = false)
    {
        if ($includeChargesForTotal === 'false') {
            $includeChargesForTotal = false;
        }
        if ($includeChargesForProducts === 'false') {
            $includeChargesForProducts = false;
        }
        
        $taxes = ArrayList::create();
        
        foreach ($this->OrderPositions() as $orderPosition) {
            if ((!$includeChargesForProducts
              && $orderPosition->isChargeOrDiscount
              && $orderPosition->chargeOrDiscountModificationImpact == 'productValue')
             || (!$includeChargesForTotal
              && $orderPosition->isChargeOrDiscount
              && $orderPosition->chargeOrDiscountModificationImpact == 'totalValue')
            ) {
                continue;
            }
            
            $taxRate = $orderPosition->TaxRate;
            if ($taxRate == '') {
                $taxRate = 0;
            }
            if ($taxRate >= 0
             && !$taxes->find('Rate', $taxRate)
            ) {
                $taxes->push(DataObject::create([
                    'Rate'      => $taxRate,
                    'AmountRaw' => 0.0,
                ]));
            }
            $taxSection = $taxes->find('Rate', $taxRate);
            $taxSection->AmountRaw += $orderPosition->TaxTotal;
        }

        foreach ($taxes as $tax) {
            $taxObj = DBMoney::create();
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
    public function getTaxTotal($excludeCharges = false)
    {
        $taxRates = $this->getTaxRatesWithFees(true, false);

        if (!$excludeCharges
         && $this->HasChargePositionsForTotal()
        ) {
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

        return $taxRates->exclude('AmountRaw', 0);
    }
    
    /**
     * Returns the tax total amount
     * 
     * @param bool $excludeCharges Exclude charges?
     * 
     * @return float
     */
    public function getTaxTotalAmount($excludeCharges = false)
    {
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
    public function getTaxRatesWithFees($includeChargesForProducts = false, $includeChargesForTotal = false)
    {
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
        if ($taxRateShipment >= 0
         && !$taxes->find('Rate', $taxRateShipment)
        ) {
            $taxes->push(DataObject::create([
                'Rate'      => $taxRateShipment,
                'AmountRaw' => 0.0,
            ]));
        }
        $taxSectionShipment = $taxes->find('Rate', $taxRateShipment);
        $taxSectionShipment->AmountRaw += $this->TaxAmountShipment;

        // Payment cost tax
        $taxRatePayment = $this->TaxRatePayment;
        if ($taxRatePayment == '') {
            $taxRatePayment = 0;
        }
        if ($taxRatePayment >= 0
         && !$taxes->find('Rate', $taxRatePayment)
        ) {
            $taxes->push(DataObject::create([
                'Rate'      => $taxRatePayment,
                'AmountRaw' => 0.0,
            ]));
        }
        $taxSectionPayment = $taxes->find('Rate', $taxRatePayment);
        $taxSectionPayment->AmountRaw += $this->TaxAmountPayment;

        foreach ($taxes as $tax) {
            $taxObj = DBMoney::create();
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
    public function getQuantity($productId = null)
    {
        $positions = $this->OrderPositions();
        $quantity = 0;

        foreach ($positions as $position) {
            if ($productId === null
             || $position->Product()->ID === $productId
            ) {
                $quantity += $position->Quantity;
            }
        }

        return $quantity;
    }
    
    /**
     * Returns extension injected order detail actions.
     *
     * @return DBHTMLText
     */
    public function OrderDetailActions() : DBHTMLText
    {
        $actions = '';
        $this->extend('updateOrderDetailActions', $actions);
        return DBHTMLText::create()->setValue($actions);
    }
    
    /**
     * Returns plugin output.
     *
     * @return DBHTMLText
     */
    public function OrderDetailInformation() : DBHTMLText
    {
        $orderDetailInformation = '';
        $this->extend('updateOrderDetailInformation', $orderDetailInformation);
        return DBHTMLText::create()->setValue($orderDetailInformation);
    }
    
    /**
     * Returns plugin output to show in the order detail table right after the 
     * OrderNumber.
     * 
     * <code>
     * // adding information:
     * public function updateOrderDetailInformationAfterOrderNumber($orderDetailInformation)
     * {
     *     $orderDetailInformation->push(ArrayData::create([
     *         'Title'     => $this->owner->fieldLabel('MyCustomField'),
     *         'Value'     => $this->owner->MyCustomField,
     *         'Highlight' => true, // optional: true or false (default: false)
     *         'Alignment' => 'left', // optional: 'left', 'center', 'right', 'justify' (default: 'left')
     *     ]));
     * }
     * </code>
     *
     * @return ArrayList
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2018
     */
    public function OrderDetailInformationAfterOrderNumber()
    {
        $orderDetailInformation = ArrayList::create();
        $this->extend('updateOrderDetailInformationAfterOrderNumber', $orderDetailInformation);
        foreach ($orderDetailInformation as $line) {
            if (empty($line->Highlight)) {
                $line->Highlight = false;
            }
            if (empty($line->Alignment)) {
                $line->Alignment = 'left';
            }
        }
        return $orderDetailInformation;
    }
    
    /**
     * Returns plugin output to show in the order detail table right after the 
     * OrderNumber.
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.09.2018
     */
    public function OrderDetailInformationHTMLAfterOrderNumber()
    {
        $orderDetailInformation = '';
        $this->extend('updateOrderDetailInformationHTMLAfterOrderNumber', $orderDetailInformation);
        return Tools::string2html($orderDetailInformation);
    }
    
    /**
     * Returns output to show in the order confirmation email right after the 
     * order detail table.
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2019
     */
    public function AfterOrderDetailTableEmailContent() : DBHTMLText
    {
        $content = '';
        $this->extend('updateAfterOrderDetailTableEmailContent', $content);
        return Tools::string2html($content);
    }

    /**
     * Returns the order positions, shipping method, payment method etc. as
     * HTML table.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function OrderDetailTable()
    {
        $viewableData = ViewableData::create();
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.09.2018
     */
    public function HasChargePositionsForProduct()
    {
        $hasChargePositionsForProduct = false;

        foreach ($this->OrderPositions() as $orderPosition) {
            if ($orderPosition->isChargeOrDiscount
             && $orderPosition->chargeOrDiscountModificationImpact == 'productValue'
            ) {
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.09.2018
     */
    public function HasChargePositionsForTotal()
    {
        $hasChargePositionsForTotal = false;

        foreach ($this->OrderPositions() as $orderPosition) {
            if ($orderPosition->isChargeOrDiscount
             && $orderPosition->chargeOrDiscountModificationImpact == 'totalValue'
            ) {
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.09.2018
     */
    public function HasIncludedInTotalPositions()
    {
        return (bool) $this->OrderIncludedInTotalPositions();
    }
    
    /**
     * Returns the i18n text for the price type
     *
     * @return string
     */
    public function getPriceTypeText()
    {
        return _t(Customer::class . '.PRICETYPE_' . strtoupper($this->PriceType), $this->PriceType);
    }

    /**
     * Indicates wether this order is gross calculated or not.
     * 
     * @return bool
     */
    public function IsPriceTypeGross() : bool
    {
        $isPriceTypeGross = $this->PriceType === Config::PRICE_TYPE_GROSS;
        $this->extend('updateIsPriceTypeGross', $isPriceTypeGross);
        return $isPriceTypeGross;
    }

    /**
     * Indicates wether this order is net calculated or not.
     * 
     * @return bool
     */
    public function IsPriceTypeNet() : bool
    {
        $isPriceTypeNet = $this->PriceType === Config::PRICE_TYPE_NET;
        $this->extend('updateIsPriceTypeNet', $isPriceTypeNet);
        return $isPriceTypeNet;
    }
    
    /**
     * Returns whether this order is a pickup order.
     * 
     * @return bool
     */
    public function IsPickup() : bool
    {
        return $this->ShippingMethod() instanceof ShippingMethod
            && $this->ShippingMethod()->isPickup;
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
    public function Log(string $context, string $text) : void
    {
        Tools::Log($context, $text, 'order');
    }

    /**
     * Send a confirmation mail with order details to the customer. If 
     * $sendNotification is set to true, the notification email will also be
     * sent to the shop owner.
     * 
     * @param bool $sendNotification Also send the notification email?
     *
     * @return \SilverCart\Model\Order\Order
     */
    public function sendConfirmationMail(bool $sendNotification = true) : Order
    {
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
            $params['OrderConfirmation']['Attachments'],
            $this->Member()->Locale
        );
        if ($sendNotification) {
            $this->sendNotificationMail($params);
        }
        $this->extend('onAfterConfirmationMail', $params);
        return $this;
    }

    /**
     * Send a notification mail with order details to the shop owner.
     *
     * @return \SilverCart\Model\Order\Order
     */
    public function sendNotificationMail(array $params = []) : Order
    {
        $this->extend('onBeforeNotificationMail');
        if (empty($params)) {
            $params = [
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
        }
        $this->extend('updateNotificationMail', $params);
        ShopEmail::send(
            $params['OrderNotification']['Template'],
            $params['OrderNotification']['Recipient'],
            $params['OrderNotification']['Variables'],
            $params['OrderNotification']['Attachments'],
            Tools::default_locale()->getLocale()
        );
        $this->extend('onAfterNotificationMail', $params);
        return $this;
    }
    
    /**
     * Returns whether this order has positions with order email text.
     * 
     * @return bool
     */
    public function HasPositionsWithOrderEmailText() : bool
    {
        return $this->PositionsWithOrderEmailText()->exists();
    }
    
    /**
     * Returns all positions with order email text.
     * 
     * @return DataList
     */
    public function PositionsWithOrderEmailText() : DataList
    {
        return $this->OrderPositions()
                ->exclude('Product.ProductTranslations.OrderEmailText', ['', null]);
    }
    
    /**
     * Returns whether this order has positions with order email text to show on 
     * order confirmation page.
     * 
     * @return bool
     */
    public function HasPositionsWithOrderConfirmationPageText() : bool
    {
        return $this->PositionsWithOrderConfirmationPageText()->exists();
    }
    
    /**
     * Returns all positions with order email text to show on order confirmation 
     * page.
     * 
     * @return DataList
     */
    public function PositionsWithOrderConfirmationPageText() : DataList
    {
        return $this->OrderPositions()
                ->filter('Product.ShowOrderEmailTextAfterCheckout', true);
    }

    /**
     * Set a new/reserved ordernumber before writing and send attributed
     * ShopEmails.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    protected function onBeforeWrite() : void
    {
        parent::onBeforeWrite();
        if (empty ($this->OrderNumber)) {
            $this->OrderNumber = NumberRange::useReservedNumberByIdentifier('OrderNumber');
        }
        $this->handleCancelOnBeforeWrite();
        $this->handleTrackingCodeChange();
        $this->handleOrderStatusChange();
        $this->handlePaymentStatusChange();
        if (array_key_exists('sa__FirstName', $_POST)
         && $this->ShippingAddress()->ID > 0
        ) {
            foreach ($_POST as $paramName => $paramValue) {
                if (strpos($paramName, 'sa__') === 0) {
                    $addressParamName = str_replace('sa__', '', $paramName);
                    $this->ShippingAddress()->{$addressParamName} = $paramValue;
                }
            }
            $this->ShippingAddress()->write();
        }
        if (array_key_exists('ia__FirstName', $_POST)
         && $this->InvoiceAddress()->ID > 0
        ) {
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
     * On before delete.
     * 
     * @return void
     */
    protected function onBeforeDelete() : void
    {
        parent::onBeforeDelete();
        if ($this->ShippingAddress()->exists()) {
            $this->ShippingAddress()->delete();
        }
        if ($this->InvoiceAddress()->exists()) {
            $this->InvoiceAddress()->delete();
        }
        foreach ($this->OrderPositions() as $position) {
            /* @var $position OrderPosition */
            $position->delete();
        }
        foreach ($this->OrderLogs() as $log) {
            /* @var $log OrderLog */
            $log->delete();
        }
    }
    
    /**
     * Handles the cancel process before writing an order.
     * 
     * @return void
     */
    protected function handleCancelOnBeforeWrite() : void
    {
        if (!$this->cancelInProgress
         && $this->isChanged('OrderStatusID')
         && $this->OrderStatus()->Code === OrderStatus::STATUS_CODE_CANCELED
        ) {
            // order is canceled
            $this->cancel(false);
        }
    }

    /**
     * Cancels the order.
     * 
     * @param bool $doWrite Write order?
     * 
     * @return \SilverCart\Model\Order\Order
     */
    public function cancel(bool $doWrite = true) : Order
    {
        $this->cancelInProgress = true;
        $this->extend('onBeforeCancel');
        if ($this->OrderStatus()->Code !== OrderStatus::STATUS_CODE_CANCELED) {
            $status              = OrderStatus::get_by_code(OrderStatus::STATUS_CODE_CANCELED);
            $this->OrderStatusID = $status->ID;
        }
        if ($this->PaymentStatus()->Code !== PaymentStatus::STATUS_CODE_CANCELED) {
            $status                = PaymentStatus::get_by_code(PaymentStatus::STATUS_CODE_CANCELED);
            $this->PaymentStatusID = $status->ID;
        }
        $this->extend('updateCancel');
        if ($doWrite) {
            $this->write();
        }
        $this->extend('onAfterCancel');
        return $this;
    }
    
    /**
     * Handles a tracking code change.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.09.2018
     */
    public function handleTrackingCodeChange() : void
    {
        if (!empty($this->TrackingCode)
         && empty($this->TrackingLink)
         && !empty($this->ShippingMethod()->Carrier()->TrackingLinkBase)
        ) {
            $this->extend('onBeforeTrackingCodeChange');
            $this->TrackingLink = str_replace('{TrackingCode}', $this->TrackingCode, $this->ShippingMethod()->Carrier()->TrackingLinkBase);
            if (strpos($this->TrackingLink, $this->TrackingCode) === false) {
                $this->TrackingLink .= $this->TrackingCode;
            }
            if (!$this->isChanged('OrderStatusID')
             && $this->isChanged('TrackingCode')
             && $this->OrderStatus()->Code === OrderStatus::STATUS_CODE_SHIPPED
            ) {
                $this->sendTrackingInformationEmail();
            }
            $this->extend('onAfterTrackingCodeChange');
        }
    }
    
    /**
     * Sends a tracking information email to the customer.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.09.2018
     */
    public function sendTrackingInformationEmail() : void
    {
        ShopEmail::send(
            'OrderTrackingNotification',
            $this->CustomersEmail,
            [
                'Order'             => $this,
                'OrderNumber'       => $this->OrderNumber,
                'CustomersEmail'    => $this->CustomersEmail,
                'FirstName'         => $this->InvoiceAddress()->FirstName,
                'Surname'           => $this->InvoiceAddress()->Surname,
                'Salutation'        => $this->InvoiceAddress()->Salutation,
                'SalutationText'    => $this->InvoiceAddress()->SalutationText,
            ]
        );
    }
    
    /**
     * Handles an order status change.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function handleOrderStatusChange() : void
    {
        if (!$this->didHandleOrderStatusChange
         && $this->exists()
         && $this->isChanged('OrderStatusID')
        ) {
            $changed = $this->getChangedFields('OrderStatusID')['OrderStatusID'];
            if ((int) $changed['before'] === (int) $changed['after']) {
                return;
            }
            $this->didHandleOrderStatusChange = true;
            $this->extend('onBeforeOrderStatusChange');
            if (method_exists($this->PaymentMethod(), 'handleOrderStatusChange')) {
                $this->PaymentMethod()->handleOrderStatusChange($this);
            }
            $newOrderStatus = OrderStatus::get()->byID($this->OrderStatusID);
            if ($newOrderStatus) {
                if ($newOrderStatus instanceof OrderStatus
                 && $newOrderStatus->Code === 'shipped'
                 && $this->ShippingDate == null
                ) {
                    $this->ShippingDate = date('Y-m-d');
                }
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
    }
    
    /**
     * Handles a payment status change.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function handlePaymentStatusChange() : void
    {
        if (!$this->didHandlePaymentStatusChange
         && $this->exists()
         && $this->isChanged('PaymentStatusID')
        ) {
            $changed = $this->getChangedFields('PaymentStatusID')['PaymentStatusID'];
            if ((int) $changed['before'] === (int) $changed['after']) {
                return;
            }
            $this->didHandlePaymentStatusChange = true;
            $this->extend('onBeforePaymentStatusChange');
            if (method_exists($this->PaymentMethod(), 'handlePaymentStatusChange')) {
                $this->PaymentMethod()->handlePaymentStatusChange($this);
            }
            $newPaymentStatus = PaymentStatus::get()->byID($this->PaymentStatusID);
            if ($newPaymentStatus instanceof PaymentStatus
             && $newPaymentStatus->Code === 'paid'
             && $this->PaymentDate == null
            ) {
                $this->PaymentDate = date('Y-m-d');
            }
            $paymentStatusID = 0;
            if (array_key_exists('PaymentStatusID', $this->original)) {
                $paymentStatusID = $this->original['PaymentStatusID'];
            }
            OrderLog::addChangedLog($this, PaymentStatus::class, $paymentStatusID, $this->PaymentStatusID);
        }
    }

    /**
     * hook triggered after write
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 07.09.2018
     */
    protected function onAfterWrite() : void
    {
        parent::onAfterWrite();
        $this->extend('updateOnAfterWrite');
        $this->didHandleOrderStatusChange   = false;
        $this->didHandlePaymentStatusChange = false;
    }

    /**
     * Returns an order by the given PaymentReferenceID.
     * 
     * @param string $paymentReferenceID Payment reference ID
     * 
     * @return Order|null
     */
    public static function get_by_payment_reference_id(string $paymentReferenceID) : ?Order
    {
        return Order::get()->filter('PaymentReferenceID', $paymentReferenceID)->first();
    }

    /**
     * Returns an order by the given Order ID and Member.
     * If no Member is given, the current logged in Member will be used as fallback.
     * 
     * @param int    $orderID  Order ID
     * @param Member $customer Customer
     * 
     * @return Order|null
     */
    public static function get_by_customer(int $orderID, Member $customer = null) : ?Order
    {
        $customerID = null;
        if ($customer instanceof Member
         && $customer->exists()
        ) {
            $customerID = (int) $customer->ID;
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
     * @return Order|null
     */
    public static function get_by_customer_id(int $orderID, int $customerID = null) : ?Order
    {
        if (is_null($customerID)) {
            $customer = Security::getCurrentUser();
            if ($customer instanceof Member) {
                $customerID = $customer->ID;
            }
        }
        return Order::get()->filter([
            'ID'       => $orderID,
            'MemberID' => $customerID,
        ])->first();
    }
    
    /**
     * Calculates the total amount of positions and handling cost.
     * 
     * @return float
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.2018
     */
    public function calculateAmountTotal() : float
    {
        $amountTotal = 0;
        foreach ($this->OrderPositions() as $orderPosition) {
            $results = $orderPosition->extend('skipCalculateAmountTotal');
            if (is_array($results)
             && count($results) > 0
             && max($results) === true
            ) {
                continue;
            }
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.07.2018
     */
    public function recalculate() : void
    {
        $this->AmountTotal->setAmount($this->calculateAmountTotal());
        $this->extend('recalculate');
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
    public function ShippingMethod() : ?ShippingMethod
    {
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
    public function getAmountTotalNice()
    {
        return $this->AmountTotal->Nice();
    }

    /**
     * returns the orders total amount as string incl. currency.
     *
     * @return string
     */
    public function getHandlingCostShipmentNice() : string
    {
        return str_replace('.', ',', number_format($this->HandlingCostShipmentAmount, 2)) . ' ' . $this->HandlingCostShipmentCurrency;
    }

    /**
     * returns the orders total amount as string incl. currency.
     *
     * @return string
     */
    public function getHandlingCostPaymentNice() : string
    {
        return str_replace('.', ',', number_format($this->HandlingCostPaymentAmount, 2)) . ' ' . $this->HandlingCostPaymentCurrency;
    }

    /**
     * returns the orders total amount as string incl. currency.
     *
     * @return DBMoney
     */
    public function getHandlingCost() : DBMoney
    {
        $amount   = $this->HandlingCostShipment->getAmount() + $this->HandlingCostPayment->getAmount();
        $currency = $this->HandlingCostShipment->Currency;
        return DBMoney::create()->setAmount($amount)->setCurrency($currency);
    }
    
    /**
     * Marks the order as seen
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function markAsSeen() : void
    {
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
    public function markAsNotSeen() : void
    {
        if ($this->IsSeen) {
            $this->IsSeen = false;
            $this->write();
            OrderLog::addMarkedAsNotSeenLog($this, Order::class);
        }
    }
    
    /**
     * Returns the link to show this complaint.
     * 
     * @return string
     */
    public function Link() : string
    {
        $link        = '';
        $orderHolder = SilverCartPage::PageByIdentifierCode(SilverCartPage::IDENTIFIER_ORDER_HOLDER);
        if ($orderHolder instanceof SilverCartPage) {
            $link = $orderHolder->Link("detail/{$this->ID}");
        }
        return $link;
    }
    
    /**
     * Renders the order with the default template.
     * 
     * @return DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function render() : DBHTMLText
    {
        return $this->renderWith(Order::class);
    }
    
    /**
     * Returns the rendered position.
     * 
     * @param string $templateAddition Optional template name addition
     * 
     * @return DBHTMLText
     */
    public function forTemplate(string $templateAddition = '') : DBHTMLText
    {
        $addition = empty($templateAddition) ? '' : "_{$templateAddition}";
        return $this->renderWith(static::class . $addition);
    }
}