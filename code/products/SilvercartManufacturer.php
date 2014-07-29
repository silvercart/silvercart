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
 * @subpackage Products
 */

/**
 * abstract for a manufacturer
 *
 * @package Silvercart
 * @subpackage Products
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @copyright 2010 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartManufacturer extends DataObject {

    /**
     * Attributes
     *
     * @var array
     */
    public static $db = array(
        'Title'         => 'VarChar',
        'URL'           => 'VarChar',
        'URLSegment'    => 'VarChar'
    );
    /**
     * Has-one relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'logo' => 'Image'
    );
    /**
     * Has-many relationships.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartProducts'              => 'SilvercartProduct',
        'SilvercartManufacturerLanguages' => 'SilvercartManufacturerLanguage'
    );
    
    /**
     * Casted attributes
     *
     * @var array
     */
    public static $casting = array(
        'LogoForTable'  => 'HtmlText',
        'Description'   => 'Text'
    );
    
    /**
     * Default sort and direction.
     *
     * @var string
     */
    public static $default_sort = 'Title ASC';

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
     * @param boolean $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.12.2013
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'Title'                           => _t('SilvercartPage.TITLE'),
                    'Description'                     => _t('SilvercartManufacturer.Description'),
                    'URL'                             => _t('SilvercartPage.URL'),
                    'logo'                            => _t('SilvercartPage.LOGO'),
                    'SilvercartProducts'              => _t('SilvercartProduct.PLURALNAME'),
                    'SilvercartManufacturerLanguages' => _t('SilvercartManufacturerLanguage.PLURALNAME'),
                )
        );
        
        $this->extend('updateFieldLabels', $fieldLabels);
        
        return $fieldLabels;
    }

    /**
     * Get the default summary fields for this object.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.02.2011
     */
    public function  summaryFields() {
        $summaryFields = array(
            'LogoForTable'  => $this->fieldLabel('logo'),
            'Title'         => $this->fieldLabel('Title'),
            'URL'           => $this->fieldLabel('URL'),
        );
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }

    /**
     * Replaces the SilvercartProductGroupID DropDownField with a GroupedDropDownField.
     *
     * @param array $params See {@link scaffoldFormFields()}
     *
     * @return FieldSet
     */
    public function getCMSFields($params = null) {
        $fields = parent::getCMSFields($params);
        $fields->removeByName('URLSegment');
        
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguage(true));
        foreach ($languageFields as $languageField) {
            $fields->insertBefore($languageField, 'logo');
        }
        
        return $fields;
    }

    /**
     * getter for the description, looks for set translation
     *
     * @return string The description from the translation object or an empty string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.11.2012
     */
    public function getDescription() {
        return $this->getLanguageFieldValue('Description');
    }

    /**
     * Returns the link to the manufacturer filtered product list in dependence
     * on the product group.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.03.2011
     */
    public function Link() {
        return Controller::curr()->Link() . _t('SilvercartProductGroupPage.MANUFACTURER_LINK','manufacturer') . '/' . $this->URLSegment;
    }

    /**
     * Returns 'current' or 'link' to use as CSS class in dependence of the current
     * view.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.03.2011
     */
    public function LinkingMode() {
        if ($_SERVER['REQUEST_URI'] == $this->Link()) {
            return 'current';
        }
        return 'link';
    }

    /**
     * Returns the manufacturer by its URL segment.
     *
     * @param string $urlSegment the manufacturers URL segment
     *
     * @return SilvercartManufacturer
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.03.2011
     */
    public static function getByUrlSegment($urlSegment) {
        return DataObject::get_one('SilvercartManufacturer', sprintf("`URLSegment` = '%s'", Convert::raw2sql($urlSegment)));
    }

    /**
     * Manipulates the object before writing.
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.03.2011
     */
    public function onBeforeWrite() {
        parent::onBeforeWrite();
        if (empty ($this->Title)) {
            return;
        }
        $this->URLSegment = $this->title2urlSegment();
    }

    /**
     * Remove chars from the title that are not appropriate for an url
     *
     * @return string sanitized manufacturer title
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.03.2011
     */
    public function title2urlSegment() {
        return SilvercartTools::string2urlSegment($this->Title);
    }
    
    /**
     * Returns the logo to display in a TableListField
     *
     * @return string
     */
    public function getLogoForTable() {
        $logoForTable = '';
        if ($this->logo()->ID > 0) {
            $logoForTable = sprintf(
                '<img src="%s" alt="%s" />',
                $this->logo()->SetRatioSize(200,25)->Link(),
                $this->logo()->Name
            );
        }
        return $logoForTable;
    }
    
    /**
     * Returns the related products.
     * 
     * @return DataObjectSet
     */
    public function getProducts() {
        $products = SilvercartProduct::get("SilvercartManufacturerID = " . $this->ID);
        return $products;
    }
}
