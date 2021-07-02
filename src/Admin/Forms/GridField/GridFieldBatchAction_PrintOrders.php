<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverCart\Admin\Forms\GridField\GridFieldBatchAction;
use SilverCart\Admin\Forms\GridField\GridFieldPopupTrigger;
use SilverCart\Model\Order\Order;
use SilverCart\View\Printer\Printer;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\ArrayList;

/**
 * Batch action to print an order.
 *
 * @package SilverCart
 * @subpackage Admin_Forms_GridField_BatchActions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldBatchAction_PrintOrders extends GridFieldBatchAction
{
    /**
     * Is used to call javascript requirements of an action.
     * 
     * @return void
     */
    public function RequireJavascript() : void
    {
        $this->RequireDefaultJavascript();
    }
    
    /**
     * Handles the action.
     * Prepares the popup to load the pring preview
     * 
     * @param GridField $gridField GridField to handle action for
     * @param array     $recordIDs Record IDs to handle action for
     * @param array     $data      Data to handle action for
     * 
     * @return string
     */
    public function handle(GridField $gridField, array $recordIDs, array $data) : string
    {
        $orders = ArrayList::create();
        foreach ($recordIDs as $orderID) {
            $order = Order::get()->byID($orderID);
            if ($order->exists()) {
                $order->markAsSeen();
                $orders->push($order);
            }
        }
        $gridField->getConfig()->addComponent(new GridFieldPopupTrigger(Printer::getPrintURLForMany($orders)));
        return $gridField->FieldHolder();
    }
}
