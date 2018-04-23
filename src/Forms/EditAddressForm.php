<?php

namespace SilverCart\Forms;

use SilverCart\Dev\Tools;
use SilverCart\Forms\AddressForm;
use SilverCart\Forms\CustomForm;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\Validator;
use SilverStripe\Control\RequestHandler;
use SilverStripe\Security\Member;

/** 
 * Customer form for editing an address.
 *
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class EditAddressForm extends AddressForm {

    /**
     * Contains the address object
     *
     * @var Address
     */
    protected $address;
    
    /**
     * Determines if the field values are already filled with the context address data.
     *
     * @var bool
     */
    protected $filledFieldValues = false;
    
    /**
     * Sets the context address.
     * 
     * @param Address $address Address
     */
    public function setAddress(Address $address) {
        if ($address->canEdit()) {
            $this->address = $address;
        }
    }

    /**
     * Returns the context address.
     * 
     * @return Address
     */
    public function getAddress() {
        return $this->address;
    }
    
    /**
     * Create a new form, with the given fields an action buttons.
     *
     * @param Address        $address    Address to edit
     * @param RequestHandler $controller Optional parent request handler
     * @param string         $name       The method on the controller that will return this form object.
     * @param FieldList      $fields     All of the fields in the form - a {@link FieldList} of {@link FormField} objects.
     * @param FieldList      $actions    All of the action buttons in the form - a {@link FieldLis} of {@link FormAction} objects
     * @param Validator      $validator  Override the default validator instance (Default: {@link RequiredFields})
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.11.2017
     */
    public function __construct(Address $address, RequestHandler $controller = null, $name = self::DEFAULT_NAME, FieldList $fields = null, FieldList $actions = null, Validator $validator = null) {
        $this->setAddress($address);
        parent::__construct($controller, $name, $fields, $actions, $validator);
    }

    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields() {
        return array_merge(
            parent::getCustomFields(),
            [
                HiddenField::create('AddressID', 'AddressID', $this->getAddress()->ID),
            ]
        );
    }

    /**
     * Returns the form fields.
     * 
     * @return FieldList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.11.2017
     */
    public function Fields() {
        $fields = parent::Fields();
        
        if (!$this->filledFieldValues) {
            $this->filledFieldValues = true;
            $member  = Customer::currentUser();
            $address = $this->getAddress();

            if ($address instanceof Address &&
                $address->exists() &&
                $address->canEdit($member)) {

                $fields->dataFieldByName('Salutation')->setValue($address->Salutation);
                $fields->dataFieldByName('AcademicTitle')->setValue($address->AcademicTitle);
                $fields->dataFieldByName('FirstName')->setValue($address->FirstName);
                $fields->dataFieldByName('Surname')->setValue($address->Surname);
                $fields->dataFieldByName('Addition')->setValue($address->Addition);
                $fields->dataFieldByName('Street')->setValue($address->Street);
                $fields->dataFieldByName('StreetNumber')->setValue($address->StreetNumber);
                $fields->dataFieldByName('Postcode')->setValue($address->Postcode);
                $fields->dataFieldByName('City')->setValue($address->City);
                $fields->dataFieldByName('PhoneAreaCode')->setValue($address->PhoneAreaCode);
                $fields->dataFieldByName('Phone')->setValue($address->Phone);
                $fields->dataFieldByName('Fax')->setValue($address->Fax);
                $fields->dataFieldByName('Country')->setValue($address->Country()->ID);
                if ($this->EnablePackstation()) {
                    $fields->dataFieldByName('PostNumber')->setValue($address->PostNumber);
                    $fields->dataFieldByName('Packstation')->setValue($address->Packstation);
                    $fields->dataFieldByName('IsPackstation')->setValue($address->IsPackstation);
                }
                if ($this->EnableBusinessCustomers()) {
                    $fields->dataFieldByName('Company')->setValue($address->Company);
                    $fields->dataFieldByName('TaxIdNumber')->setValue($address->TaxIdNumber);
                }
            }
        }
        
        return $fields;
    }

    /**
     * executed if there are no valdation errors on submit
     * Form data is saved in session
     *
     * @param array      $data Submitted data
     * @param CustomForm $form Form object
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.11.2017
     */
    public function doSubmit($data, CustomForm $form) {
        $member  = Customer::currentUser();
        $address = $this->getAddress();
        if ($member instanceof Member &&
            $address instanceof Address &&
            $address->canEdit($member)) {

            $address->castedUpdate($data);
            $country = Country::get()->byID($data['Country']);
            if ($country instanceof Country &&
                $country->exists()) {
                $address->CountryID = $country->ID;
            }
            $address->write();
            
            $redirectTo = Tools::PageByIdentifierCode("SilvercartAddressHolder")->Link();
            if (!empty($data['redirect'])) {
                $redirectTo = $data['redirect'];
            }
            $this->getController()->redirect($redirectTo);
            $this->getController()->setSuccessMessage(_t(AddressHolder::class . '.ADDED_ADDRESS_SUCCESS', 'Your address was successfully saved.'));
        } else {
            $this->getController()->redirectBack();
        }
    }
}
