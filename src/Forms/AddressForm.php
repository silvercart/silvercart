<?php

namespace SilverCart\Forms;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\CustomForm;
use SilverCart\Forms\FormFields\TextField;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Pages\Page;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\OptionsetField;

/** 
 * Customer form for adding an address.
 *
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class AddressForm extends CustomForm
{
    /**
     * List of required fields.
     *
     * @var array
     */
    private static $requiredFields = [
        'Salutation',
        'FirstName',
        'Surname',
        'Street',
        'StreetNumber',
        'Postcode',
        'City',
        'Country',
    ];

    /**
     * Returns the required fields.
     * 
     * @return array
     */
    public function getRequiredFields()
    {
        $originalFields    = parent::getRequiredFields();
        $packstationFields = $this->getRequiredPackstationFields($originalFields);
        $requiredFields = array_merge(
                $originalFields,
                $this->getRequiredBusinessFields(),
                $packstationFields
        );
        
        return $requiredFields;
    }
    
    /**
     * Returns the required business fields.
     * 
     * @return array
     */
    protected function getRequiredBusinessFields()
    {
        $requiredBusinessFields = [];
        if ($this->EnableBusinessCustomers()) {
            $requiredBusinessFields = [
                'TaxIdNumber' => [
                    'isFilledInDependentOn' => [
                        'field'     => 'IsBusinessAccount',
                        'hasValue'  => '1'
                    ],
                ],
                'Company' => [
                    'isFilledInDependentOn' => [
                        'field'     => 'IsBusinessAccount',
                        'hasValue'  => '1'
                    ],
                ],
            ];
        }
        return $requiredBusinessFields;
    }
    
    /**
     * Returns the packstation fields.
     * 
     * @param array &$originalFields Original required fields
     * 
     * @return array
     */
    protected function getRequiredPackstationFields(&$originalFields)
    {
        $requiredPackstationFields = [];
        if ($this->EnablePackstation()) {
            $streetKey = array_search('Street', $originalFields);
            if (array_key_exists($streetKey, $originalFields)) {
                unset($originalFields[$streetKey]);
            }
            $streetNumberKey = array_search('StreetNumber', $originalFields);
            if (array_key_exists($streetNumberKey, $originalFields)) {
                unset($originalFields[$streetNumberKey]);
            }
            $requiredPackstationFields = [
                'Street' => [
                    'isFilledInDependentOn' => [
                        'field'     => 'IsPackstation',
                        'hasValue'  => '0'
                    ],
                ],
                'StreetNumber' => [
                    'isFilledInDependentOn' => [
                        'field'     => 'IsPackstation',
                        'hasValue'  => '0'
                    ],
                ],
                'IsPackstation',
                'PostNumber' => [
                    'isFilledInDependentOn' => [
                        'field'     => 'IsPackstation',
                        'hasValue'  => '1'
                    ],
                ],
                'Packstation' => [
                    'isFilledInDependentOn' => [
                        'field'     => 'IsPackstation',
                        'hasValue'  => '1'
                    ],
                ],
            ];
        }
        return $requiredPackstationFields;
    }

    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields()
    {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $address = Address::singleton();
            $formFields = [
                DropdownField::create('Salutation', $address->fieldLabel('Salutation'), Tools::getSalutationMap())->setHasEmptyDefault(true),
                TextField::create('AcademicTitle', $address->fieldLabel('AcademicTitle')),
                TextField::create('FirstName', $address->fieldLabel('FirstName')),
                TextField::create('Surname', $address->fieldLabel('Surname')),
                TextField::create('Addition', $address->fieldLabel('Addition')),
                TextField::create('Street', $address->fieldLabel('Street')),
                TextField::create('StreetNumber', $address->fieldLabel('StreetNumber')),
                TextField::create('Postcode', $address->fieldLabel('Postcode')),
                TextField::create('City', $address->fieldLabel('City')),
                DropdownField::create('Country', $address->fieldLabel('Country'), Country::getPrioritiveDropdownMap(true, Tools::field_label('PleaseChoose')))->setHasEmptyDefault(true),
                TextField::create('Phone', $address->fieldLabel('Phone')),
                TextField::create('Fax', $address->fieldLabel('Fax')),
            ];
            $fields = array_merge(
                    $fields,
                    $formFields,
                    $this->getBusinessFields(),
                    $this->getPackstationFields()
            );
        });
        return parent::getCustomFields();
    }
    
    /**
     * Returns the business fields.
     * 
     * @return array
     */
    protected function getBusinessFields()
    {
        $businessFields = [];
        if ($this->EnableBusinessCustomers()) {
            $address = Address::singleton();
            $businessFields = [
                CheckboxField::create('IsBusinessAccount', $address->fieldLabel('IsBusinessAccount')),
                TextField::create('TaxIdNumber', $address->fieldLabel('TaxIdNumber'), '', 30),
                TextField::create('Company', $address->fieldLabel('Company'), '', 50),
            ];
        }
        return $businessFields;
    }
    
    /**
     * Returns the packstation fields.
     * 
     * @return array
     */
    protected function getPackstationFields()
    {
        $packstationFields = [];
        if ($this->EnablePackstation()) {
            $address = Address::singleton();
            $isPackstationSource = [
                '0' => $address->fieldLabel('UseAbsoluteAddress'),
                '1' => $address->fieldLabel('UsePackstation'),
            ];
            $packstationFields = [
                OptionsetField::create('IsPackstation', $address->fieldLabel('AddressType'), $isPackstationSource, '0'),
                TextField::create('PostNumber', $address->fieldLabel('PostNumber')),
                TextField::create('Packstation', $address->fieldLabel('Packstation')),
            ];
        } else {
            $packstationFields = [
                HiddenField::create('IsPackstation', 'IsPackstation', '0'),
            ];
        }
        return $packstationFields;
    }
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomActions()
    {
        $this->beforeUpdateCustomActions(function (array &$actions) {
            $actions += [
                FormAction::create('submit', Page::singleton()->fieldLabel('Save'))
                    ->setUseButtonTag(true)->addExtraClass('btn-primary')
            ];
        });
        return parent::getCustomActions();
    }
    
    /**
     * Indicates wether business customers should be enabled.
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 14.04.2015
     */
    public function EnableBusinessCustomers()
    {
        $enableBusinessCustomers = false;
        $customer                = $this->getCustomer();
        if (Config::enableBusinessCustomers()
         || ($customer instanceof Member
          && $customer->isB2BCustomer())
        ) {
            $enableBusinessCustomers = true;
        }
        return $enableBusinessCustomers;
    }

    /**
     * Indicates wether business customers should be enabled.
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.10.2012
     */
    public function EnablePackstation()
    {
        return Config::enablePackstation();
    }
    
    /**
     * Returns the current customer.
     * 
     * @return Member
     */
    public function getCustomer()
    {
        return Customer::currentUser();
    }
    
    /**
     * Returns an Address field label for the given name if no field label found.
     * 
     * @param string $fieldName Field name
     * @param array  $params    i18n variables to use
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.11.2017
     */
    public function fieldLabel($fieldName, $params = [])
    {
        $fieldLabel = parent::fieldLabel($fieldName, $params);
        if ($fieldLabel == $fieldName) {
            $fieldLabel = Address::singleton()->fieldLabel($fieldName);
        }
        return $fieldLabel;
    }
    
    /**
     * Returns the extra CSS classes as a string.
     * Adds the CSS class 'sc-address-form-with-packstation' if packstations are
     * enabled.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.09.2018
     */
    public function extraClass()
    {
        if ($this->EnablePackstation()) {
            $this->addExtraClass('sc-address-form-with-packstation');
        }
        return parent::extraClass();
    }
}
