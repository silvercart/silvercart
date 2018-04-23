<?php

namespace SilverCart\Model\Order;

use SilverCart\Dev\Tools;
use SilverCart\Model\ShopEmail;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Order\OrderStatusTranslation;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\ORM\DataObjectExtension;
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
class OrderStatus extends DataObject {

    /**
     * attributes
     *
     * @var array
     */
    private static $db = array(
        'Code' => 'Varchar'
    );

    /**
     * 1:n relations
     *
     * @var array
     */
    private static $has_many = array(
        'Orders'                  => Order::class,
        'OrderStatusTranslations' => OrderStatusTranslation::class,
    );

    /**
     * n:m relations
     *
     * @var array
     */
    private static $many_many = array(
        'ShopEmails'  => ShopEmail::class,
    );

    /**
     * n:m relations
     *
     * @var array
     */
    private static $belongs_many_many = array(
        'PaymentMethodRestrictions' => PaymentMethod::class,
    );
    
    /**
     * Castings
     *
     * @var array 
     */
    private static $casting = array(
        'Title' => 'Varchar(255)'
    );
    
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
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.09.2017
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.09.2017
     */
    public function plural_name() {
        return Tools::plural_name_for($this);
    }  
    
    /**
     * retirieves title from related language class depending on the set locale
     * Title is a very common attribute and is therefore located in the decorator
     *
     * @return string 
     */
    public function getTitle() {
        return $this->getTranslationFieldValue('Title');
    }

    /**
     * Get any user defined searchable fields labels that
     * exist. Allows overriding of default field names in the form
     * interface actually presented to the user.
     *
     * The reason for keeping this separate from searchable_fields,
     * which would be a logical place for this functionality, is to
     * avoid bloating and complicating the configuration array. Currently
     * much of this system is based on sensible defaults, and this property
     * would generally only be set in the case of more complex relationships
     * between data object being required in the search interface.
     *
     * Generates labels based on name of the field itself, if no static property
     * {@link self::field_labels} exists.
     *
     * @param boolean $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array|string Array of all element labels if no argument given, otherwise the label of the field
     *
     * @uses $field_labels
     * @uses FormField::name_to_label()
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.01.2014
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Title'                       => Page::singleton()->fieldLabel('Title'),
                'BadgeColor'                  => _t(OrderStatus::class . '.BADGECOLOR', 'Color code'),
                'Code'                        => _t(OrderStatus::class . '.CODE', 'Code'),
                'Orders'                      => Order::singleton()->plural_name(),
                'PaymentMethodRestrictions'   => PaymentMethod::singleton()->plural_name(),
                'OrderStatusTranslations'     => OrderStatusTranslation::singleton()->plural_name(),
                'ShopEmailsTab'               => _t(OrderStatus::class . '.ATTRIBUTED_SHOPEMAILS_LABEL_TITLE', 'Attributed emails'),
                'ShopEmailLabelField'         => _t(OrderStatus::class . '.ATTRIBUTED_SHOPEMAILS_LABEL_DESC', 'The following checked emails get sent when this order status is set for an order:'),
                'ShopEmails'                  => _t(ShopEmail::class . '.PLURALNAME', 'Shop Emails'),
                'OrderStatusTranslations.Title'  => Page::singleton()->fieldLabel('Title'),
            )
        );
        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
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
    public function excludeFromScaffolding() {
        $excludeFromScaffolding = array(
            'Orders'
        );
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }

    /**
     * remove attribute Code from the CMS fields
     *
     * @return FieldList all CMS fields related
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.07.2012
     */
    public function getCMSFields() {
        $fields = DataObjectExtension::getCMSFields($this);

        // Add shop email field
        $shopEmailLabelField = new LiteralField(
            'ShopEmailLabelField',
            sprintf(
                "<br /><p>%s</p>",
                $this->fieldLabel('ShopEmailLabelField')
            )
        );
        $fields->addFieldToTab('Root.ShopEmails', $shopEmailLabelField, 'ShopEmails');

        $this->extend('updateCMSFields', $fields);

        return $fields;
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
    public function sendMailFor(Order $order) {
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
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.11.2010
     */
    public static function getStatusList() {
        $statusList = OrderStatus::get();

        return $statusList;
    }
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.07.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'Code'  => $this->fieldLabel('Code'),
            'Title' => $this->fieldLabel('Title'),
        );

        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }

    /**
     * Searchable fields.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.01.2014
     */
    public function  searchableFields() {
        return array(
            'OrderStatusTranslations.Title' => array(
                'title'     => $this->fieldLabel('Title'),
                'filter'    => PartialMatchFilter::class,
            ),
            'Code' => array(
                'title'     => $this->fieldLabel('Code'),
                'filter'    => PartialMatchFilter::class,
            ),
        );
    }
    
}