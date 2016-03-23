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
 * form definition
 *
 * @package Silvercart
 * @subpackage Forms
 * @copyright 2013 pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @license see license file in modules root directory
 * @since 23.10.2010
 */
class SilvercartQuickLoginForm extends CustomHtmlForm {

    /**
     * Defines form fields
     *
     * @var array
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
     * Set texts for preferences with i18n methods.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 11.04.2011
     */
    public function preferences() {
        $this->preferences['submitButtonTitle']         = _t('SilvercartPage.LOGIN');
        $this->preferences['doJsValidationScrolling']   = false;
        
        $this->formFields['emailaddress']['title']  = _t('SilvercartPage.EMAIL_ADDRESS').':';
        $this->formFields['password']['title']      = _t('SilvercartPage.PASSWORD').':';
        parent::preferences();
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
                    $anonymousCustomer->logOut();
                    $anonymousCustomer->delete();
                }

                $customer->logIn();
                $customer->write();
                if ($this->Controller()->redirectedTo() == '') {
                    $myAccountHolder = SilvercartPage_Controller::PageByIdentifierCode("SilvercartMyAccountHolder");
                    $this->Controller()->redirect($myAccountHolder->RelativeLink());
                }
            } else {
                $this->addMessage(_t('SilvercartPage.CREDENTIALS_WRONG', 'Your credentials are incorrect.'));

                Requirements::customScript('jQuery(document).ready(function(){ $("#silvercart-quicklogin-form").slideDown(); });');
                
                return $this->submitFailure(
                    $data,
                    $form
                );
            }
        } else {
            $this->addMessage(_t('SilvercartPage.CREDENTIALS_WRONG'));
            
            Requirements::customScript('jQuery(document).ready(function(){ $("#silvercart-quicklogin-form").slideDown(); });');

            return $this->submitFailure(
                $data,
                $form
            );
        }
    }
}
