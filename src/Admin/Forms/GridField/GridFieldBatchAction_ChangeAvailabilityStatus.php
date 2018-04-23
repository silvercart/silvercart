<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverCart\Admin\Forms\GridField\GridFieldBatchAction;
use SilverCart\Model\Product\AvailabilityStatus;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\GridField\GridField;

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
class GridFieldBatchAction_ChangeAvailabilityStatus extends GridFieldBatchAction {
    
    /**
     * Returns the markup of the callback form fields.
     * 
     * @return string
     */
    public function getCallbackFormFields() {
        return $this->render(array(
            'Dropdown' => $this->getDataObjectAsDropdownField(AvailabilityStatus::class),
        ));
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
        $targetID       = $data[Convert::raw2att(AvailabilityStatus::class)];
        $relationName   = 'AvailabilityStatusID';
        $this->handleDefaultHasOneRelation($gridField, $recordIDs, $targetID, $relationName);
    }
    
}
