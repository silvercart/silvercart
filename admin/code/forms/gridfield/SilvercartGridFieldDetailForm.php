<?php
/**
 * Copyright 2017 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Customer
 */

/**
 * Extension for GridFieldDetailForm_ItemRequest.
 * 
 * @package Silvercart
 * @subpackage GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 05.01.2017
 * @license see license file in modules root directory
 * @copyright 2017 pixeltricks GmbH
 */
class SilvercartGridFieldDetailForm_ItemRequest extends DataExtension {
    
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
                $actions->push(FormAction::create('doSendChangePasswordEmail', _t('GridFieldDetailForm.SendChangePasswordEmail', 'Send Change Password Email'))
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