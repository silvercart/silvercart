<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverCart\Admin\Forms\GridField\GridFieldBatchAction;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\DataObject;

/**
 * Batch action to delete DataObjects.
 *
 * @package SilverCart
 * @subpackage Admin\Forms\GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 02.06.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldBatchAction_Delete extends GridFieldBatchAction
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
        $modelClass = $gridField->getModelClass();
        foreach ($recordIDs as $recordID) {
            $record = DataObject::get($modelClass)->byID($recordID);
            if (is_object($record)
             && $record->exists()
            ) {
                $record->delete();
            }
        }
    }
}