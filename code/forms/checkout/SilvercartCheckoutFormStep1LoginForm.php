<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 */

/**
 * form step for customers shipping/billing address
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 08.04.2011
 * @license see license file in modules root directory
 */
class SilvercartCheckoutFormStep1LoginForm extends CustomHtmlFormStep {

    /**
     * Don't cache this form.
     *
     * @var bool
     */
    protected $excludeFromCache = true;
    
    /**
     * Don't enable Security token for this type of form because we'll run
     * into caching problems when using it.
     * 
     * @var boolean
     */
    protected $securityTokenEnabled = false;
    
    /**
     * Returns the forms fields.
     * 
     * @param bool $withUpdate Call the method with extension updates or not?
     *
     * @return array
     */
    public function getFormFields($withUpdate = true) {
        if (!array_key_exists('emailaddress', $this->formFields)) {
            $this->formFields = array(
                'Email' => array(
                    'type'  => 'TextField',
                    'title' => _t('SilvercartPage.EMAIL_ADDRESS'),
                    'value' => '',
                    'checkRequirements' => array(
                        'isEmailAddress' => true,
                        'isFilledIn'     => true,
                    )
                ),
                'Password' => array(
                    'type'  => 'PasswordField',
                    'title' => _t('SilvercartPage.PASSWORD'),
                    'value' => '',
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
            );
        }
        return parent::getFormFields($withUpdate);
    }

    /**
     * Set initial form values
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.02.2013
     */
    protected function fillInFieldValues() {
        parent::fillInFieldValues();
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
     * @since 21.04.2011
     */
    public function  preferences() {
        parent::preferences();

        $this->preferences['submitButtonTitle']         = _t('SilvercartCheckoutFormStep1LoginForm.TITLE');
        $this->preferences['loadShoppingcartModules']   = false;
        $this->preferences['createShoppingcartForms']   = false;
        $this->preferences['doJsValidationScrolling']   = false;
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.06.2014
     */
    public function submitSuccess($data, $form, $formData) {
        $emailAddress   = $formData['Email'];
        $password       = $formData['Password'];

        // get customers data
        $user = Member::get()->filter('Email', $emailAddress)->first();

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
                Session::save();
                
                $this->getController()->redirect($this->getController()->Link());
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

