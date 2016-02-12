<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * Child of customer area; overview of all addresses;
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.04.2013
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartAddressHolder extends SilvercartMyAccountHolder {
    
    /**
     * Indicates whether this page type can be root
     *
     * @var bool
     */
    public static $can_be_root = false;
    
    /**
     * list of allowed children page types
     *
     * @var array
     */
    public static $allowed_children = array(
        "SilvercartAddressPage"
    );
    
    /**
     * The icon to use for this page in the storeadmin sitetree.
     *
     * @var string
     */
    public static $icon = "silvercart/img/page_icons/my_account_holder";

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
    }
    
    /**
     * Returns whether this page has a summary.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.04.2013
     */
    public function hasSummary() {
        return true;
    }
    
    /**
     * Returns the summary of this page.
     * 
     * @return string
     */
    public function getSummary() {
        return $this->renderWith('SilvercartAddressSummary');
    }
    
    /**
     * Returns the summary of this page.
     * 
     * @return string
     */
    public function getSummaryTitle() {
        return _t('SilvercartMyAccountHolder.YOUR_CURRENT_ADDRESSES');
    }
    
    /**
     * Return all fields of the backend
     *
     * @return FieldList Fields of the CMS
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        return $fields;
    }

    /**
     * configure the class name of the DataObjects to be shown on this page
     * this is needed to show correct breadcrumbs
     *
     * @return string class name of the DataObject to be shown on this page
     */
    public function getSection() {
        return 'SilvercartAddress';
    }
    
    /**
     * Adds the part for 'Add new address' to the breadcrumbs. Sets the link for
     * The default action in breadcrumbs.
     *
     * @param int  $maxDepth       maximum levels
     * @param bool $unlinked       link breadcrumbs elements
     * @param bool $stopAtPageType name of PageType to stop at
     * @param bool $showHidden     show pages that will not show in menus
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2011
     */
    public function Breadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
        $breadcrumbs = parent::Breadcrumbs($maxDepth, $unlinked, $stopAtPageType, $showHidden);
        if (Controller::curr()->getAction() == 'addNewAddress') {
            $parts = explode(" &raquo; ", $breadcrumbs);
            $addressHolder = array_pop($parts);
            $parts[] = ("<a href=\"" . $this->Link() . "\">" . $addressHolder . "</a>");
            $parts[] = _t('SilvercartAddressHolder.ADD', 'Add new address');
            $breadcrumbs = implode(" &raquo; ", $parts);
        }
        return $breadcrumbs;
    }

}

