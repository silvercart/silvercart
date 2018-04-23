<?php

namespace SilverCart\Forms;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\CustomForm;
use SilverCart\Forms\FormFields\TextareaField;
use SilverCart\Forms\FormFields\TextField;
use SilverCart\Model\ShopEmail;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Pages\RevocationFormPage;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Security\Member;

/**
 * Form to send an order revocation.
 *
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class RevocationForm extends CustomForm {
    
    /**
     * Custom extra CSS classes.
     *
     * @var array
     */
    protected $customExtraClasses = [
        'form-horizontal',
        'grouped',
    ];
    
    /**
     * Don't enable Security token for this type of form because we'll run
     * into caching problems when using it.
     * 
     * @var boolean
     */
    protected $securityTokenEnabled = false;
    
    /**
     * List of required fields.
     *
     * @var array
     */
    private static $requiredFields = [
        'Email',
        'FirstName',
        'Surname',
    ];

    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields() {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $email              = '';
            $orderDate          = '';
            $orderNumber        = '';
            $orderPositions     = '';
            $orderID            = '';
            $existingOrderField = null;
            $address            = Address::singleton();
            $customer           = Customer::currentRegisteredCustomer();
            if ($customer instanceof Member) {
                $email              = $customer->Email;
                $address            = $customer->InvoiceAddress();
                $contextOrder       = $this->getOrderByRequest();
                $existingOrderField = DropdownField::create('ExistingOrder', $this->fieldLabel('OrderHistory'), $customer->Orders()->map('ID', 'Title', ' '));
                $existingOrderField->setHasEmptyDefault(true);
                
                if ($contextOrder instanceof Order &&
                    $contextOrder->exists() &&
                    $contextOrder->MemberID == $customer->ID) {

                    $existingOrderField->setValue($orderID);
                    $address        = $contextOrder->InvoiceAddress();
                    $orderDate      = date(_t(Tools::class . '.DATEFORMAT', 'm/d/Y'), strtotime($contextOrder->Created));
                    $orderNumber    = $contextOrder->OrderNumber;
                    $orderPositions = $contextOrder->getPositionsAsString(false, true);
                }
            }
            $fields += [
                TextareaField::create('RevocationOrderData', $this->fieldLabel('RevocationOrderData'), $orderPositions),
                EmailField::create('Email', $address->fieldLabel('Email'), $email),
                TextField::create('OrderDate', $this->fieldLabel('OrderDate'), $orderDate),
                TextField::create('OrderNumber', $this->fieldLabel('OrderNumber'), $orderNumber),
                $salutationField = DropdownField::create('Salutation', $address->fieldLabel('Salutation'), Tools::getSalutationMap(), $address->Salutation),
                TextField::create('FirstName', $address->fieldLabel('FirstName'), $address->FirstName),
                TextField::create('Surname', $address->fieldLabel('Surname'), $address->Surname),
                TextField::create('Addition', $address->fieldLabel('Addition'), $address->Addition),
                TextField::create('Street', $address->fieldLabel('Street'), $address->Street),
                TextField::create('StreetNumber', $address->fieldLabel('StreetNumber'), $address->StreetNumber),
                TextField::create('Postcode', $address->fieldLabel('Postcode'), $address->Postcode),
                TextField::create('City', $address->fieldLabel('City'), $address->City),
                $countryField = DropdownField::create('Country', $address->fieldLabel('Country'), Country::getPrioritiveDropdownMap(true, Tools::field_label('PleaseChoose')), $address->CountryID),
            ];
            if (!is_null($existingOrderField)) {
                $fields[] = $existingOrderField;
            }
            $salutationField->setHasEmptyDefault(true);
            $countryField->setHasEmptyDefault(true);
        });
        return parent::getCustomFields();
    }
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomActions() {
        $this->beforeUpdateCustomActions(function (array &$actions) {
            $actions += [
                FormAction::create('submit', Page::singleton()->fieldLabel('Submit'))
                    ->setUseButtonTag(true)->addExtraClass('btn-primary')
            ];
        });
        return parent::getCustomActions();
    }
    
    /**
     * Submits the form.
     * 
     * @param array      $data Submitted data
     * @param CustomForm $form Form
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function doSubmit($data, CustomForm $form) {
        $data['RevocationOrderData'] = str_replace('\r\n', "\n", $data['RevocationOrderData']);

        $config    = Config::getConfig();
        $country   = Country::get()->byID($data['Country']);
        $variables = [
            'Email'               => $data['Email'],
            'Salutation'          => $data['Salutation'],
            'FirstName'           => $data['FirstName'],
            'Surname'             => $data['Surname'],
            'Street'              => $data['Street'],
            'StreetNumber'        => $data['StreetNumber'],
            'Addition'            => $data['Addition'],
            'Postcode'            => $data['Postcode'],
            'City'                => $data['City'],
            'Country'             => $country,
            'OrderDate'           => $data['OrderDate'],
            'OrderNumber'         => $data['OrderNumber'],
            'RevocationOrderData' => str_replace('\r\n', '<br/>', nl2br($data['RevocationOrderData'])),
            'CurrentDate'         => $this->getCurrentDate(),
            'ShopName'            => $config->ShopName,
            'ShopStreet'          => $config->ShopStreet,
            'ShopStreetNumber'    => $config->ShopStreetNumber,
            'ShopPostcode'        => $config->ShopPostcode,
            'ShopCity'            => $config->ShopCity,
            'ShopCountry'         => $config->ShopCountry(),
        ];
        
        ShopEmail::send(
            'RevocationNotification',
            Config::DefaultMailOrderNotificationRecipient(),
            $variables
        );
        ShopEmail::send(
            'RevocationConfirmation',
            $data['Email'],
            $variables
        );
        
        $revocationPage = RevocationFormPage::get()->first();
        $this->getController()->redirect($revocationPage->Link('success'));
    }
    
    /**
     * Returns the context order based on submitted request data.
     * 
     * @return Order
     */
    protected function getOrderByRequest() {
        $order    = null;
        $customer = Customer::currentRegisteredCustomer();
        $request  = $this->getRequest();
        $orderID  = $request->postVar('ExistingOrder');
        if (empty($orderID)) {
            $orderID = $request->getVar('o');
        }
        if (!is_numeric($orderID)) {
            $orderID = '';
        }
        if (!empty($orderID)) {
            $order = $customer->Order()->byID($orderID);
        }
        return $order;
    }
    
    /**
     * Returns the current date.
     * 
     * @return string
     */
    public function getCurrentDate() {
        return date(_t(Tools::class . '.DATEFORMAT', 'm/d/Y'));
    }
    
    /**
     * Returns the current customer.
     * 
     * @return Member
     */
    public function getCustomer() {
        return Customer::currentRegisteredCustomer();
    }
    
}