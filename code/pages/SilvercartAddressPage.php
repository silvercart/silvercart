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
 * @subpackage Pages
 */

/**
 * Child of AddressHolder, CRUD a single address
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 16.02.2011
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartAddressPage extends SilvercartMyAccountHolder {

    public static $singular_name = "";
    public static $can_be_root = false;
    
    /**
     * The icon to use for this page in the storeadmin sitetree.
     *
     * @var string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.10.2011
     */
    public static $icon = "silvercart/images/page_icons/my_account_holder";

    /**
     * configure the class name of the DataObjects to be shown on this page
     * this is needed to show correct breadcrumbs
     *
     * @return string class name of the DataObject to be shown on this page
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 3.11.2010
     */
    public function getSection() {
        return 'SilvercartAddress';
    }
}

/**
 * Controller of this page type
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 19.10.2010
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartAddressPage_Controller extends SilvercartMyAccountHolder_Controller {

    /**
     * statements to be called on instanciation
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.02.2011
     */
    public function init() {
        $addressId = false;
        
        if (isset($_POST['addressID'])) {
            $addressId = Convert::raw2sql($_POST['addressID']);
        } else {
            $addressId = $this->urlParams['Action'];
        }

        $this->setBreadcrumbElementID($addressId);

        // get the address to check whether it is related to the actual customer or not.
        $address = DataObject::get_by_id('SilvercartAddress', $addressId);
        
        if ($address->MemberID > 0) {
            if ($address->Member()->ID != Member::currentUserID()) {
                // the address is not related to the customer, redirect elsewhere...
                Director::redirect($this->PageByIdentifierCode()->Link());
            }
        }

        $this->registerCustomHtmlForm('SilvercartEditAddressForm', new SilvercartEditAddressForm($this, array('addressID' => $addressId)));
        
        parent::init();
    }

    /**
     * handles the given action. If the action is a numeric value, an address is
     * requested. This method manipulates the handling for this case.
     *
     * @param SS_HTTPRequest $request Request
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.02.2011
     */
    public function handleAction($request) {
        if (!$this->hasMethod($this->urlParams['Action'])) {
            if (is_numeric($this->urlParams['Action'])) {
                return $this->getViewer('index')->process($this);
            }
        }
        
        return parent::handleAction($request);
    }
    
    /**
     * Returns the cancel link.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 12.04.2011
     */
    public function CancelLink() {
        return $this->Parent()->Link();
    }
    
}
