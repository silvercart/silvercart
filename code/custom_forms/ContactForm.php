<?php

/**
 * a contact form of the CustomHTMLForms modul
 *
 * @copyright pixeltricks GmbH
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 21.10.2010
 * @license BSD
 */
class ContactForm extends CustomHtmlForm {

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
     * change the submit button
     *
     * @return FieldSet action field
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 21.10.2010
     */
    protected function getForm() {
        $formDefinition = parent::getForm('submit');



        $actions = new FieldSet(
                        new FormAction(
                                'submit',
                                'Nachricht Senden'
                        )
        );

        $formDefinition['actions'] = $actions;

        return $formDefinition;
    }

    /**
     * logged in users get there fields filled
     *
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 21.10.2010
     */
    protected function fillInFieldValues() {
        $member = Member::currentUser();
        if ($member) {
            $this->formFields['Salutation']['selectedValue'] = $member->Salutation;
            $this->formFields['FirstName']['value'] = $member->FirstName;
            $this->formFields['Surname']['value'] = $member->Surname;
            $this->formFields['Email']['value'] = $member->Email;
        }
    }

    /**
     * Wird ausgefuehrt, wenn nach dem Senden des Formulars keine Validierungs-
     * fehler aufgetreten sind.
     *
     * @param SS_HTTPRequest $data     silverstripes session data
     * @param Form           $form     the form object
     * @param array          $formData session data of CustomHTMLForms modul
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 21.10.2010
     * @return void
     */
    protected function submitSuccess($data, $form, $formData) {

        $email = new Email(
            'info@pourlatable.de',
            'rlehmann@pixeltricks.de',
            'Kontaktformular Anfrage PourLaTable',
            ''
        );

        $email->setTemplate('MailContact');
        $email->populateTemplate(
            array(
                'FirstName' => $formData['FirstName'],
                'Surname'   => $formData['Surname'],
                'Email'     => $formData['Email'],
                'Message'   => str_replace('\r\n', '<br>', nl2br($formData['Message']))
            )
        );

        $email->send();
        /*
         * redirect a user to the page type for the response or to the root
         */
        $contactFormResponsePage = DataObject::get_one('ContactFormResponsePage');
        if ($contactFormResponsePage) {
            $urlSegment = sprintf("/%s/", $contactFormResponsePage->URLSegment);
            Director::redirect($urlSegment);
        } else {
            Director::redirect('/');
        }
    }

}