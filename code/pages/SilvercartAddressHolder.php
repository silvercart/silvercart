<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * Child of customer area; overview of all addresses;
 *
 * @package Silvercart
 * @subpackage Pages
 * @copyright 2010 pixeltricks GmbH
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 16.02.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartAddressHolder extends SilvercartMyAccountHolder {

    public static $singular_name = "";
    public static $can_be_root = false;
    public static $allowed_children = array(
        "SilvercartAddressPage"
    );
    
    /**
     * The icon to use for this page in the storeadmin sitetree.
     *
     * @var string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.10.2011
     */
    public static $icon = "silvercart/images/page_icons/my_account_holder";

    /**
     * Return all fields of the backend
     *
     * @return FieldSet Fields of the CMS
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 3.11.2010
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
            $parts = explode(self::$breadcrumbs_delimiter, $breadcrumbs);
            $addressHolder = array_pop($parts);
            $parts[] = ("<a href=\"" . $this->Link() . "\">" . $addressHolder . "</a>");
            $parts[] = _t('SilvercartAddressHolder.ADD', 'Add new address');
            $breadcrumbs = implode(self::$breadcrumbs_delimiter, $parts);
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
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartAddressHolder_Controller extends SilvercartMyAccountHolder_Controller {

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
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2011
     */
    public function deleteAddress(SS_HTTPRequest $request) {
        $params = $request->allParams();

        if ( array_key_exists('ID', $params) &&
            !empty ($params['ID'])) {

            $addressID = (int) $params['ID'];

            if (Member::currentUser()->SilvercartAddresses()->Count() == 1) {
                // address can't be deleted because it's the only one
                $this->setErrorMessage(_t('SilvercartAddressHolder.ADDRESS_CANT_BE_DELETED', "Sorry, but you can't delete your only address."));
            } elseif (Member::currentUser()->SilvercartAddresses()->containsIDs(array($addressID))) {
                // Address contains to logged in user - delete it
                if (Member::currentUser()->SilvercartInvoiceAddress()->ID == $addressID) {
                    // set shipping address as users invoice address
                    Member::currentUser()->SilvercartInvoiceAddressID = Member::currentUser()->SilvercartShippingAddress()->ID;
                    Member::currentUser()->write();
                }
                if (Member::currentUser()->SilvercartShippingAddress()->ID == $addressID) {
                    // set invoice address as users shipping address
                    Member::currentUser()->SilvercartShippingAddressID = Member::currentUser()->SilvercartInvoiceAddress()->ID;
                    Member::currentUser()->write();
                }
                DataObject::get_by_id('SilvercartAddress', $addressID)->delete();
                $this->setSuccessMessage(_t('SilvercartAddressHolder.ADDRESS_SUCCESSFULLY_DELETED', 'Your address was successfully deleted.'));
            } else {
                // possible break in attempt!
                $this->setErrorMessage(_t('SilvercartAddressHolder.ADDRESS_NOT_FOUND', 'Sorry, but the given address was not found.'));
            }
        }
        Director::redirectBack();
    }
    
    /**
     * Action to set an address as invoice address.
     *
     * @param SS_HTTPRequest $request The given request
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2011
     */
    public function setInvoiceAddress(SS_HTTPRequest $request) {
        $params = $request->allParams();
        if (array_key_exists('ID', $params)
         && !empty ($params['ID'])) {
            $addressID = (int) $params['ID'];
            if (Member::currentUser()->SilvercartAddresses()->containsIDs(array($addressID))) {
                // Address contains to logged in user - set as invoice address
                Member::currentUser()->SilvercartInvoiceAddressID = $addressID;
                Member::currentUser()->write();
                $this->setSuccessMessage(_t('SilvercartAddressHolder.UPDATED_INVOICE_ADDRESS', 'Your invoice addres was successfully updated.'));
            } else {
                // possible break in attempt!
                $this->setErrorMessage(_t('SilvercartAddressHolder.ADDRESS_NOT_FOUND', 'Sorry, but the given address was not found.'));
            }
        }
        Director::redirectBack();
    }
    
    /**
     * Action to set an address as shipping address.
     *
     * @param SS_HTTPRequest $request The given request
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2011
     */
    public function setShippingAddress(SS_HTTPRequest $request) {
        $params = $request->allParams();
        if (array_key_exists('ID', $params)
         && !empty ($params['ID'])) {
            $addressID = (int) $params['ID'];
            if (Member::currentUser()->SilvercartAddresses()->containsIDs(array($addressID))) {
                // Address contains to logged in user - set as invoice address
                Member::currentUser()->SilvercartShippingAddressID = $addressID;
                Member::currentUser()->write();
                $this->setSuccessMessage(_t('SilvercartAddressHolder.UPDATED_SHIPPING_ADDRESS', 'Your shipping addres was successfully updated.'));
            } else {
                // possible break in attempt!
                $this->setErrorMessage(_t('SilvercartAddressHolder.ADDRESS_NOT_FOUND', 'Sorry, but the given address was not found.'));
            }
        }
        Director::redirectBack();
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
