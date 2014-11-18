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
 * Customer form for adding an address.
 *
 * @package Silvercart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 19.10.2010
 * @license see license file in modules root directory
 */
class SilvercartAddAddressForm extends SilvercartAddressForm {
    
    /**
     * indicates the success of the submission
     *
     * @var bool
     */
    public $submitSuccess = false;

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
     * @since 15.11.2014
     */
    protected function submitSuccess($data, $form, $formData) {
        $member = SilvercartCustomer::currentUser();
        if ($member) {            
            $country = DataObject::get_by_id('SilvercartCountry', $formData['Country']);
            if ($country) {
                $formData['SilvercartCountryID'] = $country->ID;
            }
            $formData['MemberID'] = $member->ID;
            
            $address = new SilvercartAddress();
            $address->write();
            $address->update($formData);
            $address->write();
            $this->submitSuccess = true;
            if (Session::get("redirect")) {
                $this->controller->redirect(Session::get("redirect"));
                Session::clear("redirect");
            } else {
                $addressHolder = SilvercartPage_Controller::PageByIdentifierCode("SilvercartAddressHolder");
                $this->controller->redirect($addressHolder->RelativeLink());
            }
        }
    }
    
}
