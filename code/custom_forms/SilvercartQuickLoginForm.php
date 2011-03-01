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
 * form definition
 *
 * @package Silvercart
 * @subpackage Forms
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 23.10.2010
 */
class SilvercartQuickLoginForm extends CustomHtmlForm {

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
                $myAccountHolder = SilvercartPage_Controller::PageByIdentifierCode("SilvercartMyAccountHolder");
                Director::redirect($myAccountHolder->RelativeLink());
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