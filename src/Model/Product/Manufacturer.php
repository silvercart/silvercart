<?php

namespace SilverCart\Model\Product;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Pages\ProductGroupPage;
use SilverCart\Model\Product\ManufacturerTranslation;
use SilverCart\Model\Product\Product;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\DataObject;

/**
 * abstract for a manufacturer.
 *
 * @package SilverCart
 * @subpackage Model_Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Manufacturer extends DataObject {

    /**
     * Attributes
     *
     * @var array
     */
    private static $db = array(
        'ManufacturerNumber'    => 'Varchar',
        'Title'                 => 'Varchar',
        'URL'                   => 'Varchar',
        'URLSegment'            => 'Varchar'
    );
    /**
     * Has-one relationships.
     *
     * @var array
     */
    private static $has_one = array(
        'logo' => \SilverStripe\Assets\Image::class,
    );
    /**
     * Has-many relationships.
     *
     * @var array
     */
    private static $has_many = array(
        'Products'                 => Product::class,
        'ManufacturerTranslations' => ManufacturerTranslation::class,
    );
    
    /**
     * Casted attributes
     *
     * @var array
     */
    private static $casting = array(
        'LogoThumbnail' => 'HtmlText',
        'Description'   => 'Text'
    );
    
    /**
     * Default sort and direction.
     *
     * @var string
     */
    private static $default_sort = 'Title ASC';

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartManufacturer';

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
                    'ManufacturerNumber'       => _t(Manufacturer::class . '.ManufacturerNumber', 'Manufacturer Number'),
                    'Title'                    => Page::singleton()->fieldLabel('Title'),
                    'Description'              => _t(Manufacturer::class . '.Description', 'Description'),
                    'URL'                      => 'URL',
                    'logo'                     => Page::singleton()->fieldLabel('Logo'),
                    'LogoThumbnail'            => Page::singleton()->fieldLabel('Logo'),
                    'Products'                 => Product::singleton()->plural_name(),
                    'ManufacturerTranslations' => ManufacturerTranslation::singleton()->plural_name(),
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
     * Replaces the ProductGroupID DropDownField with a GroupedDropDownField.
     *
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = DataObjectExtension::getCMSFields($this, 'URL', true);
        $fields->removeByName('URLSegment');
        return $fields;
    }

    /**
     * getter for the description, looks for set translation
     *
     * @return string The description from the translation object or an empty string
     */
    public function getDescription() {
        return $this->getTranslationFieldValue('Description');
    }
    
    /**
     * Returns the i18n action name to filter a product group by manufacturer.
     * 
     * @return string
     */
    public static function get_filter_action() {
        return _t(ProductGroupPage::class . '.MANUFACTURER_LINK', 'manufacturer');
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
        return Controller::curr()->Link() . self::get_filter_action() . '/' . $this->URLSegment;
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
     * @return Manufacturer
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
        return Tools::string2urlSegment($this->Title);
    }
    
    /**
     * Returns the logo to display in a TableListField
     *
     * @return string
     */
    public function getLogoThumbnail() {
        return $this->logo()->Pad(200,25);
    }
    
    /**
     * Returns the related products.
     * 
     * @return DataObjectSet
     */
    public function getProducts() {
        $products = Product::getProducts("ManufacturerID = " . $this->ID);
        return $products;
    }
}