<?php

namespace SilverCart\Model\Order;

use SilverCart\Dev\Tools;
use SilverCart\Extensions\Model\BadgeColorExtension;
use SilverCart\Model\ShopEmail;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Order\OrderStatusTranslation;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\Filters\PartialMatchFilter;

/**
 * abstract for an order status.
 *
 * @package SilverCart
 * @subpackage Model_Order
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $Code       Status code
 * @property bool   $IsDefault  Determines whether this status is the default status
 * @property string $badgeColor Badge Color
 * 
 * @property string $IsDefaultString Human readable string to display the default status
 * @property string $Title           Status title
 * 
 * @method \SilverStripe\ORM\HasManyList Orders()                  Returns the related orders.
 * @method \SilverStripe\ORM\HasManyList OrderStatusTranslations() Returns the related translations.
 * 
 * @method \SilverStripe\ORM\ManyManyList ShopEmails()                Returns the related shop emails.
 * @method \SilverStripe\ORM\ManyManyList PaymentMethodRestrictions() Returns the related PaymentMethodRestrictions.
 */
class OrderStatus extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    const STATUS_CODE_NEW                = 'new';
    const STATUS_CODE_CANCELED           = 'canceled';
    const STATUS_CODE_COMPLETED          = 'completed';
    const STATUS_CODE_INPROGRESS         = 'inprogress';
    const STATUS_CODE_SHIPPED            = 'shipped';
    const STATUS_CODE_PREPARING_SHIPMENT = 'preparing-shipment';
    /**
     * Returns the default OrderStatus.
     * 
     * @param bool $withFallback If true, a default will be written if not exists
     * 
     * @return OrderStatus
     */
    public static function get_default(bool $withFallback = true) : ?OrderStatus
    {
        $default = self::get()->filter('IsDefault', true)->first();
        if ($withFallback
         && (!($default instanceof OrderStatus)
          || !$default->exists())
        ) {
            $default = self::get()->first();
            $default->write();
        }
        return $default;
    }
    
    /**
     * Returns the order status with the given $code.
     * 
     * @param string $code Code to get status for
     * 
     * @return OrderStatus|null
     */
    public static function get_by_code(string $code) : ?OrderStatus
    {
        return self::get()->filter('Code', $code)->first();
    }
    
    /**
     * attributes
     *
     * @var array
     */
    private static $db = [
        'Code'      => 'Varchar',
        'IsDefault' => 'Boolean',
    ];
    /**
     * 1:n relations
     *
     * @var array
     */
    private static $has_many = [
        'Orders'                  => Order::class,
        'OrderStatusTranslations' => OrderStatusTranslation::class,
    ];
    /**
     * n:m relations
     *
     * @var array
     */
    private static $many_many = [
        'ShopEmails' => ShopEmail::class,
    ];
    /**
     * n:m relations
     *
     * @var array
     */
    private static $belongs_many_many = [
        'PaymentMethodRestrictions' => PaymentMethod::class,
    ];
    /**
     * Castings
     *
     * @var array 
     */
    private static $casting = [
        'IsDefaultString' => 'Varchar',
        'Title'           => 'Varchar',
    ];
    /**
     * Default sort
     *
     * @var string 
     */
    private static $default_sort = "SilvercartOrderStatusTranslation.Title";
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartOrderStatus';
    /**
     * Grant API access on this item.
     *
     * @var bool
     */
    private static $api_access = true;
    /**
     * List of extensions to use.
     *
     * @var array
     */
    private static $extensions = [
        BadgeColorExtension::class,
    ];
    /**
     * Default status codes
     *
     * @var array 
     */
    private static $default_codes = [
        self::STATUS_CODE_NEW,
        self::STATUS_CODE_CANCELED,
        self::STATUS_CODE_COMPLETED,
        self::STATUS_CODE_INPROGRESS,
        self::STATUS_CODE_SHIPPED,
        self::STATUS_CODE_PREPARING_SHIPMENT,
    ];

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
     * retirieves title from related language class depending on the set locale
     * Title is a very common attribute and is therefore located in the decorator
     *
     * @return string 
     */
    public function getTitle() : string
    {
        return (string) $this->getTranslationFieldValue('Title');
    }

    /**
     * Returns the field labels.
     * 
     * @param bool $includerelations include relations?
     * 
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'Title'                         => Page::singleton()->fieldLabel('Title'),
            'Code'                          => _t(OrderStatus::class . '.CODE', 'Code'),
            'Orders'                        => Order::singleton()->plural_name(),
            'PaymentMethodRestrictions'     => PaymentMethod::singleton()->plural_name(),
            'OrderStatusTranslations'       => OrderStatusTranslation::singleton()->plural_name(),
            'ShopEmailsTab'                 => _t(OrderStatus::class . '.ATTRIBUTED_SHOPEMAILS_LABEL_TITLE', 'Attributed emails'),
            'ShopEmailLabelField'           => _t(OrderStatus::class . '.ATTRIBUTED_SHOPEMAILS_LABEL_DESC', 'The following checked emails get sent when this order status is set for an order:'),
            'ShopEmails'                    => _t(ShopEmail::class . '.PLURALNAME', 'Shop Emails'),
            'OrderStatusTranslations.Title' => Page::singleton()->fieldLabel('Title'),
            'DefaultStatusNew'              => _t(self::class . '.DefaultStatusNew', 'New'),
            'DefaultStatusCanceled'         => _t(self::class . '.DefaultStatusCanceled', 'Canceled'),
            'DefaultStatusCompleted'        => _t(self::class . '.DefaultStatusCompleted', 'Completed'),
            'DefaultStatusInprogress'       => _t(self::class . '.DefaultStatusInprogress', 'In Progress'),
            'DefaultStatusShipped'          => _t(self::class . '.DefaultStatusShipped', 'Shipped'),
        ]);
    }
    
    /**
     * Returns an array of field/relation names (db, has_one, has_many, 
     * many_many, belongs_many_many) to exclude from form scaffolding in
     * backend.
     * This is a performance friendly way to exclude fields.
     * 
     * @return array
     */
    public function excludeFromScaffolding() : array
    {
        $excludeFromScaffolding = [
            'Orders'
        ];
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }

    /**
     * remove attribute Code from the CMS fields
     *
     * @return FieldList all CMS fields related
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $shopEmailLabelField = LiteralField::create(
                'ShopEmailLabelField',
                sprintf(
                    "<br /><p>%s</p>",
                    $this->fieldLabel('ShopEmailLabelField')
                )
            );
            $fields->addFieldToTab('Root.ShopEmails', $shopEmailLabelField, 'ShopEmails');
        });
        return DataObjectExtension::getCMSFields($this);
    }
    
    /**
     * HAndles a default status change on before write.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    protected function onBeforeWrite() : void
    {
        parent::onBeforeWrite();
        $defaultStatus = self::get_default(false);
        if (!$defaultStatus) {
            $defaultStatus = $this;
            $this->IsDefault = true;
        } elseif ($this->IsDefault &&
                  $defaultStatus->ID != $this->ID) {
            $defaultStatus->IsDefault = false;
            $defaultStatus->write();
        }
    }
    
    /**
     * Sends a mail with the given Order object as data provider.
     * 
     * @param Order $order The order object that is used to fill the
     *                               mail template variables.
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.10.2011
     */
    public function sendMailFor(Order $order) : void
    {
        $shopEmails = $this->ShopEmails();
        if ($shopEmails) {
            foreach ($shopEmails as $shopEmail) {
                ShopEmail::send(
                        $shopEmail->TemplateName,
                        (string) $order->CustomersEmail,
                        [
                            'Order'             => $order,
                            'OrderNumber'       => $order->OrderNumber,
                            'CustomersEmail'    => $order->CustomersEmail,
                            'FirstName'         => $order->InvoiceAddress()->FirstName,
                            'Surname'           => $order->InvoiceAddress()->Surname,
                            'Salutation'        => $order->InvoiceAddress()->Salutation,
                            'SalutationText'    => $order->InvoiceAddress()->SalutationText,
                        ],
                        [],
                        $order->Member()->Locale
                );
            }
        }
        $this->extend('updateSendMailFor', $order);
    }

    /**
     * returns array with StatusCode => StatusText
     *
     * @return DataList
     */
    public static function getStatusList() : \SilverStripe\ORM\DataList
    {
        return OrderStatus::get();
    }
    
    /**
     * Adds the default payment status.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.09.2018
     */
    public function requireDefaultRecords() : void
    {
        $this->beforeRequireDefaultRecords(function() {
            foreach (self::config()->get('default_codes') as $default) {
                $existing = self::get()->filter('Code', $default)->first();
                if ($existing instanceof OrderStatus
                 && $existing->exists())
                {
                    continue;
                }
                $status = self::create();
                $status->Code      = $default;
                $status->Title     = $this->fieldLabel('DefaultStatus' . ucfirst($default));
                $status->IsDefault = $default == 'new';
                $status->write();
            }
        });
        parent::requireDefaultRecords();
    }
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     */
    public function summaryFields() : array
    {
        $summaryFields = [
            'Code'            => $this->fieldLabel('Code'),
            'Title'           => $this->fieldLabel('Title'),
            'IsDefaultString' => $this->fieldLabel('IsDefault'),
        ];
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }

    /**
     * Searchable fields.
     *
     * @return array
     */
    public function  searchableFields() : array
    {
        return [
            'OrderStatusTranslations.Title' => [
                'title'  => $this->fieldLabel('Title'),
                'filter' => PartialMatchFilter::class,
            ],
            'Code' => [
                'title'  => $this->fieldLabel('Code'),
                'filter' => PartialMatchFilter::class,
            ],
        ];
    }

    /**
     * Casting to get the IsDefault state as a readable string
     *
     * @return string
     */
    public function getIsDefaultString() : string
    {
        return $this->IsDefault ? Tools::field_label('Yes') : Tools::field_label('No');
    }
}