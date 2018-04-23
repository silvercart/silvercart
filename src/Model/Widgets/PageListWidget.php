<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Pages\Page;
use SilverCart\Model\Translation\TranslationTools;
use SilverCart\Model\Widgets\Widget;
use SilverCart\Model\Widgets\WidgetTools;
use SilverCart\Model\Widgets\PageListWidgetTranslation;
use SilverStripe\Forms\TreeMultiselectField;

/**
 * Provides a view for pages.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class PageListWidget extends Widget {

    /**
     * Has-many relations
     *
     * @var array
     */
    private static $has_many = array(
        'PageListWidgetTranslations' => PageListWidgetTranslation::class,
    );

    /**
     * Many-many relations
     *
     * @var array
     */
    private static $many_many = array(
        'Pages' => Page::class,
    );

    /**
     * Casting
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
     * @return string The HTML front content
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.12.2012
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
     * @since 06.12.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            WidgetTools::fieldLabelsForProductSliderWidget($this),
            array(
                'Pages'                      => _t(PageListWidget::class . '.PAGES', 'Attributed pages'),
                'PageListWidgetTranslations' => _t(TranslationTools::class . '.TRANSLATIONS', 'Translations'),
                
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns an array of field/relation names (db, has_one, has_many, 
     * many_many, belongs_many_many) to exclude from form scaffolding in
     * backend.
     * This is a performance friendly way to exclude fields.
     * 
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 14.03.2013
     */
    public function excludeFromScaffolding() {
        $excludeFromScaffolding = array_merge(
                parent::excludeFromScaffolding(),
                array(
                    'Pages'
                )
        );
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }

    /**
     * GUI fields
     *
     * @return FieldList
     */
    public function getCMSFields() {
        $fields     = parent::getCMSFields();
        $pagesField = new TreeMultiselectField(
            'Pages',
            $this->fieldLabel('Pages'),
            Page::class,
            'ID',
            'Title'
        );
        $fields->insertAfter($pagesField, 'FrontContent');
        return $fields;
    }
}