<?php

namespace SilverCart\Model\Order;

use SilverCart\Dev\Tools;
use SilverCart\Admin\Controllers\ModelAdmin_ReadonlyInterface;
use SilverCart\Model\Order\Order;
use SilverStripe\ORM\DataObject;

/**
 * Order logs are used to log date and time of changing an order. By SilverCarts
 * default, order logs are created when an order status changes from one to
 * another.
 * 
 * @package SilverCart
 * @subpackage Model_Order
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $Context  Context
 * @property string $Action   Action
 * @property string $Message  Message
 * @property int    $SourceID Source Object ID
 * @property int    $TargetID Target Object ID
 * @property int    $OrderID  Order ID
 * 
 * @method Order Order() Returns the related Order.
 */
class OrderLog extends DataObject implements ModelAdmin_ReadonlyInterface
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    const ACTION_CHANGED            = 'Changed';
    const ACTION_CREATED            = 'Created';
    const ACTION_INFO               = 'Info';
    const ACTION_MARKED_AS_SEEN     = 'MarkedAsSeen';
    const ACTION_MARKED_AS_NOT_SEEN = 'MarkedAsNotSeen';
    
    /**
     * DB attributes
     *
     * @var array
     */
    private static $db = [
        'Context'  => 'Varchar(1024)',
        'Action'   => 'Enum("Created,Changed,Info,MarkedAsSeen,MarkedAsNotSeen","Changed")',
        'Message'  => 'Text',
        'SourceID' => 'Int',
        'TargetID' => 'Int',
    ];
    /**
     * has one relations
     *
     * @var array
     */
    private static $has_one = [
        'Order' => Order::class,
    ];
    /**
     * Casted attributes
     *
     * @var array
     */
    private static $casting = [
        'ContextNice'       => 'Text',
        'ContextMessage'    => 'Text',
        'SourceTitle'       => 'Text',
        'TargetTitle'       => 'Text',
    ];
    /**
     * Default sort field and direction
     *
     * @var string
     */
    private static $default_sort = 'Created DESC';
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartOrderLog';
    
    /**
     * Adds an log with the action Changed
     * 
     * @param Order  $order    Order to add log for
     * @param string $context  Context object
     * @param int    $sourceID ID of the source
     * @param int    $targetID ID of the target
     * @param string $message  Message
     * 
     * @return OrderLog
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public static function addChangedLog(Order $order, string $context, int $sourceID, int $targetID, string $message = '') : OrderLog
    {
        $orderLog = OrderLog::create();
        $orderLog->Context  = $context;
        $orderLog->Message  = $message;
        $orderLog->SourceID = $sourceID;
        $orderLog->TargetID = $targetID;
        $orderLog->OrderID  = $order->ID;
        $orderLog->setChangedAction();
        $orderLog->write();
        return $orderLog;
    }
    
    /**
     * Adds an log with the action Info
     * 
     * @param Order  $order   Order to add log for
     * @param string $message Message
     * 
     * @return OrderLog
     */
    public static function addInfoLog(Order $order, string $message) : OrderLog
    {
        $orderLog = OrderLog::create();
        $orderLog->Context  = $order->ClassName;
        $orderLog->Message  = $message;
        $orderLog->SourceID = $order->ID;
        $orderLog->TargetID = $order->ID;
        $orderLog->OrderID  = $order->ID;
        $orderLog->setChangedInfo();
        $orderLog->write();
        return $orderLog;
    }
    
    /**
     * Adds an log with the action MarkedAsSeen
     * 
     * @param Order  $order   Order to add log for
     * @param string $context Context object
     * @param string $message Message
     * 
     * @return OrderLog
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public static function addMarkedAsSeenLog(Order $order, string $context, string $message = '') : OrderLog
    {
        $orderLog = OrderLog::create();
        $orderLog->Context = $context;
        $orderLog->Message = $message;
        $orderLog->OrderID = $order->ID;
        $orderLog->setMarkedAsSeenAction();
        $orderLog->write();
        return $orderLog;
    }
    
    /**
     * Adds an log with the action MarkedAsNotSeen
     * 
     * @param Order  $order   Order to add log for
     * @param string $context Context object
     * @param string $message Message
     * 
     * @return OrderLog
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2012
     */
    public static function addMarkedAsNotSeenLog(Order $order, string $context, string $message = '') : OrderLog
    {
        $orderLog = OrderLog::create();
        $orderLog->Context = $context;
        $orderLog->Message = $message;
        $orderLog->OrderID = $order->ID;
        $orderLog->setMarkedAsNotSeenAction();
        $orderLog->write();
        return $orderLog;
    }
    
    /**
     * Adds an log with the action Created
     * 
     * @param Order  $order   Order to add log for
     * @param string $context Context object
     * @param string $message Message
     * 
     * @return OrderLog
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public static function addCreatedLog(Order $order, string $context, string $message = '') : OrderLog
    {
        $orderLog = OrderLog::create();
        $orderLog->Context = $context;
        $orderLog->Message = $message;
        $orderLog->OrderID = $order->ID;
        $orderLog->setCreatedAction();
        $orderLog->write();
        return $orderLog;
    }

    /**
     * Sets the action to Changed.
     * 
     * @return OrderLog
     */
    public function setChangedAction() : OrderLog
    {
        $this->Action = self::ACTION_CHANGED;
        return $this;
    }
    
    /**
     * Sets the action to Created.
     * 
     * @return OrderLog
     */
    public function setCreatedAction() : OrderLog
    {
        $this->Action = self::ACTION_CREATED;
        return $this;
    }

    /**
     * Sets the action to Info.
     * 
     * @return OrderLog
     */
    public function setChangedInfo() : OrderLog
    {
        $this->Action = self::ACTION_INFO;
        return $this;
    }
    
    /**
     * Sets the action to MarkedAsSeen.
     * 
     * @return OrderLog
     */
    public function setMarkedAsSeenAction() : OrderLog
    {
        $this->Action = self::ACTION_MARKED_AS_SEEN;
        return $this;
    }
    
    /**
     * Sets the action to MarkedAsSeen.
     * 
     * @return OrderLog
     */
    public function setMarkedAsNotSeenAction() : OrderLog
    {
        $this->Action = self::ACTION_MARKED_AS_NOT_SEEN;
        return $this;
    }

    /**
     * Returns the Context of the log in a human readable state
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function getContextNice() : string
    {
        $nice = '---';
        if ($this->exists()) {
            $nice = Tools::singular_name_for(singleton($this->Context));
        }
        return $nice;
    }
    
    /**
     * Builds and returns the message in the logs context
     * 
     * @return string
     */
    public function getContextMessage() : string
    {
        $message = '';
        switch ($this->Action) {
            case self::ACTION_CREATED:
                $message = _t(OrderLog::class . '.MESSAGE_CREATED',
                        'Created: {context} was created',
                        [
                            'context' => $this->ContextNice,
                        ]
                );
                break;
            case self::ACTION_MARKED_AS_SEEN:
                $message = $this->fieldLabel(self::ACTION_MARKED_AS_SEEN);
                break;
            case self::ACTION_MARKED_AS_NOT_SEEN:
                $message = $this->fieldLabel('MarkedAsNotSeen');
                break;
            case self::ACTION_INFO:
            case self::ACTION_CHANGED:
            default:
                if ($this->Context !== Order::class) {
                    $message = _t(OrderLog::class . '.MESSAGE_CHANGED',
                            'Changed: {sourcetitle} -> {targettitle}',
                            [
                                'sourcetitle' => $this->SourceTitle,
                                'targettitle' => $this->TargetTitle,
                            ]
                    );
                }
                break;
        }
        if (!empty($this->Message)) {
            if (empty($message)) {
                $message = $this->Message;
            } else {
                $message = "{$message}; {$this->Message}";
            }
        }
        return $message;
    }

    /**
     * Creates and returns the source title
     * 
     * @return string
     */
    public function getSourceTitle() : string
    {
        $sourceTitle = '---';
        if ($this->exists()) {
            $sourceTitle  = "#{$this->SourceID}";
            $sourceObject = DataObject::get_by_id($this->Context, $this->SourceID);
            if ($sourceObject instanceof DataObject) {
                $sourceTitle = $sourceObject->Title;
            }
        }
        return $sourceTitle;
    }

    /**
     * Creates and returns the target title
     * 
     * @return string
     */
    public function getTargetTitle() : string
    {
        $targetTitle = '---';
        if ($this->exists()) {
            $targetTitle  = "#{$this->TargetID}";
            $targetObject = DataObject::get_by_id($this->Context, $this->TargetID);
            if ($targetObject instanceof DataObject) {
                $targetTitle = $targetObject->Title;
            }
        }
        return $targetTitle;
    }

    /**
     * Indicates wether the current user can view this object.
     * 
     * @param Member $member current member
     *
     * @return bool
     */
    public function canView($member = null) : bool
    {
        return $this->Order()->canView($member);
    }

    /**
     * Indicates wether the current user can edit this object.
     * 
     * @param Member $member current member
     *
     * @return bool
     */
    public function canEdit($member = null) : bool
    {
        return $this->Order()->canEdit($member);
    }

    /**
     * Indicates wether the current user can delete this object.
     * 
     * @param Member $member current member
     *
     * @return bool
     */
    public function canDelete($member = null) : bool
    {
        return $this->Order()->canDelete($member);
    }

    /**
     * Field labgels
     * 
     * @param bool $includerelations Include relations or not?
     * 
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'Context'         => _t(OrderLog::class . '.CONTEXT', 'Context'),
            'ContextMessage'  => _t(OrderLog::class . '.MESSAGE', 'Action'),
            'Created'         => _t(OrderLog::class . '.CREATED', 'Date/Time'),
            'MarkedAsSeen'    => _t(OrderLog::class . '.MESSAGE_MARKEDASSEEN', 'Marked as seen'),
            'MarkedAsNotSeen' => _t(OrderLog::class . '.MESSAGE_MARKEDASNOTSEEN', 'Marked as not seen'),
        ]);
    }

    /**
     * Summary fields
     * 
     * @return array
     */
    public function summaryFields() : array
    {
        $this->beforeExtending('updateSummaryFields', function(array &$summaryFields) {
            $summaryFields = [
                'Created'        => $this->fieldLabel('Created'),
                'ContextNice'    => $this->fieldLabel('Context'),
                'ContextMessage' => $this->fieldLabel('ContextMessage'),
            ];
        });
        return parent::summaryFields();
    }
}