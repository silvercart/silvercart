<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Widgets
 */

/**
 * form definition
 *
 * @package Silvercart
 * @subpackage Widgets
 * @copyright 2013 pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @license see license file in modules root directory
 * @since 26.05.2011
 */
class SilvercartLoginWidgetForm extends CustomHtmlForm {
    
    /**
     * Custom form action to use for this form
     *
     * @var string
     */
    protected $customHtmlFormAction = 'doLogin';
    
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
                'emailaddress' => array(
                    'type' => 'TextField',
                    'title' => _t('SilvercartPage.EMAIL_ADDRESS'),
                    'value' => '',
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )),
                'password' => array(
                    'type' => 'PasswordField',
                    'title' => _t('SilvercartPage.PASSWORD'),
                    'value' => '',
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                )
            );
        }
        return parent::getFormFields($withUpdate);
    }

    /**
     * Form settings.
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.11.2016
     */
    public function preferences() {
        parent::preferences();

        $this->preferences['submitButtonTitle']       = _t('SilvercartPage.LOGIN');
        $this->preferences['doJsValidationScrolling'] = false;
    }
    
}
