<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverStripe\Core\Extension;
use SilverStripe\Security\Member;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\ORM\DataObject;

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
class GridFieldDetailForm_ItemRequest extends Extension
{
    /**
     * Map of URL manageable dummy actions and original form actions.
     *
     * @var array
     */
    private $actionMap = [];
    /**
     * Updates the item edit form.
     * 
     * @param Form $form Form
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2018
     */
    public function updateItemEditForm(Form $form)
    {
        if ($this->owner->record instanceof Member) {
            $actions = $form->Actions();
            if ($this->owner->record->ID !== 0) {
                $actions->push(FormAction::create('doSendChangePasswordEmail', _t(GridFieldDetailForm_ItemRequest::class . '.SendChangePasswordEmail', 'Send Change Password Email'))
                                ->setUseButtonTag(true)
                                ->addExtraClass('ss-ui-action-constructive')
                                ->setAttribute('data-icon', 'accept'));
            }
        }
        
        $record = $this->owner->record;
        if ($record instanceof DataObject
         && $record->hasMethod('getCMSActions')
         && $record->getCMSActions()->exists()
        ) {
            $index = 1;
            foreach ($record->getCMSActions() as $action) {
                /* @var $action FormAction */
                $this->actionMap[$index] = $action->getName();
                $action->setFullAction("action_sccustomaction{$index}");
                $action->setName("action_sccustomaction{$index}");
                $form->Actions()->push($action);
                $index++;
            }
        }
    }
    
    /**
     * Handler for SilverCart custom CMS actions.
     * 
     * @param int   $index Numeric index to identify the original action
     * @param array $data  Submitted data
     * @param Form  $form  Form
     * 
     * @return mixed
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2018
     */
    protected function handleSCCustomAction($index, $data, $form)
    {
        if (!array_key_exists($index, $this->actionMap)) {
            return;
        }
        $originalAction = $this->actionMap[$index];
        $methodName     = preg_replace(['/^action_/','/_x$|_y$/'], '', $originalAction);
        $record         = $this->owner->record;
        if ($record instanceof DataObject
         && $record->hasMethod($methodName)
        ) {
            return $record->{$methodName}($this->owner, $data, $form);
        }
    }
    
    /**
     * Dummy action handler for the first custom CMS action.
     * 
     * @param array $data Submitted data
     * @param Form  $form Form
     * 
     * @return mixed
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2018
     */
    public function sccustomaction1($data, $form)
    {
        return $this->handleSCCustomAction(1, $data, $form);
    }
    
    /**
     * Dummy action handler for the first custom CMS action.
     * 
     * @param array $data Submitted data
     * @param Form  $form Form
     * 
     * @return mixed
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2018
     */
    public function sccustomaction2($data, $form)
    {
        return $this->handleSCCustomAction(2, $data, $form);
    }
    
    /**
     * Dummy action handler for the first custom CMS action.
     * 
     * @param array $data Submitted data
     * @param Form  $form Form
     * 
     * @return mixed
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2018
     */
    public function sccustomaction3($data, $form)
    {
        return $this->handleSCCustomAction(3, $data, $form);
    }
    
    /**
     * Dummy action handler for the first custom CMS action.
     * 
     * @param array $data Submitted data
     * @param Form  $form Form
     * 
     * @return mixed
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2018
     */
    public function sccustomaction4($data, $form)
    {
        return $this->handleSCCustomAction(4, $data, $form);
    }
    
    /**
     * Dummy action handler for the first custom CMS action.
     * 
     * @param array $data Submitted data
     * @param Form  $form Form
     * 
     * @return mixed
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2018
     */
    public function sccustomaction5($data, $form)
    {
        return $this->handleSCCustomAction(5, $data, $form);
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
    public function doSendChangePasswordEmail($data, $form)
    {
        $member = $this->owner->record;
        $member->sendChangePasswordEmail();
    }

}