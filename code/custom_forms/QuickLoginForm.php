<?php

/**
 * Beschreibung des Formulars.
 *
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @license BSD
 * @since 23.10.2010
 */
class QuickLoginForm extends CustomHtmlForm {

    /**
     * Enthaelt die zu pruefenden und zu verarbeitenden Formularfelder.
     *
     * @var array
     */
    protected $formFields = array
        (
        'emailaddress' => array(
            'type' => 'TextField',
            'title' => '',
            'value' => '',
            'checkRequirements' => array(
                'isFilledIn' => true
        )),
        'password' => array(
            'type' => 'PasswordField',
            'title' => '',
            'value' => '',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        )
    );

    /**
     * Voreinstellungen.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.11.2010
     */
    protected $preferences = array(
        'submitButtonTitle' => ''
    );

    /**
     * Wird ausgefuehrt, wenn nach dem Senden des Formulars keine Validierungs-
     * fehler aufgetreten sind.
     *
     * @param SS_HTTPRequest $data     session data
     * @param Form           $form     the form object
     * @param array          $formData CustomHTMLForms Session data
     *
     * @return array to be rendered in the controller
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.10.2010
     */
    protected function submitSuccess($data, $form, $formData) {

        $emailAddress = $formData['emailaddress'];
        $password = $formData['password'];

        // Daten des Kunden holen
        $user = DataObject::get_one(
                        'Member',
                        'Member.Email LIKE \'' . $formData['emailaddress'] . '\''
        );

        if ($user) {
            $customer = MemberAuthenticator::authenticate(
                 array(
                       'Email' => $emailAddress,
                       'Password' => $password
                      )
                );

            if ($customer) {
                $customer->logIn();
                $customer->write();
                Director::redirect("/meinkonto/");
            } else {

                $this->messages = array(
                    'Authentication' => array(
                    'message' => 'Die eingegebenen Zugangsdaten sind nicht korrekt.'
                )
                );
                
                return $this->submitFailure(
                        $data,
                        $form
                );
            }
        } else {
            $this->messages = array(
                   'Authentication' => array(
                   'message' => 'Der Benutzer existiert nicht.'
            )
            );

            return $this->messages = array(
                   'Authentication' => array(
                   'message' => 'Die angegebenen Zugangsdaten sind falsch.'
            )
            );
        }
    }
}