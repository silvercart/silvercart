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
 * @subpackage Pages Checkout
 */

/**
 * Seite fuer den Checkoutprozess.
 *
 * @package Silvercart
 * @subpackage Pages Checkout
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 09.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCheckoutStep extends CustomHtmlFormStepPage {

    public static $icon = "silvercart/images/page_icons/checkout";
    
}

/**
 * Seite fuer den Checkoutprozess.
 *
 * @package Silvercart
 * @subpackage Pages Checkout
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 09.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCheckoutStep_Controller extends CustomHtmlFormStepPage_Controller {

    /**
     * Legt Voreinstellungen fest.
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 17.11.2010
     */
    protected $preferences = array(
        'templateDir' => ''
    );
    /**
     * Enthaelt das Zahlungsmodul-Objekt.
     *
     * @var PaymentMethod
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    protected $paymentMethodObj = false;

    /**
     * Bindet Formulare ein und laedt CSS- und Javascriptdateien.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 09.11.2010
     */
    public function init() {
        $this->preferences['templateDir'] = PIXELTRICKS_CHECKOUT_BASE_PATH_REL . 'templates/Layout/';
        if (SilvercartConfig::EnableSSL()) {
            Director::forceSSL();
        }
        parent::init();
        
        // Inject payment and shippingmethods to shoppingcart, if available
        $member = Member::currentUser();

        if ($member) {
            $stepData       = $this->getCombinedStepData();
            $shoppingCart   = $member->SilvercartShoppingCart();
            
            // If minimum order value is set and shoppingcart value is below we
            // have to redirect the customer to the shoppingcart page and set
            // an appropriate error message.
            if ( $this->getCurrentStep() < 5 &&
                 SilvercartConfig::UseMinimumOrderValue() &&
                 SilvercartConfig::MinimumOrderValue() &&
                !SilvercartConfig::DisregardMinimumOrderValue() &&
                 SilvercartConfig::MinimumOrderValue()->getAmount() > $shoppingCart->getAmountTotalWithoutFees()->getAmount()) {
                
                $_SESSION['Silvercart']['errors'][] = sprintf(
                    _t('SilvercartShoppingCart.ERROR_MINIMUMORDERVALUE_NOT_REACHED'),
                    SilvercartConfig::MinimumOrderValue()->Nice()
                );
                
                Director::redirect(SilvercartPage_Controller::PageByIdentifierCode('SilvercartCartPage')->Link());
            }

            if (isset($stepData['ShippingMethod'])) {
                $shoppingCart->setShippingMethodID($stepData['ShippingMethod']);
            }
            if (isset($stepData['PaymentMethod'])) {
                $shoppingCart->setPaymentMethodID($stepData['PaymentMethod']);
            }
            
            $requestParams = $this->getRequest()->allParams();
            if ($requestParams['Action'] == 'editAddress') {
                $addressID = (int) $requestParams['ID'];
                if (Member::currentUser()->SilvercartAddresses()->containsIDs(array($addressID))) {
                    Session::set("redirect", $this->Link());
                    $preferences = array();
                    $preferences['submitAction'] = 'editAddress/' . $addressID . '/customHtmlFormSubmit';
                    $this->registerCustomHtmlForm('SilvercartEditAddressForm', new SilvercartEditAddressForm($this, array('addressID' => $addressID), $preferences));
                }
            }
        }
    }

    /**
     * Gibt zurueck, ob ein Fehler im Zahlungsmodul aufgetreten ist.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function getErrorOccured() {
        if ($this->paymentMethodObj) {
            return $this->paymentMethodObj->getErrorOccured();
        }

        return false;
    }

    /**
     * Gibt die Fehlerliste als DataObjectSet zurueck.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function getErrorList() {
        if ($this->paymentMethodObj) {
            return $this->paymentMethodObj->getErrorList();
        }

        return false;
    }

    /**
     * Loescht den Warenkorb.
     *
     * @param bool $includeShoppingCart set wether the shoppingcart should be
     *                                  deleted
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pxieltricks GmbH
     * @since 22.11.2010
     */
    public function deleteSessionData($includeShoppingCart = true) {
        parent::deleteSessionData();

        $member = Member::currentUser();

        if ($includeShoppingCart && $member) {
            if ($member->SilvercartShoppingCartID != 0) {
                $shoppingCart = $member->SilvercartShoppingCart();
                $shoppingCart->delete();
            }
        }

        if (isset($_SESSION['paypal_module_payer_id'])) {
            unset($_SESSION['paypal_module_payer_id']);
        }
        if (isset($_SESSION['paypal_module_token'])) {
            unset($_SESSION['paypal_module_token']);
        }
    }

    /**
     * Removes a prefix from a checkout address data array.
     *
     * @param string $prefix Prefix
     * @param array  $data   Checkout address data
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.07.2011
     */
    public function extractAddressDataFrom($prefix, $data) {
        $addressData = array();
        $shippingDataFields = array(
            $prefix.'_Salutation'       => 'Salutation',
            $prefix.'_FirstName'        => 'FirstName',
            $prefix.'_Surname'          => 'Surname',
            $prefix.'_Addition'         => 'Addition',
            $prefix.'_Street'           => 'Street',
            $prefix.'_StreetNumber'     => 'StreetNumber',
            $prefix.'_Postcode'         => 'Postcode',
            $prefix.'_City'             => 'City',
            $prefix.'_Phone'            => 'Phone',
            $prefix.'_PhoneAreaCode'    => 'PhoneAreaCode',
            $prefix.'_Country'          => 'CountryID'
        );
        
        if (is_array($data)) {
            foreach ($shippingDataFields as $shippingFieldName => $dataFieldName) {
                if (isset($data[$shippingFieldName])) {
                    $addressData[$dataFieldName] = $data[$shippingFieldName];
                }
            }
        }

        return $addressData;
    }

    /**
     * Adds a prefix to a plain address data array.
     *
     * @param string $prefix Prefix
     * @param array  $data   Plain address data
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.07.2011
     */
    public function joinAddressDataTo($prefix, $data) {
        $addressData = array();
        $checkoutDataFields = array(
            $prefix.'_Salutation'       => 'Salutation',
            $prefix.'_FirstName'        => 'FirstName',
            $prefix.'_Surname'          => 'Surname',
            $prefix.'_Addition'         => 'Addition',
            $prefix.'_Street'           => 'Street',
            $prefix.'_StreetNumber'     => 'StreetNumber',
            $prefix.'_Postcode'         => 'Postcode',
            $prefix.'_City'             => 'City',
            $prefix.'_Phone'            => 'Phone',
            $prefix.'_PhoneAreaCode'    => 'PhoneAreaCode',
            $prefix.'_Country'          => 'CountryID',
            $prefix.'_Country'          => 'SilvercartCountryID',
        );
        
        if (is_array($data)) {
            foreach ($checkoutDataFields as $checkoutFieldName => $dataFieldName) {
                if (isset($data[$dataFieldName])) {
                    $addressData[$checkoutFieldName] = $data[$dataFieldName];
                }
            }
        }

        return $addressData;
    }

    /**
     * Indicates wether ui elements for removing items and altering their
     * quantity should be shown in the shopping cart templates.
     *
     * During the checkout process the user may not be able to alter the
     * shopping cart.
     *
     * @return boolean false
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.02.2011
     */
    public function getEditableShoppingCart() {
        return false;
    }
    
    /**
     * Action to delete an address. Checks, whether the given address is related
     * to the logged in customer and deletes it.
     *
     * @param SS_HTTPRequest $request The given request
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.07.2011
     */
    public function deleteAddress(SS_HTTPRequest $request) {
        $silvercartAddressHolder = new SilvercartAddressHolder_Controller();
        $silvercartAddressHolder->deleteAddress($request);
    }
    
    /**
     * Renders a form to edit addresses and handles it's sumbit event.
     *
     * @param SS_HTTPRequest $request the given request
     * 
     * @return type 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.07.2011
     */
    public function editAddress(SS_HTTPRequest $request) {
        $rendered = '';
        $params = $request->allParams();
        if (array_key_exists('ID', $params)
         && !empty ($params['ID'])) {
            if (strtolower($params['OtherID']) == 'customhtmlformsubmit') {
                $result = $this->CustomHtmlFormSubmit($request);
                $form = $this->getRegisteredCustomHtmlForm('SilvercartEditAddressForm');
                if ($form->submitSuccess) {
                    $form->addMessage(_t('SilvercartAddressHolder.ADDED_ADDRESS_SUCCESS', 'Your address was successfully saved.'));
                } else {
                    $form->addMessage(_t('SilvercartAddressHolder.ADDED_ADDRESS_FAILURE', 'Your address could not be saved.'));
                    $rendered = $this->renderWith(array('SilvercartCheckoutFormStep2RegularEditAddress','Page'));
                }
            } else {
                $addressID = (int) $params['ID'];
                if (Member::currentUser()->SilvercartAddresses()->containsIDs(array($addressID))) {
                    // Address contains to logged in user - render edit form
                    $rendered = $this->renderWith(array('SilvercartCheckoutFormStep2RegularEditAddress','Page'));
                } else {
                    // possible break in attempt!
                    $this->setErrorMessage(_t('SilvercartAddressHolder.ADDRESS_NOT_FOUND', 'Sorry, but the given address was not found.'));
                }
            }
        }
        if ($rendered == '' && is_null(Director::redirected_to())) {
            Director::redirectBack();
        }
        return $rendered;
    }
    
    /**
     * Checks whether the current step is the payment step
     *
     * @return bool
     * 
     * @author Sascha Köhler <skoehler@pixeltricks.de>
     * @since 19.7.2011
     */
    public function currentStepIsPaymentStep() {
        return $this->stepMapping[$this->getCurrentStep()]['class'] == 'SilvercartCheckoutFormStep4';
    }
}
