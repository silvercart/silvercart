<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Translation\TranslationExtension;
use SilverCart\Model\Translation\TranslationTools;
use SilverStripe\ORM\DataObject;
use WidgetSets\Model\WidgetSetWidget;

/**
 * Translation for HTMLSliderWidget.
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
 * @method HTMLSliderWidget HTMLSliderWidget() Returns the related HTMLSliderWidget.
 */
class HTMLSliderWidgetTranslation extends DataObject
{
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilverCart_Widgets_HTMLSliderTranslation';
    /**
     * DB attributes
     *
     * @var array
     */
    private static $db = [
        'FrontTitle'   => 'Varchar(255)',
        'FrontContent' => 'Text'
    ];
    /**
     * has many relations
     *
     * @var array
     */
    private static $has_one = [
        'HTMLSliderWidget' => HTMLSliderWidget::class,
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
                    'FrontTitle'       => WidgetSetWidget::singleton()->fieldLabel('FrontTitle'),
                    'FrontContent'     => WidgetSetWidget::singleton()->fieldLabel('FrontContent'),
                    'HTMLSliderWidget' => HTMLSliderWidget::singleton()->singular_name(),
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
                    'FrontTitle' => $this->fieldLabel('FrontTitle'),
                ]
        );
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
}