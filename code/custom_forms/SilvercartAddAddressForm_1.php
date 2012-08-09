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
 * Customer form for adding an address.
 *
 * @package Silvercart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright pixeltricks GmbH
 * @since 19.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartAddAddressForm extends CustomHtmlForm {

    protected $formFields = array(
        'TaxIdNumber' => array(
            'type'      => 'TextField',
            'title'     => 'Tax ID Number',
            'maxLength' => 30
        ),
        'Company' => array(
            'type'      => 'TextField',
            'title'     => 'Company',
            'maxLength' => 50
        ),
        'Salutation' => array(
            'type' => 'DropdownField',
            'title' => 'Anrede',
            'value' => array(
                '' => 'Bitte wählen',
                'Frau' => 'Frau',
                'Herr' => 'Herr'
            ),
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'FirstName' => array(
            'type'      => 'TextField',
            'title'     => 'Firstname',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Surname' => array(
            'type'      => 'TextField',
            'title'     => 'Surname',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Addition' => array(
            'type'      => 'TextField',
            'title'     => 'Addition'
        ),
        'Street' => array(
            'type'      => 'TextField',
            'title'     => 'Street',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'StreetNumber' => array(
            'type'      => 'TextField',
            'title'     => 'Streetnumber',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Postcode' => array(
            'type'      => 'TextField',
            'title'     => 'Postcode',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'City' => array(
            'type'      => 'TextField',
            'title'     => 'City',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'PhoneAreaCode' => array(
            'type'      => 'TextField',
            'title'     => 'Phone area code',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Phone' => array(
            'type'      => 'TextField',
            'title'     => 'Phone',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'Fax' => array(
            'type'  => 'TextField',
            'title' => 'Fax'
        ),
        'Country' => array(
            'type'      => 'DropdownField',
            'title'     => 'Country',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        )
    );

    /**
     * configure submit button
     */
    protected $preferences = array(
        'submitButtonTitle'  => 'Speichern',
        'markRequiredFields' => true
    );
    
    public $submitSuccess = false;

    /**
     * Indicates wether business customers should be enabled.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.03.2012
     */
    public function EnableBusinessCustomers() {
        return SilvercartConfig::enableBusinessCustomers();
    }

    /**
     * Fill the form with default values
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2011
     */
    protected function fillInFieldValues() {
        $this->CancelLink = $this->controller->Link();
        $this->formFields['TaxIdNumber']['title']   = _t('SilvercartAddress.TAXIDNUMBER');
        $this->formFields['Company']['title']       = _t('SilvercartAddress.COMPANY');
        $this->formFields['Salutation']['title']    = _t('SilvercartAddress.SALUTATION');
        $this->formFields['Salutation']['value']    = array('' => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE'), "Frau" => _t('SilvercartAddress.MISSES'), "Herr" => _t('SilvercartAddress.MISTER'));
        $this->formFields['FirstName']['title']     = _t('SilvercartAddress.FIRSTNAME');
        $this->formFields['Surname']['title']       = _t('SilvercartAddress.SURNAME');
        $this->formFields['Addition']['title']      = _t('SilvercartAddress.ADDITION');
        $this->formFields['Street']['title']        = _t('SilvercartAddress.STREET');
        $this->formFields['StreetNumber']['title']  = _t('SilvercartAddress.STREETNUMBER');
        $this->formFields['Postcode']['title']      = _t('SilvercartAddress.POSTCODE');
        $this->formFields['City']['title']          = _t('SilvercartAddress.CITY');
        $this->formFields['Phone']['title']         = _t('SilvercartAddress.PHONE');
        $this->formFields['PhoneAreaCode']['title'] = _t('SilvercartAddress.PHONEAREACODE');
        $this->formFields['Fax']['title']           = _t('SilvercartAddress.FAX');
        $this->formFields['Country']['title']       = _t('SilvercartCountry.SINGULARNAME');
        $this->formFields['Country']['value']       = DataObject::get('SilvercartCountry', "`SilvercartCountry`.`Active`=1")->toDropdownMap('Title', 'Title', _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE', '--please choose--'));
        $this->preferences['submitButtonTitle']     = _t('SilvercartPage.SAVE', 'save');
    }

    /**
     * This method will be call if there are no validation error
     *
     * @param SS_HTTPRequest $data     input data
     * @param Form           $form     form object
     * @param array          $formData secured form data
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2011
     */
    protected function submitSuccess($data, $form, $formData) {
        $member = Member::currentUser();
        if ($member) {            
            $filter = sprintf("`Title` = '%s'", $formData['Country']);
            $country = DataObject::get_one('SilvercartCountry', $filter);
            if ($country) {
                $formData['SilvercartCountryID'] = $country->ID;
            }
            $formData['MemberID'] = $member->ID;
            
            $address = new SilvercartAddress();
            $address->write();
            $address->update($formData);
            $address->write();
            $this->submitSuccess = true;
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
