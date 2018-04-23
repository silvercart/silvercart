<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverStripe\Core\Extension;
use SilverStripe\Security\Member;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;

/**
 * Extension for SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest.
 *
 * @package SilverCart
 * @subpackage Admin_Forms_GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldDetailForm_ItemRequest extends Extension {
    
    /**
     * Updates the item edit form.
     * 
     * @param Form $form Form
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.01.2017
     */
    public function updateItemEditForm(Form $form) {
        if ($this->owner->record instanceof Member) {
            
            
            $actions = $form->Actions();
            if ($this->owner->record->ID !== 0) {
                $actions->push(FormAction::create('doSendChangePasswordEmail', _t(GridFieldDetailForm_ItemRequest::class . '.SendChangePasswordEmail', 'Send Change Password Email'))
                                ->setUseButtonTag(true)
                                ->addExtraClass('ss-ui-action-constructive')
                                ->setAttribute('data-icon', 'accept'));
            }
        }
    }
    
    /**
     * Sends the change password email to the Member.
     * 
     * @param array $data Data
     * @param Form  $form Form
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.01.2017
     */
	public function doSendChangePasswordEmail($data, $form) {
        $member = $this->owner->record;
        $member->sendChangePasswordEmail();
    }

}