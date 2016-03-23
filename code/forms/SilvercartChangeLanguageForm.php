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
 * Form to change the language
 *
 * @package Silvercart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.04.2012
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartChangeLanguageForm extends CustomHtmlForm {

    /**
     * Set to true to exclude this form from caching.
     *
     * @var bool
     */
    protected $excludeFromCache = true;

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
     * @param bool $withUpdate Call the method with decorator updates or not?
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.04.2012
     */
    public function getFormFields($withUpdate = true) {
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
        return parent::getFormFields($withUpdate);
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
        $language = $formData['Language'];
        if (strpos($language, '|') !== false) {
            $languageElems = explode('|', $language);
            $language      = $languageElems[0];
        }

        $translation = $this->Controller()->getTranslation($language);
        if ($translation) {
            $this->controller->redirect($translation->Link());
        } else {
            $this->controller->redirectBack();
        }
    }
    
}