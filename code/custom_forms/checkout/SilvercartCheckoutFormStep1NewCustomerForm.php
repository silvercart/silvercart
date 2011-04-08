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
class SilvercartCheckoutFormStep1NewCustomerForm extends CustomHtmlForm {

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
     * @return void
     */
    protected function fillInFieldValues() {
        $this->formFields['AnonymousOptions']['value']['1'] = _t('SilvercartCheckoutFormStep1.PROCEED_WITH_REGISTRATION');
        $this->formFields['AnonymousOptions']['value']['2'] = _t('SilvercartCheckoutFormStep1.PROCEED_WITHOUT_REGISTRATION');
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
                Director::redirect($registerPage->Link().
                    '?backlink='.urlencode($this->controller->Link()).
                    '&backlinkText='.urlencode(_t('SilvercartCheckoutFormStep1NewCustomerForm.CONTINUE_WITH_CHECKOUT')).
                    '&optInTempText='.urlencode(_t('SilvercartCheckoutFormStep1NewCustomerForm.OPTIN_TEMP_TEXT')));
        }
    }
}

