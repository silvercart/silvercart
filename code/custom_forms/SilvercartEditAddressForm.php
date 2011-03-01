<?php
/*
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
class SilvercartEditAddressForm extends CustomHtmlForm {

    protected $formFields = array(
        'FirstName' => array(
            'type' => 'TextField',
            'title' => 'Vorname des Empfängers'
        ),
        'Surname' => array(
            'type' => 'TextField',
            'title' => 'Nachname des Empfängers'
        ),
        'Addition' => array(
            'type' => 'TextField',
            'title' => 'Adresszusatz'
        ),
        'Street' => array(
            'type' => 'TextField',
            'title' => 'Straße',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'StreetNumber' => array(
            'type' => 'TextField',
            'title' => 'Hausnummer',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Postcode' => array(
            'type' => 'TextField',
            'title' => 'PLZ',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'City' => array(
            'type' => 'TextField',
            'title' => 'Ort',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'PhoneAreaCode' => array(
            'type' => 'TextField',
            'title' => 'Vorwahl',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Phone' => array(
            'type' => 'TextField',
            'title' => 'Telefonnummer',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Country' => array(
            'type' => 'DropdownField',
            'title' => 'Land',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        )
    );

    /**
     * Fill the form with default values
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.10.2010
     * @return void
     */
    protected function fillInFieldValues() {
        $this->formFields['FirstName']['title'] = _t('SilvercartAddress.FIRSTNAME');
        $this->formFields['Surname']['title'] = _t('SilvercartAddress.SURNAME');
        $this->formFields['Addition']['title'] = _t('SilvercartAddress.ADDITION');
        $this->formFields['Street']['title'] = _t('SilvercartAddress.STREET');
        $this->formFields['StreetNumber']['title'] = _t('SilvercartAddress.STREETNUMBER');
        $this->formFields['Postcode']['title'] = _t('SilvercartAddress.POSTCODE');
        $this->formFields['City']['title'] = _t('SilvercartAddress.CITY');
        $this->formFields['Phone']['title'] = _t('SilvercartAddress.PHONE');
        $this->formFields['PhoneAreaCode']['title'] = _t('SilvercartAddress.PHONEAREACODE');
        $this->formFields['Country']['title'] = _t('SilvercartCountry.SINGULARNAME');
        
        $this->preferences['submitButtonTitle'] = _t('SilvercartPage.SAVE', 'save');

        $member = Member::currentUser();
        $id = $this->controller->getAddressID();
        
        if ($member && $id) {
            $filter = sprintf("`MemberID` = '%s' AND `ID` = '%s'", $member->ID, $id);
            $address = DataObject::get_one('SilvercartAddress', $filter);
            if ($address) {
                $this->formFields['FirstName']['value'] = $member->FirstName;
                $this->formFields['Surname']['value'] = $member->Surname;
                $this->formFields['Addition']['value'] = $address->Addition;
                $this->formFields['Street']['value'] = $address->Street;
                $this->formFields['StreetNumber']['value'] = $address->StreetNumber;
                $this->formFields['Postcode']['value'] = $address->Postcode;
                $this->formFields['City']['value'] = $address->City;
                $this->formFields['PhoneAreaCode']['value'] = $address->PhoneAreaCode;
                $this->formFields['Phone']['value'] = $address->Phone;
                $this->formFields['Country']['value'] = DataObject::get('SilvercartCountry')->toDropdownMap('Title', 'Title', _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE', '--please choose--'));
                $this->formFields['Country']['selectedValue'] = $address->SilvercartCountry()->Title;
            }
        }
    }

    /**
     * configure submit button
     */
    protected $preferences = array(
        'submitButtonTitle' => 'Speichern'
    );

    /**
     * executed if there are no valdation errors on submit
     * Form data is saved in session
     *
     * @param SS_HTTPRequest $data             contains the frameworks form data
     * @param Form           $form             not used
     * @param array          $registrationData contains the modules form data
     *
     * @return void
     * @since 19.10.2010
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     */
    protected function submitSuccess($data, $form, $registrationData) {
        $member = Member::currentUser();
        $id = $registrationData['addressID'];
        if ($member && $id) {
            $filter = sprintf("`MemberID` = '%s' AND `ID` = '%s'", $member->ID, $id);
            $address = DataObject::get_one('SilvercartAddress', $filter);
            $address->castedUpdate($registrationData);
            $filter = sprintf("`Title` = '%s'", $registrationData['Country']);
            $country = DataObject::get_one('SilvercartCountry', $filter);
            if ($country) {
                $address->SilvercartCountryID = $country->ID;
            }
            $address->write();
            if (Session::get("redirect")) {
                Director::redirect(Session::get("redirect"));
                Session::clear("redirect");
            } else {
                $addressHolder = SilvercartPage_Controller::PageByIdentifierCode("SilvercartAddressHolder");
                Director::redirect($addressHolder->RelativeLink());
            }
        }
    }
}

