<?php

/**
 * form definition
 *
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @license BSD
 * @since 23.10.2010
 */
class QuickLoginForm extends CustomHtmlForm {

    /**
     * defines form fields
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
     * form preferences
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
     * executed if there are no valdation errors on submit
     * Form data is saved in session
     *
     * @param SS_HTTPRequest $data     contains the frameworks form data
     * @param Form           $form     not used
     * @param array          $formData contains the modules form data
     *
     * @return array to be rendered in the controller
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.10.2010
     */
    protected function submitSuccess($data, $form, $formData) {

        $emailAddress = $formData['emailaddress'];
        $password = $formData['password'];

        // get customers data
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
                    'message' => _t('Page.CREDENTIALS_WRONG', 'Your credentials are incorrect.')
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
                   'message' => _t('Page.USER_NOT_EXISTING', 'This user does not exist.')
            )
            );

            return $this->messages = array(
                   'Authentication' => array(
                   'message' => _t('Page.CREDENTIALS_WRONG')
            )
            );
        }
    }
}