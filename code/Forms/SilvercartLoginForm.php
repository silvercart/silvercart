<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Forms
 */

/**
 * A form login to ones' account.
 *
 * @package Silvercart
 * @subpackage Forms
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 11.04.2011
 * @license see license file in modules root directory
 */
class SilvercartLoginForm extends CustomHtmlForm {

    /**
     * Don't cache this form.
     *
     * @var bool
     */
    protected $excludeFromCache = true;

    /**
     * Form field definitions.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 11.04.2011
     */
    protected $formFields = array(
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

        $this->preferences['submitButtonTitle'] = _t('SilvercartPage.LOGIN');

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
     * @since 27.06.2014
     */
    protected function submitSuccess($data, $form, $formData) {

        $emailAddress = $formData['emailaddress'];
        $password = $formData['password'];

        // get customers data
        $user = Member::get()->filter('Email', $formData['emailaddress'])->first();

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
                    if ($anonymousCustomer->getCart()->SilvercartShoppingCartPositions()->exists()) {
                        //delete registered customers cart positions
                        if ($customer->getCart()->SilvercartShoppingCartPositions()) {
                            foreach ($customer->getCart()->SilvercartShoppingCartPositions() as $position) {
                                $position->delete();
                            }
                        }
                        //add anonymous positions to the registered user

                        foreach ($anonymousCustomer->getCart()->SilvercartShoppingCartPositions() as $position) {
                            $customer->getCart()->SilvercartShoppingCartPositions()->add($position);
                        }
                    }
                    $anonymousCustomer->delete();
                }

                $customer->logIn();
                $customer->write();
                $myAccountHolder = SilvercartPage_Controller::PageByIdentifierCode("SilvercartMyAccountHolder");
                $this->controller->redirect($myAccountHolder->RelativeLink());
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