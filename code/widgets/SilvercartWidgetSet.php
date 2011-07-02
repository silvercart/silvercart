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
 * Contains an arbitrary number of widgets.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 27.05.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartWidgetSet extends DataObject {
    
    /**
     * singular name for backend
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.05.2011
     */
    public static $singular_name = "Widget set";

    /**
     * plural name for backend
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.05.2011
     */
    public static $plural_name = "Widget sets";
    
    /**
     * Attributes
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.05.2011
     */
    public static $db = array(
        'Title' => 'VarChar(255)'
    );
    
    /**
     * Has-one relationships
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.05.2011
     */
    public static $has_one = array(
        'WidgetArea' => 'WidgetArea'
    );
    
    /**
     * Has-many relationships
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.05.2011
     */
    public static $belongs_many_many = array(
        'SilvercartPages' => 'SilvercartPage'
    );
    
    /**
     * Constructor. We localize the static variables here.
     *
     * @param array|null $record      This will be null for a new database record.
     *                                  Alternatively, you can pass an array of
     *                                  field values.  Normally this contructor is only used by the internal systems that get objects from the database.
     * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.  Singletons
     *                                  don't have their defaults set.
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.07.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        self::$singular_name = _t('SilvercartWidgetSet.SINGULARNAME', 'Widget Set');
        self::$plural_name = _t('SilvercartWidgetSet.PLURALNAME', 'Widget Sets');
        parent::__construct($record, $isSingleton);
    }
    
    /**
     * Returns the GUI fields for the storeadmin.
     * 
     * @param array $params Additional parameters
     * 
     * @return FieldSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.05.2011
     */
    public function getCMSFields($params = null) {
        $fields           = parent::getCMSFields($params);
        $widgetAreaEditor = new WidgetAreaEditor('WidgetArea');
        
        $fields->removeByName('WidgetAreaID');
        $fields->removeFieldFromTab('Root', 'SilvercartPages');
        $fields->addFieldToTab('Root.Main', $widgetAreaEditor);
        
        $pagesTableField = new ManyManyDataObjectManager(
            $this,
            'SilvercartPages',
            'SilvercartPage'
        );
        
        $fields->addFieldToTab('Root.SilvercartPages', $pagesTableField);
        
        return $fields;
    }
    
    /**
     * Summary fields for display in tables.
     * 
     * @return array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.05.2011
     */
    public function summaryFields() {
        $fields = array(
            'Title' => 'Title'
        );
        
        return $fields;
    }
}
