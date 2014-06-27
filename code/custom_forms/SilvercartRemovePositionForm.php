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
 * Increment a cart positions quantity;
 * only a button
 *
 * @package Silvercart
 * @subpackage Forms
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 09.02.2011
 * @license see license file in modules root directory
 */
class SilvercartRemovePositionForm extends CustomHtmlForm {

    /**
     * form settings, mainly submit button´s name
     *
     * @var array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 09.02.2011
     * @return void
     */
    protected $preferences = array(
        'submitButtonTitle' => 'remove',
        'doJsValidationScrolling' => false
    );
    
    /**
     * Alternative method to define form fields.
     * 
     * @param bool $withUpdate Call the method with decorator updates or not?
     *             Just defined to be compatible with parent
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.11.2011
     */
    public function getFormFields($withUpdate = true) {
        SilvercartPlugin::call($this, 'updateFormFields', array($this->formFields), true);
        
        return $this->formFields;
    }
    
    /**
     * Alternative method to set preferences.
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.11.2011
     */
    public function preferences() {
        $this->preferences['submitButtonTitle']     = _t('SilvercartPage.REMOVE_FROM_CART');
        $this->preferences['submitButtonToolTip']   = _t('SilvercartPage.REMOVE_FROM_CART');
        
        $preferences = SilvercartPlugin::call($this, 'updatePreferences', array($this->preferences), true, array());
        
        if (is_array($preferences) &&
            count($preferences) > 0) {
            
            $this->preferences = $preferences[0];
        }
        
        return $this->preferences;
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
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 27.06.2014
     */
    protected function submitSuccess($data, $form, $formData) {

        if ($formData['positionID']) {

            //check if the position belongs to this user. Malicious people could manipulate it.
            $member = Member::currentUser();
            $position = DataObject::get_by_id('SilvercartShoppingCartPosition', $formData['positionID']);
            if ($position && ($member->getCart()->ID == $position->SilvercartShoppingCartID)) {
                $position->delete();
                $backLinkPage = DataObject::get_by_id('SiteTree', $formData['BlID']);
                $this->controller->redirect($backLinkPage->Link());
            }
        }
    }

}

