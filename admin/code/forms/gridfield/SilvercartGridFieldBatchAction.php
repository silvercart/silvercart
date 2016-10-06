<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Forms_GridField_BatchActions
 */

/**
 * Base for batch actions.
 *
 * @package Silvercart
 * @subpackage Forms_GridField_BatchActions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 08.03.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldBatchAction {
    
    /**
     * Name of action
     *
     * @var string
     */
    protected $action = null;
    
    /**
     * name of class
     *
     * @var string
     */
    protected $class = null;
    
    /**
     * Sets the default of a SilvercartGridFieldBatchAction.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.03.2013
     */
    public function __construct() {
        $this->class = get_class($this);
        $this->action = str_replace('SilvercartGridFieldBatchAction_', '', $this->class);
    }
    
    /**
     * Returns the markup of the callback form fields.
     * 
     * @return string
     */
    public function getCallbackFormFields() {
        return '';
    }

    /**
     * Returns the title of the action
     * 
     * @return string
     */
    public function getTitle() {
        return _t($this->class . '.TITLE', $this->action);
    }
    
    /**
     * Is used to call javascript requirements of an action.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function RequireJavascript() {
        
    }
    
    /**
     * Is used to call javascript requirements of an action.
     * 
     * @param string $filename Name of the JS file
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function RequireDefaultJavascript($filename = null) {
        if (is_null($filename)) {
            $filename = $this->class;
        }
        Requirements::javascript('silvercart/admin/javascript/' . $filename . '.js');
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
    public function handle(GridField $gridField, $recordIDs, $data) {
        
    }
    
    /**
     * Returns a DataObject as a Dropdown field
     * 
     * @param string $classname Classname of the DataObject to get Dropdown field for
     * 
     * @return DropdownField
     */
    public function getDataObjectAsDropdownField($classname) {
        $records    = DataObject::get($classname);
        $recordsMap = $records->map();
        if ($recordsMap instanceof SS_Map) {
            $recordsMap = $recordsMap->toArray();
        }
        $dropdown = new DropdownField($classname, $classname, $recordsMap);
        return $dropdown;
    }
    
    /**
     * Handles the default action to reset a has_one relation.
     * 
     * @param GridField $gridField    GridField to handle action for
     * @param array     $recordIDs    Record IDs to handle action for
     * @param int       $targetID     ID of the target relation
     * @param string    $relationName Name of the relation to change
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function handleDefaultHasOneRelation(GridField $gridField, $recordIDs, $targetID, $relationName) {
        foreach ($recordIDs as $recordID) {
            $record = DataObject::get_by_id($gridField->getModelClass(), $recordID);
            if ($record->exists()) {
                $record->{$relationName} = $targetID;
                $record->write();
            }
        }
    }
}

/**
 * Batch action to change an orders status.
 *
 * @package Silvercart
 * @subpackage Forms_GridField_BatchActions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 08.03.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldBatchAction_ChangeOrderStatus extends SilvercartGridFieldBatchAction {
    
    /**
     * Returns the markup of the callback form fields.
     * 
     * @return string
     */
    public function getCallbackFormFields() {
        $dropdown = $this->getDataObjectAsDropdownField('SilvercartOrderStatus');
        return '<div class="field dropdown plain">' . $dropdown->Field() . '</div>';
    }
    
    /**
     * Is used to call javascript requirements of an action.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function RequireJavascript() {
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
    public function handle(GridField $gridField, $recordIDs, $data) {
        $orderStatusID  = $data['SilvercartOrderStatus'];
        
        foreach ($recordIDs as $orderID) {
            $order = SilvercartOrder::get_by_id('SilvercartOrder', $orderID);
            if ($order->exists()) {
                $order->SilvercartOrderStatusID = $orderStatusID;
                $order->write();
                $order->markAsSeen();
            }
        }
    }
    
}

/**
 * Batch action to print an order.
 *
 * @package Silvercart
 * @subpackage Forms_GridField_BatchActions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 08.03.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldBatchAction_PrintOrders extends SilvercartGridFieldBatchAction {
    
    /**
     * Is used to call javascript requirements of an action.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function RequireJavascript() {
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
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function handle(GridField $gridField, $recordIDs, $data) {
        $orders = new ArrayList();
        foreach ($recordIDs as $orderID) {
            $order = SilvercartOrder::get_by_id('SilvercartOrder', $orderID);
            if ($order->exists()) {
                $order->markAsSeen();
                $orders->push($order);
            }
        }
        $gridField->getConfig()->addComponent(new SilvercartGridFieldPopupTrigger(SilvercartPrint::getPrintURLForMany($orders)));
        return $gridField->FieldHolder();
    }
}

/**
 * Batch action to mark an order as seen.
 *
 * @package Silvercart
 * @subpackage Forms_GridField_BatchActions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 08.03.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldBatchAction_MarkAsSeen extends SilvercartGridFieldBatchAction {
    
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
            $order = SilvercartOrder::get_by_id('SilvercartOrder', $orderID);
            if ($order->exists()) {
                $order->markAsSeen();
            }
        }
    }
    
}

/**
 * Batch action to mark an order as not seen.
 *
 * @package Silvercart
 * @subpackage Forms_GridField_BatchActions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 08.03.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldBatchAction_MarkAsNotSeen extends SilvercartGridFieldBatchAction {
    
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
            $order = SilvercartOrder::get_by_id('SilvercartOrder', $orderID);
            if ($order->exists()) {
                $order->markAsNotSeen();
            }
        }
    }
}

/**
 * Batch action to mark an DataObject as active.
 *
 * @package Silvercart
 * @subpackage Forms_GridField_BatchActions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 14.03.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldBatchAction_ActivateDataObject extends SilvercartGridFieldBatchAction {
    
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
        foreach ($recordIDs as $recordID) {
            $record = DataObject::get_by_id($gridField->getModelClass(), $recordID);
            if ($record->exists()) {
                $record->isActive = true;
                $record->write();
            }
        }
    }
}

/**
 * Batch action to mark an DataObject as not active.
 *
 * @package Silvercart
 * @subpackage Forms_GridField_BatchActions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 14.03.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldBatchAction_DeactivateDataObject extends SilvercartGridFieldBatchAction {
    
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
        foreach ($recordIDs as $recordID) {
            $record = DataObject::get_by_id($gridField->getModelClass(), $recordID);
            if ($record->exists()) {
                $record->isActive = false;
                $record->write();
            }
        }
    }
}

/**
 * Batch action to change an orders status.
 *
 * @package Silvercart
 * @subpackage Forms_GridField_BatchActions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 08.03.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldBatchAction_ChangeManufacturer extends SilvercartGridFieldBatchAction {
    
    /**
     * Returns the markup of the callback form fields.
     * 
     * @return string
     */
    public function getCallbackFormFields() {
        $dropdown = $this->getDataObjectAsDropdownField('SilvercartManufacturer');
        return '<div class="field dropdown plain">' . $dropdown->Field() . '</div>';
    }
    
    /**
     * Is used to call javascript requirements of an action.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function RequireJavascript() {
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
    public function handle(GridField $gridField, $recordIDs, $data) {
        $targetID       = $data['SilvercartManufacturer'];
        $relationName   = 'SilvercartManufacturerID';
        $this->handleDefaultHasOneRelation($gridField, $recordIDs, $targetID, $relationName);
    }
    
}

/**
 * Batch action to change a product group.
 *
 * @package Silvercart
 * @subpackage Forms_GridField_BatchActions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 08.03.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldBatchAction_ChangeProductGroup extends SilvercartGridFieldBatchAction {
    
    /**
     * Returns the markup of the callback form fields.
     * 
     * @return string
     */
    public function getCallbackFormFields() {
        $dropdown = $this->getDataObjectAsDropdownField('SilvercartProductGroupPage');
        return '<div class="field dropdown plain">' . $dropdown->Field() . '</div>';
    }
    
    /**
     * Is used to call javascript requirements of an action.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function RequireJavascript() {
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
    public function handle(GridField $gridField, $recordIDs, $data) {
        $targetID       = $data['SilvercartProductGroupPage'];
        $relationName   = 'SilvercartProductGroupID';
        $this->handleDefaultHasOneRelation($gridField, $recordIDs, $targetID, $relationName);
    }
    
}

/**
 * Batch action to change a products availability status.
 *
 * @package Silvercart
 * @subpackage Forms_GridField_BatchActions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 08.03.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldBatchAction_ChangeAvailabilityStatus extends SilvercartGridFieldBatchAction {
    
    /**
     * Returns the markup of the callback form fields.
     * 
     * @return string
     */
    public function getCallbackFormFields() {
        $dropdown = $this->getDataObjectAsDropdownField('SilvercartAvailabilityStatus');
        return '<div class="field dropdown plain">' . $dropdown->Field() . '</div>';
    }
    
    /**
     * Is used to call javascript requirements of an action.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function RequireJavascript() {
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
    public function handle(GridField $gridField, $recordIDs, $data) {
        $targetID       = $data['SilvercartAvailabilityStatus'];
        $relationName   = 'SilvercartAvailabilityStatusID';
        $this->handleDefaultHasOneRelation($gridField, $recordIDs, $targetID, $relationName);
    }
    
}