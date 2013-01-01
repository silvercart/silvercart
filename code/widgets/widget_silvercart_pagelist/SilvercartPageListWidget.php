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
     * Returns the title of this widget.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.12.2012
     */
    public function Title() {
        return $this->fieldLabel('Title');
    }

    /**
     * Returns the title of this widget for display in the WidgetArea GUI.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.12.2012
     */
    public function CMSTitle() {
        return $this->fieldLabel('CMSTitle');
    }

    /**
     * Returns the description of what this template does for display in the
     * WidgetArea GUI.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.12.2012
     */
    public function Description() {
        return $this->fieldLabel('Description');
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
                'Title'       => _t('SilvercartPageListWidget.TITLE'),
                'CMSTitle'    => _t('SilvercartPageListWidget.CMSTITLE'),
                'Description' => _t('SilvercartPageListWidget.DESCRIPTION'),
                'Pages'       => _t('SilvercartPageListWidget.PAGES'),
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * GUI fields
     *
     * @return FieldSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.12.2012
     */
    public function getCMSFields() {
        $fields     = new FieldSet();
        $rootTabSet = new TabSet('Root');
        $basicTab   = new Tab('Basic', $this->fieldLabel('BasicTab'));

        $titleField     = new TextField('FrontTitle',               $this->fieldLabel('FrontTitle'));
        $contentField   = new TextareaField('FrontContent',         $this->fieldLabel('FrontContent'), 10);
        $pagesField     = new TreeMultiselectField(
            'Pages',
            $this->fieldLabel('Pages'),
            'SiteTree',
            'ID',
            'Title'
        );
        $basicTab->push($titleField);
        $basicTab->push($contentField);
        $basicTab->push($pagesField);
        $basicTab->push(new LiteralField('spacer', '<div style="height: 200px;"></div>'));

        $fields->push($rootTabSet);
        $rootTabSet->push($basicTab);

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
     * @return DataObjectSet
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