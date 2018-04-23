<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Translation\TranslationTools;
use SilverCart\Model\Widgets\Widget;
use SilverCart\Model\Widgets\WidgetTools;
use SilverCart\Model\Widgets\ProductGroupManufacturersWidgetTranslation;

/**
 * Provides a view of all manufacturers from a product group.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductGroupManufacturersWidget extends Widget {

    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_many = array(
        'ProductGroupManufacturersWidgetTranslations' => ProductGroupManufacturersWidgetTranslation::class,
    );

    /**
     * field casting
     *
     * @var array
     */
    private static $casting = array(
        'FrontTitle'   => 'Varchar(255)',
        'FrontContent' => 'Text',
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
     * @return string
     */
    public function getFrontContent() {
        return $this->getTranslationFieldValue('FrontContent');
    }
    
    /**
     * Returns the extra css classes.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.04.2013
     */
    public function ExtraCssClasses() {
        return $this->dbObject('ExtraCssClasses')->getValue() . ' silvercart-product-group-manufacturers-widget';
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.10.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                WidgetTools::fieldLabelsForProductSliderWidget($this),
                array(
                    'ProductGroupManufacturersWidgetTranslations' => _t(TranslationTools::class . '.TRANSLATIONS', 'Translations'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
}