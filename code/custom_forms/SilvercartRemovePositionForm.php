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
 * @subpackage Forms
 */

/**
 * Increment a cart positions quantity;
 * only a button
 *
 * @package Silvercart
 * @subpackage Forms
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 09.02.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
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
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.11.2011
     */
    public function getFormFields() {
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     * @return void
     */
    protected function submitSuccess($data, $form, $formData) {

        if ($formData['positionID']) {

            //check if the position belongs to this user. Malicious people could manipulate it.
            $member = Member::currentUser();
            $position = DataObject::get_by_id('SilvercartShoppingCartPosition', $formData['positionID']);
            if ($position && ($member->SilvercartShoppingCart()->ID == $position->SilvercartShoppingCartID)) {
                $position->delete();

                $backLinkPage = DataObject::get_by_id('SiteTree', $formData['BlID']);
                Director::redirect($backLinkPage->Link());
            }
        }
    }

}

