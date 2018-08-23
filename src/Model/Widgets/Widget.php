<?php

namespace SilverCart\Model\Widgets;

use ReflectionClass;
use SilverCart\Model\Widgets\Widget;
use SilverCart\Model\Pages\Page;
use SilverCart\ORM\DataObjectExtension;
use WidgetSets\Model\ {
    WidgetSet,
    WidgetSetWidget
};

/**
 * Provides some basic functionality for all SilverCart widgets.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Widget extends WidgetSetWidget
{
    /**
     * Set whether to use the widget container divs or not.
     *
     * @var bool
     */
    public $useWidgetContainer = true;
    /**
     * Set this to false to use single elements for product slider
     *
     * @var bool
     */
    public static $use_product_pages_for_slider = false;
    /**
     * Set this to false to disable anything slider.
     *
     * @var bool
     */
    public static $use_anything_slider = false;
    /**
     * Contains a list of all registered filter plugins.
     *
     * @var array
     */
    public static $registeredFilterPlugins = [];

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
    public function fieldLabels($includerelations = true)
    {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            [
                'ClassName'    => _t(Widget::class . '.CLASSNAME', 'Class name'),
                'FrontTitle'   => _t(Widget::class . '.FRONTTITLE', 'Title to display in frontend'),
                'FrontContent' => _t(Widget::class . '.FRONTCONTENT', 'Content to display in frontend'),
                'Title'        => _t(Page::class . '.TITLE', 'Title'),
            ]
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     */
    public function getCMSFields()
    {
        return DataObjectExtension::getCMSFields($this, 'ExtraCssClasses', false);
    }
    
    /**
     * Returns the title of this widget.
     * 
     * @return string
     */
    public function getTitle()
    {
        $title = parent::getTitle();
        if (empty($title)
            && $this->hasField('FrontTitle')
            && !empty($this->FrontTitle)
        ) {
            $title = $this->fieldLabel('Title') . ': ' . $this->FrontTitle;
        }
        return $title;
    }
    
    /**
     * Indicate whether to use the widget container divs or not.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2013
     */
    public function DoUseWidgetContainer()
    {
        return $this->useWidgetContainer;
    }
    
    /**
     * Returns the related WidgetSet.
     * 
     * @return WidgetSet
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.08.2018
     */
    public function WidgetSet()
    {
        return WidgetSet::get()->filter('WidgetAreaID', $this->ParentID);
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
     * @since 22.08.2018
     */
    public static function registerFilterPlugin($plugin)
    {
        $reflectionClass = new ReflectionClass($plugin);
        
        if ($reflectionClass->hasMethod('filter')) {
            self::$registeredFilterPlugins[] = new $plugin();
        }
    }
}