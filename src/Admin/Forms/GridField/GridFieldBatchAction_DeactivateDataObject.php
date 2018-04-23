<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverCart\Admin\Forms\GridField\GridFieldBatchAction;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\DataObject;

/**
 * Batch action to mark an DataObject as not active.
 *
 * @package SilverCart
 * @subpackage Admin_Forms_GridField_BatchActions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldBatchAction_DeactivateDataObject extends GridFieldBatchAction {
    
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
