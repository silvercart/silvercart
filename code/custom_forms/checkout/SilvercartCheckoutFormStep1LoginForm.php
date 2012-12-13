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
 * @subpackage Forms Checkout
 */

/**
 * form step for customers shipping/billing address
 *
 * @package Silvercart
 * @subpackage Forms Checkout
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 08.04.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCheckoutFormStep1LoginForm extends CustomHtmlForm {

    /**
     * Don't cache this form.
     *
     * @var bool
     */
    protected $excludeFromCache = true;

    /**
     * The form field definitions.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.04.2011
     */
    protected $formFields = array(
        'Email' => array(
            'type'              => 'TextField',
            'title'             => 'Email',
            'checkRequirements' => array(
                'isEmailAddress'    => true,
                'isFilledIn'        => true
            )
        ),
        'Password' => array(
            'type'              => 'PasswordField',
            'title'             => 'Password',
            'checkRequirements' => array(
                'isFilledIn'        => true
            )
        )
    );

    /**
     * Set initial form values
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 08.04.2011
     */
    protected function fillInFieldValues() {
        $this->controller->fillFormFields($this->formFields);
        $this->formFields['Email']['title']     = _t('SilvercartAddress.EMAIL', 'email address');
        $this->formFields['Password']['title']  = _t('SilvercartPage.PASSWORD');
    }

    /**
     * Here we set some preferences.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 21.04.2011
     */
    public function  preferences() {
        parent::preferences();

        $this->preferences['submitButtonTitle']         = _t('SilvercartCheckoutFormStep1LoginForm.TITLE');
        $this->preferences['loadShoppingcartModules']   = false;
        $this->preferences['createShoppingcartForms']   = false;
    }

    /**
     * executed if there are no valdation errors on submit
     * Form data is saved in session
     *
     * @param SS_HTTPRequest $data     contains the frameworks form data
     * @param Form           $form     not used
     * @param array          $formData contains the modules form data
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 08.04.2011
     */
    public function submitSuccess($data, $form, $formData) {
        $emailAddress   = $formData['Email'];
        $password       = $formData['Password'];

        // get customers data
        $user = DataObject::get_one(
            'Member',
            'Member.Email = \'' . $emailAddress . '\''
        );

        if ($user) {
            $customer = MemberAuthenticator::authenticate(
                array(
                    'Email'     => $emailAddress,
                    'Password'  => $password
                )
            );

            if ($customer) {
                //transfer cart positions from an anonymous user to the one logging in
                $anonymousCustomer = SilvercartCustomer::currentAnonymousCustomer();
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

                Director::redirect($this->controller->Link());
            } else {
                $this->addErrorMessage('Password', _t('SilvercartPage.PASSWORD_WRONG', 'This user does not exist.'));

                return $this->submitFailure(
                    $data,
                    $form
                );
            }
        } else {
            $this->addErrorMessage('Email', _t('SilvercartPage.USER_NOT_EXISTING', 'This user does not exist.'));

            return $this->submitFailure(
                $data,
                $form
            );
        }
    }
}

