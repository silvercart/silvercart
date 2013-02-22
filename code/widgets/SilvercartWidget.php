<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * Provides some basic functionality for all SilverCart widgets.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 26.05.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartWidget extends WidgetSetWidget {

    /**
     * Set whether to use the widget container divs or not.
     *
     * @var bool
     */
    public $useWidgetContainer = true;

    /**
     * Attributes
     *
     * @var array
     */
    public static $db = array(
        'ExtraCssClasses' => 'VarChar(255)'
    );
    
    /**
     * Returns the title of this widget.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.03.2012
     */
    public function Title() {
        return _t($this->ClassName() . '.TITLE');
    }
    
    /**
     * Returns the title of this widget for display in the WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.03.2012
     */
    public function CMSTitle() {
        return _t($this->ClassName() . '.CMSTITLE');
    }
    
    /**
     * Returns the description of what this template does for display in the
     * WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.03.2012
     */
    public function Description() {
        return _t($this->ClassName() . '.DESCRIPTION');
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.08.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'ClassName' => _t('SilvercartWidget.CLASSNAME')
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Searchable fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.08.2012
     */
    public function searchableFields() {
        $searchableFields = array(
            'ClassName' => array(
                'title'     => $this->fieldLabel('ClassName'),
                'filter'    => 'PartialMatchFilter'
            )
        );
        $this->extend('updateSearchableFields', $searchableFields);
        return $searchableFields;
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.08.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'ClassName' => $this->fieldLabel('ClassName'),
        );

        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Returns an array of field/relation names (db, has_one, has_many, 
     * many_many, belongs_many_many) to exclude from form scaffolding in
     * backend.
     * This is a performance friendly way to exclude fields. It works only in
     * conjunction with the SilverCart module.
     * 
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.02.2013
     */
    public function excludeFromScaffolding() {
        $excludeFromScaffolding = array(
            'Parent',
            'Sort',
            'Enabled'
        );
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }

    /**
     * CMS fields for a SilvercartWidget
     *
     * @return FieldList
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.08.2012
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $availableWidgets = array();

        $classes = ClassInfo::subclassesFor('Widget');
        array_shift($classes);
        foreach ($classes as $class) {
            if ($class == 'SilvercartWidget') {
                continue;
            }
            $widgetClass                               = singleton($class);
            $availableWidgets[$widgetClass->ClassName] = $widgetClass->Title();
        }

        $availableWidgetsField = new DropdownField(
            'ClassName',
            'Typ',
            $availableWidgets
        );

        $fields->push(
            $availableWidgetsField
        );

        return $fields;
    }

    /**
     * Indicate whether to use the widget container divs or not.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2013
     */
    public function DoUseWidgetContainer() {
        return $this->useWidgetContainer;
    }

    /**
     * Note: Overloaded in {@link SilvercartWidget_Controller}.
     * 
     * @return string HTML
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2013
     */
    public function WidgetHolder() {
        return $this->renderWith("SilvercartWidgetHolder");
    }
}

/**
 * Provides some basic functionality for all SilverCart widgets.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 26.05.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartWidget_Controller extends WidgetSetWidget_Controller {

    /**
     * Contains a list of all registered filter plugins.
     *
     * @var array
     */
    public static $registeredFilterPlugins = array();
    
    /**
     * returns a page by IdentifierCode
     *
     * @param string $identifierCode the DataObjects IdentifierCode
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2013
     */
    public function PageByIdentifierCode($identifierCode = "SilvercartFrontPage") {
        return SilvercartTools::PageByIdentifierCode($identifierCode);
    }
    
    /**
     * returns a page link by IdentifierCode
     *
     * @param string $identifierCode the DataObjects IdentifierCode
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2013
     */
    public function PageByIdentifierCodeLink($identifierCode = "SilvercartFrontPage") {
        return SilvercartTools::PageByIdentifierCodeLink($identifierCode);
    }
    
    /**
     * Overloaded from {@link Widget->WidgetHolder()}
     * to allow for controller/form linking.
     * 
     * @return string HTML
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2013
     */
    public function WidgetHolder() {
        return $this->renderWith("SilvercartWidgetHolder");
    }

    /**
     * Registers an object as a filter plugin. Before getting the result set
     * the method 'filter' is called on the plugin. It has to return an array
     * with filters to deploy on the query.
     *
     * @param Object $plugin The filter plugin object
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.06.2012
     */
    public static function registerFilterPlugin($plugin) {
        $reflectionClass = new ReflectionClass($plugin);
        
        if ($reflectionClass->hasMethod('filter')) {
            self::$registeredFilterPlugins[] = new $plugin();
        }
    }
}
