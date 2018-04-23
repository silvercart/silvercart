<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Translation\TranslationTools;
use SilverCart\Model\Widgets\ProductGroupNavigationWidget;
use SilverStripe\ORM\DataObject;
use WidgetSets\Model\WidgetSetWidget;

/**
 * ProductGroupNavigationWidget Translation.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductGroupNavigationWidgetTranslation extends DataObject {

    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = array(
        'FrontTitle'   => 'Varchar(255)',
    );

    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_one = array(
        'ProductGroupNavigationWidget' => ProductGroupNavigationWidget::class,
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartProductGroupNavigationWidgetTranslation';
    
    /**
     * Returns the translated singular name of the object.
     * 
     * @return string 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.10.2017
     */
    public function singular_name() {
        return TranslationTools::singular_name();
    }


    /**
     * Returns the translated plural name of the object.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.10.2017
     */
    public function plural_name() {
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
     * @since 13.09.2013
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'FrontTitle'                   => WidgetSetWidget::singleton()->fieldLabel('FrontTitle'),
                'ProductGroupNavigationWidget' => _t(ProductGroupNavigationWidget::class . '.TITLE', 'Productgroup navigation'),
            )
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
     * @since 13.09.2013
     */
    public function summaryFields() {
        $summaryFields = array_merge(
            parent::summaryFields(),
            array(
                'FrontTitle' => $this->fieldLabel('FrontTitle'),
            )
        );

        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
}