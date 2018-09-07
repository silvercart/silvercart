<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverCart\Model\Order\Order;
use SilverCart\Model\Payment\PaymentStatus;
use SilverCart\Admin\Forms\GridField\GridFieldBatchAction;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\GridField\GridField;

/**
 * Batch action to change a payment status.
 *
 * @package SilverCart
 * @subpackage Admin_Forms_GridField_BatchActions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 07.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldBatchAction_ChangePaymentStatus extends GridFieldBatchAction
{
    /**
     * Returns the markup of the callback form fields.
     * 
     * @return string
     */
    public function getCallbackFormFields() {
        return $this->render([
            'Dropdown' => $this->getDataObjectAsDropdownField(PaymentStatus::class),
        ]);
    }
    
    /**
     * Is used to call javascript requirements of an action.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function RequireJavascript()
    {
        $this->RequireDefaultJavascript();
    }
    
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
    public function handle(GridField $gridField, $recordIDs, $data)
    {
        $paymentStatusID = $data[Convert::raw2att(PaymentStatus::class)];
        foreach ($recordIDs as $orderID) {
            $order = Order::get()->byID($orderID);
            if ($order->exists()) {
                $order->PaymentStatusID = $paymentStatusID;
                $order->write();
                $order->markAsSeen();
            }
        }
    }
}