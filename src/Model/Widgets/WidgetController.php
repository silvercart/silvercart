<?php

namespace SilverCart\Model\Widgets;

use ReflectionClass;
use SilverCart\Dev\Tools;
use WidgetSets\Controllers\WidgetSetWidgetController;

/**
 * Widget Controller.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class WidgetController extends WidgetSetWidgetController {

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
        return Tools::PageByIdentifierCode($identifierCode);
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
        return Tools::PageByIdentifierCodeLink($identifierCode);
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