<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Widgets
 */

/**
 * form definition
 *
 * @package Silvercart
 * @subpackage Widgets
 * @copyright 2013 pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @license see license file in modules root directory
 * @since 26.05.2011
 */
class SilvercartLoginWidgetForm extends CustomHtmlForm {

    /**
     * Form field definitions.
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
     * Form settings.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
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
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.03.2014
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
                $anonymousCustomer = SilvercartCustomer::currentAnonymousCustomer();
                if ($anonymousCustomer) {
                    if ($anonymousCustomer->getCart()->SilvercartShoppingCartPositions()->count() > 0) {
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
                $this->Controller()->redirect($formData['redirect_to']);
            } else {

                $this->messages = array(
                    'Authentication' => array(
                        'message' => _t('SilvercartPage.CREDENTIALS_WRONG'),
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
                        'message' => _t('SilvercartPage.CREDENTIALS_WRONG'),
                    )
                );
                
                return $this->submitFailure(
                        $data,
                        $form
                );
        }
    }
}
