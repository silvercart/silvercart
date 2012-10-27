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
 * @subpackage Forms
 */

/**
 * Customer form for editing an address.
 *
 * @package Silvercart
 * @subpackage Forms
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 19.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.10.2012
     */
    protected function fillInFieldValues() {
        $member = Member::currentUser();
        $id     = $this->customParameters['addressID'];
        
        if ($member && $id) {
            $filter = sprintf("\"MemberID\" = '%s' AND \"ID\" = '%s'", $member->ID, $id);
            $this->address = DataObject::get_one('SilvercartAddress', $filter);
            if ($this->address) {
                $this->formFields['TaxIdNumber']['value']           = $this->address->TaxIdNumber;
                $this->formFields['Company']['value']               = $this->address->Company;
                $this->formFields['Salutation']['selectedValue']    = $this->address->Salutation;
                $this->formFields['FirstName']['value']             = $this->address->FirstName;
                $this->formFields['Surname']['value']               = $this->address->Surname;
                $this->formFields['Addition']['value']              = $this->address->Addition;
                $this->formFields['Street']['value']                = $this->address->Street;
                $this->formFields['StreetNumber']['value']          = $this->address->StreetNumber;
                $this->formFields['Postcode']['value']              = $this->address->Postcode;
                $this->formFields['City']['value']                  = $this->address->City;
                $this->formFields['PhoneAreaCode']['value']         = $this->address->PhoneAreaCode;
                $this->formFields['Phone']['value']                 = $this->address->Phone;
                $this->formFields['Fax']['value']                   = $this->address->Fax;
                $this->formFields['Country']['selectedValue']       = $this->address->SilvercartCountry()->ID;
                if (SilvercartConfig::enablePackstation()) {
                    $this->formFields['PostNumber']['value']            = $this->address->PostNumber;
                    $this->formFields['Packstation']['value']           = $this->address->Packstation;
                    $this->formFields['IsPackstation']['selectedValue'] = $this->address->IsPackstation;
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
     * @since 19.10.2010
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     */
    protected function submitSuccess($data, $form, $formData) {
        $member = Member::currentUser();
        $id = $formData['addressID'];
        if ($member && $id) {
            $filter = sprintf("\"MemberID\" = '%s' AND \"ID\" = '%s'", $member->ID, $id);
            $address = DataObject::get_one('SilvercartAddress', $filter);
            $address->castedUpdate($formData);
            $country = DataObject::get_by_id('SilvercartCountry', $formData['Country']);
            if ($country) {
                $address->SilvercartCountryID = $country->ID;
            }
            $address->write();
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
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 12.04.2011
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
    
}
