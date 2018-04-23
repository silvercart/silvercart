<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverCart\Admin\Forms\GridField\GridFieldBatchAction;
use SilverCart\Model\Order\Order;
use SilverStripe\Forms\GridField\GridField;

/**
 * Batch action to mark an order as not seen.
 *
 * @package SilverCart
 * @subpackage Admin_Forms_GridField_BatchActions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldBatchAction_MarkAsNotSeen extends GridFieldBatchAction {
    
    /**
     * Handles the action.
     * 
     * @param GridField $gridField GridField to handle action for
     * @param array     $recordIDs Record IDs to handle action for
     * @param array     $data      Data to handle action for
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function handle(GridField $gridField, $recordIDs, $data) {
        foreach ($recordIDs as $orderID) {
            $order = Order::get()->byID($orderID);
            if ($order->exists()) {
                $order->markAsNotSeen();
            }
        }
    }
}
