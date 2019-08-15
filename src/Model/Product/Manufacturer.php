<?php

namespace SilverCart\Model\Product;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Pages\ProductGroupPage;
use SilverCart\Model\Product\ManufacturerTranslation;
use SilverCart\Model\Product\Product;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Assets\Image as SilverStripeImage;
use SilverStripe\Assets\Storage\DBFile;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\HasManyList;

/**
 * abstract for a manufacturer.
 *
 * @package SilverCart
 * @subpackage Model_Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $ManufacturerNumber Manufacturer Number
 * @property string $Title              Title
 * @property string $URL                URL
 * @property string $URLSegment         URLSegment
 * 
 * @property string $Description Description (current locale context)
 * 
 * @method SilverStripeImage logo() Returns the related logo.
 * 
 * @method HasManyList Products()                 Returns a list of related Products.
 * @method HasManyList ManufacturerTranslations() Returns a list of translations for this Manufacturer.
 */
class Manufacturer extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * Attributes
     *
     * @var array
     */
    private static $db = [
        'ManufacturerNumber' => 'Varchar',
        'Title'              => 'Varchar',
        'URL'                => 'Varchar',
        'URLSegment'         => 'Varchar'
    ];
    /**
     * Has-one relationships.
     *
     * @var array
     */
    private static $has_one = [
        'logo' => SilverStripeImage::class,
    ];
    /**
     * Has-many relationships.
     *
     * @var array
     */
    private static $has_many = [
        'Products'                 => Product::class,
        'ManufacturerTranslations' => ManufacturerTranslation::class,
    ];
    /**
     * Casted attributes
     *
     * @var array
     */
    private static $casting = [
        'LogoThumbnail' => DBHTMLText::class,
        'Description'   => 'Text'
    ];
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
     * Returns the translated singular name.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this); 
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'ManufacturerNumber'       => _t(Manufacturer::class . '.ManufacturerNumber', 'Manufacturer Number'),
            'Title'                    => Page::singleton()->fieldLabel('Title'),
            'Description'              => _t(Manufacturer::class . '.Description', 'Description'),
            'URL'                      => 'URL',
            'logo'                     => Page::singleton()->fieldLabel('Logo'),
            'LogoThumbnail'            => Page::singleton()->fieldLabel('Logo'),
            'Products'                 => Product::singleton()->plural_name(),
            'ManufacturerTranslations' => ManufacturerTranslation::singleton()->plural_name(),
        ]);
    }

    /**
     * Get the default summary fields for this object.
     *
     * @return array
     */
    public function summaryFields() : array
    {
        $summaryFields = [
            'LogoThumbnail' => $this->fieldLabel('logo'),
            'Title'         => $this->fieldLabel('Title'),
            'URL'           => $this->fieldLabel('URL'),
        ];
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
    public function excludeFromScaffolding() : array
    {
        $excludeFromScaffolding = [
            'URLSegment'
        ];
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }
    
    /**
     * Replaces the ProductGroupID DropDownField with a GroupedDropDownField.
     *
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $fields->removeByName('URLSegment');
        });
        return DataObjectExtension::getCMSFields($this, 'URL', true);
    }

    /**
     * getter for the description, looks for set translation
     *
     * @return string The description from the translation object or an empty string
     */
    public function getDescription() : ?string
    {
        return $this->getTranslationFieldValue('Description');
    }
    
    /**
     * Returns the i18n action name to filter a product group by manufacturer.
     * 
     * @return string
     */
    public static function get_filter_action() : string
    {
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
    public function Link() : string
    {
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
    public function LinkingMode() : string
    {
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
    public static function getByUrlSegment($urlSegment) : ?Manufacturer
    {
        return self::get()->filter(['URLSegment' => $urlSegment])->first();
    }

    /**
     * Manipulates the object before writing.
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.03.2011
     */
    public function onBeforeWrite() : void
    {
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
    public function title2urlSegment() : string
    {
        return Tools::string2urlSegment($this->Title);
    }
    
    /**
     * Returns the logo to display in a TableListField
     *
     * @return string
     */
    public function getLogoThumbnail() : ?DBFile
    {
        return $this->logo()->Pad(200,25);
    }
    
    /**
     * Returns the related products.
     * 
     * @return DataList
     */
    public function getProducts() : DataList
    {
        return Product::getProducts("ManufacturerID = {$this->ID}");
    }
}