<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverCart\Admin\Forms\GridField\GridFieldBatchAction;
use SilverCart\Model\Product\AvailabilityStatus;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * Batch action to change a products availability status.
 *
 * @package SilverCart
 * @subpackage Admin_Forms_GridField_BatchActions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldBatchAction_ChangeAvailabilityStatus extends GridFieldBatchAction
{
    /**
     * Returns the markup of the callback form fields.
     * 
     * @return DBHTMLText
     */
    public function getCallbackFormFields() : DBHTMLText
    {
        return $this->render([
            'Dropdown' => $this->getDataObjectAsDropdownField(AvailabilityStatus::class),
        ]);
    }
    
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
     * 
     * @param GridField $gridField GridField to handle action for
     * @param array     $recordIDs Record IDs to handle action for
     * @param array     $data      Data to handle action for
     * 
     * @return void
     */
    public function handle(GridField $gridField, array $recordIDs, array $data) : void
    {
        $targetID       = $data[Convert::raw2att(AvailabilityStatus::class)];
        $relationName   = 'AvailabilityStatusID';
        $this->handleDefaultHasOneRelation($gridField, $recordIDs, $targetID, $relationName);
    }
}