<?php

namespace SilverCart\Model\Product;

use SilverCart\Dev\Tools;
use SilverCart\Forms\HiddenField;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\Order;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\ {
    DataObject,
    DB,
    FieldType\DBInt,
    FieldType\DBText
};
use SilverStripe\Security\Member;

/**
 * Represents a stock item entry to manage a product's stock.
 * 
 * @package SilverCart
 * @subpackage Model\Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 10.01.2019
 * @copyright 2019 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class StockItemEntry extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    const ORIGIN_CODE_INITIAL      = -1;
    const ORIGIN_CODE_UNDEFINED    = 0;
    const ORIGIN_CODE_NEW_PRODUCT  = 1;
    const ORIGIN_CODE_USER_INPUT   = 2;
    const ORIGIN_CODE_ORDER_PLACED = 3;
    const ORIGIN_CODE_ORDER_UPDATE = 4;
    const ORIGIN_CODE_ORDER_CANCEL = 5;
    const ORIGIN_CODE_API_IMPORT   = 6;
    
    /**
     * DB table name.
     *
     * @var string
     */
    private static $table_name = 'SilvercartStockItemEntry';
    /**
     * DB attributes.
     *
     * @var array
     */
    private static $db = [
        'Quantity'   => DBInt::class,
        'Reason'     => DBText::class,
        'OriginCode' => DBInt::class,
    ];
    /**
     * Has one relations.
     *
     * @var array
     */
    private static $has_one = [
        'Product' => Product::class,
        'Member'  => Member::class,
        'Order'   => Order::class,
    ];
    /**
     * Default sort.
     *
     * @var string
     */
    private static $default_sort = 'Created DESC';
    /**
     * Casted attributes.
     *
     * @var array
     */
    private static $casting = [
        'MemberName'       => DBText::class,
        'Origin'           => DBText::class,
        'QuantityWithSign' => DBText::class,
    ];
    /**
     * Set to true before writing a new stock item entry to skip the update of the
     * real product stock quantity.
     *
     * @var bool
     */
    protected $skipProductUpdate = false;
    
    /**
     * Adds a new stock item entry to the database.
     * 
     * @param Product $product           Related product
     * @param int     $quantity          Quantity to add
     * @param int     $originCode        Origin code
     * @param string  $reason            Reason
     * @param Member  $member            Related member
     * @param Order   $order             Related order
     * @param bool    $skipProductUpdate Skip product update or not?
     * 
     * @return StockItemEntry
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.01.2019
     */
    public static function add(Product $product, int $quantity, int $originCode = 0, string $reason = '', Member $member = null, Order $order = null, bool $skipProductUpdate = false) : StockItemEntry
    {
        $entry = self::create();
        $entry->setSkipProductUpdate($skipProductUpdate);
        $entry->Quantity   = $quantity;
        $entry->OriginCode = $originCode;
        $entry->Reason     = $reason;
        $entry->ProductID  = $product->ID;
        $entry->MemberID   = ($member instanceof Member) ? $member->ID : 0;
        $entry->OrderID    = ($order instanceof Order) ? $order->ID : 0;
        $entry->write();
        return $entry;
    }

    /**
     * Returns the translated singular name of the object.
     *
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object.
     *
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this);
    }
    
    /**
     * Returns whether this object can be deleted.
     * Always false since stock item entries can't be deleted.
     * 
     * @param Member $member Member
     * 
     * @return bool
     */
    public function canDelete($member = null) : bool
    {
        return false;
    }
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $productID = $fields->dataFieldByName('ProductID')->Value();
            $fields->removeByName('OriginCode');
            $fields->removeByName('MemberID');
            $fields->removeByName('OrderID');
            $fields->removeByName('ProductID');
            $fields->addFieldToTab('Root.Main', HiddenField::create('OriginCode', '', 1));
            $fields->addFieldToTab('Root.Main', HiddenField::create('MemberID', '', Customer::currentUserID()));
            $fields->addFieldToTab('Root.Main', HiddenField::create('ProductID', '', $productID));
            $fields->addFieldToTab('Root.Main', ReadonlyField::create('OriginDesc', $this->fieldLabel('Origin'), $this->Origin));
            $fields->dataFieldByName('Quantity')->setDescription($this->fieldLabel('QuantityDesc'));
            $fields->dataFieldByName('Reason')->setDescription($this->fieldLabel('ReasonDesc'));
            if ($this->exists()) {
                $fields->dataFieldByName('Quantity')->setReadonly(true);
                $fields->dataFieldByName('Quantity')->setRightTitle($this->fieldLabel('QuantityRightTitle'));
                if ($this->Order()->exists()) {
                    $fields->addFieldToTab('Root.Main', ReadonlyField::create('OrderDesc', $this->fieldLabel('Order'), $this->Order()->Title));
                }
            }
        });
        return DataObjectExtension::getCMSFields($this);
    }
    
    /**
     * Returns the field labels.
     * 
     * @param bool $includerelations Include relations?
     * 
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        $this->beforeUpdateFieldLabels(function(array &$labels) {
            $labels = array_merge(
                    $labels,
                    Tools::field_labels_for(self::class),
                    [
                        'Member'  => Member::singleton()->i18n_singular_name(),
                        'Order'   => Order::singleton()->singular_name(),
                        'Product' => Product::singleton()->singular_name(),
                    ]
            );
            for ($originCode = self::ORIGIN_CODE_INITIAL; $originCode <= self::ORIGIN_CODE_API_IMPORT; $originCode++) {
                $labels["Origin{$originCode}"] = _t(self::class . ".Origin{$originCode}", "Code");
            }
        });
        return parent::fieldLabels($includerelations);
    }
    
    /**
     * Returns the summary fields.
     * 
     * @return array
     */
    public function summaryFields() : array
    {
        $summaryFields = [
            'Created.Nice'     => $this->fieldLabel('Created'),
            'QuantityWithSign' => $this->fieldLabel('Quantity'),
            'Origin'           => $this->fieldLabel('Origin'),
            'Reason'           => $this->fieldLabel('Reason'),
            'MemberName'       => $this->fieldLabel('Member'),
        ];
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Updates the related product's stock quantity after writing a stock item 
     * entry.
     * 
     * @return void
     */
    protected function onAfterWrite() : void
    {
        parent::onAfterWrite();
        if ($this->skipProductUpdate()) {
            return;
        }
        $tableName = Product::config()->get('table_name');
        DB::query("BEGIN;UPDATE {$tableName} SET StockQuantity = StockQuantity + {$this->Quantity} WHERE ID = {$this->ProductID};COMMIT;");
    }
    
    /**
     * If there are no stock item entries yet, this method will create an initial
     * stock item entry for each product with a stock above or below 0.
     * 
     * @return void
     */
    public function requireDefaultRecords() : void
    {
        if (self::get()->count() > 0) {
            return;
        }
        $productsWithStock = Product::get()->where("StockQuantity != 0");
        foreach ($productsWithStock as $product) {
            self::add($product, $product->StockQuantity, self::ORIGIN_CODE_INITIAL);
        }
    }
    
    /**
     * Returns the origin text.
     * 
     * @return string
     */
    public function getMemberName() : string
    {
        $memberName = "System";
        if ($this->Member()->exists()) {
            $memberName = $this->Member()->Name;
        }
        return $memberName;
    }
    
    /**
     * Returns the origin text.
     * 
     * @return string
     */
    public function getOrigin() : string
    {
        return "{$this->fieldLabel("Origin{$this->OriginCode}")} [#{$this->OriginCode}]";
    }
    
    /**
     * Returns the quantiy with plus or minus sign as text.
     * 
     * @return string
     */
    public function getQuantityWithSign() : string
    {
        $sign = "";
        if ($this->Quantity > 0) {
            $sign = "+";
        }
        return "{$sign}{$this->Quantity}";
    }
    
    /**
     * Sets whether to skip the real product stock quantity update after writing
     * or not.
     * 
     * @param bool $skip Skip or not?
     * 
     * @return $this
     */
    public function setSkipProductUpdate(bool $skip) : StockItemEntry
    {
        $this->skipProductUpdate = $skip;
        return $this;
    }
    
    /**
     * Returns whether to skip the real product stock quantity update after 
     * writing or not.
     * 
     * @return bool
     */
    public function getSkipProductUpdate() : bool
    {
        return $this->skipProductUpdate;
    }
    
    /**
     * Returns whether to skip the real product stock quantity update after 
     * writing or not.
     * Alias for self::getSkipProductUpdate().
     * 
     * @return bool
     */
    public function skipProductUpdate() : bool
    {
        return $this->getSkipProductUpdate();
    }
}