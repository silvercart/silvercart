<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\MetaNavigationHolder;
use SilverStripe\Forms\CheckboxField;
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
class ContactFormPage extends MetaNavigationHolder {
    
    /**
     * DB attributes.
     *
     * @var array
     */
    private static $db = array(
        'ResponseContent'       => 'HTMLText',
        'EnableStreet'          => 'Boolean(0)',
        'StreetIsRequired'      => 'Boolean(0)',
        'EnableCity'            => 'Boolean(0)',
        'CityIsRequired'        => 'Boolean(0)',
        'EnableCountry'         => 'Boolean(0)',
        'CountryIsRequired'     => 'Boolean(0)',
        'EnablePhoneNumber'     => 'Boolean(0)',
        'PhoneNumberIsRequired' => 'Boolean(0)',
    );

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
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return Tools::singular_name_for($this);
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
        return Tools::plural_name_for($this); 
    }
    
    /**
     * Returns the field labels.
     * 
     * @param bool $includerelations Include relations?
     * 
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.06.2014
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
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
                )
        );
        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        $fields->addFieldToTab('Root.Main', new HTMLEditorField('ResponseContent', $this->fieldLabel('ResponseContent')));
        $fields->findOrMakeTab('Root.FormFields', $this->fieldLabel('FormFieldsTab'));
        $fields->addFieldToTab('Root.FormFields', new CheckboxField('EnableStreet', $this->fieldLabel('EnableStreet')));
        $fields->addFieldToTab('Root.FormFields', new CheckboxField('StreetIsRequired', $this->fieldLabel('StreetIsRequired')));
        $fields->addFieldToTab('Root.FormFields', new CheckboxField('EnableCity', $this->fieldLabel('EnableCity')));
        $fields->addFieldToTab('Root.FormFields', new CheckboxField('CityIsRequired', $this->fieldLabel('CityIsRequired')));
        $fields->addFieldToTab('Root.FormFields', new CheckboxField('EnableCountry', $this->fieldLabel('EnableCountry')));
        $fields->addFieldToTab('Root.FormFields', new CheckboxField('CountryIsRequired', $this->fieldLabel('CountryIsRequired')));
        $fields->addFieldToTab('Root.FormFields', new CheckboxField('EnablePhoneNumber', $this->fieldLabel('EnablePhoneNumber')));
        $fields->addFieldToTab('Root.FormFields', new CheckboxField('PhoneNumberIsRequired', $this->fieldLabel('PhoneNumberIsRequired')));
        
        return $fields;
    }

}