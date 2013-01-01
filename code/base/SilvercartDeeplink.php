<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * @subpackage Base
 */

/**
 * Define how an url can be built to redirect to a product detail page without
 * The URL has the attribute and the value as parameters:
 * www.mysite.com/deeplink/attribute/value
 * These definitions are always handled on the SilvercartDeeplinkPage.
 *
 * @package Silvercart
 * @subpackage Base
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 29.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartDeeplink extends DataObject {
    
    /**
     * attributes
     * 
     * @var array additional attributes 
     */
    public static $db = array(
        'productAttribute' => 'VarChar(50)',
        'isActive'         => 'Boolean(0)'
    );

    /**
     * Casted attributes
     *
     * @var array
     */
    public static $casting = array(
        'DeeplinkUrl'      => 'VarChar',
        'ActivationStatus' => 'VarChar'
    );
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 01.10.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),             array(
                    'isActive'          => _t('SilvercartPage.ISACTIVE'),
                    'productAttribute'  => _t('SilvercartProductGroupPage.ATTRIBUTES'),
                    'deeplinkAttribute' => _t('SilvercartDeeplinkAttribute.SINGULARNAME'),
                    'countryActive'     => _t('SilvercartCountry.ACTIVE'),
                    'emptyString'       => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * getter for the avtivation status
     * 
     * @return boolean answer 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 30.7.2011
     */
    public function isActive() {
        return $this->isActive;
    }
    
    /**
     * Returns the text label for the deeplinks activion status.
     *
     * @return string answer as text
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 30.7.2011
     */
    public function getActivationStatus() {
        if ($this->isActive()) {
            return _t('Silvercart.YES');
        }
        return _t('Silvercart.NO');
    }
    
    /**
     * Return the absolute URL of the deeplink page plus the attribute for the
     * filter;
     * 
     * @return string The absolute link to this deeplink setting or an empty string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * since 28.7.2011
     */
    public function getDeeplinkUrl() {
        $url = "";
        $deeplinkPage = SilvercartPage_Controller::PageByIdentifierCode("SilvercartDeeplinkPage");
        if ($deeplinkPage && $this->productAttribute != "" && $this->isActive()) {
            $url = $deeplinkPage->AbsoluteLink() . $this->productAttribute . "/";
        }
        return $url;
    }
    
    /**
     * Returns the GUI fields for the storeadmin.
     * 
     * @param array $params Additional parameters
     * 
     * @return FieldList a set of fields 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function getCMSFields($params = null) {
        $productFields  = array();
        $fields         = parent::getCMSFields($params);
        $fields->removeByName('productAttribute');
        
        $dbFields = DataObject::database_fields('SilvercartProduct');
        foreach ($dbFields as $fieldName => $fieldType) {
            $productFields[$fieldName] = $fieldName;
        }
        
        $productAttributeDropdown = new DropdownField('productAttribute', $this->fieldLabel('deeplinkAttribute'), $productFields, null, null, $this->fieldLabel('emptyString'));
        
        $fields->addFieldToTab('Root.Main', $productAttributeDropdown);
        $fields->addFieldToTab('Root.Main', new ReadonlyField('deeplink', $this->singular_name(), $this->getDeeplinkUrl()));
        return $fields;
    }
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 30.7.2011
     */
    public function summaryFields() {
        $summaryFields = array(
            'productAttribute' => $this->fieldLabel('deeplinkAttribute'),
            'ActivationStatus' => $this->fieldLabel('countryActive'),
            'DeeplinkUrl'      => 'Deeplink'
        );
        return $summaryFields;
    }
}

