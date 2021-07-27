<?php

namespace SilverCart\Model\Pages;

use SilverCart\Forms\EditProfileForm;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Customer\DeletedCustomerReason;
use SilverCart\Model\Pages\MyAccountHolderController;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\ORM\DataList;
use SilverStripe\Security\Member;

/**
 * CustomerDataPage Controller class.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CustomerDataPageController extends MyAccountHolderController {
    
    /**
     * List of allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = array(
        'EditProfileForm',
        'delete_account',
        'undo_delete_account',
    );

    /**
     * Action to mark a customer account for deletion.
     *
     * @return HTTPResponse
     */
    public function delete_account(HTTPRequest $request) : HTTPResponse
    {
        $error    = '';
        $reason   = $request->postVar('Reason');
        $email    = $request->postVar('Email');
        $password = $request->postVar('Password');
        $confirm  = $request->postVar('ConfirmDeleteAccount') === "1";
        if ($confirm) {
            $customer = Customer::currentRegisteredCustomer();
            if (!($customer) instanceof Member
             || !$customer->exists()
            ) {
                $this->httpError(403);
            }
            if ($customer->Email !== $email) {
                $error = _t('SilverCart.DeleteYourAccountErrorPassword', 'Sorry, but the login data you entered is not correct.');
            } else {
                $result = $customer->checkPassword($password);
                if ($result->isValid()) {
                    $reasonText   = $request->postVar('CustomReason');
                    $reasonObject = DeletedCustomerReason::get()->byID((int) $reason);
                    if ($reasonObject instanceof DeletedCustomerReason) {
                        $reasonText = $reasonObject->Reason;
                    }
                    $customer->MarkForDeletion         = true;
                    $customer->MarkForDeletionDate     = date('Y-m-d');
                    $customer->MarkForDeletionReason   = $reasonText;
                    $customer->MarkForDeletionReasonID = (int) $reason;
                    $customer->write();
                } else {
                    $error = _t('SilverCart.DeleteYourAccountErrorPassword', 'Sorry, but the login data you entered is not correct.');
                }
            }
        } else {
            $error = _t('SilverCart.DeleteYourAccountErrorConfirm', 'Please confirm the deletion of your account by checking the checkbox.');
        }
        return HTTPResponse::create($this->render([
            'Error'  => $error,
            'Reason' => $reason,
        ]));
    }

    /**
     * Action to unmark a customer account for deletion.
     *
     * @return HTTPResponse
     */
    public function undo_delete_account(HTTPRequest $request) : HTTPResponse
    {
        $customer = Customer::currentRegisteredCustomer();
        if (!($customer) instanceof Member
         || !$customer->exists()
        ) {
            $this->httpError(403);
        }
        $customer->MarkForDeletion = false;
        $customer->write();
        return HTTPResponse::create($this->render());
    }

    /**
     * Returns the EditProfileForm.
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.11.2017
     */
    public function EditProfileForm() {
        $form = new EditProfileForm($this);
        return $form;
    }
    
    /**
     * Returns the available reasons to delete a customer.
     * 
     * @return DataList
     */
    public function DeletedCustomerReasons() : DataList
    {
        return DeletedCustomerReason::get();
    }
}