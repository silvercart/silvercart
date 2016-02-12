<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * show an process a contact form
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license see license file in modules root directory
 * @since 19.10.2010
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartContactFormPage extends SilvercartMetaNavigationHolder {
    
    /**
     * DB attributes.
     *
     * @var array
     */
    public static $db = array(
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
     * list of allowed children page types
     *
     * @var array
     */
    public static $allowed_children = array(
        'SilvercartContactFormResponsePage'
    );
    
    /**
     * We set a custom icon for this page type here
     *
     * @var string
     */
    public static $icon = "silvercart/img/page_icons/metanavigation_page";
    
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
                    'EnableStreet'          => _t('SilvercartContactFormPage.EnableStreet'),
                    'StreetIsRequired'      => _t('SilvercartContactFormPage.StreetIsRequired'),
                    'EnableCity'            => _t('SilvercartContactFormPage.EnableCity'),
                    'CityIsRequired'        => _t('SilvercartContactFormPage.CityIsRequired'),
                    'EnableCountry'         => _t('SilvercartContactFormPage.EnableCountry'),
                    'CountryIsRequired'     => _t('SilvercartContactFormPage.CountryIsRequired'),
                    'EnablePhoneNumber'     => _t('SilvercartContactFormPage.EnablePhoneNumber'),
                    'PhoneNumberIsRequired' => _t('SilvercartContactFormPage.PhoneNumberIsRequired'),
                    'FormFieldsTab'         => _t('SilvercartContactFormPage.FormFieldsTab'),
                )
        );
        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldSet
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
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

/**
 * Controller of this page type
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license see license file in modules root directory
 * @since 19.10.2010
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartContactFormPage_Controller extends SilvercartMetaNavigationHolder_Controller {
    
    /**
     * List of allowed actions
     * 
     * @var array
     */
    public static $allowed_actions = array(
        'productQuestion',
    );

    /**
     * initialisation of the form object
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 21.10.2010
     * @return void
     */
    public function init() {
        $this->registerCustomHtmlForm('SilvercartContactForm', new SilvercartContactForm($this));
        parent::init();
    }
    
    /**
     * Fills the contact form with a predefined product question text and renders the template
     *
     * @param SS_HTTPRequest $request HTTP request
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 31.05.2012
     */
    public function productQuestion(SS_HTTPRequest $request) {
        $params = $request->allParams();
        if (!empty($params['ID']) &&
            is_numeric($params['ID'])) {
            $product = DataObject::get_by_id('SilvercartProduct', $params['ID']);
            if ($product) {
                $silvercartContactForm = $this->getRegisteredCustomHtmlForm('SilvercartContactForm');
                $silvercartContactForm->setFormFieldValue(
                        'Message',
                        sprintf(
                                _t('SilvercartProduct.PRODUCT_QUESTION'),
                                $product->Title,
                                $product->ProductNumberShop
                        )
                );
            }
        }
        return $this->render();
    }
}
