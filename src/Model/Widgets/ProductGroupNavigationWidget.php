<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Pages\ProductGroupHolderController;
use SilverCart\Model\Pages\ProductGroupPage;
use SilverCart\Model\Widgets\Widget;
use SilverCart\Model\Widgets\ProductGroupNavigationWidgetTranslation;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\GroupedDropdownField;
use WidgetSets\Model\WidgetSetWidget;

/**
 * Provides a navigation that starts at a definable productgroup.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductGroupNavigationWidget extends Widget {
    
    /**
     * Attributes.
     * 
     * @var array
     */
    private static $db = array(
        'ProductGroupPageID'      => 'Int',
        'levelsToShow'            => 'Int',
        'expandActiveSectionOnly' => 'Boolean(0)'
    );

    /**
     * Has-many relationships.
     *
     * @var array
     */
    private static $has_many = array(
        'ProductGroupNavigationWidgetTranslations' => ProductGroupNavigationWidgetTranslation::class,
    );

    /**
     * Casted attributes.
     * 
     * @var array
     */
    private static $casting = array(
        'FrontTitle' => 'Text',
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartProductGroupNavigationWidget';

    /**
     * retirieves the attribute FreeText from related language class depending
     * on the set locale
     *
     * @return string
     */
    public function getFrontTitle() {
        return $this->getTranslationFieldValue('FrontTitle');
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2013
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'FrontTitle'                               => WidgetSetWidget::singleton()->fieldLabel('FrontTitle'),
                    'FieldLabel'                               => _t(ProductGroupItemsWidget::class . '.STOREADMIN_FIELDLABEL', 'Please choose the product group to display:'),
                    'levelsToShow'                             => _t(ProductGroupNavigationWidget::class . '.LEVELS_TO_SHOW', 'Number of levels to show'),
                    'ShowAllLevels'                            => _t(ProductGroupNavigationWidget::class . '.SHOW_ALL_LEVELS', 'Show all levels'),
                    'Title'                                    => _t(ProductGroupNavigationWidget::class . '.TITLE', 'Productgroup navigation'),
                    'CMSTitle'                                 => _t(ProductGroupNavigationWidget::class . '.CMSTITLE', 'Productgroup navigation'),
                    'Description'                              => _t(ProductGroupNavigationWidget::class . '.DESCRIPTION', 'This widget creates a hierarchical navigation for productgroups. You can define what productgroup should be used as root.'),
                    'expandActiveSectionOnly'                  => _t(ProductGroupNavigationWidget::class . '.EXPAND_ACTIVE_SECTION_ONLY', 'Expand only active branch'),
                    'ProductGroupNavigationWidgetTranslations' => _t(ProductGroupNavigationWidgetTranslation::class . '.PLURALNAME', 'Translations'),
                    'ProductGroupPageID'                       => ProductGroupPage::singleton()->singular_name(),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns a list of fields to exclude from scaffolding
     * 
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.04.2013
     */
    public function excludeFromScaffolding() {
        $fields = array_merge(
            parent::excludeFromScaffolding(),
            array(
                'levelsToShow',
                'ProductGroupPageID'
            )
                );
        return $fields;
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $levels = array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '0' => $this->fieldLabel('ShowAllLevels')
        );
        $levelsToShow = new DropdownField(
                'levelsToShow',
                $this->fieldLabel('levelsToShow'),
                $levels
        );
        $fields->insertBefore($levelsToShow, 'ExtraCssClasses');
        $productGroupField = new GroupedDropdownField(
            'ProductGroupPageID',
            $this->fieldLabel('ProductGroupPageID'),
            ProductGroupHolderController::getAllProductGroupsWithChildrenAsArray(),
            $this->ProductGroupPageID
        );
        $fields->insertBefore($productGroupField, 'ExtraCssClasses');
        return $fields;
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
        return $this->dbObject('ExtraCssClasses')->getValue() . ' silvercart-product-group-navigation-widget';
    }
    
}