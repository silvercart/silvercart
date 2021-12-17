<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Translation\TranslationExtension;
use SilverCart\Model\Translation\TranslationTools;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * Translation for HTMLSliderWidgetSlide.
 *
 * @package SilverCart
 * @subpackage Model\Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.11.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $HTMLContent HTMLContent
 * 
 * @method HTMLSliderWidgetSlide HTMLSliderWidgetSlide() Returns the related HTMLSliderWidgetSlide.
 */
class HTMLSliderWidgetSlideTranslation extends DataObject
{
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilverCart_Widgets_HTMLSliderSlideTranslation';
    /**
     * DB attributes
     *
     * @var array
     */
    private static $db = [
        'HTMLContent' => DBHTMLText::class,
    ];
    /**
     * has many relations
     *
     * @var array
     */
    private static $has_one = [
        'HTMLSliderWidgetSlide' => HTMLSliderWidgetSlide::class,
    ];
    /**
     * Extensions
     * 
     * @var string[]
     */
    private static $extensions = [
        TranslationExtension::class,
    ];
    
    /**
     * Returns the translated singular name of the object.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return TranslationTools::singular_name();
    }


    /**
     * Returns the translated plural name of the object.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return TranslationTools::plural_name();
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
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                [
                    'HTMLContent'           => HTMLSliderWidgetSlide::singleton()->fieldLabel('HTMLContent'),
                    'HTMLSliderWidgetSlide' => HTMLSliderWidgetSlide::singleton()->singular_name(),
                ]
        );
        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Summary fields
     *
     * @return array
     */
    public function summaryFields() : array
    {
        $summaryFields = array_merge(
                parent::summaryFields(),
                [
                    'HTMLContent.FirstSentence' => $this->fieldLabel('HTMLContent'),
                ]
        );
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
}