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
class SilvercartCheckoutFormStep1NewCustomerForm extends CustomHtmlFormStep {

    /**
     * The form field definitions.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.04.2011
     */
    protected $formFields = array(
        'AnonymousOptions' => array(
            'type'              => 'OptionsetField',
            'title'             => '',
            'value'             => array(
                '1' => 'Yes, I want to register so I can reuse my data on my next purchase.',
                '2' => 'No, I don\'t want to register.'
            ),
            'selectedValue'     => '1'
        )
    );

    /**
     * We set the localized field labels here.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.04.2011
     */
    protected function fillInFieldValues() {
        $this->formFields['AnonymousOptions']['value']['1'] = _t('SilvercartCheckoutFormStep1.PROCEED_WITH_REGISTRATION');
        $this->formFields['AnonymousOptions']['value']['2'] = _t('SilvercartCheckoutFormStep1.PROCEED_WITHOUT_REGISTRATION');
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

        $this->preferences['submitButtonTitle']         = _t('SilvercartCheckoutFormStep1NewCustomerForm.TITLE');
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.04.2011
     */
    public function submitSuccess($data, $form, $formData) {
        switch ($formData['AnonymousOptions']) {
            case '2':
                // Checkout without registration
                $this->controller->addCompletedStep();
                $this->controller->NextStep();
                break;
            case '1':
            default:
                // Jump to registration page
                $registerPage = SilvercartPage_Controller::PageByIdentifierCode('SilvercartRegistrationPage');
                $this->getController()->redirect($registerPage->Link().
                    '?backlink='.urlencode($this->controller->Link()).
                    '&backlinkText='.urlencode(_t('SilvercartCheckoutFormStep1NewCustomerForm.CONTINUE_WITH_CHECKOUT')).
                    '&optInTempText='.urlencode(_t('SilvercartCheckoutFormStep1NewCustomerForm.OPTIN_TEMP_TEXT')));
        }
    }
}

