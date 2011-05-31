<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * @subpackage Widgets
 */

/**
 * form definition
 *
 * @package Silvercart
 * @subpackage Widgets
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 26.05.2011
 */
class SilvercartLoginWidgetForm extends CustomHtmlForm {

    /**
     * Form field definitions.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 11.04.2011
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
     * Form settings.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright Pixeltricks GmbH
     * @since 11.04.2011
     */
    public function preferences() {
        parent::preferences();

        $this->preferences['submitButtonTitle']         = _t('SilvercartPage.LOGIN');
        $this->preferences['doJsValidationScrolling']   = false;

        $this->formFields['emailaddress']['title']  = _t('SilvercartPage.EMAIL_ADDRESS');
        $this->formFields['password']['title']      = _t('SilvercartPage.PASSWORD');
    }

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
                //transfer cart positions from an anonymous user to the one logging in
                $anonymousCustomer = SilvercartAnonymousCustomer::currentAnonymousCustomer();
                if ($anonymousCustomer) {
                    if ($anonymousCustomer->getCart()->SilvercartShoppingCartPositions()->Count() > 0) {
                        //delete registered customers cart positions
                        if ($customer->SilvercartShoppingCart()->SilvercartShoppingCartPositions()) {
                            foreach ($customer->SilvercartShoppingCart()->SilvercartShoppingCartPositions() as $position) {
                                $position->delete();
                            }
                        }
                        //add anonymous positions to the registered user

                        foreach ($anonymousCustomer->SilvercartShoppingCart()->SilvercartShoppingCartPositions() as $position) {
                            $customer->SilvercartShoppingCart()->SilvercartShoppingCartPositions()->add($position);
                        }
                    }
                    $anonymousCustomer->logOut();
                    $anonymousCustomer->delete();
                }

                $customer->logIn();
                $customer->write();
                Director::redirect($formData['redirect_to']);
            } else {

                $this->messages = array(
                    'Authentication' => array(
                        'message' => _t('SilvercartPage.CREDENTIALS_WRONG', 'Your credentials are incorrect.')
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
                    'message' => _t('SilvercartPage.EMAIL_NOT_FOUND', 'This Email address could not be found.')
                )
            );

            return $this->messages = array(
        'Authentication' => array(
            'message' => _t('SilvercartPage.CREDENTIALS_WRONG')
        )
            );
        }
    }
}
