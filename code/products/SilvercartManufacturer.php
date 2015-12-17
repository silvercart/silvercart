<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Products
 */

/**
 * abstract for a manufacturer
 *
 * @package Silvercart
 * @subpackage Products
 * @author Roland Lehmann <rlehmann@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 02.07.2013
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartManufacturer extends DataObject {

    /**
     * Attributes
     *
     * @var array
     */
    public static $db = array(
        'ManufacturerNumber'    => 'VarChar',
        'Title'                 => 'VarChar',
        'URL'                   => 'VarChar',
        'URLSegment'            => 'VarChar'
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
        'LogoThumbnail' => 'HtmlText',
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
                    'ManufacturerNumber'              => _t('SilvercartManufacturer.ManufacturerNumber'),
                    'Title'                           => _t('SilvercartPage.TITLE'),
                    'Description'                     => _t('SilvercartManufacturer.Description'),
                    'URL'                             => 'URL',
                    'logo'                            => _t('SilvercartPage.LOGO'),
                    'LogoThumbnail'                   => _t('SilvercartPage.LOGO'),
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
            'LogoThumbnail' => $this->fieldLabel('logo'),
            'Title'         => $this->fieldLabel('Title'),
            'URL'           => $this->fieldLabel('URL'),
        );
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }

    /**
     * excludes defined fields from scaffolding
     *
     * @return array numeric array with field identifiers 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 10.02.2013
     */
    public function excludeFromScaffolding() {
        $excludeFromScaffolding = array(
            'URLSegment'
        );
        
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        
        return $excludeFromScaffolding;
    }
    /**
     * Replaces the SilvercartProductGroupID DropDownField with a GroupedDropDownField.
     *
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this, 'URL', true);
        $fields->removeByName('URLSegment');
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
     * @since 28.01.2013
     */
    public static function getByUrlSegment($urlSegment) {
        return self::get()->filter(array('URLSegment' => $urlSegment))->first();
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
    public function getLogoThumbnail() {
        return $this->logo()->SetRatioSize(200,25);
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
