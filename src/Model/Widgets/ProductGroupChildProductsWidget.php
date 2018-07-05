<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Admin\Model\Config;
use SilverCart\Model\Widgets\Widget;
use SilverCart\Model\Widgets\WidgetTools;
use SilverCart\Model\Widgets\ProductGroupChildProductsWidgetTranslation;

/**
 * Provides a view of items of the child product groups of the current product
 * group if there are no products assigned to the current product group.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductGroupChildProductsWidget extends Widget {

    /**
     * Set whether to use the widget container divs or not.
     *
     * @var bool
     */
    public $useWidgetContainer = false;

    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_many = array(
        'ProductGroupChildProductsWidgetTranslations' => ProductGroupChildProductsWidgetTranslation::class,
    );

    /**
     * field casting
     *
     * @var array
     */
    private static $casting = array(
        'FrontTitle'                    => 'Varchar(255)',
        'FrontContent'                  => 'Text',
    );

    /**
     * Getter for the front title depending on the set language
     *
     * @return string
     */
    public function getFrontTitle() {
        return $this->getTranslationFieldValue('FrontTitle');
    }

    /**
     * Getter for the FrontContent depending on the set language
     *
     * @return string The HTML front content
     */
    public function getFrontContent() {
        return $this->getTranslationFieldValue('FrontContent');
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.11.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                WidgetTools::fieldLabelsForProductSliderWidget($this),
                array(
                    'ProductGroupChildProductsWidgetTranslations' => _t(Config::class . '.TRANSLATIONS', 'Translations'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
}