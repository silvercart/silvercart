<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverCart\Admin\Forms\GridField\GridFieldBatchAction;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\DataObject;

/**
 * Batch action to mark an DataObject as active.
 *
 * @package SilverCart
 * @subpackage Admin_Forms_GridField_BatchActions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldBatchAction_ActivateDataObject extends GridFieldBatchAction
{
    /**
     * Handles the action.
     * 
     * @param GridField $gridField GridField to handle action for
     * @param array     $recordIDs Record IDs to handle action for
     * @param array     $data      Data to handle action for
     * 
     * @return void
     */
    public function handle(GridField $gridField, array $recordIDs, array $data) : void
    {
        foreach ($recordIDs as $recordID) {
            $record = DataObject::get($gridField->getModelClass())->byID($recordID);
            if ($record->exists()) {
                $record->isActive = true;
                $record->write();
            }
        }
    }
}