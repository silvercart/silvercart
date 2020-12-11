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
 * @property int    $SourceID Source Object ID
 * @property int    $TargetID Target Object ID
 * @property int    $OrderID  Order ID
 * 
 * @method Order Order() Returns the related Order.
 */
class OrderLog extends DataObject implements ModelAdmin_ReadonlyInterface
{
    /**
     * DB attributes
     *
     * @var array
     */
    private static $db = [
        'Context'   => 'Varchar(64)',
        'Action'    => 'Enum("Created,Changed,MarkedAsSeen","Changed")',
        'SourceID'  => 'Int',
        'TargetID'  => 'Int',
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
     * 
     * @return OrderLog
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public static function addChangedLog(Order $order, string $context, int $sourceID, int $targetID) : OrderLog
    {
        $orderLog = OrderLog::create();
        $orderLog->Context  = $context;
        $orderLog->SourceID = $sourceID;
        $orderLog->TargetID = $targetID;
        $orderLog->OrderID  = $order->ID;
        $orderLog->setChangedAction();
        $orderLog->write();
        return $orderLog;
    }
    
    /**
     * Adds an log with the action MarkedAsSeen
     * 
     * @param Order  $order   Order to add log for
     * @param string $context Context object
     * 
     * @return OrderLog
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public static function addMarkedAsSeenLog(Order $order, string $context) : OrderLog
    {
        $orderLog = OrderLog::create();
        $orderLog->Context = $context;
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
     * 
     * @return OrderLog
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2012
     */
    public static function addMarkedAsNotSeenLog(Order $order, string $context) : OrderLog
    {
        $orderLog = OrderLog::create();
        $orderLog->Context = $context;
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
     * 
     * @return OrderLog
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public static function addCreatedLog(Order $order, string $context) : OrderLog
    {
        $orderLog = OrderLog::create();
        $orderLog->Context = $context;
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
        $this->Action = 'Changed';
        return $this;
    }
    
    /**
     * Sets the action to Created.
     * 
     * @return OrderLog
     */
    public function setCreatedAction() : OrderLog
    {
        $this->Action = 'Created';
        return $this;
    }
    
    /**
     * Sets the action to MarkedAsSeen.
     * 
     * @return OrderLog
     */
    public function setMarkedAsSeenAction() : OrderLog
    {
        $this->Action = 'MarkedAsSeen';
        return $this;
    }
    
    /**
     * Sets the action to MarkedAsSeen.
     * 
     * @return OrderLog
     */
    public function setMarkedAsNotSeenAction() : OrderLog
    {
        $this->Action = 'MarkedAsNotSeen';
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
            case 'Created':
                $message = _t(OrderLog::class . '.MESSAGE_CREATED',
                        'Created: {context} was created',
                        [
                            'context' => $this->ContextNice,
                        ]
                );
                break;
            case 'MarkedAsSeen':
                $message = $this->fieldLabel('MarkedAsSeen');
                break;
            case 'MarkedAsNotSeen':
                $message = $this->fieldLabel('MarkedAsNotSeen');
                break;
            case 'Changed':
            default:
                $message = _t(OrderLog::class . '.MESSAGE_CHANGED',
                        'Changed: {sourcetitle} -> {targettitle}',
                        [
                            'sourcetitle' => $this->SourceTitle,
                            'targettitle' => $this->TargetTitle,
                        ]
                );
                break;
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
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function fieldLabels($includerelations = true) : array
    {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                [
                    'Context'         => _t(OrderLog::class . '.CONTEXT', 'Context'),
                    'Created'         => _t(OrderLog::class . '.CREATED', 'Date/Time'),
                    'Message'         => _t(OrderLog::class . '.MESSAGE', 'Action'),
                    'MarkedAsSeen'    => _t(OrderLog::class . '.MESSAGE_MARKEDASSEEN', 'Marked as seen'),
                    'MarkedAsNotSeen' => _t(OrderLog::class . '.MESSAGE_MARKEDASNOTSEEN', 'Marked as not seen'),
                ]
        );
        
        $this->extend('updateFieldLabels', $fieldLabels);
        
        return $fieldLabels;
    }

    /**
     * Summary fields
     * 
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2012
     */
    public function summaryFields() : array
    {
        $summaryFields = array(
            'Created'           => $this->fieldLabel('Created'),
            'ContextNice'       => $this->fieldLabel('Context'),
            'ContextMessage'    => $this->fieldLabel('Message'),
        );
        
        $this->extend('updateSummaryFields', $summaryFields);
        
        return $summaryFields;
    }
}