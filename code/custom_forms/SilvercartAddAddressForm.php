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
 * Customer form for adding an address.
 *
 * @package Silvercart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright pixeltricks GmbH
 * @since 19.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartAddAddressForm extends SilvercartAddressForm {
    
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
     * @since 20.06.2011
     */
    protected function submitSuccess($data, $form, $formData) {
        $member = Member::currentUser();
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
