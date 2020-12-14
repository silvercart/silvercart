<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\BlacklistEntry;
use SilverCart\Model\Pages\MetaNavigationHolder;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

/**
 * show an process a contact form.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ContactFormPage extends MetaNavigationHolder
{
    /**
     * DB attributes.
     *
     * @var array
     */
    private static $db = [
        'ResponseContent'       => 'HTMLText',
        'EnableStreet'          => 'Boolean(0)',
        'StreetIsRequired'      => 'Boolean(0)',
        'EnableCity'            => 'Boolean(0)',
        'CityIsRequired'        => 'Boolean(0)',
        'EnableCountry'         => 'Boolean(0)',
        'CountryIsRequired'     => 'Boolean(0)',
        'EnablePhoneNumber'     => 'Boolean(0)',
        'PhoneNumberIsRequired' => 'Boolean(0)',
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartContactFormPage';
    /**
     * We set a custom icon for this page type here
     *
     * @var string
     */
    private static $icon = "silvercart/silvercart:client/img/page_icons/metanavigation_page-file.gif";
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this); 
    }
    
    /**
     * Returns the field labels.
     * 
     * @param bool $includerelations Include relations?
     * 
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'ResponseContent'       => _t(ContactFormPage::class . '.ResponseContent', 'Use field for street'),
            'EnableStreet'          => _t(ContactFormPage::class . '.EnableStreet', 'Use field for street'),
            'StreetIsRequired'      => _t(ContactFormPage::class . '.StreetIsRequired', 'Street is required'),
            'EnableCity'            => _t(ContactFormPage::class . '.EnableCity', 'Use field for city'),
            'CityIsRequired'        => _t(ContactFormPage::class . '.CityIsRequired', 'City is required'),
            'EnableCountry'         => _t(ContactFormPage::class . '.EnableCountry', 'Use field for country'),
            'CountryIsRequired'     => _t(ContactFormPage::class . '.CountryIsRequired', 'Country is required'),
            'EnablePhoneNumber'     => _t(ContactFormPage::class . '.EnablePhoneNumber', 'Use field for phone number'),
            'PhoneNumberIsRequired' => _t(ContactFormPage::class . '.PhoneNumberIsRequired', 'Phone number is required'),
            'FormFieldsTab'         => _t(ContactFormPage::class . '.FormFieldsTab', 'Form Fields'),
        ]);
    }
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $fields->addFieldToTab('Root.Main', HTMLEditorField::create('ResponseContent', $this->fieldLabel('ResponseContent')));
            $fields->findOrMakeTab('Root.FormFields', $this->fieldLabel('FormFieldsTab'));
            $fields->addFieldToTab('Root.FormFields', CheckboxField::create('EnableStreet', $this->fieldLabel('EnableStreet')));
            $fields->addFieldToTab('Root.FormFields', CheckboxField::create('StreetIsRequired', $this->fieldLabel('StreetIsRequired')));
            $fields->addFieldToTab('Root.FormFields', CheckboxField::create('EnableCity', $this->fieldLabel('EnableCity')));
            $fields->addFieldToTab('Root.FormFields', CheckboxField::create('CityIsRequired', $this->fieldLabel('CityIsRequired')));
            $fields->addFieldToTab('Root.FormFields', CheckboxField::create('EnableCountry', $this->fieldLabel('EnableCountry')));
            $fields->addFieldToTab('Root.FormFields', CheckboxField::create('CountryIsRequired', $this->fieldLabel('CountryIsRequired')));
            $fields->addFieldToTab('Root.FormFields', CheckboxField::create('EnablePhoneNumber', $this->fieldLabel('EnablePhoneNumber')));
            $fields->addFieldToTab('Root.FormFields', CheckboxField::create('PhoneNumberIsRequired', $this->fieldLabel('PhoneNumberIsRequired')));
            BlacklistEntry::getBlackListCMSFields($fields);
        });
        return parent::getCMSFields();
    }
}