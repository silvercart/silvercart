<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Widgets\Widget;

/**
 * Provides the a view of the topseller products.
 * 
 * You can define the number of products to be shown.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class TopsellerProductsWidget extends Widget {
    
    /**
     * Indicates the number of products that shall be shown with this widget.
     * 
     * @var int
     */
    private static $db = array(
        'numberOfProductsToShow' => 'Int'
    );
    
    /**
     * Set default values.
     * 
     * @var array
     */
    private static $defaults = array(
        'numberOfProductsToShow' => 5
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartTopsellerProductsWidget';
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'numberOfProductsToShow' => _t(TopsellerProductsWidget::class . '.STOREADMIN_FIELDLABEL', 'Number of products to show:')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
}