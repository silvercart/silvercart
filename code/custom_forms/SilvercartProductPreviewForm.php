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
class SilvercartProductPreviewForm extends CustomHtmlForm {
    /**
     * field configuration
     *
     * @var array
     */
    protected $formFields = array
    (
        'productAmount' => array(
            'type'              => 'TextField',
            'title'             => 'Anzahl',
            'value'             => '1',
            'checkRequirements' => array(
                'isFilledIn'    => true,
                'isNumbersOnly' => true
            )
        )
    );

    /**
     * form settings, mainly submit button´s name
     *
     * @var array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 1.11.2010
     * @return void
     */
    protected $preferences = array(
        'submitButtonTitle'         => 'in den Warenkorb',
        'doJsValidationScrolling'   => false
    );

    /**
     * initial field values
     *
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     */
    protected function fillInFieldValues() {
        $this->formFields['productAmount']['title'] = _t('SilvercartProduct.QUANTITY');
        $this->preferences['submitButtonTitle'] = _t('SilvercartProduct.ADD_TO_CART');

    }

    /**
     * executed if there are no valdation errors on submit
     * Form data is saved in session
     *
     * @param SS_HTTPRequest $data     contains the frameworks form data
     * @param Form           $form     not used
     * @param array          $formData contains the modules form data
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     * @return void
     */
    protected function submitSuccess($data, $form, $formData) {
       $backLink = $this->controller->Link();

        if (isset($formData['backLink'])) {
            $backLink = $formData['backLink'];
        }

        if (ShoppingCart::addProduct($formData)) {
            Director::redirect($backLink,302);
        } else {
            Director::redirect($backLink,302);
            exit();
        }
    }
}