/**
 * Controller Class
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 16.02.2011
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartAddressHolder_Controller extends SilvercartMyAccountHolder_Controller {

    /**
     * List of allowed actions
     *
     * @var array
     */
    public static $allowed_actions = array (
        'deleteAddress',
        'setInvoiceAddress',
        'setShippingAddress',
        'addNewAddress',
    );

    /**
     * execute these statements on object call
     *
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.10.2010
     */
    public function init() {
        parent::init();
        $preferences = array();
        $urlParams = $this->getURLParams();
        if ($urlParams['Action'] == 'addNewAddress') {
            $preferences['submitAction'] = 'addNewAddress/customHtmlFormSubmit';
        }
        $this->registerCustomHtmlForm('SilvercartAddAddressForm', new SilvercartAddAddressForm($this, null, $preferences));
    }
    
    /**
     * Action to delete an address. Checks, whether the given address is related
     * to the logged in customer and deletes it.
     *
     * @param SS_HTTPRequest $request The given request
     * @param string         $context specifies the context from the action to adjust redirect behaviour
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function deleteAddress(SS_HTTPRequest $request, $context = '') {
        $params = $request->allParams();
        
        if ( array_key_exists('ID', $params) &&
            !empty ($params['ID'])) {

            $addressID          = (int) $params['ID'];
            $member             = SilvercartCustomer::currentUser();
            $membersAddresses   = $member->SilvercartAddresses();
            $membersAddress     = $membersAddresses->find('ID', $addressID);

            if ($membersAddresses->count() == 1) {
                // address can't be deleted because it's the only one
                $this->setErrorMessage(_t('SilvercartAddressHolder.ADDRESS_CANT_BE_DELETED', "Sorry, but you can't delete your only address."));
            } elseif ($membersAddress instanceof SilvercartAddress && $membersAddress->exists()) {
                // Address contains to logged in user - delete it
                if ($member->SilvercartInvoiceAddress()->ID == $addressID) {
                    // set shipping address as users invoice address
                    $member->SilvercartInvoiceAddressID = $member->SilvercartShippingAddress()->ID;
                    $member->write();
                }
                if ($member->SilvercartShippingAddress()->ID == $addressID) {
                    // set invoice address as users shipping address
                    $member->SilvercartShippingAddressID = $member->SilvercartInvoiceAddress()->ID;
                    $member->write();
                }
                $membersAddress->delete();
                $this->setSuccessMessage(_t('SilvercartAddressHolder.ADDRESS_SUCCESSFULLY_DELETED', 'Your address was successfully deleted.'));
            } else {
                // possible break in attempt!
                $this->setErrorMessage(_t('SilvercartAddressHolder.ADDRESS_NOT_FOUND', 'Sorry, but the given address was not found.'));
            }
        }
        if (!empty($context)) {
            $this->redirectBack();
        } else {
            $this->redirect(SilvercartTools::PageByIdentifierCodeLink('SilvercartAddressHolder'));
        }
    }
    
    /**
     * Action to set an address as invoice address.
     *
     * @param SS_HTTPRequest $request The given request
     *
     * @return void
     */
    public function setInvoiceAddress(SS_HTTPRequest $request) {
        $params = $request->allParams();
        if (array_key_exists('ID', $params)
         && !empty ($params['ID'])) {
            $addressID          = (int) $params['ID'];
            $membersAddresses   = SilvercartCustomer::currentUser()->SilvercartAddresses();
            $membersAddress     = $membersAddresses->find('ID', $addressID);
            if ($membersAddress instanceof SilvercartAddress && $membersAddress->exists()) {
                // Address contains to logged in user - set as invoice address
                $member = SilvercartCustomer::currentUser();
                $member->SilvercartInvoiceAddressID = $addressID;
                $member->write();
                $this->setSuccessMessage(_t('SilvercartAddressHolder.UPDATED_INVOICE_ADDRESS', 'Your invoice addres was successfully updated.'));
            } else {
                // possible break in attempt!
                $this->setErrorMessage(_t('SilvercartAddressHolder.ADDRESS_NOT_FOUND', 'Sorry, but the given address was not found.'));
            }
        }
        $this->redirectBack();
    }
    
    /**
     * Action to set an address as shipping address.
     *
     * @param SS_HTTPRequest $request The given request
     *
     * @return void
     */
    public function setShippingAddress(SS_HTTPRequest $request) {
        $params = $request->allParams();
        if (array_key_exists('ID', $params)
         && !empty ($params['ID'])) {
            $addressID          = (int) $params['ID'];
            $membersAddresses   = SilvercartCustomer::currentUser()->SilvercartAddresses();
            $membersAddress     = $membersAddresses->find('ID', $addressID);
            if ($membersAddress instanceof SilvercartAddress && $membersAddress->exists()) {
                // Address contains to logged in user - set as invoice address
                $member = SilvercartCustomer::currentUser();
                $member->SilvercartShippingAddressID = $addressID;
                $member->write();
                $this->setSuccessMessage(_t('SilvercartAddressHolder.UPDATED_SHIPPING_ADDRESS', 'Your shipping addres was successfully updated.'));
            } else {
                // possible break in attempt!
                $this->setErrorMessage(_t('SilvercartAddressHolder.ADDRESS_NOT_FOUND', 'Sorry, but the given address was not found.'));
            }
        }
        $this->redirectBack();
    }
    
    /**
     * Action to add a new address.
     *
     * @param SS_HTTPRequest $request The given request
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2011
     */
    public function addNewAddress(SS_HTTPRequest $request) {
        $params = $request->allParams();
        if (strtolower($params['ID']) == 'customhtmlformsubmit') {
            $result = $this->CustomHtmlFormSubmit($request);
            $form = $this->getRegisteredCustomHtmlForm('SilvercartAddAddressForm');
            if ($form->submitSuccess) {
                $this->setSuccessMessage(_t('SilvercartAddressHolder.ADDED_ADDRESS_SUCCESS', 'Your address was successfully saved.'));
                return;
            } else {
                $this->setErrorMessage(_t('SilvercartAddressHolder.ADDED_ADDRESS_FAILURE', 'Your address could not be saved.'));
            }
        }
        return $this->renderWith(array('SilvercartAddNewAddress', 'Page'));
    }
    
    /**
     * Get the error message out of session and delete it (from session).
     *
     * @return string
     */
    public function getErrorMessage() {
        $errorMessage = Session::get('SilvercartAddressHolder.errorMessage');
        Session::clear('SilvercartAddressHolder.errorMessage');
        Session::save();
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
        Session::set('SilvercartAddressHolder.errorMessage', $errorMessage);
        Session::save();
    }
    
    /**
     * Get the success message out of session and delete it (from session).
     *
     * @return string
     */
    public function getSuccessMessage() {
        $successMessage = Session::get('SilvercartAddressHolder.successMessage');
        Session::clear('SilvercartAddressHolder.successMessage');
        Session::save();
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
        Session::set('SilvercartAddressHolder.successMessage', $successMessage);
        Session::save();
    }



}
