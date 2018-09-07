<?php

namespace SilverCart\Model\Order;

use SilverCart\Dev\Tools;
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
 */
class OrderStatus extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    /**
     * Returns the default OrderStatus.
     * 
     * @param bool $withFallback If true, a default will be written if not exists
     * 
     * @return OrderStatus
     */
    public static function get_default($withFallback = true) {
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
     * Default status codes
     *
     * @var array 
     */
    private static $default_codes = [
        'new',
        'canceled',
        'completed',
        'inprogress',
        'shipped',
    ];

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function singular_name()
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name()
    {
        return Tools::plural_name_for($this);
    }  
    
    /**
     * retirieves title from related language class depending on the set locale
     * Title is a very common attribute and is therefore located in the decorator
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->getTranslationFieldValue('Title');
    }

    /**
     * Returns the field labels.
     * 
     * @param bool $includerelations include relations?
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function fieldLabels($includerelations = true)
    {
        $this->beforeUpdateFieldLabels(function (&$labels) {
            $labels = array_merge(
                    $labels,
                    Tools::field_labels_for(self::class),
                    [
                        'Title'                         => Page::singleton()->fieldLabel('Title'),
                        'BadgeColor'                    => _t(OrderStatus::class . '.BADGECOLOR', 'Color code'),
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
                    ]
            );
        });
        return parent::fieldLabels($includerelations);
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
     * @since 10.02.2013
     */
    public function excludeFromScaffolding()
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
    public function getCMSFields()
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
    protected function onBeforeWrite()
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
    public function sendMailFor(Order $order)
    {
        $shopEmails = $this->ShopEmails();
        
        if ($shopEmails) {
            foreach ($shopEmails as $shopEmail) {
                ShopEmail::send(
                    $shopEmail->TemplateName,
                    $order->CustomersEmail,
                    [
                        'Order'             => $order,
                        'OrderNumber'       => $order->OrderNumber,
                        'CustomersEmail'    => $order->CustomersEmail,
                        'FirstName'         => $order->InvoiceAddress()->FirstName,
                        'Surname'           => $order->InvoiceAddress()->Surname,
                        'Salutation'        => $order->InvoiceAddress()->Salutation,
                        'SalutationText'    => $order->InvoiceAddress()->SalutationText,
                    ]
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
    public static function getStatusList()
    {
        return OrderStatus::get();
    }
    
    /**
     * Adds the default payment status.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function requireDefaultRecords()
    {
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
    }
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 07.09.2018
     */
    public function summaryFields()
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
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function  searchableFields()
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
    public function getIsDefaultString()
    {
        return $this->IsDefault ? Tools::field_label('Yes') : Tools::field_label('No');
    }
}