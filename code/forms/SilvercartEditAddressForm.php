<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Forms
 */

/**
 * Customer form for editing an address.
 *
 * @package Silvercart
 * @subpackage Forms
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 19.10.2010
 * @license see license file in modules root directory
 */
class SilvercartEditAddressForm extends SilvercartAddressForm {

    /**
     * Contains the address object
     *
     * @var SilvercartAddress
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 11.04.2011
     */
    protected $address;
    
    /**
     * Sets the preferences for this form
     * 
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.10.2012
     */
    public function preferences() {
        parent::preferences();
        if ($this->controller->class == 'SilvercartCheckoutStep_Controller') {
            $this->CancelLink = $this->controller->Link();
        } else {
            $this->CancelLink = $this->controller->Parent()->Link();
        }
    }

    /**
     * Fill the form with default values
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 17.08.2018
     */
    protected function fillInFieldValues() {
        $member = SilvercartCustomer::currentUser();
        $id     = $this->customParameters['addressID'];
        
        if ($member && $id) {
            $filter = array(
                "MemberID" => $member->ID,
                "ID"       => $id
            );
            $this->address = SilvercartAddress::get()->filter($filter)->first();
            if ($this->address) {
                foreach (array_keys($this->address->db()) as $dbFieldName) {
                    if (array_key_exists($dbFieldName, $this->formFields)) {
                        if ($this->formFields[$dbFieldName]['type'] == 'DropdownField') {
                            $this->formFields[$dbFieldName]['selectedValue'] = $this->address->{$dbFieldName};
                        } else {
                            $this->formFields[$dbFieldName]['value'] = $this->address->{$dbFieldName};
                        }
                    }
                }
                if (array_key_exists('Country', $this->formFields)) {
                    $this->formFields['Country']['selectedValue'] = $this->address->SilvercartCountry()->ID;
                }
                if (!$this->EnablePackstation()) {
                    unset($this->formFields['PostNumber']);
                    unset($this->formFields['Packstation']);
                }
                if (!$this->EnableBusinessCustomers()) {
                    unset($this->formFields['Company']);
                    unset($this->formFields['TaxIdNumber']);
                }
            }
        }
    }

    /**
     * executed if there are no valdation errors on submit
     * Form data is saved in session
     *
     * @param SS_HTTPRequest $data     contains the frameworks form data
     * @param Form           $form     not used
     * @param array          $formData contains the modules form data
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 15.11.2014
     */
    protected function submitSuccess($data, $form, $formData) {
        $member = SilvercartCustomer::currentUser();
        $id = $formData['addressID'];
        if ($member && $id) {
            $filter = array(
                "MemberID" => $member->ID,
                "ID"       => $id
            );
            $address = SilvercartAddress::get()->filter($filter)->first();
            if ($address->canEdit()) {
                $address->castedUpdate($formData);
                $country = SilvercartCountry::get()->byID($formData['Country']);
                if ($country) {
                    $address->SilvercartCountryID = $country->ID;
                }
                $address->write();
            }
            $this->submitSuccess = true;
            if (Session::get("redirect")) {
                $this->controller->redirect(Session::get("redirect"));
                Session::clear("redirect");
            } else {
                $addressHolder = SilvercartPage_Controller::PageByIdentifierCode("SilvercartAddressHolder");
                $this->controller->redirect($addressHolder->RelativeLink());
            }
        }
    }

    /**
     * Returns the title of the current form.
     *
     * @return string
     */
    public function getAddressFormTitle() {
        $title = '';

        if ($this->address->ClassName == 'SilvercartInvoiceAddress') {
            $title = _t('SilvercartAddress.EDITINVOICEADDRESS');
        } else {
            $title = _t('SilvercartAddress.EDITSHIPPINGADDRESS');
        }

        return $title;
    }

    /**
     * Returns the context address.
     * 
     * @return SilvercartAddress
     */
    public function getAddress() {
        $member = SilvercartCustomer::currentUser();
        $id     = $this->customParameters['addressID'];
        if ($member && $id) {
            $filter = array(
                "MemberID" => $member->ID,
                "ID"       => $id
            );
            $this->address = SilvercartAddress::get()->filter($filter)->first();
        }
        return $this->address;
    }
}
