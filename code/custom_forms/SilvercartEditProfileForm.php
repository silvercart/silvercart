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
 * A form to manipulate a customers profile
 *
 * @package Silvercart
 * @subpackage Forms
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 22.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartEditProfileForm extends CustomHtmlForm {

    protected $formFields = array
        (
        'Salutation' => array(
            'type' => 'DropdownField',
            'title' => 'Anrede',
            'value' => array('' => 'Bitte wählen', 'Frau' => 'Frau', 'Herr' => 'Herr'),
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'FirstName' => array
        (
                'type' => 'TextField',
                'title' => 'Vorname',
                'checkRequirements' => array
                (
                        'isFilledIn' => true,
                        'hasMinLength' => 3
                )
        ),
        'Surname' => array
        (
                'type' => 'TextField',
                'title' => 'Nachname',
                'checkRequirements' => array
                (
                        'isFilledIn' => true,
                        'hasMinLength' => 3
                )
        ),
        'Email' => array(
                'type' => 'TextField',
                'title' => 'Email Adresse',
                'checkRequirements' => array(
                        'isEmailAddress'    => true,
                        'isFilledIn'        => true
                )
        ),
        'BirthdayDay' => array(
            'type' => 'DropdownField',
            'title' => 'Tag',
            'value' => array('' => 'Bitte wählen', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20', '20' => '21', '22' => 22, '23' => 23, '24' => '24', '25' => '25', '26' => '26', '27' => '27', '28' => '28', '29' => '29', '30' => '30', '31' => '31'),
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'BirthdayMonth' => array(
            'type' => 'DropdownField',
            'title' => 'Monat',
            'value' => array('' => 'Bitte wählen', '1' => 'Januar', '2' => 'Februar', '3' => 'März', '4' => 'April', '5' => 'Mai', '6' => 'Juni', '7' => 'Juli', '8' => 'August', '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Dezember'),
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'BirthdayYear' => array(
            'type' => 'TextField',
            'title' => 'Jahr',
            'maxLength' => 4,
            'checkRequirements' => array(
                'isFilledIn' => true,
                'isNumbersOnly' => true,
                'hasLength' => 4
            )
        ),
        'Password' => array
        (
                'type' => 'PasswordField',
                'title' => 'Passwort',
                'checkRequirements' => array
                (
                        'hasMinLength' => 6
                )
        ),
        'PasswordCheck' => array
        (
                'type' => 'PasswordField',
                'title' => 'Passwort Gegenprüfung',
                'checkRequirements' => array
                (
                        'mustEqual' => 'Password'
                )
        ),
        'SubscribedToNewsletter' => array
        (       'title' => "Ich m&ouml;chte &uuml;ber neue Aktionen oder Veranstaltungen von Pour LA Table informiert werden",
                'type' => 'CheckboxField'
        )
    );

    /**
     * form preferences
     *
     * @var array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.10.2010
     * @return void
     */
    protected $preferences = array(
        'submitButtonTitle' => 'Speichern'
    );

    /**
     * Set initial form values
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.10.2010
     * @return void
     */
    protected function fillInFieldValues() {
        $this->formFields['Salutation']['title'] = _t('SilvercartAddress.SALUTATION', 'salutation');
        $this->formFields['Salutation']['value'] = array('' => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE'), "Frau" => _t('SilvercartAddress.MISSES'), "Herr" => _t('SilvercartAddress.MISTER'));
        $this->formFields['FirstName']['title'] = _t('SilvercartAddress.FIRSTNAME', 'firstname');
        $this->formFields['Surname']['title'] = _t('SilvercartAddress.SURNAME', 'surname');
        $this->formFields['Email']['title'] = _t('SilvercartAddress.EMAIL');
        $this->formFields['BirthdayDay']['title'] = _t('SilvercartPage.DAY', 'day');
        $this->formFields['BirthdayDay']['value'] = array('' => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE'), '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20', '20' => '21', '22' => 22, '23' => 23, '24' => '24', '25' => '25', '26' => '26', '27' => '27', '28' => '28', '29' => '29', '30' => '30', '31' => '31');
        $this->formFields['BirthdayMonth']['title'] = _t('SilvercartPage.MONTH', 'month');
        $this->formFields['BirthdayMonth']['value'] = array('' => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE'), '1' => _t('SilvercartPage.JANUARY', 'january'), '2' => _t('SilvercartPage.FEBRUARY', 'february'), '3' => _t('SilvercartPage.MARCH', 'march'), '4' => _t('SilvercartPage.APRIL', 'april'), '5' => _t('SilvercartPage.MAY', 'may'), '6' => _t('SilvercartPage.JUNE', 'june'), '7' => _t('SilvercartPage.JULY', 'july'), '8' => _t('SilvercartPage.AUGUST', 'august'), '9' => _t('SilvercartPage.SEPTEMBER', 'september'), '10' => _t('SilvercartPage.OCTOBER', 'october'), '11' => _t('SilvercartPage.NOVEMBER', 'november'), '12' => _t('SilvercartPage.DECEMBER', 'december'));
        $this->formFields['BirthdayYear']['title'] = _t('SilvercartPage.YEAR', 'year');
        $this->formFields['Password']['title'] = _t('SilvercartPage.PASSWORD');
        $this->formFields['PasswordCheck']['title'] = _t('SilvercartPage.PASSWORD_CHECK', 'password check');
        $this->formFields['SubscribedToNewsletter']['title'] = _t('SilvercartCheckoutFormStep.I_SUBSCRIBE_NEWSLETTER');
        $this->preferences['submitButtonTitle'] = _t('SilvercartPage.SAVE');

        $member = Member::currentUser();
        #var_dump(date('d', strtotime($member->Birthday)));die();
        if ($member) {
            $this->formFields['Salutation']['selectedValue'] = $member->Salutation;
            $this->formFields['FirstName']['value'] = $member->FirstName;
            $this->formFields['Surname']['value'] = $member->Surname;

            if ($member->Birthday) {
                $this->formFields['BirthdayDay']['selectedValue'] = date('d', strtotime($member->Birthday));
                $this->formFields['BirthdayMonth']['selectedValue'] = date('m', strtotime($member->Birthday));
                $this->formFields['BirthdayYear']['value'] = date('Y', strtotime($member->Birthday));
            }

            if ($member->Email) {
                $this->formFields['Email']['value'] = $member->Email;
            }

            $this->formFields['SubscribedToNewsletter']['value'] = $member->SubscribedToNewsletter;

        }
    }

    /**
     * executed if there are no valdation errors on submit
     * Form data is saved in session
     *
     * @param SS_HTTPRequest $data             contains the frameworks form data
     * @param Form           $form             not used
     * @param array          $registrationData contains the modules form data
     *
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.10.2010
     */
    protected function submitSuccess($data, $form, $registrationData) {
        $member = Member::currentUser();

        // -------------------------------------------------------------------
        // process data
        // -------------------------------------------------------------------
        // Password
        unset($registrationData['PasswordCheck']);
        if (empty($registrationData['Password'])) {
            unset($registrationData['Password']);
        }

        // birthday
        if (!empty($registrationData['BirthdayDay']) &&
            !empty($registrationData['BirthdayMonth']) &&
            !empty($registrationData['BirthdayYear'])) {
            $registrationData['Birthday'] = $registrationData['BirthdayYear'] . '-' .
                $registrationData['BirthdayMonth'] . '-' .
                $registrationData['BirthdayDay'];
        }

        $member->castedUpdate($registrationData);
        
        if (!$member->SubscribedToNewsletter) {
            $member->NewsletterOptInStatus      = false;
            $member->NewsletterConfirmationHash = '';
        }
        
        $member->write();
        
        if ( $member->SubscribedToNewsletter &&
            !$member->NewsletterOptInStatus) {
            
            $confirmationHash = SilvercartNewsletter::createConfirmationHash(
                $member->Salutation,
                $member->FirstName,
                $member->Surname,
                $member->Email
            );
            $member->setField('NewsletterConfirmationHash', $confirmationHash);
            $member->write();
            
            SilvercartNewsletter::sendOptInEmailTo(
                $member->Salutation,
                $member->FirstName,
                $member->Surname,
                $member->Email,
                $confirmationHash
            );
        }

        Director::redirect($this->controller->Link());
    }
}