<?php
/**
 * Copyright 2012 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Widgets
 */

/**
 * Provides a view for pages.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 06.12.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
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
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 06.12.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
 */
class SilvercartPageListWidget_Controller extends SilvercartWidget_Controller {

    /**
     * Returns the attributed pages as DataObjectSet
     *
     * @return ArrayList
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.12.2012
     */
    public function getPages() {
        $pages = $this->Pages(null, 'widgetPriority ASC');

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