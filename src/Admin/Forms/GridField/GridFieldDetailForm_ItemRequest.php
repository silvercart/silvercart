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
 * @subpackage Admin\Forms\GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property \SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest $owner Owner
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
     */
    public function updateItemEditForm(Form $form) : void
    {
        $record = $this->owner->record;
        if ($record instanceof DataObject) {
            if ($record instanceof Member
             && $record->exists()
            ) {
                $actions = $form->Actions();
                $actions->push(FormAction::create('doSendChangePasswordEmail', _t(GridFieldDetailForm_ItemRequest::class . '.SendChangePasswordEmail', 'Send Change Password Email'))
                                ->setUseButtonTag(true)
                                ->addExtraClass('ss-ui-action-constructive')
                                ->setAttribute('data-icon', 'accept'));
            }
            if ($record->hasMethod('getCMSActions')
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
    }
    
    /**
     * Handler for SilverCart custom CMS actions.
     * 
     * @param int   $index Numeric index to identify the original action
     * @param array $data  Submitted data
     * @param Form  $form  Form
     * 
     * @return mixed
     */
    protected function handleSCCustomAction(int $index, array $data, Form $form)
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
     */
    public function sccustomaction1(array $data, Form $form)
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
     */
    public function sccustomaction2(array $data, Form $form)
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
     */
    public function sccustomaction3(array $data, Form $form)
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
     */
    public function sccustomaction4(array $data, Form $form)
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
     */
    public function sccustomaction5(array $data, Form $form)
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
     */
    public function doSendChangePasswordEmail(array $data, Form $form) : void
    {
        $member = $this->owner->record;
        $member->sendChangePasswordEmail();
    }
}