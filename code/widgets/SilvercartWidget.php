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
class SilvercartWidget extends Widget {
   
    /**
     * Attributes
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 14.07.2011
     */
    public static $db = array(
        'sortOrder' => 'Int'
    );
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 14.07.2011
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        $sortOrderField = new TextField('sortOrder', _t('SilvercartWidget.SORT_ORDER_LABEL'));
        
        $fields->push($sortOrderField);
        
        return $fields;
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
class SilvercartWidget_Controller extends Widget_Controller {
    
    /**
     * Instances of $this will have a unique ID
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public static $classInstanceCounter = array();
    
    /**
     * Contains the unique ID of the current class instance
     * 
     * @var int
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    protected $classInstanceIdx = 0;
    
    protected $pageControler = null;
    
    /**
     * We register the search form on the page controller here.
     * 
     * @param string $widget Not documented in parent class unfortunately
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function __construct($widget = null) {
        parent::__construct($widget);
        
        // Initialize or increment the Counter for the form class
        if (!isset(self::$classInstanceCounter[$this->class])) {
            self::$classInstanceCounter[$this->class] = 0;
        } else {
            self::$classInstanceCounter[$this->class]++;
        }
        
        $this->classInstanceIdx = self::$classInstanceCounter[$this->class];
    }
}
