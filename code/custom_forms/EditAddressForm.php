<?php

/**
 * Customer form for editing an address.
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 19.10.2010
 * @license BSD
 */
class EditAddressForm extends CustomHtmlForm {

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
        $this->formFields['FirstName']['title'] = _t('Address.FIRSTNAME');
        $this->formFields['Surname']['title'] = _t('Address.SURNAME');
        $this->formFields['Addition']['title'] = _t('Address.ADDITION');
        $this->formFields['Street']['title'] = _t('Address.STREET');
        $this->formFields['StreetNumber']['title'] = _t('Address.STREETNUMBER');
        $this->formFields['Postcode']['title'] = _t('Address.POSTCODE');
        $this->formFields['City']['title'] = _t('Address.CITY');
        $this->formFields['Phone']['title'] = _t('Address.PHONE');
        $this->formFields['PhoneAreaCode']['title'] = _t('Address.PHONEAREACODE');
        $this->formFields['Country']['title'] = _t('Country.SINGULARNAME');
        
        $this->preferences['submitButtonTitle'] = _t('Page.SAVE', 'save');

        $member = Member::currentUser();
        $id = Controller::curr()->urlParams['ID'];

        if ($member && $id) {
            $filter = sprintf("\"ownerID\" = '%s' AND \"ID\" = '%s'", $member->ID, $id);
            $address = DataObject::get_one('Address', $filter);
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
                $this->formFields['Country']['value'] = DataObject::get('Country')->toDropdownMap('Title', 'Title', _t('EditAddressForm.EMPTYSTRING_PLEASECHOOSE', '--please choose--'));
                $this->formFields['Country']['selectedValue'] = $address->country()->Title;
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
        $id = Session::get('addressID');
        if ($member && $id) {
            $filter = sprintf("\"ownerID\" = '%s' AND \"ID\" = '%s'", $member->ID, $id);
            $address = DataObject::get_one('Address', $filter);
            $address->castedUpdate($registrationData);
            $filter = sprintf("`Title` = '%s'", $registrationData['Country']);
            $country = DataObject::get_one('Country', $filter);
            if ($country) {
                $address->countryID = $country->ID;
            }
            $address->write();
            if (Session::get("redirect")) {
                Director::redirect(Session::get("redirect"));
                Session::clear("redirect");
            } else {
                Director::redirect('/my-account/address-overview/');
            }
        }
    }
}

