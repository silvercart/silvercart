<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Dev\Tools;
use SilverCart\Model\Translation\TranslatableDataObjectExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\DataObject;

/**
 * Slide for HTMLSliderWidget.
 *
 * @package SilverCart
 * @subpackage Model\Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.11.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property DBHTMLText $HTMLContent HTMLContent
 * 
 * @method \SilverStripe\ORM\HasManyList HTMLSliderWidgetSlideTranslations() Returns the related translations.
 */
class HTMLSliderWidgetSlide extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilverCart_Widgets_HTMLSliderSlide';
    /**
     * DB attributes
     *
     * @var string[]
     */
    private static $db = [
        'Title' => 'Varchar',
        'Sort'  => 'Int',
    ];
    /**
     * Casted attributes
     *
     * @var string[]
     */
    private static $casting = [
        'HTMLContent' => DBHTMLText::class,
    ];
    /**
     * has many relations
     *
     * @var string[]
     */
    private static $has_one = [
        'HTMLSliderWidget' => HTMLSliderWidget::class,
    ];
    /**
     * Has many relations.
     * 
     * @var array
     */
    private static $has_many = [
        'HTMLSliderWidgetSlideTranslations' => HTMLSliderWidgetSlideTranslation::class,
    ];
    /**
     * DB default sort
     *
     * @var string
     */
    private static $default_sort = 'Sort ASC';
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
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $fields->removeByName('Sort');
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

    /**
     * Getter for the HTMLContent depending on the set language
     *
     * @return DBHTMLText
     */
    public function getHTMLContent() : DBHTMLText
    {
        $htmlContent = $this->getTranslationFieldValue('HTMLContent');
        if (!($htmlContent instanceof DBHTMLText)) {
            $htmlContent = DBHTMLText::create()->setValue($htmlContent);
        }
        $htmlContent->setProcessShortcodes(true);
        return $htmlContent;
    }
}