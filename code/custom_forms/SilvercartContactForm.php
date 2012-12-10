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
 * a contact form of the CustomHTMLForms modul
 *
 * @package Silvercart
 * @subpackage Forms
 * @copyright pixeltricks GmbH
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 21.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartContactForm extends CustomHtmlForm {

    protected $excludeFromCache = true;

    protected $useSpamCheck = true;

    /**
     * definition of the form fields
     *
     * @var array
     */
    protected $formFields = array(
        'Salutation' => array(
            'type' => 'DropdownField',
            'title' => 'Anrede',
            'value' => array('' => 'Bitte wählen', 'Frau' => 'Frau', 'Herr' => 'Herr'),
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'FirstName' => array(
            'type' => 'TextField',
            'title' => 'Vorname',
            'checkRequirements' => array(
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
            'value' => '',
            'checkRequirements' => array(
                'isFilledIn' => true,
                'isEmailAddress' => true
            )
        ),
        'Message' => array(
            'type' => 'TextareaField',
            'title' => 'Nachricht',
            'checkRequirements' => array
                (
                'isFilledIn' => true,
                'hasMinLength' => 3
            )
        )
    );

    /**
     * form settings, mainly submit button´s name
     *
     * @var array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 02.02.2011
     */
    protected $preferences = array(
        'submitButtonTitle'  => 'Nachricht senden',
        'markRequiredFields' => true
    );

    /**
     * logged in users get there fields filled
     *
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 21.10.2010
     */
    protected function fillInFieldValues() {
        $this->formFields['Salutation']['title'] = _t('SilvercartAddress.SALUTATION');
        $this->formFields['Salutation']['value'] = array(
            ''      => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE'),
            "Frau"  => _t('SilvercartAddress.MISSES'),
            "Herr"  => _t('SilvercartAddress.MISTER')
        );
        $this->formFields['FirstName']['title']  = _t('SilvercartAddress.FIRSTNAME', 'firstname');
        $this->formFields['Surname']['title']    = _t('SilvercartAddress.SURNAME');
        $this->formFields['Email']['title']      = _t('SilvercartAddress.EMAIL', 'email address');
        $this->formFields['Message']['title']    = _t('SilvercartPage.MESSAGE', 'message');
        $this->preferences['submitButtonTitle']  = _t('SilvercartPage.SUBMIT_MESSAGE', 'submit message');

        $member = Member::currentUser();
        if ($member) {
            $this->formFields['Salutation']['selectedValue'] = $member->Salutation;
            $this->formFields['FirstName']['value']          = $member->FirstName;
            $this->formFields['Surname']['value']            = $member->Surname;
            $this->formFields['Email']['value']              = $member->Email;
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 21.10.2010
     * @return void
     */
    protected function submitSuccess($data, $form, $formData) {

        $formData['Message'] = str_replace('\r\n', "\n", $formData['Message']);

        $contactMessage = new SilvercartContactMessage();
        $contactMessage->update($formData);
        $contactMessage->write();
        $contactMessage->send();
        /*
         * redirect a user to the page type for the response or to the root
         */
        $contactFormResponsePage = SilvercartPage_Controller::PageByIdentifierCode("SilvercartContactFormResponsePage");
        Director::redirect($contactFormResponsePage->RelativeLink());
    }
}
