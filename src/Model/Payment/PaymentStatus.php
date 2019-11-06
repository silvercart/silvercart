<?php

namespace SilverCart\Model\Payment;

use SilverCart\Dev\Tools;
use SilverCart\Model\Order\Order;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;

/**
 * 
 * @package SilverCart
 * @subpackage Model_Payment
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 07.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $Code      Status code
 * @property bool   $IsDefault Status is default?
 * 
 * @property string $IsDefaultString self::$IsDefault as a human readable string
 * @property string $Title           Status title
 * 
 * @method \SilverStripe\ORM\HasManyList Orders()                    Returns the related orders.
 * @method \SilverStripe\ORM\HasManyList PaymentStatusTranslations() Returns the related translations.
 */
class PaymentStatus extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    const STATUS_CODE_OPEN = 'open';
    const STATUS_CODE_PAID = 'paid';
    
    /**
     * Returns the default PaymentStatus.
     * 
     * @param bool $withFallback If true, a default will be written if not exists
     * 
     * @return PaymentStatus
     */
    public static function get_default($withFallback = true) : PaymentStatus
    {
        $default = self::get()->filter('IsDefault', true)->first();
        if ($withFallback
         && (!($default instanceof PaymentStatus)
          || !$default->exists())
        ) {
            $default = self::get()->first();
            $default->write();
        }
        return $default;
    }
    
    /**
     * Returns the payment status with the given $code.
     * 
     * @param string $code Code to get status for
     * 
     * @return PaymentStatus|null
     */
    public static function get_by_code(string $code) : ?PaymentStatus
    {
        return self::get()->filter('Code', $code)->first();
    }
    
    /**
     * DB attributes.
     *
     * @var array
     */
    private static $db = [
        'Code'      => 'Varchar(16)',
        'IsDefault' => 'Boolean',
    ];
    /**
     * 1:n relations
     *
     * @var array
     */
    private static $has_many = [
        'Orders'                    => Order::class,
        'PaymentStatusTranslations' => PaymentStatusTranslation::class,
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
    private static $default_sort = "SilvercartPaymentStatusTranslation.Title";
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartPaymentStatus';
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
        self::STATUS_CODE_OPEN,
        self::STATUS_CODE_PAID,
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
     * Returns the field labels.
     * 
     * @param bool $includerelations include relations?
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function fieldLabels($includerelations = true) : array
    {
        $this->beforeUpdateFieldLabels(function (&$labels) {
            $labels = array_merge(
                    $labels,
                    Tools::field_labels_for(self::class),
                    [
                        'DefaultStatusOpen' => _t(self::class . '.DefaultStatusOpen', 'Open'),
                        'DefaultStatusPaid' => _t(self::class . '.DefaultStatusPaid', 'Paid'),
                    ]
            );
        });
        return parent::fieldLabels($includerelations);
    }
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            if (in_array($this->Code, self::config()->get('default_codes'))) {
                $fields->dataFieldByName('Code')->setReadonly(true);
            }
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
                if ($existing instanceof PaymentStatus
                 && $existing->exists())
                {
                    continue;
                }
                $status = self::create();
                $status->Code      = $default;
                $status->Title     = $this->fieldLabel('DefaultStatus' . ucfirst($default));
                $status->IsDefault = $default == self::STATUS_CODE_OPEN;
                $status->write();
            }
        });
        parent::requireDefaultRecords();
    }
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
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
     * Casting to get the IsDefault state as a readable string
     *
     * @return string
     */
    public function getIsDefaultString() : string
    {
        return $this->IsDefault ? Tools::field_label('Yes') : Tools::field_label('No');
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
}