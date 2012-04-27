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
     * custom redirect action to handle special sites like product detail page, 
     * order detail page, address forms, etc.
     *
     * @var string
     */
    protected $customRedirectAction = null;

    /**
     * creates a form object with a free configurable markup
     *
     * @param ContentController $controller  the calling controller instance
     * @param array             $params      optional parameters
     * @param array             $preferences optional preferences
     * @param bool              $barebone    defines if a form should only be instanciated or be used too
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.04.2012
     */
    public function __construct($controller, $params = null, $preferences = null, $barebone = false) {
        parent::__construct($controller, $params, $preferences, $barebone);
        
        // this is needed to handle special translation site (product detail, order detail, address detail)
        $request = $controller->getRequest();
        if ($request) {
            $customRedirectAction   = false;
            $params                 = $request->allParams();
            $action                 = $params['Action'];
            if (is_numeric($action)) {
                $customRedirectAction = $action;
            } elseif (empty ($action)) {
                $customRedirectAction = null;
            } elseif ($action != 'customHtmlFormSubmit') {
                $customRedirectAction = $action;
            }
            if ($customRedirectAction !== false) {
                $this->setCustomRedirectAction($customRedirectAction);
            }
        }
    }
    
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
            Director::redirect($translation->Link($this->getCustomRedirectAction()));
        } else {
            Director::redirectBack();
        }
    }
    
    /**
     * returns the custom redirect action
     *
     * @return string
     */
    public function getCustomRedirectAction() {
        if (is_null($this->customRedirectAction)) {
            $this->customRedirectAction = Session::get('SilvercartChangeLanguageForm.CustomRedirectAction');
        }
        return $this->customRedirectAction;
    }

    /**
     * Sets the custom redirect action
     *
     * @param string $customRedirectAction custom redirect action
     * 
     * @return void
     */
    public function setCustomRedirectAction($customRedirectAction) {
        $this->customRedirectAction = $customRedirectAction;
        Session::set('SilvercartChangeLanguageForm.CustomRedirectAction', $customRedirectAction);
        Session::save();
    }
    
}