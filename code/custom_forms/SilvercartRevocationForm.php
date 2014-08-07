<?php
/**
 * Copyright 2014 pixeltricks GmbH
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
 * Form to send a order revocation.
 *
 * @package Silvercart
 * @subpackage Forms
 * @copyright pixeltricks GmbH
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 05.06.2014
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartRevocationForm extends CustomHtmlForm {

    /**
     * Indicates whether to exclude this form from caching or not
     *
     * @var bool
     */
    protected $excludeFromCache = true;

    /**
     * Returns the form fields
     * 
     * @param bool $withUpdate Execute update method of decorators?
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2014
     */
    public function getFormFields($withUpdate = true) {
        if (empty($this->formFields)) {
            $email          = '';
            $orderDate      = '';
            $orderNumber    = '';
            $orderPositions = '';
            $orderID        = '';
            $customer    = SilvercartCustomer::currentRegisteredCustomer();
            if ($customer instanceof Member) {
                $email = $customer->Email;
                $address = $customer->SilvercartInvoiceAddress();
                $existingOrder = array(
                    'type'      => 'DropdownField',
                    'title'     => _t('SilvercartRevocationForm.OrderHistory'),
                    'value'     => $customer->SilvercartOrder()->map('ID', 'Title', ' '),
                );
                if (array_key_exists('ExistingOrder', $_POST)) {
                    $orderID = $_POST['ExistingOrder'];
                } elseif (array_key_exists('o', $_GET)) {
                    $orderID = $_GET['o'];
                    if (!is_numeric($orderID)) {
                        $orderID = '';
                    }
                }
                if (!empty($orderID)) {
                    $order   = $customer->SilvercartOrder()->find('ID', $orderID);
                    if ($order instanceof SilvercartOrder &&
                        $order->exists()) {
                        
                        $existingOrder['selectedValue'] = $orderID;
                        $address        = $order->SilvercartInvoiceAddress();
                        $orderDate      = date(_t('Silvercart.DATEFORMAT'), strtotime($order->Created));
                        $orderNumber    = $order->OrderNumber;
                        $orderPositions = $order->getPositionsAsString(false, true);
                    }
                }
            } else {
                $address = singleton('SilvercartAddress');
                $existingOrder = array();
            }
            $this->formFields   = array(
                
                'RevocationOrderData' => array(
                    'type'      => 'TextareaField',
                    'title'     => _t('SilvercartRevocationForm.RevocationOrderData'),
                    'value'     => $orderPositions,
                ),
                'Email' => array(
                    'type'      => 'TextField',
                    'title'     => _t('SilvercartAddress.EMAIL'),
                    'value'     => $email,
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'OrderDate' => array(
                    'type'      => 'TextField',
                    'title'     => _t('SilvercartRevocationForm.OrderDate'),
                    'value'     => $orderDate,
                ),
                'OrderNumber' => array(
                    'type'      => 'TextField',
                    'title'     => _t('SilvercartRevocationForm.OrderNumber'),
                    'value'     => $orderNumber,
                ),
                
                'Salutation' => array(
                    'type' => 'DropdownField',
                    'title' => $address->fieldLabel('Salutation'),
                    'value' => array(
                        ''      => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE'),
                        "Frau"  => _t('SilvercartAddress.MISSES'),
                        "Herr"  => _t('SilvercartAddress.MISTER')
                    ),
                    'selectedValue'     => $address->Salutation,
                ),
                'FirstName' => array(
                    'type'      => 'TextField',
                    'title'     => $address->fieldLabel('FirstName'),
                    'value'     => $address->FirstName,
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'Surname' => array(
                    'type'      => 'TextField',
                    'title'     => $address->fieldLabel('Surname'),
                    'value'     => $address->Surname,
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'Addition' => array(
                    'type'      => 'TextField',
                    'title'     => $address->fieldLabel('Addition'),
                    'value'     => $address->Addition,
                ),
                'Street' => array(
                    'type'      => 'TextField',
                    'title'     => $address->fieldLabel('Street'),
                    'value'     => $address->Street,
                ),
                'StreetNumber' => array(
                    'type'      => 'TextField',
                    'title'     => $address->fieldLabel('StreetNumber'),
                    'value'     => $address->StreetNumber,
                ),
                'Postcode' => array(
                    'type'      => 'TextField',
                    'title'     => $address->fieldLabel('Postcode'),
                    'value'     => $address->Postcode,
                ),
                'City' => array(
                    'type'      => 'TextField',
                    'title'     => $address->fieldLabel('City'),
                    'value'     => $address->City,
                ),
                'Country' => array(
                    'type'              => 'DropdownField',
                    'title'             => $address->fieldLabel('SilvercartCountry'),
                    'value'             => SilvercartCountry::getPrioritiveDropdownMap(true, _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE')),
                    'selectedValue'     => $address->SilvercartCountryID,
                )
            );
            if (!empty($existingOrder)) {
                $this->formFields['ExistingOrder'] = $existingOrder;
            }
        }
        return parent::getFormFields($withUpdate);
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2014
     */
    protected function submitSuccess($data, $form, $formData) {

        $formData['RevocationOrderData'] = str_replace('\r\n', "\n", $formData['RevocationOrderData']);

        $config    = SilvercartConfig::getConfig();
        $country   = DataObject::get_by_id('SilvercartCountry', $formData['Country']);
        $variables = array(
            'Email'               => $formData['Email'],
            'Salutation'          => $formData['Salutation'],
            'FirstName'           => $formData['FirstName'],
            'Surname'             => $formData['Surname'],
            'Street'              => $formData['Street'],
            'StreetNumber'        => $formData['StreetNumber'],
            'Addition'            => $formData['Addition'],
            'Postcode'            => $formData['Postcode'],
            'City'                => $formData['City'],
            'Country'             => $country,
            'OrderDate'           => $formData['OrderDate'],
            'OrderNumber'         => $formData['OrderNumber'],
            'RevocationOrderData' => str_replace('\r\n', '<br/>', nl2br($formData['RevocationOrderData'])),
            'CurrentDate'         => $this->getCurrentDate(),
            'ShopName'            => $config->ShopName,
            'ShopStreet'          => $config->ShopStreet,
            'ShopStreetNumber'    => $config->ShopStreetNumber,
            'ShopPostcode'        => $config->ShopPostcode,
            'ShopCity'            => $config->ShopCity,
            'ShopCountry'         => $config->ShopCountry(),
        );
        
        
        SilvercartShopEmail::send(
            'RevocationNotification',
            SilvercartConfig::DefaultMailOrderNotificationRecipient(),
            $variables
        );
        SilvercartShopEmail::send(
            'RevocationConfirmation',
            $formData['Email'],
            $variables
        );
        
        $revocationPage = DataObject::get_one('SilvercartRevocationFormPage');
        $this->Controller()->redirect($revocationPage->Link('success'));
    }
    
    /**
     * Returns the current date.
     * 
     * @return string
     */
    public function getCurrentDate() {
        return date(_t('Silvercart.DATEFORMAT'));
    }
    
    /**
     * Returns the current customer.
     * 
     * @return Member
     */
    public function getCustomer() {
        return SilvercartCustomer::currentRegisteredCustomer();
    }
    
}