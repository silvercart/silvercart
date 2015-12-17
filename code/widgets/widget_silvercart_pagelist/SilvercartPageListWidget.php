<?php
/**
 * Copyright 2015 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Widgets
 */

/**
 * Provides a view for pages.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>,
 *         Sascha Koehler <skoehler@pixeltricks.de>
 * @since 02.06.2015
 * @license see license file in modules root directory
 * @copyright 2015 pixeltricks GmbH
 */
class SilvercartPageListWidget extends SilvercartWidget {

    /**
     * Attributes
     *
     * @var array
     *
     * @since 06.12.2012
     */
    public static $db = array(
    );

    /**
     * Has-many relations
     *
     * @var array
     *
     * @since 06.12.2012
     */
    public static $has_many = array(
        'SilvercartPageListWidgetLanguages' => 'SilvercartPageListWidgetLanguage'
    );

    /**
     * Many-many relations
     *
     * @var array
     *
     * @since 06.12.2012
     */
    public static $many_many = array(
        'Pages' => 'SiteTree'
    );

    /**
     * Casting
     *
     * @var array
     *
     * @since 06.12.2012
     */
    public static $casting = array(
        'FrontTitle'                    => 'VarChar(255)',
        'FrontContent'                  => 'Text',
    );

    /**
     * Getter for the front title depending on the set language
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.12.2012
     */
    public function getFrontTitle() {
        return $this->getLanguageFieldValue('FrontTitle');
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
        return $this->getLanguageFieldValue('FrontContent');
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
            SilvercartWidgetTools::fieldLabelsForProductSliderWidget($this),
            array(
                'Pages'                             => _t('SilvercartPageListWidget.PAGES'),
                'SilvercartPageListWidgetLanguages' => _t('Silvercart.TRANSLATIONS'),
                
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
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.12.2012
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this, 'ExtraCssClasses', false);
        $pagesField     = new TreeMultiselectField(
            'Pages',
            $this->fieldLabel('Pages'),
            'SiteTree',
            'ID',
            'Title'
        );
        $fields->insertAfter($pagesField, 'FrontContent');
        return $fields;
    }
}

/**
 * Provides a view for pages.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>,
 *         Sascha Koehler <skoehler@pixeltricks.de>
 * @since 02.06.2015
 * @license see license file in modules root directory
 * @copyright 2015 pixeltricks GmbH
 */
class SilvercartPageListWidget_Controller extends SilvercartWidget_Controller {

    /**
     * Returns the attributed pages as DataList
     * 
     * @param int $start Limit start
     * @param int $end   Limit end
     *
     * @return ArrayList
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 02.06.2015
     */
    public function getPages($start = null, $end = null) {
        $limit = '';
        if (is_numeric($start) &&
            is_null($end)) {
            $limit = $start . ',999';
        } elseif (is_numeric($start) &&
                  is_numeric($end)) {
            $limit = $start . ',' . $end;
        } elseif (is_null($start) &&
                  is_numeric($end)) {
            $limit = '0,' . $end;
        }
        $pages = $this->Pages(null, 'widgetPriority ASC', '', $limit);
        
        return $pages;
    }

    /**
     * Creates the cache key for this widget.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.12.2012
     */
    public function WidgetCacheKey() {
        $key = i18n::get_locale();

        if ((int) $key > 0) {
            $permissions = $this->Pages()->map('ID', 'CanView');

            foreach ($permissions as $pageID => $permission) {
                $key .= '_'.$pageID.'-'.((string) $permission);
            }

            $key .= $this->Pages()->Aggregate('MAX(Last_Edited)');
        }

        return $key;
    }
}