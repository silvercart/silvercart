<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Translation\TranslationTools;
use SilverCart\Model\Widgets\ProductGroupWidget;
use SilverStripe\ORM\DataObject;
use WidgetSets\Model\WidgetSetWidget;

/**
 * Translation object of ProductGroupWidget.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 24.08.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductGroupWidgetTranslation extends DataObject
{    
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = [
        'FrontTitle' => 'Varchar(128)',
    ];
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_one = [
        'ProductGroupWidget' => ProductGroupWidget::class,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartProductGroupWidgetTranslation';
    
    /**
     * Returns the translated singular name of the object.
     * 
     * @return string 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.08.2018
     */
    public function singular_name()
    {
        return TranslationTools::singular_name();
    }

    /**
     * Returns the translated plural name of the object.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.08.2018
     */
    public function plural_name()
    {
        return TranslationTools::plural_name();
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.08.2018
     */
    public function fieldLabels($includerelations = true)
    {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                [
                    'FrontTitle'         => WidgetSetWidget::singleton()->fieldLabel('FrontTitle'),
                    'ProductGroupWidget' => ProductGroupWidget::singleton()->singular_name(),
                ]
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Summary fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.08.2018
     */
    public function summaryFields() {
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