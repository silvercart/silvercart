<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Dev\Tools;
use SilverCart\Model\Translation\TranslatableDataObjectExtension;
use SilverCart\Model\Widgets\Widget;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

/**
 * Widget to show custom HTML slides.
 *
 * @package SilverCart
 * @subpackage Model\Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.11.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $FrontTitle   FrontTitle
 * @property string $FrontContent FrontContent
 * 
 * @method \SilverStripe\ORM\HasManyList Slides()                       Returns the related slides.
 * @method \SilverStripe\ORM\HasManyList HTMLSliderWidgetTranslations() Returns the related translations.
 */
class HTMLSliderWidget extends Widget
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilverCart_Widgets_HTMLSlider';
    /**
     * Has many relations.
     * 
     * @var array
     */
    private static $has_many = [
        'Slides'                       => HTMLSliderWidgetSlide::class,
        'HTMLSliderWidgetTranslations' => HTMLSliderWidgetTranslation::class,
    ];
    /**
     * field casting
     *
     * @var array
     */
    private static $casting = [
        'FrontTitle'   => 'Varchar(255)',
        'FrontContent' => 'Text',
    ];
    /**
     * Extensions
     * 
     * @var string[]
     */
    private static $extensions = [
        TranslatableDataObjectExtension::class,
    ];
    /**
     * Determines to insert the translation CMS fields.
     * 
     * @var bool
     */
    private static $insert_translation_cms_fields = true;

    /**
     * Returns the translated singular name of the object.
     * 
     * @return string
     */
    public function singular_name()
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object.
     *
     * @return string
     */
    public function plural_name()
    {
        return Tools::plural_name_for($this);
    }
    
    /**
     * Getter for the front title depending on the set language
     *
     * @return string
     */
    public function getFrontTitle() : string
    {
        return (string) $this->getTranslationFieldValue('FrontTitle');
    }
    
    /**
     * Getter for the FrontContent depending on the set language
     *
     * @return string
     */
    public function getFrontContent() : string
    {
        return (string) $this->getTranslationFieldValue('FrontContent');
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $slidesGrid = $fields->dataFieldByName('Slides');
            if ($slidesGrid instanceof GridField) {
                if (class_exists(GridFieldOrderableRows::class)) {
                    $slidesGrid->getConfig()->addComponent(new GridFieldOrderableRows('Sort'));
                } elseif (class_exists(GridFieldSortableRows::class)) {
                    $slidesGrid->getConfig()->addComponent(new GridFieldSortableRows('Sort'));
                }
            }
        });
        return parent::getCMSFields();
    }

    /**
     * Field labels for display in tables.
     *
     * @param bool $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, []);
    }
}