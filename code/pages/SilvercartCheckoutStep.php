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

    /**
     * icon for site tree
     *
     * @var string
     */
    public static $icon = "silvercart/images/page_icons/checkout_page";

    /**
     * Deletes the step session data.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 02.10.2012
     */
    public static function deleteSessionStepData() {
        if (isset($_SESSION['CustomHtmlFormStep']) &&
            is_array($_SESSION['CustomHtmlFormStep'])) {

            foreach ($_SESSION['CustomHtmlFormStep'] as $sessionIdx => $sessionContent) {
                unset($_SESSION['CustomHtmlFormStep'][$sessionIdx]);
            }
        }
    }

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
    }
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
     * @var array
     */
    protected $preferences = array(
        'templateDir' => ''
    );
    /**
     * Enthaelt das Zahlungsmodul-Objekt.
     *
     * @var PaymentMethod
     */
    protected $paymentMethodObj = false;
    
    /**
     * cache key for the current step
     *
     * @var string
     */
    protected $cacheKey = null;

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
                 SilvercartConfig::MinimumOrderValue()->getAmount() > $shoppingCart->getAmountTotalWithoutFees()->getAmount()) {
                
                $_SESSION['Silvercart']['errors'][] = sprintf(
                    _t('SilvercartShoppingCart.ERROR_MINIMUMORDERVALUE_NOT_REACHED'),
                    SilvercartConfig::MinimumOrderValue()->Nice()
                );
                
                $this->redirect(SilvercartPage_Controller::PageByIdentifierCode('SilvercartCartPage')->Link());
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
     * Returns a cache key for the current step
     * 
     * @return string
     */
    public function getCacheKey() {
        if (is_null($this->cacheKey)) {
            $member     = Member::currentUser();
            $stepData   = $this->getStepData($this->getCurrentStep());
            $cacheKey   = '';

            if ($member) {
                $cart = $member->getCart();
                $cacheKey .= $member->ID;
                $cacheKey .= sha1($cart->LastEdited) . md5($cart->LastEdited);
            }
            $cacheKey   .= $this->getCurrentStep();
            if (is_array($stepData)) {
                $stepDataString  = '';
                foreach ($stepData as $parameterName => $parameterValue) {
                    $stepDataString .= $parameterName . ':' . $parameterValue . ';';
                }
                $cacheKey .= sha1($stepDataString);
            } else {
                $cacheKey .= (int) $stepData;
            }

            $this->cacheKey = $cacheKey;
        }
        return $this->cacheKey;
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
     * Gibt die Fehlerliste als DataList zurueck.
     *
     * @return DataList
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
            $prefix.'_TaxIdNumber'      => 'TaxIdNumber',
            $prefix.'_Company'          => 'Company',
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
            $prefix.'_Fax'              => 'Fax',
            $prefix.'_Country'          => 'CountryID',
            $prefix.'_Country'          => 'SilvercartCountryID',
            $prefix.'_PostNumber'       => 'PostNumber',
            $prefix.'_Packstation'      => 'Packstation',
            $prefix.'_IsPackstation'    => 'IsPackstation',
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
     * @param string         $context specifies the context from the action to adjust redirect behaviour
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.07.2011
     */
    public function deleteAddress(SS_HTTPRequest $request, $context = 'SilvercartCheckoutStep') {
        $silvercartAddressHolder = new SilvercartAddressHolder_Controller();
        $silvercartAddressHolder->deleteAddress($request, $context);
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
                $this->CustomHtmlFormSubmit($request);
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
                    $rendered = $this->renderWith(array('SilvercartCheckoutStep','Page'));
                }
            }
        }
        if ($rendered == '' && is_null($this->redirectedTo())) {
            $this->redirectBack();
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
    
    /**
     * Returns the invoice address set in checkout
     *
     * @return SilvercartAddress 
     */
    public function getInvoiceAddress() {
        return $this->getAddress('Invoice');
    }
    
    /**
     * Returns the shipping address set in checkout
     *
     * @return SilvercartAddress 
     */
    public function getShippingAddress() {
        return $this->getAddress('Shipping');
    }
    
    /**
     * Returns the shipping or invoice address set in checkout
     *
     * @param string $prefix The prefix to use
     *
     * @return SilvercartAddress 
     */
    public function getAddress($prefix) {
        $address    = false;
        $stepData   = $this->getCombinedStepData();
        if ($stepData != false) {
            $addressData = SilvercartTools::extractAddressDataFrom($prefix, $stepData);
            if (!empty($addressData) &&
                array_key_exists('CountryID', $addressData)) {
                $address = new SilvercartAddress($addressData);
                $address->SilvercartCountryID = $addressData['CountryID'];
            }
        }
        return $address;
    }
}
