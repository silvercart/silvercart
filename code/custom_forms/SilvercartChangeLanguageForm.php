<?php
/**
 * Copyright 2010 - 2012 pixeltricks GmbH
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
 * @subpackage Forms
 */

/**
 * Form to change the language
 *
 * @package Silvercart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.04.2012
 * @copyright 2012 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartChangeLanguageForm extends CustomHtmlForm {
    
    /**
     * Returns the preferences for this form
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.04.2012
     */
    public function preferences() {
        $this->preferences = array(
            'submitButtonTitle' => _t('Silvercart.CHOOSE'),
        );
        return parent::preferences();
    }
    
    /**
     * Returns the preferences for this form
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.04.2012
     */
    public function getFormFields() {
        $this->formFields = array(
            'Language' => array(
                'type' => 'SilvercartLanguageDropdownField',
                'title' => _t('Silvercart.LANGUAGE'),
                'value' => array(
                ),
                'checkRequirements' => array(
                    'isFilledIn' => true
                )
            ),
        );
        return parent::getFormFields();
    }
    
    /**
     * This method will be call if there are no validation error
     *
     * @param SS_HTTPRequest $data     input data
     * @param Form           $form     form object
     * @param array          $formData secured form data
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.04.2012
     */
    protected function submitSuccess($data, $form, $formData) {
        $translation = $this->Controller()->getTranslation($formData['Language']);
        if ($translation) {
            Director::redirect($translation->Link());
        } else {
            Director::redirectBack();
        }
    }
    
}