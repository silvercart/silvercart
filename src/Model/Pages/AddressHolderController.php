<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Forms\AddAddressForm;
use SilverCart\Forms\EditAddressForm;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Pages\AddressHolder;
use SilverCart\Model\Pages\MyAccountHolderController;
use SilverStripe\Control\HTTPRequest;

/**
 * AddressHolder Controller Class;
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class AddressHolderController extends MyAccountHolderController {

    /**
     * List of allowed actions
     *
     * @var array
     */
    private static $allowed_actions = array (
        'AddAddressForm',
        'EditAddressForm',
        'deleteAddress',
        'setInvoiceAddress',
        'setShippingAddress',
        'addNewAddress',
        'edit',
    );

    /**
     * ID of the requested address
     *
     * @var int 
     */
    protected $addressID = 0;
    
    /**
     * Returns the AddAddressForm.
     * 
     * @return AddAddressForm
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.11.2017
     */
    public function AddAddressForm() {
        $form = new AddAddressForm($this);
        return $form;
    }
    
    /**
     * Returns the EditAddressForm.
     * 
     * @return EditAddressForm
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.11.2017
     */
    public function EditAddressForm() {
        $addressID = $this->getRequest()->postVar('AddressID');
        if (is_null($addressID)) {
            $addressID = $this->getRequest()->param('ID');
        }
        if (!is_numeric($addressID)) {
            return;
        }
        $address = Address::get()->byID($addressID);
        if (!($address instanceof Address) ||
            !$address->exists()) {
            return;
        }
        $form = new EditAddressForm($address, $this);
        return $form;
    }
    
    /**
     * Action to delete an address. Checks, whether the given address is related
     * to the logged in customer and deletes it.
     *
     * @param HTTPRequest $request The given request
     * @param string      $context specifies the context from the action to adjust redirect behaviour
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function deleteAddress(HTTPRequest $request, $context = '') {
        $params = $request->allParams();
        
        if ( array_key_exists('ID', $params) &&
            !empty ($params['ID'])) {

            $addressID          = (int) $params['ID'];
            $member             = Customer::currentUser();
            $membersAddresses   = $member->Addresses();
            $membersAddress     = $membersAddresses->find('ID', $addressID);

            if ($membersAddresses->count() == 1) {
                // address can't be deleted because it's the only one
                $this->setErrorMessage(_t(AddressHolder::class . '.ADDRESS_CANT_BE_DELETED', "Sorry, but you can't delete your only address."));
            } elseif ($membersAddress instanceof Address &&
                      $membersAddress->exists() &&
                      $membersAddress->canDelete()) {
                // Address contains to logged in user - delete it
                if ($member->InvoiceAddress()->ID == $addressID) {
                    // set shipping address as users invoice address
                    $member->InvoiceAddressID = $member->ShippingAddress()->ID;
                    $member->write();
                }
                if ($member->ShippingAddress()->ID == $addressID) {
                    // set invoice address as users shipping address
                    $member->ShippingAddressID = $member->InvoiceAddress()->ID;
                    $member->write();
                }
                $membersAddress->delete();
                $this->setSuccessMessage(_t(AddressHolder::class . '.ADDRESS_SUCCESSFULLY_DELETED', 'Your address was successfully deleted.'));
            } else {
                // possible break in attempt!
                $this->setErrorMessage(_t(AddressHolder::class . '.ADDRESS_NOT_FOUND', 'Sorry, but the given address was not found.'));
            }
        }
        if (!empty($context)) {
            $this->redirectBack();
        } else {
            $this->redirect(Tools::PageByIdentifierCodeLink('SilvercartAddressHolder'));
        }
    }

    /**
     * returns the id of the address requested by the Action.
     *
     * @return int
     */
    public function getAddressID() {
        return $this->addressID;
    }

    /**
     * sets the id of the address requested by the Action.
     *
     * @param int $addressID addressID
     *
     * @return void
     */
    public function setAddressID($addressID) {
        $this->addressID = $addressID;
    }
    
    /**
     * Action to set an address as invoice address.
     *
     * @param HTTPRequest $request The given request
     *
     * @return void
     */
    public function setInvoiceAddress(HTTPRequest $request) {
        $params = $request->allParams();
        if (array_key_exists('ID', $params)
         && !empty ($params['ID'])) {
            $addressID          = (int) $params['ID'];
            $membersAddresses   = Customer::currentUser()->Addresses();
            $membersAddress     = $membersAddresses->find('ID', $addressID);
            if ($membersAddress instanceof Address && $membersAddress->exists()) {
                // Address contains to logged in user - set as invoice address
                $member = Customer::currentUser();
                $member->InvoiceAddressID = $addressID;
                $member->write();
                $this->setSuccessMessage(_t(AddressHolder::class . '.UPDATED_INVOICE_ADDRESS', 'Your invoice addres was successfully updated.'));
            } else {
                // possible break in attempt!
                $this->setErrorMessage(_t(AddressHolder::class . '.ADDRESS_NOT_FOUND', 'Sorry, but the given address was not found.'));
            }
        }
        $this->redirectBack();
    }
    
    /**
     * Action to set an address as shipping address.
     *
     * @param HTTPRequest $request The given request
     *
     * @return void
     */
    public function setShippingAddress(HTTPRequest $request) {
        $params = $request->allParams();
        if (array_key_exists('ID', $params)
         && !empty ($params['ID'])) {
            $addressID          = (int) $params['ID'];
            $membersAddresses   = Customer::currentUser()->Addresses();
            $membersAddress     = $membersAddresses->find('ID', $addressID);
            if ($membersAddress instanceof Address && $membersAddress->exists()) {
                // Address contains to logged in user - set as invoice address
                $member = Customer::currentUser();
                $member->ShippingAddressID = $addressID;
                $member->write();
                $this->setSuccessMessage(_t(AddressHolder::class . '.UPDATED_SHIPPING_ADDRESS', 'Your shipping addres was successfully updated.'));
            } else {
                // possible break in attempt!
                $this->setErrorMessage(_t(AddressHolder::class . '.ADDRESS_NOT_FOUND', 'Sorry, but the given address was not found.'));
            }
        }
        $this->redirectBack();
    }
    
    /**
     * Get the error message out of session and delete it (from session).
     *
     * @return string
     */
    public function getErrorMessage() {
        $errorMessage = Tools::Session()->get('SilvercartAddressHolder.errorMessage');
        Tools::Session()->clear('SilvercartAddressHolder.errorMessage');
        Tools::saveSession();
        return $errorMessage;
    }

    /**
     * Set the error message into the session.
     *
     * @param string $errorMessage Error message
     * 
     * @return void
     */
    public function setErrorMessage($errorMessage) {
        Tools::Session()->set('SilvercartAddressHolder.errorMessage', $errorMessage);
        Tools::saveSession();
    }
    
    /**
     * Get the success message out of session and delete it (from session).
     *
     * @return string
     */
    public function getSuccessMessage() {
        $successMessage = Tools::Session()->get('SilvercartAddressHolder.successMessage');
        Tools::Session()->clear('SilvercartAddressHolder.successMessage');
        Tools::saveSession();
        return $successMessage;
    }

    /**
     * Set the success message into the session.
     *
     * @param string $successMessage Success message
     * 
     * @return void
     */
    public function setSuccessMessage($successMessage) {
        Tools::Session()->set('SilvercartAddressHolder.successMessage', $successMessage);
        Tools::saveSession();
    }

}
