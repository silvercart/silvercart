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
class SilvercartCheckoutFormStep1 extends CustomHtmlForm {

    /**
     * init
     *
     * @param Controller $controller  the controller object
     * @param array      $params      additional parameters
     * @param array      $preferences array with preferences
     * @param bool       $barebone    is the form initialized completely?
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 08.04.2011
     */
    public function __construct($controller, $params = null, $preferences = null, $barebone = false) {

        parent::__construct($controller, $params, $preferences, $barebone);

        if (!$barebone) {
            // Redirect a user if his cart is empty
            if (!Member::currentUser() ||
                !Member::currentUser()->SilvercartShoppingCart()->isFilled()) {

                $frontPage = SilvercartPage_Controller::PageByIdentifierCode();
                Director::redirect($frontPage->RelativeLink());
            }

            $this->registerCustomHtmlForm('SilvercartCheckoutFormStep1LoginForm', new SilvercartCheckoutFormStep1LoginForm($this->controller));
            $this->registerCustomHtmlForm('SilvercartCheckoutFormStep1NewCustomerForm', new SilvercartCheckoutFormStep1NewCustomerForm($this->controller));
        }
    }

    /**
     * Logged in users get directed to the next step immediately.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 08.04.2011
     */
    public function isConditionForDisplayFulfilled() {
        if (SilvercartCustomerRole::currentRegisteredCustomer()) {
            return false;
        }

        return true;
    }

    /**
     * Here we set some preferences.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 08.04.2011
     */
    public function preferences() {
        $this->preferences['stepIsVisible']         = true;
        $this->preferences['stepTitle']             = _t('SilvercartCheckoutFormStep1.TITLE');
        $this->preferences['submitButtonTitle']     = _t('SilvercartCheckoutFormStep.FORWARD');
        $this->preferences['fillInRequestValues']   = true;
        $this->preferences['isConditionalStep']     = true;

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
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 08.04.2011
     */
    public function submitSuccess($data, $form, $formData) {
        print "Do Login<br />";
        print_r($formData);
        exit();
    }

    public function submitFailure($data, $form) {
        print "FEHLER";
        print_r($data);
        print_r($form);
        exit();
    }
}

