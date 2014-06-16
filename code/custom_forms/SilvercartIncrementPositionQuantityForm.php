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
class SilvercartIncrementPositionQuantityForm extends CustomHtmlForm {

    /**
     * form settings, mainly submit button´s name
     *
     * @var array
     */
    protected $preferences = array(
        'submitButtonTitle'         => '+',
        'doJsValidationScrolling'   => false
    );
    
    /**
     * Sets some dynamic preferences
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.03.2013
     */
    public function preferences() {
        $this->preferences['submitButtonToolTip'] = _t('SilvercartPage.INCREMENT_POSITION');
        return parent::preferences();
    }

    /**
     * executed if there are no validation errors on submit
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
        $overwrite = SilvercartPlugin::call($this, 'overwriteSubmitSuccess', array($data, $form, $formData), null, 'boolean');
        
        if (!$overwrite) {
            if ($formData['positionID']) {
                //check if the position belongs to this user. Malicious people could manipulate it.
                $member = Member::currentUser();
                $position = DataObject::get_by_id('SilvercartShoppingCartPosition', $formData['positionID']);
                if ($position && ($member->SilvercartShoppingCart()->ID == $position->SilvercartShoppingCartID)) {
                    $position->SilvercartProduct()->addToCart($member->SilvercartShoppingCart()->ID, 1, true);
                    $backLinkPage = DataObject::get_by_id('SiteTree', $formData['BlID']);
                    $this->controller->redirect($backLinkPage->Link());
                }
            }
        }
    }
}

