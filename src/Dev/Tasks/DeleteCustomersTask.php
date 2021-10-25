<?php

namespace SilverCart\Dev\Tasks;

use SilverCart\Model\Customer\DeletedCustomer;
use SilverCart\Model\Customer\DeletedCustomerReason;
use SilverStripe\Security\Member;

/**
 * Task to delete customer accounts.
 * 
 * @package SilverCart
 * @subpackage Dev\Tasks
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 13.07.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DeleteCustomersTask extends Task
{
    /**
     * Processes this task.
     *
     * @return void
     */
    public function process() : void
    {
        $members = Member::get()->filter([
            'MarkForDeletion'              => true,
            'MarkForDeletionDate:LessThan' => date('Y-m-d'),
        ]);
        if ($members->exists()) {
            $this->printInfo("found {$members->count()} customer(s) to delete.");
            foreach ($members as $member) {
                if (!$member->canBeDeletedAutomatically()) {
                    continue;
                }
                $reason     = DeletedCustomerReason::get()->byID($member->MarkForDeletionReasonID);
                $reasonText = $member->MarkForDeletionReason;
                if ($reason instanceof DeletedCustomerReason) {
                    $reasonText = $reason->Reason;
                }
                $deleted = DeletedCustomer::create();
                $deleted->CustomerID = $member->ID;
                $deleted->ReasonID   = $member->MarkForDeletionReasonID;
                $deleted->ReasonText = $reasonText;
                $deleted->write();
                $member->delete();
                $member->sendDeletionConfirmation();
            }
        } else {
            $this->printInfo("no customers to delete.");
        }
    }
    
    /**
     * Creates example data.
     * 
     * @param int    $count        Total count of records to create
     * @param int    $months       Count of months (past) to use for the creation date
     * @param string $customReason Custom reason text to use
     * 
     * @return void
     */
    protected function createExampleData(int $count = 300, int $months = 24, string $customReason = 'Lorem Ipsum Dolor Sit Amet.') : void
    {
        $this->printInfo("creating {$count} example records.");
        $reasons = DeletedCustomerReason::get()->map('ID', 'Reason')->toArray();
        for ($x = 0; $x < $count; $x++) {
            $this->printProgressInfo($this->getXofY($x+1,$count));
            $reasonID   = rand(0,count($reasons));
            $reasonText = array_key_exists($reasonID, $reasons) ? $reasons[$reasonID] : $customReason;
            $customerID = rand(1,999999999);
            $deleted = DeletedCustomer::create();
            $deleted->CustomerID = $customerID;
            $deleted->ReasonID   = $reasonID;
            $deleted->ReasonText = $reasonText;
            $deleted->Created    = date('Y-m-d H:i:s', rand(time() - ($months*30*24*60*60), time()));
            $deleted->write();
        }
    }
}